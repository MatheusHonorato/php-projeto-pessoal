<?php

declare(strict_types=1);

namespace App\Models;

class UserCompanyModel extends ModelAbstract
{
    public const TABLE = 'users_companies';

    public function __construct(
        protected readonly int|null $id,
        protected readonly int $user_id,
        protected readonly int $company_id,
    ) {}

    public function toArray(): array
    {
        return [
          'id' => $this->id,
          'user_id' => $this->user_id,
          'company_id' => $this->company_id
        ];
    }
}