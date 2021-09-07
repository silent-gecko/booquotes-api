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
    $router->get('/', ['as' => 'v1.home', function () use ($router) {
        return response()->jsonHealthCheck();
    }]);

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->get('/authors', ['as' => 'v1.author.index', 'uses' => 'AuthorController@index']);
        $router->get('/authors/{uuid}', ['as' => 'v1.author.show', 'uses' => 'AuthorController@show']);

        $router->get('books', ['as' => 'v1.book.index', 'uses' => 'BookController@index']);
        $router->get('books/{uuid}', ['as' => 'v1.book.show', 'uses' => 'BookController@show']);

        $router->get('/authors/{uuid}/books', [
            'as'   => 'v1.author.book.index',
            'uses' => 'AuthorBookController@show'
        ]);

        $router->get('/authors/{uuid}/quotes', [
            'as' => 'v1.author.quote.index',
            'uses' => 'AuthorQuoteController@index'
        ]);

        $router->get('/books/{uuid}/quotes', [
            'as' => 'v1.book.quote.index',
            'uses' => 'BookQuoteController@index'
        ]);

        $router->get('/quotes', ['as' => 'v1.quote.index', 'uses' => 'QuoteController@index']);
        $router->get('/quotes/{uuid}', ['as' => 'v1.quote.show', 'uses' => 'QuoteController@show']);
        $router->get('/quotes/random', ['as' => 'v1.quote.random', 'uses' => 'QuoteController@showRandom']);
    });
});
