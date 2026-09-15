<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BongkarPayoutMethodModel;

class BongkarPayoutMethodController extends BaseController
{
    protected BongkarPayoutMethodModel $methods;

    public function __construct()
    {
        $this->methods = new BongkarPayoutMethodModel();
    }

    public function index()
    {
        return view('admin/bongkar_payout_methods/index', [
            'methods' => $this->methods->orderBy('sort_order')->findAll(),
        ]);
    }

    public function create()
    {
        return view('admin/bongkar_payout_methods/form', ['method' => null]);
    }

    public function store()
    {
        if (! $this->methods->save($this->methodData())) {
            return redirect()->back()->withInput()->with('errors', $this->methods->errors());
        }

        return redirect()->to('/admin/bongkar-metode-pencairan')->with('success', 'Metode pencairan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $method = $this->methods->find($id);
        if (! $method) {
            return redirect()->to('/admin/bongkar-metode-pencairan')->with('error', 'Metode pencairan tidak ditemukan.');
        }

        return view('admin/bongkar_payout_methods/form', ['method' => $method]);
    }

    public function update(int $id)
    {
        if (! $this->methods->find($id)) {
            return redirect()->to('/admin/bongkar-metode-pencairan')->with('error', 'Metode pencairan tidak ditemukan.');
        }

        $data = $this->methodData();
        $data['id'] = $id;

        if (! $this->methods->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->methods->errors());
        }

        return redirect()->to('/admin/bongkar-metode-pencairan')->with('success', 'Metode pencairan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->methods->delete($id);

        return redirect()->to('/admin/bongkar-metode-pencairan')->with('success', 'Metode pencairan berhasil dihapus.');
    }

    private function methodData(): array
    {
        return [
            'code'       => trim((string) $this->request->getPost('code')),
            'name'       => trim((string) $this->request->getPost('name')),
            'category'   => $this->request->getPost('category') === 'ewallet' ? 'ewallet' : 'bank',
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }
}
