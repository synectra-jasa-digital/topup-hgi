<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-neutral-900">Halaman Statis</h1>
    <a href="<?= base_url('admin/halaman-statis/tambah') ?>" class="btn btn-primary">Tambah Halaman</a>
</div>

<?php if (empty($pages)): ?>
    <div class="card p-8 text-center text-neutral-500">Belum ada halaman statis.</div>
<?php else: ?>
    <div class="card overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="border-b border-neutral-200 text-neutral-500">
                <tr>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                <?php foreach ($pages as $page): ?>
                    <tr class="even:bg-neutral-50">
                        <td class="px-4 py-3 font-semibold"><?= esc($page['title']) ?></td>
                        <td class="px-4 py-3 font-mono text-neutral-500"><?= esc($page['slug']) ?></td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="<?= base_url('admin/halaman-statis/' . $page['id'] . '/ubah') ?>" class="text-primary hover:underline">Ubah</a>
                            <form method="post" action="<?= base_url('admin/halaman-statis/' . $page['id'] . '/hapus') ?>" class="inline" onsubmit="return confirm('Hapus halaman ini?')">
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
