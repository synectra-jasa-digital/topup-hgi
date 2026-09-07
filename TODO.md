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
