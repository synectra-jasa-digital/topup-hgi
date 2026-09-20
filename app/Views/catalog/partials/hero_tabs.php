<!-- CSS HELPER FOR TABS, CAROUSEL & HERO MOTION -->
<style>
  /* Shared with the catalog sections: .tab-mode-hidden drives buy/sell views, .category-pill.active is used by buy_mode.php */
  .tab-mode-hidden {
    display: none !important;
  }
  .category-pill.active {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35) !important;
    border-color: #2563eb !important;
  }
  .category-pill.active .pill-icon {
    color: #fde047 !important;
  }

  /* 3D Coverflow Slider Animation Styles */
  .coverflow-slide {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 85%;
    height: 90%;
    max-height: 320px;
    aspect-ratio: 16 / 9;
    will-change: transform, opacity;
    transition: transform 600ms cubic-bezier(0.25, 1, 0.5, 1), opacity 600ms ease, filter 600ms ease;
  }
  @media (min-width: 640px) {
    .coverflow-slide { width: 75%; }
  }
  @media (min-width: 768px) {
    .coverflow-slide { width: 65%; }
  }
  @media (min-width: 1024px) {
    .coverflow-slide { width: 58%; }
  }
  .coverflow-slide.state-center {
    transform: translate(-50%, -50%) scale(1) translateX(0);
    z-index: 30;
    opacity: 1;
    filter: brightness(1);
    box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.4), 0 0 20px rgba(37, 99, 235, 0.2);
    border-color: rgba(59, 130, 246, 0.5);
  }
  .coverflow-slide.state-left {
    transform: translate(-50%, -50%) scale(0.85) translateX(-58%);
    z-index: 20;
    opacity: 0.5;
    filter: brightness(0.65) blur(0.3px);
    cursor: pointer;
  }
  .coverflow-slide.state-right {
    transform: translate(-50%, -50%) scale(0.85) translateX(58%);
    z-index: 20;
    opacity: 0.5;
    filter: brightness(0.65) blur(0.3px);
    cursor: pointer;
  }
  .coverflow-slide.state-hidden {
    transform: translate(-50%, -50%) scale(0.6) translateX(0);
    z-index: 0;
    opacity: 0;
    pointer-events: none;
  }

  @media (max-width: 640px) {
    .coverflow-slide.state-left {
      transform: translate(-50%, -50%) scale(0.88) translateX(-38%);
      opacity: 0.35;
    }
    .coverflow-slide.state-right {
      transform: translate(-50%, -50%) scale(0.88) translateX(38%);
      opacity: 0.35;
    }
  }

  /*
   * Hero motion. Every rule sits behind prefers-reduced-motion: no-preference,
   * so visitors who ask for less motion get the static layout.
   */
  @media (prefers-reduced-motion: no-preference) {
    /* Load-in, staggered by --d: carousel first, then the identity card in reading order. */
    .hero-rise {
      opacity: 0;
      animation: hero-rise 650ms cubic-bezier(0.2, 0.8, 0.2, 1) both;
      animation-delay: var(--d, 0ms);
    }
    @keyframes hero-rise {
      from { opacity: 0; transform: translateY(16px); }
      to   { opacity: 1; transform: none; }
    }

    /* Switching Beli / Jual: the incoming view fades up so the change is visible. */
    #view-mode-buy:not(.tab-mode-hidden),
    #view-mode-sell:not(.tab-mode-hidden) {
      animation: view-in 380ms cubic-bezier(0.2, 0.8, 0.2, 1) both;
    }
    @keyframes view-in {
      from { opacity: 0; transform: translateY(12px); }
      to   { opacity: 1; transform: none; }
    }

    /* Depth: banner image is over-scaled and drifts with the pointer (--px/--py) and with scroll (--sy). */
    .coverflow-slide img {
      transform: scale(1.12) translate3d(var(--px, 0px), calc(var(--py, 0px) + var(--sy, 0px)), 0);
      transition: transform 260ms ease-out;
      will-change: transform;
    }

    /* Countdown to the next slide, drawn inside the active dot. */
    #hero-banner-carousel[data-autoplay="on"] [data-active="true"] .dot-fill,
    #hero-banner-carousel[data-autoplay="hold"] [data-active="true"] .dot-fill {
      animation: dot-progress var(--interval, 4500ms) linear both;
    }
    #hero-banner-carousel[data-autoplay="hold"] [data-active="true"] .dot-fill {
      animation-play-state: paused;
    }
    @keyframes dot-progress {
      from { transform: scaleX(0); }
      to   { transform: scaleX(1); }
    }

    /* One light sweep across the game badge every few seconds. */
    .hero-avatar::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(105deg, transparent 35%, rgba(255, 255, 255, 0.55) 50%, transparent 65%);
      transform: translateX(-120%);
      animation: avatar-sweep 5s ease-in-out 1.2s infinite;
      pointer-events: none;
    }
    @keyframes avatar-sweep {
      0%, 70% { transform: translateX(-120%); }
      100%    { transform: translateX(120%); }
    }
  }

  /* Identity card. The indicator replaces the pressed-tab fill once JS has placed it (data-indicator). */
  .hero-mode-tabs[data-indicator] button[aria-pressed="true"] {
    background-color: transparent;
    border-color: transparent;
    box-shadow: none;
  }

  @media (prefers-reduced-motion: no-preference) {
    /* Title reveals word by word, each word rising out of a mask (--d = start, --i = word index). */
    .hero-word {
      display: inline-block;
      overflow: hidden;
      vertical-align: bottom;
      padding-bottom: 0.14em;
      margin-bottom: -0.14em;
    }
    .hero-word > span {
      display: inline-block;
      transform: translateY(110%);
      animation: word-up 700ms cubic-bezier(0.2, 0.8, 0.2, 1) both;
      animation-delay: calc(var(--d, 0ms) + var(--i, 0) * 70ms);
    }
    @keyframes word-up {
      from { transform: translateY(110%); }
      to   { transform: none; }
    }

    /* The description swaps with the mode: the new sentence fades up. */
    [data-hero-copy]:not([hidden]) {
      animation: copy-in 380ms cubic-bezier(0.2, 0.8, 0.2, 1) both;
    }
    @keyframes copy-in {
      from { opacity: 0; transform: translateY(8px); }
      to   { opacity: 1; transform: none; }
    }

    /* One pill slides between Beli and Jual and changes colour on the way. */
    .hero-indicator {
      transition:
        transform 380ms cubic-bezier(0.3, 1.2, 0.4, 1),
        width 380ms cubic-bezier(0.3, 1.2, 0.4, 1),
        height 380ms ease,
        background-color 300ms ease,
        border-color 300ms ease;
    }

    /* The game badge pops once when the mode changes. */
    .badge-pop {
      animation: badge-pop 560ms cubic-bezier(0.3, 1.5, 0.5, 1);
    }
    @keyframes badge-pop {
      0%   { transform: scale(1) rotate(0); }
      40%  { transform: scale(1.16) rotate(-5deg); }
      100% { transform: scale(1) rotate(0); }
    }
  }

  /* Spotlight that follows the mouse across the card, mouse users only. */
  @media (hover: hover) and (prefers-reduced-motion: no-preference) {
    .hero-card::before {
      content: "";
      position: absolute;
      inset: 0;
      pointer-events: none;
      opacity: 0;
      transition: opacity 300ms ease;
      background: radial-gradient(280px circle at var(--mx, 50%) var(--my, 50%), rgba(59, 130, 246, 0.18), transparent 70%);
    }
    .hero-card:hover::before {
      opacity: 1;
    }
  }
</style>

<!-- HERO PROMO CAROUSEL & GAME HEADER -->
<?php
  // Only real, admin-managed banners are shown. With one or two banners the same real image fills the extra
  // coverflow positions (marked aria-hidden). With none, the carousel is left out.
  $realBanners = ! empty($banners) ? array_values($banners) : [];
  $realCount   = count($realBanners);
  $bannerList  = $realBanners;
  while ($realCount > 0 && count($bannerList) < 3) {
      $bannerList[] = ['image_path' => $realBanners[0]['image_path'], 'link_url' => $realBanners[0]['link_url'] ?? '', 'clone' => true];
  }
  $bannerTotal = count($bannerList);
?>

<div class="max-w-[1360px] mx-auto px-4 sm:px-6 pt-4 pb-2 select-none">
  <?php if ($bannerTotal > 0): ?>
  <!-- Top Banner Carousel -->
  <div class="relative w-full group hero-rise" style="--d: 0ms" id="hero-banner-carousel" data-autoplay="off" role="region" aria-roledescription="carousel" aria-label="Promo toko">
    <div class="relative h-[180px] sm:h-[260px] md:h-[320px] lg:h-[360px] w-full flex items-center justify-center overflow-hidden py-1" id="coverflow-track">
      <?php foreach ($bannerList as $bIndex => $b): ?>
        <?php
          $isClone   = ! empty($b['clone']);
          $image      = banner_image_set($b['image_path']);
          $imgUrl     = $image['src'];
          $webpSrcset = $image['srcset'];
          $initialState = match($bIndex) {
              0 => 'state-center',
              1 => 'state-right',
              $bannerTotal - 1 => 'state-left',
              default => 'state-hidden'
          };
        ?>
        <div class="coverflow-slide <?= $initialState ?> rounded-2xl border border-slate-700/80 bg-slate-950 overflow-hidden shadow-lg" data-index="<?= $bIndex ?>" role="group" aria-roledescription="slide" <?= $isClone ? 'aria-hidden="true"' : 'aria-label="' . ($bIndex + 1) . ' dari ' . $realCount . '"' ?>>
          <?php if (! empty($b['link_url'])): ?>
            <a href="<?= esc($b['link_url']) ?>" target="_blank" rel="noopener noreferrer" class="block w-full h-full focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-blue-500" <?= $isClone ? 'tabindex="-1"' : '' ?>>
          <?php endif; ?>
            <picture class="block w-full h-full">
              <?php if ($webpSrcset): ?>
                <source srcset="<?= $webpSrcset ?>" sizes="(min-width: 1024px) 748px, (min-width: 768px) 62vw, (min-width: 640px) 72vw, 82vw" type="image/webp">
              <?php endif; ?>
              <img alt="<?= $isClone ? '' : esc('Promo ' . ($bIndex + 1) . ' dari ' . $realCount) ?>" class="w-full h-full object-cover object-center select-none bg-slate-950 block" src="<?= $imgUrl ?>" width="900" height="502" <?= $bIndex === 0 ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"' ?>>
            </picture>
          <?php if (! empty($b['link_url'])): ?>
            </a>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Navigation Arrows: visible on hover, on keyboard focus, and always on touch screens -->
    <button type="button" class="absolute left-2 sm:left-4 top-[45%] -translate-y-1/2 z-40 w-11 h-11 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white backdrop-blur-md border border-slate-700/80 flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 [@media(hover:none)]:opacity-100 hover:scale-110 active:scale-95 shadow-lg cursor-pointer focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500" id="btn-coverflow-prev" aria-label="Promo sebelumnya">
      <span class="material-symbols-outlined text-[24px]" aria-hidden="true">chevron_left</span>
    </button>
    <button type="button" class="absolute right-2 sm:right-4 top-[45%] -translate-y-1/2 z-40 w-11 h-11 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white backdrop-blur-md border border-slate-700/80 flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 [@media(hover:none)]:opacity-100 hover:scale-110 active:scale-95 shadow-lg cursor-pointer focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500" id="btn-coverflow-next" aria-label="Promo berikutnya">
      <span class="material-symbols-outlined text-[24px]" aria-hidden="true">chevron_right</span>
    </button>

    <!-- Indicators (the active one counts down to the next slide) -->
    <div class="mt-1 flex items-center justify-center">
      <div class="flex items-center" id="coverflow-dots" role="group" aria-label="Pilih promo">
        <?php foreach ($bannerList as $bIndex => $b): ?>
          <button type="button" class="group/dot flex h-11 w-11 items-center justify-center rounded-full focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-blue-600" data-dot-index="<?= $bIndex ?>" data-active="<?= $bIndex === 0 ? 'true' : 'false' ?>" aria-label="Promo <?= $bIndex + 1 ?>" <?= ! empty($b['clone']) ? 'aria-hidden="true" tabindex="-1"' : '' ?>>
            <span class="block h-1.5 w-2 overflow-hidden rounded-full bg-slate-300 transition-all duration-300 group-data-[active=true]/dot:w-6">
              <span class="dot-fill block h-full w-full origin-left scale-x-0 bg-blue-600 group-data-[active=true]/dot:scale-x-100"></span>
            </span>
          </button>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Game Identity Header Banner Card -->
  <div id="hero-identity-card" class="hero-card hero-rise <?= $bannerTotal > 0 ? 'mt-3' : 'mt-1' ?> relative overflow-hidden rounded-2xl border border-slate-800 p-5 text-white shadow-md sm:p-6" style="--d: 180ms; background-color: #000000 !important; background-image: none !important;">
    <div class="relative z-10 flex flex-col justify-between gap-5 sm:gap-6 md:flex-row md:items-center">
      <div class="flex items-center gap-4">
        <!-- Game badge -->
        <div class="hero-rise shrink-0" style="--d: 320ms">
          <div id="hero-badge" class="hero-avatar relative size-14 overflow-hidden rounded-2xl bg-gradient-to-tr from-amber-500 to-yellow-400 p-0.5 shadow-sm">
            <div class="flex h-full w-full items-center justify-center overflow-hidden rounded-[14px] bg-slate-900 p-2">
              <svg viewBox="0 0 48 48" class="w-full h-full" aria-hidden="true">
                <rect x="4" y="4" width="40" height="18" rx="4" fill="#fde68a" stroke="#b45309" stroke-width="1.5" transform="rotate(-8 24 13)"></rect>
                <circle cx="14" cy="10" r="2" fill="#78350f" transform="rotate(-8 24 13)"></circle>
                <circle cx="24" cy="10" r="2" fill="#78350f" transform="rotate(-8 24 13)"></circle>
                <circle cx="34" cy="10" r="2" fill="#78350f" transform="rotate(-8 24 13)"></circle>
                <rect x="6" y="24" width="38" height="18" rx="4" fill="#fef3c7" stroke="#b45309" stroke-width="1.5" transform="rotate(6 24 33)"></rect>
                <circle cx="16" cy="33" r="2" fill="#92400e" transform="rotate(6 24 33)"></circle>
                <circle cx="32" cy="29" r="2" fill="#92400e" transform="rotate(6 24 33)"></circle>
                <circle cx="32" cy="37" r="2" fill="#92400e" transform="rotate(6 24 33)"></circle>
                <circle cx="16" cy="29" r="2" fill="#92400e" transform="rotate(6 24 33)"></circle>
                <circle cx="16" cy="37" r="2" fill="#92400e" transform="rotate(6 24 33)"></circle>
              </svg>
            </div>
          </div>
        </div>

        <div class="min-w-0 space-y-1.5">
          <h1 class="font-display text-lg font-black leading-tight tracking-tight text-white sm:text-xl md:text-2xl" style="--d: 380ms">
            <span class="hero-word"><span style="--i: 0">Higgs</span></span>
            <span class="hero-word"><span style="--i: 1">Domino</span></span>
            <span class="hero-word"><span style="--i: 2">Island</span></span>
            <span class="hero-word"><span style="--i: 3">/</span></span>
            <span class="hero-word"><span style="--i: 4">Global</span></span>
          </h1>
          <div class="hero-rise max-w-xl text-xs text-slate-300 sm:text-sm" style="--d: 640ms">
            <p data-hero-copy="buy">Tanpa login. Cukup ID game dan nomor WhatsApp, bayar lewat transfer bank atau QRIS, lalu unggah bukti pembayaran.</p>
            <p data-hero-copy="sell" hidden>Pilih kartu dan jumlahnya, pilih metode pencairan, lalu kirim pengajuan. Admin yang memprosesnya.</p>
          </div>
        </div>
      </div>

      <!-- Mode Switcher Tabs (Beli vs Jual). State lives in aria-pressed (see interactive.php); the indicator only follows it. -->
      <div id="hero-mode-tabs" class="hero-mode-tabs hero-rise relative flex w-full shrink-0 items-center gap-1.5 self-start rounded-xl border border-slate-800 bg-slate-900 p-1.5 shadow-inner md:w-auto md:self-center" style="--d: 760ms" role="group" aria-label="Pilih layanan">
        <span id="hero-mode-indicator" class="hero-indicator pointer-events-none absolute left-0 top-0 z-0 rounded-lg border border-blue-500 bg-blue-600 shadow-sm data-[mode=sell]:border-amber-400 data-[mode=sell]:bg-amber-500" aria-hidden="true" hidden></span>
        <button id="tab-mode-buy" type="button" aria-pressed="true"
                class="relative z-10 flex min-h-11 flex-1 cursor-pointer items-center justify-center gap-2 rounded-lg border border-transparent px-4 py-2 font-display text-xs font-bold text-slate-300 transition-colors duration-300 hover:bg-slate-800/80 hover:text-white active:scale-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-400 aria-pressed:border-blue-500 aria-pressed:bg-blue-600 aria-pressed:text-white aria-pressed:shadow-sm aria-pressed:hover:bg-blue-600 sm:text-sm md:flex-none">
          <span class="material-symbols-outlined text-[18px]" aria-hidden="true">shopping_cart</span>
          <span>Beli / Top Up</span>
        </button>
        <button id="tab-mode-sell" type="button" aria-pressed="false"
                class="relative z-10 flex min-h-11 flex-1 cursor-pointer items-center justify-center gap-2 rounded-lg border border-transparent px-4 py-2 font-display text-xs font-bold text-slate-300 transition-colors duration-300 hover:bg-slate-800/80 hover:text-white active:scale-95 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-400 aria-pressed:border-amber-400 aria-pressed:bg-amber-500 aria-pressed:text-neutral-950 aria-pressed:shadow-sm aria-pressed:hover:bg-amber-500 sm:text-sm md:flex-none">
          <span class="material-symbols-outlined text-[18px] text-amber-400 transition-colors duration-300 [[aria-pressed=true]>&]:text-neutral-950" aria-hidden="true">currency_exchange</span>
          <span>Bongkar / Jual</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- IDENTITY CARD: sliding mode indicator, badge pop on mode change, mouse spotlight -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const group = document.getElementById('hero-mode-tabs');
    const indicator = document.getElementById('hero-mode-indicator');
    const buy = document.getElementById('tab-mode-buy');
    const sell = document.getElementById('tab-mode-sell');
    const card = document.getElementById('hero-identity-card');
    const badge = document.getElementById('hero-badge');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    if (!group || !indicator || !buy || !sell) return;

    let lastMode = null;

    function pop() {
      if (!badge || reduceMotion.matches) return;
      badge.classList.remove('badge-pop');
      void badge.offsetWidth;
      badge.classList.add('badge-pop');
    }

    badge?.addEventListener('animationend', (e) => {
      if (e.animationName === 'badge-pop') badge.classList.remove('badge-pop');
    });

    // Sizes the indicator to the pressed tab. animate=false snaps it (first paint, resize, font swap).
    function place(animate) {
      const tab = sell.getAttribute('aria-pressed') === 'true' ? sell : buy;
      const mode = tab === sell ? 'sell' : 'buy';
      const snap = !animate || reduceMotion.matches;

      if (snap) indicator.style.transition = 'none';
      indicator.hidden = false;
      const t = tab.getBoundingClientRect();
      const g = group.getBoundingClientRect();
      indicator.style.width = t.width + 'px';
      indicator.style.height = t.height + 'px';
      indicator.style.transform = 'translate(' + (t.left - g.left - group.clientLeft) + 'px, ' + (t.top - g.top - group.clientTop) + 'px)';
      indicator.dataset.mode = mode;
      group.dataset.indicator = 'on';
      if (snap) {
        void indicator.offsetWidth;
        indicator.style.transition = '';
      }

      if (lastMode !== null && lastMode !== mode) pop();
      lastMode = mode;
    }

    new MutationObserver(() => place(true)).observe(group, { attributes: true, attributeFilter: ['aria-pressed'], subtree: true });
    new ResizeObserver(() => place(false)).observe(group);
    if (document.fonts && document.fonts.ready) document.fonts.ready.then(() => place(false));
    place(false);

    if (card && window.matchMedia('(hover: hover)').matches && !reduceMotion.matches) {
      card.addEventListener('pointermove', (e) => {
        const box = card.getBoundingClientRect();
        card.style.setProperty('--mx', (e.clientX - box.left) + 'px');
        card.style.setProperty('--my', (e.clientY - box.top) + 'px');
      });
    }
  });
</script>

<?php if ($bannerTotal > 1): ?>
<!-- COVERFLOW CAROUSEL: auto-play with pause, progress in the active dot, pointer and scroll depth -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('hero-banner-carousel');
    if (!root) return;

    const slides = Array.from(root.querySelectorAll('.coverflow-slide'));
    const dots = Array.from(root.querySelectorAll('[data-dot-index]'));
    const btnPrev = document.getElementById('btn-coverflow-prev');
    const btnNext = document.getElementById('btn-coverflow-next');
    const total = slides.length;
    if (total <= 1) return;

    const INTERVAL = 4500;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    root.style.setProperty('--interval', INTERVAL + 'ms');

    let current = 0;
    let timer = null;
    let motionOff = reduceMotion.matches;  // no auto-play for visitors who asked for less motion
    let holding = false;                   // pointer, focus or touch is on the carousel

    function render(index) {
      current = (index + total) % total;

      slides.forEach((slide, i) => {
        slide.classList.remove('state-center', 'state-left', 'state-right', 'state-hidden');
        slide.style.removeProperty('--px');
        slide.style.removeProperty('--py');
        if (i === current) {
          slide.classList.add('state-center');
        } else if (i === (current + 1) % total) {
          slide.classList.add('state-right');
        } else if (i === (current - 1 + total) % total) {
          slide.classList.add('state-left');
        } else {
          slide.classList.add('state-hidden');
        }
      });

      dots.forEach((dot, i) => {
        dot.dataset.active = String(i === current);
        if (i === current) dot.setAttribute('aria-current', 'true');
        else dot.removeAttribute('aria-current');
      });
    }

    // One place decides the auto-play state; the CSS countdown in the active dot follows data-autoplay.
    function schedule() {
      clearTimeout(timer);
      const state = motionOff ? 'off' : (holding ? 'hold' : 'on');
      root.dataset.autoplay = state;
      if (state === 'on' && !document.hidden) timer = setTimeout(() => go(current + 1), INTERVAL);
    }

    function go(index) {
      render(index);
      schedule();
    }

    function restartCountdown() {
      root.dataset.autoplay = 'off';
      void root.offsetWidth;
      schedule();
    }

    function hold(value) {
      if (holding === value) return;
      holding = value;
      if (value) schedule();
      else restartCountdown();
    }

    btnNext?.addEventListener('click', (e) => { e.preventDefault(); go(current + 1); });
    btnPrev?.addEventListener('click', (e) => { e.preventDefault(); go(current - 1); });

    dots.forEach((dot, i) => {
      dot.addEventListener('click', (e) => { e.preventDefault(); go(i); });
    });

    // Clicking the left/right slide brings it to the centre
    slides.forEach((slide, i) => {
      slide.addEventListener('click', (e) => {
        if (i !== current) {
          e.preventDefault();
          go(i);
        }
      });
    });

    reduceMotion.addEventListener('change', () => {
      motionOff = reduceMotion.matches;
      restartCountdown();
    });

    // Keep the carousel still while someone is using it, and while the tab is in the background
    root.addEventListener('mouseenter', () => hold(true));
    root.addEventListener('mouseleave', () => hold(false));
    root.addEventListener('focusin', () => hold(true));
    root.addEventListener('focusout', (e) => { if (!root.contains(e.relatedTarget)) hold(false); });
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) clearTimeout(timer);
      else restartCountdown();
    });

    // Touch swipe
    let touchStartX = 0;
    root.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
      hold(true);
    }, { passive: true });
    root.addEventListener('touchend', (e) => {
      const diff = e.changedTouches[0].screenX - touchStartX;
      if (Math.abs(diff) > 40) go(diff < 0 ? current + 1 : current - 1);
      hold(false);
    }, { passive: true });

    // Depth: the centre banner's image follows the mouse a few pixels, and drifts a little with scroll
    root.addEventListener('pointermove', (e) => {
      if (e.pointerType !== 'mouse' || reduceMotion.matches) return;
      const slide = slides[current];
      const box = slide.getBoundingClientRect();
      const nx = Math.max(-1, Math.min(1, (e.clientX - (box.left + box.width / 2)) / (box.width / 2)));
      const ny = Math.max(-1, Math.min(1, (e.clientY - (box.top + box.height / 2)) / (box.height / 2)));
      slide.style.setProperty('--px', (nx * -10).toFixed(1) + 'px');
      slide.style.setProperty('--py', (ny * -6).toFixed(1) + 'px');
    });
    root.addEventListener('pointerleave', () => {
      slides[current].style.removeProperty('--px');
      slides[current].style.removeProperty('--py');
    });

    if (!reduceMotion.matches && window.matchMedia('(min-width: 768px)').matches) {
      let ticking = false;
      window.addEventListener('scroll', () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
          ticking = false;
          root.style.setProperty('--sy', (Math.min(window.scrollY, 600) * 0.02).toFixed(1) + 'px');
        });
      }, { passive: true });
    }

    render(0);
    schedule();
  });
</script>
<?php endif; ?>
