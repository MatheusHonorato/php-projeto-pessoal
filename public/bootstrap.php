<?php

declare(strict_types=1);

require_once __DIR__ . '../../vendor/autoload.php';

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: HEAD, GET, PUT, PATCH, POST, DELETE, OPTIONS");

use App\config\DBConfig;
use App\DB\PDOMonostate;
use App\DB\QueryBuilder;
use App\Http\Request;
use App\Util\Http;
use App\Util\Response;
use Dotenv\Dotenv;
use App\Util\Router\Router;
use App\Util\Validator;

$dbConfig = DBConfig::getConfig();
$db = new PDOMonostate(config: $dbConfig);
$queryBuilder = new QueryBuilder(db: $db);

Dotenv::createUnsafeImmutable(paths: dirname(__DIR__, 1))->load();

$debug = "App\config\Debug".ucfirst(string: getenv('APP_ENV'))."Config";

$debug::set();

$http = new Http(request: new Request, response: new Response, validator: new Validator(queryBuilder: $queryBuilder));

$router = new Router(http: $http);
