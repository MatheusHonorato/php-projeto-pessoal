<?php

declare(strict_types=1);

namespace App\Models;

class UserCompanyModel extends UserCompanyModelAbstract
{
    public function toArray(): array
    {
        return [
          'id' => $this->id,
          'user_id' => $this->user_id,
          'company_id' => $this->company_id
        ];
    }
}
