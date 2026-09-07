<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<!-- HERO BANNER FRAME -->
<div class="max-w-[1360px] mx-auto px-4 sm:px-6 pt-5 pb-2">
<div class="relative rounded-2xl overflow-hidden border-2 border-amber-400/80 shadow-lg bg-slate-900">
<!-- Image Hero Frame -->
<div class="relative h-64 sm:h-80 md:h-96 lg:h-[380px] w-full overflow-hidden">
<img alt="Higgs Games Island Hero Banner" class="w-full h-full object-cover object-center transform hover:scale-105 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida/AEtjO1Xkmeor5HdJYUSrixZ-AzI0Nncuf7tYmyNxC1SwZdCDjOMU2BjepgpLXbad3fySmsQm7rP5nP-ptDLEIo2MMimWSzGcIHVkQSlrxiOr-zLVdB_OvX-VtyWrOJh0BvOZilFEiQ8h4Ck8egDDGp3p68c22YensCJWpq6l6pDVIJCn9oeXJMtojO-IKJOU47c-kgqr7XlYTNou8LADwr6yjDGsxmgUgT3SlDg5R8tjmBh1dHMjUbENtang-g">
<div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent"></div>
<div class="absolute inset-0 bg-gradient-to-r from-slate-950/85 via-transparent to-slate-950/60"></div>
<!-- Hero Badges & Content Overlay -->
<div class="absolute bottom-6 left-5 sm:bottom-8 sm:left-8 right-5 sm:right-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
<div class="space-y-3 max-w-2xl">
<div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-600 text-white text-xs font-semibold tracking-wide shadow-sm"><span class="material-symbols-outlined text-[15px]">verified</span> Top Up Resmi &amp; Terpercaya</div>
<h1 class="text-2xl sm:text-4xl md:text-5xl font-display font-black text-white drop-shadow-[0_2px_8px_rgba(0,0,0,0.8)]">Top Up Koin &amp; Produk Higgs Games Island</h1>
<p class="text-xs sm:text-sm md:text-base text-slate-200 font-normal leading-relaxed drop-shadow-md max-w-xl">Layanan top up koin emas, koin MD, dan kartu VIP resmi terpercaya dengan proses kilat tanpa login akun.</p>
</div>
<div class="hidden lg:flex flex-col items-end gap-1.5 bg-slate-900/80 backdrop-blur-md px-5 py-3.5 rounded-2xl border border-slate-700 text-right shadow-lg">
<div class="text-xs font-bold text-emerald-400 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span> Transaksi Otomatis</div>
<div class="text-[11px] text-slate-300 font-mono">Proses Kilat 1-3 Detik</div>
</div>
</div>
</div>
</div>
</div>

<!-- MAIN COCKPIT: SPLIT DECK GAMING FLOW -->
<main class="max-w-[1360px] mx-auto px-4 sm:px-6 py-6" id="katalog-section">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start">

<!-- LEFT COLUMN (7 Cols): The Cockpit Ordering Wizard (Step 1 -> Step 4) -->
<div class="lg:col-span-7 xl:col-span-7 space-y-6">

<!-- STEP 1: PILIH KATEGORI PRODUK -->
<section class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm relative overflow-hidden">
<div class="flex items-center justify-between mb-4">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-display font-black flex items-center justify-center text-base shadow-sm">1</div>
<div>
<h2 class="font-display font-bold text-base sm:text-lg text-neutral-900 leading-tight">Pilih Kategori Produk</h2>
<p class="text-xs text-slate-500">Daftar denominasi koin emas dan durasi kartu member resmi</p>
</div>
</div>
<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200"><span class="material-symbols-outlined text-[14px]">verified</span> Jalur Resmi ID</span>
</div>

<!-- Segmented Deck Tabs (Light Mode) -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-2 bg-slate-100/90 p-1.5 rounded-xl border border-slate-200" id="category-pills">
<button class="category-pill active flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs sm:text-sm font-bold transition-all bg-blue-600 text-white shadow-sm border border-blue-700" data-cat="koin-emas" type="button">
<span class="material-symbols-outlined text-[19px] text-yellow-300" style="font-variation-settings: 'FILL' 1;">monetization_on</span>
<span class="">Koin Emas</span>
</button>
<button class="category-pill flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs sm:text-sm font-semibold transition-all text-slate-700 hover:text-neutral-900 hover:bg-white" data-cat="kartu-emas" type="button">
<span class="material-symbols-outlined text-[19px] text-amber-600">credit_card</span>
<span class="">Kartu VIP</span>
</button>
<button class="category-pill flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs sm:text-sm font-semibold transition-all text-slate-700 hover:text-neutral-900 hover:bg-white" data-cat="koin-md" type="button">
<span class="material-symbols-outlined text-[19px] text-sky-600">diamond</span>
<span class="">Koin MD</span>
</button>
<button class="category-pill flex items-center justify-center gap-2 py-2.5 px-3 rounded-lg text-xs sm:text-sm font-semibold transition-all text-slate-700 hover:text-neutral-900 hover:bg-white" data-cat="kartu-ungu" type="button">
<span class="material-symbols-outlined text-[19px] text-purple-600">workspace_premium</span>
<span class="">Kartu Ungu</span>
</button>
</div>
</section>

<!-- STEP 2: PILIH NOMINAL PRODUK -->
<section class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm relative">
<div class="flex items-center justify-between mb-4">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-display font-black flex items-center justify-center text-base shadow-sm">2</div>
<div>
<h2 class="font-display font-bold text-base sm:text-lg text-neutral-900 leading-tight">Pilih Nominal Top Up HGD</h2>
<p class="text-xs text-slate-500">Pilih pecahan koin chip atau masa durasi VIP Domino</p>
</div>
</div>
<span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-300">
<span class="material-symbols-outlined text-[14px] text-amber-600">bolt</span> Flash 1 Detik
</span>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5" id="product-grid">
<?php if (! empty($sections)): ?>
    <?php foreach ($sections as $secIndex => $section): ?>
        <?php foreach ($section['products'] as $pIndex => $p): ?>
            <?php 
                $isDefaultActive = ($secIndex === 0 && $pIndex === 2) || ($secIndex === 0 && $pIndex === 0 && count($section['products']) < 3);
                $sellPrice = (float) $p['sell_price'];
                $priceFormatted = 'Rp' . number_format($sellPrice, 0, ',', '.');
            ?>
            <button class="product-card <?= $isDefaultActive ? 'active border-2 border-blue-600 bg-blue-50/70 ring-2 ring-blue-500/20' : 'border border-slate-200 bg-white hover:border-blue-400 hover:bg-blue-50/40' ?> group relative p-3.5 rounded-xl text-left shadow-xs transition-all focus:outline-none" data-id="<?= $p['id'] ?>" data-cat="<?= esc($section['category']['name']) ?>" data-price="<?= $sellPrice ?>" data-title="<?= esc($p['name']) ?>" data-unit="<?= esc($p['nominal'] ?: $priceFormatted) ?>" type="button">
                <?php if ($isDefaultActive): ?>
                    <span class="absolute -top-2.5 right-2 px-2 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wide shadow-xs">Terpopuler</span>
                <?php endif; ?>
                <div class="flex items-start justify-between">
                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-700 font-black text-[10px] border border-blue-200">HGD</div>
                    <span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded"><?= esc($section['category']['name']) ?></span>
                </div>
                <div class="mt-2 font-display font-bold text-neutral-900 text-base leading-snug"><?= esc($p['name']) ?></div>
                <div class="text-[11px] text-slate-500"><?= esc($p['nominal'] ?: 'Koin Emas Resmi') ?></div>
                <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-baseline justify-between">
                    <span class="text-xs font-bold text-blue-600 font-display"><?= $priceFormatted ?></span>
                    <span class="text-[10px] text-slate-500 font-mono">Resmi</span>
                </div>
            </button>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php else: ?>
    <!-- Default Mockup Products if Database is Fresh -->
    <button class="product-card group relative p-3.5 rounded-xl border border-slate-200 text-left bg-white hover:border-blue-400 hover:bg-blue-50/40 transition-all focus:outline-none shadow-xs" data-cat="Koin Emas" data-price="13000" data-title="200M Koin Emas HGD" data-unit="Rp65.000 / 1B" type="button">
    <div class="flex items-start justify-between"><div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-700 font-black text-[10px] border border-blue-200">HGD</div><span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded">Retail</span></div>
    <div class="mt-2 font-display font-bold text-neutral-900 text-base leading-snug">200M</div>
    <div class="text-[11px] text-slate-500">Koin Emas Resmi</div>
    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-baseline justify-between"><span class="text-xs font-bold text-blue-600 font-display">Rp13.000</span><span class="text-[10px] text-slate-500 font-mono">Rp65k/B</span></div>
    </button>
    
    <button class="product-card group relative p-3.5 rounded-xl border border-slate-200 text-left bg-white hover:border-blue-400 hover:bg-blue-50/40 transition-all focus:outline-none shadow-xs" data-cat="Koin Emas" data-price="25500" data-title="400M Koin Emas HGD" data-unit="Rp63.750 / 1B" type="button">
    <div class="flex items-start justify-between"><div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-700 font-black text-[10px] border border-blue-200">HGD</div><span class="text-[10px] font-semibold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">Hemat</span></div>
    <div class="mt-2 font-display font-bold text-neutral-900 text-base leading-snug">400M</div>
    <div class="text-[11px] text-slate-500">Koin Emas Resmi</div>
    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-baseline justify-between"><span class="text-xs font-bold text-blue-600 font-display">Rp25.500</span><span class="text-[10px] text-slate-500 font-mono">Rp63.7k/B</span></div>
    </button>
    
    <button class="product-card active group relative p-3.5 rounded-xl border-2 border-blue-600 bg-blue-50/70 text-left shadow-sm ring-2 ring-blue-500/20 transition-all focus:outline-none" data-cat="Koin Emas" data-price="63000" data-title="1B (1 Miliar) Koin Emas" data-unit="Rp63.000 / 1B" type="button">
    <span class="absolute -top-2.5 right-2 px-2 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wide shadow-xs">Terpopuler</span>
    <div class="flex items-start justify-between"><div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center text-neutral-950 font-black text-[10px] shadow-sm border border-amber-300">HGD</div><span class="text-[10px] font-bold text-blue-700 bg-white border border-blue-200 px-1.5 py-0.5 rounded">Paket Rekomendasi</span></div>
    <div class="mt-2 font-display font-bold text-blue-700 text-base leading-snug">1B (1 Miliar)</div>
    <div class="text-[11px] text-slate-600 font-medium">Koin Emas Resmi</div>
    <div class="mt-2.5 pt-2 border-t border-blue-200/60 flex items-baseline justify-between"><span class="text-xs font-bold text-blue-800 font-display">Rp63.000</span><span class="text-[10px] text-blue-700 font-mono font-bold">Rp63k/B</span></div>
    </button>
    
    <button class="product-card group relative p-3.5 rounded-xl border border-slate-200 text-left bg-white hover:border-blue-400 hover:bg-blue-50/40 transition-all focus:outline-none shadow-xs" data-cat="Koin Emas" data-price="125000" data-title="2B (2 Miliar) Koin Emas" data-unit="Rp62.500 / 1B" type="button">
    <span class="absolute -top-2.5 right-2 px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-wide shadow-xs">Diskon 4%</span>
    <div class="flex items-start justify-between"><div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-700 font-black text-[10px] border border-blue-200">HGD</div><span class="text-[10px] text-slate-400 line-through">Rp130k</span></div>
    <div class="mt-2 font-display font-bold text-neutral-900 text-base leading-snug">2B (2 Miliar)</div>
    <div class="text-[11px] text-slate-500">Koin Emas Resmi</div>
    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-baseline justify-between"><span class="text-xs font-bold text-blue-600 font-display">Rp125.000</span><span class="text-[10px] text-emerald-700 font-semibold font-mono">Rp62.5k/B</span></div>
    </button>
    
    <button class="product-card group relative p-3.5 rounded-xl border border-slate-200 text-left bg-white hover:border-blue-400 hover:bg-blue-50/40 transition-all focus:outline-none shadow-xs" data-cat="Koin Emas" data-price="310000" data-title="5B Koin Emas Sultan" data-unit="Rp62.000 / 1B" type="button">
    <div class="flex items-start justify-between"><div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-700 font-black text-[10px] border border-blue-200">HGD</div><span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">Paket Besar</span></div>
    <div class="mt-2 font-display font-bold text-neutral-900 text-base leading-snug">5B Koin</div>
    <div class="text-[11px] text-slate-500">Koin Emas Resmi</div>
    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-baseline justify-between"><span class="text-xs font-bold text-blue-600 font-display">Rp310.000</span><span class="text-[10px] text-emerald-700 font-semibold font-mono">Rp62k/B</span></div>
    </button>
    
    <button class="product-card group relative p-3.5 rounded-xl border border-slate-200 text-left bg-white hover:border-blue-400 hover:bg-blue-50/40 transition-all focus:outline-none shadow-xs" data-cat="Koin Emas" data-price="615000" data-title="10B Koin Emas VIP Max" data-unit="Rp61.500 / 1B" type="button">
    <span class="absolute -top-2.5 right-2 px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-wide shadow-xs">Paket Maksimal</span>
    <div class="flex items-start justify-between"><div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-700 font-black text-[10px] border border-blue-200">HGD</div><span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Ekstra Hemat</span></div>
    <div class="mt-2 font-display font-bold text-neutral-900 text-base leading-snug">10B Koin</div>
    <div class="text-[11px] text-slate-500">Koin Emas Resmi</div>
    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-baseline justify-between"><span class="text-xs font-bold text-blue-600 font-display">Rp615.000</span><span class="text-[10px] text-emerald-700 font-bold font-mono">Rp61.5k/B</span></div>
    </button>
    
    <button class="product-card group relative p-3.5 rounded-xl border border-slate-200 text-left bg-white hover:border-blue-400 hover:bg-blue-50/40 transition-all focus:outline-none shadow-xs" data-cat="Kartu Emas" data-price="15000" data-title="Kartu Emas VIP (1 Hari)" data-unit="Aktif 24 Jam" type="button">
    <div class="flex items-start justify-between"><span class="material-symbols-outlined text-amber-500 text-[24px]">workspace_premium</span><span class="text-[10px] font-medium text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded">1 Hari</span></div>
    <div class="mt-2 font-display font-bold text-neutral-900 text-base leading-snug">Kartu Emas 1H</div>
    <div class="text-[11px] text-slate-500">Durasi 1 Hari VIP</div>
    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-baseline justify-between"><span class="text-xs font-bold text-blue-600 font-display">Rp15.000</span><span class="text-[10px] text-slate-500 font-mono">24 Jam</span></div>
    </button>
    
    <button class="product-card group relative p-3.5 rounded-xl border border-slate-200 text-left bg-white hover:border-blue-400 hover:bg-blue-50/40 transition-all focus:outline-none shadow-xs" data-cat="Kartu Emas" data-price="120000" data-title="Kartu Emas VIP (30 Hari)" data-unit="Aktif 30 Hari" type="button">
    <div class="flex items-start justify-between"><span class="material-symbols-outlined text-amber-500 text-[24px]">workspace_premium</span><span class="text-[10px] font-bold text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-300">30 Hari</span></div>
    <div class="mt-2 font-display font-bold text-neutral-900 text-base leading-snug">Kartu Emas 30H</div>
    <div class="text-[11px] text-slate-500">Durasi 30 Hari VIP</div>
    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-baseline justify-between"><span class="text-xs font-bold text-blue-600 font-display">Rp120.000</span><span class="text-[10px] text-amber-700 font-mono">30 Hari</span></div>
    </button>
<?php endif; ?>
</div>
</section>

<!-- STEP 3: MASUKKAN DATA USER ID HIGGS & KONTAK -->
<section class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm">
<div class="flex items-center justify-between mb-4">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-display font-black flex items-center justify-center text-base shadow-sm">3</div>
<div><h2 class="font-display font-bold text-base sm:text-lg text-neutral-900 leading-tight">Data Akun &amp; Kontak</h2><p class="text-xs text-slate-500">ID akun untuk tujuan pengiriman koin &amp; konfirmasi pemesanan</p></div>
</div>
<span class="text-xs text-emerald-700 flex items-center gap-1 font-semibold">
<span class="material-symbols-outlined text-[16px]">verified_user</span> Tanpa Password
</span>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<!-- Input ID Game -->
<div class="space-y-1.5">
<label class="block text-xs font-bold text-neutral-700" for="input-user-id">
          User ID Higgs Games Island <span class="text-rose-500">*</span>
</label>
<div class="flex gap-2">
<div class="relative flex-1">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-slate-400">sports_esports</span>
<input class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all font-mono font-bold text-neutral-900 placeholder-slate-400" id="input-user-id" placeholder="Contoh: 123456789" type="text" value="123456789">
</div>
</div>
<div class="flex items-center justify-between pt-1">
<span class="text-[10px] text-slate-500 font-mono">8–10 digit</span>
</div>
</div>
<!-- Input WhatsApp -->
<div class="space-y-1.5">
<label class="block text-xs font-bold text-neutral-700" for="input-whatsapp">
          Nomor WhatsApp Pembeli <span class="text-rose-500">*</span>
</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-emerald-600">chat</span>
<input class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all font-mono font-bold text-neutral-900 placeholder-slate-400" id="input-whatsapp" placeholder="08xxxxxxxxxx" type="tel" value="081234567890">
</div>
<p class="text-[11px] text-slate-500 pt-1">Notifikasi bukti pengiriman chip &amp; invoice otomatis dikirimkan ke nomor WhatsApp ini.</p>
</div>
</div>
</section>

<!-- STEP 4: PILIH METODE PEMBAYARAN -->
<section class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm">
<div class="flex items-center justify-between mb-4">
<div class="flex items-center gap-3">
<div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-display font-black flex items-center justify-center text-base shadow-sm">4</div>
<div>
<h2 class="font-display font-bold text-base sm:text-lg text-neutral-900 leading-tight">Metode Pembayaran Resmi</h2>
<p class="text-xs text-slate-500">Pilihan saluran pembayaran resmi yang disiapkan untuk peluncuran</p>
</div>
</div>
<span class="text-xs font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-300 flex items-center gap-1">
<span class="material-symbols-outlined text-[14px] text-amber-600">flash_on</span> Konfirmasi 1 Detik
</span>
</div>
<!-- Group A: QRIS & E-Wallet -->
<div class="space-y-2 mb-4">
<div class="flex items-center justify-between text-[11px] font-extrabold uppercase tracking-wider text-slate-600">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[15px] text-blue-600">qr_code_scanner</span> QRIS &amp; E-Wallet (Bebas Biaya Admin)
</span>
<span class="text-emerald-700 font-mono font-bold">Biaya Rp0</span>
</div>
<div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
<!-- QRIS (Default Selected) -->
<button class="pay-method-card active p-3 rounded-xl border-2 border-blue-600 bg-blue-50/70 text-left transition-all relative ring-2 ring-blue-500/20 shadow-xs" data-badge="Bebas Biaya" data-fee="0" data-method="QRIS Resmi" type="button">
<span class="absolute top-1.5 right-1.5 px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-600 text-white">INSTAN</span>
<div class="text-xs font-black text-blue-700">QRIS Resmi</div>
<div class="text-[10px] text-slate-600 truncate">Semua Bank / e-Wallet</div>
<div class="mt-1.5 text-[11px] font-bold text-emerald-700">Rp0 Admin</div>
</button>
<!-- GoPay -->
<button class="pay-method-card p-3 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all relative shadow-xs" data-badge="Bebas Biaya" data-fee="0" data-method="GoPay" type="button">
<div class="text-xs font-bold text-sky-700">GoPay</div>
<div class="text-[10px] text-slate-500">Gojek App Instant</div>
<div class="mt-1.5 text-[11px] font-bold text-emerald-700">Rp0 Admin</div>
</button>
<!-- DANA -->
<button class="pay-method-card p-3 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all relative shadow-xs" data-badge="Bebas Biaya" data-fee="0" data-method="DANA" type="button">
<div class="text-xs font-bold text-blue-700">DANA</div>
<div class="text-[10px] text-slate-500">Dompet Digital</div>
<div class="mt-1.5 text-[11px] font-bold text-emerald-700">Rp0 Admin</div>
</button>
<!-- ShopeePay -->
<button class="pay-method-card p-3 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all relative shadow-xs" data-badge="Bebas Biaya" data-fee="0" data-method="ShopeePay" type="button">
<div class="text-xs font-bold text-orange-600">ShopeePay</div>
<div class="text-[10px] text-slate-500">SPay Deeplink</div>
<div class="mt-1.5 text-[11px] font-bold text-emerald-700">Rp0 Admin</div>
</button>
<!-- OVO -->
<button class="pay-method-card p-3 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all relative shadow-xs" data-badge="Bebas Biaya" data-fee="0" data-method="OVO" type="button">
<div class="text-xs font-bold text-purple-700">OVO Cash</div>
<div class="text-[10px] text-slate-500">Notifikasi Push</div>
<div class="mt-1.5 text-[11px] font-bold text-emerald-700">Rp0 Admin</div>
</button>
<!-- LinkAja -->
<button class="pay-method-card p-3 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all relative shadow-xs" data-badge="Bebas Biaya" data-fee="0" data-method="LinkAja" type="button">
<div class="text-xs font-bold text-red-600">LinkAja</div>
<div class="text-[10px] text-slate-500">Aplikasi LinkAja</div>
<div class="mt-1.5 text-[11px] font-bold text-emerald-700">Rp0 Admin</div>
</button>
</div>
</div>
<!-- Group B: Virtual Account -->
<div class="space-y-2 pt-3 border-t border-slate-200">
<div class="flex items-center justify-between text-[11px] font-extrabold uppercase tracking-wider text-slate-600">
<span class="flex items-center gap-1">
<span class="material-symbols-outlined text-[15px] text-blue-600">account_balance</span> Virtual Account Otomatis 24 Jam
</span>
<span class="text-slate-500 font-mono">+Rp1.000 VA Fee</span>
</div>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
<button class="pay-method-card p-2.5 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all shadow-xs" data-badge="+Rp1.000" data-fee="1000" data-method="BCA Virtual Account" type="button">
<div class="text-xs font-extrabold text-blue-700 font-display">BCA VA</div>
<div class="text-[10px] text-slate-500">m-BCA / KlikBCA</div>
<div class="mt-1 text-[10px] font-semibold text-slate-500">+Rp1.000</div>
</button>
<button class="pay-method-card p-2.5 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all shadow-xs" data-badge="+Rp1.000" data-fee="1000" data-method="Mandiri VA" type="button">
<div class="text-xs font-extrabold text-amber-700 font-display">MANDIRI</div>
<div class="text-[10px] text-slate-500">Livin Mandiri</div>
<div class="mt-1 text-[10px] font-semibold text-slate-500">+Rp1.000</div>
</button>
<button class="pay-method-card p-2.5 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all shadow-xs" data-badge="+Rp1.000" data-fee="1000" data-method="BRI Virtual Account" type="button">
<div class="text-xs font-extrabold text-sky-700 font-display">BRIVA</div>
<div class="text-[10px] text-slate-500">BRImo App</div>
<div class="mt-1 text-[10px] font-semibold text-slate-500">+Rp1.000</div>
</button>
<button class="pay-method-card p-2.5 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all shadow-xs" data-badge="+Rp1.000" data-fee="1000" data-method="BNI Virtual Account" type="button">
<div class="text-xs font-extrabold text-orange-600 font-display">BNI VA</div>
<div class="text-[10px] text-slate-500">BNI Mobile</div>
<div class="mt-1 text-[10px] font-semibold text-slate-500">+Rp1.000</div>
</button>
</div>
</div>
</section>

<!-- SECTION: JUAL ATAU BONGKAR KARTU -->
<section class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm relative overflow-hidden mt-6">
  <div class="flex items-start sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-200">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-display font-black flex items-center justify-center text-base shadow-sm shrink-0">
        <span class="material-symbols-outlined text-[20px]">currency_exchange</span>
      </div>
      <div>
        <div class="flex items-center gap-2 flex-wrap">
          <h2 class="font-display font-bold text-base sm:text-lg text-neutral-900 leading-tight">Jual atau Bongkar Kartu</h2>
          <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-300 text-[10px] font-black uppercase tracking-wider">Cair Instan</span>
        </div>
        <p class="text-xs text-slate-500 mt-0.5">Tukar kartu Higgs Games Island (Kartu Ungu, Kartu Emas, dan sejenisnya) menjadi saldo atau uang tunai langsung ke rekening/e-wallet.</p>
      </div>
    </div>
    <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full border border-blue-200 shrink-0">
      <span class="material-symbols-outlined text-[14px]">verified</span> Rate Terbaik
    </span>
  </div>

  <div class="space-y-4 text-xs">
    <div>
      <label class="block font-bold text-neutral-700 mb-2">Pilih Jenis Kartu / Koin yang Dijual <span class="text-rose-500">*</span></label>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
        <button type="button" class="bongkar-card selected p-3 rounded-xl border-2 border-blue-600 bg-blue-50/70 text-left cursor-pointer transition-all relative ring-2 ring-blue-500/20 shadow-xs" data-label="Kartu Ungu" data-rate="65000" data-unit="kartu">
          <span class="absolute top-1.5 right-1.5 px-1.5 py-0.5 rounded text-[9px] font-black bg-blue-600 text-white uppercase">Populer</span>
          <div class="font-display font-bold text-neutral-900 text-xs sm:text-sm">Kartu Ungu</div>
          <div class="text-[10px] text-slate-500 mt-0.5">Rate Tinggi Resmi</div>
          <div class="mt-2 pt-1.5 border-t border-blue-200/60 font-bold text-blue-700 font-mono text-[11px]">Rp65.000 / kartu</div>
        </button>
        <button type="button" class="bongkar-card p-3 rounded-xl border border-slate-200 bg-white hover:border-blue-400 hover:bg-slate-50 text-left cursor-pointer transition-all relative shadow-xs" data-label="Kartu Emas 1H" data-rate="14000" data-unit="kartu">
          <div class="font-display font-bold text-neutral-900 text-xs sm:text-sm">Kartu Emas 1H</div>
          <div class="text-[10px] text-slate-500 mt-0.5">Durasi 24 Jam VIP</div>
          <div class="mt-2 pt-1.5 border-t border-slate-100 font-bold text-blue-600 font-mono text-[11px]">Rp14.000 / kartu</div>
        </button>
        <button type="button" class="bongkar-card p-3 rounded-xl border border-slate-200 bg-white hover:border-blue-400 hover:bg-slate-50 text-left cursor-pointer transition-all relative shadow-xs" data-label="Kartu Emas 30H" data-rate="110000" data-unit="kartu">
          <span class="absolute top-1.5 right-1.5 px-1.5 py-0.5 rounded text-[9px] font-black bg-amber-500 text-neutral-950 uppercase">Best Rate</span>
          <div class="font-display font-bold text-neutral-900 text-xs sm:text-sm">Kartu Emas 30H</div>
          <div class="text-[10px] text-slate-500 mt-0.5">Durasi 30 Hari VIP</div>
          <div class="mt-2 pt-1.5 border-t border-slate-100 font-bold text-blue-600 font-mono text-[11px]">Rp110.000 / kartu</div>
        </button>
        <button type="button" class="bongkar-card p-3 rounded-xl border border-slate-200 bg-white hover:border-blue-400 hover:bg-slate-50 text-left cursor-pointer transition-all relative shadow-xs" data-label="Koin MD Bongkar" data-rate="60000" data-unit="1B">
          <div class="font-display font-bold text-neutral-900 text-xs sm:text-sm">Koin MD Bongkar</div>
          <div class="text-[10px] text-slate-500 mt-0.5">Pecahan Koin Chip</div>
          <div class="mt-2 pt-1.5 border-t border-slate-100 font-bold text-blue-600 font-mono text-[11px]">Rp60.000 / 1B</div>
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
      <div class="space-y-1.5">
        <label class="block font-bold text-neutral-700" for="input-card-qty">Jumlah Kartu / Nominal <span class="text-rose-500">*</span></label>
        <div class="flex items-center rounded-xl border border-slate-300 bg-slate-50 overflow-hidden focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-100 focus-within:bg-white transition-all">
          <button type="button" class="w-10 h-10 flex items-center justify-center text-slate-600 hover:bg-slate-200 text-base font-bold transition-colors">-</button>
          <input id="input-card-qty" type="number" min="1" value="1" class="flex-1 text-center py-2 text-sm bg-transparent outline-none font-mono font-bold text-neutral-900 border-none focus:ring-0">
          <button type="button" class="w-10 h-10 flex items-center justify-center text-slate-600 hover:bg-slate-200 text-base font-bold transition-colors">+</button>
        </div>
        <span class="text-[10px] text-slate-500 block">Bisa jual satuan maupun partai besar.</span>
      </div>

      <div class="space-y-1.5">
        <label class="block font-bold text-neutral-700" for="input-sell-wa">Nomor WhatsApp Penerima Saldo / Konfirmasi <span class="text-rose-500">*</span></label>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-emerald-600">chat</span>
          <input id="input-sell-wa" type="tel" placeholder="0812xxxxxxxx" value="081234567890" class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all font-mono font-bold text-neutral-900 placeholder-slate-400">
        </div>
        <span class="text-[10px] text-slate-500 block">Admin akan menghubungi nomor ini untuk verifikasi transfer koin &amp; bukti pencairan.</span>
      </div>
    </div>

    <div class="space-y-1.5 pt-1">
      <label class="block font-bold text-neutral-700">Pilihan Rekening / E-Wallet Pencairan Dana <span class="text-rose-500">*</span></label>
      <div class="grid grid-cols-3 sm:grid-cols-7 gap-2 text-center text-[10px] font-bold">
        <button type="button" class="py-2 px-1.5 rounded-lg border-2 border-blue-600 bg-blue-50/70 text-blue-700">BCA</button>
        <button type="button" class="py-2 px-1.5 rounded-lg border border-slate-200 bg-white hover:border-blue-400 text-slate-700">BRI</button>
        <button type="button" class="py-2 px-1.5 rounded-lg border border-slate-200 bg-white hover:border-blue-400 text-slate-700">Mandiri</button>
        <button type="button" class="py-2 px-1.5 rounded-lg border border-slate-200 bg-white hover:border-blue-400 text-slate-700">DANA</button>
        <button type="button" class="py-2 px-1.5 rounded-lg border border-slate-200 bg-white hover:border-blue-400 text-slate-700">GoPay</button>
        <button type="button" class="py-2 px-1.5 rounded-lg border border-slate-200 bg-white hover:border-blue-400 text-slate-700">OVO</button>
        <button type="button" class="py-2 px-1.5 rounded-lg border border-slate-200 bg-white hover:border-blue-400 text-slate-700">ShopeePay</button>
      </div>
    </div>

    <div class="p-3.5 rounded-xl bg-blue-50/60 border border-blue-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0">
          <span class="material-symbols-outlined text-[18px]">payments</span>
        </div>
        <div>
          <div class="text-[10px] uppercase font-bold text-slate-500">Estimasi Saldo Diterima</div>
          <div class="text-lg font-black text-blue-700 font-display"><span id="bongkar-estimated">Rp65.000</span> <span class="text-[11px] font-normal text-slate-500">(Bersih tanpa potongan)</span></div>
        </div>
      </div>
      <div class="flex items-center gap-1.5 text-[11px] text-emerald-700 bg-white px-2.5 py-1 rounded-lg border border-emerald-200 font-semibold">
        <span class="material-symbols-outlined text-[15px]">verified</span>
        <span class="" id="bongkar-status">Proses 1-5 Menit Langsung Masuk</span>
      </div>
    </div>

    <button type="button" id="btn-submit-bongkar" class="w-full py-3.5 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-display font-bold text-sm sm:text-base transition-all flex items-center justify-center gap-2 shadow-sm border border-blue-700 cursor-pointer">
      <span class="material-symbols-outlined text-[20px]">send</span>
      <span class="">Ajukan Jual Kartu</span>
    </button>

    <p class="text-[11px] text-slate-500 text-center pt-1">
      Pengajuan akan dibuka di WhatsApp admin setelah form diisi.
    </p>
  </div>
</section>
</div>

<!-- RIGHT COLUMN (5 Cols): STICKY LIVE ORDER CARD & VIP BENEFITS -->
<div class="lg:col-span-5 xl:col-span-5 lg:sticky lg:top-22 space-y-4">
<!-- Live Ticket Receipt Gaming Cockpit (Crisp White Card) -->
<div class="bg-white rounded-2xl border-2 border-slate-200 shadow-md overflow-hidden relative">
<!-- Gaming Ticket Header -->
<div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 p-4 text-white relative border-b border-blue-800">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2">
<div class="w-8 h-8 rounded-lg bg-white text-blue-700 flex items-center justify-center font-bold shadow-sm"><span class="material-symbols-outlined text-[20px]">receipt_long</span></div>
<div><div class="font-display font-bold text-sm tracking-wide text-white">Ringkasan Pesanan</div><div class="text-[10px] text-blue-100">Rincian Pembayaran Resmi</div></div>
</div>
<div class="flex flex-col items-end">
<span class="px-2.5 py-0.5 rounded-full bg-emerald-600 text-white font-mono text-[10px] font-bold uppercase tracking-wider shadow-xs">CHECKOUT AMAN</span>
<span class="text-[10px] text-emerald-300 font-medium mt-0.5 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Server Online</span>
</div>
</div>
</div>
<!-- Ticket Content -->
<div class="p-5 space-y-4 text-sm bg-white">
<!-- Selected Product Display Card -->
<div class="flex items-center gap-3.5 p-3 rounded-xl bg-slate-50 border border-slate-200 shadow-xs">
<div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 via-yellow-400 to-amber-500 flex flex-col items-center justify-center text-neutral-950 shrink-0 shadow-sm border border-amber-300">
<span class="text-[11px] font-black leading-none">HGD</span>
<span class="text-[9px] font-bold leading-none mt-0.5">GOLD</span>
</div>
<div class="min-w-0 flex-1">
<div class="text-[10px] font-black text-amber-600 uppercase tracking-wide">Higgs Games Island ID</div>
<div class="font-display font-extrabold text-neutral-900 text-sm truncate" id="receipt-item-name">1B (1 Miliar) Koin Emas</div>
<div class="text-[11px] text-slate-500" id="receipt-unit-rate">Rp63.000 / 1B</div>
</div>
<div class="text-right shrink-0">
<span class="text-xs font-black text-blue-600 font-display" id="receipt-item-price">Rp63.000</span>
</div>
</div>
<!-- Destination Account & Order Specs -->
<div class="space-y-2 py-3 border-y border-dashed border-slate-200 text-xs">
<div class="flex justify-between items-center">
<span class="text-slate-500">ID Higgs Tujuan:</span>
<span class="font-mono font-bold text-neutral-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200" id="receipt-user-id">123456789</span>
</div>
<div class="flex justify-between items-center">
<span class="text-slate-500">WhatsApp Notif:</span>
<span class="font-mono font-semibold text-neutral-800" id="receipt-wa">081234567890</span>
</div>
<div class="flex justify-between items-center">
<span class="text-slate-500">Metode Bayar:</span>
<span class="font-bold text-blue-700" id="receipt-method">QRIS Resmi</span>
</div>
</div>
<!-- Coupon Code Input -->
<div class="space-y-1.5">
<div class="flex items-center justify-between"><label class="block text-xs font-semibold text-neutral-700" for="receipt-promo-input">Kode Voucher Promo</label><span class="text-[10px] text-amber-600 font-bold">Gunakan: AYONGHEMAT</span></div>
<div class="flex gap-2">
<div class="relative flex-1"><span class="material-symbols-outlined absolute left-2.5 top-2 text-[16px] text-amber-500">local_activity</span><input class="w-full pl-8 pr-2 py-1.5 text-xs uppercase font-mono rounded-lg bg-slate-50 border border-slate-300 focus:border-blue-600 focus:bg-white outline-none text-neutral-900 placeholder-slate-400 font-bold" id="receipt-promo-input" placeholder="MASUKKAN KODE VOUCHER" type="text"></div>
<button class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-neutral-950 text-xs font-black transition-all shrink-0 shadow-xs border border-amber-400" id="btn-apply-coupon" type="button">Gunakan</button>
</div>
<div class="text-[11px] hidden" id="promo-status"></div>
</div>
<!-- Price Calculation Breakdown -->
<div class="space-y-1.5 pt-1 text-xs">
<div class="flex justify-between text-slate-600">
<span class="">Harga Produk Koin:</span>
<span class="font-bold text-neutral-900 font-mono" id="calc-subtotal">Rp63.000</span>
</div>
<div class="flex justify-between text-slate-600">
<span class="">Biaya Layanan Gateway:</span>
<span class="font-bold text-emerald-700 font-mono" id="calc-admin-fee">Rp0 (Gratis)</span>
</div>
<div class="flex justify-between text-rose-600 hidden" id="calc-discount-row">
<span class="flex items-center gap-1 font-bold">
<span class="material-symbols-outlined text-[14px]">discount</span> Diskon Kupon:
</span>
<span class="font-black font-mono" id="calc-discount-val">-Rp5.000</span>
</div>
</div>
<!-- Decorative Line -->
<div class="border-b-2 border-dotted border-slate-200 my-1"></div>
<!-- Grand Total Display -->
<div class="pt-1 flex items-end justify-between">
<div>
<span class="text-[10px] uppercase font-black tracking-wider text-slate-500 block">Total Pembayaran Netto</span>
<div class="text-2xl sm:text-3xl font-black text-blue-700 font-display flex items-baseline gap-1" id="calc-grand-total">Rp63.000</div>
</div>
<div class="text-right">
<span class="text-[10px] font-black text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-300 block">Garansi Masuk 100%</span>
<span class="text-[10px] text-slate-500 mt-0.5 block font-mono">Gateway BI Terlisensi</span>
</div>
</div>
<!-- Checkout Button -->
<button class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-display font-black text-base shadow-md transition-all flex items-center justify-center gap-2 group cursor-pointer border border-blue-700" id="btn-pay-now" type="button">
<span class="material-symbols-outlined text-[20px] text-amber-300">lock</span><span class="">Bayar Sekarang</span>
</button>
</div>
</div>
<!-- VIP Benefits Box (Clean Light) -->
<div class="rounded-2xl border border-slate-200 bg-white p-4 text-xs shadow-xs space-y-3">
<div class="flex items-center gap-2.5">
<div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shadow-xs border border-blue-200"><span class="material-symbols-outlined text-[20px]">verified</span></div>
<div><div class="font-display font-bold text-neutral-900 text-sm">Jaminan Layanan Ayong Store</div><div class="text-slate-500 text-[11px]">Standar keamanan &amp; keandalan transaksi terbaik</div></div>
</div>
<ul class="space-y-1.5 text-slate-600 text-[11px] pl-1">
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-blue-600">check_circle</span><span class="">Integrasi jalur API resmi langsung ke akun Higgs</span></li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-blue-600">check_circle</span><span class="">Harga transparan tanpa biaya tersembunyi</span></li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-blue-600">check_circle</span><span class="">Dukungan CS WhatsApp 24 jam siap melayani</span></li>
</ul>
<div class="pt-2 border-t border-slate-200 flex items-center justify-between"><span class="text-[11px] text-slate-500">Butuh bantuan transaksi?</span><a class="inline-flex items-center gap-1 font-bold text-blue-600 hover:text-blue-700 transition-colors" href="https://wa.me/" rel="noopener noreferrer" target="_blank"><span class="">Hubungi CS</span><span class="material-symbols-outlined text-[14px]">arrow_forward</span></a></div>
</div>
</div>
</div>

<!-- LIVE TRANSAKSI TERAKHIR TERKIRIM (MARQUEE WITH LIGHT THEME) -->
<section class="mt-12 pt-6 border-t border-slate-200">
<div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
<div class="flex items-center gap-3.5">
<div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200"><span class="material-symbols-outlined text-[24px]">verified</span></div>
<div><h3 class="font-display font-bold text-sm text-neutral-900">Layanan Top Up Aktif 24 Jam Nonstop</h3><p class="text-xs text-slate-500 mt-0.5">Sistem pengiriman koin chip diproses otomatis dalam 1-3 detik setelah verifikasi pembayaran berhasil.</p></div>
</div>
<div class="flex items-center gap-3 shrink-0">
<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 font-mono text-xs font-bold border border-emerald-300"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Gateway Normal 100%</span>
<a class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs flex items-center gap-1.5" href="https://wa.me/" rel="noopener noreferrer" target="_blank"><span class="material-symbols-outlined text-[16px]">chat</span> Bantuan Cepat WA</a>
</div>
</div>
</section>

<!-- TRUST BADGES SECTION (Light Mode) -->
<section class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
<div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-3 shadow-xs">
<div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-200"><span class="material-symbols-outlined text-[22px]">bolt</span></div>
<div><h4 class="font-display font-bold text-xs text-neutral-900">Proses Kilat 1 Detik</h4><p class="text-[11px] text-slate-500">Koin masuk otomatis via API</p></div>
</div>
<div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-3 shadow-xs">
<div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200"><span class="material-symbols-outlined text-[22px]">shield</span></div>
<div><h4 class="font-display font-bold text-xs text-neutral-900">100% Legal &amp; Anti Banned</h4><p class="text-[11px] text-slate-500">Hanya ID Higgs, tanpa sandi</p></div>
</div>
<div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-3 shadow-xs">
<div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-200"><span class="material-symbols-outlined text-[22px]">lock</span></div>
<div><h4 class="font-display font-bold text-xs text-neutral-900">Payment Gateway Resmi</h4><p class="text-[11px] text-slate-500">Bank resmi, VA &amp; QRIS instan</p></div>
</div>
<div class="bg-white p-4 rounded-xl border border-slate-200 flex items-center gap-3 shadow-xs">
<div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-200"><span class="material-symbols-outlined text-[22px]">support_agent</span></div>
<div><h4 class="font-display font-bold text-xs text-neutral-900">Admin CS 24 Jam</h4><p class="text-[11px] text-slate-500">Garansi pengawalan transaksi</p></div>
</div>
</section>
</main>

<!-- Checkout Confirmation Dialog (Clean Light Modal) -->
<div class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" id="checkout-modal">
<div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative animate-in fade-in zoom-in duration-200 text-neutral-900">
<div class="flex items-center justify-between pb-3 border-b border-slate-200">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-blue-600 text-[24px]">verified</span>
<h3 class="font-display font-bold text-lg text-neutral-900">Konfirmasi Pembayaran Top Up</h3>
</div>
<button class="text-slate-400 hover:text-neutral-700 p-1" id="btn-close-modal" type="button"><span class="material-symbols-outlined text-[20px]">close</span></button>
</div>
<div class="mt-4 bg-slate-50 rounded-xl p-4 space-y-2.5 text-xs border border-slate-200">
<div class="flex justify-between">
<span class="text-slate-500">Item Produk:</span>
<span class="font-bold text-neutral-900" id="modal-item">1B (1 Miliar) Koin Emas</span>
</div>
<div class="flex justify-between">
<span class="text-slate-500">User ID Higgs:</span>
<span class="font-mono font-bold text-neutral-900" id="modal-id">123456789</span>
</div>
<div class="flex justify-between">
<span class="text-slate-500">Metode Pembayaran:</span>
<span class="font-semibold text-blue-700" id="modal-method">QRIS Resmi</span>
</div>
<div class="flex justify-between pt-2 border-t border-slate-200 text-sm font-bold">
<span class="">Total Tagihan Netto:</span>
<span class="text-blue-700 font-display font-black" id="modal-total">Rp63.000</span>
</div>
</div>
<div class="mt-4 flex items-center gap-2 text-[11px] text-slate-600 bg-blue-50 p-2.5 rounded-lg border border-blue-200">
<span class="material-symbols-outlined text-[18px] text-blue-600 shrink-0">info</span>
<span class="">Jendela instruksi pembayaran resmi akan dibuka. Koin chip otomatis dikirim ke akun Anda 1-3 detik setelah verifikasi.</span>
</div>
<div class="mt-5 flex gap-3">
<button class="flex-1 py-2.5 rounded-xl border border-slate-300 font-bold text-xs text-slate-700 hover:bg-slate-100 transition-colors" id="btn-cancel-checkout" type="button">Batal</button>
<button class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 font-display font-black text-xs text-white transition-all flex items-center justify-center gap-1 shadow-md shadow-blue-600/20" id="btn-submit-pay" type="button">
<span class="">Lanjut ke Pembayaran</span>
<span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</button>
</div>
</div>
</div>

<!-- Mobile Sticky Footer Bar (Light Mode) -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 p-3 px-4 flex items-center justify-between shadow-lg">
<div>
<span class="text-[10px] uppercase font-bold text-slate-500 block">Total Tagihan</span>
<div class="font-display font-black text-blue-700 text-lg" id="mobile-bottom-total">Rp63.000</div>
</div>
<button class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-display font-black text-xs flex items-center gap-1.5 shadow-md border border-blue-700" id="btn-mobile-checkout" type="button"><span class="material-symbols-outlined text-[16px]">lock</span><span class="">Bayar Sekarang</span></button>
</div>

<!-- Form tersembunyi untuk backend submit (bila ada productId) -->
<form id="backend-checkout-form" method="POST" action="" class="hidden">
    <?= csrf_field() ?>
    <input type="hidden" name="game_id" id="hidden-game-id">
    <input type="hidden" name="whatsapp_number" id="hidden-whatsapp">
    <input type="hidden" name="voucher_code" id="hidden-voucher">
</form>

<!-- State & Interactive Script -->
<script>
  (function() {
    const adminWhatsapp = <?= json_encode($adminWhatsapp ?? '') ?>;
    const state = {
      productId: null,
      itemTitle: "1B (1 Miliar) Koin Emas",
      category: "Koin Emas",
      basePrice: 63000,
      unitRate: "Rp63.000 / 1B",
      userId: "123456789",
      nickname: "HiggsMaster",
      whatsapp: "081234567890",
      payMethod: "QRIS Resmi",
      adminFee: 0,
      discount: 0,
      couponApplied: false,
      bongkarType: "Kartu Ungu",
      bongkarQty: 1,
      bongkarPayout: "BCA"
    };

    function formatRupiah(num) {
      return 'Rp' + num.toLocaleString('id-ID');
    }

    function updateReceiptUI() {
      const grandTotal = Math.max(0, state.basePrice + state.adminFee - state.discount);

      const receiptItemName = document.getElementById('receipt-item-name');
      const receiptUnitRate = document.getElementById('receipt-unit-rate');
      const receiptItemPrice = document.getElementById('receipt-item-price');
      const receiptUserId = document.getElementById('receipt-user-id');
      const receiptWa = document.getElementById('receipt-wa');
      const receiptMethod = document.getElementById('receipt-method');
      const calcSubtotal = document.getElementById('calc-subtotal');
      const calcAdminFee = document.getElementById('calc-admin-fee');
      const calcDiscountRow = document.getElementById('calc-discount-row');
      const calcDiscountVal = document.getElementById('calc-discount-val');
      const calcGrandTotal = document.getElementById('calc-grand-total');
      const mobileBottomTotal = document.getElementById('mobile-bottom-total');

      if (receiptItemName) receiptItemName.textContent = state.itemTitle;
      if (receiptUnitRate) receiptUnitRate.textContent = state.unitRate;
      if (receiptItemPrice) receiptItemPrice.textContent = formatRupiah(state.basePrice);
      if (receiptUserId) receiptUserId.textContent = state.userId || '-';
      if (receiptWa) receiptWa.textContent = state.whatsapp || '-';
      if (receiptMethod) receiptMethod.textContent = state.payMethod;
      if (calcSubtotal) calcSubtotal.textContent = formatRupiah(state.basePrice);
      if (calcAdminFee) {
        calcAdminFee.textContent = state.adminFee > 0 ? formatRupiah(state.adminFee) : 'Rp0 (Gratis)';
        calcAdminFee.className = state.adminFee > 0 ? 'font-bold text-neutral-900 font-mono' : 'font-bold text-emerald-700 font-mono';
      }

      if (calcDiscountRow && calcDiscountVal) {
        if (state.discount > 0) {
          calcDiscountRow.classList.remove('hidden');
          calcDiscountVal.textContent = '-' + formatRupiah(state.discount);
        } else {
          calcDiscountRow.classList.add('hidden');
        }
      }

      if (calcGrandTotal) calcGrandTotal.textContent = formatRupiah(grandTotal);
      if (mobileBottomTotal) mobileBottomTotal.textContent = formatRupiah(grandTotal);
    }

    // Step 1: Category Pill Tabs
    const catPills = document.querySelectorAll('.category-pill');
    catPills.forEach(pill => {
      pill.addEventListener('click', () => {
        catPills.forEach(p => {
          p.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-sm', 'border-blue-700');
          p.classList.add('text-slate-700');
        });
        pill.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-sm', 'border-blue-700');
        pill.classList.remove('text-slate-700');
      });
    });

    // Step 2: Product Nominal Selection
    const productCards = document.querySelectorAll('.product-card');
    if (productCards.length > 0) {
      const firstActive = document.querySelector('.product-card.active') || productCards[0];
      if (firstActive) {
        state.productId = firstActive.getAttribute('data-id') || null;
        state.itemTitle = firstActive.getAttribute('data-title') || state.itemTitle;
        state.basePrice = parseInt(firstActive.getAttribute('data-price') || "63000", 10);
        state.unitRate = firstActive.getAttribute('data-unit') || state.unitRate;
      }
    }

    productCards.forEach(card => {
      card.addEventListener('click', () => {
        productCards.forEach(c => {
          c.classList.remove('active', 'border-2', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
          c.classList.add('border-slate-200', 'bg-white');
        });
        card.classList.add('active', 'border-2', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
        card.classList.remove('border-slate-200', 'bg-white');

        state.productId = card.getAttribute('data-id') || null;
        state.itemTitle = card.getAttribute('data-title') || "";
        state.basePrice = parseInt(card.getAttribute('data-price') || "0", 10);
        state.unitRate = card.getAttribute('data-unit') || "";
        updateReceiptUI();
      });
    });

    // Step 3: User ID & WhatsApp Binding
    const inputUserId = document.getElementById('input-user-id');
    const inputWa = document.getElementById('input-whatsapp');

    if (inputUserId) {
      inputUserId.addEventListener('input', (e) => {
        state.userId = e.target.value.trim();
        updateReceiptUI();
      });
    }

    if (inputWa) {
      inputWa.addEventListener('input', (e) => {
        state.whatsapp = e.target.value.trim();
        updateReceiptUI();
      });
    }

    // Step 4: Payment Method Selection
    const payMethodCards = document.querySelectorAll('.pay-method-card');
    payMethodCards.forEach(payCard => {
      payCard.addEventListener('click', () => {
        payMethodCards.forEach(p => {
          p.classList.remove('active', 'border-2', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
          p.classList.add('border-slate-200', 'bg-white');
        });
        payCard.classList.add('active', 'border-2', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
        payCard.classList.remove('border-slate-200', 'bg-white');

        state.payMethod = payCard.getAttribute('data-method') || "QRIS Resmi";
        state.adminFee = parseInt(payCard.getAttribute('data-fee') || "0", 10);
        updateReceiptUI();
      });
    });

    function normalizePhone(phone) {
      return String(phone || '').replace(/\D+/g, '').replace(/^0/, '62');
    }

    function refreshBongkarEstimate() {
      const estimated = state.bongkarQty * parseInt(state.bongkarRate || '0', 10);
      const estimatedEl = document.getElementById('bongkar-estimated');
      if (estimatedEl) {
        estimatedEl.textContent = formatRupiah(estimated);
      }
      const statusEl = document.getElementById('bongkar-status');
      if (statusEl) {
        statusEl.textContent = `${state.bongkarType} • ${state.bongkarQty} ${state.bongkarUnit}`;
      }
    }

    const bongkarCards = document.querySelectorAll('.bongkar-card');
    const bongkarQtyInput = document.getElementById('input-card-qty');
    const bongkarWaInput = document.getElementById('input-sell-wa');
    const bongkarPayoutButtons = document.querySelectorAll('[data-bongkar-payout]');
    const btnSubmitBongkar = document.getElementById('btn-submit-bongkar');

    bongkarCards.forEach(card => {
      card.addEventListener('click', () => {
        bongkarCards.forEach(item => {
          item.classList.remove('selected', 'border-2', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
          item.classList.add('border-slate-200', 'bg-white');
        });
        card.classList.add('selected', 'border-2', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
        card.classList.remove('border-slate-200', 'bg-white');
        state.bongkarType = card.getAttribute('data-label') || state.bongkarType;
        state.bongkarRate = card.getAttribute('data-rate') || state.bongkarRate;
        state.bongkarUnit = card.getAttribute('data-unit') || state.bongkarUnit;
        refreshBongkarEstimate();
      });
    });

    if (bongkarQtyInput) {
      bongkarQtyInput.addEventListener('input', (e) => {
        const qty = Math.max(1, parseInt(e.target.value || '1', 10) || 1);
        state.bongkarQty = qty;
        refreshBongkarEstimate();
      });
    }

    if (bongkarWaInput) {
      bongkarWaInput.addEventListener('input', (e) => {
        state.bongkarWa = e.target.value.trim();
      });
    }

    bongkarPayoutButtons.forEach(button => {
      button.addEventListener('click', () => {
        bongkarPayoutButtons.forEach(item => {
          item.classList.remove('border-2', 'border-blue-600', 'bg-blue-50/70', 'text-blue-700');
          item.classList.add('border-slate-200', 'bg-white', 'text-slate-700');
        });
        button.classList.add('border-2', 'border-blue-600', 'bg-blue-50/70', 'text-blue-700');
        button.classList.remove('border-slate-200', 'bg-white', 'text-slate-700');
        state.bongkarPayout = button.textContent.trim();
      });
    });

    if (btnSubmitBongkar) {
      btnSubmitBongkar.addEventListener('click', () => {
        const phone = normalizePhone(adminWhatsapp);
        const qty = Math.max(1, parseInt(bongkarQtyInput?.value || '1', 10) || 1);
        const sellerWa = bongkarWaInput ? bongkarWaInput.value.trim() : '';
        const payout = state.bongkarPayout || 'BCA';
        const rate = parseInt(state.bongkarRate || '0', 10);
        const estimated = qty * rate;

        if (!state.bongkarType || !sellerWa) {
          alert('Lengkapi jenis kartu, jumlah, dan nomor WhatsApp terlebih dahulu.');
          return;
        }

        if (!phone) {
          alert('Nomor WhatsApp admin belum diset di pengaturan toko.');
          return;
        }

        const message = [
          'Halo Admin, saya mau bongkar kartu.',
          '',
          `Jenis: ${state.bongkarType}`,
          `Jumlah: ${qty} ${state.bongkarUnit || 'kartu'}`,
          `Rate: Rp${rate.toLocaleString('id-ID')} / ${state.bongkarUnit || 'kartu'}`,
          `Estimasi: Rp${estimated.toLocaleString('id-ID')}`,
          `WA Customer: ${sellerWa}`,
          `Pencairan: ${payout}`,
          '',
          'Mohon dibantu cek dan proses.'
        ].join('\n');

        window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank', 'noopener,noreferrer');
      });
    }

    // Coupon Voucher
    const btnApplyCoupon = document.getElementById('btn-apply-coupon');
    const promoInput = document.getElementById('receipt-promo-input');
    const promoStatus = document.getElementById('promo-status');

    if (btnApplyCoupon && promoInput && promoStatus) {
      btnApplyCoupon.addEventListener('click', () => {
        const code = promoInput.value.trim().toUpperCase();
        promoStatus.classList.remove('hidden');
        if (code === "AYONGHEMAT") {
          state.discount = 5000;
          state.couponApplied = true;
          promoStatus.className = "text-[11px] font-bold text-emerald-700 block";
          promoStatus.textContent = "Kupon AYONGHEMAT berhasil dipakai! Potongan Rp5.000 aktif.";
        } else if (!code) {
          state.discount = 0;
          promoStatus.className = "text-[11px] text-rose-600 block";
          promoStatus.textContent = "Silakan ketikkan kode voucher.";
        } else {
          state.discount = 0;
          promoStatus.className = "text-[11px] text-rose-600 block";
          promoStatus.textContent = "Kode tidak valid atau kuota klaim habis.";
        }
        updateReceiptUI();
      });
    }

    // Modal Simulation & Backend Checkout
    const checkoutModal = document.getElementById('checkout-modal');
    const btnPayNow = document.getElementById('btn-pay-now');
    const btnMobileCheckout = document.getElementById('btn-mobile-checkout');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const btnCancelCheckout = document.getElementById('btn-cancel-checkout');
    const btnSubmitPay = document.getElementById('btn-submit-pay');

    function openModal() {
      if (!state.userId) {
        alert("Silakan ketik ID Akun Higgs Games Island Anda terlebih dahulu!");
        inputUserId?.focus();
        return;
      }

      const grandTotal = Math.max(0, state.basePrice + state.adminFee - state.discount);
      document.getElementById('modal-item').textContent = state.itemTitle;
      document.getElementById('modal-id').textContent = state.userId;
      document.getElementById('modal-method').textContent = state.payMethod;
      document.getElementById('modal-total').textContent = formatRupiah(grandTotal);

      if (checkoutModal) checkoutModal.classList.remove('hidden');
    }

    function closeModal() {
      if (checkoutModal) checkoutModal.classList.add('hidden');
    }

    btnPayNow?.addEventListener('click', openModal);
    btnMobileCheckout?.addEventListener('click', openModal);
    btnCloseModal?.addEventListener('click', closeModal);
    btnCancelCheckout?.addEventListener('click', closeModal);

    btnSubmitPay?.addEventListener('click', () => {
      btnSubmitPay.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">refresh</span> <span>Membuka Pembayaran...</span>';
      
      if (state.productId) {
        const form = document.getElementById('backend-checkout-form');
        document.getElementById('hidden-game-id').value = state.userId;
        document.getElementById('hidden-whatsapp').value = state.whatsapp;
        document.getElementById('hidden-voucher').value = promoInput ? promoInput.value.trim() : '';
        form.action = '<?= base_url("checkout/") ?>' + state.productId;
        form.submit();
        return;
      }

      setTimeout(() => {
        closeModal();
        btnSubmitPay.innerHTML = '<span>Lanjut ke Pembayaran</span> <span class="material-symbols-outlined text-[16px]">arrow_forward</span>';
        alert(`Simulasi Pembayaran Berhasil!\n\nID Pemain: ${state.userId}\nItem: ${state.itemTitle}\nMetode: ${state.payMethod}\nTotal: ${formatRupiah(state.basePrice + state.adminFee - state.discount)}\n\nKoin HGD otomatis diproses & masuk ke akun game Anda dalam 1-3 detik via API Server Resmi.`);
      }, 750);
    });

    updateReceiptUI();
  })();
</script>

<?= $this->endSection() ?>

