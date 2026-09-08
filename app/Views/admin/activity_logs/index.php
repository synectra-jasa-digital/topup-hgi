<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div>
    <h1 class="text-xl font-semibold text-neutral-900">Log Aktivitas Admin</h1>
    <p class="mt-1 text-sm text-neutral-500">Catatan aksi yang dilakukan oleh pengguna panel.</p>
</div>

<?php if (empty($logs)): ?>
    <div class="panel-surface flex flex-col items-center px-6 py-16 text-center">
        <span class="material-symbols-outlined text-[36px] text-neutral-300">fact_check</span>
        <h2 class="mt-3 text-base font-semibold text-neutral-900">Belum ada log aktivitas</h2>
        <p class="mt-1 text-sm text-neutral-500">Aksi admin akan tercatat di sini.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="border-b border-neutral-100 px-4 py-3">Waktu</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Admin</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Aksi</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Deskripsi</th>
                        <th class="border-b border-neutral-100 px-4 py-3">IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr class="table-row">
                            <td class="whitespace-nowrap px-4 py-3 text-neutral-500"><?= esc($log['created_at']) ?></td>
                            <td class="px-4 py-3 font-medium text-neutral-900"><?= esc($log['admin_name'] ?? '—') ?></td>
                            <td class="px-4 py-3"><span class="badge badge-neutral font-mono"><?= esc($log['action']) ?></span></td>
                            <td class="px-4 py-3 text-neutral-700"><?= esc($log['description']) ?></td>
                            <td class="px-4 py-3 font-mono text-neutral-500"><?= esc($log['ip_address']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4"><?= $pager->links() ?></div>
<?php endif; ?>
<?= $this->endSection() ?>