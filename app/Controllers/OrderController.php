<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\VoucherModel;
use App\Models\ProductModel;
use App\Libraries\Money;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class OrderController extends BaseController
{
    protected ProductModel $products;
    protected OrderModel $orders;
    protected VoucherModel $vouchers;

    public function __construct()
    {
        $this->products = new ProductModel();
        $this->orders   = new OrderModel();
        $this->vouchers = new VoucherModel();
    }

    public function create(int $productId): string
    {
        $product = $this->findActiveProduct($productId);
        session()->set('checkout_idempotency_token', bin2hex(random_bytes(32)));
        $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

        return view('checkout/form', [
            'title'   => 'Checkout - Ayong Store',
            'noindex' => true,
            'product' => $product,
            'paymentChannels' => (new \App\Models\PaymentChannelModel())->listActive(),
        ]);
    }

    public function store(int $productId)
    {
        $product = $this->findActiveProduct($productId);
        $idempotencyToken = trim((string) $this->request->getPost('idempotency_token'));

        if ($idempotencyToken !== '') {
            $existingOrder = $this->orders->findByToken($idempotencyToken);
            if ($existingOrder) {
                return redirect()->to('/pesanan/' . $existingOrder['invoice_number']);
            }
        }

        if (! $this->validate($this->orders->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $paymentChannelId = (int) $this->request->getPost('payment_channel_id');
        $paymentChannel = $paymentChannelId > 0
            ? (new \App\Models\PaymentChannelModel())->where('is_active', 1)->find($paymentChannelId)
            : null;
        if (! $paymentChannel) {
            return redirect()->back()->withInput()->with('errors', ['payment_channel_id' => 'Silakan pilih metode pembayaran.']);
        }

        $subtotal    = Money::rupiah($product['sell_price']);
        $this->orders->releaseExpiredVoucherReservations();
        $voucherCode = trim((string) $this->request->getPost('voucher_code'));
        $voucher     = null;
        $discount    = 0;

        if ($voucherCode !== '') {
            $voucher = $this->vouchers->findValid($voucherCode, $subtotal);
            if (! $voucher) {
                return redirect()->back()->withInput()->with('errors', ['voucher_code' => 'Kode voucher tidak valid atau tidak berlaku.']);
            }
            $discount = $this->vouchers->calculateDiscount($voucher, $subtotal);
        }

        $data = [
            'invoice_number'        => $this->orders->generateInvoiceNumber(),
            'product_id'            => $product['id'],
            'product_name_snapshot' => $product['name'],
            'nominal_snapshot'      => $product['nominal'],
            'price_snapshot'        => $subtotal,
            'game_id'               => $this->request->getPost('game_id'),
            'whatsapp_number'       => $this->request->getPost('whatsapp_number'),
            'voucher_id'            => $voucher['id'] ?? null,
            'voucher_reserved'      => $voucher ? 1 : 0,
            'voucher_committed'     => 0,
            'voucher_reserved_until' => $voucher ? date('Y-m-d H:i:s', time() + 900) : null,
            'discount_amount'       => $discount,
            'total_amount'          => $subtotal - $discount,
            'status'                => 'menunggu_pembayaran',
            'idempotency_token'     => $idempotencyToken !== '' ? $idempotencyToken : null,
            'payment_channel_id'    => $paymentChannel['id'],
        ];

        $this->orders->db->transStart();
        if (! $this->orders->save($data)) {
            $this->orders->db->transRollback();
            return redirect()->back()->withInput()->with('errors', $this->orders->errors());
        }

        if ($voucher && ! $this->vouchers->reserve((int) $voucher['id'])) {
            $this->orders->db->transRollback();
            return redirect()->back()->withInput()->with('errors', ['voucher_code' => 'Voucher baru saja habis digunakan.']);
        }
        $this->orders->db->transComplete();

        if (! $this->orders->db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Pesanan gagal disimpan. Silakan coba lagi.');
        }

        return redirect()->to('/pesanan/' . $data['invoice_number']);
    }

    public function checkStatus()
    {
        $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        return view('checkout/check_status', [
            'title'   => 'Cek Status Pesanan - Ayong Store',
            'noindex' => true,
        ]);
    }

    public function processCheckStatus()
    {
        $rules = [
            'invoice_number' => ['label' => 'Nomor Invoice', 'rules' => 'required|max_length[30]'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $invoiceNumber = trim((string) $this->request->getPost('invoice_number'));
        $order = $this->orders->findByInvoice($invoiceNumber);

        if (! $order) {
            return redirect()->back()->withInput()->with('error', 'Pesanan dengan Nomor Invoice tersebut tidak ditemukan.');
        }

        return redirect()->to('/pesanan/' . $order['invoice_number']);
    }

    public function invoice(string $invoiceNumber): ResponseInterface
    {
        $order = $this->orders->findByInvoice($invoiceNumber);

        if (! $order) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        return $this->response->setBody(view('checkout/invoice', [
            'title'   => 'Invoice ' . $order['invoice_number'] . ' - Ayong Store',
            'noindex' => true,
            'order'   => $order,
            'masked_game_id' => self::mask((string) $order['game_id']),
            'masked_whatsapp' => self::mask((string) $order['whatsapp_number']),
        ]));
    }

    private static function mask(string $value): string
    {
        $length = strlen($value);
        if ($length <= 4) {
            return str_repeat('*', $length);
        }
        return substr($value, 0, 2) . str_repeat('*', $length - 4) . substr($value, -2);
    }

    private function findActiveProduct(int $productId): array
    {
        $product = $this->products->find($productId);

        if (! $product || ! $product['is_active']) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $product;
    }
}
