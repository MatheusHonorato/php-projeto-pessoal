<?php

declare(strict_types=1);

namespace App\Util;

class ClearString implements ClearStringInterface
{
    public static function execute(string $string): string
    {
        $string = str_replace('/', '', $string);
        $string = str_replace('\\', '', $string);

        return $string;
    }  
}