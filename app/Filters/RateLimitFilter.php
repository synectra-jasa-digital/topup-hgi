<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class RateLimitFilter implements FilterInterface
{
    private const DEFAULT_LIMIT = 60;
    private const DEFAULT_WINDOW = 60;

    public function before(RequestInterface $request, $arguments = null)
    {
        [$limit, $window] = $this->parseArguments($arguments);

        if ($limit <= 0 || $window <= 0) {
            return null;
        }

        $ip = $request->getIPAddress();
        if ($ip === '' || $ip === '0.0.0.0') {
            return null;
        }

        $cache = service('cache');
        $bucket = intdiv(time(), $window);
        // NOTE: cache keys must avoid CodeIgniter's reserved characters ({}()/\@:),
        // so the parts below are joined with "_" rather than ":".
        $key = sprintf(
            'rate_limit_%s_%s_%d',
            hash('sha256', $ip),
            sha1(strtolower($request->getMethod()) . ':' . trim($request->getUri()->getPath(), '/')),
            $bucket
        );

        $count = $cache->increment($key);
        if ($count === false) {
            return null;
        }

        // Some cache handlers give an incremented key a very long lifetime. Pin it to this window again,
        // with the same value, so stale counters do not pile up. The cache backend should be shared
        // (Redis/Memcached) when using multiple app instances.
        $cache->save($key, $count, $window + 1);

        if ($count > $limit) {
            return $this->tooManyRequests($request, max(1, ($bucket + 1) * $window - time()));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    /**
     * 429 with a Retry-After header. Scripts (fetch/XHR) get JSON; a person using a form or link gets a readable page.
     */
    public function tooManyRequests(RequestInterface $request, int $retryAfter): ResponseInterface
    {
        $response = service('response')
            ->setStatusCode(429)
            ->setHeader('Retry-After', (string) $retryAfter)
            ->setHeader('Cache-Control', 'no-store');

        $wantsJson = $request->isAJAX()
            || str_contains(strtolower($request->getHeaderLine('Accept')), 'application/json');

        if ($wantsJson) {
            return $response->setJSON(['error' => 'Terlalu banyak permintaan. Silakan coba lagi nanti.']);
        }

        return $response->setBody(view('errors/public_message', [
            'code'    => '429',
            'icon'    => 'hourglass_top',
            'heading' => 'Terlalu banyak percobaan',
            'message' => 'Untuk menjaga keamanan, permintaan Anda dibatasi sementara. Tunggu sekitar ' . $retryAfter . ' detik lalu coba lagi.',
            'actions' => [
                ['label' => 'Ke Beranda', 'href' => base_url('/'), 'primary' => true],
                ['label' => 'Cek Pesanan', 'href' => base_url('cek-pesanan')],
            ],
        ]));
    }

    /**
     * Supports: rate:60:60, where the values are requests and window seconds.
     * Invalid values fall back to safe defaults.
     *
     * @return array{0: int, 1: int}
     */
    private function parseArguments($arguments): array
    {
        $limit = self::DEFAULT_LIMIT;
        $window = self::DEFAULT_WINDOW;

        if (is_array($arguments) && isset($arguments[0])) {
            $parts = explode(':', (string) $arguments[0], 2);
            if (count($parts) === 2) {
                $parsedLimit = filter_var($parts[0], FILTER_VALIDATE_INT);
                $parsedWindow = filter_var($parts[1], FILTER_VALIDATE_INT);

                if ($parsedLimit !== false && $parsedLimit >= 0) {
                    $limit = $parsedLimit;
                }
                if ($parsedWindow !== false && $parsedWindow > 0 && $parsedWindow <= 86400) {
                    $window = $parsedWindow;
                }
            }
        }

        return [$limit, $window];
    }
}
