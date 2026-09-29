<!doctype html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($storeName) ?> - Toko Sedang Tutup</title>
    
    <!-- Open Graph & Social Sharing -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="<?= esc($storeName) ?> - Toko Sedang Tutup Sementara">
    <meta property="og:description" content="Toko sedang tutup sementara. Pesanan yang sudah masuk tetap diproses. Cek status invoice Anda atau hubungi Customer Service WhatsApp.">
    <meta property="og:site_name" content="<?= esc($storeName) ?>">
    <meta property="og:image" content="<?= base_url('assets/img/og-image.png') ?>">
    <meta property="og:image:secure_url" content="<?= base_url('assets/img/og-image.png') ?>">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($storeName) ?> - Toko Sedang Tutup Sementara">
    <meta name="twitter:description" content="Toko sedang tutup sementara. Pesanan yang sudah masuk tetap diproses. Cek status invoice Anda atau hubungi Customer Service WhatsApp.">
    <meta name="twitter:image" content="<?= base_url('assets/img/og-image.png') ?>">
    
    <!-- Direct Gaming Trust Design System Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=Material+Symbols+Outlined:wght,FILL@400,0&display=swap" rel="stylesheet">
    
    <!-- App CSS & Tailwind -->
    <?php $cssVersion = is_file(FCPATH . 'assets/css/public.css') ? filemtime(FCPATH . 'assets/css/public.css') : time(); ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/public.css') ?>?v=<?= $cssVersion ?>">

    <style>
        /* Tactile & Motion micro-interactions per DESIGN.md */
        @keyframes swing-gentle {
            0%, 100% { transform: rotate(-3deg); }
            50% { transform: rotate(3deg); }
        }

        .swing-sign {
            transform-origin: top center;
            animation: swing-gentle 4s ease-in-out infinite;
        }

        /* Direct Gaming Trust Color Variables */
        :root {
            --bg-page: #f8f9ff;
            --surface-card: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-default: #e5e7eb;
            --primary-blue: #2563eb;
            --primary-hover: #1e3a8a;
            --tertiary-tint: #dbeafe;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
        }

        .font-display {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .trust-card {
            background: var(--surface-card);
            border: 1px solid var(--border-default);
            box-shadow: 0 4px 20px -2px rgba(31, 41, 55, 0.05);
        }

        kbd {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            box-shadow: inset 0 -1px 0 rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between items-center relative overflow-x-hidden select-none px-4">

    <!-- Top Loading Progress Bar -->
    <div id="reload-progress-container" class="fixed top-0 inset-x-0 h-1 bg-transparent z-50 overflow-hidden pointer-events-none">
        <div id="reload-progress-bar" class="h-full bg-gradient-to-r from-blue-500 via-indigo-600 to-blue-500 w-0 transition-all duration-300"></div>
    </div>

    <!-- Header / Navbar per DESIGN.md (Clean Canvas) -->
    <header class="w-full max-w-5xl mx-auto py-5 flex items-center justify-between border-b border-slate-200/80">
        <div class="flex items-center gap-3">
            <?php if (! empty($storeLogo)): ?>
                <img src="<?= base_url($storeLogo) ?>" alt="<?= esc($storeName) ?>" class="h-9 w-auto object-contain rounded-xl border border-slate-200">
            <?php else: ?>
                <div class="h-10 w-10 rounded-xl bg-blue-600 flex items-center justify-center font-display font-bold text-white text-lg shadow-xs">
                    <?= esc(substr($storeName, 0, 1)) ?>
                </div>
            <?php endif; ?>
            <span class="font-display font-bold text-lg sm:text-xl tracking-tight text-slate-900"><?= esc($storeName) ?></span>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-rose-50 border border-rose-200 text-rose-700">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-600"></span>
                </span>
                Toko Tutup Sementara
            </span>
        </div>
    </header>

    <!-- Main Content Box -->
    <main class="w-full max-w-lg mx-auto py-8 my-auto">
        <div class="trust-card rounded-2xl p-6 sm:p-8 text-center relative overflow-hidden transition-all">
            
            <!-- Store Closed Visual Signboard Badge -->
            <div class="inline-flex items-center justify-center p-3.5 rounded-2xl bg-blue-50 border border-blue-100 mb-5 swing-sign">
                <div class="flex items-center justify-center w-14 h-14 rounded-xl bg-white border border-blue-200 text-blue-600 shadow-xs">
                    <span class="material-symbols-outlined text-3xl">storefront</span>
                </div>
            </div>

            <!-- Title & Subtitle (Plus Jakarta Sans & Inter per DESIGN.md) -->
            <h1 class="font-display font-bold text-2xl sm:text-3xl text-slate-900 tracking-tight leading-tight mb-2">
                Toko Sedang Tutup
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-sm mx-auto mb-6">
                Layanan top up koin dan pengajuan bongkar di <span class="text-slate-900 font-semibold"><?= esc($storeName) ?></span> sedang diistirahatkan sementara.
            </p>

            <!-- Store Note (Pesan Pengelola dari Admin) -->
            <?php if (! empty($reason)): ?>
                <div class="bg-amber-50 border border-amber-200/80 rounded-xl p-4 text-left mb-6 shadow-xs">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-600 text-xl shrink-0 mt-0.5">campaign</span>
                        <div class="space-y-0.5 min-w-0">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-amber-800">Pesan Pengelola</h2>
                            <p class="text-xs sm:text-sm text-amber-950 font-medium leading-relaxed break-words">
                                <?= esc($reason) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Live Status & WIB Digital Clock Card -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 mb-6 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 text-slate-600">
                    <span class="material-symbols-outlined text-base text-slate-500">schedule</span>
                    <span class="font-medium">Jam Server:</span>
                </div>
                <div class="flex items-center gap-2">
                    <span id="live-clock" class="font-mono font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-200">--:--:-- WIB</span>
                </div>
            </div>

            <!-- Interactive Invoice Lookup Form -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6 text-left space-y-2">
                <label for="invoice-lookup" class="block text-xs font-bold text-slate-900">
                    Cek Status Pesanan Anda
                </label>
                <p class="text-[11px] text-slate-600">Sudah memesan sebelum toko tutup? Lacak status invoice Anda di sini:</p>
                <form onsubmit="return handleInvoiceSearch(event)" class="flex gap-2 pt-1">
                    <div class="relative flex-1">
                        <input type="text" id="invoice-lookup" placeholder="Contoh: INV-123456" 
                            class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 placeholder-slate-400 font-mono focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:outline-none transition-all">
                    </div>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-display font-semibold text-xs transition-all flex items-center gap-1.5 shrink-0 cursor-pointer shadow-xs active:scale-95">
                        <span class="material-symbols-outlined text-base">search</span>
                        <span>Cek</span>
                    </button>
                </form>
            </div>

            <!-- Action Buttons (DESIGN.md Heights & Colors) -->
            <div class="space-y-3">
                <?php if (! empty($waUrl)): ?>
                    <a href="<?= esc($waUrl) ?>" target="_blank" rel="noopener" class="w-full min-h-[48px] px-4 py-3 rounded-xl font-display font-semibold text-xs sm:text-sm text-white bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                        <span class="material-symbols-outlined text-xl">chat</span>
                        <span>Hubungi CS WhatsApp</span>
                    </a>
                <?php endif; ?>

                <button type="button" id="btn-reload" onclick="handleReload()" class="w-full min-h-[46px] px-4 py-2.5 rounded-xl font-display font-semibold text-xs sm:text-sm text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                    <span id="btn-reload-icon" class="material-symbols-outlined text-lg transition-transform duration-300">refresh</span>
                    <span id="btn-reload-text">Cek Apakah Toko Sudah Buka</span>
                </button>
            </div>

            <!-- Keyboard Hints -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-center gap-3 text-[11px] text-slate-500">
                <span class="inline-flex items-center gap-1">
                    <kbd class="px-1.5 py-0.5 rounded text-[10px] font-mono text-slate-600">R</kbd> Cek Status
                </span>
                <?php if (! empty($waUrl)): ?>
                    <span class="inline-flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 rounded text-[10px] font-mono text-slate-600">W</kbd> WhatsApp CS
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer per DESIGN.md -->
    <footer class="w-full max-w-5xl mx-auto py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500 border-t border-slate-200/80">
        <div class="flex items-center gap-1.5">
            <span class="material-symbols-outlined text-slate-400 text-sm">shield</span>
            <span>Pesanan yang dibuat sebelum toko tutup tetap diproses admin.</span>
        </div>
        <span class="font-mono text-slate-500">&copy; <?= date('Y') ?> <?= esc($storeName) ?></span>
    </footer>

    <!-- Interactive Scripts -->
    <script>
        // 1. Live Digital WIB Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false, timeZone: 'Asia/Jakarta' }) + ' WIB';
            const clockEl = document.getElementById('live-clock');
            if (clockEl) clockEl.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 2. Invoice Lookup Handler
        function handleInvoiceSearch(e) {
            e.preventDefault();
            const input = document.getElementById('invoice-lookup');
            if (!input || !input.value.trim()) return false;
            
            const inv = input.value.trim().toUpperCase().replace(/\s+/g, '');
            window.location.href = '<?= base_url("cek-pesanan") ?>/' + encodeURIComponent(inv);
            return false;
        }

        // 3. Interactive Reload Button
        let isReloading = false;
        function handleReload() {
            if (isReloading) return;
            isReloading = true;

            const btnText = document.getElementById('btn-reload-text');
            const btnIcon = document.getElementById('btn-reload-icon');
            const progressBar = document.getElementById('reload-progress-bar');

            if (btnText) btnText.textContent = 'Memeriksa Status Toko...';
            if (btnIcon) btnIcon.classList.add('animate-spin');
            if (progressBar) {
                progressBar.style.width = '0%';
                setTimeout(() => { progressBar.style.width = '100%'; }, 50);
            }

            setTimeout(() => {
                window.location.reload();
            }, 700);
        }

        // 4. Keyboard Shortcuts
        document.addEventListener('keydown', function(e) {
            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return;

            const key = e.key.toLowerCase();
            if (key === 'r') {
                e.preventDefault();
                handleReload();
            } else if (key === 'w') {
                <?php if (! empty($waUrl)): ?>
                    e.preventDefault();
                    window.open('<?= esc($waUrl) ?>', '_blank');
                <?php endif; ?>
            }
        });
    </script>
</body>
</html>
