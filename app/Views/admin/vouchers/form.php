<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="text-2xl font-bold text-neutral-900 mb-6"><?= $voucher ? 'Ubah' : 'Tambah' ?> Voucher</h1>

<?php $errors = session()->getFlashdata('errors') ?? []; ?>

<form method="post" action="<?= $voucher ? base_url('admin/voucher/' . $voucher['id'] . '/ubah') : base_url('admin/voucher/tambah') ?>" class="card p-6 max-w-lg space-y-4">
    <?= csrf_field() ?>

    <div>
        <label for="code" class="form-label">Kode Voucher</label>
        <input type="text" id="code" name="code" value="<?= esc(old('code', $voucher['code'] ?? '')) ?>"
               class="<?= isset($errors['code']) ? 'form-input-error' : 'form-input' ?>"
               placeholder="DISKON50" required>
        <?php if (isset($errors['code'])): ?><p class="form-error"><?= esc($errors['code']) ?></p><?php endif; ?>
    </div>

    <div>
        <label for="type" class="form-label">Tipe Diskon</label>
        <select id="type" name="type" class="form-input" required>
            <option value="percentage" <?= (old('type', $voucher['type'] ?? '') === 'percentage') ? 'selected' : '' ?>>Persentase (%)</option>
            <option value="nominal"    <?= (old('type', $voucher['type'] ?? '') === 'nominal')    ? 'selected' : '' ?>>Nominal (Rp)</option>
        </select>
        <?php if (isset($errors['type'])): ?><p class="form-error"><?= esc($errors['type']) ?></p><?php endif; ?>
    </div>

    <div>
        <label for="value" class="form-label">Nilai Diskon</label>
        <input type="number" id="value" name="value" min="0" step="0.01"
               value="<?= esc(old('value', $voucher['value'] ?? '')) ?>"
               class="<?= isset($errors['value']) ? 'form-input-error' : 'form-input' ?>" required>
        <?php if (isset($errors['value'])): ?><p class="form-error"><?= esc($errors['value']) ?></p><?php endif; ?>
        <p class="text-xs text-neutral-500 mt-1">Persentase: 0-100 | Nominal: jumlah rupiah</p>
    </div>

    <div>
        <label for="max_discount" class="form-label">Maksimum Diskon (opsional, untuk tipe %)</label>
        <input type="number" id="max_discount" name="max_discount" min="0" step="0.01"
               value="<?= esc(old('max_discount', $voucher['max_discount'] ?? '')) ?>"
               class="form-input" placeholder="contoh: 25000">
        <?php if (isset($errors['max_discount'])): ?><p class="form-error"><?= esc($errors['max_discount']) ?></p><?php endif; ?>
    </div>

    <div>
        <label for="min_purchase" class="form-label">Minimum Pembelian (Rp)</label>
        <input type="number" id="min_purchase" name="min_purchase" min="0" step="0.01"
               value="<?= esc(old('min_purchase', $voucher['min_purchase'] ?? '0')) ?>"
               class="<?= isset($errors['min_purchase']) ? 'form-input-error' : 'form-input' ?>">
        <?php if (isset($errors['min_purchase'])): ?><p class="form-error"><?= esc($errors['min_purchase']) ?></p><?php endif; ?>
    </div>

    <div>
        <label for="quota" class="form-label">Kuota (0 = tidak terbatas)</label>
        <input type="number" id="quota" name="quota" min="0" step="1"
               value="<?= esc(old('quota', $voucher['quota'] ?? '0')) ?>"
               class="form-input">
        <?php if (isset($errors['quota'])): ?><p class="form-error"><?= esc($errors['quota']) ?></p><?php endif; ?>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="start_date" class="form-label">Mulai Berlaku</label>
            <input type="date" id="start_date" name="start_date"
                   value="<?= esc(old('start_date', $voucher['start_date'] ?? '')) ?>" class="form-input">
        </div>
        <div>
            <label for="end_date" class="form-label">Berlaku Sampai</label>
            <input type="date" id="end_date" name="end_date"
                   value="<?= esc(old('end_date', $voucher['end_date'] ?? '')) ?>" class="form-input">
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" id="is_active" name="is_active" value="1"
               <?= (old('is_active', $voucher['is_active'] ?? 1)) ? 'checked' : '' ?>>
        <label for="is_active" class="text-sm text-neutral-900">Aktif</label>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/voucher') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
