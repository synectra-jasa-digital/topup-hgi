<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php $chartLabels = $chartLabels ?? []; $chartData = $chartData ?? []; ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="section-title">Laporan Penjualan</h1>
        <p class="section-subtitle">Ringkasan pendapatan dan transaksi harian.</p>
    </div>
    <a href="<?= base_url('admin/laporan/export/csv/' . date('Y') . '/' . date('m')) ?>" class="btn btn-secondary shrink-0">
        <span class="material-symbols-outlined text-[16px]">file_download</span>
        Export CSV Bulan Ini
    </a>
</div>

<div class="grid gap-4 md:grid-cols-2">
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
</div>

<div class="panel-surface">
    <h2 class="font-display text-base font-semibold tracking-tight text-neutral-900">Grafik 7 Hari Terakhir</h2>
    <p class="mt-1 text-sm text-neutral-500">Tren pendapatan harian.</p>
    <canvas id="salesChart" height="110" class="mt-4"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('salesChart');
        if (!canvas) {
            return;
        }

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chartLabels) ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?= json_encode($chartData) ?>,
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
    });
</script>
<?= $this->endSection() ?>
