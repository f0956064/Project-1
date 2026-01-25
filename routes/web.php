<?php

use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'admin'], function () {
	Auth::routes(['verify' => true]);
});

Route::get('user/verify/{token}', 'App\Http\Controllers\Auth\RegisterController@verifyUser');
Route::get('admin', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('admin.login');

Route::group(['prefix' => 'admin', 'middleware' => 'permission'], function () {
	Route::get('/home', 'App\Http\Controllers\DashboardController@index')->name('admin.home');
	Route::get('logout', 'App\Http\Controllers\Auth\LoginController@adminLogout')->name('admin.logout');
	Route::get('export/users', 'App\Http\Controllers\UserController@export')->name('export.users');

	Route::resources([
		'users' => 'App\Http\Controllers\UserController',
		'roles' => 'App\Http\Controllers\Admin\RoleController',
		'contents' => 'App\Http\Controllers\Admin\SiteContentController',
		'permissions' => 'App\Http\Controllers\Admin\PermissionController',
		'settings' => 'App\Http\Controllers\Admin\SiteSettingController',
	]);

	Route::group(['prefix' => 'location'], function () {
		Route::resource('countries', 'App\Http\Controllers\Admin\Locations\LocationCountryController', ['as' => 'location']);
		Route::resource('countries.states', 'App\Http\Controllers\Admin\Locations\LocationStateController', ['as' => 'location']);
		Route::resource('countries.states.cities', 'App\Http\Controllers\Admin\Locations\LocationCityController', ['as' => 'location']);
	});

	Route::group(['prefix' => 'permissions'], function () {
		Route::get('manage_role/{id}', 'App\Http\Controllers\Admin\PermissionController@manageRole')->name('permissions.manage_role');
		Route::post('menus/assign', 'App\Http\Controllers\Admin\PermissionController@setMenu')->name('permissions.assign.menu');
		Route::patch('assign/{id}', "App\Http\Controllers\Admin\PermissionController@assignPermission")->name('permissions.assign');
	});

	Route::group(['prefix' => 'setting'], function () {
		Route::post('export', 'App\Http\Controllers\Admin\SiteSettingController@settingsExport')->name('settings.export');
		Route::post('import', 'App\Http\Controllers\Admin\SiteSettingController@settingsImport')->name('settings.import');
	});

	Route::group(['prefix' => 'menus'], function () {
		Route::get('/{parent_id?}', 'App\Http\Controllers\Admin\MenuController@index')->name('menus.index');
		Route::get('{menu_id}/children', 'App\Http\Controllers\Admin\MenuController@getChildren')->name('menus.children');
		Route::get('create/{parent_id}', 'App\Http\Controllers\Admin\MenuController@create')->name('menus.create');
		Route::get('edit/{parent_id}/{id}', 'App\Http\Controllers\Admin\MenuController@edit')->name('menus.edit');
		Route::post('store', 'App\Http\Controllers\Admin\MenuController@store')->name('menus.store');
		Route::patch('update/{id}', 'App\Http\Controllers\Admin\MenuController@update')->name('menus.update');
		Route::delete('destroy/{parent_id}/{id}', 'App\Http\Controllers\Admin\MenuController@destroy')->name('menus.destroy');
	});

	Route::group(['prefix' => 'templates'], function () {
		Route::get('index', 'App\Http\Controllers\Admin\SiteTemplateController@index')->name('templates.index');
		Route::get('create', 'App\Http\Controllers\Admin\SiteTemplateController@create')->name('templates.create');
		Route::get('edit/{id}', 'App\Http\Controllers\Admin\SiteTemplateController@edit')->name('templates.edit');
		Route::post('store', 'App\Http\Controllers\Admin\SiteTemplateController@store')->name('templates.store');
		Route::patch('update/{id}', 'App\Http\Controllers\Admin\SiteTemplateController@update')->name('templates.update');
		Route::delete('destroy/{id}', 'App\Http\Controllers\Admin\SiteTemplateController@destroy')->name('templates.destroy');
	});

	Route::group(['prefix' => 'ui'], function () {
		Route::get('icons', 'App\Http\Controllers\Admin\SiteSettingController@uiIcons')->name('ui.icons');
	});

});

Auth::routes(['verify' => true]);
Route::get('/', 'App\Http\Controllers\HomeController@home')->name('home');
Route::group(['middleware' => 'auth'], function () {
	Route::get('logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');
});

Route::fallback(function () {
	return view('404');
});