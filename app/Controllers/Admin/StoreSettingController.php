<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StoreSettingModel;

class StoreSettingController extends BaseController
{
    private const UPLOAD_PATH = FCPATH . 'assets/uploads/store/';

    protected StoreSettingModel $settings;

    public function __construct()
    {
        helper('upload');
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
        $rules = ['store_name' => 'required|max_length[150]'];

        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid()) {
            if (! validate_uploaded_image_dimensions($logo, 2048, 2048)) {
                return redirect()->back()->withInput()->with('errors', ['logo' => 'Dimensi logo tidak valid atau melebihi batas.']);
            }
            $rules['logo'] = 'max_size[logo,2048]|is_image[logo]|mime_in[logo,image/jpeg,image/png,image/webp]|ext_in[logo,jpg,jpeg,png,webp]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'store_name'    => $this->request->getPost('store_name'),
            'store_contact' => $this->request->getPost('store_contact'),
            'store_address' => $this->request->getPost('store_address'),
            'maintenance'   => $this->request->getPost('maintenance') ? '1' : '0',
        ];

        $wablasToken = trim((string) $this->request->getPost('wablas_key'));
        if ($wablasToken !== '') {
            $data['wablas_key'] = (new \App\Libraries\IntegrationSettings($this->settings))->encrypt($wablasToken);
        }

        if ($logo && $logo->isValid()) {
            $data['store_logo'] = $this->storeLogo($logo);
        }

        $oldLogo = $this->settings->getVal('store_logo');
        $this->settings->batchSave($data);

        if (isset($data['store_logo']) && $oldLogo !== $data['store_logo']) {
            delete_public_asset($oldLogo);
        }

        return redirect()->to('/admin/pengaturan-toko')->with('success', 'Pengaturan toko berhasil disimpan.');
    }

    private function storeLogo($file): string
    {
        if (! is_dir(self::UPLOAD_PATH)) {
            mkdir(self::UPLOAD_PATH, 0755, true);
        }

        $filename = $file->getRandomName();
        $file->move(self::UPLOAD_PATH, $filename);

        return 'assets/uploads/store/' . $filename;
    }
}
