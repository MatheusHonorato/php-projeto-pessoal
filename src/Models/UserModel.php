<?php

declare(strict_types=1);

namespace App\Models;

class UserModel extends UserModelAbstract
{
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