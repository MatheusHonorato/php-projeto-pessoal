<?php

namespace App\Util\Router;

interface UriInterface
{
    public function getUri(): string;

    public function setUri(string $uri): void;

    public function currentUri(): string;

    public function currentRequest(): string;
}
