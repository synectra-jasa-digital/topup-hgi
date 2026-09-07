<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VoucherModel;

class VoucherController extends BaseController
{
    protected VoucherModel $vouchers;

    public function __construct()
    {
        $this->vouchers = new VoucherModel();
    }

    public function index()
    {
        return view('admin/vouchers/index', [
            'vouchers' => $this->vouchers->orderBy('created_at', 'DESC')->paginate(20),
            'pager'    => $this->vouchers->pager,
        ]);
    }

    public function create()
    {
        return view('admin/vouchers/form', ['voucher' => null]);
    }

    public function store()
    {
        $data = $this->voucherData();

        if (! $this->vouchers->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->vouchers->errors());
        }

        return redirect()->to('/admin/voucher')->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $voucher = $this->vouchers->find($id);
        if (! $voucher) {
            return redirect()->to('/admin/voucher')->with('error', 'Voucher tidak ditemukan.');
        }

        return view('admin/vouchers/form', ['voucher' => $voucher]);
    }

    public function update(int $id)
    {
        $voucher = $this->vouchers->find($id);
        if (! $voucher) {
            return redirect()->to('/admin/voucher')->with('error', 'Voucher tidak ditemukan.');
        }
        $data       = $this->voucherData();
        $data['id'] = $id;

        if (! $this->vouchers->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->vouchers->errors());
        }

        return redirect()->to('/admin/voucher')->with('success', 'Voucher berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->vouchers->delete($id);
        return redirect()->to('/admin/voucher')->with('success', 'Voucher berhasil dihapus.');
    }

    private function voucherData(): array
    {
        return [
            'code'         => strtoupper(trim($this->request->getPost('code'))),
            'type'         => $this->request->getPost('type'),
            'value'        => (float) $this->request->getPost('value'),
            'min_purchase' => (float) ($this->request->getPost('min_purchase') ?: 0),
            'max_discount' => $this->request->getPost('max_discount') !== '' ? (float) $this->request->getPost('max_discount') : null,
            'quota'        => (int) ($this->request->getPost('quota') ?: 0),
            'start_date'   => $this->request->getPost('start_date') ?: null,
            'end_date'     => $this->request->getPost('end_date') ?: null,
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }
}
