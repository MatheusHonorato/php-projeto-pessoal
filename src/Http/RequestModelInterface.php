<?php

declare(strict_types=1);

namespace App\Http;

use App\Util\Validator;
use stdClass;

interface RequestModelInterface
{
    public static function validated(RequestInterface $request, Validator $validator): stdClass;
}