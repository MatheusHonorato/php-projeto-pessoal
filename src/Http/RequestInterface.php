<?php

declare(strict_types=1);

namespace App\Http;

use App\Util\ValidatorAbstract;

interface RequestInterface
{
    public static function validate(?ValidatorAbstract $validator = null, ?array $params = [], ?array $extraDatas = []): \stdClass;

    public static function getHttpMethod(): string;

    public static function getCurrentUri(): string;
}
