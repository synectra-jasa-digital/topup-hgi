<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $statuses = $statuses ?? []; $pager = $pager ?? null; ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Pesanan Bongkar</h1>
        <p class="section-subtitle">Pengajuan jual/bongkar kartu dan koin dari customer.</p>
    </div>
</div>

<div class="flex flex-wrap gap-2">
    <a href="<?= base_url('admin/bongkar-pesanan') ?>" class="inline-flex h-9 items-center rounded-lg border px-3.5 text-sm font-medium transition <?= $status === null ? 'border-primary bg-primary text-white' : 'border-neutral-200 bg-white text-neutral-600 hover:border-neutral-300 hover:text-neutral-900' ?>">Semua</a>
    <?php foreach ($statuses as $s): ?>
        <a href="<?= base_url('admin/bongkar-pesanan?status=' . $s) ?>" class="inline-flex h-9 items-center rounded-lg border px-3.5 text-sm font-medium transition <?= $status === $s ? 'border-primary bg-primary text-white' : 'border-neutral-200 bg-white text-neutral-600 hover:border-neutral-300 hover:text-neutral-900' ?>">
            <?= esc(bongkar_status_label($s)) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (empty($requests)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">currency_exchange</span>
        </span>
        <h2 class="empty-state-title">Belum ada pengajuan<?= $status ? ' dengan status ' . esc(bongkar_status_label($status)) : '' ?></h2>
        <p class="empty-state-copy">Pengajuan bongkar baru dari customer akan muncul di sini.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>No. Pengajuan</th>
                        <th>Item</th>
                        <th>Jumlah</th>
                        <th>Estimasi Payout</th>
                        <th>WhatsApp</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                        <tr class="table-row">
                            <td class="font-mono text-xs text-neutral-700"><?= esc($req['request_number']) ?></td>
                            <td class="text-neutral-900"><?= esc($req['catalog_name_snapshot']) ?></td>
                            <td class="text-neutral-700"><?= esc($req['quantity']) ?> <?= esc($req['unit_label_snapshot']) ?></td>
                            <td class="font-medium text-neutral-900">Rp<?= number_format((float) $req['estimated_amount'], 0, ',', '.') ?></td>
                            <td class="font-mono text-neutral-700"><?= esc($req['customer_whatsapp']) ?></td>
                            <td><span class="badge <?= bongkar_status_badge_class($req['status']) ?>"><?= esc(bongkar_status_label($req['status'])) ?></span></td>
                            <td class="text-xs text-neutral-500"><?= esc(date('d/m/Y H:i', strtotime($req['created_at']))) ?></td>
                            <td><a href="<?= base_url('admin/bongkar-pesanan/' . $req['id']) ?>" class="table-action">Detail</a></td>
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
