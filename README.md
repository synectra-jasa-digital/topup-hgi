# Ayong Store - Platform Top-Up Game & Gateway Operasional

**Ayong Store** adalah aplikasi web top-up game (khususnya koin dan item Higgs Games Island) berbasis **CodeIgniter 4** yang dirancang dengan prinsip *frictionless transaction*, keamanan tinggi, dan otomasi operasional. Aplikasi ini dilengkapi dengan **WhatsApp Gateway self-hosted berbasis Baileys Node.js**, **Bot Telegram Operasional Admin**, serta halaman publik tanpa login untuk checkout dan pengecekan transaksi.

---

## 🚀 Tech Stack & Arsitektur Sistem

### Backend & Core Framework
- **Framework**: CodeIgniter 4 (PHP 8.2+)
- **Database**: MySQL / MariaDB (InnoDB dengan Foreign Key constraints & indeks optimasi laporan)
- **Ekspor Dokumen**: XML Spreadsheet 2003 via `ReportExporter` (dibuka Excel sebagai laporan `.xlsx`) & `Dompdf` (Dokumen `.pdf`)
- **Keamanan & Kriptografi**: Enkripsi token 64-karakter acak, sanitasi input, proteksi CSRF, proteksi idempotensi transaksi, & hashing password Argon2id/Bcrypt.

### Frontend & Visual System
- **Styling**: Tailwind CSS (Minified Production Build) & Custom Micro-Animations
- **Design System**: *Direct Gaming Trust* (Corporate Modernism & Tactile Simplicity)
- **Typography**: Plus Jakarta Sans (Headings & Identitas) & Inter (UI Data, Form, & Tabel)
- **Icons**: Material Symbols Outlined (Google Fonts)

### Sub-Sistem Integrasi
1. **WhatsApp Gateway Service (`wa-gateway/`)**:
   - Engine Node.js 20+ Express server menggunakan `@whiskeysockets/baileys` (Multi-Device WhatsApp Web API).
   - Mendukung otentikasi API Key (`x-api-key`), auto-reconnect, QR code generator API, & routing kompatibel cPanel.
   - Sistem antrean publik berbasis database (`wa_outbox`) dengan mekanis worker retry (exponential backoff).
2. **Telegram Bot Operasional**:
   - Memanfaatkan Telegram Bot API (Webhook & Long-polling Outbox) untuk operasional admin via grup/chat Telegram.
   - Fitur inline keyboard interaktif untuk verifikasi/penolakan pembayaran langsung dari obrolan Telegram.

### Testing & CI/CD
- **Testing Framework**: PHPUnit 11 & PCOV Code Coverage driver.
- **CI/CD Pipeline**: GitHub Actions (Automated Syntax Check, PHPUnit Test, Tailwind Build, & FTP Deployment to Production Hosting).

---

## ✨ Fitur Utama Sistem

### 1. Modul Publik (Customer Experience)
- **Katalog Dual-Mode tanpa Login**: Memisahkan alur **Beli Koin (Top Up)** dan **Jual Kartu (Bongkar)** dalam 1 halaman tanpa perlu muat ulang halaman (*zero-reload*).
- **Wizard Checkout 4-Langkah**:
  1. Pilih Kategori Produk (Koin Gold, Koin MD, Kartu Ungu, dll).
  2. Pilih Nominal (dilengkapi filter pencarian cepat `1B`, `200M`).
  3. Masukkan Data Game (ID Game dengan masking otomatis & Nomor WhatsApp).
  4. Pilih Kanal Pembayaran Manual (Transfer Bank / QRIS).
- **Invoice Privat Berbasis Token**:
  - Halaman invoice hanya dapat dibuka dengan URL khusus yang memuat token akses privat 64-karakter (`/pesanan/INV-xxx?token=yyy`).
  - Dilengkapi fitur upload bukti pembayaran (mendukung foto hingga 10MB JPG/PNG/WEBP).
- **Cek Pesanan Terenkripsi (`/cek-pesanan`)**:
  - Customer dapat mengecek status pesanan hanya dengan nomor invoice.
  - Halaman publik ini menyamarkan ID Game, Nomor WhatsApp, dan detail rekening demi privasi customer.

### 2. WhatsApp Gateway (Baileys Node.js & Outbox Engine)
- **Dual Driver**: Pilihan driver **Baileys** (Self-hosted Node.js gratis) atau **Wablas** (Layanan berbayar) yang dapat ditukar kapan saja dari admin panel.
- **Notifikasi Otomatis Customer**:
  - 🔔 *Pesanan Dibuat*: Instruksi transfer & tautan invoice privat.
  - 📥 *Bukti Pembayaran Diterima*: Konfirmasi bukti sedang diperiksa admin.
  - ✅ *Pembayaran Diverifikasi*: Notifikasi pembayaran sah.
  - ❌ *Pembayaran Ditolak*: Notifikasi penolakan beserta alasan resmi dari admin.
  - 🎉 *Pesanan Selesai*: Konfirmasi koin/item telah berhasil dikirim ke ID Game.
  - 🔄 *Status Bongkar*: Pembaruan status pengajuan bongkar kartu customer.
- **Masking Sensitif**: ID Game otomatis disamarkan (contoh: `98****32`) pada pesan WhatsApp demi keamanan akun customer.
- **Panel Admin WhatsApp (`/admin/whatsapp`)**:
  - Khusus role `owner`.
  - Tampilan status koneksi real-time & QR Code scanner otomatis.
  - Form pengubahan Gateway URL & API Key.
  - Pengirim pesan uji coba.
  - Tabel riwayat outbox 50 pesan terakhir dengan tombol *Retry* (kirim ulang manual).

### 3. Bot Telegram Operasional Admin (`@ayongstore_bot`)
- **Otentikasi Admin Terikat Session**:
  - `/login <email> <password>` untuk otentikasi akun admin via Telegram.
  - `/logout` untuk keluar dan `/sesi` untuk mengecek status login.
- **Notifikasi & Verifikasi Instant**:
  - Mengirim foto bukti pembayaran terbaru secara otomatis ke chat Telegram admin.
  - Dilengkapi tombol inline `✅ Verifikasi` dan `❌ Tolak` (dengan prompt instruksi alasan penolakan).
- **Unduh Laporan Penjualan (`/laporan`)**:
  - Prompt interaktif pemilihan format file (**Excel XLSX** atau **PDF**).
  - Pilihan periode: *Hari Ini*, *7 Hari Terakhir*, *Bulan Ini*, atau *Kustom*.
- **Manajemen Toko & Pesanan**:
  - `/tutup [alasan]` & `/buka`: Mengubah mode operasional toko secara instant dari Telegram.
  - `/status`: Cek status buka/tutup toko.
  - `/pending`: Menampilkan daftar pesanan yang menanti verifikasi pembayaran.
  - `/pesanan <invoice>`: Cek detail pesanan spesifik.
  - `/selesai <invoice>`: Menandai pesanan selesai dari Telegram.

### 4. Tampilan Toko Tutup (Interactive Store Closed Page)
- **Desain Modern non-AI Slop**: Mengikuti pedoman *Direct Gaming Trust* dengan animasi papan gantung toko (`swing-sign`) & kartu ber-elevasi bersih.
- **Jam Server Realtime**: Jam digital WIB live update tiap detik.
- **Form Lacak Invoice**: Customer tetap dapat melacak status pesanan yang telah dibayar sebelum toko tutup (route `/cek-pesanan` dibypass oleh `MaintenanceFilter`).
- **Pesan Pengelola & Contact CS**: Menampilkan catatan alasan penolakan/toko tutup dari admin dan tombol langsung ke WhatsApp CS.
- **Aksesibilitas Keyboard**: Shortcut tombol `R` (refresh status toko) dan `W` (buka WhatsApp CS).

### 5. Panel Admin & Manajemen Owner
- **Manajemen Role**: Access Control List (ACL) memisahkan hak akses `owner` dan `admin`.
- **Katalog & Banner**: Kelola produk, kategori (dengan ikon custom), banner hero carousel, & pengumuman toko.
- **Voucher & Diskon**: Sistem reservasi voucher saat checkout dengan proteksi otomatis release jika pesanan dibatalkan/expired.
- **Manajemen Bongkar Kartu**: Katalog kartu bongkar & metode pencairan dana customer.
- **Laporan & Audit**: Laporan penjualan dengan grafik, export Excel/PDF, audit log aktivitas admin, & fitur backup database (khusus owner).

---

## 🛠️ Persyaratan Sistem

- **PHP**: `8.2` atau lebih baru
- **Ekstensi PHP**: `intl`, `mbstring`, `mysqli`, `curl`, `fileinfo`, `dom`, `gd` / `exif`
- **Database**: MySQL `8.0+` atau MariaDB `10.4+` (Port 3306)
- **Node.js**: `v20.x` atau lebih baru (untuk frontend build & Baileys WA gateway)
- **Composer**: `v2.x`

---

## 📦 Panduan Instalasi Lokal

### 1. Clone Repository & Install Dependency
```bash
git clone https://github.com/synectra-jasa-digital/topup-hgi.git
cd topup-hgi

# Install dependency PHP & Node.js
composer install
npm ci
```

### 2. Konfigurasi Environment (`.env`)
Salin file `.env.example` atau `env` menjadi `.env`:
```bash
cp env .env
```
Sesuaikan variabel environment minimal:
```dotenv
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/topup-hgi/'

database.default.hostname = localhost
database.default.database = topup_hgi
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306

# Jangan pernah commit kunci asli ke version control.
# Buat kunci baru (64 hex) dengan:
#   php -r "echo 'hex2bin:' . bin2hex(random_bytes(32)) . PHP_EOL;"
encryption.key = hex2bin:<64-karakter-hex-acak>
```

> ⚠️ **Rotasi kunci**: kunci enkripsi lama pernah ter-commit ke repo. Jika repo pernah dipublikasikan, buat kunci baru, lalu simpan ulang kredensial terenkripsi di admin panel (WhatsApp Gateway API Key, dsb.) karena nilai lama tidak dapat didekripsi dengan kunci baru.

### 3. Migrasi Database & Seeder Admin Initial
```bash
php spark migrate --all
php spark db:seed AdminSeeder
```
*Akun bawaan seeder (hanya untuk instalasi lokal):*
- **Email**: `owner@gmail.com`
- **Password**: `password`
- **Role**: `owner`

> ⚠️ **Ganti password segera** setelah login pertama di lingkungan apa pun selain lokal. Jangan pernah biarkan kredensial bawaan ini aktif di produksi.

### 4. Setup Service WhatsApp Gateway (`wa-gateway/`)
```bash
cd wa-gateway
npm install
node server.js
```
*Service akan berjalan di `http://127.0.0.1:3000`.*

### 5. Build CSS & Jalankan Local Development Server
```bash
# Compile Tailwind CSS
npm run build:css

# Jalankan server lokal CodeIgniter
php spark serve
```
Buka browser pada `http://localhost:8080/`.

---

## ⚙️ Cron Workers & CLI Commands

Sistem memerlukan beberapa background worker yang dapat dikonfigurasi via Cron Job server (tiap 1 menit):

| Perintah CLI Spark | Kegunaan / Deskripsi | Frekuensi Rekomendasi |
| :--- | :--- | :--- |
| `php spark wa:outbox` | Memproses antrean pesan WhatsApp (`wa_outbox`) dengan retry backoff | Setiap 1 menit |
| `php spark telegram:outbox` | Memproses antrean pesan & foto bukti bayar ke Telegram admin | Setiap 1 menit |
| `php spark bongkar:notifications` | Mengirim ulang notifikasi pengajuan bongkar yang sempat gagal | Setiap 5 menit |
| `php spark uploads:audit` | Audit file upload yatim (tidak terdaftar di DB) | Mingguan / Manual |
| `php spark uploads:audit --delete` | Menghapus file upload yatim yang terdeteksi | Manual |

---

## 📁 Struktur Direktori Utama

```text
topup-hgi/
├── app/
│   ├── Commands/            # Perintah Spark CLI (wa:outbox, telegram:outbox, dll)
│   ├── Config/              # Konfigurasi aplikasi, database, route, & filter
│   ├── Controllers/         # Controller Publik, Checkout, & Admin Panel
│   ├── Database/            # Migrasi database & Seeder awal
│   ├── Filters/             # Auth filter, Maintenance filter, Role filter
│   ├── Helpers/             # Helper phone_helper, activity_helper, upload_helper
│   ├── Libraries/           # TelegramBot, TelegramNotifier, WhatsAppNotifier, BaileysGateway, ReportExporter
│   ├── Models/              # Model data CodeIgniter 4
│   └── Views/               # Template tampilan UI (Catalog, Checkout, Admin, Errors)
├── docs/                    # Dokumentasi spesifikasi, DESIGN.md, TODO.md, & SQL schema
├── public/                  # Document root web (index.php, CSS, JS, asset publik)
├── resources/               # Sumber Tailwind CSS (resources/css/public.css)
├── stitch/                  # Assets & UI screen references
├── tests/                   # Suite pengujian otomatis (Unit & Feature Tests)
├── wa-gateway/              # Service Node.js WhatsApp Baileys Gateway (server.js, package.json)
└── writable/                # File runtime, upload bukti bayar, log, & backup database
```

---

## 🧪 Pengujian Otomatis (Testing)

Proyek ini dilengkapi dengan 93+ pengujian otomatis (Unit Test & Feature Test) untuk menjamin stabilitas aplikasi.

Jalankan seluruh pengujian PHPUnit:
```bash
vendor/bin/phpunit
# atau via composer
composer test
```

---

## 🔒 Lisensi & Hak Cipta

© 2026 **Ayong Store**. Dikelola oleh Synectra Jasa Digital. Seluruh hak cipta dilindungi undang-undang.
