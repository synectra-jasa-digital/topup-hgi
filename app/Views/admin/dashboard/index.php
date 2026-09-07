<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<h1 class="text-2xl font-bold text-neutral-900">Dashboard</h1>
<p class="mt-2 text-neutral-500">Selamat datang, <?= esc(session('admin_name')) ?>.</p>
<?= $this->endSection() ?>
