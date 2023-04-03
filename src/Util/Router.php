<?php

declare(strict_types=1);

namespace App\Util;

class Router
{
    public function __construct(
        private array $routes,
        private Container $container,
    ) {
        $this->getRequestRoute();
    }

    private function getRequestRoute()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $namespace = "App\Controllers";

        foreach ($this->routes as $key => $route) {
            $replaced = str_replace('/', '\/', $route['uri']);

            $replaced = str_replace('{numeric}', '([0-9]+)', $replaced);

            $replaced = str_replace('{alpha}', '([a-zA-Z]+)', $replaced);

            $replaced = str_replace('{any}', '([a-zA-Z0-9\-]+)', $replaced);

            $currentUri = $_SERVER['REQUEST_URI'];

            if (preg_match("/^{$replaced}[a-z0-9&\?\=]*$/i", $currentUri, $params) && $method == $route['method']) {
                unset($params[0]);

                [$resource, $action] = explode("@", $route['action']);

                $controller = $namespace . '\\' . ucfirst($resource);

                if (class_exists(class: $controller)) {
                    $newController = $this->container->make($controller);

                    if (method_exists(object_or_class: $controller, method: $action)) {
                        $result_api = null;

                        $params = array_map(
                            function ($value) {
                                if (is_numeric($value)) {
                                    if (strpos($value, '.') !== false) {
                                        return floatval($value);
                                    }
                                    return intval($value);
                                }
                                return $value;
                            }, $params
                        );

                        if ($params!=null) {
                            $params[] = $this->container->make(Http::class);
                            $result_api = $newController->$action(...$params);
                        } else {
                            $result_api = $newController->$action($this->container->make(Http::class));
                        }

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
