# Design Guideline - Ayong Store

Versi Dokumen: 1.0
Tanggal: 6 September 2026

Panduan ini dipakai untuk menjaga tampilan website Ayong Store tetap konsisten di semua halaman, mulai dari halaman depan, detail produk, checkout, cek pesanan, sampai panel admin. Semua komponen di dokumen ini bersifat reusable, artinya satu komponen yang sama dipakai ulang di halaman berbeda, bukan dibuat versi baru untuk tiap halaman.

## 1. Prinsip Desain

Tampilan harus bersih dan tidak ramai, supaya customer fokus ke proses pembelian. Setiap halaman punya satu tujuan utama yang jelas, contoh halaman produk fokus ke pemilihan nominal, halaman checkout fokus ke pembayaran. Elemen kepercayaan seperti logo pembayaran resmi dan status transaksi selalu ditampilkan jelas, karena customer bertransaksi tanpa akun.

## 2. Warna (Color Token)

| Token | Hex | Penggunaan |
|---|---|---|
| Primary | #2563EB | Tombol utama, link aktif, elemen penting |
| Primary Dark | #1E3A8A | Header, footer, hover pada tombol utama |
| Primary Light | #DBEAFE | Background elemen aktif/terpilih |
| Success | #16A34A | Status berhasil/selesai |
| Warning | #F59E0B | Status menunggu/diproses |
| Danger | #DC2626 | Status gagal/dibatalkan |
| Neutral 900 | #1F2937 | Warna teks utama |
| Neutral 500 | #6B7280 | Teks sekunder, keterangan |
| Neutral 200 | #E5E7EB | Garis pembatas, border |
| Neutral 50 | #F9FAFB | Background halaman |
| White | #FFFFFF | Background kartu dan form |

Satu warna aksen dipakai konsisten untuk semua tombol utama dan elemen penting. Warna status (success, warning, danger) hanya dipakai untuk badge status pesanan, tidak dipakai untuk elemen dekoratif lain, supaya artinya tetap jelas bagi customer.

## 3. Tipografi

Font memakai Plus Jakarta Sans atau Inter dari Google Fonts, dipilih karena mudah dibaca di layar kecil.

| Elemen | Ukuran / Line Height | Bobot |
|---|---|---|
| H1 | 32px / 40px | Bold |
| H2 | 24px / 32px | Bold |
| H3 | 20px / 28px | Semibold |
| H4 | 16px / 24px | Semibold |
| Body | 16px / 24px | Regular |
| Body Small | 14px / 20px | Regular |
| Caption | 12px / 16px | Regular |

H1 dipakai maksimal satu kali per halaman, untuk judul utama halaman. H2 dipakai untuk judul bagian/section. Body dipakai untuk seluruh teks paragraf dan label form.

## 4. Spacing dan Padding

Semua jarak di layout mengikuti grid kelipatan 4 pixel, supaya rapi dan konsisten.

| Token | Nilai | Contoh Pemakaian |
|---|---|---|
| space-1 | 4px | Jarak antar ikon dan teks kecil |
| space-2 | 8px | Jarak antar elemen dalam satu komponen kecil |
| space-3 | 12px | Padding dalam tombol ukuran kecil |
| space-4 | 16px | Padding dalam card, padding form input |
| space-6 | 24px | Jarak antar card dalam satu grid |
| space-8 | 32px | Jarak antar section di dalam satu halaman |
| space-12 | 48px | Jarak besar antar section utama |
| space-16 | 64px | Padding atas/bawah halaman di layar desktop |

Aturan pemakaian: padding dalam card produk pakai `space-4` di semua sisi. Jarak antar card dalam grid produk pakai `space-6`. Jarak antar section besar (contoh dari section produk ke section testimoni) pakai `space-8` di mobile dan `space-12` di desktop. Padding dalam tombol pakai `space-3` vertikal dan `space-4` horizontal untuk ukuran medium.

## 5. Border Radius dan Shadow

| Token | Nilai | Penggunaan |
|---|---|---|
| radius-sm | 6px | Badge, input kecil |
| radius-md | 10px | Tombol, form input |
| radius-lg | 16px | Card produk, card banner, modal |
| radius-full | 9999px | Badge status berbentuk pill, avatar |

Shadow dipakai minimal dan halus, hanya untuk elemen yang mengambang di atas konten lain seperti dropdown, modal, dan sticky bar. Card produk dalam kondisi normal cukup pakai border tipis warna `Neutral 200`, tanpa shadow, supaya halaman tidak terlihat berat.

## 6. Grid dan Breakpoint

| Breakpoint | Lebar Layar | Kolom Grid Produk |
|---|---|---|
| Mobile | di bawah 640px | 2 kolom |
| Tablet | 640px sampai 1024px | 3 kolom |
| Desktop | di atas 1024px | 4 sampai 5 kolom |

Lebar konten maksimal di desktop dibatasi 1200px supaya teks dan card tidak melebar berlebihan di layar besar. Semua halaman dites dulu di lebar 375px (HP kecil) sebelum dianggap selesai.

## 7. Komponen Reusable

Komponen berikut dipakai ulang di seluruh halaman website. Semua halaman baru yang dibuat setelah dokumen ini harus memakai komponen dari daftar ini, bukan membuat versi baru.

### 7.1 Tombol (Button)

Varian: Primary (warna Primary, teks putih, dipakai untuk aksi utama seperti "Bayar Sekarang"), Secondary (border warna Primary, teks Primary, dipakai untuk aksi kedua seperti "Batal"), dan Disabled (warna abu-abu Neutral 200, tidak bisa diklik).
Ukuran: Small (padding 8px/12px, teks 14px), Medium (padding 12px/16px, teks 16px, ukuran default), Large (padding 16px/24px, teks 16px, dipakai untuk tombol checkout utama).
Aturan: tinggi tombol minimal 44px di tampilan mobile supaya nyaman disentuh. Sudut tombol pakai `radius-md`.

### 7.2 Card Produk

Isi: gambar produk atau ikon kategori, nama produk, nominal, dan harga. Padding dalam card pakai `space-4`. Sudut card pakai `radius-lg`. Saat dipilih atau di-hover, background card berubah ke `Primary Light` dan border berubah warna `Primary`.

### 7.3 Card Kategori

Isi: ikon kategori dan nama kategori, disusun horizontal untuk navigasi cepat di halaman depan. Dipakai ulang di halaman depan dan di halaman admin untuk filter produk.

### 7.4 Badge Status

Dipakai untuk menampilkan status pesanan. Menunggu Pembayaran pakai warna Warning, Dibayar dan Diproses pakai warna Primary, Selesai pakai warna Success, Gagal/Dibatalkan pakai warna Danger. Bentuk badge pill (`radius-full`), padding 4px vertikal dan 12px horizontal, teks 12px.

### 7.5 Form Input

Terdiri dari label di atas input, kolom input, dan teks keterangan/error di bawah input. Label wajib ada di setiap input, tidak boleh hanya mengandalkan placeholder. Padding dalam input pakai `space-3` sampai `space-4`. Saat error, border input berubah warna Danger dan muncul teks keterangan error di bawahnya.

### 7.6 Pemilih Metode Pembayaran

Ditampilkan sebagai grid ikon logo metode pembayaran (QRIS, GoPay, DANA, ShopeePay, Virtual Account, Kartu Kredit). Setiap ikon dalam kotak dengan `radius-md`, dan kotak yang dipilih diberi border warna Primary.

### 7.7 Sticky Bar Checkout (Mobile)

Bar tombol "Bayar Sekarang" yang menempel di bagian bawah layar saat customer sedang memilih produk di mode mobile, supaya tombol checkout selalu terlihat tanpa perlu scroll ke bawah.

### 7.8 Navbar/Topbar

Berisi logo Ayong Store, menu navigasi utama, dan ikon "Cek Pesanan". Tinggi topbar konsisten 64px di desktop dan 56px di mobile.

### 7.9 Footer

Berisi informasi kontak, link halaman statis (FAQ, Syarat Ketentuan, Kontak Kami), dan logo metode pembayaran yang didukung, untuk membangun kepercayaan customer.

### 7.10 Modal/Dialog

Dipakai untuk konfirmasi aksi penting, misalnya konfirmasi sebelum membatalkan pesanan di panel admin. Lebar modal maksimal 480px di desktop, penuh lebar layar dikurangi margin 16px di mobile. Sudut modal pakai `radius-lg`.

### 7.11 Notifikasi/Alert Banner

Dipakai untuk pesan sukses, peringatan, atau error di bagian atas form atau halaman, misalnya setelah berhasil menyimpan data produk di panel admin. Warna latar mengikuti token warna status yang sesuai.

### 7.12 Tabel (Panel Admin)

Dipakai di halaman daftar pesanan, daftar produk, dan laporan penjualan. Baris tabel berselang warna (zebra striping) memakai `Neutral 50` untuk baris genap, supaya mudah dibaca saat datanya panjang.

### 7.13 Pagination

Dipakai di bawah tabel dan grid produk yang datanya banyak. Tombol halaman aktif memakai warna Primary, tombol lain netral.

### 7.14 Empty State

Ditampilkan saat data kosong, misalnya belum ada pesanan atau hasil pencarian tidak ditemukan. Berisi ilustrasi sederhana atau ikon, teks penjelasan singkat, dan tombol aksi jika relevan.

### 7.15 Loading Skeleton

Dipakai saat data sedang dimuat, terutama di grid produk dan tabel panel admin, supaya customer dan admin tahu halaman sedang bekerja, bukan macet.

### 7.16 Sidebar Menu (Panel Admin)

Menu navigasi tetap di sisi kiri panel admin, menu yang tampil menyesuaikan peran, Admin tidak melihat menu yang khusus untuk Owner seperti Pengaturan Toko dan Manajemen Akun Admin.

## 8. Ikon

Ikon memakai satu set konsisten, disarankan Phosphor Icons atau Remix Icon. Ukuran ikon dalam teks/tombol 20px, ikon navigasi utama 24px. Tidak mencampur beberapa gaya ikon berbeda dalam satu halaman.

## 9. Aksesibilitas

Kontras teks terhadap background minimal rasio 4.5:1 supaya tetap terbaca. Semua tombol dan area yang bisa diklik punya ukuran minimal 44x44 pixel di tampilan mobile. Semua form wajib punya label yang terhubung ke input-nya, tidak hanya mengandalkan placeholder yang hilang saat diisi.

## 10. Penerapan Konsisten

Semua komponen pada bagian 7 dipakai ulang di seluruh halaman, termasuk halaman depan, detail produk, checkout, cek status pesanan, dan seluruh halaman panel admin. Kalau ada kebutuhan tampilan baru yang belum tercakup di dokumen ini, komponen baru didiskusikan dulu dan ditambahkan ke dokumen ini sebelum dipakai, supaya desain website tetap satu sistem yang konsisten dan gampang dirawat.
