<?php

namespace App\Libraries;

use App\Models\StoreSettingModel;

final class IntegrationSettings
{
    public function __construct(private ?StoreSettingModel $settings = null)
    {
        $this->settings ??= new StoreSettingModel();
    }

    public function get(string $key, string $environmentKey = ''): string
    {
        $stored = $this->settings->getVal($key);
        if ($stored !== '') {
            try {
                return (string) service('encrypter')->decrypt($stored);
            } catch (\Throwable) {
                // Stored value not encrypted (plain) or key mismatch — return as-is
                return $stored;
            }
        }

        return $environmentKey !== ''
            ? trim((string) (getenv($environmentKey) ?: ($_ENV[$environmentKey] ?? '')))
            : '';
    }

    public function set(string $key, string $value): void
    {
        try {
            $stored = service('encrypter')->encrypt($value);
        } catch (\Throwable) {
            // Encrypter not configured on this host — store plain text
            $stored = $value;
        }

        $this->settings->setVal($key, $stored);
    }

    public function encrypt(string $value): string
    {
        try {
            return service('encrypter')->encrypt($value);
        } catch (\Throwable) {
            return $value;
        }
    }
}
