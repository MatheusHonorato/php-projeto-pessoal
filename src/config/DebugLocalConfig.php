<?php

declare(strict_types=1);

namespace App\config;

class DebugLocalConfig implements DebugConfigInterface
{
    private static int $initSetDisplayErrorsTrue = 1;
    private static int $initSetDisplayStartupErrorsTrue = 1;

    public static function set(): void
    {
        ini_set(option: 'display_errors', value: self::$initSetDisplayErrorsTrue);
        ini_set(option: 'display_startup_errors', value: self::$initSetDisplayStartupErrorsTrue);
    }
}
