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
    //Route::post('register', ['as' => 'auth.register', 'uses' => 'RegisterController@register']);
    Route::post('login', ['as' => 'auth.login', 'uses' => 'LoginController@login']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
