<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="text-xl font-semibold text-neutral-900">Banner Promo</h1>
        <p class="mt-1 text-sm text-neutral-500">Atur banner yang tampil di halaman utama.</p>
    </div>
    <a href="<?= base_url('admin/banner/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Banner
    </a>
</div>

<?php if (empty($banners)): ?>
    <div class="panel-surface flex flex-col items-center px-6 py-16 text-center">
        <span class="material-symbols-outlined text-[36px] text-neutral-300">view_carousel</span>
        <h2 class="mt-3 text-base font-semibold text-neutral-900">Belum ada banner</h2>
        <p class="mt-1 text-sm text-neutral-500">Tambahkan banner promosi untuk memperkuat tampilan katalog.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="border-b border-neutral-100 px-4 py-3">Gambar</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Kategori</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Jadwal Tayang</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Status</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($banners as $banner): ?>
                        <tr class="table-row">
                            <td class="px-4 py-3"><img src="<?= base_url($banner['image_path']) ?>" alt="Banner" class="h-12 w-24 rounded-md object-cover ring-1 ring-neutral-200"></td>
                            <td class="px-4 py-3 text-neutral-700"><?= esc($banner['category_name']) ?></td>
                            <td class="px-4 py-3 text-sm text-neutral-500"><?= esc($banner['start_date'] ?? '-') ?> s/d <?= esc($banner['end_date'] ?? '-') ?></td>
                            <td class="px-4 py-3"><span class="<?= $banner['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $banner['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td class="px-4 py-3">
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