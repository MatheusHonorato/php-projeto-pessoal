<?php

declare(strict_types=1);

namespace App\Http;

use App\Models\UserModel;
use App\Util\HttpInterface;

interface RequestModelUserInterface extends RequestModelInterface
{
    public function setExtraDatas(array $value): RequestModelUser;

    public function validated(HttpInterface $http): UserModel | array;
}