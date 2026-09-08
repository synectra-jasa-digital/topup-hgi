<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BongkarRequestModel;

class BongkarRequestController extends BaseController
{
    protected BongkarRequestModel $requests;

    public function __construct()
    {
        $this->requests = new BongkarRequestModel();
        helper('activity');
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        if (! in_array($status, BongkarRequestModel::adminStatuses(), true)) {
            $status = null;
        }

        return view('admin/bongkar_requests/index', [
            'requests' => $this->requests->adminList($status),
            'pager'    => $this->requests->pager,
            'status'   => $status,
            'statuses' => BongkarRequestModel::adminStatuses(),
        ]);
    }

    public function show(int $id)
    {
        $bongkarRequest = $this->requests->find($id);
        if (! $bongkarRequest) {
            return redirect()->to('/admin/bongkar-pesanan')->with('error', 'Pengajuan bongkar tidak ditemukan.');
        }

        return view('admin/bongkar_requests/detail', ['bongkarRequest' => $bongkarRequest]);
    }

    public function updateStatus(int $id)
    {
        $bongkarRequest = $this->requests->find($id);
        if (! $bongkarRequest) {
            return redirect()->to('/admin/bongkar-pesanan')->with('error', 'Pengajuan bongkar tidak ditemukan.');
        }

        $status = $this->request->getPost('status');
        if (! in_array($status, BongkarRequestModel::adminStatuses(), true)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $this->requests->update($id, ['status' => $status]);
        log_activity(
            'ubah_status_bongkar',
            'Mengubah status pengajuan bongkar ' . $bongkarRequest['request_number'] . ' menjadi ' . bongkar_status_label($status) . '.'
        );

        return redirect()->to('/admin/bongkar-pesanan/' . $id)->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
