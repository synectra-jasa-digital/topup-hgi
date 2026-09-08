<?php
$role = session('admin_role');
$adminName = session('admin_name') ?? 'A';
$adminPhoto = session('admin_photo');
$topbarTitle = $title ?? 'Dashboard';
?>
<header class="topbar-shell">
    <div class="flex h-16 items-center justify-between gap-4 px-4 md:px-6">
        <div class="flex min-w-0 items-center gap-3">
            <button class="rounded-lg p-2 text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900 md:hidden" @click="sidebarOpen = !sidebarOpen" aria-label="Buka sidebar">
                <span class="material-symbols-outlined text-[22px]">menu</span>
            </button>
            <div class="min-w-0">
                <h1 class="topbar-title truncate"><?= esc($topbarTitle) ?></h1>
                <p class="hidden text-xs text-neutral-500 md:block">Panel admin Ayong Store</p>
            </div>
        </div>

        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
            <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-full border border-neutral-200 bg-neutral-50 px-2 py-1.5 transition hover:bg-neutral-100 sm:px-3" aria-haspopup="true" :aria-expanded="open">
                <?php if ($adminPhoto): ?>
                    <img src="<?= base_url($adminPhoto) ?>" alt="Foto profil" class="h-7 w-7 rounded-full object-cover ring-1 ring-inset ring-neutral-200">
                <?php else: ?>
                    <div class="flex h-7 w-7 items-center justify-center rounded-full bg-white text-xs font-semibold text-neutral-700 ring-1 ring-inset ring-neutral-200">
                        <?= strtoupper(substr($adminName, 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div class="hidden flex-col items-start leading-tight sm:flex">
                    <span class="text-sm font-medium text-neutral-900"><?= esc($adminName) ?></span>
                    <span class="text-xs capitalize text-neutral-500"><?= esc($role) ?></span>
                </div>
                <span class="material-symbols-outlined text-[18px] text-neutral-400">expand_more</span>
            </button>

            <div x-show="open" x-cloak x-transition.origin.top.right class="absolute right-0 z-30 mt-2 w-52 rounded-xl border border-neutral-200 bg-white p-1.5 shadow-[0_12px_32px_rgba(30,58,138,0.12)]">
                <a href="<?= base_url('admin/profile') ?>" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-neutral-700 transition hover:bg-neutral-50 hover:text-neutral-900">
                    <span class="material-symbols-outlined text-[18px] text-neutral-400">person</span>
                    Profil Saya
                </a>
                <div class="my-1 border-t border-neutral-100"></div>
                <a href="<?= base_url('admin/logout') ?>" data-confirm="Yakin ingin keluar dari panel admin?" data-confirm-title="Logout" data-confirm-button="Ya, keluar" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-danger transition hover:bg-danger/5">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    Keluar
                </a>
            </div>
        </div>
    </div>
</header>
