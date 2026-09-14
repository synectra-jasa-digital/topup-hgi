<?php

namespace App\Libraries;

final class Money
{
    private function __construct()
    {
    }

    public static function rupiah(mixed $value): int
    {
        if (is_int($value)) {
            return max(0, $value);
        }

        $str = trim((string) $value);
        $parts = preg_split("/[.,]/", $str);
        $integerPart = $parts[0] ?? "0";
        $integerPart = str_replace([",", " "], "", $integerPart);
        if ($integerPart === "" || ! is_numeric($integerPart)) {
            $integerPart = "0";
        }

        return max(0, (int) $integerPart);
    }

    public static function percentage(int $amount, mixed $percentage): int
    {
        $rate = (float) $percentage;
        if ($rate <= 0 || $amount <= 0) {
            return 0;
        }

        return max(0, (int) round($amount * $rate / 100, 0, PHP_ROUND_HALF_UP));
    }
}
