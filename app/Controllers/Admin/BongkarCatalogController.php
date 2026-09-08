<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BongkarCatalogModel;
use App\Models\BongkarRequestModel;

class BongkarCatalogController extends BaseController
{
    protected BongkarCatalogModel $catalogs;

    public function __construct()
    {
        $this->catalogs = new BongkarCatalogModel();
    }

    public function index()
    {
        return view('admin/bongkar_catalogs/index', [
            'catalogs' => $this->catalogs->orderBy('sort_order')->findAll(),
        ]);
    }

    public function create()
    {
        return view('admin/bongkar_catalogs/form', ['catalog' => null]);
    }

    public function store()
    {
        if (! $this->catalogs->save($this->catalogData())) {
            return redirect()->back()->withInput()->with('errors', $this->catalogs->errors());
        }

        return redirect()->to('/admin/bongkar-katalog')->with('success', 'Katalog bongkar berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $catalog = $this->catalogs->find($id);
        if (! $catalog) {
            return redirect()->to('/admin/bongkar-katalog')->with('error', 'Katalog bongkar tidak ditemukan.');
        }

        return view('admin/bongkar_catalogs/form', ['catalog' => $catalog]);
    }

    public function update(int $id)
    {
        if (! $this->catalogs->find($id)) {
            return redirect()->to('/admin/bongkar-katalog')->with('error', 'Katalog bongkar tidak ditemukan.');
        }

        $data = $this->catalogData();
        $data['id'] = $id;

        if (! $this->catalogs->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->catalogs->errors());
        }

        return redirect()->to('/admin/bongkar-katalog')->with('success', 'Katalog bongkar berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $hasRequests = (new BongkarRequestModel())->where('bongkar_catalog_id', $id)->countAllResults() > 0;
        if ($hasRequests) {
            return redirect()->to('/admin/bongkar-katalog')->with('error', 'Katalog tidak bisa dihapus karena sudah memiliki riwayat pengajuan bongkar. Nonaktifkan saja jika ingin menyembunyikannya.');
        }

        $this->catalogs->delete($id);

        return redirect()->to('/admin/bongkar-katalog')->with('success', 'Katalog bongkar berhasil dihapus.');
    }

    private function catalogData(): array
    {
        return [
            'code'       => strtoupper(trim((string) $this->request->getPost('code'))),
            'name'       => $this->request->getPost('name'),
            'unit_label' => $this->request->getPost('unit_label'),
            'base_rate'  => $this->request->getPost('base_rate'),
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }
}
