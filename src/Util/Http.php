<?php

declare(strict_types=1);

namespace App\Util;

use App\Http\RequestInterface;

class Http implements HttpInterface
{
    public function __construct(
        public readonly RequestInterface $request,
        public readonly ResponseInterface $response,
        public readonly ValidatorInterface $validator,
    ) {
    }
}
