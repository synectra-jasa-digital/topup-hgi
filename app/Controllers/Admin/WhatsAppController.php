<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\IntegrationSettings;
use App\Libraries\WhatsApp\BaileysGateway;
use App\Libraries\WhatsApp\WhatsAppNotifier;
use App\Models\StoreSettingModel;
use App\Models\WaOutboxModel;

class WhatsAppController extends BaseController
{
    protected StoreSettingModel $settings;
    protected WaOutboxModel $outbox;
    protected IntegrationSettings $integration;

    public function __construct()
    {
        helper(['activity', 'url', 'form']);
        $this->settings    = new StoreSettingModel();
        $this->outbox      = new WaOutboxModel();
        $this->integration = new IntegrationSettings();
    }

    public function index()
    {
        $baileys = new BaileysGateway();
        $status  = $baileys->status();

        $outboxMessages = $this->outbox->orderBy('id', 'DESC')->limit(50)->find();

        return view('admin/whatsapp/index', [
            'status'            => $status,
            'driver'            => $this->settings->getVal('wa_driver', 'off'),
            'gatewayUrl'        => $this->settings->getVal('wa_gateway_url', ''),
            'gatewayKeyState'   => $this->integration->state('wa_gateway_key', 'WA_GATEWAY_KEY'),
            'enableOrderCreated'  => $this->settings->getVal('wa_enable_order_created', '1'),
            'enablePaymentRecv'   => $this->settings->getVal('wa_enable_payment_received', '1'),
            'enablePaymentVerif'  => $this->settings->getVal('wa_enable_payment_verified', '1'),
            'enablePaymentRej'    => $this->settings->getVal('wa_enable_payment_rejected', '1'),
            'enableOrderCompl'    => $this->settings->getVal('wa_enable_order_completed', '1'),
            'enableBongkarStat'   => $this->settings->getVal('wa_enable_bongkar_status', '1'),
            'outboxMessages'    => $outboxMessages,
        ]);
    }

    public function updateSettings()
    {
        $driver     = (string) $this->request->getPost('wa_driver');
        $gatewayUrl = trim((string) $this->request->getPost('wa_gateway_url'));
        $gatewayKey = trim((string) $this->request->getPost('wa_gateway_key'));

        if (! in_array($driver, ['baileys', 'wablas', 'off'], true)) {
            $driver = 'off';
        }

        $this->settings->setVal('wa_driver', $driver);
        $this->settings->setVal('wa_gateway_url', $gatewayUrl);

        if ($gatewayKey !== '') {
            try {
                $this->integration->set('wa_gateway_key', $gatewayKey);
            } catch (\RuntimeException $e) {
                log_message('error', $e->getMessage());

                return redirect()->to(base_url('admin/whatsapp'))->with(
                    'error',
                    'Gagal menyimpan API key: enkripsi tidak terkonfigurasi. Set encryption.key di .env lalu coba lagi.'
                );
            }
        }

        $types = ['order_created', 'payment_received', 'payment_verified', 'payment_rejected', 'order_completed', 'bongkar_status'];
        foreach ($types as $t) {
            $val = $this->request->getPost("wa_enable_{$t}") === '1' ? '1' : '0';
            $this->settings->setVal("wa_enable_{$t}", $val);
        }

        log_activity('ubah_pengaturan_wa', 'Memperbarui pengaturan WhatsApp Gateway');

        return redirect()->to(base_url('admin/whatsapp'))->with('success', 'Pengaturan WhatsApp Gateway berhasil disimpan.');
    }

    public function sendTest()
    {
        $phone = trim((string) $this->request->getPost('phone'));
        if (empty($phone)) {
            return redirect()->to(base_url('admin/whatsapp'))->with('error', 'Nomor telepon wajib diisi.');
        }

        $notifier = new WhatsAppNotifier();
        $gateway  = $notifier->getDriver();

        if (! $gateway) {
            return redirect()->to(base_url('admin/whatsapp'))->with('error', 'Driver WhatsApp Gateway sedang non-aktif.');
        }

        $res = $gateway->send($phone, "Pesan Uji WhatsApp Gateway Ayong Store.\nWaktu: " . date('Y-m-d H:i:s'));

        if ($res['success']) {
            log_activity('tes_whatsapp', "Mengirim pesan tes WA ke {$phone}");
            return redirect()->to(base_url('admin/whatsapp'))->with('success', 'Pesan uji berhasil dikirim.');
        }

        return redirect()->to(base_url('admin/whatsapp'))->with('error', 'Gagal mengirim pesan uji: ' . ($res['error'] ?? 'Gagal'));
    }

    public function logout()
    {
        $baileys = new BaileysGateway();
        $ok      = $baileys->logout();

        log_activity('logout_whatsapp', 'Memutuskan sesi WhatsApp Business');

        if ($ok) {
            return redirect()->to(base_url('admin/whatsapp'))->with('success', 'Sesi WhatsApp berhasil diputuskan.');
        }

        return redirect()->to(base_url('admin/whatsapp'))->with('error', 'Gagal memutuskan sesi WhatsApp.');
    }

    public function retry(int $id)
    {
        $row = $this->outbox->find($id);
        if (! $row) {
            return redirect()->to(base_url('admin/whatsapp'))->with('error', 'Pesan tidak ditemukan.');
        }

        $this->outbox->update($id, [
            'status'        => 'pending',
            'attempts'      => 0,
            'next_retry_at' => null,
            'error_message' => null,
        ]);

        $notifier = new WhatsAppNotifier();
        $sent     = $notifier->sendOutboxImmediately($id);

        if ($sent) {
            log_activity('retry_wa_outbox', "Mengirim ulang pesan WA #{$id}");
            return redirect()->to(base_url('admin/whatsapp'))->with('success', "Pesan #{$id} berhasil dikirim ulang.");
        }

        return redirect()->to(base_url('admin/whatsapp'))->with('warning', "Pesan #{$id} dimasukkan ke antrean kirim ulang.");
    }

    public function getQr()
    {
        $baileys = new BaileysGateway();
        $qr      = $baileys->getQr();

        return $this->response->setJSON([
            'success' => $qr !== null,
            'qr'      => $qr,
        ]);
    }
}
