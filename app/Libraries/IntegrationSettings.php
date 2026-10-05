<?php

namespace App\Libraries;

use App\Models\StoreSettingModel;

/**
 * Penyimpanan rahasia (API key, token) di store_settings.
 *
 * Ciphertext dibungkus base64 sebelum masuk kolom TEXT: driver MySQLi/SQLite3
 * mengikat string dengan panjang C-string sehingga nilai binary terpotong pada
 * byte NUL — round-trip enkripsi tidak akan pernah jalan tanpa encoding ini.
 *
 * Kegagalan enkripsi saat menulis FAIL-CLOSED (lempar exception), tidak pernah
 * jatuh ke penyimpanan plaintext diam-diam.
 */
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
            $plain = $this->decryptStored($stored);
            if ($plain !== null) {
                return $plain;
            }

            // Plaintext lama, encryption.key berganti, atau nilai terkorupsi.
            log_message('error', 'IntegrationSettings: nilai "{key}" tidak dapat didekripsi (plaintext lama / kunci berganti / korupsi). Simpan ulang nilai ini lewat panel admin.', ['key' => $key]);

            return $stored;
        }

        return $this->envValue($environmentKey);
    }

    /**
     * Simpan nilai rahasia — terenkripsi, base64-wrapped.
     *
     * @throws \RuntimeException bila enkripsi tidak tersedia (tidak menyimpan plaintext).
     */
    public function set(string $key, string $value): void
    {
        $this->settings->setVal($key, $this->encrypt($value));
    }

    public function encrypt(string $value): string
    {
        try {
            $cipher = service('encrypter')->encrypt($value);
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                'Enkripsi gagal — menolak menyimpan rahasia dalam plaintext. Set encryption.key di .env. Asal: ' . $e->getMessage(),
                0,
                $e
            );
        }

        return base64_encode($cipher);
    }

    /**
     * Status penyimpanan sebuah nilai: 'unset' | 'env' | 'encrypted' | 'unprotected'.
     *
     * 'unprotected' = nilai terbaca tapi tidak bisa didekripsi (plaintext lama /
     * kunci berganti) — panel admin harus meminta simpan ulang.
     */
    public function state(string $key, string $environmentKey = ''): string
    {
        $stored = $this->settings->getVal($key);
        if ($stored === '') {
            return $this->envValue($environmentKey) !== '' ? 'env' : 'unset';
        }

        return $this->decryptStored($stored) !== null ? 'encrypted' : 'unprotected';
    }

    private function decryptStored(string $stored): ?string
    {
        foreach ($this->candidates($stored) as $candidate) {
            try {
                return (string) service('encrypter')->decrypt($candidate);
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    /**
     * Format baru: base64(ciphertext). Legacy: ciphertext mentah / plaintext.
     */
    private function candidates(string $stored): array
    {
        $candidates = [];
        $decoded    = base64_decode($stored, true);
        if ($decoded !== false && $decoded !== '') {
            $candidates[] = $decoded;
        }
        $candidates[] = $stored;

        return $candidates;
    }

    private function envValue(string $environmentKey): string
    {
        if ($environmentKey === '') {
            return '';
        }

        return trim((string) (getenv($environmentKey) ?: ($_ENV[$environmentKey] ?? '')));
    }
}
