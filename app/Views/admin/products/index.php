<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="text-xl font-semibold text-neutral-900">Produk Top Up</h1>
        <p class="mt-1 text-sm text-neutral-500">Kelola daftar produk top up yang tampil di katalog publik.</p>
    </div>
    <a href="<?= base_url('admin/produk/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Produk
    </a>
</div>

<?php if (empty($products)): ?>
    <div class="panel-surface flex flex-col items-center px-6 py-16 text-center">
        <span class="material-symbols-outlined text-[36px] text-neutral-300">inventory_2</span>
        <h2 class="mt-3 text-base font-semibold text-neutral-900">Belum ada produk</h2>
        <p class="mt-1 text-sm text-neutral-500">Tambahkan produk pertama untuk mulai menjual.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="border-b border-neutral-100 px-4 py-3">Kategori</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Nama</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Nominal</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Harga Jual</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Harga Modal</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Status</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr class="table-row">
                            <td class="px-4 py-3 text-neutral-700"><?= esc($product['category_name']) ?></td>
                            <td class="px-4 py-3 font-medium text-neutral-900"><?= esc($product['name']) ?></td>
                            <td class="px-4 py-3 text-neutral-600"><?= esc($product['nominal']) ?></td>
                            <td class="px-4 py-3 font-medium text-neutral-900">Rp<?= number_format((float) $product['sell_price'], 0, ',', '.') ?></td>
                            <td class="px-4 py-3 text-neutral-600">Rp<?= number_format((float) $product['cost_price'], 0, ',', '.') ?></td>
                            <td class="px-4 py-3"><span class="<?= $product['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $product['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td class="px-4 py-3">
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