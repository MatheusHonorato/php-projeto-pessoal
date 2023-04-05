<?php

declare(strict_types=1);

namespace App\Util;

class Router
{
    public function __construct(
        private array $routes,
        private Container $container,
        private Http $http,
    ) {
        $this->getRequestRoute();
    }

    private function getRequestRoute(): void
    {
        $namespace = "App\Controllers";

        foreach ($this->routes as $route) {
            [$isCurrentRoute, $params] = $this->getCurrentRouteData(routeMethod: $route['method'], uri: $route['uri']);

            if ($isCurrentRoute) {
                [$controller, $method] = self::generateNameControllerAndMethod(controllerAndMehtodName: $route['action'], namespace: $namespace);

                $params[] = $this->http;

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

    private static function generateNameControllerAndMethod(string $controllerAndMehtodName, string $namespace): array
    {
        [$resource, $method] = explode("@", $controllerAndMehtodName);

        $controller = $namespace . '\\' . ucfirst($resource);

        return [$controller, $method];
    }

    private function getCurrentRouteData(string $routeMethod, string $uri): bool|array
    {
        if ($this->http->request::getHttpMethod() != $routeMethod) {
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
        $replaced = self::regExInParamsRoute(string: $routeUri);

        $pattern = "/^{$replaced}[a-z0-9&\?\=]*$/i";

        $validateRoute = preg_match(pattern: $pattern, subject: $this->http->request::getCurrentUri(), matches: $matches);

        unset($matches[0]);

        return [$validateRoute, $matches];
    }

    private static function regExInParamsRoute(string $string): string
    {
        $string = str_replace('/', '\/', $string);

        $string = str_replace('{numeric}', "([0-9]+)", $string);

        $string = str_replace('{alpha}', '([a-zA-Z]+)', $string);

        $string = str_replace('{any}', '([a-zA-Z0-9\-]+)', $string);

        return $string;
    }
}
