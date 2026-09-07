<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="max-w-md mx-auto space-y-6">

    <div class="text-center space-y-2">
        <div class="w-12 h-12 rounded-2xl bg-primary-light text-primary flex items-center justify-center font-bold mx-auto">
            <span class="material-symbols-outlined text-[28px]">search_check</span>
        </div>
        <h1 class="text-2xl font-bold text-on-surface font-sans">Lacak Pesanan</h1>
        <p class="text-xs text-neutral-500 font-inter">Masukkan nomor invoice Anda untuk memeriksa status transaksi.</p>
    </div>
    
    <div class="bg-surface-white border border-neutral-200 rounded-2xl p-6 shadow-sm font-inter">
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="bg-error-container text-on-error-container p-3 rounded-xl mb-4 text-xs font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span><?= esc(session()->getFlashdata('error')) ?></span>
            </div>
        <?php endif ?>

        <form action="<?= base_url('cek-pesanan') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>
            
            <div class="space-y-1.5">
                <label for="invoice_number" class="text-xs font-semibold text-on-surface block">Nomor Invoice Transactions</label>
                <div class="relative flex items-center">
                    <input type="text" name="invoice_number" id="invoice_number" value="<?= old('invoice_number') ?>" 
                        class="w-full rounded-xl border border-neutral-200 p-3 pl-10 text-sm font-mono uppercase tracking-wider focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all <?= session('errors.invoice_number') ? 'border-danger' : '' ?>" 
                        placeholder="Contoh: INV20260906ABCDEF" required>
                    <span class="material-symbols-outlined text-[20px] text-neutral-400 absolute left-3">receipt</span>
                </div>
                <?php if (session('errors.invoice_number')) : ?>
                    <p class="text-danger text-xs mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span> <?= esc(session('errors.invoice_number')) ?></p>
                <?php endif ?>
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold text-sm py-3 rounded-xl transition-all shadow-md flex items-center justify-center gap-2 active:scale-[0.99]">
                <span class="material-symbols-outlined text-[18px]">search</span>
                <span>Cek Status Transaksi</span>
            </button>
        </form>
    </div>

    <div class="bg-neutral-50 border border-neutral-200 rounded-xl p-4 text-center text-xs text-neutral-500 font-inter space-y-1">
        <p class="font-semibold text-neutral-700">Lupa Nomor Invoice?</p>
        <p>Nomor invoice juga telah kami kirimkan ke WhatsApp yang Anda daftarkan saat checkout.</p>
    </div>

</div>

<?= $this->endSection() ?>

