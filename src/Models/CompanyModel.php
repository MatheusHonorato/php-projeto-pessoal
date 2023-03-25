<?php

declare(strict_types=1);

namespace App\Models;

class CompanyModel extends CompanyModelAbstract
{
    public function toArray(): array
    {
        return [
          'id' => $this->id,
          'name' => $this->name,
          'cnpj' => $this->cnpj,
          'address' => $this->address,
        ];
    }
}
