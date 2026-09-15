<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $method = $method ?? null; $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <h1 class="section-title"><?= $method ? 'Ubah' : 'Tambah' ?> Metode Pencairan</h1>
    <p class="section-subtitle">Atur channel bank/e-wallet yang tampil di halaman jual/bongkar publik.</p>
</div>

<form method="post" action="<?= $method ? base_url('admin/bongkar-metode-pencairan/' . $method['id'] . '/ubah') : base_url('admin/bongkar-metode-pencairan/tambah') ?>" class="space-y-5 panel-surface">
    <?= csrf_field() ?>
    <div>
        <label for="code" class="form-label">Kode</label>
        <input type="text" id="code" name="code" value="<?= esc(old('code', $method['code'] ?? '')) ?>" class="<?= isset($errors['code']) ? 'form-input-error' : 'form-input' ?>" placeholder="BCA" required>
        <?php if (isset($errors['code'])): ?><p class="form-error"><?= esc($errors['code']) ?></p><?php endif; ?>
        <p class="form-help">Kode unik, dikirim apa adanya oleh customer saat mengajukan bongkar.</p>
    </div>
    <div>
        <label for="name" class="form-label">Nama Tampilan</label>
        <input type="text" id="name" name="name" value="<?= esc(old('name', $method['name'] ?? '')) ?>" class="<?= isset($errors['name']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['name'])): ?><p class="form-error"><?= esc($errors['name']) ?></p><?php endif; ?>
    </div>
    <div>
        <label for="category" class="form-label">Kategori</label>
        <select id="category" name="category" class="form-input">
            <option value="bank" <?= old('category', $method['category'] ?? 'bank') === 'bank' ? 'selected' : '' ?>>Bank / Virtual Account</option>
            <option value="ewallet" <?= old('category', $method['category'] ?? '') === 'ewallet' ? 'selected' : '' ?>>E-Wallet</option>
        </select>
    </div>
    <div>
        <label for="sort_order" class="form-label">Urutan Tampil</label>
        <input type="number" id="sort_order" name="sort_order" value="<?= esc(old('sort_order', $method['sort_order'] ?? 0)) ?>" class="form-input">
    </div>
    <label class="flex items-center gap-2.5 text-sm text-neutral-700">
        <input type="checkbox" id="is_active" name="is_active" value="1" <?= (old('is_active', $method['is_active'] ?? 1)) ? 'checked' : '' ?> class="h-4 w-4 rounded border-neutral-300 accent-primary">
        Aktif
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/bongkar-metode-pencairan') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
