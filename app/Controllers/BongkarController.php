<?php

namespace App\Controllers;

use App\Models\BongkarCatalogModel;
use App\Models\BongkarRequestModel;
use CodeIgniter\API\ResponseTrait;

class BongkarController extends BaseController
{
    use ResponseTrait;

    public function submit()
    {
        $payload = $this->request->getJSON(true);
        if (! is_array($payload)) {
            $payload = $this->request->getPost();
        }

        $catalogId = (int) ($payload['bongkar_catalog_id'] ?? 0);
        $quantity = max(1, (int) ($payload['quantity'] ?? 1));
        $customerWhatsapp = trim((string) ($payload['customer_whatsapp'] ?? ''));
        $payoutMethod = trim((string) ($payload['payout_method'] ?? ''));
        $customerNote = trim((string) ($payload['customer_note'] ?? ''));

        if ($catalogId <= 0 || $customerWhatsapp === '' || $payoutMethod === '') {
            return $this->failValidationErrors('Data pengajuan bongkar belum lengkap.');
        }

        $catalog = (new BongkarCatalogModel())->find($catalogId);
        if (! $catalog || ! (int) ($catalog['is_active'] ?? 0)) {
            return $this->failNotFound('Catalog not found');
        }

        $rate = (float) $catalog['base_rate'];
        $estimatedAmount = $quantity * $rate;
        $requestNumber = 'BR' . date('Ymd') . strtoupper(bin2hex(random_bytes(3)));

        $requestModel = new BongkarRequestModel();
        $requestModel->insert([
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
