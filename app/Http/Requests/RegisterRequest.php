<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	private $_id;

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules(Request $request)
	{
		$validationRules = [
			'first_name' => 'required|max:100',
			'last_name' => 'required|max:100',
			'email'     => 'required|email|max:255|unique:users,email,0,id,deleted_at,NULL',
			'phone'     => 'required|unique:user_phones,phone,0,id,deleted_at,NULL',
			'captcha'	=> 'required'
		];

		return $validationRules;
	}

	/**
	 * [failedValidation [Overriding the event validator for custom error response]]
	 * @param  Validator $validator [description]
	 * @return [object][object of various validation errors]
	 */
	public function failedValidation(Validator $validator)
	{
		//write your bussiness logic here otherwise it will give same old JSON response
		$response = \App\Helpers\Helper::resp('Bad Request', 400, [
			'errors' => $validator->errors()
		]);
		throw new HttpResponseException(response()->json($response, 400));
	}

	public function message()
	{
		$validationMessages = [];

		return $validationMessages;
	}

	public function withValidator($validator)
	{
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
