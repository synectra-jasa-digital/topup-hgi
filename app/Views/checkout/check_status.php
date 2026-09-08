<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php
  $adminWhatsapp = (new \App\Models\StoreSettingModel())->getVal('store_contact', (string) (getenv('wablas.adminPhone') ?: ''));
  $waNum = ! empty($adminWhatsapp) ? preg_replace('/[^0-9]/', '', $adminWhatsapp) : '';
  $waUrl = ! empty($waNum) ? 'https://wa.me/' . $waNum : 'https://wa.me/';
?>

<div class="max-w-md mx-auto px-4 sm:px-6 py-10 sm:py-16 space-y-6">

    <!-- Header Title -->
    <div class="text-center space-y-1.5">
        <h1 class="text-2xl font-display font-bold text-neutral-900">Lacak Status Pesanan</h1>
        <p class="text-xs text-slate-500 max-w-xs mx-auto">Masukkan nomor invoice transaksi Anda untuk memeriksa status pembayaran &amp; pengiriman.</p>
    </div>
    
    <!-- Clean Simple Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm space-y-5">
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-3 rounded-xl text-xs font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-rose-600 shrink-0">error</span>
                <span><?= esc(session()->getFlashdata('error')) ?></span>
            </div>
        <?php endif ?>

        <form action="<?= base_url('cek-pesanan') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>
            
            <div class="space-y-1.5">
                <label for="invoice_number" class="block text-xs font-bold text-neutral-800">Nomor Invoice <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input type="text" name="invoice_number" id="invoice_number" value="<?= old('invoice_number') ?>" 
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 px-3.5 text-sm font-mono font-bold uppercase tracking-wider text-neutral-900 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all placeholder:font-normal placeholder:normal-case placeholder:text-slate-400 <?= session('errors.invoice_number') ? 'border-rose-500' : '' ?>" 
                        placeholder="Contoh: INV20260906ABCDEF" required autocomplete="off">
                </div>
                <?php if (session('errors.invoice_number')) : ?>
                    <p class="text-rose-600 text-xs mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span> <?= esc(session('errors.invoice_number')) ?></p>
                <?php endif ?>
            </div>

            <button type="submit" class="w-full py-3 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-display font-bold text-sm shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer border border-blue-700">
                <span class="material-symbols-outlined text-[18px]">search</span>
                <span>Cek Status Pesanan</span>
            </button>
        </form>

        <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-500 leading-relaxed">
            Lupa nomor invoice? Kode transaksi telah dikirim ke WhatsApp Anda. <a href="<?= esc($waUrl) ?>" target="_blank" rel="noopener noreferrer" class="font-bold text-blue-600 hover:underline inline-flex items-center gap-0.5">Bantuan CS <span class="material-symbols-outlined text-[13px]">open_in_new</span></a>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

