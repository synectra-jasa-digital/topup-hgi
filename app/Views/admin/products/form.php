<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <h1 class="text-xl font-semibold text-neutral-900"><?= $product ? 'Ubah' : 'Tambah' ?> Produk</h1>
</div>

<?php if (empty($categories)): ?>
    <div class="panel-surface flex flex-col items-center px-6 py-16 text-center">
        <span class="material-symbols-outlined text-[36px] text-neutral-300">category</span>
        <h2 class="mt-3 text-base font-semibold text-neutral-900">Belum ada kategori produk</h2>
        <p class="mt-1 text-sm text-neutral-500">Tambahkan kategori produk terlebih dahulu sebelum membuat produk baru.</p>
    </div>
<?php else: ?>
    <form method="post" action="<?= $product ? base_url('admin/produk/' . $product['id'] . '/ubah') : base_url('admin/produk/tambah') ?>" class="max-w-2xl space-y-5 panel-surface p-6">
        <?= csrf_field() ?>
        <div>
            <label for="category_id" class="form-label">Kategori</label>
            <select id="category_id" name="category_id" class="form-input" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= (old('category_id', $product['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>><?= esc($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="name" class="form-label">Nama Produk</label>
            <input type="text" id="name" name="name" value="<?= esc(old('name', $product['name'] ?? '')) ?>" class="<?= isset($errors['name']) ? 'form-input-error' : 'form-input' ?>" required>
            <?php if (isset($errors['name'])): ?><p class="form-error"><?= esc($errors['name']) ?></p><?php endif; ?>
        </div>
        <div>
            <label for="nominal" class="form-label">Nominal</label>
            <input type="text" id="nominal" name="nominal" value="<?= esc(old('nominal', $product['nominal'] ?? '')) ?>" class="form-input" required>
            <p class="form-help">Contoh: 100M Koin Gold, 1B Koin Gold, atau durasi member.</p>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label for="sell_price" class="form-label">Harga Jual (Rp)</label>
                <input type="number" step="0.01" id="sell_price" name="sell_price" value="<?= esc(old('sell_price', $product['sell_price'] ?? '')) ?>" class="<?= isset($errors['sell_price']) ? 'form-input-error' : 'form-input' ?>" required>
                <?php if (isset($errors['sell_price'])): ?><p class="form-error"><?= esc($errors['sell_price']) ?></p><?php endif; ?>
            </div>
            <div>
                <label for="cost_price" class="form-label">Harga Modal (Rp)</label>
                <input type="number" step="0.01" id="cost_price" name="cost_price" value="<?= esc(old('cost_price', $product['cost_price'] ?? '')) ?>" class="<?= isset($errors['cost_price']) ? 'form-input-error' : 'form-input' ?>" required>
                <?php if (isset($errors['cost_price'])): ?><p class="form-error"><?= esc($errors['cost_price']) ?></p><?php endif; ?>
            </div>
        </div>
        <div>
            <label for="sort_order" class="form-label">Urutan Tampil</label>
            <input type="number" id="sort_order" name="sort_order" value="<?= esc(old('sort_order', $product['sort_order'] ?? 0)) ?>" class="form-input">
        </div>
        <label class="flex items-center gap-2.5 text-sm text-neutral-700">
            <input type="checkbox" id="is_active" name="is_active" value="1" <?= (old('is_active', $product['is_active'] ?? 1)) ? 'checked' : '' ?> class="h-4 w-4 rounded accent-primary">
            Aktif
        </label>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/produk') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
<?php endif; ?>
<?= $this->endSection() ?>