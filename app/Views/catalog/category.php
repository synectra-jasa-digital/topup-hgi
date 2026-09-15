<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="w-full space-y-6">
    <section class="relative isolate overflow-hidden rounded-xl border border-primary/20 bg-primary text-white shadow-sm" aria-labelledby="category-hero-title">
        <div class="absolute -right-12 -top-16 -z-10 text-white/10">
            <span class="material-symbols-outlined text-[220px]">sports_esports</span>
        </div>
        <div class="p-6 sm:p-8 lg:p-10">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold">
                <span class="h-2 w-2 rounded-full bg-success"></span>
                Kategori aktif
            </span>
            <div class="mt-4 flex items-center gap-3">
                <?php if (! empty($category['icon'])): ?>
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 p-2">
                        <img src="<?= base_url($category['icon']) ?>" alt="" class="h-full w-full object-contain">
                    </span>
                <?php endif; ?>
                <h1 id="category-hero-title" class="text-balance font-sans text-3xl font-bold leading-tight sm:text-4xl">
                    <?= esc($category['name']) ?>
                </h1>
            </div>
            <p class="mt-4 max-w-2xl text-pretty text-sm leading-6 text-blue-100 sm:text-base">
                Pilih nominal top up yang tersedia untuk kategori ini, lalu lanjutkan ke checkout aman tanpa memasukkan kata sandi.
            </p>
        </div>
    </section>

    <section class="w-full rounded-xl border border-neutral-200 bg-surface-white p-4 shadow-sm sm:p-6" aria-labelledby="nominal-heading">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-primary">Nominal tersedia</p>
                <h2 id="nominal-heading" class="mt-1 text-xl font-bold text-on-surface">Pilih produk</h2>
            </div>
            <a href="<?= base_url('/') ?>" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-primary/30 bg-primary-light px-4 py-2 text-sm font-semibold text-primary transition-colors hover:bg-primary hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Katalog
            </a>
        </div>

        <?php if (empty($products)): ?>
            <div class="mt-6 rounded-xl border border-neutral-200 bg-surface-white p-10 text-center text-neutral-500">
                <span class="material-symbols-outlined mb-2 text-[56px] text-neutral-300">inventory_2</span>
                <p>Belum ada nominal produk yang tersedia untuk kategori ini saat ini.</p>
            </div>
        <?php else: ?>
            <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                <?php foreach ($products as $product): ?>
                    <a href="<?= base_url('checkout/' . $product['id']) ?>" class="group flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-4 transition-all hover:border-blue-600 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 min-h-[120px]">
                        <div>
                            <h3 class="text-sm sm:text-base font-black leading-snug text-slate-900 group-hover:text-blue-600 transition-colors"><?= esc($product['name']) ?></h3>
                            <?php if (! empty($product['nominal'])): ?>
                                <p class="mt-0.5 text-xs text-slate-500 font-medium"><?= esc($product['nominal']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                            <span class="font-display text-sm sm:text-base font-black text-blue-600">Rp<?= number_format((float) $product['sell_price'], 0, ',', '.') ?></span>
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors"><span class="material-symbols-outlined text-[16px]">arrow_forward</span></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?= $this->endSection() ?>