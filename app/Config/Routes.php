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
});
