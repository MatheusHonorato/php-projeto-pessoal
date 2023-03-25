<?php

declare(strict_types=1);

namespace App\Models;

abstract class UserModelAbstract implements ModelInterface
{
    public const TABLE = 'users';

    public function __construct(
        protected readonly ?int $id = null,
        protected readonly string $name = "",
        protected readonly string $email = "",
        protected readonly ?string $phone = null,
        protected readonly ?string $date = null,
        protected readonly ?string $city = null,
        protected readonly ?array $company_ids = [],
    ) {
    }

    abstract public function toArray(): array;

    public function __get($atrib): mixed
    {
        return $this->$atrib;
    }
}
