<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Panel Admin — Ayong Store</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="admin-shell min-h-screen bg-neutral-50 px-4 py-8 font-sans text-neutral-900 antialiased">
<?php
$error = session()->getFlashdata('error');
$success = session()->getFlashdata('success');
$storeLogo = (new \App\Models\StoreSettingModel())->getVal('store_logo');
?>

<div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-md flex-col justify-center">
    <div class="panel-surface w-full p-6 md:p-8">
        <div class="text-center">
            <?php if ($storeLogo): ?>
                <div class="mx-auto flex h-11 w-11 items-center justify-center overflow-hidden rounded-xl border border-neutral-200 bg-white">
                    <img src="<?= base_url($storeLogo) ?>" alt="Logo Toko" class="h-full w-full object-contain p-1.5">
                </div>
            <?php else: ?>
                <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-primary text-white">
                    <span class="material-symbols-outlined text-[22px]">shield_person</span>
                </div>
            <?php endif; ?>
            <h1 class="mt-4 font-display text-xl font-semibold tracking-tight text-neutral-900">Masuk Panel Admin</h1>
            <p class="mt-1 text-sm leading-6 text-neutral-500">Gunakan akun admin untuk mengelola operasional toko.</p>
        </div>

        <form method="POST" action="<?= base_url('admin/login') ?>" autocomplete="on" class="mt-6 space-y-4">
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

            <button type="submit" id="btnSubmitLogin" class="btn btn-primary w-full justify-center">
                <span class="material-symbols-outlined text-[18px]">login</span>
                <span>Masuk ke Panel Admin</span>
            </button>
        </form>

        <div class="mt-6 border-t border-neutral-200 pt-4 text-center">
            <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-1.5 text-sm font-medium text-neutral-600 transition hover:text-neutral-900">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                Kembali ke Halaman Utama Toko
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const errorMsg = <?= json_encode($error) ?>;
        const successMsg = <?= json_encode($success) ?>;

        const swalBase = {
            buttonsStyling: false,
            customClass: {
                popup: '!rounded-2xl !p-6 font-sans shadow-2xl border border-neutral-100',
                title: '!text-lg !font-bold !text-neutral-900',
                htmlContainer: '!text-sm !text-neutral-600',
                confirmButton: 'btn btn-primary !py-2.5 !px-5 !rounded-xl !font-semibold',
            },
        };

        if (errorMsg) {
            Swal.fire({
                ...swalBase,
                icon: 'error',
                title: 'Gagal Masuk',
                text: errorMsg,
                confirmButtonText: 'Tutup',
                customClass: {
                    ...swalBase.customClass,
                    confirmButton: 'btn btn-primary !bg-rose-600 hover:!bg-rose-700 !py-2.5 !px-5 !rounded-xl !font-semibold',
                },
            });
        } else if (successMsg) {
            Swal.fire({
                ...swalBase,
                icon: 'success',
                title: 'Berhasil',
                text: successMsg,
                confirmButtonText: 'Tutup',
            });
        }

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
