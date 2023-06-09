<?php

declare(strict_types=1);

namespace App\Util;

interface ValidatorInterface
{
    public function getErrors(): array;
}
