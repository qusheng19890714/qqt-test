<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('test', 'HomeController@test');
    $router->resource('users', UsersController::class);
    $router->resource('topics', TopicsController::class);
    $router->resource('categories', CategoriesController::class);

    $router->get('topic/{id}/replies', 'RepliesController@replies')->name('topic.reply');

});

