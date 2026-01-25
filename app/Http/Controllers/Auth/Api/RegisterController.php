<?php

namespace App\Http\Controllers\Auth\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Validator;

class RegisterController extends Controller {
	public $successStatus = 200;

	/**
	 * Register api
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function register(RegisterRequest $request) {
		// $validator = Validator::make($request->all(), [
		// 	'first_name' => 'required|max:100',
		// 	'last_name' => 'required|max:100',
		// 	'email'     => 'required|email|max:255|unique:users,email,0,id,deleted_at,NULL',
		// 	'phone'     => 'required|unique:user_phones,phone,0,id,deleted_at,NULL',
		// 	'captcha'	=>'required'
		// ]);
		// if ($validator->fails()) {
		// 	return Helper::rj('Error.', 401, [
		// 		'errors' => $validator->errors(),
		// 	]);
		// }
		$input                     = $request->all();
		$input['username']         = Helper::randomString(15);
		$input['status']           = 0;
		$input['remember_token']   = Helper::randomString(25);
		$user                      = User::create($input);

		$role                      = \App\Role::where('slug', 'sub-admin')->first();
		$input['user_id']          = $user->id;
		$input['role_id']          = $role->id;
		$role                      = UserRole::create($input);

		$mailData = [
			'full_name'       => $user->full_name,
			'activation_link' => \Config::get('settings.frontend_url') . 'user/verify/' . $user->remember_token,
			'extra_text'      => '',
		];
		$fullName = $user->full_name;
		\App\SiteTemplate::sendMail($user->email, $fullName, $mailData, 'register_provider'); //register_provider from db site_template table template_name field

		return Helper::rj('Registration has been successfully completed.', $this->successStatus, $user);
	}
}
