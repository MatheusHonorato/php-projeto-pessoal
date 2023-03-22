<?php

declare(strict_types=1);

namespace App\Http;

use App\Util\ValidatorInterface;
use stdClass;

class RequestModelCompany implements RequestModelInterface
{
    public static function validated(
        RequestInterface $request,
        ValidatorInterface $validator,
        ?array $extra_datas = [],
        string $unique = ""
    ): stdClass
    {
        return $request::validate(
            validator: $validator,
            params: [
                'name' => ['required', 'string'],
                'cnpj' => ['required', $unique],
                'address' => ['required', 'string'],
                'user_ids' => ['required', 'uniqueArray', 'foreignKey:user:id'],
            ],
            extra_datas: $extra_datas,
        );
    }
}