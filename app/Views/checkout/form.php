<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center gap-3">
        <a href="<?= base_url('/') ?>" class="w-9 h-9 rounded-xl bg-surface-white border border-neutral-200 text-neutral-600 hover:text-primary flex items-center justify-center transition-colors">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="text-xl font-bold text-on-surface font-sans">Formulir Pemesanan</h1>
            <p class="text-xs text-neutral-500 font-inter">Lengkapi ID Game & nomor WhatsApp untuk memproses top up Anda.</p>
        </div>
    </div>

    <?php $errors = session()->getFlashdata('errors') ?? []; ?>

    <!-- Selected Product Summary Card -->
    <div class="bg-gradient-to-r from-blue-900 to-primary text-white rounded-2xl p-5 shadow-sm relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="space-y-1">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[11px] font-semibold">
                    <span class="material-symbols-outlined text-[13px] text-amber-300">monetization_on</span> Produk Dipilih
                </span>
                <h2 class="text-lg font-extrabold text-white font-sans"><?= esc($product['name']) ?></h2>
                <p class="text-xs text-blue-100 font-inter"><?= esc($product['nominal']) ?></p>
            </div>
            <div class="text-right">
                <span class="text-xs text-blue-200 block">Total Harga</span>
                <span class="text-xl font-black text-amber-300 font-inter">Rp<?= number_format((float) $product['sell_price'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-surface-white border border-neutral-200 rounded-2xl p-6 shadow-sm space-y-6 font-inter">

        <form method="post" action="<?= base_url('checkout/' . $product['id']) ?>" class="space-y-5">
            <?= csrf_field() ?>

            <!-- Step 1: Account Info -->
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-primary text-white font-bold text-xs flex items-center justify-center">1</span>
                    <h3 class="font-bold text-sm text-on-surface font-sans">Data Akun Game</h3>
                </div>

                <div class="space-y-1.5">
                    <label for="game_id" class="text-xs font-semibold text-on-surface block">
                        User ID Game Higgs Island <span class="text-danger">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <input type="text" id="game_id" name="game_id" value="<?= esc(old('game_id')) ?>" 
                            class="w-full rounded-xl border p-3 pl-10 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all <?= isset($errors['game_id']) ? 'border-danger bg-danger/5' : 'border-neutral-200' ?>" 
                            placeholder="Contoh: 18475920" required>
                        <span class="material-symbols-outlined text-[20px] text-neutral-400 absolute left-3">badge</span>
                    </div>
                    <?php if (isset($errors['game_id'])): ?>
                        <p class="text-danger text-xs flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span> <?= esc($errors['game_id']) ?></p>
                    <?php else: ?>
                        <p class="text-xs text-neutral-500 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">info</span> Pastikan User ID Game sudah benar.</p>
                    <?php endif; ?>
                </div>

                <div class="space-y-1.5">
                    <label for="whatsapp_number" class="text-xs font-semibold text-on-surface block">
                        Nomor WhatsApp Notifikasi <span class="text-danger">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <input type="text" id="whatsapp_number" name="whatsapp_number" value="<?= esc(old('whatsapp_number')) ?>" 
                            class="w-full rounded-xl border p-3 pl-10 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all <?= isset($errors['whatsapp_number']) ? 'border-danger bg-danger/5' : 'border-neutral-200' ?>" 
                            placeholder="Contoh: 081234567890" required>
                        <span class="material-symbols-outlined text-[20px] text-neutral-400 absolute left-3">chat</span>
                    </div>
                    <?php if (isset($errors['whatsapp_number'])): ?>
                        <p class="text-danger text-xs flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span> <?= esc($errors['whatsapp_number']) ?></p>
                    <?php else: ?>
                        <p class="text-xs text-neutral-500 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">info</span> Bukti pembayaran dan kwitansi akan dikirim ke nomor WA ini.</p>
                    <?php endif; ?>
                </div>
            </div>

            <hr class="border-neutral-100">

            <!-- Step 2: Voucher Code -->
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-primary text-white font-bold text-xs flex items-center justify-center">2</span>
                    <h3 class="font-bold text-sm text-on-surface font-sans">Kode Voucher (Opsional)</h3>
                </div>

                <div class="relative flex items-center">
                    <input type="text" id="voucher_code" name="voucher_code" value="<?= esc(old('voucher_code')) ?>" 
                        class="w-full rounded-xl border p-3 pl-10 text-sm uppercase tracking-wider focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all <?= isset($errors['voucher_code']) ? 'border-danger' : 'border-neutral-200' ?>" 
                        placeholder="Contoh: DISKON50">
                    <span class="material-symbols-outlined text-[20px] text-neutral-400 absolute left-3">confirmation_number</span>
                </div>
                <?php if (isset($errors['voucher_code'])): ?>
                    <p class="text-danger text-xs flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">error</span> <?= esc($errors['voucher_code']) ?></p>
                <?php endif; ?>
            </div>

            <hr class="border-neutral-100">

            <!-- Action Submit -->
            <div class="space-y-3 pt-2">
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold text-base py-3.5 rounded-xl transition-all shadow-md flex items-center justify-center gap-2 active:scale-[0.99]">
                    <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                    <span>Lanjut ke Pembayaran Snap</span>
                </button>

                <div class="flex items-center justify-center gap-4 text-xs text-neutral-400 font-inter pt-1">
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-emerald-500 text-[14px]">lock</span> SSL Encrypted</span>
                    <span>•</span>
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-amber-500 text-[14px]">verified</span> Safe Checkout</span>
                </div>
            </div>

        </form>

    </div>

</div>

<?= $this->endSection() ?>

