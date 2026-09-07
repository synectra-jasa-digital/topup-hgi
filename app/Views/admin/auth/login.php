<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Ayong Store</title>
    
    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="min-h-screen flex items-center justify-center bg-background font-sans text-on-background p-4 antialiased">

    <div class="w-full max-w-sm space-y-6">
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-primary text-white flex items-center justify-center font-bold mx-auto shadow-md">
                <span class="material-symbols-outlined text-[32px]">shield_person</span>
            </div>
            <h1 class="text-2xl font-extrabold text-on-surface font-sans">Panel Admin</h1>
            <p class="text-xs text-neutral-500 font-inter">Masuk untuk mengelola Ayong Store</p>
        </div>

        <div class="bg-surface-white border border-neutral-200 rounded-2xl p-6 shadow-sm font-inter">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-4 rounded-xl bg-error-container text-on-error-container p-3 text-xs font-semibold flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">error</span>
                    <span><?= esc(session()->getFlashdata('error')) ?></span>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-4 rounded-xl bg-success-light text-success p-3 text-xs font-semibold flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('admin/login') ?>" class="space-y-4">
                <?= csrf_field() ?>

                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-semibold text-on-surface block">Email Admin</label>
                    <div class="relative flex items-center">
                        <input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" class="w-full rounded-xl border border-neutral-200 p-3 pl-10 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" placeholder="owner@ayongstore.test" required>
                        <span class="material-symbols-outlined text-[20px] text-neutral-400 absolute left-3">mail</span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-semibold text-on-surface block">Kata Sandi</label>
                    <div class="relative flex items-center">
                        <input type="password" id="password" name="password" class="w-full rounded-xl border border-neutral-200 p-3 pl-10 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all" placeholder="••••••••" required>
                        <span class="material-symbols-outlined text-[20px] text-neutral-400 absolute left-3">lock</span>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold text-sm py-3 rounded-xl transition-all shadow-md flex items-center justify-center gap-2 active:scale-[0.99] mt-2">
                    <span class="material-symbols-outlined text-[18px]">login</span>
                    <span>Masuk ke Panel</span>
                </button>
            </form>
        </div>

        <div class="text-center">
            <a href="<?= base_url('/') ?>" class="text-xs font-semibold text-neutral-500 hover:text-primary inline-flex items-center gap-1 transition-colors">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali ke Halaman Utama</span>
            </a>
        </div>
    </div>

</body>
</html>

