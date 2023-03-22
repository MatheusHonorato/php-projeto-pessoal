<?php

declare(strict_types=1);

namespace App\config;

class DBConfig implements DBConfigInterface
{
    public static function getConfig(): object
    {
        return (object) [
            'DB_CONNECTION' => getenv('DB_CONNECTION') ? getenv('DB_CONNECTION') : 'mysql',
            'DB_HOST' => getenv('DB_HOST') ? getenv('DB_HOST') : 'db',
            'DB_NAME' => getenv('DB_NAME') ? getenv('DB_NAME') : 'app',
            'DB_USER' => getenv('DB_USER') ? getenv('DB_USER') : 'app_user',
            'DB_PASSWORD' => getenv('DB_PASSWORD') ? getenv('DB_PASSWORD') : 'password',
        ];
    }
}