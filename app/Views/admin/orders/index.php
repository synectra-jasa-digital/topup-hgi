<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php helper('order'); ?>

<div class="flex items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-semibold text-neutral-900">Daftar Pesanan</h1>
        <p class="mt-1 text-sm text-neutral-500">Pantau transaksi masuk dan status penyelesaiannya.</p>
    </div>
</div>

<div class="flex flex-wrap gap-2">
    <a href="<?= base_url('admin/pesanan') ?>" class="inline-flex h-9 items-center rounded-lg border px-3.5 text-sm font-medium transition <?= $status === null ? 'border-primary bg-primary text-white' : 'border-neutral-200 bg-white text-neutral-600 hover:border-neutral-300 hover:text-neutral-900' ?>">Semua</a>
    <?php foreach ($statuses as $s): ?>
        <a href="<?= base_url('admin/pesanan?status=' . $s) ?>" class="inline-flex h-9 items-center rounded-lg border px-3.5 text-sm font-medium transition <?= $status === $s ? 'border-primary bg-primary text-white' : 'border-neutral-200 bg-white text-neutral-600 hover:border-neutral-300 hover:text-neutral-900' ?>">
            <?= esc(order_status_label($s)) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (empty($orders)): ?>
    <div class="panel-surface flex flex-col items-center px-6 py-16 text-center">
        <span class="material-symbols-outlined text-[36px] text-neutral-300">shopping_cart</span>
        <h2 class="mt-3 text-base font-semibold text-neutral-900">Belum ada pesanan<?= $status ? ' dengan status ' . esc(order_status_label($status)) : '' ?></h2>
        <p class="mt-1 text-sm text-neutral-500">Pesanan baru akan muncul di sini.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="border-b border-neutral-100 px-4 py-3">Invoice</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Produk</th>
                        <th class="border-b border-neutral-100 px-4 py-3">ID Game</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Total</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Status</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Tanggal</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr class="table-row">
                            <td class="px-4 py-3 font-mono text-xs text-neutral-700"><?= esc($order['invoice_number']) ?></td>
                            <td class="px-4 py-3 text-neutral-900"><?= esc($order['product_name_snapshot']) ?> <span class="text-neutral-400">(<?= esc($order['nominal_snapshot']) ?>)</span></td>
                            <td class="px-4 py-3 font-mono text-neutral-700"><?= esc($order['game_id']) ?></td>
                            <td class="px-4 py-3 font-medium text-neutral-900">Rp<?= number_format((float)$order['total_amount'], 0, ',', '.') ?></td>
                            <td class="px-4 py-3">
                                <span class="badge <?= order_status_badge_class($order['status']) ?>"><?= esc(order_status_label($order['status'])) ?></span>
                            </td>
                            <td class="px-4 py-3 text-xs text-neutral-500"><?= esc(date('d/m/Y H:i', strtotime($order['created_at']))) ?></td>
                            <td class="px-4 py-3">
                                <a href="<?= base_url('admin/pesanan/' . $order['id']) ?>" class="table-action">Detail</a>
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