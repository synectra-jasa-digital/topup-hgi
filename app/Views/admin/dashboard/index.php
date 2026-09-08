<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$isOwner = $role === 'owner';

$adminCards = [
    ['label' => 'Kelola Produk', 'icon' => 'inventory_2', 'desc' => 'Atur katalog top up dan harga jual.', 'href' => base_url('admin/produk')],
    ['label' => 'Kelola Pesanan', 'icon' => 'shopping_cart', 'desc' => 'Pantau status transaksi masuk dan selesai.', 'href' => base_url('admin/pesanan')],
    ['label' => 'Voucher & Promo', 'icon' => 'confirmation_number', 'desc' => 'Buat diskon untuk mendorong konversi.', 'href' => base_url('admin/voucher')],
    ['label' => 'Kelola Banner', 'icon' => 'photo_library', 'desc' => 'Atur banner promosi di halaman depan.', 'href' => base_url('admin/banner')],
];

$ownerCards = [
    ['label' => 'Laporan Penjualan', 'icon' => 'bar_chart', 'desc' => 'Grafik pendapatan lengkap dan export.', 'href' => base_url('admin/laporan')],
    ['label' => 'Akun Admin', 'icon' => 'manage_accounts', 'desc' => 'Kelola akses tim admin toko.', 'href' => base_url('admin/akun-admin')],
    ['label' => 'Pengaturan Toko', 'icon' => 'settings', 'desc' => 'Kontak, integrasi pembayaran, dan WA.', 'href' => base_url('admin/pengaturan-toko')],
];

$shortcutCards = $isOwner ? array_merge($adminCards, $ownerCards) : $adminCards;
?>

<div class="flex flex-col gap-6">
    <div>
        <h1 class="text-xl font-semibold text-neutral-900">Selamat datang, <?= esc(session('admin_name')) ?>.</h1>
        <p class="mt-1 text-sm text-neutral-500">
            <?= $isOwner
                ? 'Pantau performa penjualan dan kelola seluruh operasional toko dari satu panel.'
                : 'Kelola produk, pesanan, banner, dan voucher dari satu panel.' ?>
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 <?= $isOwner ? 'xl:grid-cols-3' : '' ?>">
        <?php if ($isOwner): ?>
            <div class="panel-surface p-5">
                <p class="text-sm text-neutral-500">Pendapatan Hari Ini</p>
                <p class="mt-1.5 text-2xl font-semibold text-primary">Rp <?= number_format($today['revenue'] ?? 0, 0, ',', '.') ?></p>
                <p class="mt-1 text-sm text-neutral-500"><?= $today['order_count'] ?? 0 ?> transaksi selesai</p>
            </div>
            <div class="panel-surface p-5">
                <p class="text-sm text-neutral-500">Pendapatan Kemarin</p>
                <p class="mt-1.5 text-2xl font-semibold text-neutral-900">Rp <?= number_format($yesterday['revenue'] ?? 0, 0, ',', '.') ?></p>
                <p class="mt-1 text-sm text-neutral-500"><?= $yesterday['order_count'] ?? 0 ?> transaksi selesai</p>
            </div>
        <?php else: ?>
            <div class="panel-surface p-5">
                <p class="text-sm text-neutral-500">Transaksi Selesai Hari Ini</p>
                <p class="mt-1.5 text-2xl font-semibold text-primary"><?= $today['order_count'] ?? 0 ?></p>
                <p class="mt-1 text-sm text-neutral-500">Pesanan berstatus selesai</p>
            </div>
        <?php endif; ?>

        <a href="<?= base_url('admin/pesanan?status=dibayar') ?>" class="panel-surface block p-5 transition hover:bg-neutral-50">
            <p class="text-sm text-neutral-500">Perlu Ditindaklanjuti</p>
            <p class="mt-1.5 text-2xl font-semibold text-neutral-900"><?= $pendingCount ?></p>
            <p class="mt-1 text-sm text-neutral-500">Pesanan menunggu diproses / diselesaikan</p>
        </a>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.55fr_1fr]">
        <div class="flex flex-col gap-6">
            <?php if ($isOwner): ?>
                <section class="panel-surface p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-neutral-900">Tren Pendapatan 7 Hari</h2>
                        <a href="<?= base_url('admin/laporan') ?>" class="text-sm font-medium text-primary hover:text-primary-dark">Lihat laporan lengkap</a>
                    </div>
                    <canvas id="dashboardRevenueChart" height="110" class="mt-4"></canvas>
                </section>

                <section class="panel-surface p-6">
                    <h2 class="text-base font-semibold text-neutral-900">Kategori Terlaris (30 Hari)</h2>
                    <?php if (empty($topCategories)): ?>
                        <p class="mt-3 text-sm text-neutral-500">Belum ada data penjualan pada periode ini.</p>
                    <?php else: ?>
                        <?php $maxRevenue = max(array_column($topCategories, 'total_revenue')) ?: 1; ?>
                        <div class="mt-4 space-y-4">
                            <?php foreach ($topCategories as $cat): ?>
                                <div>
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-medium text-neutral-900"><?= esc($cat['category_name']) ?></span>
                                        <span class="text-neutral-500"><?= (int) $cat['total_quantity'] ?> transaksi · Rp <?= number_format($cat['total_revenue'], 0, ',', '.') ?></span>
                                    </div>
                                    <div class="mt-1.5 h-1.5 rounded-full bg-neutral-100">
                                        <div class="h-1.5 rounded-full bg-primary" style="width: <?= (int) round(($cat['total_revenue'] / $maxRevenue) * 100) ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <section class="panel-surface p-6">
                <h2 class="text-base font-semibold text-neutral-900">Akses Cepat</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <?php foreach ($shortcutCards as $card): ?>
                        <a href="<?= esc($card['href']) ?>" class="panel-surface p-5 transition hover:bg-neutral-50">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-neutral-100 text-neutral-700">
                                <span class="material-symbols-outlined text-[20px]"><?= esc($card['icon']) ?></span>
                            </span>
                            <h3 class="mt-3 text-sm font-semibold text-neutral-900"><?= esc($card['label']) ?></h3>
                            <p class="mt-1 text-sm leading-5 text-neutral-500"><?= esc($card['desc']) ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <aside class="panel-surface h-fit p-6">
            <h2 class="text-base font-semibold text-neutral-900">Aktivitas Terbaru</h2>
            <div class="mt-2">
                <?php if (empty($recentLogs)): ?>
                    <p class="mt-3 text-sm text-neutral-500">Belum ada aktivitas.</p>
                <?php else: ?>
                    <ul class="divide-y divide-neutral-100">
                        <?php foreach ($recentLogs as $log): ?>
                            <li class="flex items-start gap-3 py-3 first:pt-2 last:pb-0">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-neutral-300"></span>
                                <div class="min-w-0">
                                    <p class="text-sm leading-5 text-neutral-900">
                                        <span class="font-medium"><?= esc($log['admin_name']) ?></span>
                                        <?= esc($log['description']) ?>
                                    </p>
                                    <p class="mt-0.5 text-xs text-neutral-500"><?= esc(date('d M, H:i', strtotime($log['created_at']))) ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>

<?php if ($isOwner): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('dashboardRevenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?= json_encode($chartData) ?>,
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37, 99, 235, 0.06)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 2,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
<?php endif; ?>
<?= $this->endSection() ?>