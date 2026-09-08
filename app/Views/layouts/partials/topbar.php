<?php
$role = session('admin_role');
$adminName = session('admin_name') ?? 'A';
$topbarTitle = $title ?? 'Dashboard';
?>
<header class="sticky top-0 z-40 h-16 border-b border-neutral-200 bg-white">
    <div class="flex h-16 items-center justify-between gap-4 px-4 md:px-6">
        <div class="flex items-center gap-3">
            <button class="rounded-lg p-2 text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900 md:hidden" @click="sidebarOpen = !sidebarOpen" aria-label="Buka sidebar">
                <span class="material-symbols-outlined text-[22px]">menu</span>
            </button>
            <h1 class="text-base font-semibold text-neutral-900"><?= esc($topbarTitle) ?></h1>
        </div>
        <div class="flex items-center gap-2">
            <div class="flex items-center gap-2.5">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-neutral-100 text-xs font-semibold text-neutral-700">
                    <?= strtoupper(substr($adminName, 0, 1)) ?>
                </div>
                <div class="hidden flex-col sm:flex">
                    <span class="text-sm font-medium leading-tight text-neutral-900"><?= esc($adminName) ?></span>
                    <span class="text-xs capitalize leading-tight text-neutral-500"><?= esc($role) ?></span>
                </div>
            </div>
            <a href="<?= base_url('admin/logout') ?>" data-confirm="Yakin ingin keluar dari panel admin?" data-confirm-title="Logout" data-confirm-button="Ya, keluar" class="btn btn-ghost">
                <span class="material-symbols-outlined text-[16px]">logout</span>
                <span>Keluar</span>
            </a>
        </div>
    </div>
</header>