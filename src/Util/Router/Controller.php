<?php

namespace App\Util\Router;

use Exception;

class Controller
{

  private function controllerPath($route, $controller): string
  {
    return ($route->getRouteOptionsInstance() && $route->getRouteOptionsInstance()->optionExist('controller')) ?
      "App\\Controllers\\" . $route->getRouteOptionsInstance()->execute('controller') . '\\' . $controller :
      "App\\Controllers\\" . $controller;
  }

  public function call(Route $route): void
  {
    $controller = $route->controller;

    if (!str_contains($controller, ':')) {
      throw new Exception("Colon need to controller {$controller} in route");
    }

    [$controller, $action] = explode(':', $controller);

    $controllerInstance = $this->controllerPath($route, $controller);

    if (!class_exists($controllerInstance)) {
      throw new Exception("Controller {$controller} does not exist");
    }

    $controller = new $controllerInstance($route->repository, $route->requestModel);

    if (!method_exists($controller, $action)) {
      throw new Exception("Action {$action} does not exist");
    }

    $route->getRouteOptionsInstance();

    call_user_func([$controller, '__construct'], $route->repository, $route->requestModel);

    $params = $route->getRouteWildcardInstance()?->getParams();

    if(count($params) > 0) {
      $result_api = call_user_func(
        [$controller, $action],
        $params,
        $route->http
      );
    } else {
      $result_api = call_user_func(
        [$controller, $action],
        $route->http
      );
    }

    echo json_encode($result_api, JSON_UNESCAPED_UNICODE);
  }
}