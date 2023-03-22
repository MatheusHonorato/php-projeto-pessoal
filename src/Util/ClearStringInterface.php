<?php

declare(strict_types=1);

namespace App\Util;

interface ClearStringInterface
{
    public static function execute(string $string): string;
}