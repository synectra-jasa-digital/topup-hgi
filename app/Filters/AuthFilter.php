<?php

namespace App\Filters;

use App\Models\AdminModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $adminId = (int) session()->get('admin_id');

        if ($adminId <= 0) {
            return redirect()->to('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Session hanya menyimpan snapshot role dan is_active sejak login.
        // Tanpa pengecekan ulang, akun yang dinonaktifkan, dihapus, atau
        // diturunkan rolenya tetap punya akses sampai sesi kedaluwarsa.
        // Role juga disegarkan di sini supaya RoleFilter tidak memakai nilai basi.
        $admin = (new AdminModel())->find($adminId);

        if ($admin === null || ! (bool) $admin['is_active']) {
            session()->destroy();

            return redirect()->to('/admin/login')
                ->with('error', 'Akun tidak aktif atau sudah dihapus. Silakan hubungi owner.');
        }

        if (session()->get('admin_role') !== $admin['role']) {
            session()->set('admin_role', $admin['role']);
            session()->set('admin_name', $admin['name']);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
