<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'v1/', 'middleware' => ['api']], function () {
	Route::post('login/', 'App\Http\Controllers\Auth\Api\LoginController@login');
	Route::post('register', 'App\Http\Controllers\Auth\Api\RegisterController@register');
	Route::post('forgot/password', 'App\Http\Controllers\Auth\Api\LoginController@forgotPassword');
	Route::post('set/password', 'App\Http\Controllers\Auth\Api\LoginController@setPassword');
	Route::post('verify/email', 'App\Http\Controllers\Auth\Api\LoginController@verifyEmail');
	Route::get('captcha', 'App\Http\Controllers\Api\InitController@captcha');
});

Route::group(['prefix' => 'v1/', 'middleware' => ['auth:api', 'permission']], function () {
	Route::post('auth/logout', 'App\Http\Controllers\Auth\Api\LoginController@logout');
	Route::get('init', 'App\Http\Controllers\Api\InitController@initialDetails');

	Route::group(['prefix' => 'users'], function () {
		Route::post('avatar', 'App\Http\Controllers\Api\UserController@uploadAvatar');
		Route::put('password', 'App\Http\Controllers\Api\UserController@updatePassword');
	});
	
	// Deposit API
	Route::post('deposit', 'App\Http\Controllers\Api\DepositController@deposit');

});

