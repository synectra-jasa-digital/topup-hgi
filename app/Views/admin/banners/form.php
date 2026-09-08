<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $banner = $banner ?? null; $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <h1 class="section-title"><?= $banner ? 'Ubah' : 'Tambah' ?> Banner</h1>
    <p class="section-subtitle">Atur visual promosi yang muncul di beranda.</p>
</div>

<?php if (empty($categories)): ?>
    <div class="empty-state">
        <span class="empty-state-icon">
            <span class="material-symbols-outlined text-[20px]">bookmarks</span>
        </span>
        <h2 class="empty-state-title">Belum ada kategori banner</h2>
        <p class="empty-state-copy">Buat kategori banner terlebih dahulu sebelum membuat banner baru.</p>
    </div>
<?php else: ?>
    <form method="post" enctype="multipart/form-data" action="<?= $banner ? base_url('admin/banner/' . $banner['id'] . '/ubah') : base_url('admin/banner/tambah') ?>" class="max-w-2xl space-y-5 panel-surface">
        <?= csrf_field() ?>
        <div>
            <label for="banner_category_id" class="form-label">Kategori Banner</label>
            <select id="banner_category_id" name="banner_category_id" class="form-input" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= (old('banner_category_id', $banner['banner_category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>><?= esc($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="image" class="form-label">Gambar Banner<?= $banner ? ' (kosongkan jika tidak diubah)' : '' ?></label>
            <?php if ($banner): ?><img src="<?= base_url($banner['image_path']) ?>" alt="Banner" class="mb-3 h-20 rounded-lg object-cover ring-1 ring-neutral-200"><?php endif; ?>
            <input type="file" id="image" name="image" accept="image/*" class="<?= isset($errors['image']) ? 'form-input-error' : 'form-input' ?>">
            <?php if (isset($errors['image'])): ?><p class="form-error"><?= esc($errors['image']) ?></p><?php endif; ?>
        </div>
        <div>
            <label for="link_url" class="form-label">Tautan (opsional)</label>
            <input type="url" id="link_url" name="link_url" value="<?= esc(old('link_url', $banner['link_url'] ?? '')) ?>" class="<?= isset($errors['link_url']) ? 'form-input-error' : 'form-input' ?>">
            <?php if (isset($errors['link_url'])): ?><p class="form-error"><?= esc($errors['link_url']) ?></p><?php endif; ?>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="start_date" class="form-label">Mulai Tayang</label>
                <input type="date" id="start_date" name="start_date" value="<?= esc(old('start_date', $banner['start_date'] ?? '')) ?>" class="form-input">
            </div>
            <div>
                <label for="end_date" class="form-label">Selesai Tayang</label>
                <input type="date" id="end_date" name="end_date" value="<?= esc(old('end_date', $banner['end_date'] ?? '')) ?>" class="form-input">
            </div>
        </div>
        <div>
            <label for="sort_order" class="form-label">Urutan Tampil</label>
            <input type="number" id="sort_order" name="sort_order" value="<?= esc(old('sort_order', $banner['sort_order'] ?? 0)) ?>" class="form-input">
        </div>
        <label class="flex items-center gap-2.5 text-sm text-neutral-700">
            <input type="checkbox" id="is_active" name="is_active" value="1" <?= (old('is_active', $banner['is_active'] ?? 1)) ? 'checked' : '' ?> class="h-4 w-4 rounded border-neutral-300 accent-primary">
            Aktif
        </label>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/banner') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
<?php endif; ?>
<?= $this->endSection() ?>
