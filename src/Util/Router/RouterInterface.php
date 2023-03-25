<?php

namespace App\Util\Router;

use App\Http\RequestModelInterface;
use App\Repositories\RepositoryInterface;

interface RouterInterface
{
    public function add(
        string $uri,
        string $method,
        string $controller,
        RepositoryInterface $repository,
        RequestModelInterface $requestModel,
        array $wildcardAliases = [],
    ): RouterInterface;

    public function group(array $routeOptions, \Closure $callback): void;

    public function options(array $options): void;

    public function init();
}
