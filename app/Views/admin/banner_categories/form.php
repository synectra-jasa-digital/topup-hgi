<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1 class="text-2xl font-bold text-neutral-900 mb-6"><?= $category ? 'Ubah' : 'Tambah' ?> Kategori Banner</h1>

<?php $errors = session()->getFlashdata('errors') ?? []; ?>

<form method="post" action="<?= $category ? base_url('admin/kategori-banner/' . $category['id'] . '/ubah') : base_url('admin/kategori-banner/tambah') ?>" class="card p-6 max-w-lg space-y-4">
    <?= csrf_field() ?>

    <div>
        <label for="name" class="form-label">Nama Kategori</label>
        <input type="text" id="name" name="name" value="<?= esc(old('name', $category['name'] ?? '')) ?>" class="<?= isset($errors['name']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['name'])): ?><p class="form-error"><?= esc($errors['name']) ?></p><?php endif; ?>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/kategori-banner') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
