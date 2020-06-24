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
            $api->post('branch/add', [
                'as' => 'branch.add',
                'uses' => 'BranchController@add',
            ]);
            $api->post('branch/edit', [
                'as' => 'branch.edit',
                'uses' => 'BranchController@edit',
            ]);
            $api->post('branch/delete', [
                'as' => 'branch.delete',
                'uses' => 'BranchController@delete',
            ]);
            $api->get('branch/list', [
                'as' => 'branch.list',
                'uses' => 'BranchController@listBranches',
            ]);
        });

    });
});
