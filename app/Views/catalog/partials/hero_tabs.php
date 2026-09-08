<!-- CSS HELPER FOR TABS & PILLS -->
<style>
  .tab-mode-hidden {
    display: none !important;
  }
  .category-pill.active .pill-icon {
    color: #ffffff !important;
  }
</style>

<!-- DYNAMIC HORIZONTAL SLIDESHOW HERO BANNER -->
<?php
  $defaultFallbackImage = 'https://lh3.googleusercontent.com/aida/AEtjO1Xkmeor5HdJYUSrixZ-AzI0Nncuf7tYmyNxC1SwZdCDjOMU2BjepgpLXbad3fySmsQm7rP5nP-ptDLEIo2MMimWSzGcIHVkQSlrxiOr-zLVdB_OvX-VtyWrOJh0BvOZilFEiQ8h4Ck8egDDGp3p68c22YensCJWpq6l6pDVIJCn9oeXJMtojO-IKJOU47c-kgqr7XlYTNou8LADwr6yjDGsxmgUgT3SlDg5R8tjmBh1dHMjUbENtang-g';

  $bannerList = ! empty($banners) ? $banners : [];

  // Ensure at least 2 slide items exist so the slide animation works dynamically
  if (count($bannerList) === 0) {
      $bannerList = [
          ['image_path' => $defaultFallbackImage, 'link_url' => '', 'title' => 'Banner Promo 1'],
          ['image_path' => $defaultFallbackImage, 'link_url' => '', 'title' => 'Banner Promo 2'],
      ];
  } elseif (count($bannerList) === 1) {
      $bannerList[] = [
          'image_path' => $bannerList[0]['image_path'],
          'link_url'   => $bannerList[0]['link_url'] ?? '',
          'title'      => $bannerList[0]['title'] ?? 'Banner Promo 2',
      ];
  }
?>

<div class="max-w-[1360px] mx-auto px-4 sm:px-6 pt-5 pb-2">
  <div class="relative rounded-2xl overflow-hidden shadow-lg border border-slate-200 bg-slate-900 group" id="hero-banner-carousel">
    
    <!-- Slides Container with 1920x1080 (16:9) Responsive Aspect Ratio -->
    <div class="relative w-full aspect-[16/9] overflow-hidden">
      <div class="flex h-full w-full transition-transform duration-500 ease-out" id="banner-slides-track">
        <?php foreach ($bannerList as $bIndex => $b): ?>
          <?php 
            $imgUrl = str_starts_with($b['image_path'], 'http') ? $b['image_path'] : base_url($b['image_path']);
          ?>
          <div class="banner-slide w-full h-full shrink-0 relative" data-index="<?= $bIndex ?>">
            <?php if (! empty($b['link_url'])): ?>
              <a href="<?= esc($b['link_url']) ?>" target="_blank" rel="noopener noreferrer" class="block w-full h-full">
            <?php endif; ?>
              <img alt="<?= esc($b['title'] ?? 'Banner Promo') ?>" class="w-full h-full object-cover object-center select-none" src="<?= $imgUrl ?>">
            <?php if (! empty($b['link_url'])): ?>
              </a>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Controls: Prev & Next Buttons -->
    <button type="button" id="banner-prev" class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-9.5 h-9.5 rounded-full bg-black/40 hover:bg-black/75 text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 backdrop-blur-xs cursor-pointer shadow-md" aria-label="Previous Slide">
      <span class="material-symbols-outlined text-[22px]">chevron_left</span>
    </button>
    <button type="button" id="banner-next" class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-9.5 h-9.5 rounded-full bg-black/40 hover:bg-black/75 text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100 backdrop-blur-xs cursor-pointer shadow-md" aria-label="Next Slide">
      <span class="material-symbols-outlined text-[22px]">chevron_right</span>
    </button>

    <!-- Pagination Indicators / Dots -->
    <div class="absolute bottom-3 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-black/35 backdrop-blur-xs" id="banner-dots">
      <?php foreach ($bannerList as $bIndex => $b): ?>
        <button type="button" class="banner-dot w-2.5 h-2.5 rounded-full transition-all duration-300 cursor-pointer <?= $bIndex === 0 ? 'bg-white w-6' : 'bg-white/50 hover:bg-white/80' ?>" data-slide="<?= $bIndex ?>" aria-label="Slide <?= $bIndex + 1 ?>"></button>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- Slideshow Auto-rotate Script (Horizontal Slide) -->
<script>
  (function() {
    const track = document.getElementById('banner-slides-track');
    if (!track) return;

    const slides = track.querySelectorAll('.banner-slide');
    const dots = document.querySelectorAll('.banner-dot');
    const prevBtn = document.getElementById('banner-prev');
    const nextBtn = document.getElementById('banner-next');

    if (slides.length <= 1) return;

    let currentIndex = 0;
    let timer = null;

    function goToSlide(index) {
      if (dots[currentIndex]) {
        dots[currentIndex].classList.remove('bg-white', 'w-6');
        dots[currentIndex].classList.add('bg-white/50');
      }

      currentIndex = (index + slides.length) % slides.length;
      track.style.transform = `translateX(-${currentIndex * 100}%)`;

      if (dots[currentIndex]) {
        dots[currentIndex].classList.remove('bg-white/50');
        dots[currentIndex].classList.add('bg-white', 'w-6');
      }
    }

    function startTimer() {
      stopTimer();
      timer = setInterval(() => {
        goToSlide(currentIndex + 1);
      }, 3500);
    }

    function stopTimer() {
      if (timer) clearInterval(timer);
    }

    prevBtn?.addEventListener('click', () => {
      goToSlide(currentIndex - 1);
      startTimer();
    });

    nextBtn?.addEventListener('click', () => {
      goToSlide(currentIndex + 1);
      startTimer();
    });

    dots.forEach((dot, idx) => {
      dot.addEventListener('click', () => {
        goToSlide(idx);
        startTimer();
      });
    });

    const carousel = document.getElementById('hero-banner-carousel');
    carousel?.addEventListener('mouseenter', stopTimer);
    carousel?.addEventListener('mouseleave', startTimer);

    startTimer();
  })();
</script>

<!-- MAIN MODE SWITCHER TABS (BELI VS JUAL) -->
<div class="max-w-[1360px] mx-auto px-4 sm:px-6 pt-4 pb-1">
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
