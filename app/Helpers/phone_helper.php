<?php

if (! function_exists('normalize_phone')) {
    /**
     * Normalizes local/international phone numbers into standard 628xxxxxxxx format.
     * Handles 08xx, 8xx, 628xx, and +628xx.
     */
    function normalize_phone(?string $phone): string
    {
        if ($phone === null || trim($phone) === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        if (strlen($digits) < 9 || strlen($digits) > 15) {
            return '';
        }

        return $digits;
    }
}
