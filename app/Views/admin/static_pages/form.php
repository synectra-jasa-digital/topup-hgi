<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="text-2xl font-bold text-neutral-900 mb-6"><?= $page ? 'Ubah' : 'Tambah' ?> Halaman Statis</h1>

<?php $errors = session()->getFlashdata('errors') ?? []; ?>

<form method="post" action="<?= $page ? base_url('admin/halaman-statis/' . $page['id'] . '/ubah') : base_url('admin/halaman-statis/tambah') ?>" class="card p-6 max-w-3xl space-y-4">
    <?= csrf_field() ?>

    <div>
        <label for="title" class="form-label">Judul</label>
        <input type="text" id="title" name="title" value="<?= esc(old('title', $page['title'] ?? '')) ?>"
               class="<?= isset($errors['title']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['title'])): ?><p class="form-error"><?= esc($errors['title']) ?></p><?php endif; ?>
    </div>

    <div>
        <label for="content" class="form-label">Konten</label>
        <textarea id="content" name="content" rows="10"
                  class="<?= isset($errors['content']) ? 'form-input-error' : 'form-input' ?>"><?= esc(old('content', $page['content'] ?? '')) ?></textarea>
        <?php if (isset($errors['content'])): ?><p class="form-error"><?= esc($errors['content']) ?></p><?php endif; ?>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/halaman-statis') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
