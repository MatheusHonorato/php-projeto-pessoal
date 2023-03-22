<?php

declare(strict_types=1);

namespace App\Util;

class Response implements ResponseInterface
{
    public static function execute(array $data, int $status): object
    {
        return (object) ['data' => $data, 'status' => $status];
    }
}