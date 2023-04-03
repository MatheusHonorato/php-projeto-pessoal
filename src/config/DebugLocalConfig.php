<?php

declare(strict_types=1);

namespace App\config;

class DebugLocalConfig implements DebugConfigInterface
{
    private static int $initSetDisplayErrorsTrue = 1;
    private static int $initSetDisplayStartupErrorsTrue = 1;
    private static int $initSetErrorReportingTrue = 1;
    private static int $initSetLogErrorsTrue = 1;
    private static string $initSetMbInternalEncoding = 'UTF-8';

    public static function set(): void
    {
        ini_set(option: 'display_errors', value: self::$initSetDisplayErrorsTrue);
        ini_set(option: 'display_startup_errors', value: self::$initSetDisplayStartupErrorsTrue);
        ini_set(option: 'error_reporting', value: self::$initSetErrorReportingTrue);
        ini_set(option: 'log_errors', value: self::$initSetLogErrorsTrue);
        mb_internal_encoding(encoding: self::$initSetMbInternalEncoding);
    }
}
