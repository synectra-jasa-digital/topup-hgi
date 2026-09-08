<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Halaman Statis</h1>
        <p class="section-subtitle">Atur konten bantuan, syarat, dan informasi toko.</p>
    </div>
    <a href="<?= base_url('admin/halaman-statis/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Halaman
    </a>
</div>

<?php if (empty($pages)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">description</span>
        </span>
        <h2 class="empty-state-title">Belum ada halaman statis</h2>
        <p class="empty-state-copy">Tambahkan halaman informasi untuk kebutuhan pelanggan.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>Judul</th>
                        <th>Slug</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page): ?>
                        <tr class="table-row">
                            <td class="font-medium text-neutral-900"><?= esc($page['title']) ?></td>
                            <td class="font-mono text-neutral-500"><?= esc($page['slug']) ?></td>
                            <td>
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
