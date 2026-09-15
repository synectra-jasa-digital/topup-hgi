<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $channel = $channel ?? null; $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <h1 class="section-title"><?= $channel ? 'Ubah' : 'Tambah' ?> Metode Pembayaran</h1>
    <p class="section-subtitle">Atur rekening bank atau QRIS yang tampil di halaman checkout &amp; invoice.</p>
</div>

<form method="post" action="<?= $channel ? base_url('admin/metode-bayar/' . $channel['id'] . '/ubah') : base_url('admin/metode-bayar/tambah') ?>" enctype="multipart/form-data" class="space-y-5 panel-surface">
    <?= csrf_field() ?>
    <div>
        <label for="type" class="form-label">Tipe</label>
        <select id="type" name="type" class="form-input" onchange="document.getElementById('bank-fields').classList.toggle('hidden', this.value!=='bank'); document.getElementById('qris-fields').classList.toggle('hidden', this.value!=='qris');">
            <option value="bank" <?= old('type', $channel['type'] ?? 'bank') === 'bank' ? 'selected' : '' ?>>Transfer Bank</option>
            <option value="qris" <?= old('type', $channel['type'] ?? '') === 'qris' ? 'selected' : '' ?>>QRIS</option>
        </select>
    </div>
    <div>
        <label for="name" class="form-label">Nama Tampilan</label>
        <input type="text" id="name" name="name" value="<?= esc(old('name', $channel['name'] ?? '')) ?>" class="<?= isset($errors['name']) ? 'form-input-error' : 'form-input' ?>" placeholder="BCA / QRIS Toko" required>
        <?php if (isset($errors['name'])): ?><p class="form-error"><?= esc($errors['name']) ?></p><?php endif; ?>
    </div>

    <div id="bank-fields" class="space-y-5 <?= ($channel['type'] ?? 'bank') === 'qris' ? 'hidden' : '' ?>">
        <div>
            <label for="account_number" class="form-label">Nomor Rekening</label>
            <input type="text" id="account_number" name="account_number" value="<?= esc(old('account_number', $channel['account_number'] ?? '')) ?>" class="<?= isset($errors['account_number']) ? 'form-input-error' : 'form-input' ?>">
            <?php if (isset($errors['account_number'])): ?><p class="form-error"><?= esc($errors['account_number']) ?></p><?php endif; ?>
        </div>
        <div>
            <label for="account_holder" class="form-label">Nama Pemilik Rekening</label>
            <input type="text" id="account_holder" name="account_holder" value="<?= esc(old('account_holder', $channel['account_holder'] ?? '')) ?>" class="<?= isset($errors['account_holder']) ? 'form-input-error' : 'form-input' ?>">
            <?php if (isset($errors['account_holder'])): ?><p class="form-error"><?= esc($errors['account_holder']) ?></p><?php endif; ?>
        </div>
    </div>

    <div id="qris-fields" class="space-y-3 <?= ($channel['type'] ?? 'bank') === 'qris' ? '' : 'hidden' ?>">
        <label for="qr_image" class="form-label">Gambar QR</label>
        <?php if (! empty($channel['qr_image_path'])): ?>
            <img src="<?= base_url($channel['qr_image_path']) ?>" alt="QR saat ini" class="mb-2 h-32 w-32 rounded-lg border border-neutral-200 object-contain">
        <?php endif; ?>
        <input type="file" id="qr_image" name="qr_image" accept="image/png,image/jpeg,image/webp" class="<?= isset($errors['qr_image']) ? 'form-input-error' : 'form-input' ?>">
        <?php if (isset($errors['qr_image'])): ?><p class="form-error"><?= esc($errors['qr_image']) ?></p><?php endif; ?>
        <p class="form-help">PNG/JPG/WEBP, maks 2MB. Kosongkan saat ubah data jika tidak mengganti gambar.</p>
    </div>

    <div>
        <label for="sort_order" class="form-label">Urutan Tampil</label>
        <input type="number" id="sort_order" name="sort_order" value="<?= esc(old('sort_order', $channel['sort_order'] ?? 0)) ?>" class="form-input">
    </div>
    <label class="flex items-center gap-2.5 text-sm text-neutral-700">
        <input type="checkbox" id="is_active" name="is_active" value="1" <?= (old('is_active', $channel['is_active'] ?? 1)) ? 'checked' : '' ?> class="h-4 w-4 rounded border-neutral-300 accent-primary">
        Aktif
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/metode-bayar') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
