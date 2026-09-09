<?php
    $storeSettings = new \App\Models\StoreSettingModel();
    $storeName = $storeSettings->getVal('store_name', 'Ayong Store');
    $storeLogo = $storeSettings->getVal('store_logo');
    $metaTitle = isset($title) ? $title : ($storeName . ' - Top Up & Game Store Express');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($metaTitle) ?></title>
    
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <style>
        @layer base {
          html, body { margin: 0; padding: 0; }
          body { overscroll-behavior: none; }
        }
        ::-webkit-scrollbar { display: none; }
        
        /* Clean Light Theme Gaming Ambient Texture */
        .light-felt-pattern {
          background-color: #f8faff;
          background-image: 
            radial-gradient(circle at 10% 15%, rgba(37, 99, 235, 0.04) 0%, transparent 40%),
            radial-gradient(circle at 90% 85%, rgba(245, 158, 11, 0.05) 0%, transparent 45%),
            radial-gradient(rgba(148, 163, 184, 0.12) 1px, transparent 1px);
          background-size: 100% 100%, 100% 100%, 24px 24px;
        }

        .shimmer-gold {
          background: linear-gradient(90deg, #F59E0B 0%, #FDE68A 50%, #D97706 100%);
          background-size: 200% auto;
          animation: shine 3s linear infinite;
        }

        @keyframes shine {
          to {
            background-position: 200% center;
          }
        }

        @keyframes pulse-glow {
          0%, 100% { box-shadow: 0 0 12px rgba(245, 158, 11, 0.25); }
          50% { box-shadow: 0 0 22px rgba(245, 158, 11, 0.55); }
        }
        .gold-glow {
          animation: pulse-glow 2.5s infinite;
        }

        @keyframes marquee {
          0% { transform: translateX(0%); }
          100% { transform: translateX(-50%); }
        }
        .animate-marquee {
          display: flex;
          width: max-content;
          animation: marquee 24s linear infinite;
        }
        .animate-marquee:hover {
          animation-play-state: paused;
        }

        /* 3D Domino Tile Mini component */
        .domino-chip {
          background: #FFFFFF;
          border-radius: 4px;
          box-shadow: inset 0 -1px 0 rgba(0,0,0,0.15), 0 2px 4px rgba(0,0,0,0.12);
          border: 1px solid #CBD5E1;
          display: inline-flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          padding: 2px;
          position: relative;
        }
        .domino-chip::after {
          content: '';
          width: 80%;
          height: 1px;
          background: #94A3B8;
          margin: 1px 0;
        }
    </style>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "background": "#f8f9ff",
              "surface": "#ffffff",
              "surface-card": "#ffffff",
              "surface-border": "#e2e8f0",
              "primary": "#004ac6",
              "primary-container": "#2563eb",
              "gold-accent": "#F59E0B",
              "gold-light": "#FEF3C7",
              "emerald-table": "#059669",
              "neutral-900": "#0F172A",
              "neutral-800": "#1E293B",
              "neutral-700": "#334155",
              "neutral-600": "#475569",
              "neutral-500": "#64748B",
              "neutral-200": "#E2E8F0",
              "neutral-100": "#F1F5F9",
              "neutral-50": "#F8FAFC",
              "success": "#16A34A",
              "warning": "#F59E0B",
              "danger": "#DC2626"
            },
            fontFamily: {
              sans: ["Inter", "sans-serif"],
              display: ["Plus Jakarta Sans", "sans-serif"]
            },
            borderRadius: {
              "DEFAULT": "0.375rem",
              "md": "0.5rem",
              "lg": "0.75rem",
              "xl": "1rem",
              "2xl": "1.25rem",
              "full": "9999px"
            }
          }
        }
      }
    </script>
</head>
<body class="light-felt-pattern font-sans text-neutral-800 antialiased selection:bg-amber-100 selection:text-amber-900 min-h-screen">
    <?php $storeLogo = (new \App\Models\StoreSettingModel())->getVal('store_logo'); ?>
    <!-- Top Navigation Bar (Clean Light Theme) -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm">
        <div class="max-w-[1360px] mx-auto px-4 sm:px-6 h-18 py-2.5 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a class="flex items-center group transition-transform hover:scale-[1.01]" href="<?= base_url('/') ?>">
                <div class="h-10 sm:h-11 px-3 py-1 bg-slate-50 rounded-xl shadow-xs border border-slate-200 flex items-center justify-center">
                    <?php if ($storeLogo): ?>
                        <img alt="Ayong Store Logo" class="h-7 sm:h-8 w-auto object-contain" src="<?= base_url($storeLogo) ?>">
                    <?php else: ?>
                        <span class="font-display text-sm sm:text-base font-black text-primary tracking-tight">Ayong Store</span>
                    <?php endif; ?>
                </div>
            </a>
            <!-- Nav Links Cockpit Pill -->
            <nav class="hidden md:flex items-center gap-1 bg-slate-100/90 p-1.5 rounded-xl border border-slate-200 text-xs font-semibold shadow-inner">
                <a class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-bold shadow-sm flex items-center gap-1.5" href="<?= base_url('/') ?>"><span class="material-symbols-outlined text-[16px]">home</span> Beranda</a>
                <a class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-neutral-900 hover:bg-white transition-all flex items-center gap-1.5" href="<?= base_url('/#katalog-section') ?>"><span class="material-symbols-outlined text-[16px]">inventory_2</span> Katalog Produk</a>
                <a class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-neutral-900 hover:bg-white transition-all flex items-center gap-1.5" href="<?= base_url('cek-pesanan') ?>"><span class="material-symbols-outlined text-[16px]">receipt_long</span> Cek Status Pesanan</a>
            </nav>
            <!-- Header Action Support -->
            <div class="flex items-center gap-2.5">
                <a class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold transition-all shadow-sm border border-emerald-500/30" href="https://wa.me/" rel="noopener noreferrer" target="_blank">
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
    <div class="bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-500 text-neutral-900 text-xs py-1.5 px-4 font-bold shadow-xs relative overflow-hidden border-b border-amber-300">
        <div class="max-w-[1360px] mx-auto flex items-center gap-3 overflow-hidden text-xs">
            <div class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-slate-900 text-amber-300 font-extrabold text-[10px] uppercase tracking-wider shrink-0 shadow-xs">
                <span class="material-symbols-outlined text-[13px] text-amber-400">campaign</span>
                <span class="">INFO RESMI</span>
            </div>
            <div class="flex-1 overflow-hidden relative">
                <div class="animate-marquee whitespace-nowrap flex items-center gap-8 text-neutral-900 font-semibold text-xs">
                    <?php // Daftar digandakan 2x agar animasi marquee (translateX -50%) terlihat menyambung tanpa jeda. ?>
                    <?php foreach (array_merge($announcements, $announcements) as $i => $announcement): ?>
                        <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full <?= $i % 2 === 0 ? 'bg-blue-700' : 'bg-emerald-700' ?>"></span><?= esc($announcement['message']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- MAIN CONTENT SECTION -->
    <?= $this->renderSection('content') ?>

    <!-- Footer (Professional & Universal Dark Theme) -->
    <footer class="mt-16 bg-slate-900 text-slate-400 text-xs pb-20 lg:pb-8 border-t border-slate-800">
        <div class="max-w-[1360px] mx-auto px-4 sm:px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-slate-800">
                <!-- Brand & Info -->
                <div class="space-y-4">
                    <a class="flex items-center gap-3" href="<?= base_url('/') ?>">
                        <div class="h-10 px-3 py-1 bg-white/10 backdrop-blur rounded-xl border border-white/10 flex items-center justify-center">
                            <?php if ($storeLogo): ?>
                                <img alt="Store Logo" class="h-7 w-auto object-contain" src="<?= base_url($storeLogo) ?>">
                            <?php else: ?>
                                <span class="font-display text-sm font-black text-white tracking-tight">Ayong Store</span>
                            <?php endif; ?>
                        </div>
                        <span class="font-display font-bold text-base text-white tracking-tight">Ayong Store</span>
                    </a>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Platform layanan top-up game &amp; voucher digital otomatis. Transaksi cepat, aman, dan dapat diakses 24 jam setiap hari.
                    </p>
                    <div class="flex items-center gap-2 text-xs text-emerald-400 bg-emerald-500/10 px-3 py-1.5 rounded-lg border border-emerald-500/20 w-fit font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Layanan Otomatis 24/7</span>
                    </div>
                </div>

                <!-- Navigasi -->
                <div class="space-y-3">
                    <h4 class="font-display font-bold text-xs text-white tracking-wider uppercase">Navigasi</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a class="hover:text-white transition-colors flex items-center gap-1.5" href="<?= base_url('/') ?>"><span class="material-symbols-outlined text-[14px] text-slate-500">chevron_right</span> Top Up &amp; Katalog</a></li>
                        <li><a class="hover:text-white transition-colors flex items-center gap-1.5" href="<?= base_url('/#jual') ?>"><span class="material-symbols-outlined text-[14px] text-slate-500">chevron_right</span> Jual / Bongkar Chip</a></li>
                        <li><a class="hover:text-white transition-colors flex items-center gap-1.5" href="<?= base_url('cek-pesanan') ?>"><span class="material-symbols-outlined text-[14px] text-slate-500">chevron_right</span> Cek Status Pesanan</a></li>
                        <li><a class="hover:text-white transition-colors flex items-center gap-1.5" href="<?= ! empty($storeLogo) ? 'https://wa.me/' : '#' ?>" target="_blank" rel="noopener noreferrer"><span class="material-symbols-outlined text-[14px] text-slate-500">chevron_right</span> Pusat Bantuan</a></li>
                    </ul>
                </div>

                <!-- Metode Pembayaran -->
                <div class="space-y-3">
                    <h4 class="font-display font-bold text-xs text-white tracking-wider uppercase">Pembayaran</h4>
                    <p class="text-xs text-slate-400">Mendukung berbagai saluran pembayaran instan:</p>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-lg p-2 text-center text-[10px] font-bold text-slate-200">QRIS</div>
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-lg p-2 text-center text-[10px] font-bold text-slate-200">GoPay</div>
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-lg p-2 text-center text-[10px] font-bold text-slate-200">DANA</div>
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-lg p-2 text-center text-[10px] font-bold text-slate-200">ShopeePay</div>
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-lg p-2 text-center text-[10px] font-bold text-slate-200">OVO</div>
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-lg p-2 text-center text-[10px] font-bold text-slate-200">LinkAja</div>
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-lg p-2 text-center text-[10px] font-bold text-slate-200">BCA VA</div>
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-lg p-2 text-center text-[10px] font-bold text-slate-200">Mandiri VA</div>
                        <div class="bg-slate-800/80 border border-slate-700/60 rounded-lg p-2 text-center text-[10px] font-bold text-slate-200">BRI VA</div>
                    </div>
                </div>

                <!-- Bantuan CS -->
                <div class="space-y-3">
                    <h4 class="font-display font-bold text-xs text-white tracking-wider uppercase">Layanan Pelanggan</h4>
                    <p class="text-xs text-slate-400">Hubungi tim kami jika mengalami kendala transaksi:</p>
                    <a class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-sm" href="https://wa.me/" rel="noopener noreferrer" target="_blank">
                        <span class="material-symbols-outlined text-[18px]">support_agent</span>
                        <span>Customer Service</span>
                    </a>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400 pt-1">
                        <span class="material-symbols-outlined text-[15px] text-blue-400">shield</span>
                        <span>Transaksi Aman &amp; Terenkripsi</span>
                    </div>
                </div>
            </div>

            <!-- Copyright Bottom Bar -->
            <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-400">
                <p class="text-center md:text-left">© <?= date('Y') ?> <strong>Ayong Store</strong>. All rights reserved.</p>
                <p class="text-center md:text-right text-[11px] text-slate-400">Hak cipta seluruh merek dagang &amp; aset game milik penerbit masing-masing.</p>
            </div>
        </div>
    </footer>
</body>
</html>



