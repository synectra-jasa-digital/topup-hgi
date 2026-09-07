<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-neutral-900">Daftar Pesanan</h1>
</div>

<?php helper('order'); ?>

<!-- Filter status -->
<div class="flex flex-wrap gap-2 mb-5">
    <a href="<?= base_url('admin/pesanan') ?>"
       class="px-3 py-1.5 rounded-lg text-sm font-semibold border <?= $status === null ? 'bg-primary text-white border-primary' : 'border-neutral-300 text-neutral-600 hover:bg-neutral-100' ?>">
        Semua
    </a>
    <?php foreach ($statuses as $s): ?>
        <a href="<?= base_url('admin/pesanan?status=' . $s) ?>"
           class="px-3 py-1.5 rounded-lg text-sm font-semibold border <?= $status === $s ? 'bg-primary text-white border-primary' : 'border-neutral-300 text-neutral-600 hover:bg-neutral-100' ?>">
            <?= esc(order_status_label($s)) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (empty($orders)): ?>
    <div class="card p-8 text-center text-neutral-500">Belum ada pesanan<?= $status ? ' dengan status ' . esc(order_status_label($status)) : '' ?>.</div>
<?php else: ?>
    <div class="card overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="border-b border-neutral-200 text-neutral-500">
                <tr>
                    <th class="px-4 py-3">Invoice</th>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3">ID Game</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                <?php foreach ($orders as $order): ?>
                    <tr class="even:bg-neutral-50">
                        <td class="px-4 py-3 font-mono text-xs"><?= esc($order['invoice_number']) ?></td>
                        <td class="px-4 py-3"><?= esc($order['product_name_snapshot']) ?> <span class="text-neutral-400">(<?= esc($order['nominal_snapshot']) ?>)</span></td>
                        <td class="px-4 py-3 font-mono"><?= esc($order['game_id']) ?></td>
                        <td class="px-4 py-3 font-semibold">Rp<?= number_format((float)$order['total_amount'], 0, ',', '.') ?></td>
                        <td class="px-4 py-3">
                            <span class="badge <?= order_status_badge_class($order['status']) ?>"><?= esc(order_status_label($order['status'])) ?></span>
                        </td>
                        <td class="px-4 py-3 text-neutral-500 text-xs"><?= esc(date('d/m/Y H:i', strtotime($order['created_at']))) ?></td>
                        <td class="px-4 py-3">
                            <a href="<?= base_url('admin/pesanan/' . $order['id']) ?>" class="text-primary hover:underline">Detail</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4"><?= $pager->links('default', 'default_full') ?></div>
<?php endif; ?>
<?= $this->endSection() ?>
