<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;

class OrderController extends BaseController
{
    protected OrderModel $orders;

    public function __construct()
    {
        $this->orders = new OrderModel();
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
        if ($order) {
            (new \App\Libraries\WhatsApp\WhatsAppNotifier())->orderCompleted($order);
        }

        return redirect()->to('/admin/pesanan/' . $id)->with('success', 'Pesanan diselesaikan.');
    }

    public function proof(int $id)
    {
        $order = $this->orders->find($id);
        $path = $order ? WRITEPATH . 'uploads/payment-proofs/' . basename((string) $order['payment_proof_path']) : '';
        if (! $order || empty($order['payment_proof_path']) || ! is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response->download($path, null)->setFileName('bukti-' . $order['invoice_number'] . '.' . pathinfo($path, PATHINFO_EXTENSION));
    }

    public function verify(int $id)
    {
        if (! $this->orders->verifyPayment($id, (int) session()->get('admin_id'))) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak dapat diverifikasi.');
        }

        $order = $this->orders->find($id);
        if ($order) {
            (new \App\Libraries\WhatsApp\WhatsAppNotifier())->paymentVerified($order);
        }

        return redirect()->to('/admin/pesanan/' . $id)->with('success', 'Pembayaran diverifikasi dan pesanan siap diproses.');
    }

    public function reject(int $id)
    {
        $reason = trim((string) $this->request->getPost('reason'));
        if ($reason === '' || mb_strlen($reason) > 500) {
            return redirect()->back()->with('error', 'Alasan penolakan wajib diisi dan maksimal 500 karakter.');
        }
        if (! $this->orders->rejectPayment($id, (int) session()->get('admin_id'), $reason)) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak dapat ditolak.');
        }

        $order = $this->orders->find($id);
        if ($order) {
            (new \App\Libraries\WhatsApp\WhatsAppNotifier())->paymentRejected($order, $reason);
        }

        return redirect()->to('/admin/pesanan/' . $id)->with('success', 'Bukti ditolak. Customer dapat mengunggah bukti baru.');
    }

    public function previewProof(int $id)
    {
        $order = $this->orders->find($id);
        if (! $order || empty($order['payment_proof_path'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $filePath = WRITEPATH . 'uploads/payment-proofs/' . basename((string) $order['payment_proof_path']);
        if (! is_file($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $mime = mime_content_type($filePath);
        if ($mime === false) {
            $mime = 'image/jpeg';
        }
        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . esc(basename((string) $order['payment_proof_path'])) . '"')
            ->setBody(file_get_contents($filePath));
    }
}
