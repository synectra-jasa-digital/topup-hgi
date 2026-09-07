<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="text-2xl font-bold text-neutral-900 mb-6"><?= $admin ? 'Ubah' : 'Tambah' ?> Akun Admin</h1>

<?php $errors = session()->getFlashdata('errors') ?? []; ?>

<form method="post" action="<?= $admin ? base_url('admin/akun-admin/' . $admin['id'] . '/ubah') : base_url('admin/akun-admin/tambah') ?>" class="card p-6 max-w-xl space-y-5">
    <?= csrf_field() ?>

    <div>
        <label for="name" class="form-label">Nama</label>
        <input type="text" id="name" name="name" value="<?= esc(old('name', $admin['name'] ?? '')) ?>"
               class="<?= isset($errors['name']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['name'])): ?><p class="form-error"><?= esc($errors['name']) ?></p><?php endif; ?>
    </div>
    <div>
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" value="<?= esc(old('email', $admin['email'] ?? '')) ?>"
               class="<?= isset($errors['email']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['email'])): ?><p class="form-error"><?= esc($errors['email']) ?></p><?php endif; ?>
    </div>
    <div>
        <label for="password" class="form-label">
            Kata Sandi <?php if ($admin): ?> <span class="text-xs">(isi untuk ganti)</span><?php endif; ?>
        </label>
        <input type="password" id="password" name="password" minlength="8"
               class="<?= isset($errors['password']) ? 'form-input-error' : 'form-input' ?>">
        <?php if (isset($errors['password'])): ?><p class="form-error"><?= esc($errors['password']) ?></p><?php endif; ?>
    </div>
    <div>
        <label for="role" class="form-label">Role</label>
        <select id="role" name="role" class="form-input" required>
            <option value="admin" <?= old('role', $admin['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="owner" <?= old('role', $admin['role'] ?? '') === 'owner' ? 'selected' : '' ?>>Owner</option>
        </select>
        <?php if (isset($errors['role'])): ?><p class="form-error"><?= esc($errors['role']) ?></p><?php endif; ?>
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" id="is_active" name="is_active" value="1"
               <?= old('is_active', $admin['is_active'] ?? 1) ? 'checked' : '' ?>>
        <label for="is_active" class="text-sm text-neutral-900">Aktif</label>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/akun-admin') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
