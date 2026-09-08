<!-- Checkout Confirmation Dialog (Clean Light Modal) -->
<div class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" id="checkout-modal">
<div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative animate-in fade-in zoom-in duration-200 text-neutral-900">
<div class="flex items-center justify-between pb-3 border-b border-slate-200">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-blue-600 text-[24px]">verified</span>
<h3 class="font-display font-bold text-lg text-neutral-900">Konfirmasi Pembayaran Top Up</h3>
</div>
<button class="text-slate-400 hover:text-neutral-700 p-1" id="btn-close-modal" type="button"><span class="material-symbols-outlined text-[20px]">close</span></button>
</div>
<div class="mt-4 bg-slate-50 rounded-xl p-4 space-y-2.5 text-xs border border-slate-200">
<div class="flex justify-between">
<span class="text-slate-500">Item Produk:</span>
<span class="font-bold text-neutral-900" id="modal-item">Belum memilih produk</span>
</div>
<div class="flex justify-between">
<span class="text-slate-500">User ID Higgs:</span>
<span class="font-mono font-bold text-neutral-900" id="modal-id">-</span>
</div>
<div class="flex justify-between">
<span class="text-slate-500">Metode Pembayaran:</span>
<span class="font-semibold text-blue-700" id="modal-method">-</span>
</div>
<div class="flex justify-between pt-2 border-t border-slate-200 text-sm font-bold">
<span class="">Total Tagihan Netto:</span>
<span class="text-blue-700 font-display font-black" id="modal-total">Rp0</span>
</div>
</div>
<div class="mt-4 flex items-center gap-2 text-[11px] text-slate-600 bg-blue-50 p-2.5 rounded-lg border border-blue-200">
<span class="material-symbols-outlined text-[18px] text-blue-600 shrink-0">info</span>
<span class="">Jendela instruksi pembayaran resmi akan dibuka. Koin chip otomatis dikirim ke akun Anda 1-3 detik setelah verifikasi.</span>
</div>
<div class="mt-5 flex gap-3">
<button class="flex-1 py-2.5 rounded-xl border border-slate-300 font-bold text-xs text-slate-700 hover:bg-slate-100 transition-colors" id="btn-cancel-checkout" type="button">Batal</button>
<button class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 font-display font-black text-xs text-white transition-all flex items-center justify-center gap-1 shadow-md shadow-blue-600/20" id="btn-submit-pay" type="button">
<span class="">Lanjut ke Pembayaran</span>
<span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</button>
</div>
</div>
</div>

<!-- Mobile Sticky Footer Bar (Light Mode) -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 p-3 px-4 flex items-center justify-between shadow-lg">
<div>
<span class="text-[10px] uppercase font-bold text-slate-500 block">Total Tagihan</span>
<div class="font-display font-black text-blue-700 text-lg" id="mobile-bottom-total">Rp0</div>
</div>
<button class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-display font-black text-xs flex items-center gap-1.5 shadow-md border border-blue-700" id="btn-mobile-checkout" type="button"><span class="material-symbols-outlined text-[16px]">lock</span><span class="">Bayar Sekarang</span></button>
</div>

<!-- Form tersembunyi untuk backend submit (bila ada productId) -->
<form id="backend-checkout-form" method="POST" action="" class="hidden">
    <?= csrf_field() ?>
    <input type="hidden" name="game_id" id="hidden-game-id">
    <input type="hidden" name="whatsapp_number" id="hidden-whatsapp">
    <input type="hidden" name="voucher_code" id="hidden-voucher">
</form>

