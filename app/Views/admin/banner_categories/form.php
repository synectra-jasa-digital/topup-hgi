<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $category = $category ?? null; $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <h1 class="section-title"><?= $category ? 'Ubah' : 'Tambah' ?> Kategori Banner</h1>
    <p class="section-subtitle">Gunakan nama yang singkat dan mudah dipahami.</p>
</div>

<form method="post" action="<?= $category ? base_url('admin/kategori-banner/' . $category['id'] . '/ubah') : base_url('admin/kategori-banner/tambah') ?>" class="max-w-lg space-y-5 panel-surface">
    <?= csrf_field() ?>
    <div>
        <label for="name" class="form-label">Nama Kategori</label>
        <input type="text" id="name" name="name" value="<?= esc(old('name', $category['name'] ?? '')) ?>" class="<?= isset($errors['name']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['name'])): ?><p class="form-error"><?= esc($errors['name']) ?></p><?php endif; ?>
    </div>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/kategori-banner') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
