<?php

namespace App\Util\Router;

class Uri implements UriInterface
{
    public function __construct(private string $uri)
    {
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    public function currentUri(): string
    {
        return $_SERVER['REQUEST_URI'] !== '/' ? rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') : '/';
    }

    public function currentRequest(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}
