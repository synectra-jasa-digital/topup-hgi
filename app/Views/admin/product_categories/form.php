<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <h1 class="text-xl font-semibold text-neutral-900"><?= $category ? 'Ubah' : 'Tambah' ?> Kategori Produk</h1>
</div>

<?php if (empty($categories)): ?>
    <div class="panel-surface flex flex-col items-center px-6 py-16 text-center">
        <span class="material-symbols-outlined text-[36px] text-neutral-300">category</span>
        <h2 class="mt-3 text-base font-semibold text-neutral-900">Belum ada kategori produk</h2>
        <p class="mt-1 text-sm text-neutral-500">Tambahkan kategori produk terlebih dahulu sebelum membuat produk baru.</p>
    </div>
<?php else: ?>
    <form method="post" action="<?= $category ? base_url('admin/kategori-produk/' . $category['id'] . '/ubah') : base_url('admin/kategori-produk/tambah') ?>" class="max-w-xl space-y-5 panel-surface p-6">
        <?= csrf_field() ?>
        <div>
            <label for="name" class="form-label">Nama Kategori</label>
            <input type="text" id="name" name="name" value="<?= esc(old('name', $category['name'] ?? '')) ?>" class="<?= isset($errors['name']) ? 'form-input-error' : 'form-input' ?>" required>
            <?php if (isset($errors['name'])): ?><p class="form-error"><?= esc($errors['name']) ?></p><?php endif; ?>
        </div>
        <div>
            <label for="sort_order" class="form-label">Urutan Tampil</label>
            <input type="number" id="sort_order" name="sort_order" value="<?= esc(old('sort_order', $category['sort_order'] ?? 0)) ?>" class="form-input">
        </div>
        <label class="flex items-center gap-2.5 text-sm text-neutral-700">
            <input type="checkbox" id="is_active" name="is_active" value="1" <?= (old('is_active', $category['is_active'] ?? 1)) ? 'checked' : '' ?> class="h-4 w-4 rounded accent-primary">
            Aktif
        </label>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/kategori-produk') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
<?php endif; ?>
<?= $this->endSection() ?>