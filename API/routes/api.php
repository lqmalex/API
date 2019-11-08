<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('Authentication')->group(function () {
    Route::get('/nav/getNav', 'Nav\NavController@getNav');

    Route::post('/nav/del', 'Nav\NavController@del');

    Route::match(['get', 'post'], '/nav/edit', 'Nav\NavController@edit');

    Route::post('/nav/create', 'Nav\NavController@create');

    Route::get('/product/getProduct', 'Pro\ProController@getProduct');

    Route::get('/cate/getCate', 'Cate\CateController@getCate');

    Route::post('/upload', 'PreController@upload');

    Route::post('/move','PreController@moveImage');

    Route::post('/cate/delete', 'Cate\CateController@delete');

    Route::match(['get', 'post'], '/cate/edit', 'Cate\CateController@edit');

    Route::post('/cate/create', 'Cate\CateController@create');

    Route::post('/product/delete', 'Pro\ProController@delete');

    Route::match(['get', 'post'], '/product/edit', 'Pro\ProController@edit');

    Route::post('/product/create', 'Pro\ProController@create');

    Route::get('/product/getTag', 'Pro\ProController@getTag');

    Route::get('/product/getSku', 'Pro\ProController@getSku');

    Route::post('/product/changeShelf', 'Pro\ProController@changeShelf');

    Route::get('/product/getProducts', 'Pro\ProController2@getProduct2');

    Route::post('/product/creates', 'Pro\ProController2@create');

    Route::get('/Authentication', 'User\UserController@Authentication');

    Route::post('/Out', 'User\UserController@out');

    Route::post('/tagDel','Pro\ProController@tagDelete');
});

Route::post('/Register', 'User\UserController@Register');

Route::post('/Login', 'User\UserController@Login');
