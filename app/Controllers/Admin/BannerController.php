<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BannerCategoryModel;
use App\Models\BannerModel;

class BannerController extends BaseController
{
    private const UPLOAD_PATH = FCPATH . 'assets/uploads/banners/';

    protected BannerModel $banners;
    protected BannerCategoryModel $categories;

    public function __construct()
    {
        $this->banners    = new BannerModel();
        $this->categories = new BannerCategoryModel();
    }

    public function index()
    {
        return view('admin/banners/index', [
            'banners' => $this->banners->listWithCategory(),
        ]);
    }

    public function create()
    {
        return view('admin/banners/form', [
            'banner'     => null,
            'categories' => $this->categories->orderBy('id')->findAll(),
        ]);
    }

    public function store()
    {
        $rules = $this->banners->getValidationRules();
        $rules['image'] = 'uploaded[image]|max_size[image,2048]|is_image[image]';

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data              = $this->bannerData();
        $data['image_path'] = $this->storeImage();

        if (! $this->banners->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->banners->errors());
        }

        return redirect()->to('/admin/banner')->with('success', 'Banner berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $banner = $this->banners->find($id);

        if (! $banner) {
            return redirect()->to('/admin/banner')->with('error', 'Banner tidak ditemukan.');
        }

        return view('admin/banners/form', [
            'banner'     => $banner,
            'categories' => $this->categories->orderBy('id')->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $banner = $this->banners->find($id);

        if (! $banner) {
            return redirect()->to('/admin/banner')->with('error', 'Banner tidak ditemukan.');
        }

        $image = $this->request->getFile('image');
        $rules = $this->banners->getValidationRules();
        if ($image && $image->isValid()) {
            $rules['image'] = 'max_size[image,2048]|is_image[image]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data     = $this->bannerData();
        $data['id'] = $id;

        if ($image && $image->isValid()) {
            $data['image_path'] = $this->storeImage();
        }

        if (! $this->banners->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->banners->errors());
        }

        return redirect()->to('/admin/banner')->with('success', 'Banner berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->banners->delete($id);

        return redirect()->to('/admin/banner')->with('success', 'Banner berhasil dihapus.');
    }

    private function bannerData(): array
    {
        return [
            'banner_category_id' => (int) $this->request->getPost('banner_category_id'),
            'link_url'           => $this->request->getPost('link_url') ?: null,
            'start_date'         => $this->request->getPost('start_date') ?: null,
            'end_date'           => $this->request->getPost('end_date') ?: null,
            'sort_order'         => (int) $this->request->getPost('sort_order'),
            'is_active'          => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    private function storeImage(): string
    {
        if (! is_dir(self::UPLOAD_PATH)) {
            mkdir(self::UPLOAD_PATH, 0755, true);
        }

        $image    = $this->request->getFile('image');
        $filename = $image->getRandomName();
        $image->move(self::UPLOAD_PATH, $filename);

        return 'assets/uploads/banners/' . $filename;
    }
}
