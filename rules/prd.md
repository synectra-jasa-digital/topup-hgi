# PRD - Ayong Store (Website Top Up Higgs Games Island)

Versi Dokumen: 1.1
Tanggal: 6 September 2026
Nama Toko: Ayong Store

## 1. Latar Belakang dan Tujuan

Ayong Store adalah toko top up khusus untuk game Higgs Games Island. Website ini dibangun supaya Ayong Store punya kanal penjualan sendiri, dengan kendali penuh atas harga, kecepatan layanan, dan pengalaman pelanggan, tanpa bergantung penuh pada marketplace pihak ketiga seperti VCGamers.

Dokumen ini jadi acuan bersama antara pemilik toko dan tim pengembang mengenai fitur, alur proses, peran pengguna, batasan teknis, dan aturan kerja pengembangan, supaya proses pembangunan website berjalan terarah dan hasil akhirnya mudah dirawat.

## 2. Ruang Lingkup Produk

Website Ayong Store melayani transaksi pembelian koin dan kartu emas Higgs Games Island oleh customer umum tanpa perlu login atau membuat akun. Pengelolaan produk, transaksi, dan laporan dilakukan lewat panel admin yang hanya bisa diakses oleh Owner dan Admin. Website dibangun dengan CodeIgniter dan database PHP (MySQL/MariaDB), dirancang responsive untuk web desktop, mobile, dan siap dibungkus WebView Android.

## 3. Peran Pengguna

| Peran | Deskripsi | Hak Akses Utama |
|---|---|---|
| Owner | Pemilik toko, kontrol penuh sistem | Seluruh akses Admin, ditambah laporan penjualan & keuangan, dashboard bergrafik, manajemen akun Admin, pengaturan toko, dan log aktivitas seluruh pengguna |
| Admin | Staf operasional harian | Kelola kategori & data produk, kelola kategori & banner, proses dan ubah status pesanan, kelola voucher/diskon, kelola halaman statis, lihat log aktivitas milik sendiri |
| Customer | Pembeli umum, tanpa akun/login | Melihat katalog produk, checkout dan bayar lewat Midtrans, cek status pesanan lewat nomor invoice, menerima notifikasi WA otomatis |

## 4. Daftar Modul Fitur

| No | Modul | Deskripsi Singkat | Akses |
|---|---|---|---|
| 1 | Manajemen Kategori Produk | Kelola pengelompokan produk, misal Koin Emas, Kartu Emas, Koin Emas MD, Kartu Ungu | Admin, Owner |
| 2 | Manajemen Produk | Kelola nama, nominal, harga jual, harga modal, dan status aktif produk | Admin, Owner |
| 3 | Manajemen Kategori Banner | Kelola pengelompokan banner, misal Banner Utama dan Banner Promo | Admin, Owner |
| 4 | Manajemen Banner | Kelola gambar, link tujuan, dan jadwal tayang banner | Admin, Owner |
| 5 | Manajemen Pesanan | Pantau dan proses seluruh transaksi customer | Admin, Owner |
| 6 | Notifikasi Pesanan ke WA Admin | Kirim detail pesanan otomatis ke WA Admin setelah pembayaran dikonfirmasi | Sistem (otomatis) |
| 7 | Cek Status Pesanan | Customer cek status lewat nomor invoice, plus notifikasi WA otomatis ke customer | Customer (tanpa login) |
| 8 | Integrasi Midtrans | Proses pembayaran lewat Midtrans Snap (QRIS, e-wallet, VA, kartu kredit) | Sistem |
| 9 | Manajemen Voucher/Diskon | Buat kode promo potongan nominal atau persentase | Admin, Owner |
| 10 | Log Aktivitas | Catat aktivitas penting Admin dan Owner untuk audit | Admin (miliknya sendiri), Owner (semua) |
| 11 | Laporan Penjualan | Laporan harian, bulanan, tahunan, bisa diunduh Excel/PDF | Owner |
| 12 | Dashboard Ringkasan Bergrafik | Grafik penjualan, pesanan tertunda, produk terlaris | Admin (operasional), Owner (lengkap) |
| 13 | Pengaturan Toko | Nama toko, logo, kontak, nomor WA, kredensial Midtrans, mode maintenance | Owner |
| 14 | Halaman Statis | Kelola konten FAQ, Syarat Ketentuan, Kontak Kami | Admin, Owner |
| 15 | Manajemen Akun Admin | Tambah, ubah, nonaktifkan, hapus akun Admin | Owner |

## 5. Alur Proses Utama

**Alur pemesanan customer:** pilih kategori produk, pilih nominal, isi ID akun Higgs Games Island dan nomor WA aktif, bayar lewat Midtrans Snap, sistem terbitkan nomor invoice.

**Alur pemrosesan admin:** sistem terima konfirmasi Midtrans, kirim notifikasi WA ke Admin, Admin kirim chip secara manual ke akun game customer, Admin ubah status jadi Selesai, sistem kirim notifikasi WA ke customer.

**Alur pembayaran Midtrans:** website kirim data transaksi ke Snap API, customer bayar di halaman Snap, Midtrans kirim callback status, sistem verifikasi dan update status pesanan.

## 6. Kebutuhan Non-Fungsional

Kata sandi Admin dan Owner disimpan terenkripsi, akses panel dibatasi lewat role-based access control. Transaksi pembayaran divalidasi lewat signature key resmi Midtrans. Halaman utama dan halaman produk harus cepat dimuat pada koneksi mobile standar. Website kompatibel di browser umum dan WebView Android. Struktur database dirancang mudah ditambah kategori atau produk baru. Log aktivitas dan cadangan data tersedia untuk audit.

## 7. Arsitektur Teknis

Backend memakai CodeIgniter 4 dengan database MySQL/MariaDB, pola MVC standar. Frontend memakai Tailwind CSS untuk styling, Alpine.js untuk interaksi ringan, dan HTMX untuk pembaruan sebagian halaman tanpa reload penuh. Integrasi pihak ketiga mencakup Midtrans Snap API untuk pembayaran dan gateway WhatsApp untuk notifikasi otomatis.

## 8. Aturan Pengembangan

Bagian ini wajib diikuti oleh siapa pun yang mengerjakan pengembangan website Ayong Store, baik developer manusia maupun asisten AI coding.

### 8.1 Manajemen To Do

Setiap fitur wajib dipecah jadi daftar tugas kecil sebelum mulai coding. Daftar tugas dicatat di file `TODO.md` di root project atau di task board yang dipakai tim. Setiap tugas ditandai selesai satu per satu, tidak boleh loncat mengerjakan banyak tugas sekaligus tanpa mencatatnya. Fitur baru tidak boleh dimulai sebelum fitur sebelumnya selesai dan sudah diuji. Setiap sesi kerja diakhiri dengan memperbarui status to do, supaya progres selalu jelas kalau dilanjutkan orang lain atau di lain waktu.

### 8.2 Prinsip Clean Code

Kode PHP mengikuti standar penulisan PSR-12. Satu fungsi atau method hanya mengerjakan satu tugas, dikenal sebagai prinsip single responsibility. Nama variabel, fungsi, dan class harus deskriptif dan menjelaskan maksudnya sendiri, hindari singkatan yang ambigu. Kode yang berulang dipindahkan ke helper atau service class, tidak boleh disalin tempel di banyak tempat. Logic bisnis dipisahkan dari controller, ditaruh di Model atau Service, controller cukup mengatur alur permintaan dan respons. Validasi input wajib dilakukan di sisi server, validasi di frontend hanya sebagai bantuan pengalaman pengguna, bukan pengaman utama. Kode yang tidak lagi dipakai dihapus sebelum commit, tidak dibiarkan menumpuk sebagai komentar mati. Komentar kode ditulis hanya untuk bagian logic yang rumit, bukan untuk kode yang sudah jelas dari penamaannya.

### 8.3 Struktur Folder dan Penamaan

Nama Controller memakai PascalCase, nama method di dalamnya memakai camelCase. Nama tabel database memakai snake_case dan bentuk jamak, misalnya `products`, `order_payments`, `activity_logs`. Nama file view dikelompokkan per folder modul dan mengikuti nama fiturnya, supaya mudah ditemukan. Konfigurasi sensitif seperti kredensial Midtrans disimpan di file environment, tidak ditulis langsung di kode.

### 8.4 Version Control

Setiap fitur dikerjakan di branch terpisah dengan format nama `fitur/nama-fitur`. Pesan commit ditulis singkat dan jelas, menjelaskan perubahan yang dilakukan, bukan proses pengerjaannya. Kode direview dulu sebelum digabungkan ke branch utama. Branch utama harus selalu dalam kondisi bisa dijalankan tanpa error.

### 8.5 Pengujian Sebelum Rilis

Setiap fitur diuji manual minimal untuk skenario berhasil dan skenario gagal sebelum ditandai selesai di daftar to do. Fitur pembayaran wajib diuji dulu memakai mode sandbox Midtrans sebelum dipakai di mode produksi. Perubahan pada modul laporan penjualan diuji dengan data contoh untuk memastikan angka yang ditampilkan akurat.

## 9. Kriteria Penerimaan

| Fitur | Kriteria Penerimaan |
|---|---|
| Checkout & Pembayaran | Customer bisa menyelesaikan pembayaran lewat Midtrans dan menerima nomor invoice tanpa perlu membuat akun |
| Notifikasi Admin | Notifikasi WA ke Admin terkirim maksimal dalam 1 menit setelah pembayaran dikonfirmasi berhasil |
| Cek Status Pesanan | Customer bisa melihat status pesanan yang akurat lewat nomor invoice kapan saja |
| Laporan Penjualan | Owner bisa melihat dan mengunduh laporan harian, bulanan, dan tahunan sesuai rentang tanggal yang dipilih |
| Hak Akses | Admin tidak bisa mengakses menu laporan keuangan, pengaturan toko, dan manajemen akun Admin |
| Log Aktivitas | Setiap perubahan data penting tercatat dengan nama pengguna dan waktu yang benar |

## 10. Batasan dan Asumsi

Pengiriman chip ke akun game customer dilakukan manual oleh Admin, sistem tidak terhubung otomatis ke server Higgs Games Island. Website hanya melayani produk top up Higgs Games Island pada versi ini. Nomor WA Admin dan customer harus aktif dan bisa menerima pesan otomatis dari sistem. Integrasi notifikasi WA bergantung pada penyedia gateway WA pihak ketiga yang dipilih saat implementasi.
