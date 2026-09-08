<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $pager = $pager ?? null; ?>
<div>
    <h1 class="section-title">Log Aktivitas Admin</h1>
    <p class="section-subtitle">Catatan aksi yang dilakukan oleh pengguna panel.</p>
</div>

<?php if (empty($logs)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">fact_check</span>
        </span>
        <h2 class="empty-state-title">Belum ada log aktivitas</h2>
        <p class="empty-state-copy">Aksi admin akan tercatat di sini.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>Waktu</th>
                        <th>Admin</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr class="table-row">
                            <td class="whitespace-nowrap text-neutral-500"><?= esc($log['created_at']) ?></td>
                            <td class="font-medium text-neutral-900"><?= esc($log['admin_name'] ?? '—') ?></td>
                            <td><span class="badge badge-neutral font-mono"><?= esc($log['action']) ?></span></td>
                            <td class="text-neutral-700"><?= esc($log['description']) ?></td>
                            <td class="font-mono text-neutral-500"><?= esc($log['ip_address']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php if ($pager && $pager->getPageCount() > 1): ?>
        <div class="mt-4"><?= $pager->links() ?></div>
    <?php endif; ?>
<?php endif; ?>
<?= $this->endSection() ?>
