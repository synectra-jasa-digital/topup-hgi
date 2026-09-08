<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Banner Promo</h1>
        <p class="section-subtitle">Atur banner yang tampil di halaman utama.</p>
    </div>
    <a href="<?= base_url('admin/banner/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Banner
    </a>
</div>

<?php if (empty($banners)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">view_carousel</span>
        </span>
        <h2 class="empty-state-title">Belum ada banner</h2>
        <p class="empty-state-copy">Tambahkan banner promosi untuk memperkuat tampilan katalog.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>Gambar</th>
                        <th>Kategori</th>
                        <th>Jadwal Tayang</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($banners as $banner): ?>
                        <tr class="table-row">
                            <td><img src="<?= base_url($banner['image_path']) ?>" alt="Banner" class="h-12 w-24 rounded-lg object-cover ring-1 ring-neutral-200"></td>
                            <td class="text-neutral-700"><?= esc($banner['category_name']) ?></td>
                            <td class="text-sm text-neutral-500"><?= esc($banner['start_date'] ?? '-') ?> s/d <?= esc($banner['end_date'] ?? '-') ?></td>
                            <td><span class="badge <?= $banner['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $banner['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="<?= base_url('admin/banner/' . $banner['id'] . '/ubah') ?>" class="table-action">Ubah</a>
                                    <form method="post" action="<?= base_url('admin/banner/' . $banner['id'] . '/hapus') ?>" class="inline" data-confirm="Hapus banner ini?" data-confirm-title="Hapus Banner" data-confirm-button="Ya, hapus">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="table-action-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
