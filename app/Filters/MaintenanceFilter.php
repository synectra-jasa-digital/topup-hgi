<?php

namespace App\Filters;

use App\Models\StoreSettingModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class MaintenanceFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $path = trim($request->getUri()->getPath(), '/');
        if ($path === '' || str_starts_with($path, 'admin') || str_starts_with($path, 'login')) {
            return null;
        }

        if (session()->get('admin_id')) {
            return null;
        }

        $maintenance = (new StoreSettingModel())->getVal('maintenance', '0');
        if ($maintenance !== '1') {
            return null;
        }

        return service('response')->setStatusCode(503)->setHeader('Retry-After', '3600')->setBody(
            '<!doctype html><html lang="id"><head><meta charset="utf-8"><title>Pemeliharaan</title></head><body><h1>Situs sedang dalam pemeliharaan</h1><p>Silakan coba lagi nanti.</p></body></html>'
        );
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
