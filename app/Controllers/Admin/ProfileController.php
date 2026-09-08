<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class ProfileController extends BaseController
{
    private const UPLOAD_PATH = FCPATH . 'assets/uploads/avatars/';

    protected AdminModel $admins;

    public function __construct()
    {
        $this->admins = new AdminModel();
        helper('activity');
    }

    public function index()
    {
        $admin = $this->admins->find(session('admin_id'));
        if (! $admin) {
            return redirect()->to('/admin/login');
        }

        return view('admin/profile/index', ['admin' => $admin]);
    }

    public function update()
    {
        $adminId = (int) session('admin_id');
        $admin   = $this->admins->find($adminId);
        if (! $admin) {
            return redirect()->to('/admin/login');
        }

        $rules = [
            'name'  => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[150]|is_unique[admins.email,id,' . $adminId . ']',
        ];

        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid()) {
            $rules['photo'] = 'max_size[photo,2048]|is_image[photo]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newPassword = $this->request->getPost('password');
        if ($newPassword !== '' && $newPassword !== null && strlen($newPassword) < 8) {
            return redirect()->back()->withInput()->with('errors', ['password' => 'Kata sandi minimal 8 karakter.']);
        }

        $data = [
            'id'    => $adminId,
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ];

        if ($newPassword !== '' && $newPassword !== null) {
            $data['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        if ($photo && $photo->isValid()) {
            $data['photo'] = $this->storePhoto($photo);
        }

        $this->admins->save($data);

        session()->set('admin_name', $data['name']);
        if (isset($data['photo'])) {
            session()->set('admin_photo', $data['photo']);
        }

        log_activity('ubah_profil', 'Memperbarui profil sendiri.');

        return redirect()->to('/admin/profile')->with('success', 'Profil berhasil diperbarui.');
    }

    private function storePhoto($file): string
    {
        if (! is_dir(self::UPLOAD_PATH)) {
            mkdir(self::UPLOAD_PATH, 0755, true);
        }

        $filename = $file->getRandomName();
        $file->move(self::UPLOAD_PATH, $filename);

        return 'assets/uploads/avatars/' . $filename;
    }
}
