<?php

// Shown for any page that does not exist, including an invoice link with a wrong token.
// The framework's own $message is left out on purpose: it can name the path that was tried.
$home = function_exists('base_url') ? base_url('/') : '/';
$check = function_exists('base_url') ? base_url('cek-pesanan') : '/cek-pesanan';

echo view('errors/public_message', [
    'code'    => '404',
    'icon'    => 'search_off',
    'heading' => 'Halaman tidak ditemukan',
    'message' => 'Alamat yang Anda buka tidak ada atau sudah dipindahkan. Bila Anda mencari pesanan, periksa nomor invoice dan token akses di halaman Cek Pesanan.',
    'actions' => [
        ['label' => 'Ke Beranda', 'href' => $home, 'primary' => true],
        ['label' => 'Cek Pesanan', 'href' => $check],
    ],
]);
