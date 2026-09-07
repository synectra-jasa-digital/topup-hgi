<!-- CSS HELPER FOR TABS & PILLS -->
<style>
  .tab-mode-hidden {
    display: none !important;
  }
  .category-pill.active .pill-icon {
    color: #ffffff !important;
  }
</style>

<!-- HERO BANNER FRAME -->
<?php $heroBanner = ! empty($banners) ? $banners[0] : null; ?>
<div class="max-w-[1360px] mx-auto px-4 sm:px-6 pt-5 pb-2">
  <div class="relative rounded-2xl overflow-hidden border-2 border-amber-400/80 shadow-lg bg-slate-900">
    <!-- Image Hero Frame -->
    <div class="relative h-64 sm:h-80 md:h-96 lg:h-[380px] w-full overflow-hidden">
      <?php if ($heroBanner): ?>
        <?php if (! empty($heroBanner['link_url'])): ?><a href="<?= esc($heroBanner['link_url']) ?>" target="_blank" rel="noopener noreferrer"><?php endif; ?>
        <img alt="Banner promo aktif" class="w-full h-full object-cover object-center transform hover:scale-105 transition-transform duration-700" src="<?= base_url($heroBanner['image_path']) ?>">
        <?php if (! empty($heroBanner['link_url'])): ?></a><?php endif; ?>
      <?php else: ?>
        <img alt="Higgs Games Island Hero Banner" class="w-full h-full object-cover object-center transform hover:scale-105 transition-transform duration-700" src="https://lh3.googleusercontent.com/aida/AEtjO1Xkmeor5HdJYUSrixZ-AzI0Nncuf7tYmyNxC1SwZdCDjOMU2BjepgpLXbad3fySmsQm7rP5nP-ptDLEIo2MMimWSzGcIHVkQSlrxiOr-zLVdB_OvX-VtyWrOJh0BvOZilFEiQ8h4Ck8egDDGp3p68c22YensCJWpq6l6pDVIJCn9oeXJMtojO-IKJOU47c-kgqr7XlYTNou8LADwr6yjDGsxmgUgT3SlDg5R8tjmBh1dHMjUbENtang-g">
      <?php endif; ?>
      <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-slate-950/85 via-transparent to-slate-950/60"></div>
      
      <!-- Hero Badges & Content Overlay -->
      <div class="absolute bottom-6 left-5 sm:bottom-8 sm:left-8 right-5 sm:right-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div class="space-y-3 max-w-2xl">
          <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-600 text-white text-xs font-semibold tracking-wide shadow-sm">
            <span class="material-symbols-outlined text-[15px]">verified</span> Top Up Resmi &amp; Terpercaya
          </div>
          <h1 class="text-2xl sm:text-4xl md:text-5xl font-display font-black text-white drop-shadow-[0_2px_8px_rgba(0,0,0,0.8)]">Top Up Koin &amp; Produk Higgs Games Island</h1>
          <p class="text-xs sm:text-sm md:text-base text-slate-200 font-normal leading-relaxed drop-shadow-md max-w-xl">Layanan top up koin emas, koin MD, dan kartu VIP resmi terpercaya dengan proses kilat tanpa login akun.</p>
        </div>
        <div class="hidden lg:flex flex-col items-end gap-1.5 bg-slate-900/80 backdrop-blur-md px-5 py-3.5 rounded-2xl border border-slate-700 text-right shadow-lg">
          <div class="text-xs font-bold text-emerald-400 flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Transaksi Otomatis
          </div>
          <div class="text-[11px] text-slate-300 font-mono">Proses Kilat 1-3 Detik</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MAIN MODE SWITCHER TABS (BELI VS JUAL) -->
<div class="max-w-[1360px] mx-auto px-4 sm:px-6 pt-5 pb-1">
  <div class="bg-slate-200/80 p-1.5 rounded-2xl border border-slate-300/80 grid grid-cols-2 w-full shadow-xs" id="mode-tabs">
    <button type="button" id="tab-mode-buy" class="mode-tab active flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-display font-extrabold text-xs sm:text-sm transition-all bg-blue-600 text-white shadow-sm border border-blue-700 cursor-pointer">
      <span class="material-symbols-outlined text-[19px]">shopping_bag</span>
      <span>Top Up / Beli</span>
    </button>
    <button type="button" id="tab-mode-sell" class="mode-tab flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-display font-extrabold text-xs sm:text-sm transition-all text-slate-700 hover:text-neutral-900 hover:bg-white cursor-pointer">
      <span class="material-symbols-outlined text-[19px]">currency_exchange</span>
      <span>Jual / Bongkar</span>
    </button>
  </div>
</div>

