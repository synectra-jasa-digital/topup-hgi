<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-neutral-900">Akun Admin</h1>
    <a href="<?= base_url('admin/akun-admin/tambah') ?>" class="btn btn-primary">Tambah Admin</a>
</div>

<?php if (empty($admins)): ?>
    <div class="card p-8 text-center text-neutral-500">Belum ada admin lain selain Anda.</div>
<?php else: ?>
    <div class="card overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="border-b border-neutral-200 text-neutral-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                <?php foreach ($admins as $admin): ?>
                    <tr class="even:bg-neutral-50">
                        <td class="px-4 py-3 font-semibold"><?= esc($admin['name']) ?></td>
                        <td class="px-4 py-3 text-neutral-600"><?= esc($admin['email']) ?></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-semibold <?= $admin['role'] === 'owner' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">
                                <?= ucfirst(esc($admin['role'])) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($admin['is_active']): ?>
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="<?= base_url('admin/akun-admin/' . $admin['id'] . '/ubah') ?>" class="text-primary hover:underline text-xs">Ubah</a>
                            <form method="post" action="<?= base_url('admin/akun-admin/' . $admin['id'] . '/hapus') ?>" class="inline" onsubmit="return confirm('Hapus akun ini?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="text-danger hover:underline text-xs">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
