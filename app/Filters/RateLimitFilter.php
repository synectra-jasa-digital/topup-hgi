<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Cache\CacheInterface;

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
        $key = sprintf(
            'rate_limit:%s:%s:%d',
            hash('sha256', $ip),
            sha1(strtolower($request->getMethod()) . ':' . trim($request->getUri()->getPath(), '/')),
            $bucket
        );

        $count = $cache->increment($key);
        if ($count === false) {
            return null;
        }
        if ($count === 1) {
            $cache->save($key, 1, $window + 1);
        }
        if ($count > $limit) {
            $retryAfter = max(1, ($bucket + 1) * $window - time());

            return service('response')
                ->setStatusCode(429)
                ->setHeader('Retry-After', (string) $retryAfter)
                ->setHeader('Cache-Control', 'no-store')
                ->setJSON([
                    'error' => 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
                ]);
        }

        // The short TTL prevents stale counters from accumulating in the cache.
        // The cache backend should be shared (Redis/Memcached) when using multiple app instances.
        $cache->save($key, $count + 1, $window + 1);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
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
