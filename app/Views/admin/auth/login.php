<?php
$error   = session()->getFlashdata('error');
$success = session()->getFlashdata('success');

$storeSettings = new \App\Models\StoreSettingModel();
$storeName     = $storeSettings->getVal('store_name', 'Ayong Store');
$storeLogo     = $storeSettings->getVal('store_logo');

$ring  = 'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600';
$field = 'w-full min-h-12 rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-3 text-base text-slate-900 outline-none transition-all placeholder:text-slate-500 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-200 sm:text-sm';
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Masuk Panel Admin - <?= esc($storeName) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&family=Material+Symbols+Outlined:wght,FILL@400,0&display=swap">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">

<main class="mx-auto flex min-h-screen w-full max-w-sm flex-col justify-center px-6 py-10">
    <div class="rise-stagger space-y-6">

        <header class="space-y-4 text-center">
            <a class="mx-auto inline-flex min-h-11 items-center rounded-md <?= $ring ?>" href="<?= base_url('/') ?>">
                <?php if ($storeLogo): ?>
                    <img src="<?= base_url($storeLogo) ?>" alt="<?= esc($storeName) ?>" class="h-10 w-auto object-contain">
                <?php else: ?>
                    <span class="font-display text-xl font-extrabold tracking-tight text-slate-950"><?= esc($storeName) ?></span>
                <?php endif; ?>
            </a>
            <div>
                <h1 class="font-display text-3xl font-extrabold tracking-tight text-slate-950">Masuk ke panel admin</h1>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Gunakan email dan kata sandi akun admin Anda.</p>
            </div>
        </header>

        <div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <?php if ($error): ?>
                <div id="login-alert" class="login-shake flex items-start gap-2.5 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-sm font-medium text-rose-900" role="alert">
                    <span class="material-symbols-outlined shrink-0 text-[20px] text-rose-700" aria-hidden="true">error</span>
                    <span><?= esc($error) ?></span>
                </div>
            <?php elseif ($success): ?>
                <div class="flex items-start gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 text-sm font-medium text-emerald-900" role="status">
                    <span class="material-symbols-outlined shrink-0 text-[20px] text-emerald-700" aria-hidden="true">check_circle</span>
                    <span><?= esc($success) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= base_url('admin/login') ?>" id="login-form" class="space-y-4" novalidate>
                <?= csrf_field() ?>

                <div class="space-y-1.5">
                    <label for="email" class="block text-sm font-bold text-slate-900">Email</label>
                    <input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" required autofocus autocomplete="email" inputmode="email" spellcheck="false" autocapitalize="off"
                           class="<?= $field ?>" placeholder="nama@email.com" aria-describedby="login-required">
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="block text-sm font-bold text-slate-900">Kata Sandi</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                               class="<?= $field ?> pr-14" aria-describedby="login-required caps-hint">
                        <button type="button" id="toggle-password" aria-pressed="false" aria-label="Tampilkan kata sandi"
                                class="absolute right-1 top-1/2 flex h-11 w-11 -translate-y-1/2 cursor-pointer items-center justify-center rounded-lg text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 <?= $ring ?>">
                            <span class="material-symbols-outlined text-[22px]" aria-hidden="true" id="toggle-password-icon">visibility</span>
                        </button>
                    </div>
                    <p id="caps-hint" class="hidden items-center gap-1 text-sm font-medium text-amber-800" role="status">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">keyboard_capslock</span> Caps Lock aktif
                    </p>
                </div>

                <p id="login-required" class="hidden items-center gap-1 text-sm font-medium text-rose-800" role="alert">
                    <span class="material-symbols-outlined text-[16px]" aria-hidden="true">error</span> Isi email dan kata sandi terlebih dahulu.
                </p>

                <button type="submit" id="login-submit"
                        class="flex min-h-12 w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-blue-700 bg-blue-600 px-5 py-3 font-display text-sm font-bold text-white shadow-sm transition-all hover:bg-blue-700 active:scale-[0.99] disabled:cursor-wait disabled:opacity-70 <?= $ring ?>">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true" id="login-submit-icon">login</span>
                    <span id="login-submit-label">Masuk</span>
                </button>
            </form>

            <p class="text-center text-sm text-slate-600">Lupa kata sandi? Hubungi owner toko.</p>
        </div>

        <div class="text-center">
            <a href="<?= base_url('/') ?>" class="inline-flex min-h-11 items-center gap-1 rounded-lg px-3 text-sm font-semibold text-slate-700 transition-colors hover:text-blue-700 <?= $ring ?>">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_back</span>
                <span>Kembali ke halaman toko</span>
            </a>
        </div>

    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('login-form');
        var email = document.getElementById('email');
        var password = document.getElementById('password');
        var toggle = document.getElementById('toggle-password');
        var toggleIcon = document.getElementById('toggle-password-icon');
        var capsHint = document.getElementById('caps-hint');
        var required = document.getElementById('login-required');
        var submit = document.getElementById('login-submit');
        var label = document.getElementById('login-submit-label');
        var icon = document.getElementById('login-submit-icon');

        function showPassword(visible) {
            password.type = visible ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', String(visible));
            toggleIcon.textContent = visible ? 'visibility_off' : 'visibility';
        }

        toggle.addEventListener('click', function () { showPassword(password.type === 'password'); });

        // Caps Lock is the most common reason a correct password is rejected.
        function checkCaps(e) {
            var on = e.getModifierState && e.getModifierState('CapsLock');
            capsHint.classList.toggle('hidden', !on);
            capsHint.classList.toggle('flex', !!on);
        }
        password.addEventListener('keydown', checkCaps);
        password.addEventListener('keyup', checkCaps);
        password.addEventListener('blur', function () { capsHint.classList.add('hidden'); capsHint.classList.remove('flex'); });

        [email, password].forEach(function (field) {
            field.addEventListener('input', function () {
                field.removeAttribute('aria-invalid');
                required.classList.add('hidden');
                required.classList.remove('flex');
            });
        });

        form.addEventListener('submit', function (e) {
            var missing = [email, password].filter(function (field) { return field.value.trim() === ''; })[0];
            if (missing) {
                e.preventDefault();
                missing.setAttribute('aria-invalid', 'true');
                required.classList.remove('hidden');
                required.classList.add('flex');
                missing.focus();
                return;
            }
            showPassword(false); // never leave the password visible in a restored page
            submit.disabled = true;
            label.textContent = 'Memeriksa...';
            icon.textContent = 'progress_activity';
            icon.classList.add('animate-spin');
        });

        window.addEventListener('pageshow', function () { // back button restores the page from cache
            submit.disabled = false;
            label.textContent = 'Masuk';
            icon.textContent = 'login';
            icon.classList.remove('animate-spin');
        });
    });
</script>
</body>
</html>
