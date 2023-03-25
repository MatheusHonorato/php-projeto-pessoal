<?php

declare(strict_types=1);

namespace App\Models;

interface ModelInterface
{
    public function toArray(): array;

    public function __get($atrib): mixed;
}
