<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Akun Admin</h1>
        <p class="section-subtitle">Kelola akses admin tambahan dan status aktifnya.</p>
    </div>
    <a href="<?= base_url('admin/akun-admin/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Admin
    </a>
</div>

<?php if (empty($admins)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
        </span>
        <h2 class="empty-state-title">Belum ada admin lain</h2>
        <p class="empty-state-copy">Tambahkan akun admin jika diperlukan.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr class="table-row">
                            <td class="font-medium text-neutral-900"><?= esc($admin['name']) ?></td>
                            <td class="text-neutral-600"><?= esc($admin['email']) ?></td>
                            <td><span class="badge <?= $admin['role'] === 'owner' ? 'badge-primary' : 'badge-neutral' ?>"><?= ucfirst(esc($admin['role'])) ?></span></td>
                            <td><span class="badge <?= $admin['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $admin['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <a href="<?= base_url('admin/akun-admin/' . $admin['id'] . '/ubah') ?>" class="table-action">Ubah</a>
                                    <form method="post" action="<?= base_url('admin/akun-admin/' . $admin['id'] . '/hapus') ?>" class="inline" data-confirm="Hapus akun ini?" data-confirm-title="Hapus Akun Admin" data-confirm-button="Ya, hapus">
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
