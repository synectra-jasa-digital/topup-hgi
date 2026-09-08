<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $catalog = $catalog ?? null; $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <h1 class="section-title"><?= $catalog ? 'Ubah' : 'Tambah' ?> Katalog Bongkar</h1>
    <p class="section-subtitle">Atur item dan rate beli-kembali yang tampil di halaman jual/bongkar publik.</p>
</div>

<form method="post" action="<?= $catalog ? base_url('admin/bongkar-katalog/' . $catalog['id'] . '/ubah') : base_url('admin/bongkar-katalog/tambah') ?>" class="space-y-5 panel-surface">
    <?= csrf_field() ?>
    <div>
        <label for="code" class="form-label">Kode</label>
        <input type="text" id="code" name="code" value="<?= esc(old('code', $catalog['code'] ?? '')) ?>" class="<?= isset($errors['code']) ? 'form-input-error' : 'form-input' ?>" placeholder="KOIN-EMAS" required>
        <?php if (isset($errors['code'])): ?><p class="form-error"><?= esc($errors['code']) ?></p><?php endif; ?>
        <p class="form-help">Kode unik internal, huruf otomatis jadi kapital.</p>
    </div>
    <div>
        <label for="name" class="form-label">Nama Item</label>
        <input type="text" id="name" name="name" value="<?= esc(old('name', $catalog['name'] ?? '')) ?>" class="<?= isset($errors['name']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['name'])): ?><p class="form-error"><?= esc($errors['name']) ?></p><?php endif; ?>
    </div>
    <div>
        <label for="unit_label" class="form-label">Satuan</label>
        <input type="text" id="unit_label" name="unit_label" value="<?= esc(old('unit_label', $catalog['unit_label'] ?? 'kartu')) ?>" class="<?= isset($errors['unit_label']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['unit_label'])): ?><p class="form-error"><?= esc($errors['unit_label']) ?></p><?php endif; ?>
        <p class="form-help">Contoh: kartu, koin, durasi.</p>
    </div>
    <div>
        <label for="base_rate" class="form-label">Rate per Satuan (Rp)</label>
        <input type="number" step="0.01" id="base_rate" name="base_rate" value="<?= esc(old('base_rate', $catalog['base_rate'] ?? '')) ?>" class="<?= isset($errors['base_rate']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['base_rate'])): ?><p class="form-error"><?= esc($errors['base_rate']) ?></p><?php endif; ?>
        <p class="form-help">Estimasi payout = jumlah × rate ini.</p>
    </div>
    <div>
        <label for="sort_order" class="form-label">Urutan Tampil</label>
        <input type="number" id="sort_order" name="sort_order" value="<?= esc(old('sort_order', $catalog['sort_order'] ?? 0)) ?>" class="form-input">
    </div>
    <label class="flex items-center gap-2.5 text-sm text-neutral-700">
        <input type="checkbox" id="is_active" name="is_active" value="1" <?= (old('is_active', $catalog['is_active'] ?? 1)) ? 'checked' : '' ?> class="h-4 w-4 rounded border-neutral-300 accent-primary">
        Aktif
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/bongkar-katalog') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
