<?php
  $storeSettingModel = new \App\Models\StoreSettingModel();
  $storeContact = $storeSettingModel->getVal('store_contact');
  $waUrl = whatsapp_url($storeContact);
  $checkUrl = base_url('cek-pesanan');

  $focusRing = 'focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600';
  $linkClass = 'font-semibold text-blue-700 underline underline-offset-2 hover:text-blue-800 ' . $focusRing;

  // Payment methods are whatever the admin has switched on, so the FAQ reads them from the same list as checkout.
  $channelNames = array_map(static fn (array $channel): string => (string) $channel['name'], (array) ($paymentChannels ?? []));
  $paymentAnswer = $channelNames !== []
      ? 'Pembayaran lewat transfer bank atau QRIS ke rekening toko. Saat ini tersedia: ' . esc(implode(', ', $channelNames)) . '. Setelah pesanan dibuat, unggah bukti pembayaran di halaman invoice.'
      : 'Metode pembayaran sedang belum tersedia. Silakan kembali lagi nanti.';

  // Answers are HTML built only from escaped values and links defined above.
  $faqs = [
      [
          'q' => 'Berapa lama pesanan saya diproses?',
          'a' => 'Setelah Anda membayar dan mengunggah bukti pembayaran, admin memeriksa buktinya, lalu pesanan diproses. Lamanya bergantung pada antrean admin. Pantau statusnya kapan saja di <a class="' . $linkClass . '" href="' . esc($checkUrl) . '">Cek Pesanan</a>.',
      ],
      [
          'q' => 'Apakah saya perlu memberikan password akun game?',
          'a' => 'Tidak. Checkout hanya meminta User ID game dan nomor WhatsApp. Jangan pernah membagikan password akun game Anda kepada siapa pun.',
      ],
      [
          'q' => 'Metode pembayaran apa saja yang tersedia?',
          'a' => $paymentAnswer,
      ],
      [
          'q' => 'Bagaimana cara Bongkar / Jual koin atau kartu?',
          'a' => 'Pilih tombol "Bongkar / Jual" di bagian atas, pilih jenis kartu atau koin, tentukan jumlah, lalu isi rekening atau e-wallet tujuan pencairan dan kirim pengajuan. Admin yang memproses pengajuan Anda.',
      ],
      [
          'q' => 'Bagaimana cara mengecek atau melacak pesanan saya?',
          'a' => 'Buka <a class="' . $linkClass . '" href="' . esc($checkUrl) . '">Cek Pesanan</a>, lalu isi nomor invoice dan token akses. Keduanya ada di halaman invoice yang muncul setelah pesanan dibuat, jadi simpan tautannya.' . ($waUrl !== '' ? ' Bila pesanan bermasalah, hubungi CS lewat WhatsApp.' : ''),
      ],
  ];

  $facts = [
      ['icon' => 'payments', 'title' => 'Biaya Layanan Rp0', 'note' => 'Bayar sebesar harga produk', 'href' => null],
      ['icon' => 'lock', 'title' => 'Invoice Privat', 'note' => 'Dibuka dengan token akses', 'href' => null],
      ['icon' => 'receipt_long', 'title' => 'Cek Status Pesanan', 'note' => 'Pakai nomor invoice dan token', 'href' => $checkUrl],
  ];
  if ($waUrl !== '') {
      $facts[] = ['icon' => 'support_agent', 'title' => 'Bantuan via WhatsApp', 'note' => 'Hubungi CS bila ada kendala', 'href' => $waUrl];
  }
?>

<!-- PANDUAN: editorial two-column guide. Left stays in view on desktop, right holds the steps and the FAQ. -->
<section class="mt-12 mb-10" aria-labelledby="guide-title">
  <div class="flex flex-col gap-10 lg:grid lg:grid-cols-12 lg:gap-x-14">

    <!-- Left: title + facts. On phones the wrapper dissolves so the order becomes title, steps and FAQ, facts. -->
    <div class="contents lg:col-span-4 lg:block lg:self-start lg:sticky lg:top-24">
      <div class="order-1" data-reveal>
        <h2 id="guide-title" class="font-display text-3xl font-extrabold leading-[1.1] tracking-tight text-slate-950 sm:text-4xl">Cara top up di Ayong Store</h2>
        <p class="mt-4 max-w-sm text-base leading-relaxed text-slate-600">Empat langkah dari memilih nominal sampai pesanan diproses. Yang perlu Anda siapkan hanya ID game dan nomor WhatsApp.</p>
      </div>

      <ul class="order-3 border-b border-slate-300 lg:mt-10" aria-label="Hal yang perlu Anda ketahui">
        <?php foreach ($facts as $i => $fact): ?>
          <li class="ruled" data-reveal style="--rd: <?= $i * 80 ?>ms">
            <?php if ($fact['href'] !== null): ?>
              <a class="group flex items-start gap-3.5 py-4 <?= $focusRing ?>" href="<?= esc($fact['href']) ?>"<?= str_starts_with($fact['href'], 'https://wa.me/') ? ' target="_blank" rel="noopener noreferrer"' : '' ?>>
            <?php else: ?>
              <div class="flex items-start gap-3.5 py-4">
            <?php endif; ?>
                <span class="material-symbols-outlined mt-0.5 text-[22px] text-blue-700" aria-hidden="true"><?= $fact['icon'] ?></span>
                <span class="min-w-0 flex-1">
                  <span class="block font-display text-base font-bold leading-snug text-slate-900 <?= $fact['href'] !== null ? 'group-hover:text-blue-700' : '' ?> transition-colors"><?= esc($fact['title']) ?></span>
                  <span class="block text-sm text-slate-600"><?= esc($fact['note']) ?></span>
                </span>
                <?php if ($fact['href'] !== null): ?>
                  <span class="material-symbols-outlined mt-0.5 text-[20px] text-slate-500 transition-all duration-300 group-hover:translate-x-1 group-hover:text-blue-700" aria-hidden="true">arrow_forward</span>
                <?php endif; ?>
            <?php if ($fact['href'] !== null): ?>
              </a>
            <?php else: ?>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Right: steps, then FAQ -->
    <div class="order-2 min-w-0 lg:col-span-8 lg:order-none">
      <ol>
        <li class="ruled grid grid-cols-[3.5rem_1fr] gap-x-4 py-7 sm:grid-cols-[5.5rem_1fr]" data-reveal style="--rd: 0ms">
          <span class="step-num font-display text-4xl font-black leading-none tabular-nums text-blue-700 sm:text-5xl" aria-hidden="true">01</span>
          <div>
            <h3 class="font-display text-xl font-bold text-slate-900">Pilih nominal produk</h3>
            <p class="mt-1.5 max-w-prose text-base leading-relaxed text-slate-600">Tentukan paket koin emas atau kartu yang Anda butuhkan.</p>
            <a class="mt-3 inline-flex min-h-11 items-center gap-1 text-sm <?= $linkClass ?>" href="#katalog-section">Mulai dari katalog <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_downward</span></a>
          </div>
        </li>

        <li class="ruled grid grid-cols-[3.5rem_1fr] gap-x-4 py-7 sm:grid-cols-[5.5rem_1fr]" data-reveal style="--rd: 120ms">
          <span class="step-num font-display text-4xl font-black leading-none tabular-nums text-blue-700 sm:text-5xl" aria-hidden="true">02</span>
          <div>
            <h3 class="font-display text-xl font-bold text-slate-900">Isi ID game &amp; WhatsApp</h3>
            <p class="mt-1.5 max-w-prose text-base leading-relaxed text-slate-600">Cukup dua data itu. Password akun game tidak pernah diminta.</p>
          </div>
        </li>

        <li class="ruled grid grid-cols-[3.5rem_1fr] gap-x-4 py-7 sm:grid-cols-[5.5rem_1fr]" data-reveal style="--rd: 240ms">
          <span class="step-num font-display text-4xl font-black leading-none tabular-nums text-blue-700 sm:text-5xl" aria-hidden="true">03</span>
          <div>
            <h3 class="font-display text-xl font-bold text-slate-900">Bayar dan unggah bukti</h3>
            <p class="mt-1.5 max-w-prose text-base leading-relaxed text-slate-600">Transfer atau scan QRIS, lalu unggah bukti pembayaran di halaman invoice.</p>
          </div>
        </li>

        <li class="ruled grid grid-cols-[3.5rem_1fr] gap-x-4 border-b border-slate-300 py-7 sm:grid-cols-[5.5rem_1fr]" data-reveal style="--rd: 360ms">
          <span class="step-num font-display text-4xl font-black leading-none tabular-nums text-blue-700 sm:text-5xl" aria-hidden="true">04</span>
          <div>
            <h3 class="font-display text-xl font-bold text-slate-900">Admin memproses</h3>
            <p class="mt-1.5 max-w-prose text-base leading-relaxed text-slate-600">Bukti diverifikasi, lalu pesanan diproses. Pantau statusnya di <a class="<?= $linkClass ?>" href="<?= esc($checkUrl) ?>">Cek Pesanan</a>.</p>
          </div>
        </li>
      </ol>

      <!-- FAQ -->
      <h3 id="faq-title" class="mt-16 font-display text-2xl font-extrabold tracking-tight text-slate-950" data-reveal>Pertanyaan yang sering muncul</h3>
      <div class="mt-6 border-b border-slate-300" id="faq-accordion">
        <?php foreach ($faqs as $index => $faq): ?>
          <details class="ruled group" data-reveal style="--rd: <?= $index * 70 ?>ms" <?= $index === 0 ? 'open' : '' ?>>
            <summary class="flex min-h-14 cursor-pointer select-none items-center justify-between gap-4 py-4 font-display text-base font-bold text-slate-900 transition-colors hover:text-blue-700 group-open:text-blue-700 sm:text-lg [&::-webkit-details-marker]:hidden <?= $focusRing ?>">
              <span><?= esc($faq['q']) ?></span>
              <span class="material-symbols-outlined shrink-0 text-[26px] text-slate-600 transition-transform duration-300 group-open:rotate-45 group-open:text-blue-700" aria-hidden="true">add</span>
            </summary>
            <div class="faq-body max-w-prose pb-5 pr-10 text-base leading-relaxed text-slate-700">
              <?= $faq['a'] ?>
            </div>
          </details>
        <?php endforeach; ?>
      </div>
    </div>

  </div>
</section>
