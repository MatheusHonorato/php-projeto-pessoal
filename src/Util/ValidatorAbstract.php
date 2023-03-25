<?php

declare(strict_types=1);

namespace App\Util;

abstract class ValidatorAbstract implements ValidatorInterface
{
    public function __construct(
        protected array $errors = [],
    ) {
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    protected function addError(string $error): void
    {
        $this->errors[] = $error;
    }
}
