<?php

declare(strict_types=1);

namespace App\Models;

abstract class UserCompanyModelAbstract implements ModelInterface
{
    public const TABLE = 'users_companies';

    public function __construct(
        protected readonly int|null $id,
        protected readonly int $user_id,
        protected readonly int $company_id,
    ) {}

    abstract public function toArray(): array;

    public function __get($atrib): mixed
    {
        return $this->$atrib;
    }
}