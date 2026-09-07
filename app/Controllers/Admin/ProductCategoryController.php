<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductCategoryModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ProductCategoryController extends BaseController
{
    protected ProductCategoryModel $categories;

    public function __construct()
    {
        $this->categories = new ProductCategoryModel();
        helper('text');
    }

    public function index()
    {
        return view('admin/product_categories/index', [
            'categories' => $this->categories->orderBy('sort_order')->findAll(),
        ]);
    }

    public function create()
    {
        return view('admin/product_categories/form', ['category' => null]);
    }

    public function store()
    {
        $data = [
            'name'       => $this->request->getPost('name'),
            'slug'       => url_title($this->request->getPost('name'), '-', true),
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if (! $this->categories->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->categories->errors());
        }

        return redirect()->to('/admin/kategori-produk')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $category = $this->categories->find($id);

        if (! $category) {
            return redirect()->to('/admin/kategori-produk')->with('error', 'Kategori tidak ditemukan.');
        }

        return view('admin/product_categories/form', ['category' => $category]);
    }

    public function update(int $id)
    {
        $category = $this->categories->find($id);

        if (! $category) {
            return redirect()->to('/admin/kategori-produk')->with('error', 'Kategori tidak ditemukan.');
        }

        $data = [
            'id'         => $id,
            'name'       => $this->request->getPost('name'),
            'slug'       => url_title($this->request->getPost('name'), '-', true),
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if (! $this->categories->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->categories->errors());
        }

        return redirect()->to('/admin/kategori-produk')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        try {
            $this->categories->delete($id);
        } catch (DatabaseException $e) {
            return redirect()->to('/admin/kategori-produk')->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk.');
        }

        return redirect()->to('/admin/kategori-produk')->with('success', 'Kategori berhasil dihapus.');
    }
}
