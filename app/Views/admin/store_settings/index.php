<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div>
    <h1 class="text-xl font-semibold text-neutral-900">Pengaturan Toko</h1>
    <p class="mt-1 text-sm text-neutral-500">Kelola identitas toko dan kredensial integrasi.</p>
</div>

<form method="post" action="<?= base_url('admin/pengaturan-toko') ?>" class="max-w-3xl space-y-5 panel-surface p-6">
    <?= csrf_field() ?>
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
        <input type="checkbox" id="maintenance" name="maintenance" value="1" <?= (old('maintenance', $settings['maintenance'] ?? '0') === '1') ? 'checked' : '' ?> class="h-4 w-4 rounded accent-primary">
        Mode Pemeliharaan Aktif
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/pengaturan-toko') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>