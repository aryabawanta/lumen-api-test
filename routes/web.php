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

//$router->get('/generate_app_key', function () use ($router) {
//    return str_random(32);
//});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register',  ['uses' => 'AuthController@registerOrUpdate']);
    $router->post('/login',  ['uses' => 'AuthController@login']);
});

//$router->group(['prefix' => 'users'], function () use ($router) {
//    $router->get('',  ['uses' => 'UserController@index']);
//    $router->get('/{id}',  ['uses' => 'UserController@show']);
//});

//$router->group(['prefix' => 'templates'], function () use ($router) {
//    $router->get('', 'TemplateController@index');
//    $router->get('/{id}', 'TemplateController@show');
//    $router->post('', 'TemplateController@create');
//    $router->patch('/{id}', 'TemplateController@update');
//    $router->delete('/{id}', 'TemplateController@delete');
//});

$router->group(['prefix' => 'checklists'], function () use ($router) {
    $router->get('', 'ChecklistController@index');
    $router->post('', 'ChecklistController@create');
    $router->post('/complete', 'ChecklistItemController@complete');
    $router->post('/incomplete', 'ChecklistItemController@incomplete');
    $router->post('/items/summaries', 'ChecklistItemController@summaries');

    $router->group(['prefix' => '/templates'], function () use ($router) {
        $router->get('', 'ChecklistTemplateController@index');
        $router->post('', 'ChecklistTemplateController@create');

        $router->group(['prefix' => '/{templateId}'], function () use ($router) {
            $router->get('', 'ChecklistTemplateController@show');
            $router->patch('', 'ChecklistTemplateController@update');
            $router->delete('', 'ChecklistTemplateController@delete');
            $router->post('/assigns', 'ChecklistTemplateController@assigns');
        });

    });

    $router->group(['prefix' => '/{checklistId}'], function () use ($router) {
        $router->get('', 'ChecklistController@show');
        $router->patch('', 'ChecklistController@update');
        $router->delete('', 'ChecklistController@delete');

        $router->group(['prefix' => '/items'], function () use ($router) {
            $router->post('', 'ChecklistItemController@create');
            $router->get('', 'ChecklistItemController@index');
            $router->post('/_bulk', 'ChecklistItemController@bulk_updates');
            $router->get('/{itemId}', 'ChecklistItemController@show');
            $router->patch('/{itemId}', 'ChecklistItemController@update');
            $router->delete('/{itemId}', 'ChecklistItemController@delete');
        });
    });


});

