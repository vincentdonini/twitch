<?php

namespace App\Shared\Utils;

final class LocalizationHelper
{
    public static function getLocalizedValue(array $data, string $locale): string
    {
        if (is_array($data) && !empty($data)) {
            return $data[$locale]
                ?? $data['en']
                ?? reset($data)
                ?? '';
        }

        return is_string($data) ? $data : '';
    }
}