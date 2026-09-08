<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $voucher = $voucher ?? null; $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <h1 class="section-title"><?= $voucher ? 'Ubah' : 'Tambah' ?> Voucher</h1>
    <p class="section-subtitle">Atur potongan harga dengan konfigurasi yang jelas.</p>
</div>

<form method="post" action="<?= $voucher ? base_url('admin/voucher/' . $voucher['id'] . '/ubah') : base_url('admin/voucher/tambah') ?>" class="max-w-2xl space-y-5 panel-surface">
    <?= csrf_field() ?>
    <div>
        <label for="code" class="form-label">Kode Voucher</label>
        <input type="text" id="code" name="code" value="<?= esc(old('code', $voucher['code'] ?? '')) ?>" class="<?= isset($errors['code']) ? 'form-input-error' : 'form-input' ?>" placeholder="DISKON50" required>
        <?php if (isset($errors['code'])): ?><p class="form-error"><?= esc($errors['code']) ?></p><?php endif; ?>
    </div>
    <div>
        <label for="type" class="form-label">Tipe Diskon</label>
        <select id="type" name="type" class="form-input" required>
            <option value="percentage" <?= (old('type', $voucher['type'] ?? '') === 'percentage') ? 'selected' : '' ?>>Persentase (%)</option>
            <option value="nominal" <?= (old('type', $voucher['type'] ?? '') === 'nominal') ? 'selected' : '' ?>>Nominal (Rp)</option>
        </select>
        <?php if (isset($errors['type'])): ?><p class="form-error"><?= esc($errors['type']) ?></p><?php endif; ?>
    </div>
    <div>
        <label for="value" class="form-label">Nilai Diskon</label>
        <input type="number" id="value" name="value" min="0" step="0.01" value="<?= esc(old('value', $voucher['value'] ?? '')) ?>" class="<?= isset($errors['value']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['value'])): ?><p class="form-error"><?= esc($errors['value']) ?></p><?php endif; ?>
        <p class="form-help">Persentase: 0-100 | Nominal: jumlah rupiah</p>
    </div>
    <div>
        <label for="max_discount" class="form-label">Maksimum Diskon (opsional, untuk tipe %)</label>
        <input type="number" id="max_discount" name="max_discount" min="0" step="0.01" value="<?= esc(old('max_discount', $voucher['max_discount'] ?? '')) ?>" class="form-input" placeholder="contoh: 25000">
        <?php if (isset($errors['max_discount'])): ?><p class="form-error"><?= esc($errors['max_discount']) ?></p><?php endif; ?>
    </div>
    <div>
        <label for="min_purchase" class="form-label">Minimum Pembelian (Rp)</label>
        <input type="number" id="min_purchase" name="min_purchase" min="0" step="0.01" value="<?= esc(old('min_purchase', $voucher['min_purchase'] ?? '0')) ?>" class="<?= isset($errors['min_purchase']) ? 'form-input-error' : 'form-input' ?>">
        <?php if (isset($errors['min_purchase'])): ?><p class="form-error"><?= esc($errors['min_purchase']) ?></p><?php endif; ?>
    </div>
    <div>
        <label for="quota" class="form-label">Kuota (0 = tidak terbatas)</label>
        <input type="number" id="quota" name="quota" min="0" step="1" value="<?= esc(old('quota', $voucher['quota'] ?? '0')) ?>" class="form-input">
        <?php if (isset($errors['quota'])): ?><p class="form-error"><?= esc($errors['quota']) ?></p><?php endif; ?>
    </div>
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="start_date" class="form-label">Mulai Berlaku</label>
            <input type="date" id="start_date" name="start_date" value="<?= esc(old('start_date', $voucher['start_date'] ?? '')) ?>" class="form-input">
        </div>
        <div>
            <label for="end_date" class="form-label">Berlaku Sampai</label>
            <input type="date" id="end_date" name="end_date" value="<?= esc(old('end_date', $voucher['end_date'] ?? '')) ?>" class="form-input">
        </div>
    </div>
    <label class="flex items-center gap-2.5 text-sm text-neutral-700">
        <input type="checkbox" id="is_active" name="is_active" value="1" <?= (old('is_active', $voucher['is_active'] ?? 1)) ? 'checked' : '' ?> class="h-4 w-4 rounded border-neutral-300 accent-primary">
        Aktif
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/voucher') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
