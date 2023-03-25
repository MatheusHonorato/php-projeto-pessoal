<?php

declare(strict_types=1);

namespace App\config;

interface DBConfigInterface
{
    public static function getConfig(): object;
}
