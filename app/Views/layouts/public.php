<?php
    $storeSettings = new \App\Models\StoreSettingModel();
    $storeName = $storeSettings->getVal('store_name', 'Ayong Store');
    $storeLogo = $storeSettings->getVal('store_logo');
    $storeContact = $storeSettings->getVal('store_contact');
    $metaTitle = isset($title) ? $title : ($storeName . ' - Top Up & Game Store Express 24 Jam');
    $waNum = ! empty($storeContact) ? preg_replace('/[^0-9]/', '', $storeContact) : '';
    $waUrl = ! empty($waNum) ? 'https://wa.me/' . $waNum : 'https://wa.me/';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= csrf_meta() ?>
    <title><?= esc($metaTitle) ?></title>
    <?php if (! empty($heroPreloadImage)): ?>
        <link rel="preload" as="image" href="<?= $heroPreloadImage ?>" fetchpriority="high" <?= ! empty($heroPreloadSrcset) ? 'imagesrcset="' . $heroPreloadSrcset . '" imagesizes="(min-width: 1024px) 748px, (min-width: 768px) 62vw, (min-width: 640px) 72vw, 82vw"' : '' ?>>
    <?php endif; ?>
    
    <!-- Dynamic Metadata -->
    <?php $metaDesc = $metaDescription ?? 'Platform top up koin emas & item game otomatis, cepat, aman, dan terpercaya 24 jam nonstop.'; ?>
    <meta name="description" content="<?= esc($metaDesc) ?>">
    <meta name="keywords" content="<?= esc($metaKeywords ?? 'top up higgs, koin emas higgs, bongkar chip, topup higgs domino') ?>">
    <meta name="robots" content="<?= (! empty($noindex)) ? 'noindex, nofollow' : 'index, follow' ?>">
    <link rel="canonical" href="<?= current_url() ?>">

    <!-- OpenGraph Metadata -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="<?= esc($metaTitle) ?>">
    <meta property="og:description" content="<?= esc($metaDesc) ?>">
    <meta property="og:site_name" content="<?= esc($storeName) ?>">
    <?php if ($storeLogo): ?>
        <meta property="og:image" content="<?= base_url($storeLogo) ?>">
        <link rel="icon" href="<?= base_url($storeLogo) ?>" type="image/png">
        <link rel="shortcut icon" href="<?= base_url($storeLogo) ?>" type="image/png">
    <?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($metaTitle) ?>">
    <meta name="twitter:description" content="<?= esc($metaDesc) ?>">
    <?php if ($storeLogo): ?>
        <meta name="twitter:image" content="<?= base_url($storeLogo) ?>">
    <?php endif; ?>

    <!-- Structured Data -->
    <script type="application/ld+json">
    <?= json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => $storeName,
        'url'      => base_url('/'),
        'logo'     => $storeLogo ? base_url($storeLogo) : null,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800;900&family=Material+Symbols+Outlined:wght,FILL@400,0&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800;900&family=Material+Symbols+Outlined:wght,FILL@400,0&display=swap"></noscript>

    <?php $publicCssVersion = is_file(FCPATH . 'assets/css/public.css') ? filemtime(FCPATH . 'assets/css/public.css') : time(); ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/public.css') ?>?v=<?= $publicCssVersion ?>">
    <style>
        @media print {
            header, footer, nav, .announcement-ticker { display: none !important; }
        }
    </style>
</head>
<body class="light-felt-pattern font-sans text-neutral-800 antialiased selection:bg-blue-100 selection:text-blue-900 min-h-screen flex flex-col justify-between pb-28 md:pb-0">

    <!-- Header Navigation Bar -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-[1360px] mx-auto px-4 sm:px-6 h-16 sm:h-18 py-2 flex items-center justify-between gap-3">
            <!-- Brand Logo -->
            <a class="flex items-center group transition-transform hover:scale-[1.01]" href="<?= base_url('/') ?>">
                <?php if ($storeLogo): ?>
                    <img alt="<?= esc($storeName) ?> Logo" class="h-10 sm:h-12 w-auto object-contain" src="<?= base_url($storeLogo) ?>">
                <?php else: ?>
                    <span class="font-display text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[24px] text-blue-600">bolt</span>
                        <?= esc($storeName) ?>
                    </span>
                <?php endif; ?>
            </a>

            <!-- Nav Links Cockpit Pill -->
            <nav class="hidden md:flex items-center gap-1 bg-slate-100/90 p-1.5 rounded-xl border border-slate-200/90 text-xs font-semibold shadow-inner">
                <a class="px-3.5 py-1.5 rounded-lg bg-blue-600 text-white font-bold shadow-xs flex items-center gap-1.5 transition-all" href="<?= base_url('/') ?>">
                    <span class="material-symbols-outlined text-[16px]">home</span> Beranda
                </a>
                <a class="px-3.5 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-white transition-all flex items-center gap-1.5" href="<?= base_url('/#katalog-section') ?>">
                    <span class="material-symbols-outlined text-[16px]">sports_esports</span> Katalog Produk
                </a>
                <a class="px-3.5 py-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-white transition-all flex items-center gap-1.5" href="<?= base_url('cek-pesanan') ?>">
                    <span class="material-symbols-outlined text-[16px]">receipt_long</span> Cek Status Pesanan
                </a>
            </nav>

            <!-- Header Action Support -->
            <div class="flex items-center gap-2">
                <a class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold transition-all shadow-xs border border-emerald-500/30" href="<?= esc($waUrl) ?>" rel="noopener noreferrer" target="_blank">
                    <span class="material-symbols-outlined text-[18px]">chat</span>
                    <span class="hidden sm:inline">CS WhatsApp 24 Jam</span>
                    <span class="sm:hidden">CS WA</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Live Gaming Ticker -->
    <?php $announcements = (new \App\Models\AnnouncementModel())->listActive(); ?>
    <?php if (! empty($announcements)): ?>
    <div class="announcement-ticker bg-slate-900 text-slate-200 text-xs py-2 px-4 font-bold shadow-xs relative overflow-hidden border-b border-slate-800">
        <div class="max-w-[1360px] mx-auto flex items-center gap-3 overflow-hidden text-xs">
            <div class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-amber-500 text-neutral-950 font-black text-[10px] uppercase tracking-wider shrink-0 shadow-xs">
                <span class="material-symbols-outlined text-[14px]">campaign</span>
                <span>INFO RESMI</span>
            </div>
            <div class="flex-1 overflow-hidden relative">
                <div class="animate-marquee whitespace-nowrap flex items-center gap-8 text-slate-200 font-medium text-xs">
                    <?php foreach (array_merge($announcements, $announcements) as $i => $announcement): ?>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full <?= $i % 2 === 0 ? 'bg-amber-400' : 'bg-emerald-400' ?>"></span>
                            <?= esc($announcement['message']) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- MAIN CONTENT SECTION -->
    <div class="flex-1">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Footer (Professional Gaming Theme) -->
    <footer class="mt-16 bg-slate-950 text-slate-400 text-xs pb-20 lg:pb-8 border-t border-slate-800">
        <div class="max-w-[1360px] mx-auto px-4 sm:px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-slate-800">
                <!-- Brand & Info -->
                <div class="space-y-4">
                    <a class="flex items-center gap-3" href="<?= base_url('/') ?>">
                        <?php if ($storeLogo): ?>
                            <img alt="<?= esc($storeName) ?> Logo" class="h-9 w-auto object-contain" src="<?= base_url($storeLogo) ?>">
                        <?php else: ?>
                            <span class="font-display font-black text-base text-white tracking-tight"><?= esc($storeName) ?></span>
                        <?php endif; ?>
                    </a>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Platform layanan top-up game &amp; voucher digital otomatis. Transaksi cepat, harga bersaing, dan pengiriman otomatis 24 jam nonstop.
                    </p>
                    <div class="flex items-center gap-2 text-xs text-emerald-400 bg-emerald-500/10 px-3 py-1.5 rounded-lg border border-emerald-500/20 w-fit font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Sistem Otomatis Instan 24/7</span>
                    </div>
                </div>

                <!-- Navigasi -->
                <div class="space-y-3">
                    <h4 class="font-display font-bold text-xs text-white tracking-wider uppercase">Navigasi Utama</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a class="hover:text-amber-400 transition-colors flex items-center gap-1.5" href="<?= base_url('/') ?>"><span class="material-symbols-outlined text-[14px] text-slate-500">chevron_right</span> Beli / Top Up Koin</a></li>
                        <li><a class="hover:text-amber-400 transition-colors flex items-center gap-1.5" href="<?= base_url('/#jual') ?>"><span class="material-symbols-outlined text-[14px] text-slate-500">chevron_right</span> Jual / Bongkar Chip</a></li>
                        <li><a class="hover:text-amber-400 transition-colors flex items-center gap-1.5" href="<?= base_url('cek-pesanan') ?>"><span class="material-symbols-outlined text-[14px] text-slate-500">chevron_right</span> Cek Status Pesanan</a></li>
                        <li><a class="hover:text-amber-400 transition-colors flex items-center gap-1.5" href="<?= esc($waUrl) ?>" target="_blank" rel="noopener noreferrer"><span class="material-symbols-outlined text-[14px] text-slate-500">chevron_right</span> Bantuan CS WhatsApp</a></li>
                    </ul>
                </div>

                <!-- Metode Pembayaran -->
                <div class="space-y-3">
                    <h4 class="font-display font-bold text-xs text-white tracking-wider uppercase">Metode Pembayaran</h4>
                    <p class="text-xs text-slate-400">Mendukung saluran pembayaran instan terverifikasi:</p>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-slate-900 border border-slate-800 rounded-lg p-2 text-center text-[10px] font-extrabold text-slate-200">QRIS</div>
                        <div class="bg-slate-900 border border-slate-800 rounded-lg p-2 text-center text-[10px] font-extrabold text-blue-400">GoPay</div>
                        <div class="bg-slate-900 border border-slate-800 rounded-lg p-2 text-center text-[10px] font-extrabold text-sky-400">DANA</div>
                        <div class="bg-slate-900 border border-slate-800 rounded-lg p-2 text-center text-[10px] font-extrabold text-orange-400">ShopeePay</div>
                        <div class="bg-slate-900 border border-slate-800 rounded-lg p-2 text-center text-[10px] font-extrabold text-purple-400">OVO</div>
                        <div class="bg-slate-900 border border-slate-800 rounded-lg p-2 text-center text-[10px] font-extrabold text-red-400">LinkAja</div>
                        <div class="bg-slate-900 border border-slate-800 rounded-lg p-2 text-center text-[10px] font-extrabold text-blue-300">BCA VA</div>
                        <div class="bg-slate-900 border border-slate-800 rounded-lg p-2 text-center text-[10px] font-extrabold text-amber-300">Mandiri VA</div>
                        <div class="bg-slate-900 border border-slate-800 rounded-lg p-2 text-center text-[10px] font-extrabold text-sky-300">BRI VA</div>
                    </div>
                </div>

                <!-- Bantuan CS -->
                <div class="space-y-3">
                    <h4 class="font-display font-bold text-xs text-white tracking-wider uppercase">Layanan Bantuan</h4>
                    <p class="text-xs text-slate-400">Tim Customer Service aktif membantu kendala transaksi Anda:</p>
                    <a class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-sm" href="<?= esc($waUrl) ?>" rel="noopener noreferrer" target="_blank">
                        <span class="material-symbols-outlined text-[18px]">support_agent</span>
                        <span>Hubungi CS WhatsApp</span>
                    </a>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400 pt-1">
                        <span class="material-symbols-outlined text-[15px] text-blue-400">verified_user</span>
                        <span>Sistem Terenkripsi &amp; Garansi Resmi</span>
                    </div>
                </div>
            </div>

            <!-- Copyright Bottom Bar -->
            <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-400">
                <p class="text-center md:text-left">© <?= date('Y') ?> <strong><?= esc($storeName) ?></strong>. All rights reserved.</p>
                <p class="text-center md:text-right text-[11px] text-slate-500">Hak cipta seluruh merek dagang &amp; aset game milik penerbit masing-masing.</p>
            </div>
        </div>
    </footer>

    <!-- CSS Helper for Mobile Navigation & Hiding on Desktop -->
    <style>
        @media (min-width: 768px) {
            .mobile-only-nav,
            .mobile-only-sticky {
                display: none !important;
            }
        }
    </style>

    <!-- Mobile Bottom Navigation (Only Beranda & Cek Pesanan - Flat Rectangular White Bar) -->
    <?php $uriPath = parse_url(current_url(), PHP_URL_PATH) ?? '/'; ?>
    <?php 
        $isHome = ($uriPath === '/' || $uriPath === '/index.php' || empty($uriPath));
        $isCek = str_contains($uriPath, 'cek-pesanan');
    ?>
    <nav class="mobile-only-nav" style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 999; background: #ffffff; border-top: 1px solid #e2e8f0; border-radius: 0px !important; padding: 6px 12px; box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.08); display: flex; align-items: center; justify-content: space-around; height: 58px;">
        <a href="<?= base_url('/') ?>" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px 16px; border-radius: 8px; text-decoration: none; flex: 1; transition: all 0.2s ease; <?= $isHome ? 'background: #eff6ff; color: #2563eb; font-weight: 800; border: 1px solid #bfdbfe;' : 'color: #0f172a; font-weight: 700;' ?>">
            <span class="material-symbols-outlined" style="font-size: 20px;">home</span>
            <span style="font-size: 12px; font-family: Plus Jakarta Sans, Inter, sans-serif;">Beranda</span>
        </a>

        <a href="<?= base_url('cek-pesanan') ?>" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px 16px; border-radius: 8px; text-decoration: none; flex: 1; transition: all 0.2s ease; <?= $isCek ? 'background: #eff6ff; color: #2563eb; font-weight: 800; border: 1px solid #bfdbfe;' : 'color: #0f172a; font-weight: 700;' ?>">
            <span class="material-symbols-outlined" style="font-size: 20px;">receipt_long</span>
            <span style="font-size: 12px; font-family: Plus Jakarta Sans, Inter, sans-serif;">Cek Pesanan</span>
        </a>
    </nav>
</body>
</html>
