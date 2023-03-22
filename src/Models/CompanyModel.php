<?php

declare(strict_types=1);

namespace App\Models;

class CompanyModel extends ModelAbstract
{
    public const TABLE = 'companies';

    public function __construct(
        protected readonly ?int $id = null,
        protected readonly string $name = "",
        protected readonly string $cnpj = "",
        protected readonly string $address = "",
        protected readonly array $user_ids = [],
    ) {}

    public function toArray(): array
    {
        return [
          'id' => $this->id,
          'name' => $this->name,
          'cnpj' => $this->cnpj,
          'address' => $this->address
        ];
    }
}