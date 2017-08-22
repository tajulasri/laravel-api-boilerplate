<?php

use Dingo\Api\Routing\Router;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

/** @var Router $api */
$api = app(Dingo\Api\Routing\Router::class);

$api->version('v1', ['middlware' => 'api', 'namespace' => 'App\Http\Controllers'], function (Router $api) {

    /* AUTH */
    $api->post('auth/login', 'AuthController@login');
    $api->post('auth/register', 'AuthController@register');
    $api->get('auth/refresh', 'AuthController@refresh')->middleware('api.auth');
    $api->get('auth/user', 'AuthController@me')->middleware('api.auth');

});
