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
        if ($path === '' || str_starts_with($path, 'admin') || str_starts_with($path, 'login') || str_starts_with($path, 'cek-pesanan')) {
            return null;
        }

        if (session()->get('admin_id')) {
            return null;
        }

        $settings = new StoreSettingModel();

        if ($settings->getVal('maintenance', '0') !== '1') {
            return null;
        }

        helper(['order', 'url']);

        $contact = $settings->getVal('store_contact');
        $waUrl   = ! empty($contact) ? whatsapp_url($contact) : '';

        $html = view('errors/html/maintenance', [
            'storeName' => $settings->getVal('store_name', 'Ayong Store'),
            'storeLogo' => $settings->getVal('store_logo'),
            'reason'    => $settings->getVal('maintenance_reason'),
            'waUrl'     => $waUrl,
        ]);

        return service('response')
            ->setStatusCode(503)
            ->setHeader('Retry-After', '3600')
            ->setHeader('Content-Type', 'text/html; charset=UTF-8')
            ->setBody($html);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
