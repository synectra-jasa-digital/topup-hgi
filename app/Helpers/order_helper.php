<?php

if (! function_exists('whatsapp_url')) {
    /**
     * wa.me needs the country code and no leading 0, but store contacts are typed the local way (08xx).
     * Returns '' when there is no usable number, so callers can hide the link instead of shipping a dead one.
     */
    function whatsapp_url(?string $contact, string $text = ''): string
    {
        if (! function_exists('normalize_phone')) {
            helper('phone');
        }

        $digits = normalize_phone($contact);
        if ($digits === '') {
            return '';
        }

        return 'https://wa.me/' . $digits . ($text !== '' ? '?text=' . rawurlencode($text) : '');
    }
}

if (! function_exists('order_status_label')) {
    function order_status_label(string $status): string
    {
        return [
            'menunggu_pembayaran'  => 'Menunggu Pembayaran',
            'menunggu_verifikasi'  => 'Menunggu Verifikasi',
            'diproses'             => 'Diproses',
            'selesai'              => 'Selesai',
            'gagal'                => 'Gagal',
            'dibatalkan'           => 'Dibatalkan',
        ][$status] ?? $status;
    }
}

if (! function_exists('order_status_badge_class')) {
    function order_status_badge_class(string $status): string
    {
        return [
            'menunggu_pembayaran' => 'badge-warning',
            'menunggu_verifikasi' => 'badge-warning',
            'diproses'            => 'badge-primary',
            'selesai'             => 'badge-success',
            'gagal'               => 'badge-danger',
            'dibatalkan'          => 'badge-danger',
        ][$status] ?? 'badge-primary';
    }
}

if (! function_exists('order_status_stages')) {
    /**
     * The four steps an order normally moves through, in order.
     * "gagal" and "dibatalkan" are outside this path.
     *
     * @return list<string>
     */
    function order_status_stages(): array
    {
        return ['menunggu_pembayaran', 'menunggu_verifikasi', 'diproses', 'selesai'];
    }
}

if (! function_exists('order_status_ui')) {
    /**
     * Icon and Tailwind classes for the status badge on the public pages.
     * The admin pages use order_status_badge_class() instead.
     *
     * @return array{icon: string, class: string}
     */
    function order_status_ui(string $status): array
    {
        return [
            'menunggu_pembayaran' => ['icon' => 'schedule', 'class' => 'bg-amber-100 text-amber-900 ring-amber-300'],
            'menunggu_verifikasi' => ['icon' => 'hourglass_top', 'class' => 'bg-amber-100 text-amber-900 ring-amber-300'],
            'diproses'            => ['icon' => 'autorenew', 'class' => 'bg-blue-100 text-blue-900 ring-blue-300'],
            'selesai'             => ['icon' => 'check_circle', 'class' => 'bg-emerald-100 text-emerald-900 ring-emerald-300'],
            'gagal'               => ['icon' => 'error', 'class' => 'bg-rose-100 text-rose-900 ring-rose-300'],
            'dibatalkan'          => ['icon' => 'cancel', 'class' => 'bg-rose-100 text-rose-900 ring-rose-300'],
        ][$status] ?? ['icon' => 'info', 'class' => 'bg-blue-100 text-blue-900 ring-blue-300'];
    }
}

if (! function_exists('order_status_note')) {
    /**
     * One plain sentence about what a status means for the buyer. "menunggu_pembayaran" has no note here
     * because the invoice shows the payment details and the upload form for it instead.
     */
    function order_status_note(string $status): string
    {
        return [
            'menunggu_verifikasi' => 'Bukti pembayaran sedang diperiksa admin. Buka halaman ini lagi kapan saja untuk melihat perkembangannya.',
            'diproses'            => 'Pembayaran sudah diverifikasi. Admin sedang memproses pesanan ini.',
            'selesai'             => 'Admin telah menandai pesanan ini selesai.',
            'gagal'               => 'Pesanan ini gagal diproses. Bila sudah membayar, hubungi CS dan sebutkan nomor invoice.',
            'dibatalkan'          => 'Pesanan ini dibatalkan. Bila masih ingin membeli, buat pesanan baru dari katalog.',
        ][$status] ?? '';
    }
}
