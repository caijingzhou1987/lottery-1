<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->get('LotteryCodes', 'LotteryCodesController@index');
    $router->put('LotteryCodes/{id}', 'LotteryCodesController@update');
    $router->get('LotteryCodes/{id}/edit', 'LotteryCodesController@edit');
    $router->get('LotteryCodes/create', 'LotteryCodesController@create');
    $router->post('LotteryCodes/generateCode', 'LotteryCodesController@generateCode')->name('generateCode');
    $router->resource('prizes', PrizesController::class);
});
