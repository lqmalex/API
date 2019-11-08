<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//use Illuminate\Support\Facades\Request;

Route::get('/', function () {
//    return url()->previous();
    return view('welcome');
});

Route::get('/aa', function () {
//    return $_SERVER;
//    return Request::path();
//    return view('welcome');
});
