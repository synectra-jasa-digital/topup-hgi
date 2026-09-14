<?php

namespace App\Controllers;

use App\Models\BongkarCatalogModel;
use App\Models\BongkarRequestModel;
use CodeIgniter\API\ResponseTrait;

class BongkarController extends BaseController
{
    use ResponseTrait;

    private const MAX_QUANTITY = 1000;
    private const MAX_NOTE_LENGTH = 500;
    private const PAYOUT_METHODS = [
        'BCA', 'BRI', 'Mandiri', 'BNI', 'DANA', 'GoPay', 'OVO', 'ShopeePay', 'Seabank',
    ];

    public function submit()
    {
        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost();
        }

        $catalogId = (int) ($payload['bongkar_catalog_id'] ?? 0);
        $quantity = filter_var($payload['quantity'] ?? null, FILTER_VALIDATE_INT);
        $customerWhatsapp = trim((string) ($payload['customer_whatsapp'] ?? ''));
        $payoutMethod = trim((string) ($payload['payout_method'] ?? ''));
        $customerNote = trim((string) ($payload['customer_note'] ?? ''));

        $errors = [];
        if ($catalogId <= 0) {
            $errors['bongkar_catalog_id'] = 'Jenis item tidak valid.';
        }
        if (! is_int($quantity) || $quantity < 1 || $quantity > self::MAX_QUANTITY) {
            $errors['quantity'] = 'Jumlah harus antara 1 dan ' . self::MAX_QUANTITY . '.';
        }
        if (! preg_match('/^(08|628)[0-9]{7,12}$/', $customerWhatsapp)) {
            $errors['customer_whatsapp'] = 'Nomor WhatsApp tidak valid.';
        }
        if (! in_array($payoutMethod, self::PAYOUT_METHODS, true)) {
            $errors['payout_method'] = 'Metode pencairan tidak valid.';
        }
        if (mb_strlen($customerNote) > self::MAX_NOTE_LENGTH) {
            $errors['customer_note'] = 'Catatan terlalu panjang.';
        }
        if ($errors !== []) {
            return $this->failValidationErrors($errors);
        }

        $catalog = (new BongkarCatalogModel())->find($catalogId);
        if (! $catalog || ! (int) ($catalog['is_active'] ?? 0)) {
            return $this->failNotFound('Catalog not found');
        }

        $rate = (float) $catalog['base_rate'];
        $estimatedAmount = $quantity * $rate;
        $requestNumber = 'BR' . date('Ymd') . strtoupper(bin2hex(random_bytes(3)));

        $requestModel = new BongkarRequestModel();
        $inserted = $requestModel->insert([
            'request_number' => $requestNumber,
            'bongkar_catalog_id' => $catalogId,
            'catalog_code_snapshot' => $catalog['code'],
            'catalog_name_snapshot' => $catalog['name'],
            'unit_label_snapshot' => $catalog['unit_label'],
            'quantity' => $quantity,
            'rate_snapshot' => $rate,
            'estimated_amount' => $estimatedAmount,
            'customer_whatsapp' => $customerWhatsapp,
            'payout_method' => $payoutMethod,
            'customer_note' => $customerNote,
            'status' => 'pending',
        ]);

        if ($inserted === false) {
            return $this->failServerError('Pengajuan bongkar gagal disimpan.');
        }

        $adminPhone = trim((string) (getenv('wablas.adminPhone') ?: ($_ENV['wablas.adminPhone'] ?? '')));
        $waUrl = $adminPhone !== ''
            ? 'https://wa.me/' . preg_replace('/\D+/', '', $adminPhone) . '?text=' . rawurlencode('Pengajuan bongkar ' . $requestNumber)
            : '';

        return $this->respond([
            'success' => true,
            'request_number' => $requestNumber,
            'wa_url' => $waUrl,
        ]);
    }
}
