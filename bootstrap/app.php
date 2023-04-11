<?php

declare(strict_types=1);

require_once __DIR__.'../../vendor/autoload.php';
require_once __DIR__.'../../src/routes/web.php';

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: HEAD, GET, PUT, PATCH, POST, DELETE, OPTIONS');

use App\config\DBConfig;
use App\Controllers\CompaniesController;
use App\Controllers\UsersController;
use App\DB\PDOMonostate;
use App\DB\QueryBuilder;
use App\Http\Request;
use App\Http\RequestModelCompany;
use App\Http\RequestModelUser;
use App\Models\UserModel;
use App\Repositories\CompanyRepository;
use App\Repositories\UserRepository;
use App\Util\Container;
use App\Util\Http;
use App\Util\Response;
use App\Util\Router;
use App\Util\Validator;
use Dotenv\Dotenv;

$dbConfig = DBConfig::getConfig();
$db = new PDOMonostate(config: $dbConfig);
$queryBuilder = new QueryBuilder(db: $db);

Dotenv::createUnsafeImmutable(paths: dirname(__DIR__, 1))->load();

$debug = "App\config\Debug".ucfirst(string: getenv('APP_ENV')).'Config';

$debug::set();

$container = Container::instance();

$container->bind(QueryBuilder::class, function() use ($db) {
    return new QueryBuilder(db: $db);
});

$container->bind(Http::class, function() use ($container) {
    return new Http(
        request: new Request(),
        response: new Response(),
        validator: new Validator(queryBuilder: $container->make(key: QueryBuilder::class))
    );
});

$container->bind(UserRepository::class, function() use ($container) {
    return new UserRepository(queryBuilder: $container->make(key: QueryBuilder::class));
});

$container->bind(CompanyRepository::class, function() use ($container) {
    return new CompanyRepository(queryBuilder: $container->make(key: QueryBuilder::class));
});

$container->bind(RequestModelUser::class, function() use ($container) {
    return new RequestModelUser(validator: $container->make(key: Http::class)->validator, model: UserModel::class);
});

$container->bind(UsersController::class, function() use ($container) {
    return new UsersController($container->make(key: UserRepository::class), $container->make(key: RequestModelUser::class));
});

$container->bind(RequestModelCompany::class, function() use ($container) {
    return new RequestModelCompany(validator: $container->make(key: Http::class)->validator, model: CompanyModel::class);
});

$container->bind(CompaniesController::class, function() use ($container) {
    return new CompaniesController($container->make(key: CompanyRepository::class), $container->make(key: RequestModelCompany::class));
});

$router = new Router(routes: $routes, container: $container, http: $container->make(key: Http::class));
