<?php
    $storeSettings = new \App\Models\StoreSettingModel();
    $storeName = $storeSettings->getVal('store_name', 'Ayong Store');
    $storeLogo = $storeSettings->getVal('store_logo');
    $storeContact = $storeSettings->getVal('store_contact');
    $metaTitle = isset($title) ? $title : ($storeName . ' - Top Up Koin Emas Higgs Domino & Global Murah 24 Jam');
    $waUrl = whatsapp_url($storeContact);
    // What the footer and the structured data say about payment comes from the channels the admin switched on.
    $footerChannelNames = array_map(
        static fn (array $channel): string => (string) $channel['name'],
        (new \App\Models\PaymentChannelModel())->listActive()
    );
    $logoUrl = ! empty($storeLogo) ? base_url($storeLogo) : base_url('assets/img/logo.png');
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
    <?php $metaDesc = $metaDescription ?? 'Top up koin emas Higgs Domino Island & Global dan bongkar kartu. Bayar lewat transfer bank atau QRIS, tanpa biaya layanan dan tanpa password akun.'; ?>
    <meta name="description" content="<?= esc($metaDesc) ?>">
    <meta name="keywords" content="<?= esc($metaKeywords ?? 'top up higgs domino, top up koin emas higgs, bongkar chip higgs domino, top up higgs global, ayong store, topup koin emas murah, jual koin higgs domino, beli chip higgs') ?>">
    <meta name="robots" content="<?= (! empty($noindex)) ? 'noindex, nofollow' : 'index, follow' ?>">
    <link rel="canonical" href="<?= current_url() ?>">

    <!-- Favicon & Touch Icon Specs for Googlebot / Search Engine Results -->
    <link rel="icon" href="<?= esc($logoUrl) ?>" sizes="32x32 48x48 96x96 192x192 512x512" type="image/png">
    <link rel="icon" href="<?= esc($logoUrl) ?>" type="image/png">
    <link rel="shortcut icon" href="<?= esc($logoUrl) ?>" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?= esc($logoUrl) ?>">

    <!-- OpenGraph Metadata -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="<?= esc($metaTitle) ?>">
    <meta property="og:description" content="<?= esc($metaDesc) ?>">
    <meta property="og:site_name" content="<?= esc($storeName) ?>">
    <meta property="og:image" content="<?= esc($logoUrl) ?>">
    <meta property="og:image:secure_url" content="<?= esc($logoUrl) ?>">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="512">
    <meta property="og:image:height" content="512">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($metaTitle) ?>">
    <meta name="twitter:description" content="<?= esc($metaDesc) ?>">
    <meta name="twitter:image" content="<?= esc($logoUrl) ?>">

    <!-- Structured Data (Google Rich Results & Favicon Schema) -->
    <script type="application/ld+json">
    <?= json_encode([
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'       => 'WebSite',
                '@id'         => base_url('/#website'),
                'url'         => base_url('/'),
                'name'        => $storeName,
                'description' => $metaDesc,
                'inLanguage'  => 'id-ID',
            ],
            [
                '@type' => 'Organization',
                '@id'   => base_url('/#organization'),
                'name'  => $storeName,
                'url'   => base_url('/'),
                'logo'  => [
                    '@type'  => 'ImageObject',
                    'url'    => $logoUrl,
                    'width'  => 512,
                    'height' => 512,
                ],
                'image' => [
                    '@id' => base_url('/#organization'),
                ],
            ],
            [
                '@type'              => 'Store',
                'name'               => $storeName,
                'description'        => 'Top up koin emas Higgs Domino Island & Global dan bongkar kartu',
                'url'                => base_url('/'),
                'image'              => $logoUrl,
                'currenciesAccepted' => 'IDR',
            ] + ($footerChannelNames !== [] ? ['paymentAccepted' => implode(', ', $footerChannelNames)] : []),
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
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

    <a class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[1000] focus:rounded-lg focus:bg-slate-950 focus:px-4 focus:py-2.5 focus:text-sm focus:font-semibold focus:text-white" href="#konten-utama">Lewati ke konten</a>

    <?php
        $navPath  = trim(uri_string(), '/');
        $navItems = [
            ['mode' => 'buy', 'label' => 'Beli Koin', 'href' => base_url('/'), 'icon' => 'payments',
             'current' => $navPath === '' || str_starts_with($navPath, 'kategori')],
            ['mode' => 'sell', 'label' => 'Jual Chip', 'href' => base_url('/#jual'), 'icon' => 'sell',
             'current' => false],
            ['mode' => null, 'label' => 'Cek Pesanan', 'href' => base_url('cek-pesanan'), 'icon' => 'receipt_long',
             'current' => str_starts_with($navPath, 'cek-pesanan') || str_starts_with($navPath, 'pesanan')],
        ];
    ?>

    <!-- Header: solid bar, active link's underline sits on the bar's bottom rule -->
    <header class="sticky top-0 z-50 border-b border-slate-200 bg-white">
        <div class="mx-auto flex h-14 max-w-[1360px] items-stretch gap-6 px-4 sm:px-6 md:h-16">
            <a class="flex items-center self-center rounded-md focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-blue-600" href="<?= base_url('/') ?>">
                <?php if ($storeLogo): ?>
                    <img alt="<?= esc($storeName) ?>" class="h-9 w-auto object-contain md:h-10" src="<?= base_url($storeLogo) ?>">
                <?php else: ?>
                    <span class="font-display text-lg font-extrabold tracking-tight text-slate-950 md:text-xl"><?= esc($storeName) ?></span>
                <?php endif; ?>
            </a>

            <nav class="hidden md:flex" aria-label="Menu utama">
                <?php foreach ($navItems as $item): ?>
                    <a class="relative flex items-center px-4 text-sm font-semibold text-slate-600 transition-colors hover:text-slate-950 focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-blue-600 aria-[current=page]:text-slate-950 after:absolute after:inset-x-4 after:-bottom-px after:h-0.5 after:bg-blue-600 after:opacity-0 aria-[current=page]:after:opacity-100"
                       href="<?= esc($item['href']) ?>"
                       <?= $item['mode'] ? 'data-nav-mode="' . $item['mode'] . '"' : '' ?>
                       <?= $item['current'] ? 'aria-current="page"' : '' ?>><?= esc($item['label']) ?></a>
                <?php endforeach; ?>
            </nav>

            <?php if ($waUrl !== ''): ?>
                <a class="ml-auto inline-flex h-11 items-center self-center rounded-lg bg-emerald-700 px-4 text-sm font-semibold text-white transition-colors hover:bg-emerald-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700" href="<?= esc($waUrl) ?>" rel="noopener noreferrer" target="_blank">
                    <span class="sm:hidden">Chat CS</span>
                    <span class="hidden sm:inline">Chat CS WhatsApp</span>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Info ticker: messages come from the announcements table (admin: Info Berjalan). -->
    <?php $announcements = (new \App\Models\AnnouncementModel())->listActive(); ?>
    <?php if (! empty($announcements)): ?>
    <?php
        // Scroll speed follows the amount of text, so it stays readable whatever the admin writes.
        $tickerChars   = array_sum(array_map(static fn (array $a): int => mb_strlen((string) $a['message']), $announcements));
        $tickerSeconds = max(30, (int) round($tickerChars * 0.13));
    ?>
    <section id="announcement-ticker" class="announcement-ticker overflow-hidden border-b border-slate-800 bg-slate-900 text-slate-100" aria-label="Info toko">
        <div class="flex h-10 items-center overflow-hidden">
            <div class="announcement-track whitespace-nowrap text-sm font-medium" style="--marquee-duration: <?= $tickerSeconds ?>s">
                <?php foreach ([false, true] as $isCopy): ?>
                    <ul class="flex shrink-0 items-center gap-x-4 pr-4"<?= $isCopy ? ' aria-hidden="true"' : '' ?>>
                        <?php foreach ($announcements as $announcement): ?>
                            <li><?= esc($announcement['message']) ?></li>
                            <li aria-hidden="true" class="text-slate-500">&middot;</li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- MAIN CONTENT SECTION -->
    <div class="flex-1" id="konten-utama">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Footer: light editorial. A statement and spec-sheet rows. Only states what the system really does. -->
    <?php
        $footerRing = 'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-blue-600';
        $footerLink = 'footer-link inline-flex min-h-11 items-center pb-0.5 text-base font-semibold text-slate-950 transition-colors hover:text-blue-700 ' . $footerRing;
    ?>
    <footer class="mt-16 border-t border-slate-200 bg-white pb-24 text-slate-600 md:pb-4" aria-label="Footer">
        <div class="mx-auto max-w-[1360px] px-4 pt-14 sm:px-6">
            <div class="grid gap-12 lg:grid-cols-12 lg:gap-x-14">
                <div class="footer-rise lg:col-span-5">
                    <?php if ($storeLogo): ?>
                        <a class="inline-flex min-h-11 items-center rounded-md <?= $footerRing ?>" href="<?= base_url('/') ?>">
                            <img alt="<?= esc($storeName) ?>" class="h-10 w-auto object-contain" src="<?= base_url($storeLogo) ?>">
                        </a>
                    <?php endif; ?>
                    <p class="<?= $storeLogo ? 'mt-5 ' : '' ?>max-w-md font-display text-2xl font-extrabold leading-tight tracking-tight text-slate-950 sm:text-3xl">Top up koin emas dan bongkar kartu Higgs Domino Island &amp; Global.</p>
                </div>

                <dl class="footer-rise lg:col-span-7">
                    <div class="footer-row grid gap-x-8 gap-y-1 py-6 md:grid-cols-[9rem_1fr]">
                        <dt class="text-sm font-semibold text-slate-600 md:pt-3">Menu</dt>
                        <dd>
                            <ul class="flex flex-wrap gap-x-8 gap-y-0">
                                <li><a class="<?= $footerLink ?>" href="<?= base_url('/') ?>">Beli Koin</a></li>
                                <li><a class="<?= $footerLink ?>" href="<?= base_url('/#jual') ?>">Jual Chip</a></li>
                                <li><a class="<?= $footerLink ?>" href="<?= base_url('cek-pesanan') ?>">Cek Pesanan</a></li>
                            </ul>
                        </dd>
                    </div>

                    <?php if ($footerChannelNames !== []): ?>
                    <div class="footer-row grid gap-x-8 gap-y-1 py-6 md:grid-cols-[9rem_1fr]">
                        <dt class="text-sm font-semibold text-slate-600">Pembayaran</dt>
                        <dd>
                            <ul class="flex flex-wrap items-center gap-x-3 gap-y-1 text-base font-semibold text-slate-950">
                                <?php foreach ($footerChannelNames as $i => $channelName): ?>
                                    <?php if ($i > 0): ?><li aria-hidden="true" class="text-slate-500">&middot;</li><?php endif; ?>
                                    <li><?= esc($channelName) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <p class="mt-1.5 text-sm text-slate-600">Bukti pembayaran diunggah di halaman invoice, lalu diverifikasi admin.</p>
                        </dd>
                    </div>
                    <?php endif; ?>

                    <div class="footer-row grid gap-x-8 gap-y-1 py-6 md:grid-cols-[9rem_1fr]">
                        <dt class="text-sm font-semibold text-slate-600 <?= $waUrl !== '' ? 'md:pt-3' : '' ?>">Bantuan</dt>
                        <dd>
                            <?php if ($waUrl !== ''): ?>
                                <a class="<?= $footerLink ?>" href="<?= esc($waUrl) ?>" target="_blank" rel="noopener noreferrer">Chat CS WhatsApp</a>
                                <p class="text-sm text-slate-600">Sertakan nomor invoice bila menanyakan pesanan.</p>
                            <?php else: ?>
                                <p class="text-base text-slate-950">Simpan nomor invoice dan token akses Anda untuk mengecek pesanan di halaman Cek Pesanan.</p>
                            <?php endif; ?>
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="mt-10 flex flex-col gap-1 border-t border-slate-200 py-6 text-sm text-slate-600 md:flex-row md:items-center md:justify-between">
                <p>&copy; <?= date('Y') ?> <?= esc($storeName) ?>. Merek dagang dan aset game milik penerbit masing-masing.</p>
                <a class="footer-link inline-flex min-h-11 items-center gap-1 pb-0.5 font-semibold text-slate-950 transition-colors hover:text-blue-700 <?= $footerRing ?>" href="#konten-utama">
                    Kembali ke atas <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_upward</span>
                </a>
            </div>
        </div>
    </footer>

    <!-- Mobile bottom nav: same three destinations as the header. Height stays 58px because overlays.php offsets its sticky bar by that value. -->
    <nav class="fixed inset-x-0 bottom-0 z-[999] h-[58px] border-t border-slate-200 bg-white md:hidden" aria-label="Menu utama">
        <ul class="flex h-full">
            <?php foreach ($navItems as $item): ?>
                <li class="flex-1">
                    <a class="relative flex h-full flex-col items-center justify-center gap-0.5 text-xs font-semibold text-slate-600 focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-blue-600 aria-[current=page]:text-blue-700 before:absolute before:inset-x-5 before:-top-px before:h-0.5 before:bg-blue-700 before:opacity-0 aria-[current=page]:before:opacity-100"
                       href="<?= esc($item['href']) ?>"
                       <?= $item['mode'] ? 'data-nav-mode="' . $item['mode'] . '"' : '' ?>
                       <?= $item['current'] ? 'aria-current="page"' : '' ?>>
                        <span class="material-symbols-outlined text-[22px]" aria-hidden="true"><?= $item['icon'] ?></span>
                        <?= esc($item['label']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</body>
</html>
