
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