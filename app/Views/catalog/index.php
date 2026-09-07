<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<?= $this->include('catalog/partials/hero_tabs') ?>

<main class="max-w-[1360px] mx-auto px-4 sm:px-6 py-6 space-y-6" id="katalog-section">
    <?= $this->include('catalog/partials/buy_mode') ?>
    <?= $this->include('catalog/partials/sell_mode') ?>
    <?= $this->include('catalog/partials/shared_sections') ?>
</main>

<?= $this->include('catalog/partials/overlays') ?>
<?= $this->include('catalog/partials/interactive') ?>
<?= $this->endSection() ?>
