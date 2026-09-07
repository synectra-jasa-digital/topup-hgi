<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StoreSettingModel;

class StoreSettingController extends BaseController
{
    protected StoreSettingModel $settings;

    public function __construct()
    {
        $this->settings = new StoreSettingModel();
    }

    public function index()
    {
        return view('admin/store_settings/index', [
            'settings' => $this->settings->getAll(),
        ]);
    }

    public function update()
    {
        $data = [
            'store_name'    => $this->request->getPost('store_name'),
            'store_contact' => $this->request->getPost('store_contact'),
            'store_address' => $this->request->getPost('store_address'),
            'midtrans_key'  => $this->request->getPost('midtrans_key'),
            'wablas_key'    => $this->request->getPost('wablas_key'),
            'maintenance'   => $this->request->getPost('maintenance') ? '1' : '0',
        ];

        // Lakukan validasi minimal (opsional namun disarankan)
        $this->validate([
            'store_name' => 'required|max_length[150]',
        ]);

        $this->settings->batchSave($data);

        return redirect()->to('/admin/pengaturan-toko')->with('success', 'Pengaturan toko berhasil disimpan.');
    }
}
