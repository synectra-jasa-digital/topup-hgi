<!-- VIEW MODE: JUAL / BONGKAR -->
<div id="view-mode-sell" class="flex flex-col lg:flex-row gap-6 items-start tab-mode-hidden">

<!-- LEFT COLUMN: Form Bongkar -->
<div class="w-full lg:w-7/12 xl:w-8/12 min-w-0">
  <section class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm relative overflow-hidden">
    <!-- Section Header -->
    <div class="flex items-start sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-slate-200">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-display font-black flex items-center justify-center text-base shadow-sm shrink-0">
          <span class="material-symbols-outlined text-[20px]">currency_exchange</span>
        </div>
        <div>
          <h2 class="font-display font-bold text-base sm:text-lg text-neutral-900 leading-tight">Jual atau Bongkar Kartu</h2>
          <p class="text-xs text-slate-500 mt-0.5">Tukar kartu Higgs Games Island menjadi saldo atau uang tunai langsung ke rekening/e-wallet.</p>
        </div>
      </div>
    </div>

    <div class="space-y-5 text-xs">
      <!-- 1. Choice of Card -->
      <div class="space-y-2">
        <label class="block font-bold text-neutral-800">1. Pilih Jenis Kartu / Koin <span class="text-rose-500">*</span></label>
        <?php if (! empty($bongkarCatalogs)): ?>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            <?php foreach ($bongkarCatalogs as $index => $catalog): ?>
              <?php
                $rate = (float) $catalog['base_rate'];
                $rateLabel = 'Rp' . number_format($rate, 0, ',', '.');
                $isSelected = $index === 0;
              ?>
              <button type="button" class="bongkar-card <?= $isSelected ? 'selected border-2 border-blue-600 bg-blue-50/70 ring-2 ring-blue-500/20' : 'border border-slate-200 bg-white hover:border-blue-300' ?> p-3 rounded-xl text-left transition-all relative cursor-pointer shadow-2xs" data-bongkar-catalog-id="<?= esc($catalog['id']) ?>" data-label="<?= esc($catalog['name']) ?>" data-rate="<?= esc($rate) ?>" data-unit="<?= esc($catalog['unit_label']) ?>">
                <?php if ($isSelected): ?>
                  <span class="absolute -top-2.5 right-2 px-2 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wide shadow-xs">Rekomendasi</span>
                <?php endif; ?>
                <div class="flex items-center justify-between">
                  <span class="font-display font-bold text-neutral-900 text-xs sm:text-sm"><?= esc($catalog['name']) ?></span>
                </div>
                <div class="mt-2 pt-1.5 border-t border-slate-100 font-bold text-blue-600 font-mono text-[11px]"><?= esc($rateLabel) ?> / <?= esc($catalog['unit_label']) ?></div>
              </button>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            <button type="button" class="bongkar-card selected border-2 border-blue-600 bg-blue-50/70 ring-2 ring-blue-500/20 p-3 rounded-xl text-left transition-all relative cursor-pointer shadow-2xs" data-bongkar-catalog-id="default-1" data-label="Kartu Ungu" data-rate="65000" data-unit="kartu">
              <span class="absolute -top-2.5 right-2 px-2 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-bold uppercase tracking-wide shadow-xs">Rekomendasi</span>
              <div class="flex items-center justify-between">
                <span class="font-display font-bold text-neutral-900 text-xs sm:text-sm">Kartu Ungu</span>
              </div>
              <div class="mt-2 pt-1.5 border-t border-slate-100 font-bold text-blue-600 font-mono text-[11px]">Rp65.000 / kartu</div>
            </button>
            <button type="button" class="bongkar-card border border-slate-200 bg-white hover:border-blue-300 p-3 rounded-xl text-left transition-all relative cursor-pointer shadow-2xs" data-label="Kartu Emas 30H" data-rate="110000" data-unit="kartu">
              <div class="flex items-center justify-between">
                <span class="font-display font-bold text-neutral-900 text-xs sm:text-sm">Kartu Emas 30H</span>
              </div>
              <div class="mt-2 pt-1.5 border-t border-slate-100 font-bold text-blue-600 font-mono text-[11px]">Rp110.000 / kartu</div>
            </button>
            <button type="button" class="bongkar-card border border-slate-200 bg-white hover:border-blue-300 p-3 rounded-xl text-left transition-all relative cursor-pointer shadow-2xs" data-label="Koin Emas 1B" data-rate="58000" data-unit="1B">
              <div class="flex items-center justify-between">
                <span class="font-display font-bold text-neutral-900 text-xs sm:text-sm">Koin Emas 1B</span>
              </div>
              <div class="mt-2 pt-1.5 border-t border-slate-100 font-bold text-blue-600 font-mono text-[11px]">Rp58.000 / 1B</div>
            </button>
            <button type="button" class="bongkar-card border border-slate-200 bg-white hover:border-blue-300 p-3 rounded-xl text-left transition-all relative cursor-pointer shadow-2xs" data-label="Kartu Emas 1H" data-rate="12000" data-unit="kartu">
              <div class="flex items-center justify-between">
                <span class="font-display font-bold text-neutral-900 text-xs sm:text-sm">Kartu Emas 1H</span>
              </div>
              <div class="mt-2 pt-1.5 border-t border-slate-100 font-bold text-blue-600 font-mono text-[11px]">Rp12.000 / kartu</div>
            </button>
          </div>
        <?php endif; ?>
      </div>

      <!-- 2. Inputs Row -->
      <div class="space-y-2">
        <label class="block font-bold text-neutral-800">2. Detail Pengajuan <span class="text-rose-500">*</span></label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <!-- Quantity -->
          <div class="space-y-1.5">
            <label class="block font-bold text-neutral-700" for="input-card-qty">Jumlah (<span id="bongkar-unit-label">kartu</span>) <span class="text-rose-500">*</span></label>
            <div class="flex items-center rounded-xl border border-slate-300 bg-slate-50 overflow-hidden focus-within:border-blue-600 focus-within:bg-white transition-all">
              <button type="button" id="btn-qty-minus" class="w-10 h-9 flex items-center justify-center text-slate-600 hover:bg-slate-200 font-bold text-base transition-colors shrink-0">-</button>
              <input id="input-card-qty" type="number" min="1" value="1" class="flex-1 text-center py-1.5 text-sm bg-transparent outline-none font-mono font-bold text-neutral-900 border-none focus:ring-0 min-w-0">
              <button type="button" id="btn-qty-plus" class="w-10 h-9 flex items-center justify-center text-slate-600 hover:bg-slate-200 font-bold text-base transition-colors shrink-0">+</button>
            </div>
          </div>

          <!-- User ID Game -->
          <div class="space-y-1.5">
            <label class="block font-bold text-neutral-700" for="input-card-game-id">User ID Game <span class="text-[10px] font-normal text-slate-400">(Opsional)</span></label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-2 text-[18px] text-slate-400">sports_esports</span>
              <input id="input-card-game-id" type="text" placeholder="ID Higgs Anda" class="w-full pl-9 pr-3 py-1.5 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-blue-600 focus:bg-white outline-none transition-all font-mono font-bold text-neutral-900 placeholder-slate-400">
            </div>
          </div>

          <!-- WA Seller -->
          <div class="space-y-1.5">
            <label class="block font-bold text-neutral-700" for="input-sell-wa">Nomor WhatsApp <span class="text-rose-500">*</span></label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-3 top-2 text-[18px] text-emerald-600">chat</span>
              <input id="input-sell-wa" type="tel" placeholder="08xxxxxxxxxx" value="081234567890" class="w-full pl-9 pr-3 py-1.5 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-blue-600 focus:bg-white outline-none transition-all font-mono font-bold text-neutral-900 placeholder-slate-400">
            </div>
          </div>
        </div>
      </div>

      <!-- 3. Payout Method -->
      <div class="space-y-2">
        <div class="flex items-center justify-between">
          <label class="block font-bold text-neutral-800">3. Rekening / E-Wallet Tujuan <span class="text-rose-500">*</span></label>
          <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Bebas Biaya Transfer</span>
        </div>
        <div class="grid grid-cols-3 sm:grid-cols-9 gap-1.5 text-center text-[10px] font-bold" id="payout-method-grid">
          <button type="button" data-bongkar-payout="BCA" class="bongkar-payout-btn py-2 px-1 rounded-lg border-2 border-blue-600 bg-blue-50/70 text-blue-700 cursor-pointer transition-all">BCA</button>
          <button type="button" data-bongkar-payout="BRI" class="bongkar-payout-btn py-2 px-1 rounded-lg border border-slate-200 bg-white hover:border-blue-300 text-slate-700 cursor-pointer transition-all">BRI</button>
          <button type="button" data-bongkar-payout="Mandiri" class="bongkar-payout-btn py-2 px-1 rounded-lg border border-slate-200 bg-white hover:border-blue-300 text-slate-700 cursor-pointer transition-all">Mandiri</button>
          <button type="button" data-bongkar-payout="BNI" class="bongkar-payout-btn py-2 px-1 rounded-lg border border-slate-200 bg-white hover:border-blue-300 text-slate-700 cursor-pointer transition-all">BNI</button>
          <button type="button" data-bongkar-payout="DANA" class="bongkar-payout-btn py-2 px-1 rounded-lg border border-slate-200 bg-white hover:border-blue-300 text-slate-700 cursor-pointer transition-all">DANA</button>
          <button type="button" data-bongkar-payout="GoPay" class="bongkar-payout-btn py-2 px-1 rounded-lg border border-slate-200 bg-white hover:border-blue-300 text-slate-700 cursor-pointer transition-all">GoPay</button>
          <button type="button" data-bongkar-payout="OVO" class="bongkar-payout-btn py-2 px-1 rounded-lg border border-slate-200 bg-white hover:border-blue-300 text-slate-700 cursor-pointer transition-all">OVO</button>
          <button type="button" data-bongkar-payout="ShopeePay" class="bongkar-payout-btn py-2 px-1 rounded-lg border border-slate-200 bg-white hover:border-blue-300 text-slate-700 cursor-pointer transition-all">ShopeePay</button>
          <button type="button" data-bongkar-payout="Seabank" class="bongkar-payout-btn py-2 px-1 rounded-lg border border-slate-200 bg-white hover:border-blue-300 text-slate-700 cursor-pointer transition-all">Seabank</button>
        </div>
      </div>

      <!-- 4. Account Detail Inputs -->
      <div class="space-y-2">
        <label class="block font-bold text-neutral-800">4. Info Rekening <span class="text-rose-500">*</span></label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div class="space-y-1.5">
            <label class="block font-bold text-neutral-700" for="input-payout-account">Nomor Rekening / HP <span class="text-rose-500">*</span></label>
            <input id="input-payout-account" type="text" placeholder="Contoh: 1234567890" class="w-full px-3 py-2 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-blue-600 focus:bg-white outline-none transition-all font-mono font-bold text-neutral-900 placeholder-slate-400">
          </div>
          <div class="space-y-1.5">
            <label class="block font-bold text-neutral-700" for="input-payout-name">Nama Pemilik Rekening <span class="text-rose-500">*</span></label>
            <input id="input-payout-name" type="text" placeholder="Nama sesuai rekening" class="w-full px-3 py-2 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-blue-600 focus:bg-white outline-none transition-all font-bold text-neutral-900 placeholder-slate-400">
          </div>
        </div>
      </div>
    </div>
  </section>
</div><!-- END LEFT COLUMN -->

<!-- RIGHT COLUMN: Ringkasan Ajuan Bongkar -->
<div class="w-full lg:w-5/12 xl:w-4/12 lg:sticky lg:top-24 space-y-4 shrink-0">
  <!-- Summary Card Bongkar -->
  <div class="bg-white rounded-2xl border-2 border-slate-200 shadow-md overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-700 via-blue-600 to-blue-700 p-4 text-white border-b border-indigo-800">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-white text-indigo-700 flex items-center justify-center font-bold shadow-sm shrink-0">
            <span class="material-symbols-outlined text-[20px]">currency_exchange</span>
          </div>
          <div>
            <div class="font-display font-bold text-sm tracking-wide text-white">Ringkasan Ajuan</div>
            <div class="text-[10px] text-indigo-100">Estimasi Pencairan Saldo</div>
          </div>
        </div>
        <div class="flex flex-col items-end">
          <span class="px-2.5 py-0.5 rounded-full bg-emerald-500 text-white font-mono text-[10px] font-bold uppercase tracking-wider shadow-xs">FAST RESPONSE</span>
          <span class="text-[10px] text-emerald-300 font-medium mt-0.5 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> 1-5 Menit</span>
        </div>
      </div>
    </div>
    <!-- Content -->
    <div class="p-5 space-y-4 text-sm">
      <!-- Selected Card Display -->
      <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 shadow-xs">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 via-blue-500 to-blue-600 flex flex-col items-center justify-center text-white shrink-0 shadow-sm border border-indigo-300">
          <span class="text-[11px] font-black leading-none">HGI</span>
          <span class="text-[9px] font-bold leading-none mt-0.5">CARD</span>
        </div>
        <div class="min-w-0 flex-1">
          <div class="text-[10px] font-black text-indigo-600 uppercase tracking-wide">Jenis Kartu</div>
          <div class="font-display font-extrabold text-neutral-900 text-sm truncate" id="bongkar-receipt-label">Kartu Ungu</div>
          <div class="text-[11px] text-slate-500" id="bongkar-receipt-rate">Rp65.000 / kartu</div>
        </div>
      </div>

      <!-- Detail Rows -->
      <div class="space-y-2 py-3 border-y border-dashed border-slate-200 text-xs">
        <div class="flex justify-between items-center">
          <span class="text-slate-500">Jumlah:</span>
          <span class="font-mono font-bold text-neutral-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200" id="bongkar-receipt-qty">1 kartu</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-slate-500">Rekening Tujuan:</span>
          <span class="font-bold text-blue-700" id="bongkar-receipt-payout">BCA</span>
        </div>
        <div class="flex justify-between items-center">
          <span class="text-slate-500">WhatsApp:</span>
          <span class="font-mono font-semibold text-neutral-800" id="bongkar-receipt-wa">081234567890</span>
        </div>
      </div>

      <!-- Decorative Line -->
      <div class="border-b-2 border-dotted border-slate-200"></div>

      <!-- Estimated Payout -->
      <div class="flex items-end justify-between pt-1">
        <div>
          <span class="text-[10px] uppercase font-black tracking-wider text-slate-500 block">Estimasi Netto Cair</span>
          <div class="text-2xl sm:text-3xl font-black text-blue-700 font-display" id="bongkar-estimated">Rp65.000</div>
        </div>
        <div class="text-right">
          <span class="text-[10px] font-black text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-300 block">Saldo 100% Bersih</span>
          <span class="text-[10px] text-slate-500 mt-0.5 block font-mono">Semua Bank &amp; E-Wallet</span>
        </div>
      </div>

      <!-- Submit Button -->
      <button type="button" id="btn-submit-bongkar" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-display font-black text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer border border-blue-700">
        <span class="material-symbols-outlined text-[20px] text-amber-300">chat</span>
        <span>Ajukan Bongkar via WA</span>
      </button>
    </div>
  </div>

  <!-- Guarantees Box -->
  <div class="rounded-2xl border border-slate-200 bg-white p-4 text-xs shadow-xs space-y-3">
    <div class="flex items-center gap-2.5">
      <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shadow-xs border border-emerald-200 shrink-0">
        <span class="material-symbols-outlined text-[20px]">verified_user</span>
      </div>
      <div>
        <div class="font-display font-bold text-neutral-900 text-sm">Pencairan Saldo Fast Response</div>
        <div class="text-slate-500 text-[11px]">Proses cepat 1-5 menit 24 jam</div>
      </div>
    </div>
    <ul class="space-y-1.5 text-slate-600 text-[11px] pl-1">
      <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span> Saldo 100% bersih</li>
      <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span> Semua Bank &amp; E-Wallet</li>
      <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span> CS WA Standby 24 Jam</li>
    </ul>
  </div>
</div><!-- END RIGHT COLUMN -->

</div><!-- END VIEW MODE SELL -->
