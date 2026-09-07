<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="max-w-xl mx-auto space-y-6">

    <!-- Header Invoice Card -->
    <div class="bg-surface-white border border-neutral-200 rounded-2xl p-6 shadow-sm space-y-5 font-inter">
        
        <div class="flex items-center justify-between border-b border-neutral-100 pb-4">
            <div>
                <span class="text-xs text-neutral-400 font-semibold uppercase tracking-wider block">Status Pesanan</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold mt-1 <?= order_status_badge_class($order['status']) ?>">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    <?= esc(order_status_label($order['status'])) ?>
                </span>
            </div>
            <div class="text-right">
                <span class="text-xs text-neutral-400 block">No. Invoice</span>
                <span class="font-bold text-sm text-on-surface font-mono"><?= esc($order['invoice_number']) ?></span>
            </div>
        </div>

        <!-- Rincian Transaksi -->
        <div class="space-y-3 text-sm">
            <div class="flex justify-between py-1">
                <span class="text-neutral-500">Produk</span>
                <span class="font-bold text-on-surface text-right"><?= esc($order['product_name_snapshot']) ?></span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-neutral-500">Nominal</span>
                <span class="font-semibold text-on-surface"><?= esc($order['nominal_snapshot']) ?></span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-neutral-500">ID Akun Game</span>
                <span class="font-bold text-primary font-mono"><?= esc($order['game_id']) ?></span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-neutral-500">Nomor WhatsApp</span>
                <span class="font-medium text-on-surface"><?= esc($order['whatsapp_number']) ?></span>
            </div>

            <?php if ((float) $order['discount_amount'] > 0): ?>
                <div class="flex justify-between py-1 text-success">
                    <span>Diskon Voucher</span>
                    <span class="font-bold">-Rp<?= number_format((float) $order['discount_amount'], 0, ',', '.') ?></span>
                </div>
            <?php endif; ?>

            <div class="border-t border-dashed border-neutral-200 pt-3 flex justify-between items-center">
                <span class="font-bold text-base text-on-surface">Total Pembayaran</span>
                <span class="text-xl font-extrabold text-primary font-inter">Rp<?= number_format((float) $order['total_amount'], 0, ',', '.') ?></span>
            </div>
        </div>

        <!-- Tombol Pembayaran Midtrans Snap -->
        <?php if ($order['status'] === 'menunggu_pembayaran' && !empty($order['snap_token'])): ?>
            <div class="pt-2 space-y-2">
                <button id="pay-button" class="w-full bg-primary hover:bg-primary-dark text-white font-bold text-base py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center gap-2 active:scale-[0.99]">
                    <span class="material-symbols-outlined text-[20px]">payments</span>
                    <span>Bayar Sekarang via Midtrans</span>
                </button>
                <p class="text-xs text-center text-neutral-400">Klik tombol di atas untuk memilih metode pembayaran (QRIS, E-Wallet, VA, dll).</p>
            </div>
        <?php endif; ?>

        <!-- CS Help Button -->
        <div class="border-t border-neutral-100 pt-4 text-center space-y-2">
            <p class="text-xs text-neutral-500">Ada kendala dengan pesanan ini? Hubungi Customer Service kami:</p>
            <a href="https://wa.me/?text=Halo%20CS%20Ayong%20Store,%20saya%20butuh%20bantuan%20pesanan%20invoice%20<?= urlencode($order['invoice_number']) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-success/10 text-success font-bold text-xs hover:bg-success/20 transition-colors">
                <span class="material-symbols-outlined text-[16px]">chat</span>
                <span>Hubungi CS WhatsApp</span>
            </a>
        </div>

    </div>

    <div class="text-center">
        <a href="<?= base_url('cek-pesanan') ?>" class="text-xs font-semibold text-primary hover:underline inline-flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">search</span>
            <span>Cek Status Pesanan Lainnya</span>
        </a>
    </div>

</div>

<?php if ($order['status'] === 'menunggu_pembayaran' && !empty($order['snap_token'])): ?>
<?php 
    $midtransIsProd = filter_var(getenv('midtrans.isProduction') ?: $_ENV['midtrans.isProduction'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $snapJsUrl = $midtransIsProd ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
    $clientKey = getenv('midtrans.clientKey') ?: $_ENV['midtrans.clientKey'] ?? '';
?>
<script type="text/javascript" src="<?= $snapJsUrl ?>" data-client-key="<?= $clientKey ?>"></script>
<script type="text/javascript">
    const payBtn = document.getElementById('pay-button');
    if (payBtn) {
        payBtn.onclick = function(){
            snap.pay('<?= esc($order['snap_token']) ?>', {
                onSuccess: function(result){
                    window.location.reload();
                },
                onPending: function(result){
                    window.location.reload();
                },
                onError: function(result){
                    alert("Pembayaran Gagal!");
                }
            });
        };
    }
</script>
<?php endif; ?>

<?= $this->endSection() ?>

