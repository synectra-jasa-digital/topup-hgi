<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Panel Admin') ?> - Ayong Store</title>
    
    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
    <script defer src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>
<body class="min-h-screen flex bg-background font-sans text-on-background antialiased" x-data="{ sidebarOpen: false }">
    <?php $role = session('admin_role'); ?>
    <?php $current_url = current_url(); ?>

    <!-- Sidebar -->
    <aside
        class="fixed inset-y-0 left-0 z-40 w-64 transform bg-surface-white border-r border-neutral-200 transition-transform md:static md:translate-x-0 flex flex-col shadow-sm"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="h-16 flex items-center px-5 border-b border-neutral-200 justify-between">
            <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center gap-2 font-extrabold text-lg text-primary tracking-tight">
                <span class="material-symbols-outlined text-primary text-[24px]">shield_person</span>
                <span>Admin<span class="text-on-surface">Panel</span></span>
            </a>
            <button class="md:hidden text-neutral-500 hover:text-neutral-900" @click="sidebarOpen = false">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <nav class="p-4 space-y-1.5 text-sm font-medium flex-1 overflow-y-auto font-inter">
            <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?= str_contains($current_url, 'admin/dashboard') ? 'bg-primary-light/50 text-primary font-bold' : 'text-neutral-700 hover:bg-neutral-100' ?>">
                <span class="material-symbols-outlined text-[20px]">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a href="<?= base_url('admin/kategori-produk') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?= str_contains($current_url, 'kategori-produk') ? 'bg-primary-light/50 text-primary font-bold' : 'text-neutral-700 hover:bg-neutral-100' ?>">
                <span class="material-symbols-outlined text-[20px]">category</span>
                <span>Kategori Produk</span>
            </a>
            <a href="<?= base_url('admin/produk') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?= str_contains($current_url, 'admin/produk') ? 'bg-primary-light/50 text-primary font-bold' : 'text-neutral-700 hover:bg-neutral-100' ?>">
                <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                <span>Produk Top Up</span>
            </a>
            <a href="<?= base_url('admin/kategori-banner') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?= str_contains($current_url, 'kategori-banner') ? 'bg-primary-light/50 text-primary font-bold' : 'text-neutral-700 hover:bg-neutral-100' ?>">
                <span class="material-symbols-outlined text-[20px]">bookmarks</span>
                <span>Kategori Banner</span>
            </a>
            <a href="<?= base_url('admin/banner') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?= str_contains($current_url, 'admin/banner') ? 'bg-primary-light/50 text-primary font-bold' : 'text-neutral-700 hover:bg-neutral-100' ?>">
                <span class="material-symbols-outlined text-[20px]">view_carousel</span>
                <span>Banner Promo</span>
            </a>
            <a href="<?= base_url('admin/pesanan') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?= str_contains($current_url, 'admin/pesanan') ? 'bg-primary-light/50 text-primary font-bold' : 'text-neutral-700 hover:bg-neutral-100' ?>">
                <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
                <span>Daftar Pesanan</span>
            </a>
            <a href="<?= base_url('admin/voucher') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?= str_contains($current_url, 'admin/voucher') ? 'bg-primary-light/50 text-primary font-bold' : 'text-neutral-700 hover:bg-neutral-100' ?>">
                <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
                <span>Voucher Diskon</span>
            </a>

            <?php if ($role === 'owner'): ?>
                <div class="pt-4 mt-4 border-t border-neutral-200 text-[11px] font-bold uppercase tracking-wider text-neutral-400 px-3">Owner Menu</div>
                <a href="<?= base_url('admin/laporan') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?= str_contains($current_url, 'admin/laporan') ? 'bg-primary-light/50 text-primary font-bold' : 'text-neutral-700 hover:bg-neutral-100' ?>">
                    <span class="material-symbols-outlined text-[20px]">bar_chart</span>
                    <span>Laporan Penjualan</span>
                </a>
                <a href="<?= base_url('admin/pengaturan-toko') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?= str_contains($current_url, 'pengaturan-toko') ? 'bg-primary-light/50 text-primary font-bold' : 'text-neutral-700 hover:bg-neutral-100' ?>">
                    <span class="material-symbols-outlined text-[20px]">settings</span>
                    <span>Pengaturan Toko</span>
                </a>
                <a href="<?= base_url('admin/akun-admin') ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?= str_contains($current_url, 'akun-admin') ? 'bg-primary-light/50 text-primary font-bold' : 'text-neutral-700 hover:bg-neutral-100' ?>">
                    <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                    <span>Akun Admin</span>
                </a>
            <?php endif; ?>
        </nav>
        
        <div class="p-4 border-t border-neutral-200">
            <a href="<?= base_url('/') ?>" target="_blank" class="flex items-center justify-center gap-2 text-xs font-semibold text-primary hover:underline">
                <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                <span>Lihat Storefront Publik</span>
            </a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-surface-white border-b border-neutral-200 flex items-center justify-between px-4 md:px-6">
            <button class="md:hidden text-neutral-700 hover:text-neutral-900" @click="sidebarOpen = !sidebarOpen">
                <span class="material-symbols-outlined text-[24px]">menu</span>
            </button>
            <div class="ml-auto flex items-center gap-4 text-sm font-inter">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-primary-light text-primary flex items-center justify-center font-bold text-xs">
                        <?= strtoupper(substr(session('admin_name') ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="flex flex-col text-xs">
                        <span class="font-bold text-on-surface"><?= esc(session('admin_name')) ?></span>
                        <span class="text-neutral-500 capitalize"><?= esc($role) ?></span>
                    </div>
                </div>
                <div class="h-4 w-px bg-neutral-200"></div>
                <a href="<?= base_url('admin/logout') ?>" class="flex items-center gap-1 text-xs font-semibold text-danger hover:underline">
                    <span class="material-symbols-outlined text-[16px]">logout</span>
                    <span>Keluar</span>
                </a>
            </div>
        </header>

        <main class="flex-1 p-4 md:p-8 max-w-7xl w-full">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="mb-6 rounded-xl bg-success-light text-success border border-success/20 px-4 py-3 text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="mb-6 rounded-xl bg-error-container text-on-error-container border border-error/20 px-4 py-3 text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">error</span>
                    <span><?= esc(session()->getFlashdata('error')) ?></span>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>
</body>
</html>

