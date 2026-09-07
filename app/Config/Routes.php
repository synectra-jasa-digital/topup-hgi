<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('kategori/(:segment)', 'Home::kategori/$1');

$routes->get('checkout/(:num)', 'OrderController::create/$1');
$routes->post('checkout/(:num)', 'OrderController::store/$1');
$routes->get('pesanan/(:segment)', 'OrderController::invoice/$1');
$routes->post('webhook/midtrans', 'MidtransController::webhook');

$routes->get('cek-pesanan', 'OrderController::checkStatus');
$routes->post('cek-pesanan', 'OrderController::processCheckStatus');

$routes->get('login', 'Admin\AuthController::loginForm');
$routes->post('login', 'Admin\AuthController::login');
$routes->get('admin/login', 'Admin\AuthController::loginForm');
$routes->post('admin/login', 'Admin\AuthController::login');

$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    $routes->get('logout', 'Admin\AuthController::logout');
    $routes->get('dashboard', 'Admin\DashboardController::index');

    $routes->get('kategori-produk', 'Admin\ProductCategoryController::index');
    $routes->get('kategori-produk/tambah', 'Admin\ProductCategoryController::create');
    $routes->post('kategori-produk/tambah', 'Admin\ProductCategoryController::store');
    $routes->get('kategori-produk/(:num)/ubah', 'Admin\ProductCategoryController::edit/$1');
    $routes->post('kategori-produk/(:num)/ubah', 'Admin\ProductCategoryController::update/$1');
    $routes->post('kategori-produk/(:num)/hapus', 'Admin\ProductCategoryController::delete/$1');

    $routes->get('produk', 'Admin\ProductController::index');
    $routes->get('produk/tambah', 'Admin\ProductController::create');
    $routes->post('produk/tambah', 'Admin\ProductController::store');
    $routes->get('produk/(:num)/ubah', 'Admin\ProductController::edit/$1');
    $routes->post('produk/(:num)/ubah', 'Admin\ProductController::update/$1');
    $routes->post('produk/(:num)/hapus', 'Admin\ProductController::delete/$1');

    $routes->get('kategori-banner', 'Admin\BannerCategoryController::index');
    $routes->get('kategori-banner/tambah', 'Admin\BannerCategoryController::create');
    $routes->post('kategori-banner/tambah', 'Admin\BannerCategoryController::store');
    $routes->get('kategori-banner/(:num)/ubah', 'Admin\BannerCategoryController::edit/$1');
    $routes->post('kategori-banner/(:num)/ubah', 'Admin\BannerCategoryController::update/$1');
    $routes->post('kategori-banner/(:num)/hapus', 'Admin\BannerCategoryController::delete/$1');

    $routes->get('banner', 'Admin\BannerController::index');
    $routes->get('banner/tambah', 'Admin\BannerController::create');
    $routes->post('banner/tambah', 'Admin\BannerController::store');
    $routes->get('banner/(:num)/ubah', 'Admin\BannerController::edit/$1');
    $routes->post('banner/(:num)/ubah', 'Admin\BannerController::update/$1');
    $routes->post('banner/(:num)/hapus', 'Admin\BannerController::delete/$1');

    // Fase 10 - Pesanan
    $routes->get('pesanan', 'Admin\OrderController::index');
    $routes->get('pesanan/(:num)', 'Admin\OrderController::show/$1');
    $routes->post('pesanan/(:num)/selesai', 'Admin\OrderController::complete/$1');

    // Fase 11a - Voucher
    $routes->get('voucher', 'Admin\VoucherController::index');
    $routes->get('voucher/tambah', 'Admin\VoucherController::create');
    $routes->post('voucher/tambah', 'Admin\VoucherController::store');
    $routes->get('voucher/(:num)/ubah', 'Admin\VoucherController::edit/$1');
    $routes->post('voucher/(:num)/ubah', 'Admin\VoucherController::update/$1');
    $routes->post('voucher/(:num)/hapus', 'Admin\VoucherController::delete/$1');

    // Fase 11b - Log Aktivitas
    $routes->get('log-aktivitas', 'Admin\ActivityLogController::index');

    // Fase 11c - Halaman Statis
    $routes->get('halaman-statis', 'Admin\StaticPageController::index');
    $routes->get('halaman-statis/tambah', 'Admin\StaticPageController::create');
    $routes->post('halaman-statis/tambah', 'Admin\StaticPageController::store');
    $routes->get('halaman-statis/(:num)/ubah', 'Admin\StaticPageController::edit/$1');
    $routes->post('halaman-statis/(:num)/ubah', 'Admin\StaticPageController::update/$1');
    $routes->post('halaman-statis/(:num)/hapus', 'Admin\StaticPageController::delete/$1');

    // Fase 11d - Akun Admin (Owner only)
    $routes->get('akun-admin', 'Admin\AdminAccountController::index', ['filter' => 'role:owner']);
    $routes->get('akun-admin/tambah', 'Admin\AdminAccountController::create', ['filter' => 'role:owner']);
    $routes->post('akun-admin/tambah', 'Admin\AdminAccountController::store', ['filter' => 'role:owner']);
    $routes->get('akun-admin/(:num)/ubah', 'Admin\AdminAccountController::edit/$1', ['filter' => 'role:owner']);
    $routes->post('akun-admin/(:num)/ubah', 'Admin\AdminAccountController::update/$1', ['filter' => 'role:owner']);
    $routes->post('akun-admin/(:num)/hapus', 'Admin\AdminAccountController::delete/$1', ['filter' => 'role:owner']);

    // Fase 12 - Laporan (Owner only)
    $routes->get('laporan', 'Admin\ReportController::index', ['filter' => 'role:owner']);
    $routes->get('laporan/export/excel', 'Admin\ReportController::exportExcel', ['filter' => 'role:owner']);
    $routes->get('laporan/export/pdf', 'Admin\ReportController::exportPdf', ['filter' => 'role:owner']);

    // Fase 13 - Pengaturan Toko (Owner only)
    $routes->get('pengaturan-toko', 'Admin\StoreSettingController::index', ['filter' => 'role:owner']);
    $routes->post('pengaturan-toko', 'Admin\StoreSettingController::update', ['filter' => 'role:owner']);
});
