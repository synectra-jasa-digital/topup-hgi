# Roadmap Perbaikan — Ayong Store

Dokumen ini adalah sumber kebenaran untuk pekerjaan perbaikan yang sedang dan
akan datang. Berbeda dengan `TODO.md` (catatan build historis), dokumen ini
hanya berisi item yang **sudah diverifikasi** pada kode yang ada, dengan bukti
dan cara memverifikasinya ulang.

Kabung commit per item. Satu item = satu perubahan yang bisa di-revert sendiri.

---

## Status saat ini

- Test suite: **160 test, 415 assertion, hijau**
- Quality gate: CI/CD menjalankan `composer test` sebelum FTP deploy

---

## ✅ Batch 1 — Blocker keamanan (selesai)

### 1. Path traversal pada export laporan

**Commit:** `47d5682`
**Bukti:** `ReportExporter::tempFilePathFor()` + `tests/unit/ReportPeriodValidationTest.php` (18 test)

`period` datang dari dua sumber yang bisa dikendalikan penyerang — query string
`/admin/laporan/export/*` dan callback data Telegram `laporan:<format>:<period>`.
Keduanya masuk ke nama file temp tanpa validasi, jadi nilai seperti
`../../../../x` menulis di luar `sys_get_temp_dir()`, lalu dibaca kembali oleh
controller dan di-echo (write + read di luar temp dir).

Perbaikan: allowlist di dalam `ReportExporter` (`isValidPeriod` /
`normalizePeriod`), bukan di controller — karena Telegram adalah pemanggil kedua
dengan paparan yang sama. Nama file sekarang dari `random_bytes()`, tanpa
`period` sama sekali, sehingga nilai tak tervalidasi tidak bisa sampai ke path
bila ada pemanggil yang terlewat. Temp file juga dihapus setelah diunduh
(sebelumnya bocor satu file per unduhan).

### 2. Telegram bot melewati batas owner-only

**Commit:** `47d5682`
**Bukti:** `tests/Feature/TelegramOwnerOnlyCommandsTest.php` (5 test)

Web panel membatasi `/laporan` dan `/admin/pengaturan-toko` dengan `role:owner`,
tapi perintah Telegram yang setara hanya memanggil `requireLogin()`.
`requireRole()` sudah ada dan sudah dipakai satu perintah — ini kelalaian, bukan
kemampuan yang belum ada. Admin non-owner yang punya sesi bot bisa menarik
laporan penjualan penuh dan mematikan seluruh toko.

`Close`/`Open` ikut diperbaiki karena menulis setting `maintenance` yang sama.

### 3. AuthFilter percaya snapshot sesi

**Commit:** `494f313`
**Bukti:** `tests/Feature/AuthFilterRevalidationTest.php` (5 test)

`AuthFilter` hanya mengecek `session('admin_id')` tanpa membaca ulang tabel
`admins`. `admin_id`, `admin_role`, `admin_name` adalah snapshot sejak login dan
hidup selama sesi (2 jam), jadi menonaktifkan, menghapus, atau menurunkan role
sebuah akun tidak berefek sampai sesi kedaluwarsa.

Sekarang baris admin dibaca ulang tiap request dan role di-refresh, sehingga
`RoleFilter` juga tidak bisa memakai role basi. Jalur Telegram sudah melakukan
re-check ini sebelumnya — jadi ini menyamakan, bukan perilaku baru.

### 4. Rute yang menyentuh uang belum owner-only

**Commit:** `da8457c`
**Bukti:** `tests/Feature/OwnerOnlyRouteCoverageTest.php` (15 test)

`metode-bayar` sudah owner-only, tapi rute lain yang menggerakkan atau
memperkirakan uang customer tidak:

| Rute | Kenapa berbahaya |
| :--- | :--- |
| `bongkar-metode-pencairan` | Tujuan dana customer |
| `bongkar-katalog` | `base_rate` = penilaian kartu |
| `voucher` | Nilai diskon & kuota |

`calculateDiscount` hanya membatasi ke subtotal, jadi admin non-owner bisa membuat
voucher 100% dan memberi pesanan gratis.

### 5. Input voucher tidak divalidasi

**Commit:** `da8457c`
**Bukti:** `tests/Feature/VoucherInputValidationTest.php` (9 test)

`voucherData()` melakukan cast langsung ke `(float)`, sehingga `"abc"` menjadi
`0` dan nilai negatif lolos. Percentage `100` atau lebih juga diterima.

Sekarang: `is_numeric()` sebelum cast, batas percentage `0 < value <= 100`,
nominal/min_purchase/max_discount/quota tidak boleh negatif, `type` harus sesuai
enum, dan `end_date >= start_date`.

### 6. Kredensial owner tidak boleh ada di repo

**Commit:** (pending — sedang dikerjakan)
**Bukti:** `app/Database/Seeds/AdminSeeder.php`, `README.md`, `.env.example`

`AdminSeeder` sebelumnya membuat akun `owner@gmail.com` dengan password
`password` yang tertulis di README repo publik. Sekarang seeder menolak berjalan
tanpa `SEED_OWNER_EMAIL` + `SEED_OWNER_PASSWORD` (minimal 16 karakter), dibuat
idempoten, dan kredensial tidak lagi tertulis di README.

`.env.example` sekarang di-commit sebagai template berisi placeholder.
Instruksi install yang menunjuk ke file `env` yang tidak pernah ada sudah
diperbaiki.

---

## Batch 2 — Kebenaran jalur uang

> Semua money-path wajib punya test sebelum dianggap selesai. Urutannya penting:
> tulis test dulu, lalu perbaiki apa yang ditunjukkan test itu.

### 7. `rejectPayment` tidak melepas reservasi voucher

**Status:** belum dikerjakan
**Bukti:** `app/Models/OrderModel.php:79-88`

`rejectPayment` mengembalikan status ke `menunggu_pembayaran` tapi tidak
menyentuh `voucher_reserved` maupun `voucher_reserved_until`. Customer yang
ditolak tetap mengunci vouchernya selama masa tahan 900 detik
(`OrderController.php:18`). `payment_proof_path` juga tidak dikosongkan —
bandingkan `uploadPaymentProof` yang membersihkan `payment_rejection_reason`.

### 8. `verifyPayment` tidak atomik

**Status:** belum dikerjakan
**Bukti:** `app/Models/OrderModel.php:56-77`

Status order (`:61-68`) dan `commitReservation()` (`:73`) berjalan tanpa
transaksi. Worse, `voucher_committed => 1` ditulis **sebelum** commit nyata
dan ditulis walau tidak ada voucher sama sekali.

Perbaikan: bungkus dalam `transStart`/`transComplete`, dan hanya set
`voucher_committed` bila commit benar-benar sukses.

### 9. `telegram:outbox` tidak punya claim step

**Status:** belum dikerjakan
**Bukti:** `app/Libraries/../Models/TelegramOutboxModel.php:36-42`

`getPending()` hanya membaca; tidak ada yang menandai `processing` sebelum kirim.
Dua cron yang overlap akan mengirim 50 pesan yang sama ke customer.
`WaOutboxModel::claim()` sudah punya pola atomic yang benar — pakai itu sebagai
template. `TelegramOutboxModel::markFailed` juga belum punya backoff
(`WaOutboxModel` sudah: `120 * pow(2, $attempts-1)`).

### 10. `OrderModel::releaseExpiredVoucherReservations` transaksi per baris

**Status:** belum dikerjakan
**Bukti:** `app/Models/OrderModel.php:131-143`

`findAll()` tanpa LIMIT, lalu satu `transStart`/`transComplete` per order di
dalam loop. Seharusnya satu transaksi untuk seluruh batch + batas baris.

### 11. Cakupan test `uploadPaymentProof` masih kosong

**Status:** belum dikerjakan
**Bukti:** `app/Controllers/OrderController.php:200-263`

Validasi aturan, gate status, pemindahan file, dan pembersihan file lama belum
punya test. Ini satu-satunya endpoint publik yang menerima file dari stranger.

---

## Batch 3 — Database & performa

### 12. `orders.voucher_reserved` tanpa index

**Status:** belum dikerjakan · **Prioritas tertinggi Batch 3**

**Bukti:** `app/Database/Migrations/2026-09-14-110000_AddVoucherReservations.php:12-16`

Kolom dibuat tanpa index, padahal query
`WHERE voucher_reserved = 1 AND voucher_reserved_until < NOW()` dipanggil di
**setiap POST checkout** (`OrderController.php:72`) — full scan tabel tercepat
tumbuh, selamanya.

```php
$this->forge->addKey(['voucher_reserved', 'voucher_reserved_until'], false, false, 'idx_orders_voucher_hold');
```

### 13. `wa_outbox` tanpa index `created_at` dan row tidak pernah dihapus

**Status:** belum dikerjakan

**Bukti:** `app/Database/Migrations/2026-09-30-100000_CreateWaOutboxTable.php:32-35`,
`app/Models/WaOutboxModel.php:125-136`

`cleanupOldMessages` berjalan **setiap menit**, melakukan full scan + unbounded
UPDATE, dan hanya mengosongkan `message` tanpa menghapus baris → tabel tumbuh
tanpa batas.

### 14. `payment_verified_at` tanpa index

**Status:** belum dikerjakan

**Bukti:** `app/Models/ReportModel.php:26-33`,
`app/Database/Migrations/2026-09-15-100100_AddPaymentFieldsToOrders.php:22`

Query `dailySummary()` memakai `OR` di kolom yang tidak ter-index sehingga
degradasi ke full scan. Dipanggil **11× per halaman** laporan/dashboard
(`ReportController.php:20-25` memanggil loop 7× + 2, dan `dailySummary(1)` sudah
dihitung ulang di dalam loop).

Perbaikan: tambahkan index, dan gunakan `revenueRange()` yang sudah tersedia
dengan satu grouped query.

### 15. `bongkar_requests` notification columns tanpa index

**Status:** belum dikerjakan

**Bukti:** `app/Database/Migrations/2026-09-07-120100_CreateBongkarRequestsTable.php:28-31`

`findNotificationQueue()` melakukan full scan + filesort tiap cron run.

### 16. N+1 di beranda dan query setting tanpa cache

**Status:** belum dikerjakan

**Bukti:** `app/Controllers/Home.php:24-30` (1 query per kategori),
`app/Models/StoreSettingModel.php:16-20` (`getVal()` satu query per kunci;
`getAll()` sudah ada tapi tidak terpakai)

Sekitar 9 query `store_settings` untuk satu render beranda.

---

## Batch 4 — Integritas dokumentasi

> Prinsip: dokumen yang mengklaim sesuatu yang tidak ada lebih berbahaya dari
> dokumen yang tidak ada. `-->` tandai `[x]` hanya bila sudah diverifikasi.

### 17. Klaim README yang tidak benar

**Status:** belum dikerjakan

| Klaim | Kenyataan |
| :--- | :--- |
| Ekspor memakai `PhpSpreadsheet` | Tidak ada di `composer.json`. `exportExcelFile()` menulis SpreadsheetML 2003 dengan ekstensi `.xlsx` (`ReportExporter.php:105`) → Excel memberi warning format |
| `cp env .env` | Tidak ada file `env` maupun `.env.example` → **sudah diperbaiki** di commit ini |
| 93+ pengujian | Benar (93 test); sekarang 160 |

### 18. Checkmark TODO.md yang tidak sesuai implementasi

**Status:** belum dikerjakan

| Item | Kenyataan |
| :--- | :--- |
| `[x] Migration: order_payments` | Tabel `order_payments` tidak pernah ada |
| `[x] Fase 7 Midtrans Snap + signature webhook` | Nol referensi Midtrans di seluruh `app/` — pembayaran sekarang manual |
| `[x] H7.2 Jangan simpan credential plaintext` | Dilanggar oleh fallback senyap `IntegrationSettings` (sudah sebagian diperbaiki commit `4868be2`) |
| `[x] H7.5 Test maintenance mode` | Test tersebut tidak ada |

### 19. `docs/telegram_tables.sql` usang

**Status:** belum dikerjakan

Menyuruh "Jalankan di phpMyAdmin" untuk 4 tabel yang **sudah** punya migration
(`2026-09-29-100000` s.d. `100003`), dan tidak menyebut `wa_outbox` sama sekali.
Jalur DDL manual yang paralel dengan `php spark migrate` = drift schema.

### 20. `laminas/laminas-escaper` dependency yang tidak terpakai

**Status:** belum dikerjakan

Runtime dependency di `composer.json` yang nol referensi di seluruh `app/`.

---

## Batch 5 — Pipeline & operasional

### 21. Deploy tidak pernah menjalankan migration

**Status:** belum dikerjakan

**Bukti:** `.github/workflows/deploy.yml:55-119`

Setiap push ke `main` memicu deploy otomatis, tapi tidak ada `php spark migrate`.
Semua perubahan schema sejak migrasi manual terakhir tertidur di repo.

### 22. `dangerous-clean-slate: false` ⇒ kode terhapus tak pernah hilang

**Status:** belum dikerjakan

**Bukti:** `.github/workflows/deploy.yml:93-101`

Alasannya sangat masuk akal (tidak menghapus `writable/`), tapi konsekuensinya:
file yang dihapus dari git **tetap live** di production. Direktori eksploitasi
yang sudah diperbaiki tidak akan hilang dari server hanya dengan commit.

### 23. Tidak ada `concurrency:` guard

**Status:** belum dikerjakan

Dua push berdekatan menjalankan dua deploy FTP yang saling tumpang tindih.

### 24. `stitch/` ikut ter-deploy ke production

**Status:** belum dikerjakan

8,1 MB dari 15,4 MB repo adalah mockup desain Stitch. Tidak ada di daftar
`exclude` `deploy.yml`, dan karena `.htaccess` memakai `!-f`, file yang ada di
`/public_html/` tersaji langsung ke publik.

### 25. Tidak ada static analysis sama sekali

**Status:** belum dikerjakan

`php-cs-fixer`, `codeigniter/coding-standard`, `nexusphp/cs-config` sudah
terpasang di `require-dev` tapi **tidak punya file konfigurasi** dan tidak
dipakai di CI. `\Config\Services` masih stub framework ⇒ tidak ada DI sama
sekali, semua library di-`new` inline (akar duplikasi yang ditemukan audit arsitektur).

---

## Batch 6 — Refactor struktural (setelah stabil)

> Semua item di bawah adalah duplikasi terverifikasi. Urut dari leverage
> tertinggi. Jangan dikerjakan sebelum Batch 2 selesai.

### 26. `telegram/webhook` tidak punya filter rate limit

**Status:** belum dikerjakan

Satu-satunya POST publik tanpa `['filter' => 'ratelimit:...']`. Rate limiter
dalam-controller memakai `tg_ratelimit_<user_id>` yang berasal dari body
request (`TelegramWebhookController.php:57-61`) — penyerang bisa memberi id baru
tiap request dan mendapat anggaran 5 percobaan login + 30/menit baru.

### 27. Path bukti pembayaran duplikat di 6 tempat

**Status:** belum dargestikan

`WRITEPATH . 'uploads/payment-proofs/'` di `OrderController.php:16`,
`Admin/OrderController.php:61` & `:107`, `TelegramNotifier.php:52`,
`OrderCommand.php:62`, `PendingCommand.php:49` — tiga di antaranya memakai
fallback `is_file()` identik. Salah edit = admin diberi "bukti bayar tidak
ditemukan".

### 28. 12 dari 21 admin controller adalah klon CRUD

**Status:** belum dikerjakan

Skeleton sama dengan nama tabel yang berbeda, plus 5 method upload yang
identik (`BannerController.php:132`, `ProductCategoryController.php:112`,
`PaymentChannelController.php:144`, `ProfileController.php:91`,
`StoreSettingController.php:69`) dan 12 index view yang diklon.

`upload_helper.php` sudah memiliki operasi baca/validasi/hapus tapi **tidak**
memiliki operasi tulis — itulah alasan kelima klon itu ada.

### 29. `requireRole($update, $session, ['owner'])` ditulis 32× di Routes.php

**Status:** belum dikerjakan

Sudah terpecah jadi blok owner-only, tapi masih diulang per baris. Group
bersarang akan menyatakannya sekali.

### 30. `WhatsAppNotifier` punya 6 method 30-baris yang identik

**Status:** belum dikerjakan

`orderCreated`, `paymentReceived`, `paymentVerified`, `orderCompleted`
byte-for-byte sama kecuali string `$type`. Mengcollapsed menjadi satu
`dispatch()`.

### 31. Dua definisi "revenue" yang berbeda

**Status:** belum dikerjakan

`ReportModel.php:25` memfilter `status = 'selesai'`;
`ReportExporter.php:60` memakai `whereIn(['diproses','selesai'])`.
Dashboard dan spreadsheet yang diekspor bisa **saja berbeda**.
`ReportExporter` juga menerima `?ReportModel` lalu mengabaikannya dan memakai
`\Config\Database::connect()` langsung.

### 32. Dua implementasi gateway dengan pesan duplikat

**Status:** belum dikerjakan

`WablasGateway.php:72-84,125-140` dan `WhatsApp/MessageTemplates.php:69-105`
memuat teks pesan customer yang sama dalam dua tempat yang berbeda.

### 33. Migration: 3 backfill duplikat + 1 migration no-op

**Status:** belum dikerjakan

`AddPublicAccessTokenToOrders.php:16-19`,
`BackfillPaymentAccessTokens.php:15-23`, `AddManualPaymentSnapshot.php:28-30`
— ketiganya menjalankan loop backfill identik pada kolom yang sama.
`DropPublicAccessTokenFromOrders` tidak melakukan apa-apa tapi tetap mengurutkan
sebelum migration yang masihonzhenguard kolom tersebut.

### 34. Tidak ada schema doc yang cocok dengan 33 migration

**Status:** belum dikerjakan

`docs/` tidak memuat satu pun dokumen skema saat ini.

### 35. `order_helper.php` memiliki `whatsapp_url()`

**Status:** belum dikerjakan

Feature-specific URL builder di dalam order helper, dipanggil dari `Home.php`,
`BongkarController.php:93`, `MaintenanceFilter.php:32`, dan 4 view.

### 36. Tidak ada test JS; `wa-gateway` di-exclude dari deploy

**Status:** belum dikerjakan

`wa-gateway/package.json` punya 1 script dan nol `devDependencies`. Tidak ada
file `*.test.js` di repo. `deploy.yml:116-117` mengecualikannya, jadi komponen
yang benar-benar mengirim setiap pesan customer tidak pernah melewati CI dan
tidak punya test. Tidak ada `package-lock.json` → Baileys tidak ter-pin.

---

## Semua area yang sudah diverifikasi BERSIH

Dicatat eksplisit agar effort tidak salah arah:

- **SQL injection:** nol yang terjangkau user. 4 `->query()` semuanya dari
  `$db->listTables()` atau konstanta migration.
- **Command injection:** nol. Tidak ada `shell_exec`/`exec`/`system`/`backtick`
  di `app/`; `wa-gateway` tidak memakai `child_process`.
- **Mass assignment:** nol. Semua controller memakai allow-list eksplisit.
  `AdminModel::$allowedFields` memuat `role`/`is_active`, tapi
  `ProfileController` hanya pernah mengirim `id/name/email/password/photo` dan
  `id`-nya dari session.
- **XSS:** nol yang terjangkau user. `esc()` eksplisit di setiap field
  user-supplied; tidak ada `{!! !!}`.
- **Model token invoice:** benar. 64 hex dari `random_bytes`, dibandingkan via
  SQL `WHERE` (bukan `==`), guard token kosong, rate-limited, `no-store` +
  `Referrer-Policy: same-origin`.
- **Upload:** berbasis konten (`finfo`), nama file selalu server-generated,
  SVG tidak pernah diterima, `public/assets/uploads/.htaccess` memblokir
  eksekusi.
- **Race order:** semua transisi status memakai conditional UPDATE +
  `affectedRows() === 1`. `VoucherModel::reserve()` memakai atomic conditional
  increment, bukan read-modify-write.

---

## Cara verifikasi

```bash
# Test suite (harus hijau sebelum setiap commit)
vendor/bin/phpunit --no-coverage

# Test yang mengunci item Batch 1
vendor/bin/phpunit --no-coverage tests/unit/ReportPeriodValidationTest.php
vendor/bin/phpunit --no-coverage tests/Feature/TelegramOwnerOnlyCommandsTest.php
vendor/bin/phpunit --no-coverage tests/Feature/AuthFilterRevalidationTest.php
vendor/bin/phpunit --no-coverage tests/Feature/OwnerOnlyRouteCoverageTest.php
vendor/bin/phpunit --no-coverage tests/Feature/VoucherInputValidationTest.php
```

Setiap item di atas harus punya test yang **gagal sebelum** perbaikannya dan
**lulus sesudahnya**. Test yang langsung lulus hanya membuktikan tidak ada yang
rusak — bukan bahwa bug-nya pernah ada.