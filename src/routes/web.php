<?php

declare(strict_types=1);

use App\Http\RequestModelCompany;
use App\Http\RequestModelUser;
use App\Models\CompanyModel;
use App\Models\UserModel;
use App\Repositories\CompanyRepository;
use App\Repositories\UserRepository;

try {
    $userRepository = new UserRepository(queryBuilder: $queryBuilder);
    $requestModel = new RequestModelUser(validator: $http->validator, model: UserModel::class);

    $companyRepository = new CompanyRepository(queryBuilder: $queryBuilder);
    $requestModel = new RequestModelCompany(validator: $http->validator, model: CompanyModel::class);

    $router->add(uri: '/users', method: 'GET', controller: 'UsersController:index', repository: $userRepository, requestModel: $requestModel);
    $router->add(uri: '/users/(:numeric)', method: 'GET', controller: 'UsersController:getById', repository: $userRepository, requestModel: $requestModel, wildcardAliases: ['id']);
    $router->add(uri: '/users', method: 'POST', controller: 'UsersController:store', repository: $userRepository, requestModel: $requestModel);
    $router->add(uri: '/users/(:numeric)', method: 'PUT', controller: 'UsersController:update', repository: $userRepository, requestModel: $requestModel, wildcardAliases: ['id']);
    $router->add(uri: '/users/(:numeric)', method: 'DELETE', controller: 'UsersController:destroy', repository: $userRepository, requestModel: $requestModel, wildcardAliases: ['id']);

    $router->add(uri: '/companies', method: 'GET', controller: 'CompaniesController:index', repository: $companyRepository, requestModel: $requestModel);
    $router->add(uri: '/companies/(:numeric)', method: 'GET', controller: 'CompaniesController:getById', repository: $companyRepository, requestModel: $requestModel, wildcardAliases: ['id']);
    $router->add(uri: '/companies', method: 'POST', controller: 'CompaniesController:store', repository: $companyRepository, requestModel: $requestModel);
    $router->add(uri: '/companies/(:numeric)', method: 'PUT', controller: 'CompaniesController:update', repository: $companyRepository, requestModel: $requestModel, wildcardAliases: ['id']);
    $router->add(uri: '/companies/(:numeric)', method: 'DELETE', controller: 'CompaniesController:destroy', repository: $companyRepository, requestModel: $requestModel, wildcardAliases: ['id']);

    $router->init();
} catch (Exception $e) {
    var_dump($e->getMessage().' '.$e->getFile().' '.$e->getLine());
}
