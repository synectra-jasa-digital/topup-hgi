# Desain Halaman Katalog Produk Ayong Store

Dokumen ini mencatat desain halaman katalog (`/`) apa adanya di kode. Isinya: token visual, tata letak, komponen, interaksi, animasi, aksesibilitas, dan temuan yang perlu diperbaiki. Pakai dokumen ini sebagai acuan saat mengubah atau menambah tampilan publik.

Dianalisis dari branch `claude/compassionate-johnson-i0v6ph`, 29 September 2026.

## 1. Sumber File

| File | Isi |
| --- | --- |
| `app/Controllers/Home.php` | Menyiapkan data katalog: kategori, produk per kategori, banner, katalog bongkar, metode pencairan, kanal pembayaran |
| `app/Views/catalog/index.php` | Kerangka halaman, merangkai semua partial |
| `app/Views/layouts/public.php` | Header, info berjalan, footer, navigasi bawah mobile, meta SEO |
| `app/Views/catalog/partials/hero_tabs.php` | Carousel banner, kartu identitas game, tombol Beli/Jual, CSS dan JS hero |
| `app/Views/catalog/partials/section_motion.php` | CSS animasi bersama dan toast |
| `app/Views/catalog/partials/buy_mode.php` | Mode Beli: 4 langkah pemesanan dan ringkasan |
| `app/Views/catalog/partials/sell_mode.php` | Mode Jual: form bongkar dan ringkasan |
| `app/Views/catalog/partials/shared_sections.php` | Panduan 4 langkah, fakta toko, FAQ |
| `app/Views/catalog/partials/overlays.php` | Dialog konfirmasi, dialog cara cek ID, bar bayar mobile, form checkout tersembunyi |
| `app/Views/catalog/partials/interactive.php` | Semua logika JS halaman |
| `resources/css/public.css` | CSS sumber publik (pola latar, ticker, badge langkah, footer, halaman pesanan) |
| `tailwind.public.config.js` | Token Tailwind untuk halaman publik |
| `stitch/` | Mockup dan referensi desain awal (Google Stitch) |

Build CSS: `npm run build:css`. Hasilnya `public/assets/css/public.css`. Tailwind versi `^3.4.13`.

## 2. Karakter Desain

- **Tema terang** dengan aksen biru untuk membeli dan amber untuk menjual.
- **Satu halaman, dua mode.** Beli dan Jual berbagi hero, panduan, dan FAQ. Tombol di kartu identitas mengganti mode tanpa pindah halaman.
- **Alur bertahap.** Pembelian dipecah menjadi 4 kartu bernomor. Ringkasan pesanan di kanan selalu terlihat di desktop.
- **Jujur pada sistem.** Teks hanya menjanjikan yang benar-benar terjadi: bayar manual, admin verifikasi, invoice privat.
- **Gerak halus dan bisa dimatikan.** Semua animasi berada di dalam `prefers-reduced-motion: no-preference`.

## 3. Token Visual

### 3.1 Warna

Halaman memakai palet bawaan Tailwind (`slate`, `blue`, `amber`, `emerald`, `rose`) jauh lebih banyak daripada token kustom di config. Tabel ini mencatat peran tiap warna.

| Peran | Kelas / nilai | Hex | Dipakai di |
| --- | --- | --- | --- |
| Latar halaman | `.light-felt-pattern` | `#f1f5f9` + titik `rgba(148,163,184,.15)` tiap 20px | `body` |
| Permukaan kartu | `bg-white` | `#ffffff` | Semua kartu, header, footer |
| Permukaan sekunder | `bg-slate-50`, `bg-slate-100` | `#f8fafc`, `#f1f5f9` | Input, blok ringkasan, pill kategori |
| Teks utama | `text-slate-900`, `text-slate-950` | `#0f172a`, `#020617` | Judul, nama produk |
| Teks isi | `text-slate-700`, `text-slate-800` | `#334155`, `#1e293b` | Paragraf, label |
| Teks pendukung | `text-slate-600` | `#475569` | Deskripsi, hint, label `dt` |
| Garis | `border-slate-200`, `border-slate-100`, `border-slate-300` | `#e2e8f0`, `#f1f5f9`, `#cbd5e1` | Kartu, pemisah, input |
| Aksen Beli | `blue-600`, `blue-700` | `#2563eb`, `#1d4ed8` | Harga, badge langkah, tombol bayar, fokus |
| Gradien Beli | `from-blue-600 via-blue-700 to-indigo-700` | `#2563eb` ke `#4338ca` | Tombol Bayar Sekarang, kepala ringkasan |
| Aksen Jual | `amber-500`, `amber-700` | `#f59e0b`, `#b45309` | Kartu bongkar terpilih, rate, tombol kirim |
| Gradien Jual | `from-amber-500 via-amber-400 to-yellow-500` | `#f59e0b` ke `#eab308` | Kepala ringkasan bongkar, tombol kirim |
| Sukses / WhatsApp | `emerald-700` | `#047857` | Tombol Chat CS, badge langkah selesai, ikon centang |
| Error | `rose-50`, `rose-300`, `rose-600`, `rose-900` | `#fff1f2`, `#fda4af`, `#e11d48`, `#881337` | Kotak error, tanda wajib `*`, toast error |
| Hero gelap | `#000000`, `slate-800`, `slate-900` | `#000000`, `#1e293b`, `#0f172a` | Kartu identitas, ticker |
| Seleksi teks | `selection:bg-blue-100 selection:text-blue-900` | `#dbeafe`, `#1e3a8a` | `body` |

Token kustom di `tailwind.public.config.js`: `primary` (`#2563eb`, dark `#1d4ed8`, light `#dbeafe`), `neutral` 50 sampai 950, `success` `#10b981`, `danger` `#ef4444`, `surface` `#ffffff`. Halaman katalog hampir tidak memakainya. Halaman kategori memakainya.

### 3.2 Tipografi

Font dimuat dari Google Fonts secara async (`rel=preload` lalu `stylesheet`), dengan `display=swap`.

| Keluarga | Kelas | Bobot dimuat | Peran |
| --- | --- | --- | --- |
| Inter | `font-sans` | 400, 500, 600, 700, 800 | Teks isi, label, input |
| Plus Jakarta Sans | `font-display` | 600, 700, 800, 900 | Judul, harga, tombol, angka total |
| Monospace sistem | `font-mono` | bawaan | ID game, nomor WA, voucher, subtotal |
| Material Symbols Outlined | `.material-symbols-outlined` | 400, FILL 0 | Semua ikon |

Skala yang dipakai:

| Elemen | Kelas | Ukuran |
| --- | --- | --- |
| Judul panduan | `text-3xl sm:text-4xl font-extrabold leading-[1.1]` | 30px ke 36px |
| Angka langkah panduan | `text-4xl sm:text-5xl font-black tabular-nums` | 36px ke 48px |
| Total tagihan | `text-3xl sm:text-4xl font-black tabular-nums` | 30px ke 36px |
| Judul hero | `text-lg sm:text-xl md:text-2xl font-black` | 18px ke 24px |
| Judul FAQ | `text-2xl font-extrabold` | 24px |
| Judul langkah panduan | `text-xl font-bold` | 20px |
| Judul kartu langkah | `text-base sm:text-lg font-bold` | 16px ke 18px |
| Nama produk | `text-sm sm:text-base font-black` | 14px ke 16px |
| Harga produk | `text-sm sm:text-base font-black text-blue-700` | 14px ke 16px |
| Isi panduan dan FAQ | `text-base leading-relaxed` | 16px |
| Deskripsi kartu, hint, ringkasan | `text-xs` | 12px |
| Input | `text-base sm:text-sm` | 16px di mobile agar iOS tidak zoom, 14px di desktop |

### 3.3 Spasi dan Grid

- Lebar maksimum konten: `max-w-[1360px]`, di tengah dengan `mx-auto`.
- Gutter samping: `px-4` (16px), `sm:px-6` (24px).
- Jarak antar blok utama: `space-y-6` (24px). Kolom kiri: `space-y-4` (16px).
- Padding kartu langkah: `p-4 sm:p-5` (16px ke 20px).
- Grid produk: `grid-cols-2 sm:grid-cols-3 gap-3`.
- Grid pembayaran: `grid-cols-2 sm:grid-cols-3 gap-2.5`.
- Grid kartu bongkar: `grid-cols-2 sm:grid-cols-4 gap-2.5`.
- Header: tinggi `h-14` (56px) di mobile, `md:h-16` (64px).
- Navigasi bawah mobile: tinggi tetap `58px`. Bar bayar mobile diletakkan tepat di atasnya (`bottom-[58px]`).

### 3.4 Radius

| Radius | Kelas | Dipakai di |
| --- | --- | --- |
| 24px | `rounded-3xl` | Panel dialog |
| 16px | `rounded-2xl` | Kartu langkah, banner, kartu identitas, ringkasan, kartu bongkar |
| 12px | `rounded-xl` | Kartu produk, pill kategori, input, tombol besar, badge langkah |
| 8px | `rounded-lg` | Tombol header, ikon kecil, tombol pencairan |
| Penuh | `rounded-full` | Panah carousel, titik indikator, tanda centang |

### 3.5 Bayangan

- `shadow-md` pada ringkasan pesanan, tombol bayar, dan kartu produk saat hover.
- `shadow-lg` pada banner dan toast. `shadow-2xl` pada dialog.
- Bayangan berwarna di CSS kustom: badge langkah `0 4px 10px rgba(37,99,235,.35)`, pill aktif `0 4px 14px rgba(37,99,235,.35)`, banner tengah `0 20px 40px -10px rgba(15,23,42,.4)`.

### 3.6 Ikon

Material Symbols Outlined, ukuran lewat `text-[16px]` sampai `text-[28px]`. Semua ikon dekoratif memakai `aria-hidden="true"`.

Ikon fallback kategori (bila admin belum mengunggah ikon):

| Slug | Ikon | Warna |
| --- | --- | --- |
| `koin-emas` | `monetization_on` | `text-amber-600` |
| `kartu-emas` | `credit_card` | `text-amber-700` |
| `koin-md` | `diamond` | `text-sky-700` |
| `kartu-ungu` | `workspace_premium` | `text-purple-700` |
| lainnya | `sports_esports` | `text-slate-600` |

## 4. Tata Letak Halaman

Urutan dari atas ke bawah:

1. Tautan "Lewati ke konten" (muncul saat fokus keyboard).
2. Header sticky.
3. Info berjalan (ticker), hanya bila ada pengumuman aktif.
4. Hero: carousel banner, lalu kartu identitas game dengan tombol Beli/Jual.
5. Area katalog `#katalog-section`: mode Beli atau mode Jual.
6. Panduan dan FAQ.
7. Footer.
8. Navigasi bawah (mobile), bar bayar (mobile, mode Beli), dialog, toast.

### 4.1 Desktop (lg, 1024px ke atas)

```text
+--------------------------------------------------------------+
| Logo   Beli Koin  Jual Chip  Cek Pesanan      [Chat CS WA]   |  header sticky 64px
+--------------------------------------------------------------+
| info 1 · info 2 · info 3 ...  (berjalan)                     |  ticker 40px
+--------------------------------------------------------------+
|        [kiri redup]  [ BANNER TENGAH ]  [kanan redup]        |  coverflow 360px
|                        - o o                                 |  indikator
| +----------------------------------------------------------+ |
| | [badge] Higgs Domino Island / Global    [Beli] [Jual]    | |  kartu identitas hitam
| +----------------------------------------------------------+ |
+--------------------------------------------------------------+
| KOLOM KIRI 7/12 (xl 8/12)          | KOLOM KANAN 5/12 (xl 4/12)|
| [1] Pilih Kategori  [cari...]      | +----------------------+ |
|     (pill) (pill) (pill)           | | Ringkasan Pesanan    | |  sticky top-24
| [2] Pilih Nominal                  | | produk, ID, WA, bayar| |
|     [kartu][kartu][kartu]          | | voucher              | |
| [3] Data Akun & WhatsApp           | | Total Rp...          | |
|     [ID game]   [WhatsApp]         | | [Bayar Sekarang]     | |
| [4] Metode Pembayaran              | +----------------------+ |
|     [bank][bank][QRIS]             | | Alur pesanan         | |
+--------------------------------------------------------------+
| Cara top up (sticky 4/12)  | 01 Pilih nominal (8/12)         |
| fakta 1                    | 02 Isi ID game & WhatsApp       |
| fakta 2                    | 03 Bayar dan unggah bukti       |
| fakta 3                    | 04 Admin memproses              |
|                            | FAQ (accordion)                 |
+--------------------------------------------------------------+
| Footer: pernyataan (5/12) | Menu / Pembayaran / Bantuan (7/12)|
+--------------------------------------------------------------+
```

### 4.2 Mobile (di bawah 768px)

```text
+----------------------------+
| Logo           [Chat CS]   |  header 56px
| ticker                     |
| [   banner 180px   ]       |
| [kartu identitas]          |
| [Beli / Top Up][Bongkar]   |  tombol mode lebar penuh
| [1] kategori + cari        |
| [2] kartu produk 2 kolom   |
| [3] ID game, lalu WA       |
| [4] pembayaran 2 kolom     |
| Ringkasan Pesanan          |  pindah ke bawah, tidak sticky
| Alur pesanan               |
| Judul panduan              |
| 01..04 + FAQ               |
| fakta toko                 |  urutan diatur ulang dengan order-*
| footer                     |
+----------------------------+
| Total Rp...  [Bayar]       |  bar sticky, bottom 58px
| Beli | Jual | Cek Pesanan  |  navigasi bawah 58px
+----------------------------+
```

Breakpoint yang dipakai: `sm` 640px, `md` 768px, `lg` 1024px, `xl` 1280px. Body diberi `pb-28` di mobile agar konten tidak tertutup dua bar bawah.

## 5. Komponen

### 5.1 Header

- Putih solid, `border-b border-slate-200`, `sticky top-0 z-50`.
- Logo dari `store_logo`. Bila kosong, nama toko dalam `font-display text-lg font-extrabold`.
- Menu desktop: tiga tautan `text-sm font-semibold text-slate-600`. Tautan aktif memakai `aria-current="page"`, teks `slate-950`, dan garis bawah biru 2px yang menempel di garis header.
- Tombol "Chat CS WhatsApp": `bg-emerald-700`, tinggi 44px, teks pendek "Chat CS" di mobile. Hanya tampil bila nomor CS diisi.

### 5.2 Info Berjalan (Ticker)

- `bg-slate-900 text-slate-100`, tinggi 40px, `text-sm font-medium`.
- Isi dari tabel `announcements`. Daftar ditulis dua kali agar putaran `-50%` tidak melompat. Salinan kedua `aria-hidden`.
- Durasi mengikuti panjang teks: `max(30, jumlah karakter x 0.13)` detik.
- Berhenti saat hover. Dengan reduced motion, animasi mati dan baris bisa digeser manual.

### 5.3 Carousel Banner (Coverflow)

- Tinggi area: 180px, `sm` 260px, `md` 320px, `lg` 360px.
- Tiga posisi: tengah (skala 1, opasitas 1), kiri dan kanan (skala 0.85, geser 58%, opasitas 0.5, `brightness(.65)`), tersembunyi (skala 0.6, opasitas 0).
- Lebar slide: 85%, `sm` 75%, `md` 65%, `lg` 58%. Rasio 16:9, tinggi maksimum 320px.
- Bila banner kurang dari 3, banner pertama diulang sebagai klon `aria-hidden`.
- Tombol panah: lingkaran 44px `bg-slate-900/80`, muncul saat hover atau fokus, selalu tampil di layar sentuh.
- Indikator: titik 8x6px, titik aktif melebar ke 24px dan terisi biru sebagai hitung mundur 4,5 detik.
- Autoplay 4500ms. Berhenti saat hover, fokus, sentuh, tab tersembunyi, atau reduced motion.
- Geser (swipe) lebih dari 40px mengganti slide. Klik slide samping membawanya ke tengah.
- Gambar WebP dengan `srcset`. Banner pertama `fetchpriority="high"` dan di-preload di `<head>`.

### 5.4 Kartu Identitas Game

- Latar hitam `#000000`, `border-slate-800`, `rounded-2xl`, padding 20px ke 24px.
- Badge game 56x56px: bingkai gradien amber ke kuning, isi SVG dua kartu domino.
- Judul "Higgs Domino Island / Global" muncul kata per kata.
- Deskripsi berganti mengikuti mode (`data-hero-copy="buy"` / `"sell"`).
- Efek sorot mengikuti kursor (radial biru 280px), hanya untuk perangkat dengan hover.

### 5.5 Tombol Mode Beli / Jual

- Wadah `bg-slate-900 rounded-xl p-1.5 shadow-inner`.
- Dua tombol dengan `aria-pressed`. Beli aktif: `bg-blue-600 text-white`. Jual aktif: `bg-amber-500 text-neutral-950`.
- Satu indikator (`#hero-mode-indicator`) meluncur di antara tombol dan berganti warna. Posisi dihitung JS dari `getBoundingClientRect`.
- Mode juga dibaca dari hash URL: `#jual` atau `#bongkar` membuka mode Jual.

### 5.6 Kartu Langkah (Step Card)

- `bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5`.
- Kepala: badge nomor 32x32px bergradien biru, judul, deskripsi `text-xs`, garis bawah `border-slate-100`.
- Badge berubah menjadi centang hijau `#047857` saat langkah selesai. Pembaca layar mendengar kata "selesai".
- Aturan selesai: langkah 1 bila ada kategori aktif, 2 bila produk dipilih, 3 bila ID dan WA terisi, 4 bila kanal bayar dipilih.

### 5.7 Pill Kategori

- `min-h-11 rounded-xl px-4 py-2 text-xs sm:text-sm font-bold`, `flex-wrap gap-2`.
- Normal: `bg-slate-100/90 text-slate-700 border-slate-200`.
- Aktif (`.category-pill.active`): gradien biru, teks putih, ikon kuning `#fde047`, bayangan biru.
- Ikon: gambar unggahan admin 18px, atau ikon fallback (tabel 3.6).

### 5.8 Pencarian Nominal

- Input `type="search"` di kepala langkah 1, lebar penuh di mobile, `sm:w-60`.
- Placeholder "Cari nominal (1B, 200M...)". Menyaring kartu produk di kategori aktif.
- Bila tidak ada hasil, tampil kotak kosong bergaris putus-putus dengan `role="status"`.

### 5.9 Kartu Produk

- Elemen `<button>` dengan `aria-pressed`, `min-h-[110px]`, `p-3.5 sm:p-4`, `rounded-xl`.
- Isi: nama (`font-display font-black`), nominal (`text-xs`), ikon kategori 32px di kanan atas, garis tipis, harga biru, tanda centang.
- Hover: garis biru, `shadow-md`, naik 2px, ikon membesar 110%.
- Terpilih (`.product-card-selected`): garis `#2563eb`, latar `#eff6ff`, cincin biru 2px, naik 2px, centang biru tampil.
- Harga diformat `Rp` + pemisah titik (`number_format(..., 0, ',', '.')`).

### 5.10 Input Form

Satu gaya dasar (`$inputBase`) untuk semua input mode Beli:

- `min-h-11 rounded-xl border-slate-300 bg-slate-50 py-2.5 pr-3`.
- Ikon di kiri (`pl-9`), 18px, `text-slate-500`. Ikon WA berwarna `emerald-700`, ikon voucher `amber-700`.
- Fokus: `border-blue-600 bg-white ring-2 ring-blue-200`.
- ID game, WA, dan voucher memakai `font-mono font-bold`. Voucher otomatis huruf besar.
- Setiap input punya `label`, hint `text-xs`, dan `aria-describedby`.
- Mode Jual memakai gaya sama dengan fokus amber (`$sellInput`).

### 5.11 Kartu Metode Pembayaran

- Pola radio: `role="radiogroup"`, tiap kartu `role="radio"` dengan `aria-checked`.
- `min-h-[64px] p-3 pr-8 rounded-xl`. Nama kanal tebal, baris kedua nomor rekening atau "Scan QRIS".
- Terpilih: `border-blue-600 bg-blue-50/70 ring-2 ring-blue-500/20` dan centang biru di pojok kanan atas.
- Kanal pertama terpilih otomatis.

### 5.12 Ringkasan Pesanan

- Kartu `border-2 border-slate-200 shadow-md`, sticky `lg:top-24`.
- Kepala gradien biru ke indigo, ikon `receipt` di kotak putih.
- Isi: pratinjau produk, daftar ID/WA/metode, input voucher, rincian harga, garis putus-putus, total besar, tombol Bayar Sekarang.
- Nilai yang berubah menyala kuning sebentar (`.value-flash`), dan angka total berhitung naik.
- Biaya layanan selalu `Rp0` hijau. Total ditulis "Sebelum potongan voucher".

### 5.13 Tombol Utama

| Tombol | Gaya |
| --- | --- |
| Bayar Sekarang | `min-h-12 rounded-xl`, gradien biru ke indigo, `font-display font-black text-base`, ikon gembok kuning, panah bergeser saat hover |
| Kirim Pengajuan Bongkar | Sama, gradien amber, teks `neutral-950`, ikon `send` |
| Buat Pesanan (dialog) | `bg-blue-600 rounded-xl min-h-12`, `disabled:cursor-wait disabled:opacity-70` |
| Batal (dialog) | Garis `slate-300`, teks `slate-700` |
| Chat CS | `bg-emerald-700`, hover `emerald-800` |

Semua tombol punya `active:scale-[0.98]` atau `active:scale-95` sebagai umpan balik tekan.

### 5.14 Mode Jual (Bongkar)

- Satu kartu form di kiri, ringkasan amber di kanan.
- Langkah memakai `fieldset` dan `legend` bernomor "1.", "2.", "3.".
- Kartu jenis bongkar: `min-h-[76px] rounded-2xl`, rate `font-mono text-amber-700` dengan satuan, misalnya "Rp5.000 / kartu".
- Jumlah: stepper minus/plus 44px dengan input angka 1 sampai 1000.
- Metode pencairan: tombol pill radio.
- Ringkasan menghitung "Estimasi Dana Diterima" = jumlah x rate patokan.
- Keadaan kosong: kartu tengah dengan ikon `currency_exchange` dan tombol WA.

### 5.15 Panduan dan Fakta

- Grid 12 kolom di desktop: kiri 4 kolom (sticky), kanan 8 kolom.
- Gaya editorial: tiap baris diberi garis tipis di atas (`.ruled`), bukan kartu.
- Langkah 01 sampai 04 dengan angka besar biru dan paragraf `text-base`.
- Fakta: Biaya Layanan Rp0, Invoice Privat, Cek Status Pesanan, Bantuan via WhatsApp. Fakta bertautan punya panah yang bergeser saat hover.
- Di mobile, pembungkus kiri memakai `contents` agar urutan menjadi judul, langkah, FAQ, lalu fakta.

### 5.16 FAQ

- `<details>` bawaan HTML, item pertama terbuka.
- Ringkasan `min-h-14 font-display text-base sm:text-lg font-bold`. Ikon `add` berputar 45 derajat saat terbuka.
- Jawaban `max-w-prose text-base leading-relaxed`. Jawaban pembayaran dibuat dari kanal aktif.

### 5.17 Footer

- Putih, `border-t`, `mt-16`. Kiri: logo dan kalimat pernyataan `text-2xl sm:text-3xl font-extrabold`.
- Kanan: daftar `dl` bergaya lembar spesifikasi (Menu, Pembayaran, Bantuan), kolom label 9rem.
- Tautan bergaris bawah yang tergambar saat hover. Garis antar baris dan blok muncul mengikuti scroll (`animation-timeline: view()`), dengan fallback statis.

### 5.18 Navigasi Bawah Mobile

- `fixed bottom-0 h-[58px] bg-white border-t`, `z-[999]`, disembunyikan dari `md`.
- Tiga item: Beli Koin (`payments`), Jual Chip (`sell`), Cek Pesanan (`receipt_long`). Ikon 22px, label `text-xs`.
- Item aktif biru dengan garis 2px di atas.

### 5.19 Bar Bayar Mobile

- `fixed bottom-[58px]`, putih, menampilkan Total Tagihan dan tombol Bayar Sekarang.
- Hanya di mode Beli. Disembunyikan saat mode Jual.

### 5.20 Dialog

- Latar `bg-slate-950/70 backdrop-blur-sm`, panel `max-w-md rounded-3xl p-6 shadow-2xl`.
- `role="dialog" aria-modal="true"`, fokus dipindah ke panel, Escape menutup, scroll halaman dikunci (`html.modal-open`), fokus kembali ke pemicu.
- Dialog Konfirmasi Pesanan: item, ID, metode, voucher (bila ada), total, catatan gembok, tombol Batal dan Buat Pesanan.
- Dialog Cara Cek User ID: 3 langkah bernomor, tombol "Saya Mengerti, Isi ID Sekarang" yang memfokuskan input ID.

### 5.21 Toast

- Pengganti `alert()`. Mobile: `fixed inset-x-4 bottom-[140px]` (di atas dua bar bawah). Desktop: kanan bawah, lebar 26rem.
- `role="alert"`, ikon dan warna mengikuti nada (error rose, sukses hijau), tombol tutup 44px, Escape menutup.
- Validasi gagal: toast tampil, field diberi `aria-invalid="true"` lalu difokuskan. Atribut dihapus begitu user mengetik.

### 5.22 Kotak Error Server

Bila checkout gagal di server, halaman kembali dengan kotak `rose` berjudul "Pesanan belum dibuat", daftar alasan, dan petunjuk mencoba lagi.

## 6. Alur Interaksi Mode Beli

1. Halaman terbuka. Kategori pertama aktif, kanal bayar pertama terpilih.
2. User memilih kategori. Grup produk lain disembunyikan, kartu baru masuk berurutan.
3. User memilih produk. Kartu ditandai, ringkasan dan total diperbarui.
4. User mengisi ID game dan WA. Ringkasan ikut berubah.
5. User menekan Bayar Sekarang. JS memeriksa: produk, ID, format WA `08` atau `628`, kanal bayar.
6. Dialog konfirmasi terbuka. User menekan Buat Pesanan.
7. Tombol terkunci, form tersembunyi dikirim `POST /checkout/{id}`.
8. Server mengarahkan ke invoice privat, atau kembali ke katalog dengan kotak error.

Mode Jual mengirim pengajuan lewat `fetch` dengan header CSRF, lalu menampilkan toast sukses dan bisa membuka WhatsApp.

## 7. Animasi

Semua animasi di bawah hanya aktif bila pengguna tidak meminta pengurangan gerak.

| Nama | Durasi | Easing | Pemicu |
| --- | --- | --- | --- |
| `hero-rise` | 650ms, jeda 0 sampai 760ms | `cubic-bezier(.2,.8,.2,1)` | Muat halaman, hero |
| `word-up` | 700ms, jeda +70ms per kata | `cubic-bezier(.2,.8,.2,1)` | Judul hero |
| Transisi coverflow | 600ms | `cubic-bezier(.25,1,.5,1)` | Ganti slide |
| `dot-progress` | 4500ms | linear | Hitung mundur slide |
| `avatar-sweep` | 5s, berulang | ease-in-out | Kilau badge game |
| `badge-pop` | 560ms | `cubic-bezier(.3,1.5,.5,1)` | Ganti mode |
| Indikator mode | 380ms | `cubic-bezier(.3,1.2,.4,1)` | Ganti mode |
| `view-in` | 380ms | `cubic-bezier(.2,.8,.2,1)` | Tampilan Beli/Jual muncul |
| Reveal `[data-reveal]` | 650ms, jeda `--rd` | `cubic-bezier(.2,.8,.2,1)` | Masuk viewport (IntersectionObserver) |
| `card-in` | 420ms, +35ms per kartu | `cubic-bezier(.2,.8,.2,1)` | Ganti kategori |
| `value-flash` | 1000ms | ease-out | Nilai ringkasan berubah |
| `pop-in` | 420ms | `cubic-bezier(.3,1.6,.5,1)` | Centang dan badge langkah |
| Garis `.ruled` | 900ms | `cubic-bezier(.2,.8,.2,1)` | Baris panduan masuk viewport |
| `faq-in` | 340ms | `cubic-bezier(.2,.8,.2,1)` | FAQ dibuka |
| `dialog-in` | 340ms | `cubic-bezier(.2,.8,.2,1)` | Dialog dibuka |
| `shake` | 420ms | `cubic-bezier(.36,.07,.19,.97)` | Pesan perlu perhatian |
| `toast-in` | 320ms | `cubic-bezier(.2,.8,.2,1)` | Toast muncul |
| `marquee` | 30s atau lebih | linear | Ticker |
| Parallax banner | 260ms | ease-out | Gerak kursor dan scroll (md ke atas) |

## 8. Aksesibilitas

Yang sudah baik:

- Target sentuh minimal 44px (`min-h-11`, `h-11 w-11`) di hampir semua kontrol.
- Cincin fokus terlihat (`focus-visible:outline-2`), biru untuk Beli dan amber untuk Jual.
- Tautan "Lewati ke konten" di awal halaman.
- Pola ARIA tepat: `aria-pressed` untuk pill dan kartu produk, `radiogroup`/`radio` untuk pembayaran dan bongkar, `aria-current` untuk navigasi, `aria-roledescription="carousel"` untuk banner.
- Status langkah selesai diumumkan lewat teks `sr-only`.
- Klon banner dan salinan ticker disembunyikan dari pembaca layar.
- `prefers-reduced-motion` dihormati di semua animasi dan autoplay.
- Input mobile 16px mencegah zoom otomatis di iOS.
- Semua isi dinamis dari admin di-escape dengan `esc()`.

## 9. Konten dan Copywriting

- Bahasa Indonesia, kalimat pendek, sapaan "Anda".
- Harga ditulis `Rp150.000` tanpa spasi.
- Judul langkah berupa kata kerja: "Pilih Kategori Produk", "Pilih Nominal Top Up".
- Keadaan kosong selalu memberi jalan keluar: kembali nanti atau tanya CS.
- Penanda wajib `*` merah, penanda opsional "(opsional)" abu-abu.

## 10. Halaman Kategori (`/kategori/{slug}`)

Halaman ini memakai gaya berbeda dari katalog:

- Hero biru solid `bg-primary rounded-xl` dengan ikon `sports_esports` 220px transparan di pojok.
- Judul `font-sans text-3xl sm:text-4xl font-bold`, bukan `font-display`.
- Grid produk `grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5`. Kartu berupa tautan langsung ke `/checkout/{id}`, `min-h-[120px]`.
- Tombol kembali "Katalog" di kanan atas.

## 11. Temuan dan Rekomendasi

Diurutkan dari dampak terbesar.

### 11.1 Kelas CSS yang tidak menghasilkan style

Tailwind 3.4 tidak punya `shadow-xs` dan `shadow-2xs` (keduanya baru ada di Tailwind 4). Keduanya tidak ada di `public/assets/css/public.css`. Akibatnya kartu langkah, pill, kartu bayar, dan kartu bongkar tampil tanpa bayangan yang dimaksud.

Halaman kategori memakai `bg-surface-white` dan `text-on-surface`. Kedua token ini hanya ada di `stitch/direct_gaming_trust/DESIGN.md`, tidak di config. Keduanya juga tidak ter-build.

**Perbaikan:** tambahkan `boxShadow: { '2xs': ..., xs: ... }` dan warna `surface-white`, `on-surface` ke `tailwind.public.config.js`, atau ganti ke kelas yang ada (`shadow-sm`, `bg-white`, `text-slate-900`).

### 11.2 Halaman kategori tanpa kontainer

`category.php` tidak memakai `max-w-[1360px] mx-auto px-4 sm:px-6`. Layout juga tidak menambah padding. Konten menempel ke tepi layar, tidak sejajar dengan header. Gayanya juga berbeda dari katalog (radius, font judul, warna token). **Perbaikan:** bungkus dengan kontainer yang sama dan samakan komponen kartu produk.

### 11.3 Teks yang tidak sesuai sistem

- FAQ, fakta "Cek Status Pesanan", dan footer meminta "nomor invoice dan token akses" untuk Cek Pesanan. Padahal `cek-pesanan` sekarang hanya butuh nomor invoice.
- Meta description halaman menjanjikan "Proses 1 detik otomatis 24 jam". Pembayaran sebenarnya manual dan diverifikasi admin. Janji ini bertentangan dengan isi halaman dan berisiko memicu komplain.

**Perbaikan:** sesuaikan ketiga teks Cek Pesanan dan ganti meta description dengan klaim yang benar.

### 11.4 Proteksi klik ganda belum tersambung

Form tersembunyi di `overlays.php` tidak punya input `idempotency_token`, padahal controller sudah membuat token di session. Tombol memang dikunci setelah klik, tetapi klik ulang setelah muat ulang halaman tetap bisa membuat dua pesanan.

### 11.5 Warna dan token tidak konsisten

- Ada dua config Tailwind dengan nilai berbeda: `primary.dark` `#1d4ed8` (publik) vs `#1E3A8A` (admin), `success` `#10b981` vs `#16A34A`.
- View katalog memakai kelas palet bawaan (`blue-600`, `slate-900`), bukan token `primary`/`neutral`. Mengganti warna merek jadi harus mencari di banyak file.
- Kartu identitas memaksa latar hitam lewat `style="background-color: #000000 !important"`.

**Perbaikan:** tetapkan satu sumber token (misalnya `primary`, `accent-sell`, `surface`, `ink`), pakai di semua view, dan pindahkan warna hero ke kelas.

### 11.6 Teks kecil di area penting

Ringkasan pesanan, hint, dan deskripsi langkah memakai `text-xs` (12px). Untuk data yang harus dicek sebelum bayar (ID, WA, metode), 14px lebih aman dibaca di HP.

### 11.7 CSS dan JS tersebar di view

Sekitar 340 baris CSS ada di tag `<style>` dalam `hero_tabs.php` dan `section_motion.php`. Sekitar 700 baris JS ada di `interactive.php`. CSS ini tidak ikut minify dan tidak di-cache terpisah. **Perbaikan:** pindahkan CSS ke `resources/css/public.css` dan JS ke `public/assets/js/catalog.js`.

### 11.8 Instansiasi model berulang di view

`StoreSettingModel` dibuat ulang di `public.php`, `buy_mode.php`, `sell_mode.php`, dan `shared_sections.php`. `PaymentChannelModel` dan `AnnouncementModel` juga dipanggil dari layout. Satu halaman menjalankan beberapa query yang sama. **Perbaikan:** kirim data ini dari controller atau pakai view cell dengan cache.

### 11.9 Kontras kecil yang perlu dicek

- Teks `text-slate-500` pada placeholder di atas `bg-slate-50` mendekati batas 4.5:1.
- Ikon pill aktif kuning `#fde047` di atas biru hanya dekoratif, jadi aman, tetapi jangan dipakai untuk teks.

## 12. Aturan Saat Menambah Komponen Baru

1. Pakai kontainer `max-w-[1360px] mx-auto px-4 sm:px-6`.
2. Kartu: `bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5`.
3. Judul: `font-display font-bold`. Isi: `font-sans`.
4. Aksen biru untuk alur beli, amber untuk alur jual, emerald untuk WhatsApp dan sukses, rose untuk error.
5. Kontrol minimal 44px dan punya `focus-visible:outline`.
6. Animasi baru wajib di dalam `@media (prefers-reduced-motion: no-preference)`.
7. Ikon dekoratif wajib `aria-hidden="true"`.
8. Teks hanya menjanjikan yang benar-benar dilakukan sistem.
9. Setelah menambah kelas, jalankan `npm run build:css` dan pastikan kelasnya muncul di `public/assets/css/public.css`.
