<?php

if (! function_exists('bongkar_status_label')) {
    function bongkar_status_label(string $status): string
    {
        return [
            'pending'  => 'Menunggu Diproses',
            'diproses' => 'Diproses',
            'selesai'  => 'Selesai',
            'ditolak'  => 'Ditolak',
        ][$status] ?? $status;
    }
}

if (! function_exists('bongkar_status_badge_class')) {
    function bongkar_status_badge_class(string $status): string
    {
        return [
            'pending'  => 'badge-warning',
            'diproses' => 'badge-primary',
            'selesai'  => 'badge-success',
            'ditolak'  => 'badge-danger',
        ][$status] ?? 'badge-primary';
    }
}
