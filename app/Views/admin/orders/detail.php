<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php helper('order'); ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="<?= base_url('admin/pesanan') ?>" class="text-sm text-primary hover:underline flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali ke Daftar Pesanan
        </a>
        <h1 class="text-2xl font-bold text-neutral-900 mt-1">Detail Pesanan</h1>
    </div>
    <?php if (session()->getFlashdata('warning')): ?>
        <div class="rounded-xl bg-warning/10 text-warning border border-warning/20 px-4 py-2 text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">warning</span>
            <span><?= esc(session()->getFlashdata('warning')) ?></span>
        </div>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Info Pesanan -->
    <div class="card p-6 space-y-4">
        <h2 class="font-bold text-on-surface">Informasi Pesanan</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between border-b border-neutral-100 pb-2">
                <span class="text-neutral-500">No. Invoice</span>
                <span class="font-mono font-bold"><?= esc($order['invoice_number']) ?></span>
            </div>
            <div class="flex justify-between border-b border-neutral-100 pb-2">
                <span class="text-neutral-500">Produk</span>
                <span class="font-semibold"><?= esc($order['product_name_snapshot']) ?></span>
            </div>
            <div class="flex justify-between border-b border-neutral-100 pb-2">
                <span class="text-neutral-500">Nominal</span>
                <span><?= esc($order['nominal_snapshot']) ?></span>
            </div>
            <div class="flex justify-between border-b border-neutral-100 pb-2">
                <span class="text-neutral-500">ID Akun Game</span>
                <span class="font-mono font-bold text-primary"><?= esc($order['game_id']) ?></span>
            </div>
            <div class="flex justify-between border-b border-neutral-100 pb-2">
                <span class="text-neutral-500">No. WhatsApp</span>
                <span><?= esc($order['whatsapp_number']) ?></span>
            </div>
            <div class="flex justify-between border-b border-neutral-100 pb-2">
                <span class="text-neutral-500">Harga Produk</span>
                <span>Rp<?= number_format((float)$order['price_snapshot'], 0, ',', '.') ?></span>
            </div>
            <?php if ((float)$order['discount_amount'] > 0): ?>
            <div class="flex justify-between border-b border-neutral-100 pb-2 text-success">
                <span>Diskon Voucher</span>
                <span class="font-bold">-Rp<?= number_format((float)$order['discount_amount'], 0, ',', '.') ?></span>
            </div>
            <?php endif; ?>
            <div class="flex justify-between pt-1">
                <span class="font-bold text-on-surface">Total Pembayaran</span>
                <span class="font-extrabold text-primary text-base">Rp<?= number_format((float)$order['total_amount'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>

    <!-- Status & Aksi -->
    <div class="card p-6 space-y-4">
        <h2 class="font-bold text-on-surface">Status & Aksi</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between border-b border-neutral-100 pb-2 items-center">
                <span class="text-neutral-500">Status</span>
                <span class="badge <?= order_status_badge_class($order['status']) ?>"><?= esc(order_status_label($order['status'])) ?></span>
            </div>
            <div class="flex justify-between border-b border-neutral-100 pb-2">
                <span class="text-neutral-500">Dibuat pada</span>
                <span><?= esc(date('d/m/Y H:i', strtotime($order['created_at']))) ?></span>
            </div>
            <div class="flex justify-between border-b border-neutral-100 pb-2">
                <span class="text-neutral-500">Diperbarui pada</span>
                <span><?= esc(date('d/m/Y H:i', strtotime($order['updated_at']))) ?></span>
            </div>
            <?php if ($order['completed_at']): ?>
            <div class="flex justify-between border-b border-neutral-100 pb-2">
                <span class="text-neutral-500">Diselesaikan pada</span>
                <span class="text-success font-semibold"><?= esc(date('d/m/Y H:i', strtotime($order['completed_at']))) ?></span>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($order['status'] === 'diproses'): ?>
            <form method="post" action="<?= base_url('admin/pesanan/' . $order['id'] . '/selesai') ?>"
                  onsubmit="return confirm('Tandai pesanan ini sebagai Selesai dan kirim notifikasi WhatsApp ke customer?')">
                <?= csrf_field() ?>
                <button type="submit" class="w-full btn btn-primary flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Tandai Selesai & Kirim Notif WA
                </button>
            </form>
            <p class="text-xs text-neutral-400 text-center">Pastikan chip sudah dikirim ke akun game customer sebelum menekan tombol ini.</p>
        <?php elseif ($order['status'] === 'selesai'): ?>
            <div class="text-center py-3 text-success font-semibold flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">task_alt</span>
                Pesanan telah selesai diproses
            </div>
        <?php else: ?>
            <div class="text-center py-3 text-neutral-400 text-sm">
                Tidak ada aksi tersedia untuk status saat ini.
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
