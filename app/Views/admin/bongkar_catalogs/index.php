<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Katalog Bongkar</h1>
        <p class="section-subtitle">Atur daftar item dan rate beli-kembali (bongkar) kartu/koin dari customer.</p>
    </div>
    <a href="<?= base_url('admin/bongkar-katalog/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Katalog
    </a>
</div>

<?php if (empty($catalogs)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">inventory</span>
        </span>
        <h2 class="empty-state-title">Belum ada katalog bongkar</h2>
        <p class="empty-state-copy">Tambahkan item dan rate agar customer bisa mengajukan bongkar/jual.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Satuan</th>
                        <th>Rate</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($catalogs as $catalog): ?>
                        <tr class="table-row">
                            <td class="font-mono text-xs text-neutral-700"><?= esc($catalog['code']) ?></td>
                            <td class="font-medium text-neutral-900"><?= esc($catalog['name']) ?></td>
                            <td class="text-neutral-600"><?= esc($catalog['unit_label']) ?></td>
                            <td class="font-medium text-neutral-900">Rp<?= number_format((float) $catalog['base_rate'], 0, ',', '.') ?></td>
                            <td class="text-neutral-700"><?= esc($catalog['sort_order']) ?></td>
                            <td><span class="badge <?= $catalog['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $catalog['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="<?= base_url('admin/bongkar-katalog/' . $catalog['id'] . '/ubah') ?>" class="table-action">Ubah</a>
                                    <form method="post" action="<?= base_url('admin/bongkar-katalog/' . $catalog['id'] . '/hapus') ?>" class="inline" data-confirm="Hapus katalog bongkar ini?" data-confirm-title="Hapus Katalog" data-confirm-button="Ya, hapus">
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
