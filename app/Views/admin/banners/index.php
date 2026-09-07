<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-neutral-900">Banner</h1>
    <a href="<?= base_url('admin/banner/tambah') ?>" class="btn btn-primary">Tambah Banner</a>
</div>

<?php if (empty($banners)): ?>
    <div class="card p-8 text-center text-neutral-500">Belum ada banner. Tambahkan kategori banner terlebih dahulu jika belum ada.</div>
<?php else: ?>
    <div class="card overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="border-b border-neutral-200 text-neutral-500">
                <tr>
                    <th class="px-4 py-3">Gambar</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Jadwal Tayang</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                <?php foreach ($banners as $banner): ?>
                    <tr class="even:bg-neutral-50">
                        <td class="px-4 py-3"><img src="<?= base_url($banner['image_path']) ?>" alt="Banner" class="h-10 rounded-md"></td>
                        <td class="px-4 py-3"><?= esc($banner['category_name']) ?></td>
                        <td class="px-4 py-3 text-neutral-500"><?= esc($banner['start_date'] ?? '-') ?> s/d <?= esc($banner['end_date'] ?? '-') ?></td>
                        <td class="px-4 py-3">
                            <span class="badge <?= $banner['is_active'] ? 'badge-success' : 'bg-neutral-200 text-neutral-500' ?>">
                                <?= $banner['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="<?= base_url('admin/banner/' . $banner['id'] . '/ubah') ?>" class="text-primary hover:underline">Ubah</a>
                            <form method="post" action="<?= base_url('admin/banner/' . $banner['id'] . '/hapus') ?>" class="inline" onsubmit="return confirm('Hapus banner ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-danger hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
