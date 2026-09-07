<?php
/**
 * Admin Login View - Ayong Store
 * Clean White Background with Official Logo
 */
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Panel Admin — Ayong Store</title>

    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "primary": "#004ac6",
              "primary-dark": "#003698",
              "primary-light": "#eef2ff",
              "gold-accent": "#F59E0B",
              "neutral-900": "#0F172A",
              "neutral-800": "#1E293B",
              "neutral-700": "#334155",
              "neutral-600": "#475569",
              "neutral-500": "#64748B"
            },
            fontFamily: {
              sans: ["Inter", "sans-serif"],
              display: ["Plus Jakarta Sans", "sans-serif"]
            }
          }
        }
      }
    </script>
</head>

<body class="bg-white font-sans text-neutral-800 antialiased min-h-screen flex items-center justify-center p-4">

  <!-- Main Login Card Container -->
  <div class="w-full max-w-md">

    <!-- Card Wrapper -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-7 sm:p-9 space-y-6">

      <!-- Header & Brand Logo -->
      <div class="text-center space-y-3">
        <!-- Logo Image -->
        <div class="inline-flex items-center justify-center p-3 bg-slate-50 rounded-2xl border border-slate-200 shadow-xs mb-1">
          <img alt="Ayong Store Logo" class="h-10 sm:h-12 w-auto object-contain" src="https://lh3.googleusercontent.com/aida/AEtjO1UHDQp92Ue8fpcyg6Zi1x-4rli35Nx_qMLNjs4PD3xlchTo0ZkpU60pMkM_sa_kPn7V3KoSzdv0072y9UIj40Gzop8Lp-auwR9fPBICUpV6RID73WKKivk8bUDCQV_YvsavRb0vvNx8RJA-NvnNCQSOU8KHK0n7RUPx2z3-XovssOIjHSgoNg8SO3hUYRromj_1f2Xs4hyPFJg7tdz2a86bUQscanoY0AtA_5E_RZVa6dxK9j-TUOORGw">
        </div>
      </div>

      <!-- Flash Alert Messages -->
      <?php if (session()->getFlashdata('error')): ?>
        <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2.5">
          <span class="material-symbols-outlined text-[18px] text-rose-600 shrink-0">error</span>
          <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold flex items-center gap-2.5">
          <span class="material-symbols-outlined text-[18px] text-emerald-600 shrink-0">check_circle</span>
          <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form method="POST" action="<?= base_url('admin/login') ?>" autocomplete="on" class="space-y-4">
        <?= csrf_field() ?>

        <!-- Email Field -->
        <div class="space-y-1.5">
          <label for="email" class="block text-xs font-bold text-neutral-700">Email Admin <span class="text-rose-500">*</span></label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3.5 top-3 text-[18px] text-slate-400 pointer-events-none">mail</span>
            <input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" required autofocus autocomplete="email"
                   class="w-full pl-10 pr-4 py-3 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all font-medium text-neutral-900 placeholder-slate-400"
                   placeholder="admin@ayongstore.test">
          </div>
        </div>

        <!-- Password Field -->
        <div class="space-y-1.5">
          <label for="password" class="block text-xs font-bold text-neutral-700">Kata Sandi <span class="text-rose-500">*</span></label>
          <div class="relative">
            <span class="material-symbols-outlined absolute left-3.5 top-3 text-[18px] text-slate-400 pointer-events-none">lock</span>
            <input type="password" id="password" name="password" required autocomplete="current-password"
                   class="w-full pl-10 pr-12 py-3 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all font-medium text-neutral-900 placeholder-slate-400"
                   placeholder="••••••••">
            <button type="button" id="togglePass" class="absolute right-3.5 top-3 text-slate-400 hover:text-blue-600 transition-colors focus:outline-none" title="Tampilkan Kata Sandi">
              <span class="material-symbols-outlined text-[18px]" id="eyeIcon">visibility</span>
            </button>
          </div>
        </div>

        <!-- Badge Info -->
        <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
          
        </div>

        <!-- Submit Button -->
        <button type="submit" id="btnSubmitLogin"
                class="w-full py-3.5 px-5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-display font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-[0.99] border border-blue-700">
          <span class="material-symbols-outlined text-[20px] text-amber-300">login</span>
          <span>Masuk ke Panel Admin</span>
        </button>
      </form>

      <!-- Back Link Footer -->
      <div class="pt-4 border-t border-slate-100 text-center">
        <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-blue-600 transition-colors group">
          <span class="material-symbols-outlined text-[16px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
          <span>Kembali ke Halaman Utama Toko</span>
        </a>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const toggleBtn = document.getElementById('togglePass');
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      const loginForm = document.querySelector('form');
      const btnSubmit = document.getElementById('btnSubmitLogin');

      if (toggleBtn && passwordInput && eyeIcon) {
        toggleBtn.addEventListener('click', () => {
          const isPass = passwordInput.type === 'password';
          passwordInput.type = isPass ? 'text' : 'password';
          eyeIcon.textContent = isPass ? 'visibility_off' : 'visibility';
        });
      }

      if (loginForm && btnSubmit) {
        loginForm.addEventListener('submit', () => {
          btnSubmit.disabled = true;
          btnSubmit.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">refresh</span> <span>Memproses...</span>';
        });
      }
    });
  </script>
</body>
</html>




