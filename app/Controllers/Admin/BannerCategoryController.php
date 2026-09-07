<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BannerCategoryModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class BannerCategoryController extends BaseController
{
    protected BannerCategoryModel $categories;

    public function __construct()
    {
        $this->categories = new BannerCategoryModel();
    }

    public function index()
    {
        return view('admin/banner_categories/index', [
            'categories' => $this->categories->orderBy('id')->findAll(),
        ]);
    }

    public function create()
    {
        return view('admin/banner_categories/form', ['category' => null]);
    }

    public function store()
    {
        if (! $this->categories->save(['name' => $this->request->getPost('name')])) {
            return redirect()->back()->withInput()->with('errors', $this->categories->errors());
        }

        return redirect()->to('/admin/kategori-banner')->with('success', 'Kategori banner berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $category = $this->categories->find($id);

        if (! $category) {
            return redirect()->to('/admin/kategori-banner')->with('error', 'Kategori banner tidak ditemukan.');
        }

        return view('admin/banner_categories/form', ['category' => $category]);
    }

    public function update(int $id)
    {
        if (! $this->categories->find($id)) {
            return redirect()->to('/admin/kategori-banner')->with('error', 'Kategori banner tidak ditemukan.');
        }

        if (! $this->categories->save(['id' => $id, 'name' => $this->request->getPost('name')])) {
            return redirect()->back()->withInput()->with('errors', $this->categories->errors());
        }

        return redirect()->to('/admin/kategori-banner')->with('success', 'Kategori banner berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        try {
            $this->categories->delete($id);
        } catch (DatabaseException $e) {
            return redirect()->to('/admin/kategori-banner')->with('error', 'Kategori tidak bisa dihapus karena masih memiliki banner.');
        }

        return redirect()->to('/admin/kategori-banner')->with('success', 'Kategori banner berhasil dihapus.');
    }
}
