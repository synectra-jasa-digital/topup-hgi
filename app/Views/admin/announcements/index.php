<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Info Berjalan</h1>
        <p class="section-subtitle">Teks berjalan (ticker) yang tampil di bawah header halaman utama toko.</p>
    </div>
    <a href="<?= base_url('admin/info-berjalan/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Info
    </a>
</div>

<?php if (empty($announcements)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">campaign</span>
        </span>
        <h2 class="empty-state-title">Belum ada info berjalan</h2>
        <p class="empty-state-copy">Tambahkan pengumuman agar tampil di ticker halaman utama.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>Pesan</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $announcement): ?>
                        <tr class="table-row">
                            <td class="max-w-xl text-neutral-900"><?= esc($announcement['message']) ?></td>
                            <td class="text-neutral-700"><?= esc($announcement['sort_order']) ?></td>
                            <td><span class="badge <?= $announcement['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $announcement['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="<?= base_url('admin/info-berjalan/' . $announcement['id'] . '/ubah') ?>" class="table-action">Ubah</a>
                                    <form method="post" action="<?= base_url('admin/info-berjalan/' . $announcement['id'] . '/hapus') ?>" class="inline" data-confirm="Hapus info berjalan ini?" data-confirm-title="Hapus Info" data-confirm-button="Ya, hapus">
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
