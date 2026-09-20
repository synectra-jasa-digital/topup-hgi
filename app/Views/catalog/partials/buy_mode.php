<!-- VIEW MODE: BELI / TOP UP -->
<?php
  $storeSettingModel = new \App\Models\StoreSettingModel();
  $currentStoreName = $storeSettingModel->getVal('store_name', 'Ayong Store');
  $waUrl = whatsapp_url($adminWhatsapp ?? '');

  // Checkout is a plain POST that redirects back here on failure, so the server's reasons are shown at the top.
  $flashMessages = array_values(array_filter(array_merge(
      [session()->getFlashdata('error')],
      array_values((array) session()->getFlashdata('errors'))
  )));

  // Fallback glyph for a category that has no uploaded icon. An uploaded icon (product_categories.icon) always wins.
  $categoryGlyph = static fn (string $slug): array => match ($slug) {
      'koin-emas'  => ['icon' => 'monetization_on', 'color' => 'text-amber-600'],
      'kartu-emas' => ['icon' => 'credit_card', 'color' => 'text-amber-700'],
      'koin-md'    => ['icon' => 'diamond', 'color' => 'text-sky-700'],
      'kartu-ungu' => ['icon' => 'workspace_premium', 'color' => 'text-purple-700'],
      default      => ['icon' => 'sports_esports', 'color' => 'text-slate-600'],
  };

  $focusRing = 'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600';
  $inputBase = 'w-full min-h-11 rounded-xl border border-slate-300 bg-slate-50 py-2.5 pr-3 text-base sm:text-sm text-slate-900 placeholder:font-medium placeholder:text-slate-500 outline-none transition-all focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-200';
?>
<div id="view-mode-buy" class="flex flex-col lg:flex-row gap-6 items-start">

<!-- LEFT COLUMN: 4-Step Ordering Wizard -->
<div class="w-full lg:w-7/12 xl:w-8/12 space-y-4 min-w-0">

<?php if ($flashMessages !== []): ?>
<div class="rounded-2xl border border-rose-300 bg-rose-50 p-4 text-sm text-rose-900" role="alert">
  <p class="flex items-center gap-2 font-display font-bold">
    <span class="material-symbols-outlined text-[20px] text-rose-700" aria-hidden="true">error</span>
    Pesanan belum dibuat
  </p>
  <ul class="mt-2 list-disc space-y-0.5 pl-9 text-rose-900">
    <?php foreach ($flashMessages as $message): ?>
      <li><?= esc($message) ?></li>
    <?php endforeach; ?>
  </ul>
  <p class="mt-2 pl-9 text-rose-900">Pilih nominal lagi, periksa datanya, lalu coba bayar kembali.</p>
</div>
<?php endif; ?>

<!-- STEP 1: PILIH KATEGORI PRODUK -->
<section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs relative overflow-hidden transition-all hover:border-slate-300" data-reveal style="--rd: 0ms" aria-labelledby="step-1-title">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
    <div class="flex items-center gap-3.5">
      <div class="step-number-badge relative w-8 h-8 rounded-xl text-white font-display font-black flex items-center justify-center text-sm shrink-0" data-step="1"><span aria-hidden="true">1</span><span class="sr-only" data-step-status></span></div>
      <div>
        <h2 id="step-1-title" class="font-display font-bold text-base sm:text-lg text-slate-900 leading-tight">Pilih Kategori Produk</h2>
        <p class="text-xs text-slate-600">Pilih kategori koin atau jenis item yang ingin Anda beli</p>
      </div>
    </div>
    <!-- Quick Search Filter -->
    <div class="relative w-full sm:w-60">
      <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-500" aria-hidden="true">search</span>
      <input type="search" id="catalog-search-input" aria-label="Cari nominal" placeholder="Cari nominal (1B, 200M...)" autocomplete="off" class="<?= $inputBase ?> pl-9 font-sans font-medium">
    </div>
  </div>

  <!-- Category Pills Deck -->
  <?php if (! empty($categories)): ?>
  <div class="flex flex-wrap gap-2" id="category-pills" role="group" aria-label="Kategori produk">
    <?php foreach ($categories as $index => $category): ?>
      <?php $glyph = $categoryGlyph($category['slug']); ?>
      <button class="category-pill <?= $index === 0 ? 'active' : 'text-slate-700 bg-slate-100/90 hover:bg-slate-200/80 border-slate-200' ?> border inline-flex min-h-11 items-center justify-center gap-2 py-2 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all cursor-pointer shadow-2xs active:scale-95 <?= $focusRing ?>" data-cat="<?= esc($category['slug']) ?>" aria-pressed="<?= $index === 0 ? 'true' : 'false' ?>" type="button">
        <?php if (! empty($category['icon'])): ?>
          <img src="<?= base_url($category['icon']) ?>" alt="" class="h-[18px] w-[18px] shrink-0 object-contain">
        <?php else: ?>
          <span class="material-symbols-outlined pill-icon text-[18px] <?= $index === 0 ? 'text-amber-300' : $glyph['color'] ?>" aria-hidden="true"><?= esc($glyph['icon']) ?></span>
        <?php endif; ?>
        <span class="truncate"><?= esc($category['name']) ?></span>
      </button>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-700">Belum ada kategori produk yang dibuka. Silakan kembali lagi nanti<?= $waUrl !== '' ? ' atau tanyakan ke CS' : '' ?>.</p>
  <?php endif; ?>
</section>

<!-- STEP 2: PILIH NOMINAL PRODUK -->
<section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs relative" data-reveal style="--rd: 80ms" aria-labelledby="step-2-title">
  <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
    <div class="flex items-center gap-3.5">
      <div class="step-number-badge relative w-8 h-8 rounded-xl text-white font-display font-black flex items-center justify-center text-sm shrink-0" data-step="2"><span aria-hidden="true">2</span><span class="sr-only" data-step-status></span></div>
      <div>
        <h2 id="step-2-title" class="font-display font-bold text-base sm:text-lg text-slate-900 leading-tight">Pilih Nominal Top Up</h2>
        <p class="text-xs text-slate-600">Pilih nominal koin atau paket yang diinginkan</p>
      </div>
    </div>
  </div>

  <div class="space-y-4" id="product-grid">
    <?php if (! empty($sections)): ?>
      <?php foreach ($sections as $section): ?>
        <?php $sectionGlyph = $categoryGlyph($section['category']['slug']); ?>
        <div class="space-y-3" data-category-group="<?= esc($section['category']['slug']) ?>">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <?php if (! empty($section['category']['icon'])): ?>
                <img src="<?= base_url($section['category']['icon']) ?>" alt="" class="h-6 w-6 shrink-0 object-contain">
              <?php else: ?>
                <span class="material-symbols-outlined text-[24px] <?= $sectionGlyph['color'] ?>" aria-hidden="true"><?= esc($sectionGlyph['icon']) ?></span>
              <?php endif; ?>
              <h3 class="text-sm font-black text-slate-900"><?= esc($section['category']['name']) ?></h3>
            </div>
            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700 border border-slate-200"><?= count($section['products']) ?> pilihan</span>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <?php foreach ($section['products'] as $pIndex => $p): ?>
              <?php
                $sellPrice = (float) $p['sell_price'];
                $priceFormatted = 'Rp' . number_format($sellPrice, 0, ',', '.');
                $catIconUrl = ! empty($section['category']['icon']) ? base_url($section['category']['icon']) : '';
              ?>
              <button class="product-card group relative p-3.5 sm:p-4 rounded-xl border border-slate-200/90 bg-white hover:border-blue-500 hover:shadow-md hover:-translate-y-0.5 active:scale-[0.98] transition-all duration-200 text-left cursor-pointer flex flex-col justify-between min-h-[110px] <?= $focusRing ?>" style="--i: <?= (int) $pIndex ?>" data-id="<?= $p['id'] ?>" data-cat="<?= esc($section['category']['slug']) ?>" data-price="<?= $sellPrice ?>" data-title="<?= esc($p['name']) ?>" data-unit="<?= esc($p['nominal'] ?: $priceFormatted) ?>" data-icon="<?= esc($catIconUrl) ?>" aria-pressed="false" type="button">
                <div class="flex items-start justify-between gap-2">
                  <div class="min-w-0">
                    <div class="font-display font-black text-slate-900 text-sm sm:text-base leading-snug group-hover:text-blue-700 transition-colors"><?= esc($p['name']) ?></div>
                    <?php if (! empty($p['nominal'])): ?>
                      <div class="text-xs text-slate-600 font-medium mt-0.5"><?= esc($p['nominal']) ?></div>
                    <?php endif; ?>
                  </div>
                  <?php if ($catIconUrl !== ''): ?>
                    <img src="<?= esc($catIconUrl) ?>" alt="" loading="lazy" class="h-8 w-8 shrink-0 object-contain transition-transform duration-300 group-hover:scale-110 group-[.product-card-selected]:scale-110">
                  <?php else: ?>
                    <span class="material-symbols-outlined shrink-0 text-[28px] transition-transform duration-300 group-hover:scale-110 group-[.product-card-selected]:scale-110 <?= $sectionGlyph['color'] ?>" aria-hidden="true"><?= esc($sectionGlyph['icon']) ?></span>
                  <?php endif; ?>
                </div>

                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between">
                  <span class="text-sm sm:text-base font-black text-blue-700 font-display tracking-tight"><?= $priceFormatted ?></span>
                  <span class="check-mark w-5 h-5 rounded-full border border-slate-300 group-[.product-card-selected]:border-blue-600 group-[.product-card-selected]:bg-blue-600 text-white flex items-center justify-center text-xs font-bold opacity-0 group-[.product-card-selected]:opacity-100 transition-all" aria-hidden="true">✓</span>
                </div>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>

      <p id="catalog-search-empty" class="hidden rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-700" role="status">Tidak ada nominal yang cocok dengan pencarian Anda. Hapus kata kunci untuk melihat semua pilihan kategori ini.</p>
    <?php else: ?>
      <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-700">Belum ada produk yang dibuka untuk dibeli. Silakan kembali lagi nanti<?= $waUrl !== '' ? ' atau tanyakan ke CS' : '' ?>.</p>
    <?php endif; ?>
  </div>
</section>

<!-- STEP 3: MASUKKAN DATA USER ID GAME & KONTAK -->
<section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs relative" data-reveal style="--rd: 160ms" aria-labelledby="step-3-title">
  <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
    <div class="flex items-center gap-3.5">
      <div class="step-number-badge relative w-8 h-8 rounded-xl text-white font-display font-black flex items-center justify-center text-sm shrink-0" data-step="3"><span aria-hidden="true">3</span><span class="sr-only" data-step-status></span></div>
      <div>
        <h2 id="step-3-title" class="font-display font-bold text-base sm:text-lg text-slate-900 leading-tight">Data Akun &amp; WhatsApp</h2>
        <p class="text-xs text-slate-600">ID akun game tujuan pengiriman koin &amp; nomor WhatsApp untuk invoice</p>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <!-- Input ID Game -->
    <div class="space-y-1.5">
      <div class="flex items-center justify-between">
        <label class="block text-xs font-bold text-slate-800" for="input-user-id">
          User ID Game <span class="text-rose-600">*</span>
        </label>
        <button type="button" id="btn-guide-id" class="inline-flex min-h-11 items-center gap-1 rounded-lg px-2 text-xs font-bold text-blue-700 hover:text-blue-800 cursor-pointer <?= $focusRing ?>">
          <span class="material-symbols-outlined text-[16px]" aria-hidden="true">help</span> Cara Cek ID?
        </button>
      </div>
      <div class="relative">
        <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-500" aria-hidden="true">sports_esports</span>
        <input class="<?= $inputBase ?> pl-9 font-mono font-bold" id="input-user-id" placeholder="Contoh: 123456789" type="text" inputmode="numeric" autocomplete="off" aria-describedby="hint-user-id" value="<?= old('game_id') ?>">
      </div>
      <p class="text-xs text-slate-600" id="hint-user-id">Pastikan ID benar. Koin dikirim ke ID ini.</p>
    </div>

    <!-- Input WhatsApp -->
    <div class="space-y-1.5">
      <label class="flex min-h-11 items-end pb-0.5 text-xs font-bold text-slate-800 sm:min-h-0 sm:pb-0" for="input-whatsapp">
        <span>Nomor WhatsApp Pembeli <span class="text-rose-600">*</span></span>
      </label>
      <div class="relative">
        <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-emerald-700" aria-hidden="true">chat</span>
        <input class="<?= $inputBase ?> pl-9 font-mono font-bold" id="input-whatsapp" placeholder="08xxxxxxxxxx" type="tel" inputmode="tel" autocomplete="tel" aria-describedby="hint-whatsapp" value="<?= old('whatsapp_number') ?>">
      </div>
      <p class="text-xs text-slate-600" id="hint-whatsapp">Awali dengan 08 atau 628, tanpa spasi. Invoice dikirim ke nomor ini.</p>
    </div>
  </div>
</section>

<!-- STEP 4: PILIH METODE PEMBAYARAN -->
<section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs relative" data-reveal style="--rd: 240ms" aria-labelledby="step-4-title">
  <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
    <div class="flex items-center gap-3.5">
      <div class="step-number-badge relative w-8 h-8 rounded-xl text-white font-display font-black flex items-center justify-center text-sm shrink-0" data-step="4"><span aria-hidden="true">4</span><span class="sr-only" data-step-status></span></div>
      <div>
        <h2 id="step-4-title" class="font-display font-bold text-base sm:text-lg text-slate-900 leading-tight">Metode Pembayaran</h2>
        <p class="text-xs text-slate-600">Transfer bank atau QRIS. Bukti bayar diunggah setelah pesanan dibuat.</p>
      </div>
    </div>
  </div>

  <?php if (! empty($paymentChannels)): ?>
  <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5" role="radiogroup" aria-label="Metode pembayaran">
    <?php foreach ($paymentChannels as $index => $channel): ?>
      <button class="pay-method-card group relative min-h-[64px] p-3 pr-8 rounded-xl border border-slate-200 text-left hover:border-blue-400 hover:bg-slate-50 bg-white transition-all shadow-2xs cursor-pointer active:scale-[0.98] <?= $focusRing ?> <?= $index === 0 ? 'selected border-blue-600 bg-blue-50/70 ring-2 ring-blue-500/20' : '' ?>" data-channel-id="<?= (int) $channel['id'] ?>" data-method="<?= esc($channel['name']) ?>" role="radio" aria-checked="<?= $index === 0 ? 'true' : 'false' ?>" type="button">
        <div class="text-sm font-black text-slate-900 group-hover:text-blue-700"><?= esc($channel['name']) ?></div>
        <div class="text-xs text-slate-600"><?= $channel['type'] === 'qris' ? 'Scan QRIS' : esc($channel['account_number']) ?></div>
        <span class="check-mark absolute right-2.5 top-2.5 flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white opacity-0 transition-opacity group-[.selected]:opacity-100" aria-hidden="true">✓</span>
      </button>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4 text-sm text-slate-700" id="pay-empty">Belum ada metode pembayaran yang aktif, jadi pesanan belum bisa dibuat<?= $waUrl !== '' ? '. Silakan tanyakan ke CS' : '' ?>.</p>
  <?php endif; ?>
</section>

</div><!-- END LEFT COLUMN -->

<!-- RIGHT COLUMN: Sticky Order Summary -->
<div class="w-full lg:w-5/12 xl:w-4/12 lg:sticky lg:top-24 space-y-4 shrink-0">

  <!-- Order Summary -->
  <div class="bg-white rounded-2xl border-2 border-slate-200 shadow-md overflow-hidden relative" data-reveal style="--rd: 120ms">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 p-4 text-white relative border-b border-blue-800">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-white text-blue-700 flex items-center justify-center font-bold shadow-xs shrink-0">
          <span class="material-symbols-outlined text-[20px]" aria-hidden="true">receipt</span>
        </div>
        <div>
          <h3 class="font-display font-bold text-sm tracking-wide">Ringkasan Pesanan</h3>
          <p class="text-xs text-white">Berubah mengikuti pilihan Anda</p>
        </div>
      </div>
    </div>

    <div class="p-4 space-y-4 text-xs">
      <!-- Selected Product Box Preview -->
      <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
        <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 border border-blue-200/80 p-1" id="receipt-category-icon-container">
          <span class="material-symbols-outlined text-[22px]" aria-hidden="true">sports_esports</span>
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="font-display font-black text-slate-900 text-sm truncate" id="receipt-item-name">Belum memilih produk</h4>
          <p class="text-xs text-slate-600 truncate" id="receipt-unit-rate">Silakan pilih nominal produk di samping</p>
        </div>
        <div class="text-right shrink-0">
          <div class="text-sm font-black text-blue-700 font-display" id="receipt-item-price">Rp0</div>
        </div>
      </div>

      <!-- Account Summary -->
      <dl class="bg-slate-50/80 rounded-xl p-3 space-y-2 border border-slate-100 text-xs">
        <div class="flex justify-between items-center gap-3">
          <dt class="text-slate-600">User ID Game:</dt>
          <dd class="rounded px-1 font-mono font-bold text-slate-900" id="receipt-user-id">-</dd>
        </div>
        <div class="flex justify-between items-center gap-3">
          <dt class="text-slate-600">No. WhatsApp:</dt>
          <dd class="rounded px-1 font-mono font-semibold text-slate-900" id="receipt-wa">-</dd>
        </div>
        <div class="flex justify-between items-center gap-3">
          <dt class="text-slate-600">Metode Bayar:</dt>
          <dd class="rounded px-1 font-bold text-blue-700" id="receipt-method">-</dd>
        </div>
      </dl>

      <!-- Voucher: checked by the server when the order is created, the discount then shows on the invoice -->
      <div class="space-y-1.5">
        <label class="block text-xs font-semibold text-slate-800" for="receipt-promo-input">Kode Voucher <span class="font-normal text-slate-600">(opsional)</span></label>
        <div class="relative">
          <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-amber-700" aria-hidden="true">local_activity</span>
          <input class="<?= $inputBase ?> pl-9 font-mono font-bold uppercase" id="receipt-promo-input" placeholder="KODE VOUCHER" type="text" autocomplete="off" autocapitalize="characters" aria-describedby="promo-status" value="<?= old('voucher_code') ?>">
        </div>
        <p class="text-xs text-slate-600" id="promo-status">Kode dicek saat pesanan dibuat. Potongan harga tampil di invoice.</p>
      </div>

      <!-- Price Breakdown -->
      <dl class="space-y-1.5 pt-1 text-xs">
        <div class="flex justify-between text-slate-700">
          <dt>Harga Produk:</dt>
          <dd class="font-bold text-slate-900 font-mono" id="calc-subtotal">Rp0</dd>
        </div>
        <div class="flex justify-between text-slate-700">
          <dt>Biaya Layanan:</dt>
          <dd class="font-bold text-emerald-700 font-mono" id="calc-admin-fee">Rp0</dd>
        </div>
      </dl>

      <div class="border-b-2 border-dashed border-slate-200 my-1"></div>

      <!-- Grand Total -->
      <div class="pt-1">
        <span class="block text-xs font-semibold text-slate-600">Total Tagihan</span>
        <div class="text-3xl sm:text-4xl font-black text-blue-700 font-display flex items-baseline gap-1 tracking-tight tabular-nums" id="calc-grand-total">Rp0</div>
        <p class="mt-0.5 text-xs text-slate-600">Sebelum potongan voucher, tanpa biaya tambahan.</p>
      </div>

      <!-- Checkout Button -->
      <button class="w-full min-h-12 py-3.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-display font-black text-base shadow-md transition-all flex items-center justify-center gap-2 group cursor-pointer border border-blue-600 active:scale-[0.99] <?= $focusRing ?>" id="btn-pay-now" type="button">
        <span class="material-symbols-outlined text-[20px] text-amber-300" aria-hidden="true">lock</span>
        <span>Bayar Sekarang</span>
        <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform" aria-hidden="true">arrow_forward</span>
      </button>
    </div>
  </div>

  <!-- How the order works: only what the system really does -->
  <div class="rounded-2xl border border-slate-200 bg-white p-4 text-xs shadow-xs space-y-3" data-reveal style="--rd: 220ms">
    <div class="flex items-center gap-2.5">
      <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold shadow-xs border border-blue-200 shrink-0">
        <span class="material-symbols-outlined text-[20px]" aria-hidden="true">verified</span>
      </div>
      <div>
        <div class="font-display font-bold text-slate-900 text-sm">Alur pesanan di <?= esc($currentStoreName) ?></div>
        <div class="text-slate-600 text-xs">Dari pembayaran sampai koin masuk</div>
      </div>
    </div>
    <ul class="space-y-2 text-slate-700 text-xs pl-1">
      <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[16px] text-emerald-700" aria-hidden="true">check_circle</span><span>Bayar lewat transfer bank atau QRIS, lalu unggah bukti pembayaran.</span></li>
      <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[16px] text-emerald-700" aria-hidden="true">check_circle</span><span>Admin memverifikasi bukti sebelum pesanan diproses.</span></li>
      <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[16px] text-emerald-700" aria-hidden="true">check_circle</span><span>Invoice hanya bisa dibuka dengan token akses milik Anda.</span></li>
    </ul>
    <?php if ($waUrl !== ''): ?>
    <div class="pt-2 border-t border-slate-200 flex items-center justify-between">
      <span class="text-xs text-slate-600">Ada pertanyaan?</span>
      <a class="inline-flex min-h-11 items-center gap-1 rounded-lg px-2 font-bold text-blue-700 hover:text-blue-800 transition-colors <?= $focusRing ?>" href="<?= esc($waUrl) ?>" rel="noopener noreferrer" target="_blank">
        <span>Hubungi CS</span>
        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">arrow_forward</span>
      </a>
    </div>
    <?php endif; ?>
  </div>

</div><!-- END RIGHT COLUMN -->

</div><!-- END VIEW MODE BUY -->
