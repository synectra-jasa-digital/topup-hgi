<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $statuses = $statuses ?? []; $pager = $pager ?? null; ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Daftar Pesanan</h1>
        <p class="section-subtitle">Pantau transaksi masuk dan status penyelesaiannya.</p>
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
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
        </span>
        <h2 class="empty-state-title">Belum ada pesanan<?= $status ? ' dengan status ' . esc(order_status_label($status)) : '' ?></h2>
        <p class="empty-state-copy">Pesanan baru akan muncul di sini.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>Invoice</th>
                        <th>Produk</th>
                        <th>ID Game</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr class="table-row">
                            <td class="font-mono text-xs text-neutral-700"><?= esc($order['invoice_number']) ?></td>
                            <td class="text-neutral-900"><?= esc($order['product_name_snapshot']) ?> <span class="text-neutral-400">(<?= esc($order['nominal_snapshot']) ?>)</span></td>
                            <td class="font-mono text-neutral-700"><?= esc($order['game_id']) ?></td>
                            <td class="font-medium text-neutral-900">Rp<?= number_format((float)$order['total_amount'], 0, ',', '.') ?></td>
                            <td><span class="badge <?= order_status_badge_class($order['status']) ?>"><?= esc(order_status_label($order['status'])) ?></span></td>
                            <td class="text-xs text-neutral-500"><?= esc(date('d/m/Y H:i', strtotime($order['created_at']))) ?></td>
                            <td><a href="<?= base_url('admin/pesanan/' . $order['id']) ?>" class="table-action">Detail</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php if ($pager && $pager->getPageCount() > 1): ?>
        <div class="mt-4"><?= $pager->links('default', 'default_full') ?></div>
    <?php endif; ?>
<?php endif; ?>
<?= $this->endSection() ?>
