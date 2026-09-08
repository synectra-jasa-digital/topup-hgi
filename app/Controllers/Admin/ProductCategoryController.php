<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductCategoryModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ProductCategoryController extends BaseController
{
    private const UPLOAD_PATH = FCPATH . 'assets/uploads/category_icons/';

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
        $icon = $this->request->getFile('icon');
        if ($icon && $icon->isValid() && ! $this->validate(['icon' => 'max_size[icon,1024]|is_image[icon]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name'       => $this->request->getPost('name'),
            'slug'       => url_title($this->request->getPost('name'), '-', true),
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($icon && $icon->isValid()) {
            $data['icon'] = $this->storeIcon($icon);
        }

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

        $icon = $this->request->getFile('icon');
        if ($icon && $icon->isValid() && ! $this->validate(['icon' => 'max_size[icon,1024]|is_image[icon]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'id'         => $id,
            'name'       => $this->request->getPost('name'),
            'slug'       => url_title($this->request->getPost('name'), '-', true),
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($icon && $icon->isValid()) {
            $data['icon'] = $this->storeIcon($icon);
        }

        if (! $this->categories->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->categories->errors());
        }

        return redirect()->to('/admin/kategori-produk')->with('success', 'Kategori berhasil diperbarui.');
    }

    private function storeIcon($file): string
    {
        if (! is_dir(self::UPLOAD_PATH)) {
            mkdir(self::UPLOAD_PATH, 0755, true);
        }

        $filename = $file->getRandomName();
        $file->move(self::UPLOAD_PATH, $filename);

        return 'assets/uploads/category_icons/' . $filename;
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
