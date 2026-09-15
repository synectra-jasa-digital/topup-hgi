<!-- Checkout Confirmation Dialog (Modern Modal) -->
<div class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" id="checkout-modal">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative text-slate-900 transition-all">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
          <span class="material-symbols-outlined text-[20px]">verified</span>
        </div>
        <h3 class="font-display font-black text-lg text-slate-900">Konfirmasi Pesanan</h3>
      </div>
      <button class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer" id="btn-close-modal" type="button" aria-label="Tutup">
        <span class="material-symbols-outlined text-[18px]">close</span>
      </button>
    </div>

    <div class="mt-4 bg-slate-50 rounded-2xl p-4 space-y-2.5 text-xs border border-slate-200">
      <div class="flex justify-between items-center">
        <span class="text-slate-500">Item Produk:</span>
        <span class="font-bold text-slate-900" id="modal-item">Belum memilih produk</span>
      </div>
      <div class="flex justify-between items-center">
        <span class="text-slate-500">User ID Game:</span>
        <span class="font-mono font-bold text-slate-900" id="modal-id">-</span>
      </div>
      <div class="flex justify-between items-center">
        <span class="text-slate-500">Metode Bayar:</span>
        <span class="font-bold text-blue-700" id="modal-method">-</span>
      </div>
      <div class="flex justify-between items-baseline pt-2.5 border-t border-slate-200 text-sm font-bold">
        <span class="text-slate-700">Total Pembayaran:</span>
        <span class="text-blue-700 font-display font-black text-xl" id="modal-total">Rp0</span>
      </div>
    </div>

    <div class="mt-4 flex items-center gap-2.5 text-[11px] text-slate-600 bg-blue-50/70 p-3 rounded-xl border border-blue-200/70">
      <span class="material-symbols-outlined text-[20px] text-blue-600 shrink-0">lock</span>
      <span>Detail rekening atau QRIS akan tampil di invoice. Upload bukti pembayaran setelah transfer agar pesanan dapat diverifikasi.</span>
    </div>

    <div class="mt-5 flex gap-3">
      <button class="flex-1 py-3 rounded-xl border border-slate-300 font-bold text-xs text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer" id="btn-cancel-checkout" type="button">Batal</button>
      <button class="flex-1 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 font-display font-black text-xs text-white transition-all flex items-center justify-center gap-1.5 shadow-md shadow-blue-600/20 cursor-pointer" id="btn-submit-pay" type="button">
        <span>Buat Pesanan</span>
        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
      </button>
    </div>
  </div>
</div>

<!-- Petunjuk User ID Game Modal -->
<div class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm" id="guide-modal">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative text-slate-900">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
          <span class="material-symbols-outlined text-[20px]">help</span>
        </div>
        <h3 class="font-display font-black text-lg text-slate-900">Cara Cek User ID Game</h3>
      </div>
      <button class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center transition-colors cursor-pointer" id="btn-close-guide-modal" type="button" aria-label="Tutup">
        <span class="material-symbols-outlined text-[18px]">close</span>
      </button>
    </div>

    <div class="mt-4 space-y-3 text-xs text-slate-600">
      <div class="flex gap-3 items-start p-3 bg-slate-50 rounded-xl border border-slate-100">
        <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-[11px] shrink-0">1</span>
        <div>
          <strong class="text-slate-900 block mb-0.5">Buka Aplikasi Game</strong>
          <span>Masuk ke lobi utama Higgs Domino Island atau Higgs Games.</span>
        </div>
      </div>
      <div class="flex gap-3 items-start p-3 bg-slate-50 rounded-xl border border-slate-100">
        <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-[11px] shrink-0">2</span>
        <div>
          <strong class="text-slate-900 block mb-0.5">Klik Foto Profil / Avatar</strong>
          <span>Ketuk foto profil avatar akun Anda di pojok kiri atas lobi permainan.</span>
        </div>
      </div>
      <div class="flex gap-3 items-start p-3 bg-slate-50 rounded-xl border border-slate-100">
        <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-[11px] shrink-0">3</span>
        <div>
          <strong class="text-slate-900 block mb-0.5">Salin 8–10 Digit ID</strong>
          <span>User ID tertera di samping atau bawah nama profil Anda (contoh: <code class="bg-slate-200 px-1 py-0.5 rounded font-mono font-bold text-slate-900">123456789</code>).</span>
        </div>
      </div>
    </div>

    <div class="mt-5">
      <button class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 font-display font-black text-xs text-white transition-all cursor-pointer shadow-sm" id="btn-understand-guide" type="button">
        Saya Mengerti, Lanjutkan
      </button>
    </div>
  </div>
</div>

<!-- Mobile Floating Sticky Checkout Action Bar -->
<div class="mobile-only-sticky" style="position: fixed; bottom: 58px; left: 0; right: 0; z-index: 998; background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-top: 1px solid #e2e8f0; border-radius: 0px !important; padding: 10px 16px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 -2px 10px rgba(0,0,0,0.06);">
  <div>
    <span class="text-[10px] uppercase font-bold text-slate-500 block">Total Tagihan</span>
    <div class="font-display font-black text-blue-700 text-lg" id="mobile-bottom-total">Rp0</div>
  </div>
  <button class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-display font-black text-xs flex items-center gap-1.5 shadow-md border border-blue-600 cursor-pointer active:scale-95 transition-all" id="btn-mobile-checkout" type="button">
    <span class="material-symbols-outlined text-[16px]">lock</span>
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
