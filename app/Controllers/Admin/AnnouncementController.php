<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class AnnouncementController extends BaseController
{
    protected AnnouncementModel $announcements;

    public function __construct()
    {
        $this->announcements = new AnnouncementModel();
    }

    public function index()
    {
        return view('admin/announcements/index', [
            'announcements' => $this->announcements->orderBy('sort_order')->findAll(),
        ]);
    }

    public function create()
    {
        return view('admin/announcements/form', ['announcement' => null]);
    }

    public function store()
    {
        if (! $this->announcements->save($this->announcementData())) {
            return redirect()->back()->withInput()->with('errors', $this->announcements->errors());
        }

        return redirect()->to('/admin/info-berjalan')->with('success', 'Info berjalan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $announcement = $this->announcements->find($id);
        if (! $announcement) {
            return redirect()->to('/admin/info-berjalan')->with('error', 'Info berjalan tidak ditemukan.');
        }

        return view('admin/announcements/form', ['announcement' => $announcement]);
    }

    public function update(int $id)
    {
        if (! $this->announcements->find($id)) {
            return redirect()->to('/admin/info-berjalan')->with('error', 'Info berjalan tidak ditemukan.');
        }

        $data       = $this->announcementData();
        $data['id'] = $id;

        if (! $this->announcements->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->announcements->errors());
        }

        return redirect()->to('/admin/info-berjalan')->with('success', 'Info berjalan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->announcements->delete($id);

        return redirect()->to('/admin/info-berjalan')->with('success', 'Info berjalan berhasil dihapus.');
    }

    private function announcementData(): array
    {
        return [
            'message'    => $this->request->getPost('message'),
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_active'  => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }
}
