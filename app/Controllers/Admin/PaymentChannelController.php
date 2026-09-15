<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaymentChannelModel;

class PaymentChannelController extends BaseController
{
    private const UPLOAD_PATH = FCPATH . 'assets/uploads/payment_channels/';

    protected PaymentChannelModel $channels;

    public function __construct()
    {
        helper('upload');
        $this->channels = new PaymentChannelModel();
    }

    public function index()
    {
        return view('admin/payment_channels/index', [
            'channels' => $this->channels->orderBy('sort_order')->findAll(),
        ]);
    }

    public function create()
    {
        return view('admin/payment_channels/form', ['channel' => null]);
    }

    public function store()
    {
        $rules = $this->channels->getValidationRules();
        $type = $this->request->getPost('type') === 'qris' ? 'qris' : 'bank';

        if ($type === 'bank') {
            $rules['account_number'] = 'required|max_length[50]';
            $rules['account_holder'] = 'required|max_length[150]';
        } else {
            $rules['qr_image'] = 'uploaded[qr_image]|max_size[qr_image,2048]|is_image[qr_image]|mime_in[qr_image,image/jpeg,image/png,image/webp]|ext_in[qr_image,jpg,jpeg,png,webp]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->channelData($type);
        if ($type === 'qris') {
            $data['qr_image_path'] = $this->storeQrImage();
        }

        if (! $this->channels->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->channels->errors());
        }

        return redirect()->to('/admin/metode-bayar')->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $channel = $this->channels->find($id);
        if (! $channel) {
            return redirect()->to('/admin/metode-bayar')->with('error', 'Metode pembayaran tidak ditemukan.');
        }

        return view('admin/payment_channels/form', ['channel' => $channel]);
    }

    public function update(int $id)
    {
        $channel = $this->channels->find($id);
        if (! $channel) {
            return redirect()->to('/admin/metode-bayar')->with('error', 'Metode pembayaran tidak ditemukan.');
        }

        $type = $this->request->getPost('type') === 'qris' ? 'qris' : 'bank';
        $qrImage = $this->request->getFile('qr_image');

        $rules = $this->channels->getValidationRules();
        if ($type === 'bank') {
            $rules['account_number'] = 'required|max_length[50]';
            $rules['account_holder'] = 'required|max_length[150]';
        } elseif ($qrImage && $qrImage->isValid()) {
            if (! validate_uploaded_image_dimensions($qrImage)) {
                return redirect()->back()->withInput()->with('errors', ['qr_image' => 'Dimensi gambar tidak valid atau melebihi batas.']);
            }
            $rules['qr_image'] = 'max_size[qr_image,2048]|is_image[qr_image]|mime_in[qr_image,image/jpeg,image/png,image/webp]|ext_in[qr_image,jpg,jpeg,png,webp]';
        } elseif (empty($channel['qr_image_path'])) {
            $rules['qr_image'] = 'uploaded[qr_image]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->channelData($type);
        $data['id'] = $id;

        if ($type === 'qris' && $qrImage && $qrImage->isValid()) {
            $data['qr_image_path'] = $this->storeQrImage();
        }

        if (! $this->channels->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->channels->errors());
        }

        if (isset($data['qr_image_path']) && ! empty($channel['qr_image_path']) && $channel['qr_image_path'] !== $data['qr_image_path']) {
            delete_public_asset($channel['qr_image_path']);
        }

        return redirect()->to('/admin/metode-bayar')->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $channel = $this->channels->find($id);
        if ($channel) {
            $this->channels->delete($id);
            delete_public_asset($channel['qr_image_path'] ?? null);
        }

        return redirect()->to('/admin/metode-bayar')->with('success', 'Metode pembayaran berhasil dihapus.');
    }

    private function channelData(string $type): array
    {
        return [
            'type'           => $type,
            'name'           => trim((string) $this->request->getPost('name')),
            'account_number' => $type === 'bank' ? trim((string) $this->request->getPost('account_number')) : null,
            'account_holder' => $type === 'bank' ? trim((string) $this->request->getPost('account_holder')) : null,
            'sort_order'     => (int) $this->request->getPost('sort_order'),
            'is_active'      => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    private function storeQrImage(): string
    {
        if (! is_dir(self::UPLOAD_PATH)) {
            mkdir(self::UPLOAD_PATH, 0755, true);
        }

        $image    = $this->request->getFile('qr_image');
        $filename = $image->getRandomName();
        $image->move(self::UPLOAD_PATH, $filename);

        return 'assets/uploads/payment_channels/' . $filename;
    }
}
