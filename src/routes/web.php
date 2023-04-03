<?php

$routes = [
    ['uri' => '/users', 'method' => 'GET', 'action' => 'UsersController@index'],
    ['uri' => '/users/{numeric}', 'method' => 'GET', 'action' => 'UsersController@getById'],
    ['uri' => '/users', 'method' => 'POST', 'action' => 'UsersController@store'],
    ['uri' => '/users/{numeric}', 'method' => 'PUT', 'action' => 'UsersController@update'],
    ['uri' => '/users/{numeric}', 'method' => 'DELETE', 'action' => 'UsersController@destroy'],

    ['uri' => '/companies', 'method' => 'GET', 'action' => 'CompaniesController@index'],
    ['uri' => '/companies/{numeric}', 'method' => 'GET', 'action' => 'CompaniesController@getById'],
    ['uri' => '/companies', 'method' => 'POST', 'action' => 'CompaniesController@store'],
    ['uri' => '/companies/{numeric}', 'method' => 'PUT', 'action' => 'CompaniesController@update'],
    ['uri' => '/companies/{numeric}', 'method' => 'DELETE', 'action' => 'CompaniesController@destroy'],
];
