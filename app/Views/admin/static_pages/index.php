<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="text-xl font-semibold text-neutral-900">Halaman Statis</h1>
        <p class="mt-1 text-sm text-neutral-500">Atur konten bantuan, syarat, dan informasi toko.</p>
    </div>
    <a href="<?= base_url('admin/halaman-statis/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Halaman
    </a>
</div>

<?php if (empty($pages)): ?>
    <div class="panel-surface flex flex-col items-center px-6 py-16 text-center">
        <span class="material-symbols-outlined text-[36px] text-neutral-300">description</span>
        <h2 class="mt-3 text-base font-semibold text-neutral-900">Belum ada halaman statis</h2>
        <p class="mt-1 text-sm text-neutral-500">Tambahkan halaman informasi untuk kebutuhan pelanggan.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="border-b border-neutral-100 px-4 py-3">Judul</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Slug</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page): ?>
                        <tr class="table-row">
                            <td class="px-4 py-3 font-medium text-neutral-900"><?= esc($page['title']) ?></td>
                            <td class="px-4 py-3 font-mono text-neutral-500"><?= esc($page['slug']) ?></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="<?= base_url('admin/halaman-statis/' . $page['id'] . '/ubah') ?>" class="table-action">Ubah</a>
                                    <form method="post" action="<?= base_url('admin/halaman-statis/' . $page['id'] . '/hapus') ?>" class="inline" data-confirm="Hapus halaman ini?" data-confirm-title="Hapus Halaman" data-confirm-button="Ya, hapus">
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