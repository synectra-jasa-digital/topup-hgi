<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StaticPageModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class StaticPageController extends BaseController
{
    protected StaticPageModel $pages;

    public function __construct()
    {
        $this->pages = new StaticPageModel();
    }

    public function index()
    {
        return view('admin/static_pages/index', [
            'pages' => $this->pages->orderBy('title', 'ASC')->findAll(),
        ]);
    }

    public function create()
    {
        return view('admin/static_pages/form', ['page' => null]);
    }

    public function store()
    {
        helper('text');
        $data = [
            'slug'    => url_title($this->request->getPost('title'), '-', true),
            'title'   => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
        ];

        if (! $this->pages->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->pages->errors());
        }

        return redirect()->to('/admin/halaman-statis')->with('success', 'Halaman berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $page = $this->pages->find($id);
        if (! $page) {
            return redirect()->to('/admin/halaman-statis')->with('error', 'Halaman tidak ditemukan.');
        }

        return view('admin/static_pages/form', ['page' => $page]);
    }

    public function update(int $id)
    {
        helper('text');
        $page = $this->pages->find($id);
        if (! $page) {
            return redirect()->to('/admin/halaman-statis')->with('error', 'Halaman tidak ditemukan.');
        }

        $data = [
            'id'      => $id,
            'slug'    => url_title($this->request->getPost('title'), '-', true),
            'title'   => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
        ];

        if (! $this->pages->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->pages->errors());
        }

        return redirect()->to('/admin/halaman-statis')->with('success', 'Halaman berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->pages->delete($id);
        return redirect()->to('/admin/halaman-statis')->with('success', 'Halaman berhasil dihapus.');
    }
}
