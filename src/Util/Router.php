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
            $replaced = Helper::regExInParamsRequest(string: $route['uri']);

            $pattern = "/^{$replaced}[a-z0-9&\?\=]*$/i";

            $validateRoute = preg_match(pattern: $pattern, subject: $this->request::getUri(), matches: $params);

            if ($validateRoute && $this->request::getHttpMethod() == $route['method']) {
                unset($params[0]);

                [$resource, $action] = explode("@", $route['action']);

                $controller = $namespace . '\\' . ucfirst($resource);

                $params[] = $this->container->make(key: Http::class);

                $resultApi = Helper::methodObjectCall($this->container, $controller, $action, $params);

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
}
