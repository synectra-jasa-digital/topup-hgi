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
                return '';
            }
        }

        return $environmentKey !== ''
            ? trim((string) (getenv($environmentKey) ?: ($_ENV[$environmentKey] ?? '')))
            : '';
    }

    public function encrypt(string $value): string
    {
        return service('encrypter')->encrypt($value);
    }
}
