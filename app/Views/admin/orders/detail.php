<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php helper('order'); ?>
<?php $order = $order ?? []; ?>

<div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Detail Pesanan</h1>
        <p class="section-subtitle">Pantau detail transaksi, status pembayaran, dan aksi penyelesaian.</p>
    </div>
    <span class="badge w-fit <?= order_status_badge_class($order['status']) ?>"><?= esc(order_status_label($order['status'])) ?></span>
</div>

<div class="grid gap-6 lg:grid-cols-2">
    <section class="panel-surface">
        <h2 class="font-display text-base font-semibold tracking-tight text-neutral-900">Informasi Pesanan</h2>
        <dl class="mt-5 space-y-4 text-sm">
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">No. Invoice</dt><dd class="font-mono font-medium text-neutral-900"><?= esc($order['invoice_number']) ?></dd></div>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Produk</dt><dd class="font-medium text-neutral-900"><?= esc($order['product_name_snapshot']) ?></dd></div>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Nominal</dt><dd class="text-neutral-700"><?= esc($order['nominal_snapshot']) ?></dd></div>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">ID Akun Game</dt><dd class="font-mono font-medium text-primary"><?= esc($order['game_id']) ?></dd></div>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">No. WhatsApp</dt><dd class="text-neutral-700"><?= esc($order['whatsapp_number']) ?></dd></div>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Harga Produk</dt><dd class="font-medium text-neutral-900">Rp<?= number_format((float) $order['price_snapshot'], 0, ',', '.') ?></dd></div>
            <?php if ((float) $order['discount_amount'] > 0): ?>
                <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3 text-success"><dt class="text-neutral-500">Diskon Voucher</dt><dd class="font-medium">-Rp<?= number_format((float) $order['discount_amount'], 0, ',', '.') ?></dd></div>
            <?php endif; ?>
           <div class="flex items-center justify-between gap-4 pt-1"><dt class="text-sm font-semibold text-neutral-900">Total Pembayaran</dt><dd class="text-base font-semibold text-primary">Rp<?= number_format((float) $order['total_amount'], 0, ',', '.') ?></dd></div>
        <?php if (!empty($order['payment_qr_image_path']) && $order['payment_channel_type'] === 'qris'): ?>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3">
                <dt class="text-neutral-500">QRIS</dt>
                <dd>
                    <img src="<?= base_url($order['payment_qr_image_path']) ?>" alt="QRIS" class="cursor-pointer h-24 w-24 object-contain rounded border" id="qr-image-<?= $order['id'] ?>">
                </dd>
            </div>
        <?php endif; ?>
    </dl>
    </section>

    <section class="panel-surface">
        <h2 class="font-display text-base font-semibold tracking-tight text-neutral-900">Status & Aksi</h2>
        <dl class="mt-5 space-y-4 text-sm">
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Status</dt><dd><span class="badge <?= order_status_badge_class($order['status']) ?>"><?= esc(order_status_label($order['status'])) ?></span></dd></div>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Dibuat pada</dt><dd class="text-neutral-700"><?= esc(date('d/m/Y H:i', strtotime($order['created_at']))) ?></dd></div>
            <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Diperbarui pada</dt><dd class="text-neutral-700"><?= esc(date('d/m/Y H:i', strtotime($order['updated_at']))) ?></dd></div>
            <?php if ($order['completed_at']): ?>
                <div class="flex items-center justify-between gap-4 border-b border-neutral-100 pb-3"><dt class="text-neutral-500">Diselesaikan pada</dt><dd class="font-medium text-success"><?= esc(date('d/m/Y H:i', strtotime($order['completed_at']))) ?></dd></div>
            <?php endif; ?>
        </dl>

        <?php if (! empty($order['payment_proof_path'])): ?>
            <a href="<?= base_url('admin/pesanan/' . $order['id'] . '/bukti') ?>" class="btn btn-secondary mt-6 w-full justify-center">Lihat Bukti Pembayaran</a>
        <?php endif; ?>
        <?php if ($order['status'] === 'menunggu_verifikasi'): ?>
            <div class="mt-6 flex gap-2">
                <form method="post" action="<?= base_url('admin/pesanan/' . $order['id'] . '/verifikasi') ?>" class="flex-1"><?= csrf_field() ?><button class="btn btn-primary w-full justify-center">Verifikasi</button></form>
                <form method="post" action="<?= base_url('admin/pesanan/' . $order['id'] . '/tolak') ?>" class="flex-1 space-y-2"><?= csrf_field() ?><input name="reason" required maxlength="500" placeholder="Alasan penolakan" class="form-input"><button class="btn btn-secondary w-full justify-center">Tolak</button></form>
            </div>
        <?php elseif ($order['status'] === 'diproses'): ?>
            <form method="post" action="<?= base_url('admin/pesanan/' . $order['id'] . '/selesai') ?>" class="mt-6" data-confirm="Tandai pesanan ini sebagai selesai dan kirim notifikasi WhatsApp ke customer?" data-confirm-title="Selesaikan Pesanan" data-confirm-button="Ya, selesaikan">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-primary w-full justify-center">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Tandai Selesai & Kirim Notif WA
                </button>
            </form>
            <p class="mt-3 text-center text-xs text-neutral-500">Pastikan item sudah dikirim sebelum menyelesaikan pesanan.</p>
        <?php elseif ($order['status'] === 'selesai'): ?>
            <div class="mt-6 rounded-lg border border-success/20 bg-success/10 px-4 py-3 text-center text-sm font-medium text-success">Pesanan telah selesai diproses</div>
        <?php else: ?>
            <div class="mt-6 rounded-lg border border-neutral-200 bg-neutral-50 px-4 py-3 text-center text-sm text-neutral-500">Tidak ada aksi tersedia untuk status saat ini.</div>
        <?php endif; ?>
    </section>

    <?php if (!empty($order['payment_qr_image_path']) && $order['payment_channel_type'] === 'qris'): ?>
        <div id="qr-modal-<?= $order['id'] ?>" class="qr-modal hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-lg p-4 max-w-xs w-full relative">
                <span class="absolute top-2 right-2 text-gray-500 cursor-pointer hover:text-gray-700" id="close-qr-<?= $order['id'] ?>">&times;</span>
                <img src="<?= base_url($order['payment_qr_image_path']) ?>" alt="QRIS Large" class="w-full h-auto rounded">
            </div>
        </div>
        <script>
            document.getElementById('qr-image-<?= $order['id'] ?>').addEventListener('click', function() {
                document.getElementById('qr-modal-<?= $order['id'] ?>').classList.remove('hidden');
            });
            document.getElementById('close-qr-<?= $order['id'] ?>').addEventListener('click', function() {
                document.getElementById('qr-modal-<?= $order['id'] ?>').classList.add('hidden');
            });
            // Close when clicking outside the image
            document.getElementById('qr-modal-<?= $order['id'] ?>').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        </script>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
