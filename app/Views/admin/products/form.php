<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1 class="text-2xl font-bold text-neutral-900 mb-6"><?= $product ? 'Ubah' : 'Tambah' ?> Produk</h1>

<?php $errors = session()->getFlashdata('errors') ?? []; ?>

<?php if (empty($categories)): ?>
    <div class="card p-8 text-center text-neutral-500">Belum ada kategori produk. Tambahkan kategori terlebih dahulu.</div>
<?php else: ?>
    <form method="post" action="<?= $product ? base_url('admin/produk/' . $product['id'] . '/ubah') : base_url('admin/produk/tambah') ?>" class="card p-6 max-w-lg space-y-4">
        <?= csrf_field() ?>

        <div>
            <label for="category_id" class="form-label">Kategori</label>
            <select id="category_id" name="category_id" class="form-input" required>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= (old('category_id', $product['category_id'] ?? '') == $category['id']) ? 'selected' : '' ?>>
                        <?= esc($category['name']) ?>
                    </option>
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
        </div>

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

        <div>
            <label for="sort_order" class="form-label">Urutan Tampil</label>
            <input type="number" id="sort_order" name="sort_order" value="<?= esc(old('sort_order', $product['sort_order'] ?? 0)) ?>" class="form-input">
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" id="is_active" name="is_active" value="1" <?= (old('is_active', $product['is_active'] ?? 1)) ? 'checked' : '' ?>>
            <label for="is_active" class="text-sm text-neutral-900">Aktif</label>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/produk') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
<?php endif; ?>
<?= $this->endSection() ?>
