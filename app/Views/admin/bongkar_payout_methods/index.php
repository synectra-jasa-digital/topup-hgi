<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Metode Pencairan Bongkar</h1>
        <p class="section-subtitle">Atur daftar rekening bank/e-wallet yang bisa dipilih customer saat mengajukan bongkar/jual.</p>
    </div>
    <a href="<?= base_url('admin/bongkar-metode-pencairan/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Metode
    </a>
</div>

<?php if (empty($methods)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">account_balance</span>
        </span>
        <h2 class="empty-state-title">Belum ada metode pencairan</h2>
        <p class="empty-state-copy">Tambahkan minimal satu metode agar customer bisa mengajukan bongkar/jual.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($methods as $method): ?>
                        <tr class="table-row">
                            <td class="font-mono text-xs text-neutral-700"><?= esc($method['code']) ?></td>
                            <td class="font-medium text-neutral-900"><?= esc($method['name']) ?></td>
                            <td class="text-neutral-600"><?= $method['category'] === 'ewallet' ? 'E-Wallet' : 'Bank' ?></td>
                            <td class="text-neutral-700"><?= esc($method['sort_order']) ?></td>
                            <td><span class="badge <?= $method['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $method['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="<?= base_url('admin/bongkar-metode-pencairan/' . $method['id'] . '/ubah') ?>" class="table-action">Ubah</a>
                                    <form method="post" action="<?= base_url('admin/bongkar-metode-pencairan/' . $method['id'] . '/hapus') ?>" class="inline" data-confirm="Hapus metode pencairan ini?" data-confirm-title="Hapus Metode" data-confirm-button="Ya, hapus">
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
