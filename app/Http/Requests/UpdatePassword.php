<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class UpdatePassword extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	/**
	 * [failedValidation [Overriding the event validator for custom error response]]
	 * @param  Validator $validator [description]
	 * @return [object][object of various validation errors]
	 */
	public function failedValidation(Validator $validator) {
		//write your bussiness logic here otherwise it will give same old JSON response
		$response = \App\Helpers\Helper::resp('Bad Request', 400, [
			'errors' => $validator->errors(),
		]);
		throw new HttpResponseException(response()->json($response, 400));
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		$passwordValidators = \App\Models\User::$passwordValidator;
		return [
			'current_password' => 'required',
			'password'         => $passwordValidators,
			'confirm_password' => 'required|required_with:password|same:password',
		];
	}

	public function message() {
		$validationMessages = [
			'confirm_password.same'          => 'The confirm password field should match with password.',
			'confirm_password.required_with' => 'The confirm password field is required when password is present.',
		];

		return $validationMessages;
	}

	/**
	 * Configure the validator instance.
	 *
	 * @param  \Illuminate\Validation\Validator  $validator
	 * @return void
	 */
	public function withValidator($validator) {
		// checks user current password
		// before making changes
		$validator->after(function ($validator) {
			if (!Hash::check($this->current_password, $this->user()->password)) {
				$validator->errors()->add('current_password', 'Your current password is incorrect.');
			}
		});
		return;
	}
}
