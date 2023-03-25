<?php

declare(strict_types=1);

namespace App\Util;

class Response implements ResponseInterface
{
    public function execute(array $data, int $status): object
    {
        http_response_code($status);
        return (object) ['status' => $status, 'data' => $data];
    }
}