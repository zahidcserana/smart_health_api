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
    return $router->app->version();
});

$router->post('auth/login', ['uses' => 'AuthController@postLogin']);


$router->group(
    ['prefix' => 'api'],
    function () use ($router) {

        $router->post('auth/login', ['uses' => 'AuthController@postLogin']);

        $router->group(
            ['middleware' => 'auth:api'],
            function () use ($router) {
                $router->get('/test', function () {
                    return response()->json([
                        'message' => 'Hello World!',
                    ]);
                });
                $router->post('logout', ['uses' => 'AuthController@logout']);
                $router->post('refresh', ['uses' => 'AuthController@refresh']);
                $router->post('me', ['uses' => 'AuthController@me']);
            }
        );
    }
);
