<?php

if (! function_exists('order_status_label')) {
    function order_status_label(string $status): string
    {
        return [
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'dibayar'             => 'Dibayar',
            'diproses'            => 'Diproses',
            'selesai'             => 'Selesai',
            'gagal'               => 'Gagal',
            'dibatalkan'          => 'Dibatalkan',
        ][$status] ?? $status;
    }
}

if (! function_exists('order_status_badge_class')) {
    function order_status_badge_class(string $status): string
    {
        return [
            'menunggu_pembayaran' => 'badge-warning',
            'dibayar'             => 'badge-primary',
            'diproses'            => 'badge-primary',
            'selesai'             => 'badge-success',
            'gagal'               => 'badge-danger',
            'dibatalkan'          => 'badge-danger',
        ][$status] ?? 'badge-primary';
    }
}
