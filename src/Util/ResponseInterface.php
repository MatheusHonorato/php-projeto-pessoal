<?php

declare(strict_types=1);

namespace App\Util;

interface ResponseInterface
{
    public function execute(array $data, int $status): object;
}