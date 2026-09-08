<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $category = $category ?? null; $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <h1 class="section-title"><?= $category ? 'Ubah' : 'Tambah' ?> Kategori Produk</h1>
    <p class="section-subtitle">Atur struktur kategori untuk katalog produk publik.</p>
</div>

<form method="post" action="<?= $category ? base_url('admin/kategori-produk/' . $category['id'] . '/ubah') : base_url('admin/kategori-produk/tambah') ?>" enctype="multipart/form-data" class="space-y-5 panel-surface">
    <?= csrf_field() ?>
    <div>
        <label for="icon" class="form-label">Ikon Kategori</label>
        <div class="mb-3 flex items-center gap-3">
            <?php if (! empty($category['icon'])): ?>
                <img src="<?= base_url($category['icon']) ?>" alt="Ikon kategori" class="h-12 w-12 rounded-lg border border-neutral-200 bg-white object-contain p-1.5">
            <?php else: ?>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg border border-dashed border-neutral-200 bg-neutral-50 text-neutral-400">
                    <span class="material-symbols-outlined text-[22px]">category</span>
                </div>
            <?php endif; ?>
            <p class="text-sm text-neutral-500">Tampil di pilihan kategori pada halaman publik.</p>
        </div>
        <input type="file" id="icon" name="icon" accept="image/*" class="<?= isset($errors['icon']) ? 'form-input-error' : 'form-input' ?>">
        <?php if (isset($errors['icon'])): ?><p class="form-error"><?= esc($errors['icon']) ?></p><?php endif; ?>
        <p class="form-help">PNG/JPG/SVG, maksimum 1MB. Kosongkan jika ikon default sudah cukup.</p>
    </div>
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
        <input type="checkbox" id="is_active" name="is_active" value="1" <?= (old('is_active', $category['is_active'] ?? 1)) ? 'checked' : '' ?> class="h-4 w-4 rounded border-neutral-300 accent-primary">
        Aktif
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/kategori-produk') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
