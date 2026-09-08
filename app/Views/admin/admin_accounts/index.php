<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="text-xl font-semibold text-neutral-900">Akun Admin</h1>
        <p class="mt-1 text-sm text-neutral-500">Kelola akses admin tambahan dan status aktifnya.</p>
    </div>
    <a href="<?= base_url('admin/akun-admin/tambah') ?>" class="btn btn-primary shrink-0">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Admin
    </a>
</div>

<?php if (empty($admins)): ?>
    <div class="panel-surface flex flex-col items-center px-6 py-16 text-center">
        <span class="material-symbols-outlined text-[36px] text-neutral-300">manage_accounts</span>
        <h2 class="mt-3 text-base font-semibold text-neutral-900">Belum ada admin lain</h2>
        <p class="mt-1 text-sm text-neutral-500">Tambahkan akun admin jika diperlukan.</p>
    </div>
<?php else: ?>
    <div class="table-shell">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="table-head">
                    <tr>
                        <th class="border-b border-neutral-100 px-4 py-3">Nama</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Email</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Role</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Status</th>
                        <th class="border-b border-neutral-100 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr class="table-row">
                            <td class="px-4 py-3 font-medium text-neutral-900"><?= esc($admin['name']) ?></td>
                            <td class="px-4 py-3 text-neutral-600"><?= esc($admin['email']) ?></td>
                            <td class="px-4 py-3"><span class="<?= $admin['role'] === 'owner' ? 'badge-primary' : 'badge-neutral' ?>"><?= ucfirst(esc($admin['role'])) ?></span></td>
                            <td class="px-4 py-3"><span class="<?= $admin['is_active'] ? 'badge-success' : 'badge-neutral' ?>"><?= $admin['is_active'] ? 'Aktif' : 'Nonaktif' ?></span></td>
                            <td class="px-4 py-3">
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