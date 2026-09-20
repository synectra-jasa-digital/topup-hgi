<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php
    $ring = 'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600';
    $note = $order['status'] === 'menunggu_pembayaran'
        ? 'Pesanan ini menunggu pembayaran. Rekening pembayaran dan unggah bukti hanya ada di tautan invoice pribadi yang Anda terima saat membuat pesanan.'
        : order_status_note($order['status']);
    $csUrl = whatsapp_url(
        (new \App\Models\StoreSettingModel())->getVal('store_contact', (string) (getenv('wablas.adminPhone') ?: '')),
        'Halo CS, saya ingin menanyakan pesanan ' . $order['invoice_number']
    );
?>

<!-- Public status page: shows where the order is and nothing personal (no game ID, phone, or payment details). -->
<div class="mx-auto max-w-xl px-4 py-8 sm:px-6 sm:py-12">
    <div class="rise-stagger space-y-5">

        <?= $this->include('checkout/partials/status_card') ?>

        <section class="space-y-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="status-note-title">
            <h2 id="status-note-title" class="font-display text-lg font-bold text-slate-950">Kabar terbaru</h2>
            <p class="text-sm leading-relaxed text-slate-700"><?= esc($note) ?></p>
            <p class="rounded-xl bg-slate-50 p-3.5 text-sm leading-relaxed text-slate-600">
                <span class="font-semibold text-slate-800"><?= esc($order['product_name_snapshot']) ?></span><br>
                Untuk alasan privasi, halaman ini hanya menampilkan status. Detail pesanan lengkap ada di tautan invoice pribadi Anda.
            </p>
        </section>

        <div class="space-y-4 text-center">
            <p class="text-sm text-slate-600">
                <?php if ($csUrl !== ''): ?>Ada pertanyaan tentang pesanan ini? <a href="<?= esc($csUrl) ?>" target="_blank" rel="noopener noreferrer" class="font-semibold text-blue-700 underline underline-offset-2 hover:text-blue-800 <?= $ring ?>">Hubungi CS WhatsApp</a><?php else: ?>Ada pertanyaan tentang pesanan ini? Simpan nomor invoice Anda dan hubungi admin toko.<?php endif; ?>
            </p>
            <a href="<?= base_url('cek-pesanan') ?>" class="inline-flex min-h-11 items-center gap-1 rounded-lg px-3 text-sm font-semibold text-slate-700 transition-colors hover:text-blue-700 <?= $ring ?>">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span>
                <span>Cek pesanan lain</span>
            </a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
