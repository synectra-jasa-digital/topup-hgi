<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $req = $bongkarRequest; ?>

<div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Detail Pengajuan Bongkar</h1>
        <p class="section-subtitle">Tinjau data pengajuan dan perbarui status prosesnya.</p>
    </div>
    <span class="badge w-fit <?= bongkar_status_badge_class($req['status']) ?>"><?= esc(bongkar_status_label($req['status'])) ?></span>
</div>

<div class="grid gap-6 lg:grid-cols-2">
    <section class="panel-surface">
        <h2 class="font-display text-base font-semibold tracking-tight text-neutral-900">Informasi Pengajuan</h2>
        <dl class="mt-5 space-y-4 text-sm">
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">No. Pengajuan</dt><dd class="font-mono font-medium text-neutral-900"><?= esc($req['request_number']) ?></dd></div>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Item</dt><dd class="font-medium text-neutral-900"><?= esc($req['catalog_name_snapshot']) ?></dd></div>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Kode Item</dt><dd class="font-mono text-neutral-700"><?= esc($req['catalog_code_snapshot']) ?></dd></div>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Jumlah</dt><dd class="text-neutral-700"><?= esc($req['quantity']) ?> <?= esc($req['unit_label_snapshot']) ?></dd></div>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Rate per Satuan</dt><dd class="text-neutral-700">Rp<?= number_format((float) $req['rate_snapshot'], 0, ',', '.') ?></dd></div>
            <div class="flex items-center justify-between gap-4 pt-1"><dt class="text-sm font-semibold text-neutral-900">Estimasi Payout</dt><dd class="text-base font-semibold text-primary">Rp<?= number_format((float) $req['estimated_amount'], 0, ',', '.') ?></dd></div>
        </dl>
    </section>

    <section class="panel-surface">
        <h2 class="font-display text-base font-semibold tracking-tight text-neutral-900">Kontak & Pencairan</h2>
        <dl class="mt-5 space-y-4 text-sm">
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">No. WhatsApp</dt><dd class="font-mono font-medium text-primary"><?= esc($req['customer_whatsapp']) ?></dd></div>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Metode Pencairan</dt><dd class="text-neutral-700"><?= esc($req['payout_method']) ?></dd></div>
            <?php if (! empty($req['customer_note'])): ?>
                <div class="border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Catatan Customer</dt><dd class="mt-1.5 text-neutral-700"><?= nl2br(esc($req['customer_note'])) ?></dd></div>
            <?php endif; ?>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Diajukan pada</dt><dd class="text-neutral-700"><?= esc(date('d/m/Y H:i', strtotime($req['created_at']))) ?></dd></div>
            <div class="flex items-center justify-between gap-4"><dt class="text-neutral-500">Diperbarui pada</dt><dd class="text-neutral-700"><?= esc(date('d/m/Y H:i', strtotime($req['updated_at']))) ?></dd></div>
        </dl>

        <form method="post" action="<?= base_url('admin/bongkar-pesanan/' . $req['id'] . '/status') ?>" class="mt-6 flex flex-col gap-3 sm:flex-row" data-confirm="Ubah status pengajuan ini?" data-confirm-title="Ubah Status" data-confirm-button="Ya, ubah">
            <?= csrf_field() ?>
            <select name="status" class="form-input sm:flex-1">
                <?php foreach (\App\Models\BongkarRequestModel::adminStatuses() as $s): ?>
                    <option value="<?= esc($s) ?>" <?= $s === $req['status'] ? 'selected' : '' ?>><?= esc(bongkar_status_label($s)) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary shrink-0">
                <span class="material-symbols-outlined text-[18px]">sync</span>
                Perbarui Status
            </button>
        </form>

        <a href="<?= 'https://wa.me/' . preg_replace('/\D+/', '', (str_starts_with($req['customer_whatsapp'], '0') ? '62' . substr($req['customer_whatsapp'], 1) : $req['customer_whatsapp'])) ?>" target="_blank" rel="noopener" class="btn btn-secondary mt-3 w-full justify-center">
            <span class="material-symbols-outlined text-[18px]">chat</span>
            Hubungi via WhatsApp
        </a>
    </section>
</div>
<?= $this->endSection() ?>
