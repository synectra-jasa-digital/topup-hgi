<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\WablasGateway;
use App\Models\BongkarRequestModel;

class BongkarRequestController extends BaseController
{
    protected BongkarRequestModel $requests;
    protected WablasGateway $wablas;

    public function __construct(?WablasGateway $wablas = null)
    {
        $this->requests = new BongkarRequestModel();
        $this->wablas = $wablas ?? new WablasGateway();
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

        if (! $this->requests->canTransition($bongkarRequest['status'], $status)) {
            return redirect()->back()->with('error', 'Transisi status tidak diperbolehkan.');
        }

        $updateData = [
            'status' => $status,
            'actor_admin_id' => session()->get('admin_id'),
            'previous_status' => $bongkarRequest['status'],
            'status_changed_at' => date('Y-m-d H:i:s'),
        ];

        $updateData['notification_status'] = 'pending';
        $updateData['notification_retry_count'] = 0;
        $updateData['next_retry_at'] = date('Y-m-d H:i:s');
        if (! $this->requests->update($id, $updateData)) {
            return redirect()->back()->with('error', 'Status pengajuan gagal diperbarui.');
        }

        if ($this->requests->claimNotification($id)) {
            $updatedRequest = $this->requests->find($id);
            try {
                $notificationSent = $this->wablas->sendToCustomerBongkarStatus($updatedRequest);
            } catch (\Throwable $e) {
                log_message('error', 'Failed to send bongkar notification: ' . $e->getMessage());
                $notificationSent = false;
            }
            $this->requests->markNotificationResult($id, $notificationSent);
        }

        log_activity(
            'ubah_status_bongkar',
            'Mengubah status pengajuan bongkar ' . $bongkarRequest['request_number'] . ' menjadi ' . bongkar_status_label($status) . '.'
        );

        return redirect()->to('/admin/bongkar-pesanan/' . $id)->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
