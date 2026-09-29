<!-- VIEW MODE: JUAL / BONGKAR -->
<?php
  $storeSettingModelSell = new \App\Models\StoreSettingModel();
  $sellStoreContact = $storeSettingModelSell->getVal('store_contact');
  $sellWaUrl = whatsapp_url($sellStoreContact);
  $hasBongkarCatalog = ! empty($bongkarCatalogs);

  $sellRing  = 'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600';
  $sellInput = 'w-full min-h-11 rounded-xl border border-slate-300 bg-slate-50 py-2.5 pr-3 text-base sm:text-sm text-slate-900 placeholder:font-medium placeholder:text-slate-500 outline-none transition-all focus:border-amber-600 focus:bg-white focus:ring-2 focus:ring-amber-200';
?>
<div id="view-mode-sell" class="flex flex-col lg:flex-row gap-6 items-start tab-mode-hidden">

<?php if (! $hasBongkarCatalog): ?>

<!-- EMPTY STATE: belum ada katalog bongkar dari admin -->
<div class="w-full max-w-xl mx-auto py-4">
  <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm text-center" data-reveal>
    <div class="mx-auto flex size-12 items-center justify-center rounded-xl border border-amber-200/80 bg-amber-50 text-amber-700 shadow-xs">
      <span class="material-symbols-outlined text-[24px]" aria-hidden="true">currency_exchange</span>
    </div>
    <h3 class="mt-3.5 font-display font-bold text-base text-slate-900">Layanan Bongkar Belum Tersedia</h3>
    <p class="mx-auto mt-1 max-w-sm text-sm leading-relaxed text-slate-700">Saat ini belum ada katalog jenis koin atau kartu yang dibuka untuk dijual.<?= $sellWaUrl !== '' ? ' Hubungi Customer Service untuk info ketersediaan dan rate terbaru.' : ' Silakan kembali lagi nanti.' ?></p>
    <?php if ($sellWaUrl !== ''): ?>
    <a href="<?= esc($sellWaUrl) ?>" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-emerald-600/30 bg-emerald-700 px-5 text-sm font-bold text-white shadow-xs transition-all hover:bg-emerald-800 active:scale-95 <?= $sellRing ?>">
      <span class="material-symbols-outlined text-[18px]" aria-hidden="true">chat</span>
      <span>Hubungi CS WhatsApp</span>
    </a>
    <?php endif; ?>
  </section>
</div>

<?php else: ?>

<!-- LEFT COLUMN: Form Bongkar -->
<div class="w-full lg:w-7/12 xl:w-8/12 min-w-0">
  <section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs relative overflow-hidden" data-reveal aria-labelledby="sell-title">
    <!-- Section Header -->
    <div class="flex items-start sm:items-center gap-3 mb-5 pb-4 border-b border-slate-100">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-yellow-500 text-neutral-950 flex items-center justify-center shadow-xs shrink-0">
        <span class="material-symbols-outlined text-[20px]" aria-hidden="true">currency_exchange</span>
      </div>
      <div>
        <h2 id="sell-title" class="font-display font-bold text-base sm:text-lg text-slate-900 leading-tight">Jual atau Bongkar Kartu / Koin</h2>
        <p class="text-xs text-slate-600 mt-0.5">Ajukan kartu atau koin yang ingin dijual. Dana dicairkan ke rekening atau e-wallet yang Anda isi.</p>
      </div>
    </div>

    <div class="space-y-5 text-xs">
      <!-- 1. Choice of Card -->
      <fieldset class="space-y-2">
        <legend class="mb-2 font-bold text-slate-800">1. Pilih Jenis Kartu / Koin <span class="text-rose-600">*</span></legend>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5" role="radiogroup" aria-label="Jenis kartu atau koin">
          <?php foreach ($bongkarCatalogs as $index => $catalog): ?>
            <?php
              $rate = (float) $catalog['base_rate'];
              $rateLabel = 'Rp' . number_format($rate, 0, ',', '.');
              $isSelected = $index === 0;
            ?>
            <button type="button" role="radio" aria-checked="<?= $isSelected ? 'true' : 'false' ?>" class="bongkar-card group <?= $isSelected ? 'selected border-2 border-amber-500 bg-amber-50/70 ring-2 ring-amber-400/20' : 'border border-slate-200 bg-white hover:border-amber-400' ?> relative min-h-[76px] p-3.5 pr-8 rounded-2xl text-left transition-all cursor-pointer shadow-2xs hover:shadow-md hover:-translate-y-0.5 active:scale-[0.98] <?= $sellRing ?>" data-bongkar-catalog-id="<?= esc($catalog['id']) ?>" data-label="<?= esc($catalog['name']) ?>" data-rate="<?= esc($rate) ?>" data-unit="<?= esc($catalog['unit_label']) ?>">
              <span class="block font-display font-black text-slate-900 text-sm group-hover:text-amber-700 transition-colors"><?= esc($catalog['name']) ?></span>
              <span class="mt-2.5 block border-t border-slate-100 pt-2 font-mono text-xs font-black text-amber-700"><?= esc($rateLabel) ?> / <?= esc($catalog['unit_label']) ?></span>
              <span class="check-mark absolute right-2.5 top-2.5 flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-xs font-bold text-neutral-950 opacity-0 transition-opacity group-[.selected]:opacity-100" aria-hidden="true">✓</span>
            </button>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <!-- 2. Inputs Row -->
      <fieldset class="space-y-2">
        <legend class="mb-2 font-bold text-slate-800">2. Detail Pengajuan <span class="text-rose-600">*</span></legend>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <!-- Quantity -->
          <div class="space-y-1.5">
            <label class="block font-bold text-slate-800" for="input-card-qty">Jumlah (<span id="bongkar-unit-label">kartu</span>) <span class="text-rose-600">*</span></label>
            <div class="flex items-center overflow-hidden rounded-xl border border-slate-300 bg-slate-50 transition-all focus-within:border-amber-600 focus-within:bg-white focus-within:ring-2 focus-within:ring-amber-200">
              <button type="button" id="btn-qty-minus" aria-label="Kurangi jumlah" class="flex h-11 w-11 shrink-0 cursor-pointer items-center justify-center text-lg font-bold text-slate-700 transition-colors hover:bg-slate-200 active:scale-90 <?= $sellRing ?> focus-visible:-outline-offset-2">&minus;</button>
              <input id="input-card-qty" type="number" inputmode="numeric" min="1" max="1000" value="1" class="min-w-0 flex-1 border-none bg-transparent py-2.5 text-center font-mono text-base font-bold text-slate-900 outline-none focus:ring-0 sm:text-sm">
              <button type="button" id="btn-qty-plus" aria-label="Tambah jumlah" class="flex h-11 w-11 shrink-0 cursor-pointer items-center justify-center text-lg font-bold text-slate-700 transition-colors hover:bg-slate-200 active:scale-90 <?= $sellRing ?> focus-visible:-outline-offset-2">+</button>
            </div>
          </div>

          <!-- User ID Game -->
          <div class="space-y-1.5">
            <label class="block font-bold text-slate-800" for="input-card-game-id">User ID Game <span class="font-normal text-slate-600">(opsional)</span></label>
            <div class="relative">
              <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-500" aria-hidden="true">sports_esports</span>
              <input id="input-card-game-id" type="text" inputmode="numeric" autocomplete="off" placeholder="ID Game Anda" class="<?= $sellInput ?> pl-9 font-mono font-bold">
            </div>
          </div>

          <!-- WA Seller -->
          <div class="space-y-1.5">
            <label class="block font-bold text-slate-800" for="input-sell-wa">Nomor WhatsApp <span class="text-rose-600">*</span></label>
            <div class="relative">
              <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-emerald-700" aria-hidden="true">chat</span>
              <input id="input-sell-wa" type="tel" inputmode="tel" autocomplete="tel" placeholder="08xxxxxxxxxx" class="<?= $sellInput ?> pl-9 font-mono font-bold">
            </div>
            <p class="text-xs text-slate-600">Kami mengirim update pesanan ke nomor WhatsApp ini.</p>
          </div>
        </div>
      </fieldset>

      <!-- 3. Destination Payout Account -->
      <fieldset class="space-y-2">
        <legend class="mb-2 font-bold text-slate-800">3. Rekening / E-Wallet Tujuan Pencairan Dana <span class="text-rose-600">*</span></legend>

        <?php if (! empty($payoutMethods)): ?>
          <div class="flex flex-wrap gap-2" id="bongkar-payout-grid" role="radiogroup" aria-label="Metode pencairan">
            <?php foreach ($payoutMethods as $pIndex => $payout): ?>
              <button type="button" role="radio" aria-checked="<?= $pIndex === 0 ? 'true' : 'false' ?>" class="bongkar-payout-btn <?= $pIndex === 0 ? 'selected border-2 border-amber-500 bg-amber-50 font-bold text-amber-900' : 'border border-slate-200 bg-white hover:border-amber-300 text-slate-700' ?> min-h-11 px-4 py-2 rounded-lg text-xs sm:text-sm transition-all cursor-pointer active:scale-95 <?= $sellRing ?>" data-method="<?= esc($payout['code']) ?>">
                <?= esc($payout['name']) ?>
              </button>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-3 text-sm text-slate-700" id="payout-empty">Belum ada metode pencairan yang aktif, jadi pengajuan belum bisa dikirim<?= $sellWaUrl !== '' ? '. Silakan tanyakan ke CS' : '' ?>.</p>
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
          <div class="space-y-1">
            <label class="block text-xs font-semibold text-slate-800" for="input-payout-account">Nomor Rekening / E-Wallet <span class="text-rose-600">*</span></label>
            <input id="input-payout-account" type="text" inputmode="numeric" autocomplete="off" placeholder="Contoh: 1234567890" class="<?= $sellInput ?> pl-3 font-mono font-bold">
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-semibold text-slate-800" for="input-payout-name">Nama Pemilik Rekening <span class="text-rose-600">*</span></label>
            <input id="input-payout-name" type="text" autocomplete="name" placeholder="Sesuai nama di buku tabungan/e-wallet" class="<?= $sellInput ?> pl-3 font-bold">
          </div>
        </div>
      </fieldset>
    </div>
  </section>
</div>

<!-- RIGHT COLUMN: Summary & Action Card -->
<div class="w-full lg:w-5/12 xl:w-4/12 lg:sticky lg:top-24 space-y-4 shrink-0">
  <div class="bg-white rounded-2xl border-2 border-slate-200 shadow-md overflow-hidden relative" data-reveal style="--rd: 120ms">
    <div class="bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-500 p-4 text-neutral-950 relative border-b border-amber-300">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-neutral-950 text-amber-400 flex items-center justify-center font-bold shadow-xs shrink-0">
          <span class="material-symbols-outlined text-[20px]" aria-hidden="true">payments</span>
        </div>
        <div>
          <h3 class="font-display font-black text-sm tracking-wide">Ringkasan Bongkar</h3>
          <p class="text-xs font-semibold text-neutral-900">Estimasi dari rate patokan</p>
        </div>
      </div>
    </div>

    <div class="p-4 space-y-4 text-xs">
      <dl class="bg-slate-50/80 rounded-xl p-3 space-y-2 border border-slate-100 text-xs">
        <div class="flex justify-between items-center gap-3">
          <dt class="text-slate-600">Item Bongkar:</dt>
          <dd class="rounded px-1 text-right font-bold text-slate-900" id="bongkar-receipt-label">Belum memilih item</dd>
        </div>
        <div class="flex justify-between items-center gap-3">
          <dt class="text-slate-600">Rate Patokan:</dt>
          <dd class="rounded px-1 font-mono font-bold text-amber-700" id="bongkar-receipt-rate">-</dd>
        </div>
        <div class="flex justify-between items-center gap-3">
          <dt class="text-slate-600">Jumlah Diajukan:</dt>
          <dd class="rounded px-1 font-mono font-bold text-slate-900" id="bongkar-receipt-qty">-</dd>
        </div>
        <div class="flex justify-between items-center gap-3">
          <dt class="text-slate-600">Metode Pencairan:</dt>
          <dd class="rounded px-1 font-bold text-blue-700" id="bongkar-receipt-payout">-</dd>
        </div>
        <div class="flex justify-between items-center gap-3">
          <dt class="text-slate-600">No. WhatsApp:</dt>
          <dd class="rounded px-1 font-mono font-semibold text-slate-900" id="bongkar-receipt-wa">-</dd>
        </div>
      </dl>

      <div class="border-b-2 border-dashed border-slate-200 my-1"></div>

      <!-- Estimated Payout Total -->
      <div>
        <span class="block text-xs font-semibold text-slate-600">Estimasi Dana Diterima</span>
        <div class="text-3xl sm:text-4xl font-black text-amber-700 font-display flex items-baseline gap-1 tracking-tight tabular-nums" id="bongkar-estimated">Rp0</div>
        <p class="mt-0.5 text-xs text-slate-600">Jumlah dikali rate patokan.</p>
      </div>

      <button class="w-full min-h-12 py-3.5 px-4 rounded-xl bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-neutral-950 font-display font-black text-base shadow-md transition-all flex items-center justify-center gap-2 group cursor-pointer border border-amber-400 active:scale-[0.99] disabled:cursor-wait disabled:opacity-70 <?= $sellRing ?>" id="btn-submit-bongkar" type="button">
        <span class="material-symbols-outlined text-[20px] group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" aria-hidden="true" id="btn-submit-bongkar-icon">send</span>
        <span id="btn-submit-bongkar-label">Kirim Pengajuan Bongkar</span>
      </button>
    </div>
  </div>
</div>

<?php endif; ?>

</div><!-- END VIEW MODE SELL -->
