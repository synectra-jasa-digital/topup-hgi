<?php
  $waNum = ! empty($adminWhatsapp) ? preg_replace('/[^0-9]/', '', $adminWhatsapp) : '';
  $waUrl = ! empty($waNum) ? 'https://wa.me/' . $waNum : 'https://wa.me/';

  $faqs = [
      [
          'q' => 'Berapa lama proses pengiriman koin chip atau item game?',
          'a' => 'Pesanan Anda diproses secara instan dan otomatis oleh sistem dalam 1–3 detik setelah pembayaran berhasil dikonfirmasi.',
      ],
      [
          'q' => 'Apakah aman dan apakah perlu memberikan password akun?',
          'a' => 'Sangat aman. Anda cukup memasukkan User ID akun game. Kami tidak pernah meminta password, kata sandi, atau data login pribadi Anda.',
      ],
      [
          'q' => 'Metode pembayaran apa saja yang tersedia?',
          'a' => 'Kami mendukung QRIS (bebas biaya admin untuk semua E-Wallet & M-Banking), GoPay, DANA, OVO, ShopeePay, LinkAja, serta Virtual Account BCA, Mandiri, BRI, BNI, dan Bank Transfer.',
      ],
      [
          'q' => 'Bagaimana cara melakukan Bongkar / Jual koin?',
          'a' => 'Pilih tab "Bongkar / Jual", tentukan nominal koin dan pilih rekening/E-Wallet tujuan pencairan. Kirimkan koin ke ID penampung resmi kami, dan dana akan ditransfer otomatis ke rekening Anda.',
      ],
      [
          'q' => 'Bagaimana jika pesanan terkendala atau koin belum masuk?',
          'a' => 'Apabila terdapat kendala transaksi, silakan klik tombol Customer Service WhatsApp di atas. Tim CS kami aktif 24/7 dan siap membantu memeriksa invoice Anda secara langsung.',
      ],
  ];
?>

<!-- UNIVERSAL FAQ SECTION -->
<section class="mt-10">
    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                    <span class="material-symbols-outlined text-[20px]">help_outline</span>
                </div>
                <div>
                    <h3 class="font-display font-bold text-base text-slate-900 leading-tight">Pertanyaan Sering Diajukan (FAQ)</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Informasi penting seputar transaksi top up dan bongkar koin</p>
                </div>
            </div>
            <a class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-xs transition-all border border-emerald-200 shrink-0 self-start sm:self-auto" href="<?= esc($waUrl) ?>" rel="noopener noreferrer" target="_blank">
                <span class="material-symbols-outlined text-[16px]">support_agent</span>
                <span>Tanya CS WhatsApp</span>
            </a>
        </div>

        <div class="space-y-3" id="faq-accordion">
            <?php foreach ($faqs as $index => $faq): ?>
                <details class="group bg-slate-50/80 rounded-xl border border-slate-200/80 transition-all [&_summary::-webkit-details-marker]:hidden" <?= $index === 0 ? 'open' : '' ?>>
                    <summary class="flex items-center justify-between p-4 text-xs sm:text-sm font-bold text-slate-800 cursor-pointer select-none group-open:text-blue-700">
                        <span class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 text-[11px] font-bold flex items-center justify-center shrink-0"><?= $index + 1 ?></span>
                            <span><?= esc($faq['q']) ?></span>
                        </span>
                        <span class="material-symbols-outlined text-[20px] text-slate-400 transition-transform duration-200 group-open:rotate-180 group-open:text-blue-600 shrink-0">expand_more</span>
                    </summary>
                    <div class="px-4 pb-4 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-200/40 mt-1 pl-11">
                        <?= esc($faq['a']) ?>
                    </div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>
