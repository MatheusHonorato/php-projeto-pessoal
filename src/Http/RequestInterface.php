<?php

declare(strict_types=1);

namespace App\Http;

use App\Util\ValidatorAbstract;
use stdClass;

interface RequestInterface
{
    public static function validate(ValidatorAbstract $validator, array $params, ?array $extra_datas = []): stdClass;
}