<!-- CSS HELPER FOR TABS & PILLS -->
<style>
  .tab-mode-hidden {
    display: none !important;
  }
  .category-pill.active .pill-icon {
    color: #ffffff !important;
  }
  
  /* 3D Coverflow Slider Animation Styles */
  .coverflow-slide {
    position: absolute;
    top: 50%;
    left: 50%;
    will-change: transform, opacity;
    transition: transform 600ms cubic-bezier(0.25, 1, 0.5, 1), opacity 600ms ease, filter 600ms ease, box-shadow 600ms ease;
  }
  .coverflow-slide.state-center {
    transform: translate(-50%, -50%) scale(1) translateX(0);
    z-index: 30;
    opacity: 1;
    filter: brightness(1);
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7), 0 0 25px rgba(37, 99, 235, 0.2);
    border-color: rgba(148, 163, 184, 0.3);
  }
  .coverflow-slide.state-left {
    transform: translate(-50%, -50%) scale(0.82) translateX(-64%);
    z-index: 20;
    opacity: 0.55;
    filter: brightness(0.65) blur(0.3px);
    cursor: pointer;
  }
  .coverflow-slide.state-right {
    transform: translate(-50%, -50%) scale(0.82) translateX(64%);
    z-index: 20;
    opacity: 0.55;
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
      transform: translate(-50%, -50%) scale(0.85) translateX(-42%);
      opacity: 0.35;
    }
    .coverflow-slide.state-right {
      transform: translate(-50%, -50%) scale(0.85) translateX(42%);
      opacity: 0.35;
    }
  }
</style>

<!-- 3D COVERFLOW HERO BANNER CAROUSEL -->
<?php
  $defaultFallbackImage = 'https://lh3.googleusercontent.com/aida/AEtjO1Xkmeor5HdJYUSrixZ-AzI0Nncuf7tYmyNxC1SwZdCDjOMU2BjepgpLXbad3fySmsQm7rP5nP-ptDLEIo2MMimWSzGcIHVkQSlrxiOr-zLVdB_OvX-VtyWrOJh0BvOZilFEiQ8h4Ck8egDDGp3p68c22YensCJWpq6l6pDVIJCn9oeXJMtojO-IKJOU47c-kgqr7XlYTNou8LADwr6yjDGsxmgUgT3SlDg5R8tjmBh1dHMjUbENtang-g';

  $rawBanners = ! empty($banners) ? $banners : [];

  // Build a minimum of 3 banners for 3D Coverflow display (Left, Center, Right)
  $bannerList = $rawBanners;
  if (count($bannerList) === 0) {
      $bannerList = [
          ['image_path' => $defaultFallbackImage, 'link_url' => '', 'title' => 'Banner 1'],
          ['image_path' => $defaultFallbackImage, 'link_url' => '', 'title' => 'Banner 2'],
          ['image_path' => $defaultFallbackImage, 'link_url' => '', 'title' => 'Banner 3'],
      ];
  } elseif (count($bannerList) === 1) {
      $bannerList[] = ['image_path' => $bannerList[0]['image_path'], 'link_url' => $bannerList[0]['link_url'] ?? '', 'title' => 'Banner 2'];
      $bannerList[] = ['image_path' => $bannerList[0]['image_path'], 'link_url' => $bannerList[0]['link_url'] ?? '', 'title' => 'Banner 3'];
  } elseif (count($bannerList) === 2) {
      $bannerList[] = ['image_path' => $bannerList[0]['image_path'], 'link_url' => $bannerList[0]['link_url'] ?? '', 'title' => 'Banner 3'];
  }
?>

<div class="max-w-[1360px] mx-auto px-2 sm:px-6 pt-4 pb-2 select-none">
  <div class="relative w-full group" id="hero-banner-carousel">
    
    <!-- 3D Coverflow Track Container -->
    <div class="relative h-[210px] sm:h-[300px] md:h-[360px] lg:h-[400px] w-full flex items-center justify-center overflow-hidden py-2" id="coverflow-track">
      <?php foreach ($bannerList as $bIndex => $b): ?>
        <?php 
          $imgUrl = str_starts_with($b['image_path'], 'http') ? $b['image_path'] : base_url($b['image_path']);
          $initialState = match($bIndex) {
              0 => 'state-center',
              1 => 'state-right',
              count($bannerList) - 1 => 'state-left',
              default => 'state-hidden'
          };
        ?>
        <div class="coverflow-slide <?= $initialState ?> rounded-2xl border border-slate-700/80 bg-slate-950 overflow-hidden aspect-[16/9] w-[82%] sm:w-[72%] md:w-[62%] lg:w-[55%]" data-index="<?= $bIndex ?>">
          <?php if (! empty($b['link_url'])): ?>
            <a href="<?= esc($b['link_url']) ?>" target="_blank" rel="noopener noreferrer" class="block w-full h-full">
          <?php endif; ?>
            <img alt="<?= esc($b['title'] ?? 'Banner Promo') ?>" class="w-full h-full object-cover sm:object-contain object-center select-none bg-slate-950" src="<?= $imgUrl ?>" width="900" height="502" <?= $bIndex === 0 ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"' ?>>
          <?php if (! empty($b['link_url'])): ?>
            </a>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Controls: Prev (<) & Next (>) Side Buttons -->
    <button type="button" id="banner-prev" class="absolute left-1 sm:left-4 top-1/2 -translate-y-1/2 z-40 w-9 h-9 sm:w-11 sm:h-11 rounded-2xl bg-slate-900/85 hover:bg-slate-900 text-white flex items-center justify-center transition-all shadow-xl border border-slate-700/80 backdrop-blur-md cursor-pointer hover:scale-105 active:scale-95" aria-label="Previous Slide">
      <span class="material-symbols-outlined text-[20px] sm:text-[24px]">chevron_left</span>
    </button>
    
    <button type="button" id="banner-next" class="absolute right-1 sm:right-4 top-1/2 -translate-y-1/2 z-40 w-9 h-9 sm:w-11 sm:h-11 rounded-2xl bg-slate-900/85 hover:bg-slate-900 text-white flex items-center justify-center transition-all shadow-xl border border-slate-700/80 backdrop-blur-md cursor-pointer hover:scale-105 active:scale-95" aria-label="Next Slide">
      <span class="material-symbols-outlined text-[20px] sm:text-[24px]">chevron_right</span>
    </button>

    <!-- Pagination Indicators / Dots -->
    <div class="absolute bottom-1.5 left-1/2 -translate-x-1/2 z-40 flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900/80 backdrop-blur-md border border-slate-700/60 shadow-lg" id="banner-dots">
      <?php foreach ($bannerList as $bIndex => $b): ?>
        <button type="button" class="banner-dot w-2.5 h-2.5 rounded-full transition-all duration-300 cursor-pointer <?= $bIndex === 0 ? 'bg-white w-6' : 'bg-white/40 hover:bg-white/70' ?>" data-slide="<?= $bIndex ?>" aria-label="Slide <?= $bIndex + 1 ?>"></button>
      <?php endforeach; ?>
    </div>

  </div>
</div>

<!-- 3D Coverflow Carousel Logic -->
<script>
  (function() {
    const track = document.getElementById('coverflow-track');
    if (!track) return;

    const slides = Array.from(track.querySelectorAll('.coverflow-slide'));
    const dots = Array.from(document.querySelectorAll('.banner-dot'));
    const prevBtn = document.getElementById('banner-prev');
    const nextBtn = document.getElementById('banner-next');
    const total = slides.length;

    if (total === 0) return;

    let currentIndex = 0;
    let timer = null;

    function updateCoverflow(activeIdx) {
      currentIndex = (activeIdx + total) % total;
      const leftIdx = (currentIndex - 1 + total) % total;
      const rightIdx = (currentIndex + 1) % total;

      slides.forEach((slide, idx) => {
        slide.classList.remove('state-center', 'state-left', 'state-right', 'state-hidden');

        if (idx === currentIndex) {
          slide.classList.add('state-center');
        } else if (idx === leftIdx) {
          slide.classList.add('state-left');
        } else if (idx === rightIdx) {
          slide.classList.add('state-right');
        } else {
          slide.classList.add('state-hidden');
        }
      });

      dots.forEach((dot, idx) => {
        if (idx === currentIndex) {
          dot.classList.remove('bg-white/40');
          dot.classList.add('bg-white', 'w-6');
        } else {
          dot.classList.remove('bg-white', 'w-6');
          dot.classList.add('bg-white/40');
        }
      });
    }

    function nextSlide() {
      updateCoverflow(currentIndex + 1);
    }

    function prevSlide() {
      updateCoverflow(currentIndex - 1);
    }

    function startTimer() {
      stopTimer();
      timer = setInterval(nextSlide, 3500);
    }

    function stopTimer() {
      if (timer) clearInterval(timer);
    }

    // Event Listeners
    prevBtn?.addEventListener('click', () => {
      prevSlide();
      startTimer();
    });

    nextBtn?.addEventListener('click', () => {
      nextSlide();
      startTimer();
    });

    slides.forEach((slide, idx) => {
      slide.addEventListener('click', () => {
        if (idx !== currentIndex) {
          updateCoverflow(idx);
          startTimer();
        }
      });
    });

    dots.forEach((dot, idx) => {
      dot.addEventListener('click', () => {
        updateCoverflow(idx);
        startTimer();
      });
    });

    const carousel = document.getElementById('hero-banner-carousel');
    carousel?.addEventListener('mouseenter', stopTimer);
    carousel?.addEventListener('mouseleave', startTimer);

    // Initial render
    updateCoverflow(0);
    startTimer();
  })();
</script>

<!-- MAIN MODE SWITCHER TABS (BELI VS JUAL) -->
<div class="max-w-[1360px] mx-auto px-4 sm:px-6 pt-3 pb-1">
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
