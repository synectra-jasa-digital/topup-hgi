<?php
    $storeSettings = new \App\Models\StoreSettingModel();
    $storeName = $storeSettings->getVal('store_name', 'Ayong Store');
    $storeLogo = $storeSettings->getVal('store_logo');
    $pageTitle = $title ?? 'Panel Admin';
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?> - <?= esc($storeName) ?></title>
    <?php if ($storeLogo): ?>
        <link rel="icon" href="<?= base_url($storeLogo) ?>" type="image/png">
        <link rel="shortcut icon" href="<?= base_url($storeLogo) ?>" type="image/png">
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@400,0..1&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <script defer src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="admin-shell min-h-screen bg-neutral-50 font-sans text-neutral-900 antialiased md:flex" x-data="{ sidebarOpen: false }">
    <?php
    $role = session('admin_role');
    $currentUrl = current_url();
    $flashSuccess = session()->getFlashdata('success');
    $flashError = session()->getFlashdata('error');
    $flashWarning = session()->getFlashdata('warning');
    $sidebarItems = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'href' => base_url('admin/dashboard'), 'match' => 'admin/dashboard'],
        ['label' => 'Kategori Produk', 'icon' => 'category', 'href' => base_url('admin/kategori-produk'), 'match' => 'kategori-produk'],
        ['label' => 'Produk Top Up', 'icon' => 'inventory_2', 'href' => base_url('admin/produk'), 'match' => 'admin/produk'],
        ['label' => 'Kategori Banner', 'icon' => 'bookmarks', 'href' => base_url('admin/kategori-banner'), 'match' => 'kategori-banner'],
        ['label' => 'Banner Promo', 'icon' => 'view_carousel', 'href' => base_url('admin/banner'), 'match' => 'admin/banner'],
        ['label' => 'Info Berjalan', 'icon' => 'campaign', 'href' => base_url('admin/info-berjalan'), 'match' => 'info-berjalan'],
        ['label' => 'Daftar Pesanan', 'icon' => 'shopping_cart', 'href' => base_url('admin/pesanan'), 'match' => 'admin/pesanan'],
        ['label' => 'Voucher Diskon', 'icon' => 'confirmation_number', 'href' => base_url('admin/voucher'), 'match' => 'admin/voucher'],
        ['label' => 'Katalog Bongkar', 'icon' => 'inventory', 'href' => base_url('admin/bongkar-katalog'), 'match' => 'bongkar-katalog'],
        ['label' => 'Pesanan Bongkar', 'icon' => 'currency_exchange', 'href' => base_url('admin/bongkar-pesanan'), 'match' => 'bongkar-pesanan'],
    ];
    ?>

    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 bg-black/40 md:hidden" @click="sidebarOpen = false"></div>

    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-neutral-200 bg-white transition-transform duration-300 md:sticky md:top-0 md:h-screen md:shrink-0 md:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex h-16 items-center justify-between border-b border-neutral-200 px-4">
            <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center gap-2.5">
                <?php if ($storeLogo): ?>
                    <span class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-lg border border-neutral-200 bg-white">
                        <img src="<?= base_url($storeLogo) ?>" alt="Logo Toko" class="h-full w-full object-contain p-1">
                    </span>
                <?php else: ?>
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-white">
                        <span class="material-symbols-outlined text-[20px]">shield_person</span>
                    </span>
                <?php endif; ?>
                <div class="flex flex-col">
                    <span class="font-display text-sm font-semibold tracking-tight text-neutral-900">Ayong Admin</span>
                    <span class="text-xs text-neutral-500">Panel operasional</span>
                </div>
            </a>
            <button class="rounded-lg p-2 text-neutral-500 transition hover:bg-neutral-100 hover:text-neutral-900 md:hidden" @click="sidebarOpen = false" aria-label="Tutup sidebar">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-3 text-sm hide-scrollbar">
            <?php foreach ($sidebarItems as $item): ?>
                <?php $active = str_contains($currentUrl, $item['match']); ?>
                <a href="<?= esc($item['href']) ?>" class="sidebar-link <?= $active ? 'sidebar-link-active font-medium text-neutral-900' : '' ?>">
                    <span class="material-symbols-outlined text-[20px] <?= $active ? 'text-primary' : 'text-neutral-400' ?>"><?= esc($item['icon']) ?></span>
                    <span><?= esc($item['label']) ?></span>
                </a>
            <?php endforeach; ?>

            <?php if ($role === 'owner'): ?>
                <div class="sidebar-section">Owner Menu</div>
                <a href="<?= base_url('admin/laporan') ?>" class="sidebar-link <?= str_contains($currentUrl, 'admin/laporan') ? 'sidebar-link-active font-medium text-neutral-900' : '' ?>">
                    <span class="material-symbols-outlined text-[20px] <?= str_contains($currentUrl, 'admin/laporan') ? 'text-primary' : 'text-neutral-400' ?>">bar_chart</span>
                    <span>Laporan Penjualan</span>
                </a>
                <a href="<?= base_url('admin/pengaturan-toko') ?>" class="sidebar-link <?= str_contains($currentUrl, 'pengaturan-toko') ? 'sidebar-link-active font-medium text-neutral-900' : '' ?>">
                    <span class="material-symbols-outlined text-[20px] <?= str_contains($currentUrl, 'pengaturan-toko') ? 'text-primary' : 'text-neutral-400' ?>">settings</span>
                    <span>Pengaturan Toko</span>
                </a>
                <a href="<?= base_url('admin/akun-admin') ?>" class="sidebar-link <?= str_contains($currentUrl, 'akun-admin') ? 'sidebar-link-active font-medium text-neutral-900' : '' ?>">
                    <span class="material-symbols-outlined text-[20px] <?= str_contains($currentUrl, 'akun-admin') ? 'text-primary' : 'text-neutral-400' ?>">manage_accounts</span>
                    <span>Akun Admin</span>
                </a>
                <a href="<?= base_url('admin/log-aktivitas') ?>" class="sidebar-link <?= str_contains($currentUrl, 'log-aktivitas') ? 'sidebar-link-active font-medium text-neutral-900' : '' ?>">
                    <span class="material-symbols-outlined text-[20px] <?= str_contains($currentUrl, 'log-aktivitas') ? 'text-primary' : 'text-neutral-400' ?>">fact_check</span>
                    <span>Log Aktivitas</span>
                </a>
                <a href="<?= base_url('admin/backup-database') ?>" class="sidebar-link <?= str_contains($currentUrl, 'backup-database') ? 'sidebar-link-active font-medium text-neutral-900' : '' ?>">
                    <span class="material-symbols-outlined text-[20px] <?= str_contains($currentUrl, 'backup-database') ? 'text-primary' : 'text-neutral-400' ?>">database</span>
                    <span>Backup Database</span>
                </a>
            <?php endif; ?>
        </nav>

        <div class="border-t border-neutral-200 p-3">
            <a href="<?= base_url('/') ?>" target="_blank" class="btn btn-ghost w-full justify-start">
                <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                <span>Lihat Storefront</span>
            </a>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        <?= $this->include('layouts/partials/topbar') ?>

        <main class="flex-1 px-4 py-6 md:px-6 md:py-8">
            <div class="mx-auto w-full max-w-7xl space-y-5">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

    <div x-data="confirmDialog()" @keydown.escape.window="open = false">
        <div x-show="open" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" x-transition.opacity>
            <div class="absolute inset-0 bg-[rgba(31,41,55,0.48)]" @click="open = false"></div>
            <div class="relative w-full max-w-sm rounded-xl border border-neutral-200 bg-white p-6 shadow-[0_12px_32px_rgba(30,58,138,0.12)]" role="alertdialog" aria-modal="true">
                <h3 class="font-display text-base font-semibold tracking-tight text-neutral-900" x-text="title"></h3>
                <p class="mt-2 text-sm leading-6 text-neutral-500" x-text="message"></p>
                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" class="btn btn-secondary" x-ref="cancelBtn" x-text="cancelText" @click="open = false"></button>
                    <button type="button" class="btn btn-primary" x-text="confirmText" @click="proceed()"></button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const flashSuccess = <?= json_encode($flashSuccess) ?>;
            const flashError = <?= json_encode($flashError) ?>;
            const flashWarning = <?= json_encode($flashWarning) ?>;

            if (typeof Swal === 'undefined') {
                return;
            }

            const swalBase = {
                confirmButtonColor: '#2563eb',
                buttonsStyling: false,
                customClass: {
                    popup: '!rounded-2xl !p-6 font-sans shadow-2xl border border-neutral-100',
                    title: '!text-lg !font-bold !text-neutral-900',
                    htmlContainer: '!text-sm !text-neutral-600',
                    confirmButton: 'btn btn-primary !py-2.5 !px-5 !rounded-xl !font-semibold',
                },
            };

            if (flashSuccess) {
                Swal.fire({
                    ...swalBase,
                    icon: 'success',
                    title: 'Berhasil!',
                    text: flashSuccess,
                    confirmButtonText: 'Tutup',
                });
            } else if (flashError) {
                Swal.fire({
                    ...swalBase,
                    icon: 'error',
                    title: 'Gagal!',
                    text: flashError,
                    confirmButtonText: 'Tutup',
                    customClass: {
                        ...swalBase.customClass,
                        confirmButton: 'btn btn-primary !bg-rose-600 hover:!bg-rose-700 !py-2.5 !px-5 !rounded-xl !font-semibold',
                    },
                });
            } else if (flashWarning) {
                Swal.fire({
                    ...swalBase,
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: flashWarning,
                    confirmButtonText: 'Mengerti',
                    customClass: {
                        ...swalBase.customClass,
                        confirmButton: 'btn btn-primary !bg-amber-600 hover:!bg-amber-700 !py-2.5 !px-5 !rounded-xl !font-semibold',
                    },
                });
            }
        });

        function confirmDialog() {
            return {
                open: false,
                title: 'Konfirmasi',
                message: '',
                confirmText: 'Ya, lanjutkan',
                cancelText: 'Batal',
                action: null,
                init() {
                    const component = this;

                    document.addEventListener('submit', (event) => {
                        const form = event.target.closest('form[data-confirm]');
                        if (!form) {
                            return;
                        }

                        event.preventDefault();
                        component.title = form.dataset.confirmTitle || 'Konfirmasi';
                        component.message = form.dataset.confirm || 'Yakin melanjutkan aksi ini?';
                        component.confirmText = form.dataset.confirmButton || 'Ya, lanjutkan';
                        component.cancelText = form.dataset.confirmCancel || 'Batal';
                        component.action = () => HTMLFormElement.prototype.submit.call(form);
                        component.open = true;
                        component.$nextTick(() => component.$refs.cancelBtn?.focus());
                    });

                    document.addEventListener('click', (event) => {
                        const link = event.target.closest('a[data-confirm]');
                        if (!link) {
                            return;
                        }

                        event.preventDefault();
                        component.title = link.dataset.confirmTitle || 'Konfirmasi';
                        component.message = link.dataset.confirm || 'Yakin melanjutkan aksi ini?';
                        component.confirmText = link.dataset.confirmButton || 'Ya, lanjutkan';
                        component.cancelText = link.dataset.confirmCancel || 'Batal';
                        component.action = () => {
                            window.location.href = link.href;
                        };
                        component.open = true;
                        component.$nextTick(() => component.$refs.cancelBtn?.focus());
                    });
                },
                proceed() {
                    this.open = false;
                    if (typeof this.action === 'function') {
                        setTimeout(() => this.action(), 0);
                    }
                },
            };
        }
    </script>
</body>
</html>
