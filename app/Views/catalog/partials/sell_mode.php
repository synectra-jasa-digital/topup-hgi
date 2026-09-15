<!-- VIEW MODE: JUAL / BONGKAR -->
<?php
  $storeSettingModelSell = new \App\Models\StoreSettingModel();
  $sellStoreContact = $storeSettingModelSell->getVal('store_contact');
  $sellWaNum = ! empty($sellStoreContact) ? preg_replace('/[^0-9]/', '', $sellStoreContact) : '';
  $sellWaUrl = ! empty($sellWaNum) ? 'https://wa.me/' . $sellWaNum : 'https://wa.me/';
  $hasBongkarCatalog = ! empty($bongkarCatalogs);
?>
<div id="view-mode-sell" class="flex flex-col lg:flex-row gap-6 items-start tab-mode-hidden">

<?php if (! $hasBongkarCatalog): ?>

<!-- EMPTY STATE: belum ada katalog bongkar dari admin -->
<div class="w-full max-w-xl mx-auto py-4">
  <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm text-center">
    <div class="mx-auto rounded-xl bg-amber-50 text-amber-600 border border-amber-200/80 flex items-center justify-center shadow-xs" style="width: 48px; height: 48px; min-width: 48px; min-height: 48px;">
      <span class="material-symbols-outlined text-[24px]">currency_exchange</span>
    </div>
    <h3 class="mt-3.5 font-display font-bold text-base text-slate-900">Layanan Bongkar Belum Tersedia</h3>
    <p class="mt-1 text-xs text-slate-500 leading-relaxed max-w-sm mx-auto">Saat ini belum ada katalog jenis koin atau kartu yang dibuka untuk dijual. Silakan hubungi Customer Service kami untuk info ketersediaan dan rate transaksi terbaru.</p>
    <a href="<?= esc($sellWaUrl) ?>" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-all shadow-xs border border-emerald-500/30">
      <span class="material-symbols-outlined text-[16px]">chat</span>
      <span>Hubungi CS WhatsApp</span>
    </a>
  </section>
</div>

<?php else: ?>

<!-- LEFT COLUMN: Form Bongkar -->
<div class="w-full lg:w-7/12 xl:w-8/12 min-w-0">
  <section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xs relative overflow-hidden">
    <!-- Section Header -->
    <div class="flex items-start sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-yellow-500 text-neutral-950 font-display font-black flex items-center justify-center text-base shadow-xs shrink-0">
          <span class="material-symbols-outlined text-[20px]">currency_exchange</span>
        </div>
        <div>
          <h2 class="font-display font-bold text-base sm:text-lg text-slate-900 leading-tight">Jual atau Bongkar Kartu / Koin</h2>
          <p class="text-xs text-slate-500 mt-0.5">Tukar kartu atau koin game Anda menjadi uang tunai langsung ke rekening/e-wallet.</p>
        </div>
      </div>
      <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-300 shrink-0">
        <span class="material-symbols-outlined text-[14px] text-emerald-600">verified</span> Pencairan Dana Langsung
      </span>
    </div>

    <div class="space-y-5 text-xs">
      <!-- 1. Choice of Card -->
      <div class="space-y-2">
        <label class="block font-bold text-slate-800">1. Pilih Jenis Kartu / Koin <span class="text-rose-500">*</span></label>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
          <?php foreach ($bongkarCatalogs as $index => $catalog): ?>
            <?php
              $rate = (float) $catalog['base_rate'];
              $rateLabel = 'Rp' . number_format($rate, 0, ',', '.');
              $isSelected = $index === 0;
            ?>
            <button type="button" class="bongkar-card <?= $isSelected ? 'selected border-2 border-amber-500 bg-amber-50/70 ring-2 ring-amber-400/20' : 'border border-slate-200 bg-white hover:border-amber-400' ?> p-3.5 rounded-2xl text-left transition-all relative cursor-pointer shadow-2xs hover:shadow-md hover:-translate-y-0.5 group" data-bongkar-catalog-id="<?= esc($catalog['id']) ?>" data-label="<?= esc($catalog['name']) ?>" data-rate="<?= esc($rate) ?>" data-unit="<?= esc($catalog['unit_label']) ?>">
              <div class="flex items-center justify-between">
                <span class="font-display font-black text-slate-900 text-xs sm:text-sm group-hover:text-amber-600 transition-colors"><?= esc($catalog['name']) ?></span>
              </div>
              <div class="mt-2.5 pt-2 border-t border-slate-100 font-black text-amber-600 font-mono text-xs"><?= esc($rateLabel) ?> / <?= esc($catalog['unit_label']) ?></div>
            </button>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 2. Inputs Row -->
      <div class="space-y-2">
        <label class="block font-bold text-slate-800">2. Detail Pengajuan <span class="text-rose-500">*</span></label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <!-- Quantity -->
          <div class="space-y-1.5">
            <label class="block font-bold text-slate-700" for="input-card-qty">Jumlah (<span id="bongkar-unit-label">kartu</span>) <span class="text-rose-500">*</span></label>
            <div class="flex items-center rounded-xl border border-slate-300 bg-slate-50 overflow-hidden focus-within:border-amber-500 focus-within:bg-white transition-all">
              <button type="button" id="btn-qty-minus" class="w-10 h-9 flex items-center justify-center text-slate-600 hover:bg-slate-200 font-bold text-base transition-colors shrink-0 cursor-pointer">-</button>
              <input id="input-card-qty" type="number" min="1" value="1" class="flex-1 text-center py-1.5 text-sm bg-transparent outline-none font-mono font-bold text-slate-900 border-none focus:ring-0 min-w-0">
              <button type="button" id="btn-qty-plus" class="w-10 h-9 flex items-center justify-center text-slate-600 hover:bg-slate-200 font-bold text-base transition-colors shrink-0 cursor-pointer">+</button>
            </div>
          </div>

          <!-- User ID Game -->
          <div class="space-y-1.5">
            <label class="block font-bold text-slate-700" for="input-card-game-id">User ID Game <span class="text-[10px] font-normal text-slate-400">(Opsional)</span></label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-2 text-[18px] text-slate-400">sports_esports</span>
              <input id="input-card-game-id" type="text" placeholder="ID Game Anda" class="w-full pl-9 pr-3 py-1.5 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-amber-500 focus:bg-white outline-none transition-all font-mono font-bold text-slate-900 placeholder:text-slate-400">
            </div>
          </div>

          <!-- WA Seller -->
          <div class="space-y-1.5">
            <label class="block font-bold text-slate-700" for="input-sell-wa">Nomor WhatsApp <span class="text-rose-500">*</span></label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-2 text-[18px] text-emerald-600">chat</span>
              <input id="input-sell-wa" type="tel" placeholder="08xxxxxxxxxx" class="w-full pl-9 pr-3 py-1.5 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-amber-500 focus:bg-white outline-none transition-all font-mono font-bold text-slate-900 placeholder:text-slate-400">
            </div>
          </div>
        </div>
      </div>

      <!-- 3. Destination Payout Account -->
      <div class="space-y-2">
        <label class="block font-bold text-slate-800">3. Rekening / E-Wallet Tujuan Pencairan Dana <span class="text-rose-500">*</span></label>
        
        <!-- Payout Bank Badges -->
        <?php if (! empty($payoutMethods)): ?>
          <div class="flex flex-wrap gap-2" id="bongkar-payout-grid">
            <?php foreach ($payoutMethods as $pIndex => $payout): ?>
              <button type="button" class="bongkar-payout-btn <?= $pIndex === 0 ? 'selected border-2 border-amber-500 bg-amber-50 font-bold text-amber-900' : 'border border-slate-200 bg-white hover:border-amber-300 text-slate-700' ?> px-3 py-1.5 rounded-lg text-xs transition-all cursor-pointer" data-method="<?= esc($payout['code']) ?>">
                <?= esc($payout['name']) ?>
              </button>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-[11px] text-slate-500 italic">Belum ada metode pencairan aktif. Hubungi CS untuk bantuan.</p>
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
          <div class="space-y-1">
            <label class="block text-[11px] font-semibold text-slate-600" for="input-payout-account">Nomor Rekening / E-Wallet <span class="text-rose-500">*</span></label>
            <input id="input-payout-account" type="text" placeholder="Contoh: 1234567890" class="w-full px-3 py-1.5 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-amber-500 focus:bg-white outline-none font-mono font-bold text-slate-900">
          </div>
          <div class="space-y-1">
            <label class="block text-[11px] font-semibold text-slate-600" for="input-payout-name">Nama Pemilik Rekening <span class="text-rose-500">*</span></label>
            <input id="input-payout-name" type="text" placeholder="Sesuai nama di buku tabungan/e-wallet" class="w-full px-3 py-1.5 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-amber-500 focus:bg-white outline-none font-bold text-slate-900">
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- RIGHT COLUMN: Summary & Action Card -->
<div class="w-full lg:w-5/12 xl:w-4/12 lg:sticky lg:top-24 space-y-4 shrink-0">
  <div class="bg-white rounded-2xl border-2 border-slate-200 shadow-md overflow-hidden relative">
    <div class="bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-500 p-4 text-neutral-950 relative border-b border-amber-300">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-neutral-950 text-amber-400 flex items-center justify-center font-bold shadow-xs shrink-0">
            <span class="material-symbols-outlined text-[20px]">payments</span>
          </div>
          <div>
            <h3 class="font-display font-black text-sm tracking-wide">Ringkasan Bongkar</h3>
            <p class="text-[10px] text-neutral-800 font-semibold">Estimasi pencairan dana langsung</p>
          </div>
        </div>
        <span class="px-2 py-0.5 rounded text-[10px] font-black bg-neutral-950 text-amber-300">LIVE RATE</span>
      </div>
    </div>

    <div class="p-4 space-y-4 text-xs">
      <div class="bg-slate-50/80 rounded-xl p-3 space-y-2 border border-slate-100 text-[11px]">
        <div class="flex justify-between items-center">
          <span class="text-slate-500">Item Bongkar:</span>
          <span class="font-bold text-slate-900" id="bongkar-receipt-label">Belum memilih item</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-slate-500">Rate Patokan:</span>
          <span class="font-mono font-bold text-amber-600" id="bongkar-receipt-rate">-</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-slate-500">Jumlah Diajukan:</span>
          <span class="font-mono font-bold text-slate-900" id="bongkar-receipt-qty">-</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-slate-500">Metode Pencairan:</span>
          <span class="font-bold text-blue-700" id="bongkar-receipt-payout">-</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-slate-500">No. WA Penjual:</span>
          <span class="font-mono font-semibold text-slate-900" id="bongkar-receipt-wa">-</span>
        </div>
      </div>

      <div class="border-b-2 border-dashed border-slate-200 my-1"></div>

      <!-- Estimated Payout Total -->
      <div class="flex items-end justify-between">
        <div>
          <span class="text-[10px] uppercase font-black tracking-wider text-slate-500 block">Estimasi Dana Diterima</span>
          <div class="text-3xl sm:text-4xl font-black text-amber-600 font-display flex items-baseline gap-1 tracking-tight" id="bongkar-estimated">Rp0</div>
        </div>
        <div class="text-right">
          <span class="text-[10px] font-black text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-300 block">Pencairan Cepat</span>
        </div>
      </div>

      <button class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-neutral-950 font-display font-black text-base shadow-md transition-all flex items-center justify-center gap-2 group cursor-pointer border border-amber-400 active:scale-[0.99]" id="btn-submit-bongkar" type="button">
        <span class="material-symbols-outlined text-[20px]">send</span>
        <span>Kirim Pengajuan Bongkar</span>
      </button>
    </div>
  </div>
</div>

<?php endif; ?>

</div><!-- END VIEW MODE SELL -->
