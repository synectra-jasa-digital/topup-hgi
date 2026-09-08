<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $announcement = $announcement ?? null; $errors = session()->getFlashdata('errors') ?? []; ?>

<div>
    <h1 class="section-title"><?= $announcement ? 'Ubah' : 'Tambah' ?> Info Berjalan</h1>
    <p class="section-subtitle">Tulis pesan singkat yang akan tampil bergulir di ticker halaman utama.</p>
</div>

<form method="post" action="<?= $announcement ? base_url('admin/info-berjalan/' . $announcement['id'] . '/ubah') : base_url('admin/info-berjalan/tambah') ?>" class="space-y-5 panel-surface">
    <?= csrf_field() ?>
    <div>
        <label for="message" class="form-label">Pesan</label>
        <input type="text" id="message" name="message" value="<?= esc(old('message', $announcement['message'] ?? '')) ?>" class="<?= isset($errors['message']) ? 'form-input-error' : 'form-input' ?>" placeholder="🔥 Kode Promo Hemat: Gunakan voucher AYONGHEMAT untuk potongan Rp5.000!" required>
        <?php if (isset($errors['message'])): ?><p class="form-error"><?= esc($errors['message']) ?></p><?php endif; ?>
        <p class="form-help">Bisa diawali emoji langsung dari keyboard (Windows: Win+.). Maksimum 255 karakter.</p>
    </div>
    <div>
        <label for="sort_order" class="form-label">Urutan Tampil</label>
        <input type="number" id="sort_order" name="sort_order" value="<?= esc(old('sort_order', $announcement['sort_order'] ?? 0)) ?>" class="form-input">
    </div>
    <label class="flex items-center gap-2.5 text-sm text-neutral-700">
        <input type="checkbox" id="is_active" name="is_active" value="1" <?= (old('is_active', $announcement['is_active'] ?? 1)) ? 'checked' : '' ?> class="h-4 w-4 rounded border-neutral-300 accent-primary">
        Aktif
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= base_url('admin/info-berjalan') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>
<?= $this->endSection() ?>
