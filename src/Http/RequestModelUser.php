<?php

declare(strict_types=1);

namespace App\Http;

use App\Util\ValidatorInterface;
use stdClass;

class RequestModelUser implements RequestModelInterface
{
    public static function validated(
        RequestInterface $request,
        ValidatorInterface $validator,
        ?array $extra_datas = [],
        string $unique = ""): stdClass
    {
        return $request::validate(
            validator: $validator,
            params: [
                'name' => ['required', 'string'],
                'email' => ['required', $unique],
                'phone' => ['required', 'string'],
                'date' => ['required', 'date'],
                'city' => ['required', 'string'],
                'company_ids' => ['required', 'uniqueArray', 'foreignKey:company:id'],
            ],
            extra_datas: $extra_datas,
        );
    }
}