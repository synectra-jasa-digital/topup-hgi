<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Metode Pembayaran</h1>
        <p class="section-subtitle">Atur rekening bank atau QRIS yang tampil di halaman checkout &amp; invoice.</p>
    </div>
    <a href="<?= base_url('admin/metode-bayar/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Metode
    </a>
</div>

<?php if (empty($channels)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">payments</span>
        </span>
        <h2 class="empty-state-title">Belum ada metode pembayaran</h2>
        <p class="empty-state-copy">Tambahkan rekening bank atau QRIS agar customer bisa membayar.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>Tipe</th>
                        <th>Nama</th>
                        <th>No. Rekening</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($channels as $channel): ?>
                        <tr class="table-row">
                            <td class="text-neutral-600"><?= $channel['type'] === 'qris' ? 'QRIS' : 'Bank' ?></td>
                            <td class="font-medium text-neutral-900"><?= esc($channel['name']) ?></td>
                            <td class="text-neutral-700"><?= esc($channel['account_number'] ?: '—') ?></td>
                            <td class="text-neutral-700"><?= esc($channel['sort_order']) ?></td>
                            <td><span class="badge <?= $channel['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $channel['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="<?= base_url('admin/metode-bayar/' . $channel['id'] . '/ubah') ?>" class="table-action">Ubah</a>
                                    <form method="post" action="<?= base_url('admin/metode-bayar/' . $channel['id'] . '/hapus') ?>" class="inline" data-confirm="Hapus metode pembayaran ini?" data-confirm-title="Hapus Metode" data-confirm-button="Ya, hapus">
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
