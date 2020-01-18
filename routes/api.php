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

Route::namespace('Api')->group(function() {
    Route::post('login', ['as' => 'auth.login', 'uses' => 'LoginController@login']);
    Route::post('register', ['as' => 'auth.register', 'uses' => 'RegisterController@register']);

    Route::middleware('auth:api')->group(function() {
        Route::get('/user', 'UsersController@show');
        Route::resource('users', 'UsersController');
    });
});
