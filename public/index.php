<?php

#xdebug_info();

declare(strict_types=1);

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: HEAD, GET, PUT, PATCH, POST, DELETE, OPTIONS");

require_once __DIR__ . '../../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Util\ClearString;
use App\Util\ControllerCall;

Dotenv::createUnsafeImmutable(dirname(__DIR__, 1))->load();

$debug = "App\config\Debug".ucfirst(getenv('APP_ENV'))."Config";

$debug::set();

// implementar front-controller ou middleware para rotas
// api/resource/1
if(isset($_SERVER["REQUEST_URI"])) {
    $url = explode('/', $_SERVER['REQUEST_URI']);
    array_shift($url);

    $controller = ControllerCall::generate(clearStringInterface: new ClearString, url: $url);

    array_shift($url);

    $method = strtolower($_SERVER['REQUEST_METHOD']);

    try {

        $response = call_user_func_array(array(new $controller, $method), $url);

        http_response_code($response->status);

        if($response->status === 404) {
            echo json_encode($response->data, JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode($response->data, JSON_UNESCAPED_UNICODE);
    } catch (\Exception $exception) {
        http_response_code($response->status);

        echo json_encode($response->data, JSON_UNESCAPED_UNICODE);
    }
}