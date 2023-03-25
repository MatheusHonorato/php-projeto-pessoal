<?php

declare(strict_types=1);

namespace App\Models;

abstract class CompanyModelAbstract implements ModelInterface
{
    public const TABLE = 'companies';

    public function __construct(
        protected readonly ?int $id = null,
        protected readonly string $name = '',
        protected readonly string $cnpj = '',
        protected readonly string $address = '',
        protected readonly array $user_ids = [],
    ) {
    }

    abstract public function toArray(): array;

    public function __get($atrib): mixed
    {
        return $this->$atrib;
    }
}
