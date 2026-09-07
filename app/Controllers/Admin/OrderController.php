<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\WablasGateway;
use App\Models\OrderModel;

class OrderController extends BaseController
{
    protected OrderModel $orders;
    protected WablasGateway $wablas;

    public function __construct(?WablasGateway $wablas = null)
    {
        $this->orders = new OrderModel();
        $this->wablas = $wablas ?? new WablasGateway();
    }

    public function index()
    {
        $status = $this->request->getGet('status');
        if (! in_array($status, OrderModel::adminStatuses(), true)) {
            $status = null;
        }
        return view('admin/orders/index', [
            'orders' => $this->orders->adminList($status),
            'pager' => $this->orders->pager,
            'status' => $status,
            'statuses' => OrderModel::adminStatuses(),
        ]);
    }

    public function show(int $id)
    {
        $order = $this->orders->find($id);
        if (! $order) {
            return redirect()->to('/admin/pesanan')->with('error', 'Pesanan tidak ditemukan.');
        }
        return view('admin/orders/detail', ['order' => $order]);
    }

    public function complete(int $id)
    {
        $adminId = (int) session()->get('admin_id');
        if (! $this->orders->complete($id, $adminId)) {
            return redirect()->back()->with('error', 'Pesanan tidak dapat diselesaikan. Hanya pesanan berstatus Diproses yang dapat diubah.');
        }

        $order = $this->orders->find($id);
        if ($order && $this->wablas->sendToCustomerOrderCompleted($order)) {
            return redirect()->to('/admin/pesanan/' . $id)->with('success', 'Pesanan selesai dan notifikasi WhatsApp terkirim.');
        }
        return redirect()->to('/admin/pesanan/' . $id)->with('warning', 'Pesanan selesai, tetapi notifikasi WhatsApp gagal terkirim. Silakan kirim ulang secara manual.');
    }
}
