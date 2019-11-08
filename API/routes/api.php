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
    Route::get('/nav/getNav', 'NavController@getNav');

    Route::post('/nav/del', 'NavController@del');

    Route::match(['get', 'post'], '/nav/edit', 'NavController@edit');

    Route::post('/nav/create', 'NavController@create');

    Route::get('/product/getProduct', 'ProductController@getProduct');

    Route::get('/cate/getCate', 'CateController@getCate');

    Route::post('/upload', 'PreController@upload');

    Route::post('/move','PreController@moveImage');

    Route::post('/cate/delete', 'CateController@delete');

    Route::match(['get', 'post'], '/cate/edit', 'CateController@edit');

    Route::post('/cate/create', 'CateController@create');

    Route::post('/product/delete', 'ProductController@delete');

    Route::match(['get', 'post'], '/product/edit', 'ProductController@edit');

    Route::post('/product/create', 'ProductController@create');

    Route::get('/product/getTag', 'ProductController@getTag');

    Route::get('/product/getSku', 'ProductController@getSku');

    Route::post('/product/changeShelf', 'ProductController@changeShelf');

    Route::get('/product/getProducts', 'ProductController2@getProduct2');

    Route::post('/product/creates', 'ProductController2@create');

    Route::get('/Authentication', 'UserController@Authentication');

    Route::post('/Out', 'UserController@out');

    Route::post('/tagDel','ProductController@tagDelete');
});

Route::post('/Register', 'UserController@Register');

Route::post('/Login', 'UserController@Login');
