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

            $validateRoute = preg_match("/^{$replaced}[a-z0-9&\?\=]*$/i", $this->request::getUri(), $params);

            if ($validateRoute && $this->request::getMethod() == $route['method']) {
                unset($params[0]);

                [$resource, $action] = explode("@", $route['action']);

                $controller = $namespace . '\\' . ucfirst($resource);

                if (class_exists(class: $controller)) {
                    $newController = $this->container->make($controller);

                    if (method_exists(object_or_class: $controller, method: $action)) {
                        $params = array_map(
                            function ($value) {
                                if (is_numeric($value)) {
                                    if (strpos($value, '.') !== false) {
                                        return floatval($value);
                                    }
                                    return intval($value);
                                }
                                return $value;
                            },
                            $params
                        );

                        $params[] = $this->container->make(key: Http::class);
                        $result_api = $newController->$action(...$params);

                        echo json_encode($result_api, JSON_UNESCAPED_UNICODE);
                        return;
                    }
                }

                echo json_encode((new Response())->execute(status: 404, data: ['Not Found']));
                return;
            }
        }

        echo json_encode((new Response())->execute(status: 404, data: ['Not Found']));
        return;
    }
}
