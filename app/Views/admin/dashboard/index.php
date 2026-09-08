<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php helper('order'); ?>
<?php
$role = $role ?? session('admin_role');
$pendingCount = $pendingCount ?? 0;
$statusCounts = $statusCounts ?? [];
$chartLabels30 = $chartLabels30 ?? [];
$chartData30 = $chartData30 ?? [];
$isOwner = $role === 'owner';
$adminCards = [
    ['label' => 'Kelola Produk', 'icon' => 'inventory_2', 'desc' => 'Atur katalog top up dan harga jual.', 'href' => base_url('admin/produk')],
    ['label' => 'Kelola Pesanan', 'icon' => 'shopping_cart', 'desc' => 'Pantau transaksi masuk dan penyelesaiannya.', 'href' => base_url('admin/pesanan')],
    ['label' => 'Voucher & Promo', 'icon' => 'confirmation_number', 'desc' => 'Kelola diskon untuk checkout.', 'href' => base_url('admin/voucher')],
    ['label' => 'Kelola Banner', 'icon' => 'photo_library', 'desc' => 'Atur banner promosi di halaman depan.', 'href' => base_url('admin/banner')],
];

$ownerCards = [
    ['label' => 'Laporan Penjualan', 'icon' => 'bar_chart', 'desc' => 'Ringkasan pendapatan dan transaksi.', 'href' => base_url('admin/laporan')],
    ['label' => 'Akun Admin', 'icon' => 'manage_accounts', 'desc' => 'Kelola akses tim admin toko.', 'href' => base_url('admin/akun-admin')],
    ['label' => 'Pengaturan Toko', 'icon' => 'settings', 'desc' => 'Kontak, pembayaran, dan WA.', 'href' => base_url('admin/pengaturan-toko')],
];

$shortcutCards = $isOwner ? array_merge($adminCards, $ownerCards) : $adminCards;
?>

<div class="space-y-6">
    <div>
        <h1 class="section-title">Selamat datang, <?= esc(session('admin_name')) ?></h1>
        <p class="section-subtitle">
            <?= $isOwner
                ? 'Pantau performa penjualan dan kelola operasional dari satu panel.'
                : 'Kelola produk, pesanan, banner, dan voucher dari satu panel.' ?>
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 <?= $isOwner ? 'xl:grid-cols-3' : '' ?>">
        <?php if ($isOwner): ?>
            <div class="stat-tile border-[1.5px] border-primary bg-primary-light">
                <p class="stat-label">Pendapatan Hari Ini</p>
                <p class="stat-value-accent">Rp <?= number_format($today['revenue'] ?? 0, 0, ',', '.') ?></p>
                <p class="mt-1 text-sm text-neutral-500"><?= $today['order_count'] ?? 0 ?> transaksi selesai</p>
            </div>
            <div class="stat-tile">
                <p class="stat-label">Pendapatan Kemarin</p>
                <p class="stat-value">Rp <?= number_format($yesterday['revenue'] ?? 0, 0, ',', '.') ?></p>
                <p class="mt-1 text-sm text-neutral-500"><?= $yesterday['order_count'] ?? 0 ?> transaksi selesai</p>
            </div>
        <?php else: ?>
            <div class="stat-tile border-[1.5px] border-primary bg-primary-light">
                <p class="stat-label">Transaksi Selesai Hari Ini</p>
                <p class="stat-value-accent"><?= $today['order_count'] ?? 0 ?></p>
                <p class="mt-1 text-sm text-neutral-500">Pesanan berstatus selesai</p>
            </div>
        <?php endif; ?>

        <a href="<?= base_url('admin/pesanan?status=dibayar') ?>" class="stat-tile block transition hover:border-primary/30 hover:bg-neutral-50">
            <p class="stat-label">Perlu Ditindaklanjuti</p>
            <p class="stat-value"><?= $pendingCount ?></p>
            <p class="mt-1 text-sm text-neutral-500">Pesanan menunggu diproses</p>
        </a>
    </div>

    <section class="panel-surface">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-display text-base font-bold tracking-tight text-neutral-900">Status Pesanan</h2>
                <p class="mt-0.5 text-xs text-neutral-500">Ringkasan transaksi berdasarkan status saat ini.</p>
            </div>
            <a href="<?= base_url('admin/pesanan') ?>" class="inline-flex items-center gap-1 text-xs font-semibold text-primary hover:text-primary-dark">
                <span>Lihat semua</span>
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            <?php
            $statusMeta = [
                'menunggu_pembayaran' => ['dot' => 'bg-amber-500', 'active_border' => 'hover:border-amber-400/60'],
                'dibayar'             => ['dot' => 'bg-blue-500', 'active_border' => 'hover:border-blue-400/60'],
                'diproses'            => ['dot' => 'bg-indigo-500', 'active_border' => 'hover:border-indigo-400/60'],
                'selesai'             => ['dot' => 'bg-emerald-500', 'active_border' => 'hover:border-emerald-400/60'],
                'gagal'               => ['dot' => 'bg-rose-500', 'active_border' => 'hover:border-rose-400/60'],
                'dibatalkan'          => ['dot' => 'bg-slate-400', 'active_border' => 'hover:border-slate-400/60'],
            ];
            ?>
            <?php foreach ($statusCounts as $status => $count): ?>
                <?php $meta = $statusMeta[$status] ?? ['dot' => 'bg-neutral-400', 'active_border' => 'hover:border-primary/40']; ?>
                <a href="<?= base_url('admin/pesanan?status=' . $status) ?>" class="group flex flex-col justify-between rounded-xl border border-neutral-200/80 bg-white p-3.5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md <?= $meta['active_border'] ?>">
                    <div class="flex items-center justify-between gap-1.5">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-neutral-600">
                            <span class="h-2 w-2 rounded-full <?= $meta['dot'] ?>"></span>
                            <span><?= order_status_label($status) ?></span>
                        </span>
                    </div>
                    <div class="mt-3 flex items-baseline justify-between">
                        <span class="font-display text-2xl font-black tracking-tight text-neutral-900"><?= number_format($count, 0, ',', '.') ?></span>
                        <span class="text-[11px] font-medium text-neutral-400">pesanan</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
        <div class="space-y-6">
            <?php if ($isOwner): ?>
                <section class="panel-surface">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-neutral-200 pb-4">
                        <div>
                            <h2 class="font-display text-base font-semibold tracking-tight text-neutral-900">Tren Pendapatan</h2>
                            <p class="mt-1 text-sm text-neutral-500">Grafik ringkas performa harian.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="inline-flex rounded-lg border border-neutral-200 p-0.5">
                                <button type="button" data-range="7" class="chart-range-btn rounded-md px-3 py-1.5 text-xs font-medium transition">7 Hari</button>
                                <button type="button" data-range="30" class="chart-range-btn rounded-md px-3 py-1.5 text-xs font-medium transition">30 Hari</button>
                            </div>
                            <a href="<?= base_url('admin/laporan') ?>" class="text-sm font-medium text-primary hover:text-primary-dark">Lihat laporan</a>
                        </div>
                    </div>
                    <canvas id="dashboardRevenueChart" height="110" class="mt-4"></canvas>
                </section>

                <section class="panel-surface">
                    <div>
                        <h2 class="font-display text-base font-semibold tracking-tight text-neutral-900">Kategori Terlaris (30 Hari)</h2>
                        <p class="mt-1 text-sm text-neutral-500">Kontribusi kategori berdasarkan pendapatan.</p>
                    </div>
                    <?php if (empty($topCategories)): ?>
                        <p class="mt-4 text-sm text-neutral-500">Belum ada data penjualan pada periode ini.</p>
                    <?php else: ?>
                        <?php $maxRevenue = max(array_column($topCategories, 'total_revenue')) ?: 1; ?>
                        <div class="mt-4 space-y-4">
                            <?php foreach ($topCategories as $cat): ?>
                                <div>
                                    <div class="flex items-center justify-between gap-4 text-sm">
                                        <span class="font-medium text-neutral-900"><?= esc($cat['category_name']) ?></span>
                                        <span class="text-neutral-500"><?= (int) $cat['total_quantity'] ?> transaksi · Rp <?= number_format($cat['total_revenue'], 0, ',', '.') ?></span>
                                    </div>
                                    <div class="mt-2 h-1.5 rounded-full bg-neutral-100">
                                        <div class="h-1.5 rounded-full bg-primary" style="width: <?= (int) round(($cat['total_revenue'] / $maxRevenue) * 100) ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <section class="panel-surface">
                <div>
                    <h2 class="font-display text-base font-semibold tracking-tight text-neutral-900">Akses Cepat</h2>
                    <p class="mt-1 text-sm text-neutral-500">Menu utama untuk pekerjaan harian.</p>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <?php foreach ($shortcutCards as $card): ?>
                        <a href="<?= esc($card['href']) ?>" class="rounded-xl border border-neutral-200 bg-white p-4 transition hover:border-primary/30 hover:bg-neutral-50">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg border border-neutral-200 bg-neutral-50 text-neutral-700">
                                <span class="material-symbols-outlined text-[20px]"><?= esc($card['icon']) ?></span>
                            </span>
                            <h3 class="mt-3 font-display text-sm font-semibold tracking-tight text-neutral-900"><?= esc($card['label']) ?></h3>
                            <p class="mt-1 text-sm leading-6 text-neutral-500"><?= esc($card['desc']) ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

        <aside class="panel-surface h-fit">
            <div>
                <h2 class="font-display text-base font-semibold tracking-tight text-neutral-900">Aktivitas Terbaru</h2>
                <p class="mt-1 text-sm text-neutral-500">Log terakhir dari panel admin.</p>
            </div>
            <div class="mt-4">
                <?php if (empty($recentLogs)): ?>
                    <div class="empty-state py-10">
                        <span class="empty-state-icon">
                            <span class="material-symbols-outlined text-[20px]">fact_check</span>
                        </span>
                        <h3 class="empty-state-title mt-3">Belum ada aktivitas</h3>
                        <p class="empty-state-copy mt-1">Aksi admin akan tampil di sini.</p>
                    </div>
                <?php else: ?>
                    <ul class="divide-y divide-neutral-100">
                        <?php foreach ($recentLogs as $log): ?>
                            <li class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-neutral-300"></span>
                                <div class="min-w-0">
                                    <p class="text-sm leading-6 text-neutral-900">
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
        const canvas = document.getElementById('dashboardRevenueChart');
        if (!canvas) {
            return;
        }

        const labels30 = <?= json_encode($chartLabels30) ?>;
        const data30 = <?= json_encode($chartData30) ?>;

        const ctx = canvas.getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels30.slice(-7),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: data30.slice(-7),
                    borderColor: '#2563EB',
                    backgroundColor: 'rgba(37, 99, 235, 0.05)',
                    fill: true,
                    tension: 0.28,
                    pointRadius: 2,
                    pointHoverRadius: 3,
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

        const rangeButtons = document.querySelectorAll('.chart-range-btn');
        function setActiveRange(range) {
            rangeButtons.forEach(function (btn) {
                const active = btn.dataset.range === String(range);
                btn.classList.toggle('bg-primary', active);
                btn.classList.toggle('text-white', active);
                btn.classList.toggle('text-neutral-600', !active);
            });
        }
        rangeButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const range = parseInt(btn.dataset.range, 10);
                chart.data.labels = labels30.slice(-range);
                chart.data.datasets[0].data = data30.slice(-range);
                chart.update();
                setActiveRange(range);
            });
        });
        setActiveRange(7);
    });
</script>
<?php endif; ?>
<?= $this->endSection() ?>
