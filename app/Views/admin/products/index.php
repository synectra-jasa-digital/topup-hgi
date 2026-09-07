<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-neutral-900">Produk</h1>
    <a href="<?= base_url('admin/produk/tambah') ?>" class="btn btn-primary">Tambah Produk</a>
</div>

<?php if (empty($products)): ?>
    <div class="card p-8 text-center text-neutral-500">Belum ada produk. Tambahkan kategori produk terlebih dahulu jika belum ada.</div>
<?php else: ?>
    <div class="card overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="border-b border-neutral-200 text-neutral-500">
                <tr>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Nominal</th>
                    <th class="px-4 py-3">Harga Jual</th>
                    <th class="px-4 py-3">Harga Modal</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                <?php foreach ($products as $product): ?>
                    <tr class="even:bg-neutral-50">
                        <td class="px-4 py-3"><?= esc($product['category_name']) ?></td>
                        <td class="px-4 py-3"><?= esc($product['name']) ?></td>
                        <td class="px-4 py-3"><?= esc($product['nominal']) ?></td>
                        <td class="px-4 py-3">Rp<?= number_format((float) $product['sell_price'], 0, ',', '.') ?></td>
                        <td class="px-4 py-3">Rp<?= number_format((float) $product['cost_price'], 0, ',', '.') ?></td>
                        <td class="px-4 py-3">
                            <span class="badge <?= $product['is_active'] ? 'badge-success' : 'bg-neutral-200 text-neutral-500' ?>">
                                <?= $product['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="<?= base_url('admin/produk/' . $product['id'] . '/ubah') ?>" class="text-primary hover:underline">Ubah</a>
                            <form method="post" action="<?= base_url('admin/produk/' . $product['id'] . '/hapus') ?>" class="inline" onsubmit="return confirm('Hapus produk ini?')">
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
