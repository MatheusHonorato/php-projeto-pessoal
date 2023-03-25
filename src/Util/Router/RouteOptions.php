<?php

namespace App\Util\Router;

class RouteOptions implements RouteOptionsInterface
{
    public function __construct(private readonly array $routeOptions)
    {
    }

    public function optionExist(string $index): bool
    {
        return !empty($this->routeOptions) && isset($this->routeOptions[$index]);
    }

    public function execute(string $index): array
    {
        return $this->routeOptions[$index];
    }
}
