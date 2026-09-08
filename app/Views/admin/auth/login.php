<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Panel Admin — Ayong Store</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="min-h-screen bg-neutral-50 px-4 py-8 font-sans text-neutral-900 antialiased">
<?php
$error = session()->getFlashdata('error');
$success = session()->getFlashdata('success');
?>

<div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-md flex-col items-center justify-center">
    <div class="w-full text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-white">
            <span class="material-symbols-outlined text-[24px]">shield_person</span>
        </div>
        <h1 class="mt-5 text-xl font-semibold text-neutral-900">Masuk Panel Admin</h1>
        <p class="mt-1 text-sm text-neutral-500">Gunakan akun admin untuk mengelola operasional toko.</p>
    </div>

    <?php if ($error): ?>
    <div class="alert-error mt-6 w-full" id="loginAlertError" role="alert">
        <span class="material-symbols-outlined mt-0.5 text-[18px]">error</span>
        <p class="flex-1 text-sm leading-5"><?= esc($error) ?></p>
        <button type="button" data-dismiss="loginAlertError" class="rounded-md p-1 opacity-60 transition hover:opacity-100" aria-label="Tutup notifikasi">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert-success mt-6 w-full" id="loginAlertSuccess" role="alert">
        <span class="material-symbols-outlined mt-0.5 text-[18px]">check_circle</span>
        <p class="flex-1 text-sm leading-5"><?= esc($success) ?></p>
        <button type="button" data-dismiss="loginAlertSuccess" class="rounded-md p-1 opacity-60 transition hover:opacity-100" aria-label="Tutup notifikasi">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/login') ?>" autocomplete="on" class="mt-6 w-full space-y-4 rounded-xl border border-neutral-200 bg-white p-6">
        <?= csrf_field() ?>

        <div>
            <label for="email" class="form-label">Email Admin</label>
            <input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" required autofocus autocomplete="email" class="form-input" placeholder="admin@ayongstore.test">
        </div>

        <div>
            <label for="password" class="form-label">Kata Sandi</label>
            <div class="relative">
                <input type="password" id="password" name="password" required autocomplete="current-password" class="form-input pr-12" placeholder="••••••••">
                <button type="button" id="togglePass" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-1.5 text-neutral-400 transition hover:bg-neutral-50 hover:text-primary" aria-label="Tampilkan kata sandi">
                    <span class="material-symbols-outlined text-[20px]" id="eyeIcon">visibility</span>
                </button>
            </div>
        </div>

        <button type="submit" id="btnSubmitLogin" class="btn-primary w-full justify-center">
            <span class="material-symbols-outlined text-[18px]">login</span>
            <span>Masuk ke Panel Admin</span>
        </button>
    </form>

    <div class="mt-6 w-full border-t border-neutral-200 pt-4 text-center">
        <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-1.5 text-sm font-medium text-neutral-600 transition hover:text-neutral-900">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Halaman Utama Toko
        </a>
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

        document.querySelectorAll('[data-dismiss]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.getElementById(button.dataset.dismiss);
                if (target) {
                    target.remove();
                }
            });
        });
    });
</script>
</body>
</html>