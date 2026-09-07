<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Ayong Store - Top Up Higgs Games Island Express') ?></title>
    
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
    <!-- Top Navigation Bar (Clean Light Theme) -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm">
        <div class="max-w-[1360px] mx-auto px-4 sm:px-6 h-18 py-2.5 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a class="flex items-center gap-2.5 group transition-transform hover:scale-[1.01]" href="<?= base_url('/') ?>">
                <div class="h-10 sm:h-11 px-2.5 py-1 bg-slate-50 rounded-xl shadow-xs border border-slate-200 flex items-center justify-center">
                    <img alt="Ayong Store Logo" class="h-7 sm:h-8 w-auto object-contain" src="https://lh3.googleusercontent.com/aida/AEtjO1UHDQp92Ue8fpcyg6Zi1x-4rli35Nx_qMLNjs4PD3xlchTo0ZkpU60pMkM_sa_kPn7V3KoSzdv0072y9UIj40Gzop8Lp-auwR9fPBICUpV6RID73WKKivk8bUDCQV_YvsavRb0vvNx8RJA-NvnNCQSOU8KHK0n7RUPx2z3-XovssOIjHSgoNg8SO3hUYRromj_1f2Xs4hyPFJg7tdz2a86bUQscanoY0AtA_5E_RZVa6dxK9j-TUOORGw">
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-1.5">
                        <span class="font-display font-black text-base sm:text-lg text-neutral-900 tracking-tight leading-none">Ayong Store</span>
                        <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-300 text-[9px] font-black uppercase tracking-wider">HGD Resmi</span>
                    </div>
                    <span class="text-[10px] text-slate-500 font-medium leading-tight">Official Higgs Top Up 24 Jam</span>
                </div>
            </a>
            <!-- Nav Links Cockpit Pill -->
            <nav class="hidden md:flex items-center gap-1 bg-slate-100/90 p-1.5 rounded-xl border border-slate-200 text-xs font-semibold shadow-inner">
                <a class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-neutral-900 hover:bg-white transition-all flex items-center gap-1" href="<?= base_url('/') ?>"><span class="material-symbols-outlined text-[16px]">home</span> Beranda</a>
                <a class="px-3 py-1.5 rounded-lg bg-blue-600 text-white font-bold shadow-sm flex items-center gap-1.5" href="<?= base_url('/#katalog-section') ?>"><span class="material-symbols-outlined text-[16px]">inventory_2</span> Katalog Produk</a>
                <a class="px-3 py-1.5 rounded-lg text-slate-600 hover:text-neutral-900 hover:bg-white transition-all flex items-center gap-1" href="<?= base_url('cek-pesanan') ?>"><span class="material-symbols-outlined text-[16px]">receipt_long</span> Cek Status Pesanan</a>
            </nav>
            <!-- Header Action Support -->
            <div class="flex items-center gap-2.5">
                <div class="hidden xl:flex flex-col text-right">
                    <span class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider flex items-center justify-end gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Layanan Aktif 24 Jam</span>
                    <span class="text-xs font-mono text-slate-600">Higgs Games Island Resmi</span>
                </div>
                <a class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold transition-all shadow-sm border border-emerald-500/30" href="https://wa.me/" rel="noopener noreferrer" target="_blank">
                    <span class="material-symbols-outlined text-[18px]">chat</span>
                    <span class="hidden sm:inline">CS WhatsApp 24 Jam</span>
                    <span class="sm:hidden">CS WA</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Live Gaming Ticker -->
    <div class="bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-500 text-neutral-900 text-xs py-1.5 px-4 font-bold shadow-xs relative overflow-hidden border-b border-amber-300">
        <div class="max-w-[1360px] mx-auto flex items-center gap-3 overflow-hidden text-xs">
            <div class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-slate-900 text-amber-300 font-extrabold text-[10px] uppercase tracking-wider shrink-0 shadow-xs">
                <span class="material-symbols-outlined text-[13px] text-amber-400">campaign</span>
                <span class="">INFO RESMI</span>
            </div>
            <div class="flex-1 overflow-hidden relative">
                <div class="animate-marquee whitespace-nowrap flex items-center gap-8 text-neutral-900 font-semibold text-xs">
                    <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>🔥 Kode Promo Hemat: Gunakan voucher <strong class="text-blue-900 font-black underline decoration-blue-700">AYONGHEMAT</strong> untuk potongan langsung Rp5.000!</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-700"></span>⚡ Kirim Kilat 1-3 Detik: Sistem integrasi server resmi otomatis tanpa login &amp; tanpa sandi akun.</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>🛡️ Legal &amp; Anti Banned: Semua transaksi menggunakan jalur distribusi ID resmi terlisensi 100%.</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-700"></span>💬 Layanan Bantuan CS: Customer Service WhatsApp siap melayani 24 Jam Nonstop setiap hari.</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>🔥 Kode Promo Hemat: Gunakan voucher <strong class="text-blue-900 font-black underline decoration-blue-700">AYONGHEMAT</strong> untuk potongan langsung Rp5.000!</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-700"></span>⚡ Kirim Kilat 1-3 Detik: Sistem integrasi server resmi otomatis tanpa login &amp; tanpa sandi akun.</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-blue-700"></span>🛡️ Legal &amp; Anti Banned: Semua transaksi menggunakan jalur distribusi ID resmi terlisensi 100%.</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-700"></span>💬 Layanan Bantuan CS: Customer Service WhatsApp siap melayani 24 Jam Nonstop setiap hari.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT SECTION -->
    <?= $this->renderSection('content') ?>

    <!-- Footer (Clean Light) -->
    <footer class="mt-16 bg-white border-t border-slate-200 text-xs text-slate-500 pb-20 lg:pb-8">
        <div class="max-w-[1360px] mx-auto px-4 sm:px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 pb-10 border-b border-slate-200">
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="h-10 px-2.5 py-1 bg-slate-50 rounded-xl shadow-xs border border-slate-200 flex items-center justify-center">
                            <img alt="Ayong Store Logo" class="h-7 w-auto object-contain" src="https://lh3.googleusercontent.com/aida/AEtjO1UHDQp92Ue8fpcyg6Zi1x-4rli35Nx_qMLNjs4PD3xlchTo0ZkpU60pMkM_sa_kPn7V3KoSzdv0072y9UIj40Gzop8Lp-auwR9fPBICUpV6RID73WKKivk8bUDCQV_YvsavRb0vvNx8RJA-NvnNCQSOU8KHK0n7RUPx2z3-XovssOIjHSgoNg8SO3hUYRromj_1f2Xs4hyPFJg7tdz2a86bUQscanoY0AtA_5E_RZVa6dxK9j-TUOORGw">
                        </div>
                        <div>
                            <span class="font-display font-black text-base text-neutral-900 tracking-tight block">Ayong Store</span>
                            <span class="text-[10px] text-amber-700 font-bold uppercase tracking-wider">Official Higgs Partner</span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">Portal top up Higgs Games Island express resmi dan terpercaya di Indonesia. Transaksi otomatis masuk dalam 1-3 detik cukup dengan ID pemain tanpa memerlukan kata sandi.</p>
                    <div class="flex items-center gap-2 text-xs text-emerald-700 bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-200 w-fit">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="font-bold">Operasional 24 Jam Nonstop</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <h4 class="font-display font-bold text-sm text-neutral-900 tracking-wide uppercase">Navigasi Cepat</h4>
                    <ul class="space-y-2 text-xs text-slate-600 font-medium">
                        <li class=""><a class="hover:text-blue-600 transition-colors flex items-center gap-1.5" href="<?= base_url('/') ?>"><span class="material-symbols-outlined text-[15px] text-blue-600">chevron_right</span> Katalog Koin Emas Resmi</a></li>
                        <li class=""><a class="hover:text-blue-600 transition-colors flex items-center gap-1.5" href="<?= base_url('/') ?>"><span class="material-symbols-outlined text-[15px] text-blue-600">chevron_right</span> Kartu Member VIP Domino</a></li>
                        <li class=""><a class="hover:text-blue-600 transition-colors flex items-center gap-1.5" href="<?= base_url('cek-pesanan') ?>"><span class="material-symbols-outlined text-[15px] text-blue-600">chevron_right</span> Cek Status Pemesanan Real-Time</a></li>
                        <li class=""><a class="hover:text-blue-600 transition-colors flex items-center gap-1.5" href="#"><span class="material-symbols-outlined text-[15px] text-blue-600">chevron_right</span> Syarat &amp; Ketentuan Layanan</a></li>
                        <li class=""><a class="hover:text-blue-600 transition-colors flex items-center gap-1.5" href="#"><span class="material-symbols-outlined text-[15px] text-blue-600">chevron_right</span> Kebijakan Privasi Pengguna</a></li>
                        <li class=""><a class="hover:text-blue-600 transition-colors flex items-center gap-1.5" href="#"><span class="material-symbols-outlined text-[15px] text-blue-600">chevron_right</span> Pertanyaan Umum (FAQ)</a></li>
                    </ul>
                </div>
                <div class="space-y-3">
                    <h4 class="font-display font-bold text-sm text-neutral-900 tracking-wide uppercase">Metode Pembayaran</h4>
                    <p class="text-xs text-slate-500">Didukung oleh payment gateway berlisensi Bank Indonesia:</p>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-center text-[10px] font-extrabold text-neutral-900">QRIS</div>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-center text-[10px] font-extrabold text-sky-700">GOPAY</div>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-center text-[10px] font-extrabold text-blue-700">DANA</div>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-center text-[10px] font-extrabold text-purple-700">OVO</div>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-center text-[10px] font-extrabold text-orange-600">SHOPEE</div>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-center text-[10px] font-extrabold text-red-600">LINKAJA</div>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-center text-[10px] font-bold text-blue-800">BCA VA</div>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-center text-[10px] font-bold text-amber-700">MANDIRI</div>
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-2 text-center text-[10px] font-bold text-sky-800">BRI VA</div>
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500 pt-1">
                        <span class="material-symbols-outlined text-[15px] text-emerald-600">verified</span>
                        <span class="">Verifikasi Pembayaran Otomatis</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <h4 class="font-display font-bold text-sm text-neutral-900 tracking-wide uppercase">Bantuan &amp; Kontak Resmi</h4>
                    <p class="text-xs text-slate-600">Tim layanan pelanggan siap mendampingi transaksi Anda 24 jam nonstop.</p>
                    <a class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-sm" href="https://wa.me/" rel="noopener noreferrer" target="_blank">
                        <span class="material-symbols-outlined text-[18px]">chat</span>
                        <span class="">WhatsApp CS VIP 24 Jam</span>
                    </a>
                    <div class="space-y-1.5 pt-2 text-[11px] text-slate-500">
                        <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px] text-blue-600">lock</span><span class="">Enkripsi Transaksi SSL 256-Bit</span></div>
                        <div class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px] text-emerald-600">security</span><span class="">Jaminan 100% Anti Banned &amp; Aman</span></div>
                    </div>
                </div>
            </div>
            <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p class="text-center md:text-left">© <?= date('Y') ?> <strong>Ayong Store</strong>. Seluruh hak cipta dilindungi undang-undang. Transaksi instan &amp; terlisensi.</p>
                <p class="text-center md:text-right text-[11px] text-slate-400">Disclaimer: Seluruh merek dagang dan aset game adalah hak cipta dari penerbit masing-masing.</p>
            </div>
        </div>
    </footer>
</body>
</html>



