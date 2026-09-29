<!doctype html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($storeName) ?> - Pemeliharaan Sistem</title>
    
    <!-- Preconnect & Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800;900&family=Material+Symbols+Outlined:wght,FILL@400,0&display=swap" rel="stylesheet">
    
    <!-- App Public CSS & Tailwind -->
    <?php $cssVersion = is_file(FCPATH . 'assets/css/public.css') ? filemtime(FCPATH . 'assets/css/public.css') : time(); ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/public.css') ?>?v=<?= $cssVersion ?>">

    <style>
        /* Custom keyframes & interactive effects */
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.08); }
        }
        @keyframes float-gentle {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        @keyframes progress-fill {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        .bg-mesh-dark {
            background-color: #030712;
            background-image: 
                radial-gradient(at 10% 10%, rgba(37, 99, 235, 0.15) 0px, transparent 50%),
                radial-gradient(at 90% 90%, rgba(245, 158, 11, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(15, 23, 42, 0.8) 0px, transparent 100%);
        }
        
        .glass-card {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.05);
        }

        .animated-badge {
            animation: float-gentle 4s ease-in-out infinite;
        }

        .progress-bar-loading {
            animation: progress-fill 1.2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        kbd {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: inset 0 -1px 0 rgba(0,0,0,0.4);
        }
    </style>
</head>
<body class="bg-mesh-dark text-slate-100 font-sans min-h-screen flex flex-col justify-between items-center relative overflow-x-hidden select-none">

    <!-- Interactive Background Canvas Particles -->
    <canvas id="bg-canvas" class="absolute inset-0 pointer-events-none z-0 opacity-40"></canvas>

    <!-- Top Loading Progress Bar (Triggered on Refresh) -->
    <div id="reload-progress-container" class="fixed top-0 inset-x-0 h-1 bg-transparent z-50 overflow-hidden pointer-events-none">
        <div id="reload-progress-bar" class="h-full bg-gradient-to-r from-blue-500 via-amber-400 to-blue-600 w-0 transition-all duration-300"></div>
    </div>

    <!-- Header / Navbar Minimal -->
    <header class="w-full max-w-7xl mx-auto px-4 py-6 flex items-center justify-between relative z-10">
        <div class="flex items-center gap-3">
            <?php if (! empty($storeLogo)): ?>
                <img src="<?= base_url($storeLogo) ?>" alt="<?= esc($storeName) ?>" class="h-10 w-auto object-contain rounded-xl border border-slate-800 shadow-md">
            <?php else: ?>
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center font-display font-black text-white text-lg shadow-lg border border-blue-400/30">
                    <?= esc(substr($storeName, 0, 1)) ?>
                </div>
            <?php endif; ?>
            <span class="font-display font-extrabold text-lg sm:text-xl tracking-tight text-white"><?= esc($storeName) ?></span>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-500/10 border border-amber-500/20 text-amber-400">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
                Maintenance Mode
            </span>
        </div>
    </header>

    <!-- Main Content Center -->
    <main class="w-full max-w-xl px-4 py-8 mx-auto relative z-10 my-auto">
        <div class="glass-card rounded-3xl p-6 sm:p-8 text-center relative overflow-hidden transition-all duration-300 hover:border-slate-700/80">
            
            <!-- Ambient Glow Effect inside Card -->
            <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-64 h-64 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Animated Status Icon Badge -->
            <div class="inline-flex items-center justify-center p-4 rounded-2xl bg-slate-900/90 border border-slate-800/80 mb-6 shadow-xl animated-badge">
                <div class="relative flex items-center justify-center">
                    <span class="material-symbols-outlined text-4xl text-amber-400">construction</span>
                    <span class="absolute -bottom-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                </div>
            </div>

            <!-- Title & Subtitle -->
            <h1 class="font-display font-black text-2xl sm:text-3xl text-white tracking-tight leading-snug mb-3">
                <?= esc($storeName) ?> Dalam Pemeliharaan
            </h1>
            <p class="text-sm sm:text-base text-slate-400 leading-relaxed max-w-md mx-auto mb-6">
                Kami sedang meningkatkan performa dan keamanan sistem layanan. Mohon tunggu sejenak, kami akan segera kembali melayani transaksi Anda.
            </p>

            <!-- Admin Note Card (if set) -->
            <?php if (! empty($reason)): ?>
                <div class="bg-slate-900/90 border border-amber-500/20 rounded-2xl p-4 sm:p-5 text-left mb-6 shadow-inner relative overflow-hidden">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-400 text-xl shrink-0 mt-0.5">sticky_note_2</span>
                        <div class="space-y-1 min-w-0">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-amber-400">Catatan Pengelola</h2>
                            <p class="text-xs sm:text-sm text-slate-300 font-medium leading-relaxed break-words">
                                <?= esc($reason) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <button type="button" id="btn-reload" onclick="handleReload()" class="w-full min-h-[52px] px-5 py-3.5 rounded-xl font-display font-bold text-sm sm:text-base text-white bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 active:scale-[0.98] transition-all duration-200 shadow-lg shadow-blue-600/20 hover:shadow-blue-600/35 flex items-center justify-center gap-2.5 cursor-pointer">
                    <span id="btn-reload-icon" class="material-symbols-outlined text-xl transition-transform duration-300">sync</span>
                    <span id="btn-reload-text">Coba Muat Ulang Halaman</span>
                </button>

                <?php if (! empty($waUrl)): ?>
                    <a href="<?= esc($waUrl) ?>" target="_blank" rel="noopener" class="w-full min-h-[50px] px-5 py-3 rounded-xl font-display font-semibold text-sm text-emerald-400 bg-emerald-950/40 hover:bg-emerald-950/70 border border-emerald-500/30 hover:border-emerald-500/50 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined text-xl text-emerald-400">chat</span>
                        <span>Hubungi Layanan Pelanggan (WhatsApp)</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Keyboard Shortcuts Hint -->
            <div class="mt-6 pt-5 border-t border-slate-800/80 flex flex-wrap items-center justify-center gap-4 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1.5">
                    <kbd class="px-2 py-0.5 rounded text-[11px] font-mono text-slate-300">R</kbd> Muat Ulang
                </span>
                <?php if (! empty($waUrl)): ?>
                    <span class="inline-flex items-center gap-1.5">
                        <kbd class="px-2 py-0.5 rounded text-[11px] font-mono text-slate-300">W</kbd> WhatsApp CS
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer Bar -->
    <footer class="w-full max-w-7xl mx-auto px-4 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 relative z-10 border-t border-slate-900">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-slate-600 text-base">info</span>
            <span>Terima kasih atas kesabaran Anda. Layanan akan kembali normal sesegera mungkin.</span>
        </div>

        <div class="flex items-center gap-4 shrink-0">
            <span id="live-clock" class="font-mono text-slate-400 font-medium">00:00:00 WIB</span>
        </div>
    </footer>

    <!-- Interactive Scripts -->
    <script>
        // 1. Live Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false, timeZone: 'Asia/Jakarta' }) + ' WIB';
            const clockEl = document.getElementById('live-clock');
            if (clockEl) clockEl.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 2. Interactive Reload Button
        let isReloading = false;
        function handleReload() {
            if (isReloading) return;
            isReloading = true;

            const btnText = document.getElementById('btn-reload-text');
            const btnIcon = document.getElementById('btn-reload-icon');
            const progressBar = document.getElementById('reload-progress-bar');

            if (btnText) btnText.textContent = 'Memeriksa Status Server...';
            if (btnIcon) btnIcon.classList.add('animate-spin');
            if (progressBar) {
                progressBar.style.width = '0%';
                setTimeout(() => { progressBar.style.width = '100%'; }, 50);
            }

            setTimeout(() => {
                window.location.reload();
            }, 800);
        }

        // 3. Keyboard Shortcuts
        document.addEventListener('keydown', function(e) {
            // Ignore if user is typing in an input/textarea
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

        // 4. Ambient Background Canvas Particle Animation
        (function initCanvas() {
            const canvas = document.getElementById('bg-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');

            let width = canvas.width = window.innerWidth;
            let height = canvas.height = window.innerHeight;

            window.addEventListener('resize', () => {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            });

            const particles = [];
            const particleCount = Math.min(45, Math.floor(width / 30));

            class Particle {
                constructor() {
                    this.x = Math.random() * width;
                    this.y = Math.random() * height;
                    this.vx = (Math.random() - 0.5) * 0.4;
                    this.vy = (Math.random() - 0.5) * 0.4;
                    this.radius = Math.random() * 1.5 + 1;
                }

                update() {
                    this.x += this.vx;
                    this.y += this.vy;

                    if (this.x < 0) this.x = width;
                    if (this.x > width) this.x = 0;
                    if (this.y < 0) this.y = height;
                    if (this.y > height) this.y = 0;
                }

                draw() {
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                    ctx.fillStyle = 'rgba(59, 130, 246, 0.4)';
                    ctx.fill();
                }
            }

            for (let i = 0; i < particleCount; i++) {
                particles.push(new Particle());
            }

            function animate() {
                ctx.clearRect(0, 0, width, height);

                for (let i = 0; i < particles.length; i++) {
                    particles[i].update();
                    particles[i].draw();

                    for (let j = i + 1; j < particles.length; j++) {
                        const dx = particles[i].x - particles[j].x;
                        const dy = particles[i].y - particles[j].y;
                        const dist = Math.sqrt(dx * dx + dy * dy);

                        if (dist < 130) {
                            ctx.beginPath();
                            ctx.moveTo(particles[i].x, particles[i].y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.strokeStyle = `rgba(59, 130, 246, ${0.15 * (1 - dist / 130)})`;
                            ctx.lineWidth = 0.7;
                            ctx.stroke();
                        }
                    }
                }
                requestAnimationFrame(animate);
            }
            animate();
        })();
    </script>
</body>
</html>
