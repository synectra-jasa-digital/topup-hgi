<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="text-2xl font-bold text-neutral-900 mb-6">Pengaturan Toko</h1>

<form method="post" action="<?= base_url('admin/pengaturan-toko') ?>" class="card p-6 max-w-2xl space-y-4">
    <?= csrf_field() ?>

    <div>
        <label for="store_name" class="form-label">Nama Toko</label>
        <input type="text" id="store_name" name="store_name" value="<?= esc(old('store_name', $settings['store_name'] ?? '')) ?>"
               class="form-input" required>
    </div>

    <div>
        <label for="store_contact" class="form-label">Kontak Toko (WhatsApp / Telepon)</label>
        <input type="text" id="store_contact" name="store_contact" value="<?= esc(old('store_contact', $settings['store_contact'] ?? '')) ?>"
               class="form-input">
    </div>

    <div>
        <label for="store_address" class="form-label">Alamat Toko</label>
        <textarea id="store_address" name="store_address" rows="3"
                  class="form-input"><?= esc(old('store_address', $settings['store_address'] ?? '')) ?></textarea>
    </div>

    <div>
        <label for="midtrans_key" class="form-label">Kunci Midtrans</label>
        <input type="text" id="midtrans_key" name="midtrans_key" value="<?= esc(old('midtrans_key', $settings['midtrans_key'] ?? '')) ?>"
               class="form-input">
    </div>

    <div>
        <label for="wablas_key" class="form-label">Token / Kunci Wablas</label>
        <input type="text" id="wablas_key" name="wablas_key" value="<?= esc(old('wablas_key', $settings['wablas_key'] ?? '')) ?>"
               class="form-input">
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" id="maintenance" name="maintenance" value="1"
               <?= (old('maintenance', $settings['maintenance'] ?? '0') === '1') ? 'checked' : '' ?>>
        <label for="maintenance" class="text-sm text-neutral-900">Mode Pemeliharaan Aktif</label>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/pengaturan-toko') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
