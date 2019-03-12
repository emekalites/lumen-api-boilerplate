<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return response()->json(['version' => $router->app->version()], 200);
});

$router->group(['namespace' => 'V1', 'prefix' => 'v1'], function() use ($router) {
    $router->get('/', function () use ($router) {
        return response()->json(['api' => 'welcome to api V1'], 200);
    });

    $router->group(['prefix' => 'auth'], function() use ($router) {
        $router->post('login', ['uses' => 'AuthController@authenticate', 'as' => 'login']);
        $router->post('logout', ['uses' => 'AuthController@logout', 'as' => 'logout']);
        $router->post('register', ['uses' => 'AuthController@register']);
    });

    $router->group(['middleware' => 'jwt'], function() use ($router) {
        $router->get('users/{id}', ['uses' => 'UserController@show', 'as' => 'users.show']);
        $router->put('users/{id}', ['uses' => 'UserController@update', 'as' => 'users.update']);

        // admin
        $router->get('users', ['uses' => 'UserController@index', 'as' => 'users.index', 'middleware' => 'roles', 'roles' => ['admin']]);
    });
});