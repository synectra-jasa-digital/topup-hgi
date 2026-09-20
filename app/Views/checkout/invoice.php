<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php
    $invoiceStoreSettings = new \App\Models\StoreSettingModel();
    $invoiceStoreName = $invoiceStoreSettings->getVal('store_name', 'Ayong Store');
    $invoicePrintedAt = ! empty($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : date('d/m/Y H:i');

    $status = (string) $order['status'];
    $ring = 'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600';

    $flashMessages = array_values(array_filter(array_merge(
        [session()->getFlashdata('error')],
        array_values((array) session()->getFlashdata('errors'))
    )));
    $flashSuccess = session()->getFlashdata('success');

    $totalRaw   = (int) round((float) $order['total_amount']);
    $totalLabel = 'Rp' . number_format($totalRaw, 0, ',', '.');
    $invoiceUrl = base_url('pesanan/' . $order['invoice_number']) . '?token=' . $access_token;
    $csUrl      = whatsapp_url(
        $invoiceStoreSettings->getVal('store_contact', (string) (getenv('wablas.adminPhone') ?: '')),
        'Halo CS, saya butuh bantuan pesanan invoice ' . $order['invoice_number']
    );
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

<div class="mx-auto max-w-xl px-4 py-8 sm:px-6 sm:py-12 print:hidden">
    <div class="rise-stagger space-y-5">

        <!-- Status, invoice number and where the order is in the process -->
        <?= $this->include('checkout/partials/status_card') ?>

        <!-- What to do now, depending on the status -->
        <section class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="next-step-title">
            <h2 id="next-step-title" class="font-display text-lg font-bold text-slate-950">
                <?= $status === 'menunggu_pembayaran' ? 'Langkah berikutnya: bayar' : 'Kabar terbaru' ?>
            </h2>

            <?php if ($flashMessages !== []): ?>
                <div class="flex items-start gap-2.5 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-sm text-rose-900" role="alert">
                    <span class="material-symbols-outlined shrink-0 text-[20px] text-rose-700" aria-hidden="true">error</span>
                    <ul class="space-y-0.5">
                        <?php foreach ($flashMessages as $message): ?><li><?= esc($message) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif ($flashSuccess): ?>
                <div class="flex items-start gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-sm text-emerald-900" role="status">
                    <span class="material-symbols-outlined shrink-0 text-[20px] text-emerald-700" aria-hidden="true">check_circle</span>
                    <span><?= esc($flashSuccess) ?></span>
                </div>
            <?php endif; ?>

            <?php if (! empty($order['payment_rejection_reason'])): ?>
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900" role="alert">
                    <strong>Bukti pembayaran ditolak:</strong> <?= esc($order['payment_rejection_reason']) ?>
                </div>
            <?php endif; ?>

            <?php if ($status === 'menunggu_pembayaran'): ?>
                <?php if (! empty($order['payment_channel_name'])): ?>
                    <div class="space-y-3 rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm">
                        <p class="font-bold text-blue-950">Bayar lewat <?= esc($order['payment_channel_name']) ?></p>

                        <?php if ($order['payment_channel_type'] === 'qris' && ! empty($order['payment_qr_image_path'])): ?>
                            <img src="<?= base_url($order['payment_qr_image_path']) ?>" alt="Kode QRIS <?= esc($order['payment_channel_name']) ?>" class="mx-auto h-52 w-52 rounded-lg bg-white object-contain p-2">
                        <?php else: ?>
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="block text-slate-700">Nomor rekening</span>
                                    <strong class="block break-all font-mono text-base text-blue-950"><?= esc($order['payment_account_number']) ?></strong>
                                </div>
                                <button type="button" data-copy="<?= esc((string) $order['payment_account_number'], 'attr') ?>" class="inline-flex min-h-11 shrink-0 cursor-pointer items-center gap-1.5 rounded-lg border border-blue-300 bg-white px-3.5 text-sm font-semibold text-blue-800 transition-colors hover:bg-blue-100 active:scale-95 <?= $ring ?>">
                                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true" data-copy-icon>content_copy</span><span data-copy-label>Salin</span>
                                </button>
                            </div>
                            <p class="text-slate-700">Atas nama: <strong class="text-blue-950"><?= esc($order['payment_account_holder']) ?></strong></p>
                        <?php endif; ?>

                        <div class="flex items-center justify-between gap-3 border-t border-blue-200 pt-3">
                            <div>
                                <span class="block text-slate-700">Nominal yang dibayar</span>
                                <strong class="block font-display text-xl font-extrabold tabular-nums text-blue-800"><?= esc($totalLabel) ?></strong>
                            </div>
                            <button type="button" data-copy="<?= $totalRaw ?>" class="inline-flex min-h-11 shrink-0 cursor-pointer items-center gap-1.5 rounded-lg border border-blue-300 bg-white px-3.5 text-sm font-semibold text-blue-800 transition-colors hover:bg-blue-100 active:scale-95 <?= $ring ?>">
                                <span class="material-symbols-outlined text-[18px]" aria-hidden="true" data-copy-icon>content_copy</span><span data-copy-label>Salin</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('pesanan/' . $order['invoice_number'] . '/bukti') ?>" enctype="multipart/form-data" class="space-y-3 rounded-xl border border-slate-200 p-4" id="proof-form">
                    <?= csrf_field() ?><input type="hidden" name="access_token" value="<?= esc($access_token) ?>">
                    <label for="payment_proof" class="block text-sm font-bold text-slate-950">Unggah bukti pembayaran</label>
                    <input type="file" name="payment_proof" id="payment_proof" accept="image/png,image/jpeg,image/webp" required aria-describedby="proof-hint proof-error"
                           class="block w-full cursor-pointer rounded-xl border border-slate-300 bg-slate-50 text-sm text-slate-700 file:mr-4 file:cursor-pointer file:border-0 file:bg-blue-600 file:px-4 file:py-3 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700 <?= $ring ?>">
                    <p id="proof-hint" class="text-sm text-slate-600">Format PNG, JPG, atau WEBP, maksimal 5 MB.</p>
                    <p id="proof-error" class="hidden rounded-lg bg-rose-50 p-2.5 text-sm font-medium text-rose-900" role="alert"></p>
                    <img id="proof-preview" alt="Pratinjau bukti pembayaran" class="hidden max-h-56 w-auto rounded-lg border border-slate-200">
                    <button type="submit" id="proof-submit" class="flex min-h-12 w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-blue-600 py-3 font-display text-sm font-bold text-white shadow-sm transition-all hover:bg-blue-700 active:scale-[0.99] disabled:cursor-wait disabled:opacity-70 <?= $ring ?>">
                        <span class="material-symbols-outlined text-[20px]" aria-hidden="true" id="proof-submit-icon">upload</span>
                        <span id="proof-submit-label">Kirim Bukti Pembayaran</span>
                    </button>
                </form>

            <?php else: ?>
                <p class="text-sm leading-relaxed text-slate-700"><?= esc(order_status_note($status)) ?></p>
            <?php endif; ?>
        </section>

        <!-- What was ordered -->
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="order-detail-title">
            <h2 id="order-detail-title" class="font-display text-lg font-bold text-slate-950">Rincian Pesanan</h2>
            <dl class="mt-3 text-sm">
                <div class="flex items-center justify-between gap-4 py-2.5">
                    <dt class="text-slate-600">Produk</dt>
                    <dd class="text-right font-bold text-slate-950"><?= esc($order['product_name_snapshot']) ?></dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-t border-slate-100 py-2.5">
                    <dt class="text-slate-600">Nominal / Varian</dt>
                    <dd class="font-semibold text-slate-900"><?= esc($order['nominal_snapshot']) ?></dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-t border-slate-100 py-2.5">
                    <dt class="text-slate-600">ID Akun Game</dt>
                    <dd class="rounded border border-blue-100 bg-blue-50 px-2 py-0.5 font-mono font-bold text-blue-800"><?= esc($masked_game_id ?? $order['game_id']) ?></dd>
                </div>
                <div class="flex items-center justify-between gap-4 border-t border-slate-100 py-2.5">
                    <dt class="text-slate-600">Nomor WhatsApp</dt>
                    <dd class="font-mono font-medium text-slate-900"><?= esc($masked_whatsapp ?? $order['whatsapp_number']) ?></dd>
                </div>
                <?php if ((float) $order['discount_amount'] > 0): ?>
                    <div class="flex items-center justify-between gap-4 border-t border-slate-100 py-2.5 font-bold text-emerald-700">
                        <dt>Diskon Voucher</dt>
                        <dd class="font-mono">-Rp<?= number_format((float) $order['discount_amount'], 0, ',', '.') ?></dd>
                    </div>
                <?php endif; ?>
                <div class="flex items-center justify-between gap-4 border-t border-dashed border-slate-300 pt-3.5">
                    <dt class="font-bold text-slate-950">Total Pembayaran</dt>
                    <dd class="font-display text-xl font-extrabold tabular-nums text-blue-700"><?= esc($totalLabel) ?></dd>
                </div>
            </dl>
        </section>

        <!-- Keep access: the private link is the only way back to this full invoice -->
        <section class="space-y-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="access-title">
            <h2 id="access-title" class="font-display text-lg font-bold text-slate-950">Simpan tautan invoice</h2>
            <p class="text-sm leading-relaxed text-slate-700">Tautan ini satu-satunya cara membuka invoice lengkap ini lagi (rekening pembayaran dan unggah bukti). Simpan dan jangan bagikan. Untuk sekadar melihat status, cukup nomor invoice di halaman <a class="font-semibold text-blue-700 underline underline-offset-2 hover:text-blue-800 <?= $ring ?>" href="<?= base_url('cek-pesanan') ?>">Cek Pesanan</a>.</p>
            <div class="flex flex-col gap-2 sm:flex-row">
                <button type="button" data-copy="<?= esc($invoiceUrl, 'attr') ?>" class="inline-flex min-h-11 flex-1 cursor-pointer items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-800 transition-colors hover:bg-slate-50 active:scale-95 <?= $ring ?>">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true" data-copy-icon>link</span><span data-copy-label>Salin tautan invoice</span>
                </button>
                <button type="button" data-copy="<?= esc($order['invoice_number'], 'attr') ?>" class="inline-flex min-h-11 flex-1 cursor-pointer items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-800 transition-colors hover:bg-slate-50 active:scale-95 <?= $ring ?>">
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true" data-copy-icon>tag</span><span data-copy-label>Salin nomor invoice</span>
                </button>
            </div>
            <p id="copy-status" class="sr-only" role="status" aria-live="polite"></p>
        </section>

        <!-- Actions -->
        <div class="space-y-4 text-center">
            <button type="button" id="print-invoice" class="flex min-h-12 w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white py-3 font-display text-sm font-bold text-slate-800 shadow-xs transition-all hover:bg-slate-50 active:scale-[0.99] <?= $ring ?>">
                <span class="material-symbols-outlined text-[20px]" aria-hidden="true">print</span>
                <span>Cetak Invoice</span>
            </button>

            <p class="text-sm text-slate-600">
                <?php if ($csUrl !== ''): ?>Ada kendala dengan pesanan ini? <a href="<?= esc($csUrl) ?>" target="_blank" rel="noopener noreferrer" class="font-semibold text-blue-700 underline underline-offset-2 hover:text-blue-800 <?= $ring ?>">Hubungi CS WhatsApp</a><?php else: ?>Ada kendala dengan pesanan ini? Simpan nomor invoice Anda dan hubungi admin toko.<?php endif; ?>
            </p>

            <a href="<?= base_url('cek-pesanan') ?>" class="inline-flex min-h-11 items-center gap-1 rounded-lg px-3 text-sm font-semibold text-slate-700 transition-colors hover:text-blue-700 <?= $ring ?>">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span>
                <span>Kembali ke Cek Pesanan</span>
            </a>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var live = document.getElementById('copy-status');

        function copyText(text) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                return navigator.clipboard.writeText(text).then(function () { return true; }, function () { return fallbackCopy(text); });
            }
            return Promise.resolve(fallbackCopy(text));
        }

        function fallbackCopy(text) {
            var area = document.createElement('textarea');
            area.value = text;
            area.setAttribute('readonly', '');
            area.style.position = 'fixed';
            area.style.opacity = '0';
            document.body.appendChild(area);
            area.select();
            var ok = false;
            try { ok = document.execCommand('copy'); } catch (e) { ok = false; }
            area.remove();
            return ok;
        }

        // Copy buttons: the label says what happened, and a live region says it for screen readers.
        document.querySelectorAll('[data-copy]').forEach(function (button) {
            var label = button.querySelector('[data-copy-label]');
            var icon = button.querySelector('[data-copy-icon]');
            var original = { label: label.textContent, icon: icon.textContent };
            var timer = null;

            button.addEventListener('click', function () {
                copyText(button.dataset.copy).then(function (ok) {
                    label.textContent = ok ? 'Tersalin' : 'Gagal, salin manual';
                    icon.textContent = ok ? 'check' : 'error';
                    if (live) live.textContent = ok ? 'Tersalin ke papan klip.' : 'Gagal menyalin. Salin secara manual.';
                    clearTimeout(timer);
                    timer = setTimeout(function () { label.textContent = original.label; icon.textContent = original.icon; }, 2000);
                });
            });
        });

        document.getElementById('print-invoice').addEventListener('click', function () { window.print(); });

        // Payment proof: check type and size before uploading, show what was chosen, lock the button while sending.
        var form = document.getElementById('proof-form');
        if (!form) return;
        var input = document.getElementById('payment_proof');
        var error = document.getElementById('proof-error');
        var preview = document.getElementById('proof-preview');
        var submit = document.getElementById('proof-submit');
        var submitLabel = document.getElementById('proof-submit-label');
        var submitIcon = document.getElementById('proof-submit-icon');
        var allowed = ['image/jpeg', 'image/png', 'image/webp'];
        var maxBytes = 5 * 1024 * 1024;

        function showError(message) {
            error.textContent = message;
            error.classList.remove('hidden');
            preview.classList.add('hidden');
            input.value = '';
            input.setAttribute('aria-invalid', 'true');
        }

        input.addEventListener('change', function () {
            error.classList.add('hidden');
            input.removeAttribute('aria-invalid');
            var file = input.files && input.files[0];
            if (!file) { preview.classList.add('hidden'); return; }
            if (allowed.indexOf(file.type) === -1) { showError('Format file tidak didukung. Pilih gambar PNG, JPG, atau WEBP.'); return; }
            if (file.size > maxBytes) { showError('Ukuran file terlalu besar. Maksimal 5 MB.'); return; }
            var url = URL.createObjectURL(file);
            preview.onload = function () { URL.revokeObjectURL(url); };
            preview.src = url;
            preview.classList.remove('hidden');
        });

        form.addEventListener('submit', function () {
            if (!input.files || !input.files.length) return;
            submit.disabled = true;
            submitLabel.textContent = 'Mengunggah...';
            submitIcon.textContent = 'progress_activity';
            submitIcon.classList.add('animate-spin');
        });

        window.addEventListener('pageshow', function () { // back button restores the page from cache
            submit.disabled = false;
            submitLabel.textContent = 'Kirim Bukti Pembayaran';
            submitIcon.textContent = 'upload';
            submitIcon.classList.remove('animate-spin');
        });
    });
</script>

<?= $this->endSection() ?>
