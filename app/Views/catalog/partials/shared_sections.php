<?php
  $storeSettingModel = new \App\Models\StoreSettingModel();
  $storeContact = $storeSettingModel->getVal('store_contact');
  $waNum = ! empty($storeContact) ? preg_replace('/[^0-9]/', '', $storeContact) : '';
  $waUrl = ! empty($waNum) ? 'https://wa.me/' . $waNum : 'https://wa.me/';

  $faqs = [
      [
          'q' => 'Berapa lama proses pengiriman koin chip atau item game?',
          'a' => 'Pesanan Anda diproses secara instan dan otomatis oleh sistem gateway dalam 1–3 detik setelah pembayaran berhasil diverifikasi.',
      ],
      [
          'q' => 'Apakah aman dan apakah perlu memberikan password akun?',
          'a' => 'Sangat aman 100%. Anda hanya perlu memasukkan User ID akun game. Kami tidak pernah meminta password, kata sandi, atau data login pribadi Anda.',
      ],
      [
          'q' => 'Metode pembayaran apa saja yang tersedia?',
          'a' => 'Kami mendukung QRIS Bebas Biaya Admin (GoPay, DANA, OVO, ShopeePay, LinkAja, BCA Mobile, Livin Mandiri, BRImo, semua e-wallet & m-banking), serta Virtual Account Bank 24 Jam otomatis.',
      ],
      [
          'q' => 'Bagaimana cara melakukan Bongkar / Jual koin atau kartu?',
          'a' => 'Pilih tab "Bongkar / Jual" di bagian atas, pilih jenis kartu/koin, tentukan jumlah, dan masukkan nomor rekening/e-wallet pencairan Anda. Tim CS kami akan memverifikasi dan mentransfer dana langsung ke rekening Anda.',
      ],
      [
          'q' => 'Bagaimana jika pesanan terkendala atau koin belum masuk?',
          'a' => 'Apabila terdapat kendala jaringan atau bank maintenance, silakan klik tombol "Bantuan CS WhatsApp". Tim Customer Service kami aktif 24 jam nonstop siap membantu memeriksa invoice Anda.',
      ],
  ];
?>

<!-- 3 LANGKAH MUDAH TOP UP SECTION -->
<section class="mt-8">
  <div class="bg-slate-900 rounded-2xl border border-slate-800 p-5 sm:p-7 shadow-md text-white">
    <div class="text-center max-w-xl mx-auto mb-6">
      <span class="px-3 py-1 rounded-full bg-blue-500/20 text-blue-400 text-[11px] font-extrabold uppercase tracking-wider border border-blue-500/30">Panduan Transaksi</span>
      <h3 class="font-display font-black text-lg sm:text-xl text-white mt-2">Cara Mudah Top Up di Ayong Store</h3>
      <p class="text-xs text-slate-300 mt-1">Cukup 3 langkah sederhana, pesanan koin Anda langsung terkirim otomatis</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-slate-800 rounded-xl p-4 border border-slate-700/80 flex items-start shadow-xs" style="gap: 16px;">
        <div class="w-9 h-9 rounded-xl bg-blue-600 text-white font-display font-black flex items-center justify-center text-sm shrink-0 shadow-sm">1</div>
        <div>
          <h4 class="font-display font-bold text-sm text-white">Pilih Nominal Produk</h4>
          <p class="text-xs text-slate-300 mt-1 leading-relaxed">Tentukan paket koin emas atau kartu yang sesuai kebutuhan bermain Anda.</p>
        </div>
      </div>

      <div class="bg-slate-800 rounded-xl p-4 border border-slate-700/80 flex items-start shadow-xs" style="gap: 16px;">
        <div class="w-9 h-9 rounded-xl bg-blue-600 text-white font-display font-black flex items-center justify-center text-sm shrink-0 shadow-sm">2</div>
        <div>
          <h4 class="font-display font-bold text-sm text-white">Masukkan User ID</h4>
          <p class="text-xs text-slate-300 mt-1 leading-relaxed">Ketik 8–10 digit User ID game Anda tanpa perlu memberikan password.</p>
        </div>
      </div>

      <div class="bg-slate-800 rounded-xl p-4 border border-slate-700/80 flex items-start shadow-xs" style="gap: 16px;">
        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white font-display font-black flex items-center justify-center text-sm shrink-0 shadow-sm">3</div>
        <div>
          <h4 class="font-display font-bold text-sm text-white">Bayar &amp; Koin Masuk</h4>
          <p class="text-xs text-slate-300 mt-1 leading-relaxed">Pilih QRIS/VA, lakukan pembayaran, dan koin langsung terkirim instan.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TRUST & STATS BADGE BAR -->
<section class="mt-4">
  <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center gap-3 hover:-translate-y-0.5 hover:shadow-md transition-all">
      <div class="w-11 h-11 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-glow-gold">
        <span class="material-symbols-outlined text-[22px]">bolt</span>
      </div>
      <div>
        <div class="font-display font-black text-sm sm:text-base text-slate-900 leading-tight">Proses 1 Detik</div>
        <div class="text-[11px] text-slate-500 font-semibold mt-0.5">Koin Langsung Masuk</div>
      </div>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center gap-3 hover:-translate-y-0.5 hover:shadow-md transition-all">
      <div class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-glow-blue">
        <span class="material-symbols-outlined text-[22px]">verified_user</span>
      </div>
      <div>
        <div class="font-display font-black text-sm sm:text-base text-slate-900 leading-tight">100% Legal &amp; Aman</div>
        <div class="text-[11px] text-slate-500 font-semibold mt-0.5">Garansi Bebas Banned</div>
      </div>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center gap-3 hover:-translate-y-0.5 hover:shadow-md transition-all">
      <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-glow-emerald">
        <span class="material-symbols-outlined text-[22px]">savings</span>
      </div>
      <div>
        <div class="font-display font-black text-sm sm:text-base text-slate-900 leading-tight">Harga Termurah</div>
        <div class="text-[11px] text-slate-500 font-semibold mt-0.5">Rp0 Biaya Admin</div>
      </div>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs flex items-center gap-3 hover:-translate-y-0.5 hover:shadow-md transition-all">
      <div class="w-11 h-11 rounded-xl bg-purple-600 text-white flex items-center justify-center shrink-0">
        <span class="material-symbols-outlined text-[22px]">support_agent</span>
      </div>
      <div>
        <div class="font-display font-black text-sm sm:text-base text-slate-900 leading-tight">Garansi Selesai</div>
        <div class="text-[11px] text-slate-500 font-semibold mt-0.5">CS WA Siaga 24 Jam</div>
      </div>
    </div>
  </div>
</section>

<!-- UNIVERSAL FAQ ACCORDION SECTION -->
<section class="mt-6 mb-8">
  <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-5 border-b border-slate-100">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-200">
          <span class="material-symbols-outlined text-[20px]">help_outline</span>
        </div>
        <div>
          <h3 class="font-display font-bold text-base text-slate-900 leading-tight">Pertanyaan Sering Diajukan (FAQ)</h3>
          <p class="text-xs text-slate-500 mt-0.5">Informasi penting seputar transaksi top up dan bongkar koin</p>
        </div>
      </div>
      <a class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs transition-all border border-emerald-200 shrink-0 self-start sm:self-auto" href="<?= esc($waUrl) ?>" rel="noopener noreferrer" target="_blank">
        <span class="material-symbols-outlined text-[16px]">chat</span>
        <span>Tanya CS WhatsApp</span>
      </a>
    </div>

    <div class="space-y-2.5" id="faq-accordion">
      <?php foreach ($faqs as $index => $faq): ?>
        <details class="group bg-slate-50/70 rounded-xl border border-slate-200/80 transition-all [&_summary::-webkit-details-marker]:hidden" <?= $index === 0 ? 'open' : '' ?>>
          <summary class="flex items-center justify-between p-3.5 text-xs sm:text-sm font-bold text-slate-800 cursor-pointer select-none group-open:text-blue-700 group-open:bg-blue-50/50 rounded-xl transition-colors">
            <span class="flex items-center gap-2.5">
              <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 text-[11px] font-bold flex items-center justify-center shrink-0"><?= $index + 1 ?></span>
              <span><?= esc($faq['q']) ?></span>
            </span>
            <span class="material-symbols-outlined text-[20px] text-slate-400 transition-transform duration-200 group-open:rotate-180 group-open:text-blue-600 shrink-0">expand_more</span>
          </summary>
          <div class="px-4 pb-4 pt-2 text-xs text-slate-600 leading-relaxed border-t border-slate-200/60 mt-1 pl-11">
            <?= esc($faq['a']) ?>
          </div>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
