<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <h1 class="section-title">Profil Saya</h1>
    <p class="section-subtitle">Kelola informasi akun dan foto profil Anda.</p>
</div>

<form method="post" action="<?= base_url('admin/profile') ?>" enctype="multipart/form-data" class="mt-6 space-y-5 panel-surface">
    <?= csrf_field() ?>
    <div>
        <label for="photo" class="form-label">Foto Profil</label>
        <div class="mb-3 flex items-center gap-3">
            <?php if (! empty($admin['photo'])): ?>
                <img src="<?= base_url($admin['photo']) ?>" alt="Foto profil" class="h-14 w-14 rounded-full border border-neutral-200 object-cover">
            <?php else: ?>
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary-light text-lg font-semibold text-primary">
                    <?= esc(strtoupper(substr($admin['name'], 0, 1))) ?>
                </div>
            <?php endif; ?>
            <p class="text-sm text-neutral-500">Unggah foto baru untuk menggantinya.</p>
        </div>
        <input type="file" id="photo" name="photo" accept="image/*" class="<?= isset($errors['photo']) ? 'form-input-error' : 'form-input' ?>">
        <?php if (isset($errors['photo'])): ?><p class="form-error"><?= esc($errors['photo']) ?></p><?php endif; ?>
        <p class="form-help">PNG/JPG, maksimum 2MB.</p>
    </div>
    <div>
        <label for="name" class="form-label">Nama</label>
        <input type="text" id="name" name="name" value="<?= esc(old('name', $admin['name'])) ?>" class="<?= isset($errors['name']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['name'])): ?><p class="form-error"><?= esc($errors['name']) ?></p><?php endif; ?>
    </div>
    <div>
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" value="<?= esc(old('email', $admin['email'])) ?>" class="<?= isset($errors['email']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['email'])): ?><p class="form-error"><?= esc($errors['email']) ?></p><?php endif; ?>
    </div>
    <div>
        <label for="password" class="form-label">Kata Sandi Baru <span class="text-sm font-normal text-neutral-500">(kosongkan jika tidak diubah)</span></label>
        <input type="password" id="password" name="password" minlength="8" class="<?= isset($errors['password']) ? 'form-input-error' : 'form-input' ?>">
        <?php if (isset($errors['password'])): ?><p class="form-error"><?= esc($errors['password']) ?></p><?php endif; ?>
    </div>
    <div>
        <p class="form-label">Role</p>
        <span class="badge badge-primary capitalize"><?= esc($admin['role']) ?></span>
    </div>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>
<?= $this->endSection() ?>
