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
        $api->post('nguoidung/add', [
                'as' => 'nguoidung.add',
                'uses' => 'NguoidungController@add',
            ]);
            $api->post('nguoidung/edit', [
                'as' => 'nguoidung.edit',
                'uses' => 'NguoidungController@edit',
            ]);
            $api->post('nguoidung/delete', [
                'as' => 'nguoidung.delete',
                'uses' => 'NguoidungController@delete',
            ]);
            $api->get('nguoidung/list', [
                'as' => 'nguoidung.list',
                'uses' => 'NguoidungController@listNguoidung',
            ]);
        $api->group(['middleware' => ['api.auth']], function ($api) {
            
        });

    });
});
