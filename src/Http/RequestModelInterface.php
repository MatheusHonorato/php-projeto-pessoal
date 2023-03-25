<?php

declare(strict_types=1);

namespace App\Http;

use App\Models\ModelInterface;
use App\Util\HttpInterface;

interface RequestModelInterface
{
    public function validated(HttpInterface $http): ModelInterface | array;
}
