<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\VoucherModel;
use CodeIgniter\Exceptions\PageNotFoundException;

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

        return view('checkout/form', [
            'title'   => 'Checkout - Ayong Store',
            'product' => $product,
        ]);
    }

    public function store(int $productId)
    {
        $product = $this->findActiveProduct($productId);

        if (! $this->validate($this->orders->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $subtotal    = (float) $product['sell_price'];
        $voucherCode = trim((string) $this->request->getPost('voucher_code'));
        $voucher     = null;
        $discount    = 0.0;

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
            'discount_amount'       => $discount,
            'total_amount'          => $subtotal - $discount,
            'status'                => 'menunggu_pembayaran',
        ];

        $midtransService = new \App\Libraries\MidtransService();
        $snapToken = $midtransService->getSnapToken($data);
        $data['snap_token'] = $snapToken;

        if (! $this->orders->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->orders->errors());
        }

        if ($voucher) {
            $this->vouchers->incrementUsage($voucher['id']);
        }

        return redirect()->to('/pesanan/' . $data['invoice_number']);
    }

    public function checkStatus()
    {
        return view('checkout/check_status', [
            'title' => 'Cek Status Pesanan - Ayong Store'
        ]);
    }

    public function processCheckStatus()
    {
        $rules = [
            'invoice_number' => ['label' => 'Nomor Invoice', 'rules' => 'required|max_length[30]']
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

    public function invoice(string $invoiceNumber): string
    {
        $order = $this->orders->findByInvoice($invoiceNumber);

        if (! $order) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('checkout/invoice', [
            'title' => 'Invoice ' . $order['invoice_number'] . ' - Ayong Store',
            'order' => $order,
        ]);
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
