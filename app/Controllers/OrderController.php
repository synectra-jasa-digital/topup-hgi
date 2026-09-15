<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\VoucherModel;
use App\Models\ProductModel;
use App\Models\PaymentChannelModel;
use App\Libraries\Money;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class OrderController extends BaseController
{
    private const PROOF_PATH = WRITEPATH . 'uploads/payment-proofs/';
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
                return redirect()->to('/pesanan/' . $existingOrder['invoice_number'] . '?token=' . $existingOrder['public_access_token']);
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
            'payment_channel_type'  => $paymentChannel['type'],
            'payment_channel_name'  => $paymentChannel['name'],
            'payment_account_number'=> $paymentChannel['account_number'],
            'payment_account_holder'=> $paymentChannel['account_holder'],
            'payment_qr_image_path' => $paymentChannel['qr_image_path'],
            'public_access_token'   => bin2hex(random_bytes(32)),
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

        return redirect()->to('/pesanan/' . $data['invoice_number'] . '?token=' . $data['public_access_token']);
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
            'access_token' => ['label' => 'Token Akses', 'rules' => 'required|exact_length[64]|alpha_numeric'],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $invoiceNumber = trim((string) $this->request->getPost('invoice_number'));
        $accessToken = trim((string) $this->request->getPost('access_token'));
        $order = $this->orders->findByInvoiceAndAccessToken($invoiceNumber, $accessToken);

        if (! $order) {
            return redirect()->back()->withInput()->with('error', 'Pesanan dengan Nomor Invoice tersebut tidak ditemukan.');
        }

        return redirect()->to('/pesanan/' . $order['invoice_number'] . '?token=' . $accessToken);
    }

    public function invoice(string $invoiceNumber): ResponseInterface
    {
        $token = trim((string) $this->request->getGet('token'));
        $order = $this->orders->findByInvoiceAndAccessToken($invoiceNumber, $token);

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
            'access_token' => $token,
        ]));
    }

    public function uploadPaymentProof(string $invoiceNumber)
    {
        helper('upload');
        $token = trim((string) $this->request->getPost('access_token'));
        $order = $this->orders->findByInvoiceAndAccessToken($invoiceNumber, $token);
        if (! $order) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (empty($order['payment_channel_name']) && ! empty($order['payment_channel_id'])) {
            $channel = (new PaymentChannelModel())->find((int) $order['payment_channel_id']);
            if ($channel) {
                $order['payment_channel_type'] = $channel['type'];
                $order['payment_channel_name'] = $channel['name'];
                $order['payment_account_number'] = $channel['account_number'];
                $order['payment_account_holder'] = $channel['account_holder'];
                $order['payment_qr_image_path'] = $channel['qr_image_path'];
            }
        }
        if ($order['status'] !== 'menunggu_pembayaran') {
            return redirect()->to('/pesanan/' . $invoiceNumber . '?token=' . $token)->with('error', 'Bukti pembayaran tidak dapat diubah pada status ini.');
        }

        $proof = $this->request->getFile('payment_proof');
        $rules = ['payment_proof' => 'uploaded[payment_proof]|max_size[payment_proof,5120]|is_image[payment_proof]|mime_in[payment_proof,image/jpeg,image/png,image/webp]|ext_in[payment_proof,jpg,jpeg,png,webp]'];
        if (! $this->validate($rules) || ! validate_uploaded_image_dimensions($proof)) {
            return redirect()->to('/pesanan/' . $invoiceNumber . '?token=' . $token)->with('errors', $this->validator?->getErrors() ?: ['payment_proof' => 'Bukti pembayaran tidak valid.']);
        }

        if (! is_dir(self::PROOF_PATH)) {
            mkdir(self::PROOF_PATH, 0750, true);
        }
        $filename = $proof->getRandomName();
        $proof->move(self::PROOF_PATH, $filename);
        $oldPath = $order['payment_proof_path'] ?? null;
        $saved = $this->orders->update($order['id'], [
            'payment_proof_path' => $filename,
            'payment_proof_uploaded_at' => date('Y-m-d H:i:s'),
            'payment_rejection_reason' => null,
            'status' => 'menunggu_verifikasi',
        ]);
        if (! $saved) {
            @unlink(self::PROOF_PATH . $filename);
            return redirect()->to('/pesanan/' . $invoiceNumber . '?token=' . $token)->with('error', 'Bukti pembayaran gagal disimpan.');
        }
        if ($oldPath && is_file(self::PROOF_PATH . basename($oldPath))) {
            @unlink(self::PROOF_PATH . basename($oldPath));
        }

        return redirect()->to('/pesanan/' . $invoiceNumber . '?token=' . $token)->with('success', 'Bukti pembayaran berhasil dikirim dan menunggu verifikasi.');
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
