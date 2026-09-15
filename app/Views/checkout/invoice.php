<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php
    $invoiceStoreSettings = new \App\Models\StoreSettingModel();
    $invoiceStoreName = $invoiceStoreSettings->getVal('store_name', 'Ayong Store');
    $invoiceStoreContact = $invoiceStoreSettings->getVal('store_contact', '');
    $invoicePrintedAt = ! empty($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : date('d/m/Y H:i');
?>

<!-- Print-only receipt: everything else on this page is hidden when printing (see style below) -->
<style>
    @media print {
        @page { size: 80mm auto; margin: 3mm; }
        html, body { background: #fff !important; }
        #print-receipt { width: 100%; padding: 0; }
    }
</style>
<div id="print-receipt" class="hidden print:block font-mono text-[11px] text-black leading-snug">
    <div class="text-center">
        <div class="font-bold text-sm uppercase"><?= esc($invoiceStoreName) ?></div>
    </div>
    <div class="border-t border-dashed border-black my-1.5"></div>
    <div class="flex justify-between"><span>No. Invoice</span><span><?= esc($order['invoice_number']) ?></span></div>
    <div class="flex justify-between"><span>Tanggal</span><span><?= esc($invoicePrintedAt) ?></span></div>
    <div class="flex justify-between"><span>Status</span><span><?= esc(order_status_label($order['status'])) ?></span></div>
    <div class="border-t border-dashed border-black my-1.5"></div>
    <div class="flex justify-between"><span>Produk</span><span class="text-right"><?= esc($order['product_name_snapshot']) ?></span></div>
    <div class="flex justify-between"><span>Nominal</span><span><?= esc($order['nominal_snapshot']) ?></span></div>
    <div class="flex justify-between"><span>ID Akun Game</span><span><?= esc($masked_game_id ?? $order['game_id']) ?></span></div>
    <div class="flex justify-between"><span>No. WhatsApp</span><span><?= esc($masked_whatsapp ?? $order['whatsapp_number']) ?></span></div>
    <?php if ((float) $order['discount_amount'] > 0): ?>
        <div class="flex justify-between"><span>Diskon Voucher</span><span>-Rp<?= number_format((float) $order['discount_amount'], 0, ',', '.') ?></span></div>
    <?php endif; ?>
    <div class="border-t border-dashed border-black my-1.5"></div>
    <div class="flex justify-between font-bold text-sm"><span>TOTAL</span><span>Rp<?= number_format((float) $order['total_amount'], 0, ',', '.') ?></span></div>
    <div class="border-t border-dashed border-black my-1.5"></div>
    <div class="text-center">
        <div>Terima kasih telah bertransaksi!</div>
        <div>Simpan invoice ini sebagai bukti transaksi.</div>
    </div>
</div>

<div class="max-w-xl mx-auto px-4 sm:px-6 py-8 sm:py-12 space-y-6 print:hidden">

    <!-- Invoice Card -->
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-5">
        
        <!-- Header status & invoice number -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Status Pesanan</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold mt-1 <?= order_status_badge_class($order['status']) ?>">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    <?= esc(order_status_label($order['status'])) ?>
                </span>
            </div>
            <div class="text-right">
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">No. Invoice</span>
                <span class="font-mono font-bold text-sm text-neutral-900 select-all"><?= esc($order['invoice_number']) ?></span>
            </div>
        </div>

        <!-- Rincian Transaksi -->
        <div class="space-y-3 text-xs sm:text-sm">
            <div class="flex justify-between items-center py-1">
                <span class="text-slate-500">Produk</span>
                <span class="font-bold text-neutral-900 text-right"><?= esc($order['product_name_snapshot']) ?></span>
            </div>
            <div class="flex justify-between items-center py-1 border-t border-slate-100">
                <span class="text-slate-500">Nominal / Varian</span>
                <span class="font-semibold text-neutral-800"><?= esc($order['nominal_snapshot']) ?></span>
            </div>
            <div class="flex justify-between items-center py-1 border-t border-slate-100">
                <span class="text-slate-500">ID Akun Game</span>
                <span class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100"><?= esc($masked_game_id ?? $order['game_id']) ?></span>
            </div>
            <div class="flex justify-between items-center py-1 border-t border-slate-100">
                <span class="text-slate-500">Nomor WhatsApp</span>
                <span class="font-mono font-medium text-neutral-800"><?= esc($masked_whatsapp ?? $order['whatsapp_number']) ?></span>
            </div>

            <?php if ((float) $order['discount_amount'] > 0): ?>
                <div class="flex justify-between items-center py-1 border-t border-slate-100 text-emerald-600 font-bold">
                    <span>Diskon Voucher</span>
                    <span class="font-mono">-Rp<?= number_format((float) $order['discount_amount'], 0, ',', '.') ?></span>
                </div>
            <?php endif; ?>

            <div class="border-t border-dashed border-slate-200 pt-3 flex justify-between items-center">
                <span class="font-bold text-neutral-900 text-sm">Total Pembayaran</span>
                <span class="text-xl font-extrabold text-blue-600 font-display">Rp<?= number_format((float) $order['total_amount'], 0, ',', '.') ?></span>
            </div>
        </div>

        <?php if (! empty($order['payment_channel_name'])): ?>
            <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 space-y-2 text-sm">
                <h2 class="font-bold text-blue-900">Transfer Pembayaran</h2>
                <p class="font-semibold text-blue-900"><?= esc($order['payment_channel_name']) ?></p>
                <?php if ($order['payment_channel_type'] === 'qris' && ! empty($order['payment_qr_image_path'])): ?>
                    <img src="<?= base_url($order['payment_qr_image_path']) ?>" alt="QRIS <?= esc($order['payment_channel_name']) ?>" class="mx-auto h-48 w-48 rounded-lg bg-white object-contain p-2">
                <?php else: ?>
                    <p class="text-blue-800">Nomor rekening: <strong><?= esc($order['payment_account_number']) ?></strong></p>
                    <p class="text-blue-800">Atas nama: <strong><?= esc($order['payment_account_holder']) ?></strong></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error') || session()->getFlashdata('success')): ?>
            <div class="rounded-xl <?= session()->getFlashdata('error') ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' ?> p-3 text-sm"><?= esc(session()->getFlashdata('error') ?: session()->getFlashdata('success')) ?></div>
        <?php endif; ?>

        <?php if (! empty($order['payment_rejection_reason'])): ?>
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><strong>Bukti pembayaran ditolak:</strong> <?= esc($order['payment_rejection_reason']) ?></div>
        <?php endif; ?>

        <?php if ($order['status'] === 'menunggu_pembayaran'): ?>
            <form method="post" action="<?= base_url('pesanan/' . $order['invoice_number'] . '/bukti') ?>" enctype="multipart/form-data" class="space-y-3 rounded-xl border border-slate-200 p-4">
                <?= csrf_field() ?><input type="hidden" name="access_token" value="<?= esc($access_token) ?>">
                <label for="payment_proof" class="block text-sm font-bold text-neutral-800">Upload Bukti Pembayaran</label>
                <input type="file" name="payment_proof" id="payment_proof" accept="image/png,image/jpeg,image/webp" required class="form-input">
                <p class="text-xs text-slate-500">PNG/JPG/WEBP, maksimal 5MB.</p>
                <button type="submit" class="w-full rounded-xl bg-blue-600 py-3 font-bold text-white">Kirim Bukti Pembayaran</button>
            </form>
        <?php elseif ($order['status'] === 'menunggu_verifikasi'): ?>
            <div class="rounded-xl bg-amber-50 p-4 text-center text-sm font-medium text-amber-800">Bukti pembayaran sedang diperiksa admin.</div>
        <?php endif; ?>

        <!-- Tombol Cetak Invoice -->
        <button type="button" onclick="window.print()" class="w-full py-3 px-5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-display font-bold text-sm shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer border border-slate-300">
            <span class="material-symbols-outlined text-[18px]">print</span>
            <span>Cetak Invoice</span>
        </button>

        <!-- CS Help Section -->
        <div class="pt-3 text-center border-t border-slate-100 text-xs text-slate-500">
            Ada kendala dengan pesanan ini? <a href="https://wa.me/?text=Halo%20CS,%20saya%20butuh%20bantuan%20pesanan%20invoice%20<?= urlencode($order['invoice_number']) ?>" target="_blank" rel="noopener noreferrer" class="font-bold text-blue-600 hover:underline">Hubungi CS WhatsApp</a>
        </div>

    </div>

    <!-- Back link -->
    <div class="text-center">
        <a href="<?= base_url('cek-pesanan') ?>" class="text-xs font-semibold text-slate-600 hover:text-blue-600 inline-flex items-center gap-1 transition-colors">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Kembali ke Lacak Pesanan</span>
        </a>
    </div>

</div>

<?= $this->endSection() ?>
