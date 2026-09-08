<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="text-xl font-semibold text-neutral-900">Kategori Banner</h1>
        <p class="mt-1 text-sm text-neutral-500">Kelola kelompok banner untuk rotasi promosi.</p>
    </div>
    <a href="<?= base_url('admin/kategori-banner/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Kategori
    </a>
</div>

<?php if (empty($categories)): ?>
    <div class="panel-surface flex flex-col items-center px-6 py-16 text-center">
        <span class="material-symbols-outlined text-[36px] text-neutral-300">bookmarks</span>
        <h2 class="mt-3 text-base font-semibold text-neutral-900">Belum ada kategori banner</h2>
        <p class="mt-1 text-sm text-neutral-500">Buat kategori baru untuk mulai menambahkan banner.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="border-b border-neutral-100 px-4 py-3">Nama</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr class="table-row">
                            <td class="px-4 py-3 font-medium text-neutral-900"><?= esc($category['name']) ?></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="<?= base_url('admin/kategori-banner/' . $category['id'] . '/ubah') ?>" class="table-action">Ubah</a>
                                    <form method="post" action="<?= base_url('admin/kategori-banner/' . $category['id'] . '/hapus') ?>" class="inline" data-confirm="Hapus kategori ini?" data-confirm-title="Hapus Kategori Banner" data-confirm-button="Ya, hapus">
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