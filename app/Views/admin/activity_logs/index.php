<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1 class="text-2xl font-bold text-neutral-900 mb-6">Log Aktivitas Admin</h1>

<?php if (empty($logs)): ?>
    <div class="card p-8 text-center text-neutral-500">Belum ada log aktivitas.</div>
<?php else: ?>
    <div class="card overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="border-b border-neutral-200 text-neutral-500">
                <tr>
                    <th class="px-4 py-3">Waktu</th>
                    <th class="px-4 py-3">Admin</th>
                    <th class="px-4 py-3">Aksi</th>
                    <th class="px-4 py-3">Deskripsi</th>
                    <th class="px-4 py-3">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                <?php foreach ($logs as $log): ?>
                    <tr class="even:bg-neutral-50">
                        <td class="px-4 py-3 text-neutral-500 whitespace-nowrap"><?= esc($log['created_at']) ?></td>
                        <td class="px-4 py-3 font-semibold"><?= esc($log['admin_name'] ?? '—') ?></td>
                        <td class="px-4 py-3">
                            <span class="badge-neutral px-2 py-0.5 rounded text-xs font-mono"><?= esc($log['action']) ?></span>
                        </td>
                        <td class="px-4 py-3"><?= esc($log['description']) ?></td>
                        <td class="px-4 py-3 font-mono text-neutral-500"><?= esc($log['ip_address']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        <?= $pager->links() ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
