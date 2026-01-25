<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class LoginRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	private $_id;

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules(Request $request) {
		$validationRules = [
			'email'    => 'required',
			'password' => 'required',
			'captcha'  => 'nullable',
		];

		return $validationRules;
	}

	public function message() {
		$validationMessages = [];

		return $validationMessages;
	}

	public function withValidator($validator) {
		$validator->after(function ($validator) {
			if ($this->captcha) {
				$captcha = \App\CaptchaToken::validate(request(), $this->captcha);
				if (!$captcha) {
					$validator->errors()->add('captcha', 'The captcha is invalid.');
				}
			}

		});

		return;
	}
}
