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

	Route::group(['prefix' => 'game'], function () {
		// Game Locations Routes
		Route::get('locations', 'App\Http\Controllers\Admin\GameController@index')->name('game.locations.index');
		Route::get('locations/create', 'App\Http\Controllers\Admin\GameController@create')->name('game.locations.create');
		Route::post('locations/store', 'App\Http\Controllers\Admin\GameController@store')->name('game.locations.store');
		Route::get('locations/{id}/edit', 'App\Http\Controllers\Admin\GameController@edit')->name('game.locations.edit');
		Route::patch('locations/{id}/update', 'App\Http\Controllers\Admin\GameController@update')->name('game.locations.update');
		Route::delete('locations/{id}/destroy', 'App\Http\Controllers\Admin\GameController@destroy')->name('game.locations.destroy');
		Route::post('locations/{id}/toggle-status', 'App\Http\Controllers\Admin\GameController@toggleStatus')->name('game.locations.toggle-status');
		
		// Game Slots Routes
		Route::get('locations/{game_location_id}/slots', 'App\Http\Controllers\Admin\GameController@slotsIndex')->name('game.slots.index');
		Route::get('locations/{game_location_id}/slots/create', 'App\Http\Controllers\Admin\GameController@slotsCreate')->name('game.slots.create');
		Route::post('locations/{game_location_id}/slots/store', 'App\Http\Controllers\Admin\GameController@slotsStore')->name('game.slots.store');
		Route::get('locations/{game_location_id}/slots/{id}/edit', 'App\Http\Controllers\Admin\GameController@slotsEdit')->name('game.slots.edit');
		Route::patch('locations/{game_location_id}/slots/{id}/update', 'App\Http\Controllers\Admin\GameController@slotsUpdate')->name('game.slots.update');
		Route::delete('locations/{game_location_id}/slots/{id}/destroy', 'App\Http\Controllers\Admin\GameController@slotsDestroy')->name('game.slots.destroy');
		Route::post('locations/{game_location_id}/slots/{id}/toggle-status', 'App\Http\Controllers\Admin\GameController@slotsToggleStatus')->name('game.slots.toggle-status');
		
		// Game Modes Routes
		Route::get('locations/{game_location_id}/slots/{game_slot_id}/modes', 'App\Http\Controllers\Admin\GameController@modesIndex')->name('game.modes.index');
		Route::get('locations/{game_location_id}/slots/{game_slot_id}/modes/create', 'App\Http\Controllers\Admin\GameController@modesCreate')->name('game.modes.create');
		Route::post('locations/{game_location_id}/slots/{game_slot_id}/modes/store', 'App\Http\Controllers\Admin\GameController@modesStore')->name('game.modes.store');
		Route::get('locations/{game_location_id}/slots/{game_slot_id}/modes/{id}/edit', 'App\Http\Controllers\Admin\GameController@modesEdit')->name('game.modes.edit');
		Route::patch('locations/{game_location_id}/slots/{game_slot_id}/modes/{id}/update', 'App\Http\Controllers\Admin\GameController@modesUpdate')->name('game.modes.update');
		Route::delete('locations/{game_location_id}/slots/{game_slot_id}/modes/{id}/destroy', 'App\Http\Controllers\Admin\GameController@modesDestroy')->name('game.modes.destroy');
		Route::post('locations/{game_location_id}/slots/{game_slot_id}/modes/{id}/toggle-status', 'App\Http\Controllers\Admin\GameController@modesToggleStatus')->name('game.modes.toggle-status');
	});

	Route::group(['prefix' => 'finance'], function () {
		// Wallets
		Route::get('wallets', 'App\Http\Controllers\Admin\WalletController@index')->name('finance.wallets.index');
		Route::get('wallets/{id}/edit', 'App\Http\Controllers\Admin\WalletController@edit')->name('finance.wallets.edit');
		Route::patch('wallets/{id}/update', 'App\Http\Controllers\Admin\WalletController@update')->name('finance.wallets.update');

		// Deposits
		Route::get('deposits', 'App\Http\Controllers\Admin\DepositController@index')->name('finance.deposits.index');
		Route::post('deposits/{id}/approve', 'App\Http\Controllers\Admin\DepositController@approve')->name('finance.deposits.approve');
		Route::post('deposits/{id}/reject', 'App\Http\Controllers\Admin\DepositController@reject')->name('finance.deposits.reject');

		// Withdrawals
		Route::get('withdrawals', 'App\Http\Controllers\Admin\WithdrawalController@index')->name('finance.withdrawals.index');
		Route::post('withdrawals/{id}/approve', 'App\Http\Controllers\Admin\WithdrawalController@approve')->name('finance.withdrawals.approve');
		Route::post('withdrawals/{id}/reject', 'App\Http\Controllers\Admin\WithdrawalController@reject')->name('finance.withdrawals.reject');

		// Bets
		Route::get('bets', 'App\Http\Controllers\Admin\BetController@index')->name('finance.bets.index');
	});

	// User Deposits
	Route::group(['prefix' => 'user-deposits'], function () {
		Route::get('', 'App\Http\Controllers\Admin\UserDepositController@index')->name('user-deposits.index');
		Route::post('{id}/approve', 'App\Http\Controllers\Admin\UserDepositController@approve')->name('user-deposits.approve');
		Route::post('{id}/reject', 'App\Http\Controllers\Admin\UserDepositController@reject')->name('user-deposits.reject');
	});

});

Auth::routes(['verify' => true]);

// OTP verification (customer)
Route::get('verify-otp', 'App\Http\Controllers\Auth\OtpVerificationController@showForm')->name('otp.verify.form');
Route::post('verify-otp', 'App\Http\Controllers\Auth\OtpVerificationController@verify')->name('otp.verify');
Route::post('resend-otp', 'App\Http\Controllers\Auth\OtpVerificationController@resend')->name('otp.resend');

// Customer auth aliases (betting users) - keeps existing /login and /register intact
Route::get('customer/login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('customer.login');
Route::post('customer/login', 'App\Http\Controllers\Auth\LoginController@login')->name('customer.login.submit');
Route::post('customer/logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('customer.logout');

Route::get('customer/register', 'App\Http\Controllers\Auth\RegisterController@showRegistrationForm')->name('customer.register');
Route::post('customer/register', 'App\Http\Controllers\Auth\RegisterController@register')->name('customer.register.submit');

Route::group(['middleware' => 'auth'], function () {
	Route::get('/', 'App\Http\Controllers\Front\GameController@locations')->name('home');

	Route::group(['prefix' => 'game'], function () {
		Route::get('locations/{game_location_id}/slots', 'App\Http\Controllers\Front\GameController@slots')->name('front.game.slots');
		Route::get('locations/{game_location_id}/slots/{game_slot_id}/modes', 'App\Http\Controllers\Front\GameController@modes')->name('front.game.modes');
	});

	// Bets (implemented next)
	Route::get('bets/{game_location_id}/{game_slot_id}/{game_mode_id}', 'App\Http\Controllers\Front\BetController@index')->name('front.bets.index');
	Route::post('bets/{game_location_id}/{game_slot_id}/{game_mode_id}', 'App\Http\Controllers\Front\BetController@store')->name('front.bets.store');

	// Menus
	Route::get('menu', 'App\Http\Controllers\Front\MenuController@index')->name('front.menu');

	// Wallet
	Route::get('deposit', 'App\Http\Controllers\Front\WalletController@deposit')->name('front.wallet.deposit');
	Route::post('deposit', 'App\Http\Controllers\Front\WalletController@depositStore')->name('front.wallet.deposit.store');
	Route::get('deposit-history', 'App\Http\Controllers\Front\WalletController@depositHistory')->name('front.wallet.deposit.history');

	Route::get('withdraw', 'App\Http\Controllers\Front\WalletController@withdraw')->name('front.wallet.withdraw');
	Route::post('withdraw', 'App\Http\Controllers\Front\WalletController@withdrawStore')->name('front.wallet.withdraw.store');
	Route::get('withdraw-history', 'App\Http\Controllers\Front\WalletController@withdrawHistory')->name('front.wallet.withdraw.history');

	Route::get('profile', 'App\Http\Controllers\Front\ProfileController@edit')->name('front.profile.edit');
	Route::patch('profile', 'App\Http\Controllers\Front\ProfileController@update')->name('front.profile.update');

	Route::get('game-rules', 'App\Http\Controllers\Front\GameRulesController@index')->name('front.game.rules');
	Route::get('game-timing', 'App\Http\Controllers\Front\GameTimingController@index')->name('front.game.timing');
	Route::get('my-bet', 'App\Http\Controllers\Front\BetController@myBet')->name('front.my.bet');
	Route::get('results', 'App\Http\Controllers\Front\ResultsController@index')->name('front.results');
	Route::get('transaction-history', 'App\Http\Controllers\Front\TransactionHistoryController@index')->name('front.transaction.history');
	Route::get('helpline', 'App\Http\Controllers\Front\HelplineController@index')->name('front.helpline');
	Route::get('refer-and-earn', 'App\Http\Controllers\Front\ReferController@index')->name('front.refer');

	Route::get('logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');
});

Route::fallback(function () {
	return view('404');
});