<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->get('/', ['as' => 'v1_home', function () use ($router) {
        return response()->jsonHealthCheck();
    }]);

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->get('/authors', 'AuthorController@index');
        $router->get('/authors/{uuid}', 'AuthorController@get');
    });
});
