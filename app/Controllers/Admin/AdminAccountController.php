<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class AdminAccountController extends BaseController
{
    protected AdminModel $admins;

    public function __construct()
    {
        $this->admins = new AdminModel();
        helper('activity');
    }

    public function index()
    {
        return view('admin/admin_accounts/index', [
            'admins' => $this->admins->where('id !=', session()->get('admin_id'))
                                    ->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function create()
    {
        return view('admin/admin_accounts/form', ['admin' => null]);
    }

    public function store()
    {
        $rules = [
            'name'     => 'required|max_length[100]',
            'email'    => 'required|valid_email|max_length[150]|is_unique[admins.email]',
            'password' => 'required|min_length[8]',
            'role'     => 'required|in_list[owner,admin]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->admins->save([
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'      => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        log_activity('tambah_admin', 'Menambahkan akun admin: ' . $this->request->getPost('email'));

        return redirect()->to('/admin/akun-admin')->with('success', 'Akun admin berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $admin = $this->admins->find($id);
        if (! $admin) {
            return redirect()->to('/admin/akun-admin')->with('error', 'Akun admin tidak ditemukan.');
        }

        return view('admin/admin_accounts/form', ['admin' => $admin]);
    }

    public function update(int $id)
    {
        $admin = $this->admins->find($id);
        if (! $admin) {
            return redirect()->to('/admin/akun-admin')->with('error', 'Akun admin tidak ditemukan.');
        }

        $rules = [
            'name'  => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[150]|is_unique[admins.email,id,' . $id . ']',
            'role'  => 'required|in_list[owner,admin]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'id'        => $id,
            'name'      => $this->request->getPost('name'),
            'email'     => $this->request->getPost('email'),
            'role'      => $this->request->getPost('role'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $newPass = $this->request->getPost('password');
        if ($newPass !== '') {
            if (strlen($newPass) < 8) {
                return redirect()->back()->withInput()->with('errors', ['password' => 'Kata sandi minimal 8 karakter.']);
            }
            $data['password'] = password_hash($newPass, PASSWORD_BCRYPT);
        }

        $this->admins->save($data);
        log_activity('ubah_admin', 'Mengubah akun admin: ' . $admin['email']);

        return redirect()->to('/admin/akun-admin')->with('success', 'Akun admin berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $admin = $this->admins->find($id);
        if (! $admin) {
            return redirect()->to('/admin/akun-admin')->with('error', 'Akun admin tidak ditemukan.');
        }

        // Jangan bisa hapus diri sendiri
        if ((int) $id === (int) session()->get('admin_id')) {
            return redirect()->to('/admin/akun-admin')->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        log_activity('hapus_admin', 'Menghapus akun admin: ' . $admin['email']);
        $this->admins->delete($id);

        return redirect()->to('/admin/akun-admin')->with('success', 'Akun admin berhasil dihapus.');
    }
}
