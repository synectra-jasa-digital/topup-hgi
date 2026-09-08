<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Pengaturan Toko</h1>
        <p class="section-subtitle">Kelola identitas toko dan kredensial integrasi.</p>
    </div>
</div>

<form method="post" action="<?= base_url('admin/pengaturan-toko') ?>" enctype="multipart/form-data" class="max-w-3xl space-y-5 panel-surface">
    <?= csrf_field() ?>
    <div>
        <label for="logo" class="form-label">Logo Toko</label>
        <?php if (! empty($settings['store_logo'])): ?>
            <div class="mb-3 flex items-center gap-3">
                <img src="<?= base_url($settings['store_logo']) ?>" alt="Logo toko saat ini" class="h-14 w-14 rounded-lg border border-neutral-200 bg-white object-contain p-1.5">
                <p class="text-sm text-neutral-500">Logo saat ini. Unggah gambar baru untuk menggantinya.</p>
            </div>
        <?php endif; ?>
        <input type="file" id="logo" name="logo" accept="image/*" class="<?= isset($errors['logo']) ? 'form-input-error' : 'form-input' ?>">
        <?php if (isset($errors['logo'])): ?><p class="form-error"><?= esc($errors['logo']) ?></p><?php endif; ?>
        <p class="form-help">PNG/JPG, maksimum 2MB. Kosongkan jika tidak ingin mengubah logo.</p>
    </div>
    <div>
        <label for="store_name" class="form-label">Nama Toko</label>
        <input type="text" id="store_name" name="store_name" value="<?= esc(old('store_name', $settings['store_name'] ?? '')) ?>" class="form-input" required>
    </div>
    <div>
        <label for="store_contact" class="form-label">Kontak Toko (WhatsApp / Telepon)</label>
        <input type="text" id="store_contact" name="store_contact" value="<?= esc(old('store_contact', $settings['store_contact'] ?? '')) ?>" class="form-input">
    </div>
    <div>
        <label for="store_address" class="form-label">Alamat Toko</label>
        <textarea id="store_address" name="store_address" rows="3" class="form-input"><?= esc(old('store_address', $settings['store_address'] ?? '')) ?></textarea>
    </div>
    <div>
        <label for="midtrans_key" class="form-label">Kunci Midtrans</label>
        <input type="text" id="midtrans_key" name="midtrans_key" value="<?= esc(old('midtrans_key', $settings['midtrans_key'] ?? '')) ?>" class="form-input">
    </div>
    <div>
        <label for="wablas_key" class="form-label">Token / Kunci Wablas</label>
        <input type="text" id="wablas_key" name="wablas_key" value="<?= esc(old('wablas_key', $settings['wablas_key'] ?? '')) ?>" class="form-input">
    </div>
    <label class="flex items-center gap-2.5 text-sm text-neutral-700">
        <input type="checkbox" id="maintenance" name="maintenance" value="1" <?= (old('maintenance', $settings['maintenance'] ?? '0') === '1') ? 'checked' : '' ?> class="h-4 w-4 rounded border-neutral-300 accent-primary">
        Mode Pemeliharaan Aktif
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/pengaturan-toko') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
