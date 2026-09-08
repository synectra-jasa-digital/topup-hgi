<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="text-xl font-semibold text-neutral-900">Kategori Produk</h1>
        <p class="mt-1 text-sm text-neutral-500">Atur struktur kategori untuk katalog produk publik.</p>
    </div>
    <a href="<?= base_url('admin/kategori-produk/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Kategori
    </a>
</div>

<?php if (empty($categories)): ?>
    <div class="panel-surface flex flex-col items-center px-6 py-16 text-center">
        <span class="material-symbols-outlined text-[36px] text-neutral-300">category</span>
        <h2 class="mt-3 text-base font-semibold text-neutral-900">Belum ada kategori</h2>
        <p class="mt-1 text-sm text-neutral-500">Buat kategori agar produk lebih mudah dikelola.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="border-b border-neutral-100 px-4 py-3">Nama</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Slug</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Urutan</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Status</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr class="table-row">
                            <td class="px-4 py-3 font-medium text-neutral-900"><?= esc($category['name']) ?></td>
                            <td class="px-4 py-3 font-mono text-neutral-500"><?= esc($category['slug']) ?></td>
                            <td class="px-4 py-3 text-neutral-700"><?= esc($category['sort_order']) ?></td>
                            <td class="px-4 py-3"><span class="<?= $category['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $category['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="<?= base_url('admin/kategori-produk/' . $category['id'] . '/ubah') ?>" class="table-action">Ubah</a>
                                    <form method="post" action="<?= base_url('admin/kategori-produk/' . $category['id'] . '/hapus') ?>" class="inline" data-confirm="Hapus kategori ini?" data-confirm-title="Hapus Kategori" data-confirm-button="Ya, hapus">
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