<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-neutral-900">Voucher Diskon</h1>
    <a href="<?= base_url('admin/voucher/tambah') ?>" class="btn btn-primary">Tambah Voucher</a>
</div>

<?php if (empty($vouchers)): ?>
    <div class="card p-8 text-center text-neutral-500">Belum ada voucher.</div>
<?php else: ?>
    <div class="card overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="border-b border-neutral-200 text-neutral-500">
                <tr>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3">Nilai</th>
                    <th class="px-4 py-3">Min. Beli</th>
                    <th class="px-4 py-3">Kuota</th>
                    <th class="px-4 py-3">Berlaku</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                <?php foreach ($vouchers as $voucher): ?>
                    <tr class="even:bg-neutral-50">
                        <td class="px-4 py-3 font-mono font-semibold"><?= esc($voucher['code']) ?></td>
                        <td class="px-4 py-3 capitalize"><?= esc($voucher['type']) ?></td>
                        <td class="px-4 py-3">
                            <?php if ($voucher['type'] === 'percentage'): ?>
                                <?= esc($voucher['value']) ?>%
                            <?php else: ?>
                                Rp<?= number_format((float) $voucher['value'], 0, ',', '.') ?>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">Rp<?= number_format((float) $voucher['min_purchase'], 0, ',', '.') ?></td>
                        <td class="px-4 py-3 text-center"><?= (int) $voucher['quota'] > 0 ? ((int) $voucher['used_count'] . '/' . (int) $voucher['quota']) : '-' ?></td>
                        <td class="px-4 py-3 text-neutral-500 text-xs"><?= esc($voucher['start_date'] ?? '-') ?> s/d <?= esc($voucher['end_date'] ?? '-') ?></td>
                        <td class="px-4 py-3">
                            <span class="badge <?= $voucher['is_active'] ? 'badge-success' : 'bg-neutral-200 text-neutral-500' ?>">
                                <?= $voucher['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="<?= base_url('admin/voucher/' . $voucher['id'] . '/ubah') ?>" class="text-primary hover:underline">Ubah</a>
                            <form method="post" action="<?= base_url('admin/voucher/' . $voucher['id'] . '/hapus') ?>" class="inline" onsubmit="return confirm('Hapus voucher ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-danger hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4"><?= $pager->links('default', 'default_full') ?></div>
<?php endif; ?>
<?= $this->endSection() ?>
