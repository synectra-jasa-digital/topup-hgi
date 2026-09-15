<!-- VIEW MODE: BELI / TOP UP -->
<?php
  $storeSettingModel = new \App\Models\StoreSettingModel();
  $currentStoreName = $storeSettingModel->getVal('store_name', 'Ayong Store');
  $waNum = ! empty($adminWhatsapp) ? preg_replace('/[^0-9]/', '', $adminWhatsapp) : '';
  $waUrl = ! empty($waNum) ? 'https://wa.me/' . $waNum : 'https://wa.me/';
?>
<div id="view-mode-buy" class="flex flex-col lg:flex-row gap-6 items-start">

<!-- LEFT COLUMN: 4-Step Ordering Wizard -->
<div class="w-full lg:w-7/12 xl:w-8/12 space-y-4 min-w-0">

<!-- STEP 1: PILIH KATEGORI PRODUK -->
<section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs relative overflow-hidden transition-all hover:border-slate-300">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
    <div class="flex items-center" style="gap: 14px;">
      <div class="step-number-badge w-8 h-8 rounded-xl text-white font-display font-black flex items-center justify-center text-sm shrink-0">1</div>
      <div>
        <h2 class="font-display font-bold text-base sm:text-lg text-slate-900 leading-tight">Pilih Kategori Produk</h2>
        <p class="text-xs text-slate-500">Pilih kategori koin atau jenis item yang ingin Anda beli</p>
      </div>
    </div>
    <!-- Quick Search Filter -->
    <div class="relative w-full sm:w-56">
      <span class="material-symbols-outlined absolute left-2.5 top-2 text-[16px] text-slate-400">search</span>
      <input type="text" id="catalog-search-input" placeholder="Cari nominal (1B, 200M...)" class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 rounded-xl border border-slate-200 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all placeholder:text-slate-400 font-medium">
    </div>
  </div>

  <!-- Category Pills Deck -->
  <div class="flex flex-wrap gap-2" id="category-pills">
    <?php if (! empty($categories)): ?>
      <?php foreach ($categories as $index => $category): ?>
        <?php
          $icon = match ($category['slug']) {
              'koin-emas' => 'monetization_on',
              'kartu-emas' => 'credit_card',
              'koin-md' => 'diamond',
              'kartu-ungu' => 'workspace_premium',
              default => 'sports_esports',
          };
          $color = match ($category['slug']) {
              'koin-emas' => 'text-amber-500',
              'kartu-emas' => 'text-amber-600',
              'koin-md' => 'text-sky-600',
              'kartu-ungu' => 'text-purple-600',
              default => 'text-slate-500',
          };
        ?>
        <button class="category-pill <?= $index === 0 ? 'active' : 'text-slate-700 bg-slate-100/90 hover:bg-slate-200/80 border-slate-200' ?> border inline-flex items-center justify-center gap-2 py-2 px-3.5 rounded-xl text-xs sm:text-sm font-bold transition-all cursor-pointer shadow-2xs" data-cat="<?= esc($category['slug']) ?>" type="button">
          <?php if (! empty($category['icon'])): ?>
            <img src="<?= base_url($category['icon']) ?>" alt="" class="h-[18px] w-[18px] shrink-0 object-contain">
          <?php else: ?>
            <span class="material-symbols-outlined pill-icon text-[18px] <?= $index === 0 ? 'text-amber-300' : $color ?>"><?= esc($icon) ?></span>
          <?php endif; ?>
          <span class="truncate"><?= esc($category['name']) ?></span>
        </button>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<!-- STEP 2: PILIH NOMINAL PRODUK -->
<section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs relative">
  <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
    <div class="flex items-center" style="gap: 14px;">
      <div class="step-number-badge w-8 h-8 rounded-xl text-white font-display font-black flex items-center justify-center text-sm shrink-0">2</div>
      <div>
        <h2 class="font-display font-bold text-base sm:text-lg text-slate-900 leading-tight">Pilih Nominal Top Up</h2>
        <p class="text-xs text-slate-500">Pilih nominal koin atau paket yang diinginkan</p>
      </div>
    </div>
  </div>

  <div class="space-y-4" id="product-grid">
    <?php if (! empty($sections)): ?>
      <?php foreach ($sections as $section): ?>
        <div class="space-y-3" data-category-group="<?= esc($section['category']['slug']) ?>">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
              <h3 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-wide"><?= esc($section['category']['name']) ?></h3>
            </div>
            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-600 border border-slate-200"><?= count($section['products']) ?> pilihan</span>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <?php foreach ($section['products'] as $pIndex => $p): ?>
              <?php 
                $sellPrice = (float) $p['sell_price'];
                $priceFormatted = 'Rp' . number_format($sellPrice, 0, ',', '.');
                $catIconUrl = ! empty($section['category']['icon']) ? base_url($section['category']['icon']) : '';
              ?>
              <button class="product-card group relative p-3.5 sm:p-4 rounded-xl border border-slate-200/90 bg-white hover:border-blue-500 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 text-left focus:outline-none cursor-pointer flex flex-col justify-between min-h-[110px]" data-id="<?= $p['id'] ?>" data-cat="<?= esc($section['category']['slug']) ?>" data-price="<?= $sellPrice ?>" data-title="<?= esc($p['name']) ?>" data-unit="<?= esc($p['nominal'] ?: $priceFormatted) ?>" data-icon="<?= esc($catIconUrl) ?>" type="button">
                <div>
                  <div class="font-display font-black text-slate-900 text-sm sm:text-base leading-snug group-hover:text-blue-600 transition-colors"><?= esc($p['name']) ?></div>
                  <?php if (! empty($p['nominal'])): ?>
                    <div class="text-[11px] text-slate-500 font-medium mt-0.5"><?= esc($p['nominal']) ?></div>
                  <?php endif; ?>
                </div>

                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between">
                  <span class="text-sm sm:text-base font-black text-blue-600 font-display tracking-tight"><?= $priceFormatted ?></span>
                  <span class="w-5 h-5 rounded-full border border-slate-300 group-[.product-card-selected]:border-blue-600 group-[.product-card-selected]:bg-blue-600 text-white flex items-center justify-center text-[11px] font-bold opacity-0 group-[.product-card-selected]:opacity-100 transition-all">✓</span>
                </div>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<!-- STEP 3: MASUKKAN DATA USER ID GAME & KONTAK -->
<section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs relative">
  <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
    <div class="flex items-center" style="gap: 14px;">
      <div class="step-number-badge w-8 h-8 rounded-xl text-white font-display font-black flex items-center justify-center text-sm shrink-0">3</div>
      <div>
        <h2 class="font-display font-bold text-base sm:text-lg text-slate-900 leading-tight">Data Akun &amp; WhatsApp</h2>
        <p class="text-xs text-slate-500">ID akun game tujuan pengiriman koin &amp; nomor WhatsApp verifikasi</p>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <!-- Input ID Game -->
    <div class="space-y-1.5">
      <div class="flex items-center justify-between">
        <label class="block text-xs font-bold text-slate-800" for="input-user-id">
          User ID Game <span class="text-rose-500">*</span>
        </label>
        <button type="button" id="btn-guide-id" class="text-[11px] text-blue-600 hover:text-blue-700 font-bold inline-flex items-center gap-0.5 cursor-pointer">
          <span class="material-symbols-outlined text-[14px]">help</span> Cara Cek ID?
        </button>
      </div>
      <div class="relative">
        <span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-slate-400">sports_esports</span>
        <input class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all font-mono font-bold text-slate-900 placeholder:text-slate-400" id="input-user-id" placeholder="Contoh: 123456789" type="text" value="">
      </div>
      <p class="text-[11px] text-slate-500">Masukkan 8–10 digit User ID game tanpa huruf atau simbol.</p>
    </div>

    <!-- Input WhatsApp -->
    <div class="space-y-1.5">
      <label class="block text-xs font-bold text-slate-800" for="input-whatsapp">
        Nomor WhatsApp Pembeli <span class="text-rose-500">*</span>
      </label>
      <div class="relative">
        <span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-emerald-600">chat</span>
        <input class="w-full pl-9 pr-3 py-2 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all font-mono font-bold text-slate-900 placeholder:text-slate-400" id="input-whatsapp" placeholder="08xxxxxxxxxx" type="tel" value="">
      </div>
      <p class="text-[11px] text-slate-500">Invoice dan konfirmasi transaksi otomatis dikirimkan ke nomor ini.</p>
    </div>
  </div>
</section>

<!-- STEP 4: PILIH METODE PEMBAYARAN -->
<section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs relative">
  <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
    <div class="flex items-center" style="gap: 14px;">
      <div class="step-number-badge w-8 h-8 rounded-xl text-white font-display font-black flex items-center justify-center text-sm shrink-0">4</div>
      <div>
        <h2 class="font-display font-bold text-base sm:text-lg text-slate-900 leading-tight">Metode Pembayaran</h2>
        <p class="text-xs text-slate-500">Pilih saluran pembayaran resmi yang praktis, cepat &amp; aman</p>
      </div>
    </div>
  </div>

  <!-- Group A: QRIS & E-Wallet -->
  <div class="space-y-2 mb-4">
    <div class="flex items-center justify-between text-[11px] font-extrabold uppercase tracking-wider text-slate-600">
      <span class="flex items-center gap-1.5">
        <span class="material-symbols-outlined text-[16px] text-blue-600">qr_code_scanner</span> QRIS &amp; E-Wallet
      </span>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
      <!-- QRIS -->
      <button class="pay-method-card p-3 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all relative shadow-2xs cursor-pointer group" data-badge="Bebas Biaya" data-fee="0" data-method="QRIS Resmi" type="button">
        <span class="absolute top-1.5 right-1.5 px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-600 text-white shadow-2xs">INSTAN</span>
        <div class="text-xs font-black text-slate-900 group-hover:text-blue-600">QRIS Resmi</div>
        <div class="text-[10px] text-slate-500 truncate">Semua Bank / e-Wallet</div>
        <div class="mt-1.5 text-[11px] font-bold text-emerald-700">Rp0 Admin</div>
      </button>
      <!-- GoPay -->
      <button class="pay-method-card p-3 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all relative shadow-2xs cursor-pointer group" data-badge="Bebas Biaya" data-fee="0" data-method="GoPay" type="button">
        <div class="text-xs font-bold text-sky-700 group-hover:text-blue-600">GoPay</div>
        <div class="text-[10px] text-slate-500">Gojek App Instant</div>
        <div class="mt-1.5 text-[11px] font-bold text-emerald-700">Rp0 Admin</div>
      </button>
      <!-- DANA -->
      <button class="pay-method-card p-3 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all relative shadow-2xs cursor-pointer group" data-badge="Bebas Biaya" data-fee="0" data-method="DANA" type="button">
        <div class="text-xs font-bold text-blue-700 group-hover:text-blue-600">DANA</div>
        <div class="text-[10px] text-slate-500">Dompet Digital</div>
        <div class="mt-1.5 text-[11px] font-bold text-emerald-700">Rp0 Admin</div>
      </button>
      <!-- ShopeePay -->
      <button class="pay-method-card p-3 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all relative shadow-2xs cursor-pointer group" data-badge="Bebas Biaya" data-fee="0" data-method="ShopeePay" type="button">
        <div class="text-xs font-bold text-orange-600 group-hover:text-blue-600">ShopeePay</div>
        <div class="text-[10px] text-slate-500">SPay Deeplink</div>
        <div class="mt-1.5 text-[11px] font-bold text-emerald-700">Rp0 Admin</div>
      </button>
      <!-- OVO -->
      <button class="pay-method-card p-3 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all relative shadow-2xs cursor-pointer group" data-badge="Bebas Biaya" data-fee="0" data-method="OVO" type="button">
        <div class="text-xs font-bold text-purple-700 group-hover:text-blue-600">OVO Cash</div>
        <div class="text-[10px] text-slate-500">Notifikasi Push</div>
        <div class="mt-1.5 text-[11px] font-bold text-emerald-700">Rp0 Admin</div>
      </button>
      <!-- LinkAja -->
      <button class="pay-method-card p-3 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all relative shadow-2xs cursor-pointer group" data-badge="Bebas Biaya" data-fee="0" data-method="LinkAja" type="button">
        <div class="text-xs font-bold text-red-600 group-hover:text-blue-600">LinkAja</div>
        <div class="text-[10px] text-slate-500">Aplikasi LinkAja</div>
        <div class="mt-1.5 text-[11px] font-bold text-emerald-700">Rp0 Admin</div>
      </button>
    </div>
  </div>

  <!-- Group B: Virtual Account -->
  <div class="space-y-2 pt-3 border-t border-slate-200">
    <div class="flex items-center justify-between text-[11px] font-extrabold uppercase tracking-wider text-slate-600">
      <span class="flex items-center gap-1.5">
        <span class="material-symbols-outlined text-[16px] text-blue-600">account_balance</span> Virtual Account Otomatis 24 Jam
      </span>
      <span class="text-slate-500 font-mono text-[10px]">+Rp1.000 Biaya VA</span>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
      <button class="pay-method-card p-2.5 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all shadow-2xs cursor-pointer group" data-badge="+Rp1.000" data-fee="1000" data-method="BCA Virtual Account" type="button">
        <div class="text-xs font-black text-blue-700 font-display group-hover:text-blue-600">BCA VA</div>
        <div class="text-[10px] text-slate-500">m-BCA / KlikBCA</div>
        <div class="mt-1 text-[10px] font-semibold text-slate-500">+Rp1.000</div>
      </button>
      <button class="pay-method-card p-2.5 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all shadow-2xs cursor-pointer group" data-badge="+Rp1.000" data-fee="1000" data-method="Mandiri VA" type="button">
        <div class="text-xs font-black text-amber-700 font-display group-hover:text-blue-600">MANDIRI</div>
        <div class="text-[10px] text-slate-500">Livin Mandiri</div>
        <div class="mt-1 text-[10px] font-semibold text-slate-500">+Rp1.000</div>
      </button>
      <button class="pay-method-card p-2.5 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all shadow-2xs cursor-pointer group" data-badge="+Rp1.000" data-fee="1000" data-method="BRI Virtual Account" type="button">
        <div class="text-xs font-black text-sky-700 font-display group-hover:text-blue-600">BRIVA</div>
        <div class="text-[10px] text-slate-500">BRImo App</div>
        <div class="mt-1 text-[10px] font-semibold text-slate-500">+Rp1.000</div>
      </button>
      <button class="pay-method-card p-2.5 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all shadow-2xs cursor-pointer group" data-badge="+Rp1.000" data-fee="1000" data-method="BNI Virtual Account" type="button">
        <div class="text-xs font-black text-orange-600 font-display group-hover:text-blue-600">BNI VA</div>
        <div class="text-[10px] text-slate-500">BNI Mobile</div>
        <div class="mt-1 text-[10px] font-semibold text-slate-500">+Rp1.000</div>
      </button>
    </div>
  </div>
</section>

</div><!-- END LEFT COLUMN -->

<!-- RIGHT COLUMN: Sticky Live Gaming Order Cockpit -->
<div class="w-full lg:w-5/12 xl:w-4/12 lg:sticky lg:top-24 space-y-4 shrink-0">

  <!-- Live Ticket Receipt Cockpit -->
  <div class="bg-white rounded-2xl border-2 border-slate-200 shadow-md overflow-hidden relative">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 p-4 text-white relative border-b border-blue-800">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-white text-blue-700 flex items-center justify-center font-bold shadow-xs shrink-0">
            <span class="material-symbols-outlined text-[20px]">receipt</span>
          </div>
          <div>
            <h3 class="font-display font-bold text-sm tracking-wide">Ringkasan Pesanan</h3>
            <p class="text-[10px] text-blue-100">Live order ticket verifikasi otomatis</p>
          </div>
        </div>
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
      </div>
    </div>

    <div class="p-4 space-y-4 text-xs">
      <!-- Selected Product Box Preview -->
      <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
        <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 border border-blue-200/80 p-1" id="receipt-category-icon-container">
          <span class="material-symbols-outlined text-[22px]">sports_esports</span>
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="font-display font-black text-slate-900 text-sm truncate" id="receipt-item-name">Belum memilih produk</h4>
          <p class="text-[11px] text-slate-500 truncate" id="receipt-unit-rate">Silakan pilih nominal produk di samping</p>
        </div>
        <div class="text-right shrink-0">
          <div class="text-xs font-black text-blue-700 font-display" id="receipt-item-price">Rp0</div>
        </div>
      </div>

      <!-- Account Summary Badges -->
      <div class="bg-slate-50/80 rounded-xl p-3 space-y-2 border border-slate-100 text-[11px]">
        <div class="flex justify-between items-center">
          <span class="text-slate-500">User ID Game:</span>
          <span class="font-mono font-bold text-slate-900" id="receipt-user-id">-</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-slate-500">No. WhatsApp:</span>
          <span class="font-mono font-semibold text-slate-900" id="receipt-wa">-</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-slate-500">Metode Bayar:</span>
          <span class="font-bold text-blue-700" id="receipt-method">-</span>
        </div>
      </div>

      <!-- Coupon Code Input -->
      <div class="space-y-1.5">
        <div class="flex items-center justify-between">
          <label class="block text-xs font-semibold text-slate-700" for="receipt-promo-input">Kode Voucher Promo</label>
          <button type="button" id="btn-sample-coupon" class="text-[10px] text-amber-600 hover:text-amber-700 font-bold cursor-pointer">Gunakan: AYONGHEMAT</button>
        </div>
        <div class="flex gap-2">
          <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-2.5 top-2 text-[16px] text-amber-500">local_activity</span>
            <input class="w-full pl-8 pr-2 py-1.5 text-xs uppercase font-mono rounded-lg bg-slate-50 border border-slate-300 focus:border-blue-600 focus:bg-white outline-none text-slate-900 placeholder:text-slate-400 font-bold" id="receipt-promo-input" placeholder="KODE VOUCHER" type="text">
          </div>
          <button class="px-3.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-neutral-950 text-xs font-black transition-all shrink-0 shadow-xs border border-amber-400 cursor-pointer" id="btn-apply-coupon" type="button">Pakai</button>
        </div>
        <div class="text-[11px] hidden font-semibold" id="promo-status"></div>
      </div>

      <!-- Price Breakdown -->
      <div class="space-y-1.5 pt-1 text-xs">
        <div class="flex justify-between text-slate-600">
          <span>Harga Produk:</span>
          <span class="font-bold text-slate-900 font-mono" id="calc-subtotal">Rp0</span>
        </div>
        <div class="flex justify-between text-slate-600">
          <span>Biaya Layanan:</span>
          <span class="font-bold text-emerald-700 font-mono" id="calc-admin-fee">Rp0 (Gratis)</span>
        </div>
        <div class="flex justify-between text-rose-600 hidden" id="calc-discount-row">
          <span class="flex items-center gap-1 font-bold">
            <span class="material-symbols-outlined text-[14px]">discount</span> Diskon Kupon:
          </span>
          <span class="font-black font-mono" id="calc-discount-val">-Rp0</span>
        </div>
      </div>

      <!-- Decorative Divider -->
      <div class="border-b-2 border-dashed border-slate-200 my-1"></div>

      <!-- Grand Total -->
      <div class="pt-1 flex items-end justify-between">
        <div>
          <span class="text-[10px] uppercase font-black tracking-wider text-slate-500 block">Total Tagihan Netto</span>
          <div class="text-3xl sm:text-4xl font-black text-blue-700 font-display flex items-baseline gap-1 tracking-tight" id="calc-grand-total">Rp0</div>
        </div>
        <div class="text-right">
          <span class="text-[10px] font-black text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-300 block">✓ Garansi 100%</span>
          <span class="text-[10px] text-slate-500 mt-0.5 block font-mono">Resmi &amp; Cepat</span>
        </div>
      </div>

      <!-- Checkout Button -->
      <button class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-display font-black text-base shadow-md transition-all flex items-center justify-center gap-2 group cursor-pointer border border-blue-600 active:scale-[0.99]" id="btn-pay-now" type="button">
        <span class="material-symbols-outlined text-[20px] text-amber-300">lock</span>
        <span>Bayar Sekarang</span>
        <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
      </button>
    </div>
  </div>

  <!-- Trust & Guarantees Card -->
  <div class="rounded-2xl border border-slate-200 bg-white p-4 text-xs shadow-xs space-y-3">
    <div class="flex items-center gap-2.5">
      <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold shadow-xs border border-blue-200 shrink-0">
        <span class="material-symbols-outlined text-[20px]">verified</span>
      </div>
      <div>
        <div class="font-display font-bold text-slate-900 text-sm">Jaminan Layanan <?= esc($currentStoreName) ?></div>
        <div class="text-slate-500 text-[11px]">Keamanan &amp; kenyamanan transaksi terverifikasi</div>
      </div>
    </div>
    <ul class="space-y-1.5 text-slate-600 text-[11px] pl-1">
      <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span><span>Proses instan otomatis 1–3 detik 24/7</span></li>
      <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span><span>Harga transparan bebas biaya tersembunyi</span></li>
      <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span><span>CS WhatsApp aktif membantu kendala Anda</span></li>
    </ul>
    <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
      <span class="text-[11px] text-slate-500">Ada pertanyaan?</span>
      <a class="inline-flex items-center gap-1 font-bold text-blue-600 hover:text-blue-700 transition-colors" href="<?= esc($waUrl) ?>" rel="noopener noreferrer" target="_blank">
        <span>Hubungi CS</span>
        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
      </a>
    </div>
  </div>

</div><!-- END RIGHT COLUMN -->

</div><!-- END VIEW MODE BUY -->
