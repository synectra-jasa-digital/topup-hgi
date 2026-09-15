<!-- CSS HELPER FOR TABS & CAROUSEL -->
<style>
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
    transition: transform 500ms cubic-bezier(0.25, 1, 0.5, 1), opacity 500ms ease;
  }
  @media (min-width: 640px) {
    .coverflow-slide {
      width: 75%;
    }
  }
  @media (min-width: 768px) {
    .coverflow-slide {
      width: 65%;
    }
  }
  @media (min-width: 1024px) {
    .coverflow-slide {
      width: 58%;
    }
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
</style>

<!-- HERO PROMO CAROUSEL & GAME HEADER -->
<?php
  $defaultFallbackImage = 'https://lh3.googleusercontent.com/aida/AEtjO1Xkmeor5HdJYUSrixZ-AzI0Nncuf7tYmyNxC1SwZdCDjOMU2BjepgpLXbad3fySmsQm7rP5nP-ptDLEIo2MMimWSzGcIHVkQSlrxiOr-zLVdB_OvX-VtyWrOJh0BvOZilFEiQ8h4Ck8egDDGp3p68c22YensCJWpq6l6pDVIJCn9oeXJMtojO-IKJOU47c-kgqr7XlYTNou8LADwr6yjDGsxmgUgT3SlDg5R8tjmBh1dHMjUbENtang-g';

  $rawBanners = ! empty($banners) ? $banners : [];

  $bannerList = $rawBanners;
  if (count($bannerList) === 0) {
      $bannerList = [
          ['image_path' => $defaultFallbackImage, 'link_url' => '', 'title' => 'Banner Promo 1'],
          ['image_path' => $defaultFallbackImage, 'link_url' => '', 'title' => 'Banner Promo 2'],
          ['image_path' => $defaultFallbackImage, 'link_url' => '', 'title' => 'Banner Promo 3'],
      ];
  } elseif (count($bannerList) === 1) {
      $bannerList[] = ['image_path' => $bannerList[0]['image_path'], 'link_url' => $bannerList[0]['link_url'] ?? '', 'title' => 'Banner Promo 2'];
      $bannerList[] = ['image_path' => $bannerList[0]['image_path'], 'link_url' => $bannerList[0]['link_url'] ?? '', 'title' => 'Banner Promo 3'];
  } elseif (count($bannerList) === 2) {
      $bannerList[] = ['image_path' => $bannerList[0]['image_path'], 'link_url' => $bannerList[0]['link_url'] ?? '', 'title' => 'Banner Promo 3'];
  }
?>

<div class="max-w-[1360px] mx-auto px-4 sm:px-6 pt-4 pb-2 select-none">
  <!-- Top Banner Carousel -->
  <div class="relative w-full group" id="hero-banner-carousel">
    <div class="relative h-[180px] sm:h-[260px] md:h-[320px] lg:h-[360px] w-full flex items-center justify-center overflow-hidden py-1" id="coverflow-track">
      <?php foreach ($bannerList as $bIndex => $b): ?>
        <?php
          $isLocal = ! str_starts_with($b['image_path'], 'http');
          $imgUrl = $isLocal ? base_url($b['image_path']) : $b['image_path'];
          $webpPath = ($isLocal && str_ends_with($b['image_path'], '.png'))
              ? substr($b['image_path'], 0, -4) . '.webp'
              : null;
          $webpUrl = ($webpPath && is_file(FCPATH . $webpPath)) ? base_url($webpPath) : null;
          $webp400Path = $webpPath ? substr($webpPath, 0, -5) . '-400w.webp' : null;
          $webp400Url = ($webp400Path && is_file(FCPATH . $webp400Path)) ? base_url($webp400Path) : null;
          $webp700Path = $webpPath ? substr($webpPath, 0, -5) . '-700w.webp' : null;
          $webp700Url = ($webp700Path && is_file(FCPATH . $webp700Path)) ? base_url($webp700Path) : null;
          $webpSrcset = $webpUrl ? trim(($webp400Url ? "{$webp400Url} 400w, " : '') . ($webp700Url ? "{$webp700Url} 700w, " : '') . "{$webpUrl} 900w") : null;
          $initialState = match($bIndex) {
              0 => 'state-center',
              1 => 'state-right',
              count($bannerList) - 1 => 'state-left',
              default => 'state-hidden'
          };
        ?>
        <div class="coverflow-slide <?= $initialState ?> rounded-2xl border border-slate-700/80 bg-slate-950 overflow-hidden shadow-lg" data-index="<?= $bIndex ?>">
          <?php if (! empty($b['link_url'])): ?>
            <a href="<?= esc($b['link_url']) ?>" target="_blank" rel="noopener noreferrer" class="block w-full h-full">
          <?php endif; ?>
            <picture class="block w-full h-full">
              <?php if ($webpSrcset): ?>
                <source srcset="<?= $webpSrcset ?>" sizes="(min-width: 1024px) 748px, (min-width: 768px) 62vw, (min-width: 640px) 72vw, 82vw" type="image/webp">
              <?php endif; ?>
              <img alt="<?= esc($b['title'] ?? 'Banner Promo') ?>" class="w-full h-full object-cover object-center select-none bg-slate-950 block" src="<?= $imgUrl ?>" width="900" height="502" <?= $bIndex === 0 ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"' ?>>
            </picture>
          <?php if (! empty($b['link_url'])): ?>
            </a>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Navigation Arrows -->
    <button type="button" class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-40 w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white backdrop-blur-md border border-slate-700/80 flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 hover:scale-110 shadow-lg cursor-pointer" id="btn-coverflow-prev" aria-label="Slide sebelumnya">
      <span class="material-symbols-outlined text-[20px] sm:text-[24px]">chevron_left</span>
    </button>
    <button type="button" class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-40 w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-slate-900/80 hover:bg-slate-900 text-white backdrop-blur-md border border-slate-700/80 flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 hover:scale-110 shadow-lg cursor-pointer" id="btn-coverflow-next" aria-label="Slide berikutnya">
      <span class="material-symbols-outlined text-[20px] sm:text-[24px]">chevron_right</span>
    </button>

    <!-- Indicators -->
    <div class="flex items-center justify-center gap-1.5 mt-2" id="coverflow-dots">
      <?php foreach ($bannerList as $bIndex => $b): ?>
        <button type="button" class="h-1.5 rounded-full transition-all duration-300 <?= $bIndex === 0 ? 'w-6 bg-blue-600' : 'w-2 bg-slate-300' ?>" data-dot-index="<?= $bIndex ?>" aria-label="Go to slide <?= $bIndex + 1 ?>"></button>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Game Identity Header Banner Card -->
  <div class="mt-5 rounded-2xl p-5 sm:p-6 border border-slate-800 shadow-md text-white relative overflow-hidden" style="background-color: #000000 !important; background-image: none !important;">
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-6">
      <div class="flex items-center" style="gap: 6px;">
        <!-- Game Logo Avatar (Fixed 56x56px Box) -->
        <div class="relative rounded-2xl bg-gradient-to-tr from-amber-500 to-yellow-400 p-0.5 shadow-sm shrink-0" style="width: 56px; height: 56px; min-width: 56px; min-height: 56px;">
          <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center overflow-hidden p-2">
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

        <div class="space-y-1" style="margin-left: 16px;">
          <h1 class="font-display font-black text-lg sm:text-xl md:text-2xl text-white tracking-tight leading-tight">Higgs Domino Island / Global</h1>
          <p class="text-xs text-slate-300 max-w-xl">Top up koin emas &amp; kartu resmi proses instan 24 jam nonstop tanpa login password.</p>
        </div>
      </div>

      <!-- Mode Switcher Cockpit Tabs (Beli vs Jual) -->
      <div class="bg-slate-900 p-1.5 rounded-xl border border-slate-800 flex items-center gap-1.5 self-start md:self-center shrink-0 shadow-inner">
        <button id="tab-mode-buy" type="button" class="active px-4 py-2 rounded-lg bg-blue-600 text-white font-display font-extrabold text-xs sm:text-sm shadow-sm transition-all flex items-center gap-2 border border-blue-500 cursor-pointer">
          <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
          <span>Beli / Top Up</span>
        </button>
        <button id="tab-mode-sell" type="button" class="px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800/80 font-display font-bold text-xs sm:text-sm transition-all flex items-center gap-2 cursor-pointer">
          <span class="material-symbols-outlined text-[18px] text-amber-400">currency_exchange</span>
          <span>Bongkar / Jual</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- COVERFLOW CAROUSEL & AUTO-SLIDE INTERACTIVE SCRIPT -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('hero-banner-carousel');
    if (!track) return;

    const slides = Array.from(track.querySelectorAll('.coverflow-slide'));
    const dots = Array.from(track.querySelectorAll('[data-dot-index]'));
    const btnPrev = document.getElementById('btn-coverflow-prev');
    const btnNext = document.getElementById('btn-coverflow-next');
    const total = slides.length;

    if (total <= 1) return;

    let currentIndex = 0;
    let autoPlayTimer = null;

    function updateCoverflow(index) {
      currentIndex = (index + total) % total;

      slides.forEach((slide, i) => {
        slide.classList.remove('state-center', 'state-left', 'state-right', 'state-hidden');
        if (i === currentIndex) {
          slide.classList.add('state-center');
        } else if (i === (currentIndex + 1) % total) {
          slide.classList.add('state-right');
        } else if (i === (currentIndex - 1 + total) % total) {
          slide.classList.add('state-left');
        } else {
          slide.classList.add('state-hidden');
        }
      });

      dots.forEach((dot, i) => {
        if (i === currentIndex) {
          dot.className = 'h-1.5 rounded-full transition-all duration-300 w-6 bg-blue-600';
        } else {
          dot.className = 'h-1.5 rounded-full transition-all duration-300 w-2 bg-slate-300';
        }
      });
    }

    function nextSlide() {
      updateCoverflow(currentIndex + 1);
    }

    function prevSlide() {
      updateCoverflow(currentIndex - 1);
    }

    function startAutoPlay() {
      stopAutoPlay();
      autoPlayTimer = setInterval(nextSlide, 4000);
    }

    function stopAutoPlay() {
      if (autoPlayTimer) {
        clearInterval(autoPlayTimer);
        autoPlayTimer = null;
      }
    }

    // Button event listeners
    btnNext?.addEventListener('click', function(e) {
      e.preventDefault();
      nextSlide();
      startAutoPlay();
    });

    btnPrev?.addEventListener('click', function(e) {
      e.preventDefault();
      prevSlide();
      startAutoPlay();
    });

    // Dot click listeners
    dots.forEach((dot, i) => {
      dot.addEventListener('click', function(e) {
        e.preventDefault();
        updateCoverflow(i);
        startAutoPlay();
      });
    });

    // Slide direct click listeners (clicking left/right slide)
    slides.forEach((slide, i) => {
      slide.addEventListener('click', function(e) {
        if (i !== currentIndex) {
          e.preventDefault();
          updateCoverflow(i);
          startAutoPlay();
        }
      });
    });

    // Touch Swipe Support
    let touchStartX = 0;
    let touchEndX = 0;

    track.addEventListener('touchstart', function(e) {
      touchStartX = e.changedTouches[0].screenX;
      stopAutoPlay();
    }, { passive: true });

    track.addEventListener('touchend', function(e) {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
      startAutoPlay();
    }, { passive: true });

    function handleSwipe() {
      const diff = touchEndX - touchStartX;
      if (Math.abs(diff) > 40) {
        if (diff < 0) {
          nextSlide();
        } else {
          prevSlide();
        }
      }
    }

    // Pause auto-play on mouse enter, resume on mouse leave
    track.addEventListener('mouseenter', stopAutoPlay);
    track.addEventListener('mouseleave', startAutoPlay);

    // Initial activation
    updateCoverflow(0);
    startAutoPlay();
  });
</script>
