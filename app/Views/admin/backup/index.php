<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
if (! function_exists('format_bytes')) {
    function format_bytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }

        return number_format($bytes / 1024, 1) . ' KB';
    }
}
?>

<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Backup Database</h1>
        <p class="section-subtitle">Buat dan unduh salinan cadangan seluruh data toko sewaktu-waktu.</p>
    </div>
    <form method="post" action="<?= base_url('admin/backup-database/buat') ?>" data-confirm="Buat backup database sekarang? Proses ini bisa memakan waktu beberapa saat tergantung ukuran data." data-confirm-title="Buat Backup" data-confirm-button="Ya, buat backup">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-primary shrink-0">
            <span class="material-symbols-outlined text-[18px]">database</span>
            Backup Sekarang
        </button>
    </form>
</div>

<div class="rounded-xl border border-primary/20 bg-primary/5 px-4 py-3 text-sm text-neutral-700">
    <span class="material-symbols-outlined mr-1 align-middle text-[16px] text-primary">info</span>
    File backup berisi seluruh isi database (termasuk data pelanggan) dan disimpan di luar folder publik. Simpan file yang diunduh di tempat aman dan hapus salinan lama yang tidak diperlukan.
</div>

<?php if (empty($backups)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">database</span>
        </span>
        <h2 class="empty-state-title">Belum ada backup</h2>
        <p class="empty-state-copy">Klik "Backup Sekarang" untuk membuat cadangan database pertama.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>Nama File</th>
                        <th>Ukuran</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $backup): ?>
                        <tr class="table-row">
                            <td class="font-mono text-xs text-neutral-700"><?= esc($backup['filename']) ?></td>
                            <td class="text-neutral-700"><?= format_bytes($backup['size']) ?></td>
                            <td class="text-xs text-neutral-500"><?= esc(date('d/m/Y H:i', $backup['created'])) ?></td>
                            <td>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="<?= base_url('admin/backup-database/' . $backup['filename'] . '/unduh') ?>" class="table-action">Unduh</a>
                                    <form method="post" action="<?= base_url('admin/backup-database/' . $backup['filename'] . '/hapus') ?>" class="inline" data-confirm="Hapus file backup ini? Tindakan tidak bisa dibatalkan." data-confirm-title="Hapus Backup" data-confirm-button="Ya, hapus">
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
<?php endif; ?>
<?= $this->endSection() ?>
