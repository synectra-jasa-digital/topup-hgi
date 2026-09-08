<!-- VIEW MODE: BELI / TOP UP -->
<?php
  $storeSettingModel = new \App\Models\StoreSettingModel();
  $currentStoreName = $storeSettingModel->getVal('store_name', 'Ayong Store');
  $waNum = ! empty($adminWhatsapp) ? preg_replace('/[^0-9]/', '', $adminWhatsapp) : '';
  $waUrl = ! empty($waNum) ? 'https://wa.me/' . $waNum : 'https://wa.me/';
?>
<div id="view-mode-buy" class="flex flex-col lg:flex-row gap-6 items-start">

<!-- LEFT COLUMN: Ordering Wizard (Step 1 → Step 4) -->
<div class="w-full lg:w-7/12 xl:w-8/12 space-y-4 min-w-0">

<!-- STEP 1: PILIH KATEGORI PRODUK -->
<section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm relative overflow-hidden">
<div class="flex items-center justify-between mb-4">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-display font-bold flex items-center justify-center text-sm shadow-xs shrink-0">1</div>
<div>
<h2 class="font-display font-bold text-base sm:text-lg text-neutral-900 leading-tight">Pilih Kategori Produk</h2>
<p class="text-xs text-slate-500">Pilih kategori koin atau item game yang ingin Anda beli</p>
</div>
</div>
</div>

<!-- Segmented Deck Tabs (Light Mode) -->
<div class="flex flex-wrap gap-2.5" id="category-pills">
<?php if (! empty($categories)): ?>
    <?php foreach ($categories as $index => $category): ?>
        <?php
            $icon = match ($category['slug']) {
                'koin-emas' => 'monetization_on',
                'kartu-emas' => 'credit_card',
                'koin-md' => 'diamond',
                'kartu-ungu' => 'workspace_premium',
                default => 'apps',
            };
            $color = match ($category['slug']) {
                'koin-emas' => 'text-amber-500',
                'kartu-emas' => 'text-amber-600',
                'koin-md' => 'text-sky-600',
                'kartu-ungu' => 'text-purple-600',
                default => 'text-slate-500',
            };
        ?>
        <button class="category-pill <?= $index === 0 ? 'active bg-blue-600 text-white shadow-sm border-blue-700' : 'text-slate-700 bg-slate-100/80 hover:bg-slate-200/80 border-slate-200' ?> border inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" data-cat="<?= esc($category['slug']) ?>" type="button">
            <?php if (! empty($category['icon'])): ?>
                <img src="<?= base_url($category['icon']) ?>" alt="" class="h-[18px] w-[18px] shrink-0 object-contain">
            <?php else: ?>
                <span class="material-symbols-outlined text-[18px] <?= $index === 0 ? 'text-amber-300' : $color ?>" <?= $index === 0 && $category['slug'] === 'koin-emas' ? "style=\"font-variation-settings: 'FILL' 1;\"" : '' ?>><?= esc($icon) ?></span>
            <?php endif; ?>
            <span class="truncate"><?= esc($category['name']) ?></span>
        </button>
    <?php endforeach; ?>
<?php endif; ?>
</div>
</section>

<!-- STEP 2: PILIH NOMINAL PRODUK -->
<section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm relative">
<div class="flex items-center justify-between mb-4">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-display font-bold flex items-center justify-center text-sm shadow-xs shrink-0">2</div>
<div>
<h2 class="font-display font-bold text-base sm:text-lg text-neutral-900 leading-tight">Pilih Nominal Top Up</h2>
<p class="text-xs text-slate-500">Pilih nominal koin atau paket item yang diinginkan</p>
</div>
</div>
<span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-semibold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-300 shrink-0">
<span class="material-symbols-outlined text-[14px] text-amber-600">bolt</span> Proses Kilat
</span>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="product-grid">
<?php if (! empty($sections)): ?>
    <?php foreach ($sections as $section): ?>
        <div class="col-span-full mt-2 first:mt-0 space-y-3" data-category-group="<?= esc($section['category']['slug']) ?>">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500"><?= esc($section['category']['name']) ?></p>
                    <h3 class="text-sm font-bold text-neutral-900">Produk tersedia</h3>
                </div>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600"><?= count($section['products']) ?> item</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <?php foreach ($section['products'] as $pIndex => $p): ?>
                    <?php 
                        $isDefaultActive = false;
                        $sellPrice = (float) $p['sell_price'];
                        $priceFormatted = 'Rp' . number_format($sellPrice, 0, ',', '.');
                        $catIconUrl = ! empty($section['category']['icon']) ? base_url($section['category']['icon']) : '';
                    ?>
                    <button class="product-card <?= $isDefaultActive ? 'active border-2 border-blue-600 bg-blue-50/70 ring-2 ring-blue-500/20' : 'border border-slate-200 bg-white hover:border-blue-400 hover:bg-blue-50/40' ?> group relative p-3.5 rounded-xl text-left shadow-xs transition-all focus:outline-none" data-id="<?= $p['id'] ?>" data-cat="<?= esc($section['category']['slug']) ?>" data-price="<?= $sellPrice ?>" data-title="<?= esc($p['name']) ?>" data-unit="<?= esc($p['nominal'] ?: $priceFormatted) ?>" data-icon="<?= esc($catIconUrl) ?>" type="button">
                        <?php if ($isDefaultActive): ?>
                            <span class="absolute -top-2.5 right-2 px-2 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wide shadow-xs">Terpopuler</span>
                        <?php endif; ?>
                        <div class="flex items-start justify-between">
                            <?php if (! empty($section['category']['icon'])): ?>
                                <div class="w-8 h-8 rounded-full bg-blue-50/80 flex items-center justify-center border border-blue-200/80 overflow-hidden p-1 shrink-0">
                                    <img src="<?= base_url($section['category']['icon']) ?>" alt="<?= esc($section['category']['name']) ?>" class="w-full h-full object-contain">
                                </div>
                            <?php else: ?>
                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-700 border border-blue-200 shrink-0">
                                    <span class="material-symbols-outlined text-[18px]">sports_esports</span>
                                </div>
                            <?php endif; ?>
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
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <!-- Default Mockup Products if Database is Fresh -->
    <button class="product-card group relative p-3.5 rounded-xl border border-slate-200 text-left bg-white hover:border-blue-400 hover:bg-blue-50/40 transition-all focus:outline-none shadow-xs" data-cat="Koin Emas" data-price="13000" data-title="200M Koin Emas HGD" data-unit="Rp65.000 / 1B" type="button">
    <div class="flex items-start justify-between"><div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-700 border border-blue-200 shrink-0"><span class="material-symbols-outlined text-[18px]">sports_esports</span></div><span class="text-[10px] font-semibold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded">Retail</span></div>
    <div class="mt-2 font-display font-bold text-neutral-900 text-base leading-snug">200M</div>
    <div class="text-[11px] text-slate-500">Koin Emas Resmi</div>
    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-baseline justify-between"><span class="text-xs font-bold text-blue-600 font-display">Rp13.000</span><span class="text-[10px] text-slate-500 font-mono">Rp65k/B</span></div>
    </button>
    
    <button class="product-card group relative p-3.5 rounded-xl border border-slate-200 text-left bg-white hover:border-blue-400 hover:bg-blue-50/40 transition-all focus:outline-none shadow-xs" data-cat="Koin Emas" data-price="25500" data-title="400M Koin Emas HGD" data-unit="Rp63.750 / 1B" type="button">
    <div class="flex items-start justify-between"><div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-700 border border-blue-200 shrink-0"><span class="material-symbols-outlined text-[18px]">sports_esports</span></div><span class="text-[10px] font-semibold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">Hemat</span></div>
    <div class="mt-2 font-display font-bold text-neutral-900 text-base leading-snug">400M</div>
    <div class="text-[11px] text-slate-500">Koin Emas Resmi</div>
    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-baseline justify-between"><span class="text-xs font-bold text-blue-600 font-display">Rp25.500</span><span class="text-[10px] text-slate-500 font-mono">Rp63.7k/B</span></div>
    </button>
    
    <button class="product-card group relative p-3.5 rounded-xl border border-slate-200 text-left bg-white hover:border-blue-400 hover:bg-blue-50/40 transition-all focus:outline-none shadow-xs" data-cat="Koin Emas" data-price="63000" data-title="1B (1 Miliar) Koin Emas" data-unit="Rp63.000 / 1B" type="button">
    <span class="absolute -top-2.5 right-2 px-2 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wide shadow-xs">Terpopuler</span>
    <div class="flex items-start justify-between"><div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center text-neutral-950 shadow-sm border border-amber-300 shrink-0"><span class="material-symbols-outlined text-[18px]">sports_esports</span></div><span class="text-[10px] font-bold text-blue-700 bg-white border border-blue-200 px-1.5 py-0.5 rounded">Paket Rekomendasi</span></div>
    <div class="mt-2 font-display font-bold text-neutral-900 text-base leading-snug">1B (1 Miliar)</div>
    <div class="text-[11px] text-slate-600 font-medium">Koin Emas Resmi</div>
    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-baseline justify-between"><span class="text-xs font-bold text-blue-600 font-display">Rp63.000</span><span class="text-[10px] text-slate-500 font-mono">Rp63k/B</span></div>
    </button>
    
    <button class="product-card group relative p-3.5 rounded-xl border border-slate-200 text-left bg-white hover:border-blue-400 hover:bg-blue-50/40 transition-all focus:outline-none shadow-xs" data-cat="Koin Emas" data-price="125000" data-title="2B (2 Miliar) Koin Emas" data-unit="Rp62.500 / 1B" type="button">
    <span class="absolute -top-2.5 right-2 px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-wide shadow-xs">Diskon 4%</span>
    <div class="flex items-start justify-between"><div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-700 border border-blue-200 shrink-0"><span class="material-symbols-outlined text-[18px]">sports_esports</span></div><span class="text-[10px] text-slate-400 line-through">Rp130k</span></div>
    <div class="mt-2 font-display font-bold text-neutral-900 text-base leading-snug">2B (2 Miliar)</div>
    <div class="text-[11px] text-slate-500">Koin Emas Resmi</div>
    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-baseline justify-between"><span class="text-xs font-bold text-blue-600 font-display">Rp125.000</span><span class="text-[10px] text-emerald-700 font-semibold font-mono">Rp62.5k/B</span></div>
    </button>
    
    <button class="product-card group relative p-3.5 rounded-xl border border-slate-200 text-left bg-white hover:border-blue-400 hover:bg-blue-50/40 transition-all focus:outline-none shadow-xs" data-cat="Koin Emas" data-price="310000" data-title="5B Koin Emas Sultan" data-unit="Rp62.000 / 1B" type="button">
    <div class="flex items-start justify-between"><div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-700 border border-blue-200 shrink-0"><span class="material-symbols-outlined text-[18px]">sports_esports</span></div><span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200">Paket Besar</span></div>
    <div class="mt-2 font-display font-bold text-neutral-900 text-base leading-snug">5B Koin</div>
    <div class="text-[11px] text-slate-500">Koin Emas Resmi</div>
    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-baseline justify-between"><span class="text-xs font-bold text-blue-600 font-display">Rp310.000</span><span class="text-[10px] text-emerald-700 font-semibold font-mono">Rp62k/B</span></div>
    </button>
    
    <button class="product-card group relative p-3.5 rounded-xl border border-slate-200 text-left bg-white hover:border-blue-400 hover:bg-blue-50/40 transition-all focus:outline-none shadow-xs" data-cat="Koin Emas" data-price="615000" data-title="10B Koin Emas VIP Max" data-unit="Rp61.500 / 1B" type="button">
    <span class="absolute -top-2.5 right-2 px-2 py-0.5 rounded-full bg-emerald-600 text-white text-[10px] font-bold uppercase tracking-wide shadow-xs">Paket Maksimal</span>
    <div class="flex items-start justify-between"><div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-700 border border-blue-200 shrink-0"><span class="material-symbols-outlined text-[18px]">sports_esports</span></div><span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Ekstra Hemat</span></div>
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

<!-- STEP 3: MASUKKAN DATA USER ID GAME & KONTAK -->
<section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm">
<div class="flex items-center justify-between mb-4">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-display font-bold flex items-center justify-center text-sm shadow-xs shrink-0">3</div>
<div><h2 class="font-display font-bold text-base sm:text-lg text-neutral-900 leading-tight">Data Akun &amp; Kontak</h2><p class="text-xs text-slate-500">ID akun tujuan pengiriman koin &amp; nomor WhatsApp konfirmasi</p></div>
</div>
<span class="hidden sm:flex text-xs text-emerald-700 items-center gap-1 font-semibold shrink-0">
<span class="material-symbols-outlined text-[16px]">verified_user</span> Tanpa Password
</span>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
<!-- Input ID Game -->
<div class="space-y-1.5">
<label class="block text-xs font-bold text-neutral-700" for="input-user-id">
          User ID Game <span class="text-rose-500">*</span>
</label>
<div class="flex gap-2">
<div class="relative flex-1">
<span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-slate-400">sports_esports</span>
<input class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all font-mono font-bold text-neutral-900 placeholder-slate-400" id="input-user-id" placeholder="Contoh: 123456789" type="text" value="">
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
<input class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all font-mono font-bold text-neutral-900 placeholder-slate-400" id="input-whatsapp" placeholder="08xxxxxxxxxx" type="tel" value="">
</div>
<p class="text-[11px] text-slate-500 pt-1">Notifikasi bukti transaksi &amp; invoice otomatis dikirimkan ke nomor WhatsApp ini.</p>
</div>
</div>
</section>

<!-- STEP 4: PILIH METODE PEMBAYARAN -->
<section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm">
<div class="flex items-center justify-between mb-4">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-display font-bold flex items-center justify-center text-sm shadow-xs shrink-0">4</div>
<div>
<h2 class="font-display font-bold text-base sm:text-lg text-neutral-900 leading-tight">Metode Pembayaran</h2>
<p class="text-xs text-slate-500">Pilih saluran pembayaran resmi yang praktis &amp; aman</p>
</div>
</div>
<span class="hidden sm:flex text-xs font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-300 items-center gap-1 shrink-0">
<span class="material-symbols-outlined text-[14px] text-amber-600">flash_on</span> Konfirmasi Otomatis
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
<!-- QRIS -->
<button class="pay-method-card p-3 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all relative shadow-xs" data-badge="Bebas Biaya" data-fee="0" data-method="QRIS Resmi" type="button">
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

</div><!-- END LEFT COLUMN -->

<!-- RIGHT COLUMN: Sticky Live Order Card & VIP Benefits -->
<div class="w-full lg:w-5/12 xl:w-4/12 lg:sticky lg:top-24 space-y-4 shrink-0">
<!-- Live Ticket Receipt Gaming Cockpit (Crisp White Card) -->
<div class="bg-white rounded-2xl border-2 border-slate-200 shadow-md overflow-hidden relative">
<!-- Gaming Ticket Header -->
<div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 p-4 text-white relative border-b border-blue-800">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2">
<div class="w-8 h-8 rounded-lg bg-white text-blue-700 flex items-center justify-center font-bold shadow-sm shrink-0"><span class="material-symbols-outlined text-[20px]">receipt_long</span></div>
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
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0 shadow-xs border border-blue-200 overflow-hidden p-1" id="receipt-category-icon-container">
                            <span class="material-symbols-outlined text-[22px]">sports_esports</span>
                        </div>
<div class="min-w-0 flex-1">
<div class="text-[10px] font-black text-amber-600 uppercase tracking-wide">Detail Produk</div>
<div class="font-display font-extrabold text-neutral-900 text-sm truncate" id="receipt-item-name">Belum memilih produk</div>
<div class="text-[11px] text-slate-500" id="receipt-unit-rate">Silakan pilih nominal produk di samping</div>
</div>
<div class="text-right shrink-0">
<span class="text-xs font-black text-blue-600 font-display" id="receipt-item-price">Rp0</span>
</div>
</div>
<!-- Destination Account & Order Specs -->
<div class="space-y-2 py-3 border-y border-dashed border-slate-200 text-xs">
<div class="flex justify-between items-center">
<span class="text-slate-500">ID Game Tujuan:</span>
<span class="font-mono font-bold text-neutral-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200" id="receipt-user-id">-</span>
</div>
<div class="flex justify-between items-center">
<span class="text-slate-500">WhatsApp Notif:</span>
<span class="font-mono font-semibold text-neutral-800" id="receipt-wa">-</span>
</div>
<div class="flex justify-between items-center">
<span class="text-slate-500">Metode Bayar:</span>
<span class="font-bold text-blue-700" id="receipt-method">-</span>
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
<span>Harga Produk Koin:</span>
<span class="font-bold text-neutral-900 font-mono" id="calc-subtotal">Rp0</span>
</div>
<div class="flex justify-between text-slate-600">
<span>Biaya Layanan Gateway:</span>
<span class="font-bold text-emerald-700 font-mono" id="calc-admin-fee">Rp0 (Gratis)</span>
</div>
<div class="flex justify-between text-rose-600 hidden" id="calc-discount-row">
<span class="flex items-center gap-1 font-bold">
<span class="material-symbols-outlined text-[14px]">discount</span> Diskon Kupon:
</span>
<span class="font-black font-mono" id="calc-discount-val">-Rp0</span>
</div>
</div>
<!-- Decorative Line -->
<div class="border-b-2 border-dotted border-slate-200 my-1"></div>
<!-- Grand Total Display -->
<div class="pt-1 flex items-end justify-between">
<div>
<span class="text-[10px] uppercase font-black tracking-wider text-slate-500 block">Total Pembayaran Netto</span>
<div class="text-2xl sm:text-3xl font-black text-blue-700 font-display flex items-baseline gap-1" id="calc-grand-total">Rp0</div>
</div>
<div class="text-right">
<span class="text-[10px] font-black text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-300 block">Garansi Masuk 100%</span>
<span class="text-[10px] text-slate-500 mt-0.5 block font-mono">Gateway BI Terlisensi</span>
</div>
</div>
<!-- Checkout Button -->
<button class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-display font-black text-base shadow-md transition-all flex items-center justify-center gap-2 group cursor-pointer border border-blue-700" id="btn-pay-now" type="button">
<span class="material-symbols-outlined text-[20px] text-amber-300">lock</span><span>Bayar Sekarang</span>
</button>
</div>
</div>
<!-- VIP Benefits Box (Clean Light) -->
<div class="rounded-2xl border border-slate-200 bg-white p-4 text-xs shadow-xs space-y-3">
<div class="flex items-center gap-2.5">
<div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shadow-xs border border-blue-200 shrink-0"><span class="material-symbols-outlined text-[20px]">verified</span></div>
<div><div class="font-display font-bold text-neutral-900 text-sm">Jaminan Layanan <?= esc($currentStoreName) ?></div><div class="text-slate-500 text-[11px]">Standar keamanan &amp; keandalan transaksi terbaik</div></div>
</div>
<ul class="space-y-1.5 text-slate-600 text-[11px] pl-1">
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-blue-600">check_circle</span><span>Proses instan, cepat &amp; terpercaya 24/7</span></li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-blue-600">check_circle</span><span>Harga transparan tanpa biaya tersembunyi</span></li>
<li class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-blue-600">check_circle</span><span>Dukungan CS WhatsApp 24 jam siap melayani</span></li>
</ul>
<div class="pt-2 border-t border-slate-200 flex items-center justify-between"><span class="text-[11px] text-slate-500">Butuh bantuan transaksi?</span><a class="inline-flex items-center gap-1 font-bold text-blue-600 hover:text-blue-700 transition-colors" href="<?= esc($waUrl) ?>" rel="noopener noreferrer" target="_blank"><span>Hubungi CS</span><span class="material-symbols-outlined text-[14px]">arrow_forward</span></a></div>
</div>
</div><!-- END RIGHT COLUMN -->

</div><!-- END VIEW MODE BUY -->
