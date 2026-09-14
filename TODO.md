# TODO - Ayong Store

Dikerjakan satu fase per satu, ditandai `[x]` setelah lulus uji manual (skenario berhasil + gagal). Tidak lompat ke fase berikutnya sebelum fase berjalan selesai.

## Fase 0 - Fondasi Proyek
- [x] `.env` dibuat, koneksi database `topup_hgi` terverifikasi
- [x] Database `topup_hgi` dibuat
- [x] Tailwind CLI + token warna/spacing dari rules/design.md
- [x] Layout dasar publik (`app/Views/layouts/public.php`)
- [x] Layout dasar admin (`app/Views/layouts/admin.php`)
- [x] Library pihak ketiga: midtrans/midtrans-php, phpoffice/phpspreadsheet, dompdf/dompdf
- [x] `TODO.md` dibuat

## Fase 1 - Skema Database
- [x] Migration: admins
- [x] Migration: product_categories
- [x] Migration: products
- [x] Migration: banner_categories
- [x] Migration: banners
- [x] Migration: vouchers
- [x] Migration: orders
- [x] Migration: order_payments
- [x] Migration: activity_logs
- [x] Migration: static_pages
- [x] Migration: store_settings
- [x] Seeder akun Owner default (owner@ayongstore.test / GantiPassword123! - wajib diganti)

## Fase 2 - Autentikasi & RBAC
- [x] Model Admin + AuthController (login/logout)
- [x] AuthFilter (wajib login untuk /admin/*)
- [x] RoleFilter (batasi menu Owner-only, siap dipakai di rute Owner-only pada fase berikutnya)
- [x] View login panel admin
- [x] Uji: login benar, login salah, akses /admin tanpa login redirect ke login, logout menghapus sesi

## Fase 3 - Manajemen Kategori & Produk (Admin)
- [x] CRUD product_categories
- [x] CRUD products
- [x] Uji: tambah/ubah/hapus kategori & produk (sukses & gagal validasi), hapus kategori yang masih dipakai produk ditolak

## Fase 4 - Manajemen Kategori & Banner (Admin)
- [x] CRUD banner_categories
- [x] CRUD banners (upload gambar, jadwal tayang)
- [x] Uji: tambah/ubah/hapus kategori & banner (sukses & gagal validasi), upload gambar valid/invalid (kosong, bukan gambar), ubah tanpa ganti gambar, hapus kategori yang masih dipakai banner ditolak

## Fase 5 - Storefront Publik
- [x] Halaman depan (kategori + banner + grid produk)
- [x] Halaman pilih nominal produk (per kategori via slug)
- [x] Uji: grid responsif mobile-first (2/3/5 kolom sesuai breakpoint), kategori & produk kosong tampil empty state, produk & kategori nonaktif tersembunyi, slug kategori tidak valid/nonaktif -> 404

## Fase 6 - Checkout & Order Tanpa Login
- [x] Form checkout (ID akun HGI + no WA) + validasi server-side
- [x] Generate invoice number + simpan order status menunggu_pembayaran
- [x] Terapkan voucher (validasi kuota/tanggal)
- [x] Uji: checkout sukses, validasi gagal (label pesan disesuaikan), voucher valid diterapkan & used_count bertambah, voucher tidak ditemukan/kadaluarsa/kuota habis/di bawah min_purchase ditolak, invoice/produk tidak valid -> 404

## Fase 7 - Integrasi Midtrans Snap
- [x] Buat transaksi Snap dari order
- [x] Endpoint callback/webhook + verifikasi signature key
- [x] Update status order & isi order_payments dari callback
- [x] Uji sandbox Midtrans: pembayaran sukses, gagal, callback signature palsu ditolak

## Fase 8 - Cek Status Pesanan (Customer)
- [x] Halaman cek status via invoice number
- [x] Uji: invoice valid, invoice tidak ditemukan

## Fase 9 - Notifikasi WA (Wablas)
- [x] WablasGateway: kirim WA ke Admin saat pembayaran dikonfirmasi
- [x] Kirim WA ke customer saat status Selesai
- [x] Uji: pesan terkirim, gagal kirim tidak menggagalkan proses order

## Fase 10 - Manajemen Pesanan (Admin)
- [x] List + detail order (filter status)
- [x] Ubah status manual (Diproses -> Selesai) + trigger WA customer
- [x] Uji: perubahan status tercatat, notifikasi terkirim

## Fase 11 - Voucher, Log Aktivitas, Halaman Statis, Akun Admin
- [x] CRUD voucher
- [x] Activity log otomatis di aksi penting (siapa, kapan, apa)
- [x] CRUD halaman statis (FAQ/Syarat Ketentuan/Kontak Kami) + tampilan publik
- [x] CRUD akun Admin (Owner only)
- [x] Uji: Admin tidak bisa akses menu akun Admin

## Fase 12 - Laporan Penjualan & Dashboard
- [x] Laporan harian/bulanan/tahunan + export Excel/PDF (Owner)
- [x] Dashboard grafik (penjualan, pesanan tertunda, produk terlaris)
- [ ] Uji: angka laporan cocok dengan data contoh

## Fase 13 - Pengaturan Toko & Polish
- [ ] CRUD store_settings (nama toko, logo, kontak, WA, kredensial Midtrans, maintenance mode)
- [ ] Review responsif 375px, empty state, loading skeleton, aksesibilitas
- [ ] Uji akhir: alur checkout end-to-end, RBAC Admin vs Owner

## Hardening Non-Akun
> Checklist perbaikan berdasarkan audit keamanan/reliabilitas. Tandai `[x]` hanya setelah test otomatis dan verifikasi manual terkait lulus.

### Fase H1 - Nominal Uang & Voucher
- [x] H1.1 Gunakan integer rupiah pada checkout, diskon, total, Midtrans, invoice, dan laporan
- [x] H1.2 Tetapkan aturan pembulatan persentase/nominal dan cegah total negatif
- [x] H1.3 Tambahkan lifecycle voucher reserve/commit/release
- [x] H1.4 Release reservation voucher untuk order expired/dibatalkan
- [x] H1.5 Tambahkan test pecahan rupiah, kuota, konkurensi, dan abandoned checkout

### Fase H2 - Idempotency Pembayaran
- [x] H2.1 Cegah double-submit checkout dengan idempotency key
- [x] H2.2 Buat webhook Midtrans idempotent dan aman terhadap race condition
- [x] H2.3 Simpan event payment dengan unique key yang tepat
- [x] H2.4 Cegah notifikasi Wablas ganda
- [x] H2.5 Uji duplicate webhook, retry, dan callback out-of-order

### Fase H3 - Privasi Invoice & Cache
- [x] H3.1 Tambahkan access token terpisah dari nomor invoice
- [x] H3.2 Masking ID game dan nomor WhatsApp pada halaman publik
- [x] H3.3 Tambahkan proteksi enumerasi invoice/status
- [x] H3.4 Pastikan page cache tidak menyimpan invoice, checkout, admin, atau halaman session
- [x] H3.5 Tambahkan test akses token dan cache-control

### Fase H4 - Endpoint Publik & Workflow Bongkar
- [x] H4.1 Rate limit checkout dan endpoint invoice/status
- [x] H4.2 Gunakan storage rate limit atomic/shared di production
- [x] H4.3 Validasi transisi status pengajuan bongkar
- [x] H4.4 Catat actor/waktu perubahan status bongkar
- [x] H4.5 Tambahkan notifikasi bongkar server-side dengan retry/status

### Fase H5 - Upload
- [x] H5.1 Batasi dimensi gambar dan validasi konten file
- [x] H5.2 Pastikan folder upload tidak dapat mengeksekusi script
- [x] H5.3 Hapus asset lama setelah penggantian yang berhasil
- [ ] H5.4 Audit orphan upload dan kebijakan retention
- [ ] H5.5 Tambahkan test file invalid, oversized, malformed, dan extreme dimensions

### Fase H6 - Production Security
- [ ] H6.1 Aktifkan HTTPS production
- [ ] H6.2 Aktifkan security headers dan CSP bertahap
- [ ] H6.3 Konfigurasikan trusted host dan proxy dengan benar
- [ ] H6.4 Aktifkan database strict mode dan encryption sesuai deployment
- [ ] H6.5 Dokumentasikan environment production yang wajib

### Fase H7 - Store Settings & Maintenance
- [ ] H7.1 Sinkronkan Store Settings dengan Midtrans/Wablas runtime
- [ ] H7.2 Jangan tampilkan atau simpan credential plaintext tanpa perlindungan
- [ ] H7.3 Implementasikan maintenance mode filter dengan allowlist admin/webhook
- [ ] H7.4 Hapus logo lama dan validasi upload settings
- [ ] H7.5 Tambahkan test settings dan maintenance mode

### Fase H8 - Reports & Backup
- [ ] H8.1 Ganti fungsi DATE/MONTH/YEAR dengan date range yang sargable
- [ ] H8.2 Tambahkan index untuk query order/payment/report
- [ ] H8.3 Stream backup per chunk tanpa memuat seluruh tabel ke memory
- [ ] H8.4 Tambahkan retention, batas ukuran, dan proteksi backup
- [ ] H8.5 Audit download/delete backup dan tambahkan test

### Fase H9 - Verifikasi & Operasional
- [ ] H9.1 PHPUnit seluruh suite lulus
- [ ] H9.2 PHP syntax check lulus
- [ ] H9.3 Composer validation dan dependency audit dijalankan
- [ ] H9.4 CSS build lulus
- [ ] H9.5 Review diff, migration production, dan rollback procedure
- [ ] H9.6 Update README deployment dan checklist manual end-to-end
