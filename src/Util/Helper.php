<?php

declare(strict_types=1);

namespace App\Util;

class Helper
{
    public static function regExInParamsRequest(string $string): string
    {
        $string = str_replace('/', '\/', $string);

        $string = str_replace('{numeric}', "([0-9]+)", $string);

        $string = str_replace('{alpha}', '([a-zA-Z]+)', $string);

        $string = str_replace('{any}', '([a-zA-Z0-9\-]+)', $string);

        return $string;
    }
}
