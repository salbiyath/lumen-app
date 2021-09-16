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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/key', function() {
    return \Illuminate\Support\Str::random(32);
});

$router->get('/latest_covid_data', '\App\Http\Controllers\CovidDataController@latest_covid_data');
$router->get('/top_ten_covid_case', '\App\Http\Controllers\CovidDataController@top_ten_covid_case');
$router->get('/get_countries', '\App\Http\Controllers\CovidDataController@get_countries');
