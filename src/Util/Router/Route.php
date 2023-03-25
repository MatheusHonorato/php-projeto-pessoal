<?php

namespace App\Util\Router;

use App\Http\RequestModelInterface;
use App\Repositories\RepositoryInterface;
use App\Util\Http;
use App\Util\Router\RouteOptions;

class Route
{
    private ?RouteOptions $routeOptions = null;
    private ?UriInterface $uri = null;
    private ?RouteWildcard $routeWildcard = null;

    public function __construct(
        public readonly string $method,
        public readonly string $controller,
        public readonly Http $http,
        public readonly RepositoryInterface $repository,
        public readonly RequestModelInterface $requestModel,
        public readonly array $wildcardAliases,
    ) {
    }

    public function addRouteGroupOptions(RouteOptions $routeOptions): void
    {
        $this->routeOptions = $routeOptions;
    }

    public function getRouteOptionsInstance(): ?RouteOptions
    {
        return $this->routeOptions;
    }

    public function addRouteUri(UriInterface $uri): void
    {
        $this->uri = $uri;
    }

    public function getRouteUriInstance(): ?UriInterface
    {
        return $this->uri;
    }

    public function addRouteWildcard(RouteWildcard $routeWildcard): void
    {
        $this->routeWildcard = $routeWildcard;
    }

    public function getRouteWildcardInstance(): ?RouteWildcard
    {
        return $this->routeWildcard;
    }

    public function match()
    {
        if ($this->routeOptions->optionExist('prefix')) {
            $this->uri->setUri(rtrim("/{$this->routeOptions->execute('prefix')}{$this->uri->getUri()}", '/'));
        }

        $this->routeWildcard->replaceWildcardWithPattern($this->uri->getUri());
        $wildcardReplaced = $this->routeWildcard->getWildcardReplaced();

        if ($wildcardReplaced !== $this->uri->getUri() && $this->routeWildcard->uriEqualToPattern($this->uri->currentUri(), $wildcardReplaced)) {
            $this->uri->setUri($this->uri->currentUri());
            $this->routeWildcard->paramsToArray($this->uri->getUri(), $wildcardReplaced, $this->wildcardAliases);
        }

        if (
            $this->uri->getUri() === $this->uri->currentUri() &&
            strtolower($this->method) === $this->uri->currentRequest()
        ) {
            return $this;
        }
    }
}
