<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h1 class="text-xl font-semibold text-neutral-900">Laporan Penjualan</h1>
        <p class="mt-1 text-sm text-neutral-500">Ringkasan pendapatan dan transaksi harian.</p>
    </div>
    <a href="<?= base_url('admin/laporan/export/csv/' . date('Y') . '/' . date('m')) ?>" class="btn btn-secondary shrink-0">
        <span class="material-symbols-outlined text-[16px]">file_download</span>
        Export CSV Bulan Ini
    </a>
</div>

<div class="grid gap-4 md:grid-cols-2">
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
</div>

<div class="panel-surface p-6">
    <h2 class="text-base font-semibold text-neutral-900">Grafik 7 Hari Terakhir</h2>
    <canvas id="salesChart" height="110" class="mt-4"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
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
<?= $this->endSection() ?>