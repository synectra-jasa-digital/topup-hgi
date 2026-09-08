<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="text-xl font-semibold text-neutral-900">Voucher Diskon</h1>
        <p class="mt-1 text-sm text-neutral-500">Buat promo untuk meningkatkan transaksi checkout.</p>
    </div>
    <a href="<?= base_url('admin/voucher/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Voucher
    </a>
</div>

<?php if (empty($vouchers)): ?>
    <div class="panel-surface flex flex-col items-center px-6 py-16 text-center">
        <span class="material-symbols-outlined text-[36px] text-neutral-300">confirmation_number</span>
        <h2 class="mt-3 text-base font-semibold text-neutral-900">Belum ada voucher</h2>
        <p class="mt-1 text-sm text-neutral-500">Tambahkan voucher agar pelanggan mendapat potongan harga.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="border-b border-neutral-100 px-4 py-3">Kode</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Tipe</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Nilai</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Min. Beli</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Kuota</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Berlaku</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Status</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vouchers as $voucher): ?>
                        <tr class="table-row">
                            <td class="px-4 py-3 font-mono font-medium text-neutral-900"><?= esc($voucher['code']) ?></td>
                            <td class="px-4 py-3 capitalize text-neutral-700"><?= esc($voucher['type']) ?></td>
                            <td class="px-4 py-3 text-neutral-700">
                                <?php if ($voucher['type'] === 'percentage'): ?>
                                    <?= esc($voucher['value']) ?>%
                                <?php else: ?>
                                    Rp<?= number_format((float) $voucher['value'], 0, ',', '.') ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-neutral-700">Rp<?= number_format((float) $voucher['min_purchase'], 0, ',', '.') ?></td>
                            <td class="px-4 py-3 text-center text-neutral-700"><?= (int) $voucher['quota'] > 0 ? ((int) $voucher['used_count'] . '/' . (int) $voucher['quota']) : '-' ?></td>
                            <td class="px-4 py-3 text-sm text-neutral-500"><?= esc($voucher['start_date'] ?? '-') ?> s/d <?= esc($voucher['end_date'] ?? '-') ?></td>
                            <td class="px-4 py-3"><span class="<?= $voucher['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $voucher['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td class="px-4 py-3">
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
    <div class="mt-4"><?= $pager->links('default', 'default_full') ?></div>
<?php endif; ?>
<?= $this->endSection() ?>