<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<?php
  $waUrl = whatsapp_url((new \App\Models\StoreSettingModel())->getVal('store_contact', (string) (getenv('wablas.adminPhone') ?: '')));

  $ring  = 'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600';
  $field = 'w-full min-h-12 rounded-xl border bg-slate-50 px-3.5 py-3 font-mono text-base font-bold uppercase tracking-wider text-slate-950 outline-none transition-all placeholder:font-sans placeholder:font-normal placeholder:normal-case placeholder:tracking-normal placeholder:text-slate-500 focus:border-blue-600 focus:bg-white focus:ring-2 focus:ring-blue-200 sm:text-sm';

  $errors     = (array) (session()->getFlashdata('errors') ?? []);
  $flashError = session()->getFlashdata('error');
  $fieldError = $errors['invoice_number'] ?? null;
?>

<div class="mx-auto max-w-md px-4 py-10 sm:px-6 sm:py-16">
    <div class="rise-stagger space-y-6">

        <div class="space-y-2 text-center">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 ring-1 ring-blue-100">
                <span class="material-symbols-outlined text-[26px]" aria-hidden="true">receipt_long</span>
            </span>
            <h1 class="font-display text-2xl font-bold text-slate-950">Cek Status Pesanan</h1>
            <p class="mx-auto max-w-xs text-sm leading-relaxed text-slate-600">Masukkan nomor invoice untuk melihat sampai mana pesanan Anda.</p>
        </div>

        <div class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <?php if ($flashError): ?>
                <div class="flex items-start gap-2.5 rounded-xl border border-rose-200 bg-rose-50 p-3.5 text-sm font-medium text-rose-900" role="alert">
                    <span class="material-symbols-outlined shrink-0 text-[20px] text-rose-700" aria-hidden="true">error</span>
                    <span><?= esc($flashError) ?></span>
                </div>
            <?php endif ?>

            <form action="<?= base_url('cek-pesanan') ?>" method="post" class="space-y-4" id="status-form" novalidate>
                <?= csrf_field() ?>

                <div class="space-y-1.5">
                    <label for="invoice_number" class="block text-sm font-bold text-slate-900">Nomor Invoice <span class="text-rose-700" aria-hidden="true">*</span></label>
                    <input type="text" name="invoice_number" id="invoice_number" value="<?= old('invoice_number') ?>"
                           class="<?= $field ?> <?= $fieldError ? 'border-rose-500' : 'border-slate-300' ?>"
                           placeholder="Contoh: INV20260906ABCDEF" required autocomplete="off" autocapitalize="characters" spellcheck="false"
                           aria-describedby="invoice-hint<?= $fieldError ? ' invoice-error' : '' ?>"<?= $fieldError ? ' aria-invalid="true"' : '' ?>>
                    <p id="invoice-hint" class="text-sm text-slate-600">Ada di bagian atas halaman invoice yang Anda terima setelah membuat pesanan.</p>
                    <p id="invoice-error" class="<?= $fieldError ? 'flex' : 'hidden' ?> items-center gap-1 text-sm font-medium text-rose-800" role="alert">
                        <span class="material-symbols-outlined text-[16px]" aria-hidden="true">error</span>
                        <span id="invoice-error-text"><?= $fieldError ? esc($fieldError) : 'Isi nomor invoice terlebih dahulu.' ?></span>
                    </p>
                </div>

                <button type="submit" id="status-submit" class="flex min-h-12 w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-blue-700 bg-blue-600 px-5 py-3 font-display text-sm font-bold text-white shadow-sm transition-all hover:bg-blue-700 active:scale-[0.99] disabled:cursor-wait disabled:opacity-70 <?= $ring ?>">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true" id="status-submit-icon">search</span>
                    <span id="status-submit-label">Cek Status Pesanan</span>
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white/70 px-5 py-1 text-sm text-slate-700">
            <details class="group">
                <summary class="flex min-h-11 cursor-pointer select-none items-center justify-between gap-3 py-2 font-semibold text-slate-900 [&::-webkit-details-marker]:hidden <?= $ring ?>">
                    <span>Nomor invoice hilang?</span>
                    <span class="material-symbols-outlined shrink-0 text-[22px] text-slate-600 transition-transform duration-300 group-open:rotate-180" aria-hidden="true">expand_more</span>
                </summary>
                <div class="space-y-2 pb-4 leading-relaxed">
                    <p>Nomor invoice diawali <code class="rounded bg-slate-200 px-1 py-0.5 font-mono text-slate-900">INV</code> dan tampil di halaman invoice yang terbuka setelah Anda membuat pesanan. Buka kembali tautan invoice dari riwayat browser Anda.</p>
                    <?php if ($waUrl !== ''): ?>
                        <p>Tautannya sudah hilang? <a href="<?= esc($waUrl) ?>" target="_blank" rel="noopener noreferrer" class="font-semibold text-blue-700 underline underline-offset-2 hover:text-blue-800 <?= $ring ?>">Hubungi CS lewat WhatsApp</a>.</p>
                    <?php endif; ?>
                </div>
            </details>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('status-form');
        var field = document.getElementById('invoice_number');
        var error = document.getElementById('invoice-error');
        var submit = document.getElementById('status-submit');
        var label = document.getElementById('status-submit-label');
        var icon = document.getElementById('status-submit-icon');

        // A pasted number often carries a space or line break.
        function tidy() { field.value = field.value.replace(/\s+/g, '').toUpperCase(); }
        field.addEventListener('paste', function () { setTimeout(tidy, 0); });
        field.addEventListener('blur', tidy);
        field.addEventListener('input', function () { field.removeAttribute('aria-invalid'); });

        form.addEventListener('submit', function (e) {
            tidy();
            if (field.value === '') {
                e.preventDefault();
                error.classList.remove('hidden');
                error.classList.add('flex');
                field.setAttribute('aria-invalid', 'true');
                field.focus();
                return;
            }
            submit.disabled = true;
            label.textContent = 'Memeriksa...';
            icon.textContent = 'progress_activity';
            icon.classList.add('animate-spin');
        });

        window.addEventListener('pageshow', function () { // back button restores the page from cache
            submit.disabled = false;
            label.textContent = 'Cek Status Pesanan';
            icon.textContent = 'search';
            icon.classList.remove('animate-spin');
        });
    });
</script>

<?= $this->endSection() ?>
