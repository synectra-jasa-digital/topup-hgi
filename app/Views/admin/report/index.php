<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-neutral-900">Laporan Penjualan</h1>
    <a href="<?= base_url('admin/laporan/export/csv/' . date('Y') . '/' . date('m')) ?>" class="btn btn-secondary">
        Export CSV Bulan Ini
    </a>
</div>

<!-- Ringkasan Statistik -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="card p-4">
        <p class="text-xs text-neutral-500 font-semibold uppercase">Hari Ini</p>
        <p class="text-2xl font-bold text-primary">Rp <?= number_format($today['revenue'] ?? 0, 0, ',', '.') ?></p>
        <p class="text-sm text-neutral-600 mt-1"><?= $today['order_count'] ?? 0 ?> transaksi selesai</p>
    </div>
    <div class="card p-4">
        <p class="text-xs text-neutral-500 font-semibold uppercase">Kemarin</p>
        <p class="text-2xl font-bold text-neutral-700">Rp <?= number_format($yesterday['revenue'] ?? 0, 0, ',', '.') ?></p>
        <p class="text-sm text-neutral-600 mt-1"><?= $yesterday['order_count'] ?? 0 ?> transaksi selesai</p>
    </div>
</div>

<!-- Chart Visual -->
<div class="card p-6">
    <h2 class="text-lg font-bold text-neutral-900 mb-4">Grafik 7 Hari Terakhir</h2>
    <canvas id="salesChart" height="100"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    borderColor: '#6C63FF',
                    backgroundColor: 'rgba(108, 99, 255, 0.1)',
                    fill: true,
                    tension: 0.2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
