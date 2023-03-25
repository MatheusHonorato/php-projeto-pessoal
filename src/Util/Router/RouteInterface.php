<?php

namespace App\Util\Router;

interface RouteInterface
{
    public function addRouteGroupOptions(RouteOptionsInterface $routeOptions): void;

    public function getRouteOptionsInstance(): ?RouteOptionsInterface;

    public function addRouteUri(UriInterface $uri): void;

    public function getRouteUriInstance(): ?UriInterface;

    public function addRouteWildcard(RouteWildcardInterface $routeWildcard): void;

    public function getRouteWildcardInstance(): ?RouteWildcardInterface;

    public function match();
}
