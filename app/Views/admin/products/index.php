<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Produk Top Up</h1>
        <p class="section-subtitle">Kelola daftar produk yang tampil di katalog publik.</p>
    </div>
    <a href="<?= base_url('admin/produk/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Produk
    </a>
</div>

<?php if (empty($products)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">inventory_2</span>
        </span>
        <h2 class="empty-state-title">Belum ada produk</h2>
        <p class="empty-state-copy">Tambahkan produk pertama untuk mulai menjual.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>Kategori</th>
                        <th>Nama</th>
                        <th>Nominal</th>
                        <th>Harga Jual</th>
                        <th>Harga Modal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr class="table-row">
                            <td class="text-neutral-700"><?= esc($product['category_name']) ?></td>
                            <td class="font-medium text-neutral-900"><?= esc($product['name']) ?></td>
                            <td class="text-neutral-600"><?= esc($product['nominal']) ?></td>
                            <td class="font-medium text-neutral-900">Rp<?= number_format((float) $product['sell_price'], 0, ',', '.') ?></td>
                            <td class="text-neutral-600">Rp<?= number_format((float) $product['cost_price'], 0, ',', '.') ?></td>
                            <td><span class="badge <?= $product['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $product['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="<?= base_url('admin/produk/' . $product['id'] . '/ubah') ?>" class="table-action">Ubah</a>
                                    <form method="post" action="<?= base_url('admin/produk/' . $product['id'] . '/hapus') ?>" class="inline" data-confirm="Hapus produk ini?" data-confirm-title="Hapus Produk" data-confirm-button="Ya, hapus">
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
