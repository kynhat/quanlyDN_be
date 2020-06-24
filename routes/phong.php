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
// $app->get('/', function () use ($app) {
//     return $app->version();
// });

$api = app('Dingo\Api\Routing\Router');

// v1 version API
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\V1'], function ($api) {
    $api->group(['middleware' => ['api.locale']], function ($api) {
        //Login
        $api->group(['middleware' => ['api.auth']], function ($api) {
            $api->post('phong/add', [
                'as' => 'phong.add',
                'uses' => 'PhongController@add',
            ]);
            $api->post('phong/edit', [
                'as' => 'phong.edit',
                'uses' => 'PhongController@edit',
            ]);
            $api->post('phong/delete', [
                'as' => 'phong.delete',
                'uses' => 'PhongController@delete',
            ]);
            $api->get('phong/list', [
                'as' => 'phong.list',
                'uses' => 'PhongController@listPhong',
            ]);
        });

    });
});
