<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductCategoryModel;
use App\Models\ProductModel;

class ProductController extends BaseController
{
    protected ProductModel $products;
    protected ProductCategoryModel $categories;

    public function __construct()
    {
        $this->products   = new ProductModel();
        $this->categories = new ProductCategoryModel();
    }

    public function index()
    {
        return view('admin/products/index', [
            'products' => $this->products->listWithCategory(),
        ]);
    }

    public function create()
    {
        return view('admin/products/form', [
            'product'    => null,
            'categories' => $this->categories->orderBy('sort_order')->findAll(),
        ]);
    }

    public function store()
    {
        if (! $this->products->save($this->productData())) {
            return redirect()->back()->withInput()->with('errors', $this->products->errors());
        }

        return redirect()->to('/admin/produk')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $product = $this->products->find($id);

        if (! $product) {
            return redirect()->to('/admin/produk')->with('error', 'Produk tidak ditemukan.');
        }

        return view('admin/products/form', [
            'product'    => $product,
            'categories' => $this->categories->orderBy('sort_order')->findAll(),
        ]);
    }

    public function update(int $id)
    {
        if (! $this->products->find($id)) {
            return redirect()->to('/admin/produk')->with('error', 'Produk tidak ditemukan.');
        }

        $data = $this->productData();
        $data['id'] = $id;

        if (! $this->products->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->products->errors());
        }

        return redirect()->to('/admin/produk')->with('success', 'Produk berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->products->delete($id);

        return redirect()->to('/admin/produk')->with('success', 'Produk berhasil dihapus.');
    }

    private function productData(): array
    {
        return [
            'category_id' => (int) $this->request->getPost('category_id'),
            'name'        => $this->request->getPost('name'),
            'nominal'     => $this->request->getPost('nominal'),
            'sell_price'  => $this->request->getPost('sell_price'),
            'cost_price'  => $this->request->getPost('cost_price'),
            'sort_order'  => (int) $this->request->getPost('sort_order'),
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }
}
