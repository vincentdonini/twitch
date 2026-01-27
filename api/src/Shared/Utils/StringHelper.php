<?php

namespace App\Shared\Utils;

final class StringHelper
{
    public static function kebabToSnake(string $string): string
    {
        return str_replace('-', '_', $string);
    }

    public static function slugify(string $string): string
    {
        $string = iconv('UTF-8', 'ASCII//TRANSLIT', mb_strtolower(trim($string), 'UTF-8'));
        return trim(preg_replace('/[^a-z0-9]+/', '-', $string), '-');
    }
}
