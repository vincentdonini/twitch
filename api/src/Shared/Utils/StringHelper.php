<?php

namespace App\Shared\Utils;

final class StringHelper
{
    public static function kebabToSnake(string $string): string
    {
        return str_replace('-', '_', $string);
    }
}
