<?php

declare(strict_types=1);

namespace App\Models;

abstract class ModelAbstract implements ModelInterface
{
    abstract public function toArray(): array;

    public function __get($atrib): mixed
    {
        return $this->$atrib;
    }
}