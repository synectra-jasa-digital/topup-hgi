<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-neutral-900">Kategori Banner</h1>
    <a href="<?= base_url('admin/kategori-banner/tambah') ?>" class="btn btn-primary">Tambah Kategori</a>
</div>

<?php if (empty($categories)): ?>
    <div class="card p-8 text-center text-neutral-500">Belum ada kategori banner.</div>
<?php else: ?>
    <div class="card overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="border-b border-neutral-200 text-neutral-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                <?php foreach ($categories as $category): ?>
                    <tr class="even:bg-neutral-50">
                        <td class="px-4 py-3"><?= esc($category['name']) ?></td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="<?= base_url('admin/kategori-banner/' . $category['id'] . '/ubah') ?>" class="text-primary hover:underline">Ubah</a>
                            <form method="post" action="<?= base_url('admin/kategori-banner/' . $category['id'] . '/hapus') ?>" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
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
