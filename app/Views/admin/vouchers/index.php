<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $pager = $pager ?? null; ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Voucher Diskon</h1>
        <p class="section-subtitle">Buat promo untuk meningkatkan transaksi checkout.</p>
    </div>
    <a href="<?= base_url('admin/voucher/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Voucher
    </a>
</div>

<?php if (empty($vouchers)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
        </span>
        <h2 class="empty-state-title">Belum ada voucher</h2>
        <p class="empty-state-copy">Tambahkan voucher agar pelanggan mendapat potongan harga.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>Kode</th>
                        <th>Tipe</th>
                        <th>Nilai</th>
                        <th>Min. Beli</th>
                        <th>Kuota</th>
                        <th>Berlaku</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vouchers as $voucher): ?>
                        <tr class="table-row">
                            <td class="font-mono font-medium text-neutral-900"><?= esc($voucher['code']) ?></td>
                            <td class="capitalize text-neutral-700"><?= esc($voucher['type']) ?></td>
                            <td class="text-neutral-700">
                                <?php if ($voucher['type'] === 'percentage'): ?>
                                    <?= esc($voucher['value']) ?>%
                                <?php else: ?>
                                    Rp<?= number_format((float) $voucher['value'], 0, ',', '.') ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-neutral-700">Rp<?= number_format((float) $voucher['min_purchase'], 0, ',', '.') ?></td>
                            <td class="text-center text-neutral-700"><?= (int) $voucher['quota'] > 0 ? ((int) $voucher['used_count'] . '/' . (int) $voucher['quota']) : '-' ?></td>
                            <td class="text-sm text-neutral-500"><?= esc($voucher['start_date'] ?? '-') ?> s/d <?= esc($voucher['end_date'] ?? '-') ?></td>
                            <td><span class="badge <?= $voucher['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $voucher['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="<?= base_url('admin/voucher/' . $voucher['id'] . '/ubah') ?>" class="table-action">Ubah</a>
                                    <form method="post" action="<?= base_url('admin/voucher/' . $voucher['id'] . '/hapus') ?>" class="inline" data-confirm="Hapus voucher ini?" data-confirm-title="Hapus Voucher" data-confirm-button="Ya, hapus">
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
    <div class="mt-4"><?= $pager ? $pager->links('default', 'default_full') : '' ?></div>
<?php endif; ?>
<?= $this->endSection() ?>
