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
            <div class="mt-4">
                <div class="mb-2 flex items-center justify-between gap-3">
                    <label for="proof-trigger-<?= $order['id'] ?>" class="text-xs font-semibold uppercase tracking-wider text-neutral-500">Bukti Pembayaran</label>
                    <span class="text-[11px] text-neutral-400">Klik gambar untuk melihat</span>
                </div>
                <div id="proof-trigger-<?= $order['id'] ?>" class="group cursor-pointer overflow-hidden rounded-lg border border-neutral-200 bg-neutral-50 p-2 text-center transition hover:border-primary/50 focus:outline-none focus:ring-2 focus:ring-primary/30" role="button" tabindex="0" aria-label="Lihat bukti pembayaran">
                    <img src="<?= base_url('admin/pesanan/' . $order['id'] . '/preview-bukti') ?>" alt="Bukti Pembayaran" id="proof-image-<?= $order['id'] ?>" class="mx-auto max-h-44 w-full rounded-md object-contain" />
                </div>
            </div>
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
</div>

<?= $this->section('modals') ?>
    <?php if (! empty($order['payment_proof_path'])): ?>
        <div id="proof-modal-<?= $order['id'] ?>" class="hidden fixed inset-0 z-[10000] flex items-center justify-center bg-white/60 p-4 backdrop-blur-xl" style="-webkit-backdrop-filter: blur(24px); backdrop-filter: blur(24px);" role="dialog" aria-modal="true" aria-labelledby="proof-modal-title-<?= $order['id'] ?>">
            <div class="relative flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-neutral-100 px-4 py-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-primary">receipt_long</span>
                        <span id="proof-modal-title-<?= $order['id'] ?>" class="font-display text-sm font-semibold text-neutral-900">Bukti Pembayaran</span>
                    </div>
                    <button type="button" id="proof-close-<?= $order['id'] ?>" class="rounded-md p-1.5 text-neutral-400 transition hover:bg-neutral-100 hover:text-neutral-700" aria-label="Tutup bukti pembayaran">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
                <div class="flex min-h-0 items-center justify-center overflow-auto bg-neutral-50 p-3 sm:p-5">
                    <img src="<?= base_url('admin/pesanan/' . $order['id'] . '/preview-bukti') ?>" alt="Bukti Pembayaran" class="max-h-[75vh] max-w-full object-contain">
                </div>
            </div>
        </div>
        <script>
            (function() {
                const trigger = document.getElementById('proof-trigger-<?= $order['id'] ?>');
                const img = document.getElementById('proof-image-<?= $order['id'] ?>');
                const modal = document.getElementById('proof-modal-<?= $order['id'] ?>');
                const closeBtn = document.getElementById('proof-close-<?= $order['id'] ?>');

                function openModal() {
                    if (modal) modal.classList.remove('hidden');
                }
                function closeModal() {
                    if (modal) modal.classList.add('hidden');
                }

                if (trigger) trigger.addEventListener('click', openModal);
                if (img) img.addEventListener('click', openModal);
                if (closeBtn) closeBtn.addEventListener('click', closeModal);

                if (trigger) trigger.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openModal();
                    }
                });

                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === modal) closeModal();
                    });
                }

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                        closeModal();
                    }
                });
            })();
        </script>
    <?php endif; ?>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
