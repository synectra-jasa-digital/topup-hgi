<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $page = $page ?? null; $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <h1 class="section-title"><?= $page ? 'Ubah' : 'Tambah' ?> Halaman Statis</h1>
    <p class="section-subtitle">Tulis konten yang jelas dan ringkas untuk pelanggan.</p>
</div>

<form method="post" action="<?= $page ? base_url('admin/halaman-statis/' . $page['id'] . '/ubah') : base_url('admin/halaman-statis/tambah') ?>" class="space-y-5 panel-surface">
    <?= csrf_field() ?>
    <div>
        <label for="title" class="form-label">Judul</label>
        <input type="text" id="title" name="title" value="<?= esc(old('title', $page['title'] ?? '')) ?>" class="<?= isset($errors['title']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['title'])): ?><p class="form-error"><?= esc($errors['title']) ?></p><?php endif; ?>
    </div>
    <div>
        <label for="content" class="form-label">Konten</label>
        <textarea id="content" name="content" rows="10" class="<?= isset($errors['content']) ? 'form-input-error' : 'form-input' ?>"><?= esc(old('content', $page['content'] ?? '')) ?></textarea>
        <?php if (isset($errors['content'])): ?><p class="form-error"><?= esc($errors['content']) ?></p><?php endif; ?>
    </div>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/halaman-statis') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
