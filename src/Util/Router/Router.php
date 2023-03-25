<?php

namespace App\Util\Router;

use App\Http\RequestModelInterface;
use App\Repositories\RepositoryInterface;
use App\Util\HttpInterface;
use App\Util\Router\RouteWildcard;
use App\Util\Router\Uri;
use Closure;

class Router implements RouterInterface
{
    private array $routes = [];
    private array $routeOptions = [];
    private Route $route;

    public function __construct(
        private HttpInterface $http
    ) {
    }

    public function add(
        string $uri,
        string $method,
        string $controller,
        RepositoryInterface $repository,
        RequestModelInterface $requestModel,
        array $wildcardAliases = [],
    ): RouterInterface {
        $this->route = new Route($method, $controller, $this->http, $repository, $requestModel, $wildcardAliases);
        $this->route->addRouteUri(new Uri($uri));
        $this->route->addRouteWildcard(new RouteWildcard());
        $this->route->addRouteGroupOptions(new RouteOptions($this->routeOptions));
        $this->routes[] = $this->route;

        return $this;
    }

    public function group(array $routeOptions, Closure $callback): void
    {
        $this->routeOptions = $routeOptions;
        $callback->call($this);
        $this->routeOptions = [];
    }

    public function options(array $options): void
    {
        if (!empty($this->routeOptions)) {
            $options = array_merge($this->routeOptions, $options);
        }
        $this->route->addRouteGroupOptions(new RouteOptions($options));
    }

    public function init()
    {
        foreach ($this->routes as $route) {
            if ($route->match()) {
                return (new Controller())->call($route);
            }
        }

        //return (new Controller)->call(new Route('GET', 'NotFoundController:index', []));
    }
}
