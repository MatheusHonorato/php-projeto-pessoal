<?php

declare(strict_types=1);

namespace App\Models;

class UserModel extends ModelAbstract
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
    ) {}

    public function toArray(): array
    {
        return [
          'id' => $this->id,
          'name' => $this->name,
          'email' => $this->email,
          'phone' => $this->phone,
          'date' => $this->date,
          'city' => $this->city,
        ];
    }
}