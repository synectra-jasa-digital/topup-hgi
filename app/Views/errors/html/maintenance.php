<!doctype html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($storeName) ?> - Toko Sedang Tutup</title>
    
    <!-- Preconnect & Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800;900&family=Material+Symbols+Outlined:wght,FILL@400,0&display=swap" rel="stylesheet">
    
    <!-- App Public CSS & Tailwind -->
    <?php $cssVersion = is_file(FCPATH . 'assets/css/public.css') ? filemtime(FCPATH . 'assets/css/public.css') : time(); ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/public.css') ?>?v=<?= $cssVersion ?>">

    <style>
        /* Smooth micro-animations */
        @keyframes swing-gentle {
            0%, 100% { transform: rotate(-3deg); }
            50% { transform: rotate(3deg); }
        }
        @keyframes float-subtle {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 0.3; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        .swing-sign {
            transform-origin: top center;
            animation: swing-gentle 4s ease-in-out infinite;
        }

        .float-badge {
            animation: float-subtle 3.5s ease-in-out infinite;
        }

        .bg-mesh-dark {
            background-color: #0b0f17;
            background-image: 
                radial-gradient(at 15% 15%, rgba(37, 99, 235, 0.12) 0px, transparent 50%),
                radial-gradient(at 85% 85%, rgba(245, 158, 11, 0.08) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(15, 23, 42, 0.9) 0px, transparent 100%);
        }

        .store-card {
            background: rgba(19, 27, 46, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }

        kbd {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: inset 0 -1px 0 rgba(0,0,0,0.4);
        }
    </style>
</head>
<body class="bg-mesh-dark text-slate-100 font-sans min-h-screen flex flex-col justify-between items-center relative overflow-x-hidden select-none">

    <!-- Top Loading Progress Bar -->
    <div id="reload-progress-container" class="fixed top-0 inset-x-0 h-1 bg-transparent z-50 overflow-hidden pointer-events-none">
        <div id="reload-progress-bar" class="h-full bg-gradient-to-r from-amber-500 via-blue-500 to-amber-500 w-0 transition-all duration-300"></div>
    </div>

    <!-- Header / Navbar -->
    <header class="w-full max-w-6xl mx-auto px-4 py-5 flex items-center justify-between relative z-10">
        <div class="flex items-center gap-3">
            <?php if (! empty($storeLogo)): ?>
                <img src="<?= base_url($storeLogo) ?>" alt="<?= esc($storeName) ?>" class="h-9 w-auto object-contain rounded-xl border border-slate-800 shadow-md">
            <?php else: ?>
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center font-display font-black text-white text-lg shadow-md border border-blue-400/30">
                    <?= esc(substr($storeName, 0, 1)) ?>
                </div>
            <?php endif; ?>
            <span class="font-display font-black text-lg sm:text-xl tracking-tight text-white"><?= esc($storeName) ?></span>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-rose-500/10 border border-rose-500/25 text-rose-400">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                </span>
                Toko Tutup Sementara
            </span>
        </div>
    </header>

    <!-- Main Content -->
    <main class="w-full max-w-lg px-4 py-6 mx-auto relative z-10 my-auto">
        <div class="store-card rounded-3xl p-6 sm:p-8 text-center relative overflow-hidden transition-all duration-300">
            
            <!-- Ambient Glow -->
            <div class="absolute -top-20 left-1/2 -translate-x-1/2 w-56 h-56 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Store Closed Visual Signboard Badge -->
            <div class="inline-flex items-center justify-center p-4 rounded-2xl bg-slate-900/90 border border-slate-800 mb-5 shadow-xl swing-sign">
                <div class="flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500/20 to-rose-500/20 border border-amber-500/30 text-amber-400">
                    <span class="material-symbols-outlined text-3xl">storefront</span>
                </div>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="font-display font-black text-2xl sm:text-3xl text-white tracking-tight leading-tight mb-2">
                Toko Sedang Tutup
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 leading-relaxed max-w-sm mx-auto mb-5">
                Layanan top up koin dan pengajuan bongkar di <span class="text-slate-200 font-semibold"><?= esc($storeName) ?></span> sedang diistirahatkan sementara.
            </p>

            <!-- Store Note (Catatan Pengelola dari Admin) -->
            <?php if (! empty($reason)): ?>
                <div class="bg-amber-500/10 border border-amber-500/25 rounded-2xl p-4 text-left mb-5 shadow-inner">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-400 text-lg shrink-0 mt-0.5">campaign</span>
                        <div class="space-y-0.5 min-w-0">
                            <h2 class="text-[11px] font-bold uppercase tracking-wider text-amber-400">Pesan Pengelola</h2>
                            <p class="text-xs sm:text-sm text-amber-100 font-medium leading-relaxed break-words">
                                <?= esc($reason) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Live Status & WIB Digital Clock Card -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-3.5 mb-5 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 text-slate-400">
                    <span class="material-symbols-outlined text-base text-slate-500">schedule</span>
                    <span>Jam Server:</span>
                </div>
                <div class="flex items-center gap-2">
                    <span id="live-clock" class="font-mono font-bold text-amber-400 bg-amber-400/10 px-2.5 py-1 rounded-lg border border-amber-400/20">--:--:-- WIB</span>
                </div>
            </div>

            <!-- Interactive Invoice Lookup Form -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 mb-5 text-left space-y-2">
                <label for="invoice-lookup" class="block text-xs font-bold text-slate-300">
                    Cek Status Pesanan Anda
                </label>
                <p class="text-[11px] text-slate-400">Sudah memesan sebelum toko tutup? Lacak status invoice Anda di sini:</p>
                <form onsubmit="return handleInvoiceSearch(event)" class="flex gap-2 pt-1">
                    <div class="relative flex-1">
                        <input type="text" id="invoice-lookup" placeholder="Contoh: INV-123456" 
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 font-mono focus:border-blue-500 focus:outline-none transition-all">
                    </div>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-display font-semibold text-xs transition-all flex items-center gap-1.5 shrink-0 cursor-pointer">
                        <span class="material-symbols-outlined text-base">search</span>
                        <span>Cek</span>
                    </button>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-2.5">
                <?php if (! empty($waUrl)): ?>
                    <a href="<?= esc($waUrl) ?>" target="_blank" rel="noopener" class="w-full min-h-[48px] px-4 py-3 rounded-xl font-display font-semibold text-xs sm:text-sm text-emerald-400 bg-emerald-950/40 hover:bg-emerald-950/70 border border-emerald-500/30 hover:border-emerald-500/50 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined text-lg">chat</span>
                        <span>Hubungi CS WhatsApp</span>
                    </a>
                <?php endif; ?>

                <button type="button" id="btn-reload" onclick="handleReload()" class="w-full min-h-[46px] px-4 py-2.5 rounded-xl font-display font-semibold text-xs sm:text-sm text-slate-300 bg-slate-900 hover:bg-slate-800 border border-slate-800 active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span id="btn-reload-icon" class="material-symbols-outlined text-lg transition-transform duration-300">refresh</span>
                    <span id="btn-reload-text">Cek Apakah Toko Sudah Buka</span>
                </button>
            </div>

            <!-- Keyboard Hints -->
            <div class="mt-5 pt-4 border-t border-slate-800/80 flex flex-wrap items-center justify-center gap-3 text-[11px] text-slate-500">
                <span class="inline-flex items-center gap-1">
                    <kbd class="px-1.5 py-0.5 rounded text-[10px] font-mono text-slate-400">R</kbd> Cek Status
                </span>
                <?php if (! empty($waUrl)): ?>
                    <span class="inline-flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 rounded text-[10px] font-mono text-slate-400">W</kbd> WhatsApp CS
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-6xl mx-auto px-4 py-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500 relative z-10 border-t border-slate-900">
        <div class="flex items-center gap-1.5">
            <span class="material-symbols-outlined text-slate-600 text-sm">info</span>
            <span>Pesanan yang sudah dibuat sebelum toko tutup akan tetap diproses oleh admin.</span>
        </div>
        <span class="font-mono text-slate-600">&copy; <?= date('Y') ?> <?= esc($storeName) ?></span>
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
