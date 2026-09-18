# Ayong Store

Ayong Store adalah aplikasi web top-up Higgs Games Island berbasis CodeIgniter 4. Aplikasi menyediakan katalog produk publik, checkout tanpa akun, pembayaran manual melalui transfer bank atau QRIS, upload bukti pembayaran, pengecekan status pesanan, panel admin, pengelolaan katalog, laporan, backup database, dan pengajuan bongkar kartu.

## Fitur Utama

- Katalog produk dan kategori publik.
- Checkout tanpa login dengan validasi ID game dan nomor WhatsApp.
- Voucher dengan reservasi, commit, dan release.
- Pembayaran manual melalui kanal bank atau QRIS.
- Invoice privat menggunakan token akses terpisah dari nomor invoice.
- Upload bukti pembayaran dan verifikasi atau penolakan oleh admin.
- Notifikasi WhatsApp melalui Wablas.
- Panel admin dengan role `owner` dan `admin`.
- Manajemen produk, kategori, banner, voucher, pengumuman, halaman statis, dan pengaturan toko.
- Laporan penjualan, export PDF/Excel, activity log, dan backup database.

## Persyaratan

- PHP `8.2` atau lebih baru.
- Ekstensi PHP `intl`, `mbstring`, `mysqli`, `curl`, `fileinfo`, dan `dom`.
- MySQL/MariaDB pada port default `3306`.
- Composer, Node.js, dan npm.
- Web server dengan document root menunjuk ke direktori `public/`.

## Setup Lokal

1. Clone repository lalu masuk ke direktori proyek.

   ```bash
   git clone <repository-url> topup-hgi
   cd topup-hgi
   ```

2. Install dependency PHP dan frontend.

   ```bash
   composer install
   npm ci
   ```

3. Buat database kosong, misalnya `topup_hgi`, lalu salin konfigurasi environment.

   ```bash
   copy env .env
   ```

   Pada macOS/Linux gunakan `cp env .env`.

4. Isi konfigurasi minimal di `.env`:

   ```dotenv
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost/topup-hgi/'

   database.default.hostname = localhost
   database.default.database = topup_hgi
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   database.default.port = 3306

   encryption.key = hex2bin:<random-64-character-hex-key>
   ```

   Jangan commit `.env`. Gunakan `encryption.key` berbeda untuk setiap environment. Jika menggunakan Wablas, tambahkan domain, token, dan nomor tujuan admin sesuai deployment.

5. Jalankan migration dan seed akun owner.

   ```bash
   php spark migrate --all
   php spark db:seed AdminSeeder
   ```

6. Build asset CSS dan jalankan server development.

   ```bash
   npm run build:css
   php spark serve
   ```

   Buka URL yang ditampilkan Spark, biasanya `http://localhost:8080/`.

## Akun Admin Awal

`AdminSeeder` membuat akun owner berikut untuk instalasi awal:

| Field | Nilai awal |
| --- | --- |
| Email | `owner@gmail.com` |
| Password | `password` |
| Role | `owner` |

Segera ganti password setelah login pertama. Jangan gunakan credential default di production. Jangan menjalankan seeder berulang kali pada database berisi akun tanpa memeriksa potensi duplikasi.

## Role dan Akses

- `owner`: seluruh akses admin, termasuk akun admin, laporan, backup database, activity log, pengaturan toko, dan kanal pembayaran.
- `admin`: operasional harian seperti dashboard, katalog, banner, voucher, pengumuman, halaman statis, pesanan, verifikasi pembayaran, dan workflow bongkar sesuai route.
- Pengunjung publik tidak perlu akun untuk melihat katalog, membuat pesanan, upload bukti dengan token, atau mengecek status dengan invoice dan token akses.

## Perintah Pengembangan

| Perintah | Kegunaan |
| --- | --- |
| `php spark serve` | Menjalankan server development |
| `php spark migrate --all` | Menjalankan seluruh migration |
| `php spark db:seed AdminSeeder` | Membuat akun owner awal |
| `npm run build:css` | Build CSS Tailwind minified |
| `npm run watch:css` | Watch perubahan CSS |
| `composer test` | Menjalankan seluruh PHPUnit test |
| `vendor/bin/phpunit tests/Feature` | Menjalankan feature test |
| `composer validate --strict --no-check-publish` | Validasi metadata Composer |
| `composer audit --locked` | Audit dependency sesuai lockfile |
| `php spark uploads:audit` | Mencari orphan upload |
| `php spark uploads:audit --delete` | Menghapus orphan upload yang ditemukan |
| `php spark bongkar:notifications` | Retry notifikasi bongkar gagal |

## Struktur Aplikasi

```text
public/                 Front controller dan asset publik
app/Config/              Route, filter, database, cache, session, security
app/Controllers/         Controller publik dan panel admin
app/Models/              Akses data dan aturan domain
app/Libraries/           Money, integration settings, dan gateway Wablas
app/Database/Migrations/ Evolusi skema database
app/Database/Seeds/      Data awal, termasuk AdminSeeder
app/Views/               Template publik dan admin
resources/css/           Sumber CSS Tailwind
writable/                Session, cache, log, upload bukti, backup runtime
tests/                   Unit, database, dan feature tests
```

Invoice publik memakai token akses acak dan menyamarkan ID game serta nomor WhatsApp. Bukti pembayaran disimpan di `writable/uploads/payment-proofs/`, bukan direktori publik. Banner, logo, avatar, dan ikon kategori yang harus tampil publik berada di `public/assets/uploads/`.

## Alur Operasional

### Checkout dan Pembayaran

1. Customer memilih produk dari katalog.
2. Customer mengisi ID game, nomor WhatsApp, voucher opsional, dan kanal pembayaran.
3. Sistem membuat invoice, menyimpan snapshot produk dan kanal pembayaran, serta memberikan token akses privat.
4. Customer membuka invoice dan mengunggah bukti pembayaran melalui token tersebut.
5. Admin memeriksa bukti, lalu memilih **Verifikasi** atau **Tolak** dengan alasan.
6. Pesanan yang diverifikasi masuk status diproses; admin menandainya selesai setelah top-up berhasil.
7. Sistem mencoba mengirim notifikasi WhatsApp setelah pesanan selesai.

Customer dapat mengecek status melalui halaman `cek-pesanan` menggunakan nomor invoice dan token akses. Token harus diperlakukan sebagai rahasia.

### Pengajuan Bongkar

Customer mengirim pengajuan bongkar dari halaman publik menggunakan katalog kartu dan metode pencairan yang aktif. Admin memproses perubahan status dari panel admin. Notifikasi yang gagal dapat diproses ulang:

```bash
php spark bongkar:notifications
```

### Backup dan Maintenance

Fitur backup berada di panel owner. Backup disimpan di `writable/backups/`, dibatasi retention maksimal oleh aplikasi, dan hanya bisa diakses oleh owner. Sebelum migration production:

1. Buat dan verifikasi backup.
2. Periksa migration history.
3. Jalankan `php spark migrate --all`.
4. Jalankan smoke test checkout, invoice, upload bukti, verifikasi, dan halaman admin.

Untuk membersihkan upload yang tidak lagi direferensikan, jalankan audit tanpa `--delete` terlebih dahulu. Gunakan `--delete` hanya setelah daftar orphan diverifikasi.

## Konfigurasi Production

Production harus menggunakan `.env` terpisah dan tidak boleh memakai nilai contoh atau credential default.

```dotenv
CI_ENVIRONMENT = production
app.baseURL = 'https://your-domain.example/'
app.allowedHostnames = 'your-domain.example,www.your-domain.example'
app.proxyIPs = '10.0.0.10'
security.CSPEnabled = true
database.default.encrypt = true
cache.handler = redis
cache.backupHandler = file
redis.host = 127.0.0.1
redis.port = 6379
redis.password = 'replace-with-secret'
```

Pastikan `encryption.key`, kredensial database, Wablas, dan Redis berasal dari environment atau secret manager. Pastikan document root web server menunjuk ke `public/`, bukan root repository. Folder `writable/` harus dapat ditulis aplikasi tetapi tidak boleh diakses sebagai source publik.

## Checklist Deployment

- [ ] Set `CI_ENVIRONMENT=production` dan HTTPS `app.baseURL`.
- [ ] Isi hostname/proxy allowlist dan secret production.
- [ ] Buat backup sebelum migration.
- [ ] Jalankan `php spark migrate --all` pada database target.
- [ ] Jalankan `php spark uploads:audit` dan pastikan folder upload menolak eksekusi script.
- [ ] Uji checkout, upload bukti, verifikasi/tolak, invoice dengan token salah, cek status, bongkar, dan retry notifikasi.
- [ ] Uji backup create/download/delete dengan akun owner.
- [ ] Jalankan test dan build sebelum rilis.

## Troubleshooting

### Database gagal tersambung

Periksa service MySQL/MariaDB aktif, nama database, username, password, port, dan `database.default.DBDriver` di `.env`. Pastikan database sudah dibuat sebelum menjalankan migration.

### Halaman menampilkan error base URL atau asset tidak ditemukan

Sesuaikan `app.baseURL` dengan URL sebenarnya, termasuk trailing slash. Untuk server Apache/Nginx, gunakan `public/` sebagai document root.

### Session, cache, atau upload gagal ditulis

Pastikan direktori `writable/cache`, `writable/session`, `writable/logs`, `writable/uploads`, dan `writable/backups` ada dan dapat ditulis oleh user proses PHP.

### CSRF gagal saat submit form

Pastikan form memakai helper/form token CSRF yang disediakan layout dan halaman dibuka dari host yang sama dengan `app.baseURL`. Jangan mencampur hostname, port, atau scheme HTTP/HTTPS.

### Notifikasi WhatsApp tidak terkirim

Periksa `wablas.domain`, token, nomor admin, koneksi outbound server, dan log aplikasi. Untuk pengajuan bongkar, jalankan `php spark bongkar:notifications` setelah konfigurasi diperbaiki.

### Upload ditolak

Bukti pembayaran harus berupa JPG, JPEG, PNG, atau WebP dengan ukuran maksimal 5 MB dan dimensi gambar yang valid. Asset publik mengikuti validasi upload pada controller admin.

## Dokumentasi Tambahan

- Rencana pekerjaan berada di `TODO.md`.
- Spesifikasi dan rencana implementasi berada di `docs/superpowers/`.
- Test terkait berada di `tests/`.
