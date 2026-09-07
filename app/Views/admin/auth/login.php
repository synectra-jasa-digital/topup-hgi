<?php
/**
 * Admin Login View - Ayong Store
 * Minimalist & Simple Design
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Admin — Ayong Store</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex items-center justify-center p-4">

  <div class="w-full max-w-sm">

    <!-- Brand Header -->
    <div class="text-center mb-6">
      <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-sm mb-3">
        <span class="material-symbols-outlined text-[24px]">sports_esports</span>
      </div>
      <h1 class="text-xl font-bold text-slate-900">Ayong Store</h1>
      <p class="text-xs text-slate-500 mt-1">Masuk ke Panel Admin</p>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('error')): ?>
      <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center gap-2">
        <span class="material-symbols-outlined text-[18px]">error</span>
        <span><?= esc(session()->getFlashdata('error')) ?></span>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs flex items-center gap-2">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        <span><?= esc(session()->getFlashdata('success')) ?></span>
      </div>
    <?php endif; ?>

    <!-- Login Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
      <form method="POST" action="<?= base_url('admin/login') ?>" class="space-y-4">
        <?= csrf_field() ?>

        <div>
          <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
          <input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" required autofocus
                 class="w-full px-3.5 py-2.5 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all placeholder-slate-400"
                 placeholder="admin@ayongstore.test">
        </div>

        <div>
          <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
          <div class="relative">
            <input type="password" id="password" name="password" required
                   class="w-full pl-3.5 pr-10 py-2.5 text-sm bg-slate-50 rounded-xl border border-slate-300 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition-all placeholder-slate-400"
                   placeholder="••••••••">
            <button type="button" id="togglePass" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 focus:outline-none">
              <span class="material-symbols-outlined text-[18px]" id="eyeIcon">visibility</span>
            </button>
          </div>
        </div>

        <button type="submit"
                class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition-colors shadow-sm">
          Masuk
        </button>
      </form>
    </div>

    <!-- Back Link & Footer -->
    <div class="text-center mt-6 space-y-2">
      <a href="<?= base_url('/') ?>" class="text-xs text-slate-500 hover:text-blue-600 transition-colors inline-flex items-center gap-1">
        <span class="material-symbols-outlined text-[14px]">arrow_back</span>
        Kembali ke Halaman Utama
      </a>
      <p class="text-[11px] text-slate-400 block">&copy; <?= date('Y') ?> Ayong Store</p>
    </div>

  </div>

  <script>
    const toggleBtn = document.getElementById('togglePass');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    if (toggleBtn && passwordInput && eyeIcon) {
      toggleBtn.addEventListener('click', () => {
        const isPass = passwordInput.type === 'password';
        passwordInput.type = isPass ? 'text' : 'password';
        eyeIcon.textContent = isPass ? 'visibility_off' : 'visibility';
      });
    }
  </script>
</body>
</html>


