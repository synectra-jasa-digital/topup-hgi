<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1 class="text-2xl font-bold text-neutral-900 mb-6"><?= $banner ? 'Ubah' : 'Tambah' ?> Banner</h1>

<?php $errors = session()->getFlashdata('errors') ?? []; ?>

<?php if (empty($categories)): ?>
    <div class="card p-8 text-center text-neutral-500">Belum ada kategori banner. Tambahkan kategori terlebih dahulu.</div>
<?php else: ?>
    <form method="post" enctype="multipart/form-data" action="<?= $banner ? base_url('admin/banner/' . $banner['id'] . '/ubah') : base_url('admin/banner/tambah') ?>" class="card p-6 max-w-lg space-y-4">
        <?= csrf_field() ?>

        <div>
            <label for="banner_category_id" class="form-label">Kategori Banner</label>
            <select id="banner_category_id" name="banner_category_id" class="form-input" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= (old('banner_category_id', $banner['banner_category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="image" class="form-label">Gambar Banner<?= $banner ? ' (kosongkan jika tidak diubah)' : '' ?></label>
            <?php if ($banner): ?>
                <img src="<?= base_url($banner['image_path']) ?>" alt="Banner" class="h-16 rounded-md mb-2">
            <?php endif; ?>
            <input type="file" id="image" name="image" accept="image/*" class="<?= isset($errors['image']) ? 'form-input-error' : 'form-input' ?>">
            <?php if (isset($errors['image'])): ?><p class="form-error"><?= esc($errors['image']) ?></p><?php endif; ?>
        </div>

        <div>
            <label for="link_url" class="form-label">Tautan (opsional)</label>
            <input type="url" id="link_url" name="link_url" value="<?= esc(old('link_url', $banner['link_url'] ?? '')) ?>" class="<?= isset($errors['link_url']) ? 'form-input-error' : 'form-input' ?>">
            <?php if (isset($errors['link_url'])): ?><p class="form-error"><?= esc($errors['link_url']) ?></p><?php endif; ?>
        </div>

        <div class="grid grid-cols-2 gap-4">
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

        <div class="flex items-center gap-2">
            <input type="checkbox" id="is_active" name="is_active" value="1" <?= (old('is_active', $banner['is_active'] ?? 1)) ? 'checked' : '' ?>>
            <label for="is_active" class="text-sm text-neutral-900">Aktif</label>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/banner') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
<?php endif; ?>
<?= $this->endSection() ?>
