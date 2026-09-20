<?php
  $ovRing = 'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600';
?>
<!-- Checkout Confirmation Dialog. Opened and closed by interactive.php (focus, Escape, scroll lock). -->
<div class="dialog-backdrop fixed inset-0 z-[1100] hidden flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm" id="checkout-modal">
  <div class="dialog-panel relative w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 text-slate-900 shadow-2xl focus:outline-none" role="dialog" aria-modal="true" aria-labelledby="checkout-modal-title" tabindex="-1">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div class="flex items-center gap-2">
        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 font-bold text-blue-700">
          <span class="material-symbols-outlined text-[20px]" aria-hidden="true">verified</span>
        </div>
        <h2 id="checkout-modal-title" class="font-display text-lg font-black text-slate-900">Konfirmasi Pesanan</h2>
      </div>
      <button class="-mr-2 flex h-11 w-11 cursor-pointer items-center justify-center rounded-full text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 <?= $ovRing ?>" id="btn-close-modal" type="button" aria-label="Tutup">
        <span class="material-symbols-outlined text-[22px]" aria-hidden="true">close</span>
      </button>
    </div>

    <dl class="mt-4 space-y-2.5 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm">
      <div class="dialog-item flex items-center justify-between gap-3" style="--i: 0">
        <dt class="text-slate-600">Item Produk:</dt>
        <dd class="text-right font-bold text-slate-900" id="modal-item">Belum memilih produk</dd>
      </div>
      <div class="dialog-item flex items-center justify-between gap-3" style="--i: 1">
        <dt class="text-slate-600">User ID Game:</dt>
        <dd class="font-mono font-bold text-slate-900" id="modal-id">-</dd>
      </div>
      <div class="dialog-item flex items-center justify-between gap-3" style="--i: 2">
        <dt class="text-slate-600">Metode Bayar:</dt>
        <dd class="font-bold text-blue-700" id="modal-method">-</dd>
      </div>
      <div class="dialog-item hidden items-center justify-between gap-3" style="--i: 3" id="modal-voucher-row">
        <dt class="text-slate-600">Kode Voucher:</dt>
        <dd class="font-mono font-bold text-slate-900" id="modal-voucher">-</dd>
      </div>
      <div class="dialog-item flex items-baseline justify-between gap-3 border-t border-slate-200 pt-2.5 font-bold" style="--i: 4">
        <dt class="text-slate-700">Total Pembayaran:</dt>
        <dd class="font-display text-xl font-black tabular-nums text-blue-700" id="modal-total">Rp0</dd>
      </div>
    </dl>
    <p class="mt-2 hidden text-xs text-slate-600" id="modal-voucher-note">Kode voucher dicek saat pesanan dibuat. Bila berlaku, potongannya tampil di invoice.</p>

    <div class="mt-4 flex items-start gap-2.5 rounded-xl border border-blue-200/70 bg-blue-50/70 p-3 text-sm text-slate-700">
      <span class="material-symbols-outlined shrink-0 text-[20px] text-blue-700" aria-hidden="true">lock</span>
      <span>Detail rekening atau QRIS akan tampil di invoice. Unggah bukti pembayaran setelah transfer agar pesanan dapat diverifikasi.</span>
    </div>

    <div class="mt-5 flex gap-3">
      <button class="min-h-12 flex-1 cursor-pointer rounded-xl border border-slate-300 py-3 text-sm font-bold text-slate-700 transition-colors hover:bg-slate-100 active:scale-[0.98] <?= $ovRing ?>" id="btn-cancel-checkout" type="button">Batal</button>
      <button class="group flex min-h-12 flex-1 cursor-pointer items-center justify-center gap-1.5 rounded-xl bg-blue-600 py-3 font-display text-sm font-black text-white shadow-md transition-all hover:bg-blue-700 active:scale-[0.98] disabled:cursor-wait disabled:opacity-70 <?= $ovRing ?>" id="btn-submit-pay" type="button">
        <span>Buat Pesanan</span>
        <span class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-0.5" aria-hidden="true">arrow_forward</span>
      </button>
    </div>
  </div>
</div>

<!-- Petunjuk User ID Game Modal -->
<div class="dialog-backdrop fixed inset-0 z-[1100] hidden flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm" id="guide-modal">
  <div class="dialog-panel relative w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 text-slate-900 shadow-2xl focus:outline-none" role="dialog" aria-modal="true" aria-labelledby="guide-modal-title" tabindex="-1">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
      <div class="flex items-center gap-2">
        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 font-bold text-amber-700">
          <span class="material-symbols-outlined text-[20px]" aria-hidden="true">help</span>
        </div>
        <h2 id="guide-modal-title" class="font-display text-lg font-black text-slate-900">Cara Cek User ID Game</h2>
      </div>
      <button class="-mr-2 flex h-11 w-11 cursor-pointer items-center justify-center rounded-full text-slate-600 transition-colors hover:bg-slate-100 hover:text-slate-900 <?= $ovRing ?>" id="btn-close-guide-modal" type="button" aria-label="Tutup">
        <span class="material-symbols-outlined text-[22px]" aria-hidden="true">close</span>
      </button>
    </div>

    <ol class="mt-4 space-y-3 text-sm text-slate-700">
      <li class="dialog-item flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3" style="--i: 0">
        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white" aria-hidden="true">1</span>
        <div>
          <strong class="mb-0.5 block text-slate-900">Buka Aplikasi Game</strong>
          <span>Masuk ke lobi utama Higgs Domino Island atau Higgs Games.</span>
        </div>
      </li>
      <li class="dialog-item flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3" style="--i: 1">
        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white" aria-hidden="true">2</span>
        <div>
          <strong class="mb-0.5 block text-slate-900">Klik Foto Profil / Avatar</strong>
          <span>Ketuk foto profil avatar akun Anda di pojok kiri atas lobi permainan.</span>
        </div>
      </li>
      <li class="dialog-item flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3" style="--i: 2">
        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white" aria-hidden="true">3</span>
        <div>
          <strong class="mb-0.5 block text-slate-900">Salin User ID</strong>
          <span>User ID tertera di samping atau bawah nama profil Anda (contoh: <code class="rounded bg-slate-200 px-1 py-0.5 font-mono font-bold text-slate-900">123456789</code>).</span>
        </div>
      </li>
    </ol>

    <div class="mt-5">
      <button class="min-h-12 w-full cursor-pointer rounded-xl bg-blue-600 py-3 font-display text-sm font-black text-white shadow-sm transition-all hover:bg-blue-700 active:scale-[0.98] <?= $ovRing ?>" id="btn-understand-guide" type="button">
        Saya Mengerti, Isi ID Sekarang
      </button>
    </div>
  </div>
</div>

<!-- Mobile sticky checkout bar (buy mode only). Sits just above the 58px bottom nav and is hidden from md up. -->
<div class="mobile-only-sticky fixed inset-x-0 bottom-[58px] z-[998] flex items-center justify-between gap-3 border-t border-slate-200 bg-white px-4 py-2.5 md:hidden">
  <div>
    <span class="block text-xs font-semibold text-slate-600">Total Tagihan</span>
    <div class="font-display text-lg font-black tabular-nums text-blue-700" id="mobile-bottom-total">Rp0</div>
  </div>
  <button class="flex min-h-11 cursor-pointer items-center gap-1.5 rounded-xl border border-blue-600 bg-gradient-to-r from-blue-600 to-indigo-600 px-5 font-display text-sm font-black text-white shadow-md transition-all hover:from-blue-700 hover:to-indigo-700 active:scale-95 <?= $ovRing ?>" id="btn-mobile-checkout" type="button">
    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">lock</span>
    <span>Bayar Sekarang</span>
  </button>
</div>

<!-- Form tersembunyi untuk submit checkout backend -->
<form id="backend-checkout-form" method="POST" action="" class="hidden">
  <?= csrf_field() ?>
  <input type="hidden" name="game_id" id="hidden-game-id">
  <input type="hidden" name="whatsapp_number" id="hidden-whatsapp">
  <input type="hidden" name="voucher_code" id="hidden-voucher">
  <input type="hidden" name="payment_channel_id" id="hidden-payment-channel-id">
</form>
