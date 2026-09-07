<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class AuthController extends BaseController
{
    public function loginForm()
    {
        if (session()->get('admin_id')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('admin/auth/login');
    }

    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Email dan kata sandi wajib diisi dengan benar.');
        }

        $admin = (new AdminModel())->findActiveByEmail($this->request->getPost('email'));

        if (! $admin || ! password_verify($this->request->getPost('password'), $admin['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau kata sandi salah.');
        }

        session()->set([
            'admin_id'    => $admin['id'],
            'admin_name'  => $admin['name'],
            'admin_role'  => $admin['role'],
        ]);

        return redirect()->to('/admin/dashboard')->with('success', 'Berhasil masuk.');
    }

    public function logout()
    {
        session()->remove(['admin_id', 'admin_name', 'admin_role']);

        return redirect()->to('/admin/login')->with('success', 'Berhasil keluar.');
    }
}
