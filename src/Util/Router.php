<?php

declare(strict_types=1);

namespace App\Util;

use App\Http\RequestInterface;

class Router
{
    public function __construct(
        private array $routes,
        private RequestInterface $request,
        private Container $container,
    ) {
        $this->getRequestRoute();
    }

    private function getRequestRoute()
    {
        $namespace = "App\Controllers";

        foreach ($this->routes as $route) {
            [$isCurrentRoute, $params] = $this->getCurrentRouteData(routeMethod: $route['method'], uri: $route['uri']);

            if ($isCurrentRoute) {
                [$controller, $method] = self::getControllerAndMethod(controllerAndMehtodName: $route['action'], namespace: $namespace);

                $params[] = $this->container->make(key: Http::class);

                $resultApi = Helper::methodObjectCall($this->container, $controller, $method, $params);

                if (count($resultApi->data) == 0) {
                    echo json_encode((new Response())->execute(status: 404, data: ['Not Found']));
                    return;
                }

                echo json_encode($resultApi, JSON_UNESCAPED_UNICODE);
                return;
            }
        }

        echo json_encode((new Response())->execute(status: 404, data: ['Not Found']));
        return;
    }

    private static function getControllerAndMethod(string $controllerAndMehtodName, string $namespace): array
    {
        [$resource, $method] = explode("@", $controllerAndMehtodName);

        $controller = $namespace . '\\' . ucfirst($resource);

        return [$controller, $method];
    }

    private function getCurrentRouteData(string $routeMethod, string $uri): bool|array
    {
        if ($this->request::getHttpMethod() != $routeMethod) {
            return false;
        }

        [$validateRoute, $params] = $this->isMatchUri($uri);

        if (!$validateRoute) {
            return false;
        }

        return [$validateRoute, $params];
    }

    private function isMatchUri(string $routeUri): array
    {
        $replaced = Helper::regExInParamsRequest(string: $routeUri);

        $pattern = "/^{$replaced}[a-z0-9&\?\=]*$/i";

        $validateRoute = preg_match(pattern: $pattern, subject: $this->request::getCurrentUri(), matches: $matches);

        unset($matches[0]);

        return [$validateRoute, $matches];
    }
}
