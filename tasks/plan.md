# Implementation Plan: Penyelesaian TODO Ayong Store

## Analisis singkat
Fondasi, database, autentikasi, katalog, checkout, Midtrans, status pesanan, dan gateway Wablas sudah tersedia. Fase 10-13 belum memiliki route/controller/view walaupun sebagian menu admin sudah tampil. Database untuk vouchers, activity_logs, static_pages, dan store_settings sudah termigrasi. Test otomatis belum tersedia; verifikasi dilakukan melalui lint/syntax, migration status, route listing, dan smoke test HTTP setelah setiap slice.

## Urutan implementasi
1. Fase 10: daftar/detail pesanan, filter status, perubahan status dengan notifikasi customer.
2. Fase 11a: CRUD voucher.
3. Fase 11b: activity log reusable dan integrasi pada aksi admin penting.
4. Fase 11c: CRUD halaman statis dan route publik.
5. Fase 11d: CRUD akun admin Owner-only.
6. Fase 12: dashboard ringkasan, laporan periode, export CSV-compatible/Excel dan PDF.
7. Fase 13: pengaturan toko Owner-only, integrasi setting storefront, dan polish keamanan/responsif.

## Aturan verifikasi
- Jalankan `php -l` untuk setiap PHP baru/diubah.
- Jalankan `php spark migrate:status` dan `php spark routes` setelah perubahan route/migration.
- Jalankan `npm run build:css` bila view memakai class baru.
- Smoke test route publik/admin memakai server lokal; jangan mengklaim manual Midtrans/Wablas tanpa kredensial sandbox aktif.
- Jangan menandai TODO selesai sebelum implementasi dan verifikasi slice terkait benar-benar tersedia.

## Risiko
- Kredensial pihak ketiga berada di environment lokal dan tidak boleh dicetak.
- Export PDF bergantung pada dompdf yang tersedia di vendor.
- Data lama harus dipertahankan; semua perubahan schema bersifat additive.
