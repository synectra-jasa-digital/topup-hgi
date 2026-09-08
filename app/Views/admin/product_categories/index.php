<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Kategori Produk</h1>
        <p class="section-subtitle">Atur struktur kategori untuk katalog produk publik.</p>
    </div>
    <a href="<?= base_url('admin/kategori-produk/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Kategori
    </a>
</div>

<?php if (empty($categories)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">category</span>
        </span>
        <h2 class="empty-state-title">Belum ada kategori</h2>
        <p class="empty-state-copy">Buat kategori agar produk lebih mudah dikelola.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>Nama</th>
                        <th>Slug</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr class="table-row">
                            <td class="font-medium text-neutral-900"><?= esc($category['name']) ?></td>
                            <td class="font-mono text-neutral-500"><?= esc($category['slug']) ?></td>
                            <td class="text-neutral-700"><?= esc($category['sort_order']) ?></td>
                            <td><span class="badge <?= $category['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $category['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td>
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
