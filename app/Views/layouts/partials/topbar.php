<?php
$role = session('admin_role');
$adminName = session('admin_name') ?? 'A';
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
        <div class="flex items-center gap-2.5">
            <div class="hidden items-center gap-2 rounded-full border border-neutral-200 bg-neutral-50 px-3 py-1.5 sm:flex">
                <div class="flex h-7 w-7 items-center justify-center rounded-full bg-white text-xs font-semibold text-neutral-700 ring-1 ring-inset ring-neutral-200">
                    <?= strtoupper(substr($adminName, 0, 1)) ?>
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="text-sm font-medium text-neutral-900"><?= esc($adminName) ?></span>
                    <span class="text-xs capitalize text-neutral-500"><?= esc($role) ?></span>
                </div>
            </div>
            <a href="<?= base_url('admin/logout') ?>" data-confirm="Yakin ingin keluar dari panel admin?" data-confirm-title="Logout" data-confirm-button="Ya, keluar" class="btn btn-ghost px-3">
                <span class="material-symbols-outlined text-[16px]">logout</span>
                <span class="hidden sm:inline">Keluar</span>
            </a>
        </div>
    </div>
</header>
