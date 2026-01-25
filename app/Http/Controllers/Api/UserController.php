<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePassword;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller {
	public function __construct($parameters = array()) {
		parent::__construct($parameters);

		$this->_module = 'User';
		$this->_routePrefix = 'users';
		$this->_model = new User();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		try {
			$srch_params = $request->all();
			$srch_params['role_gte'] = $this->_model->myRoleMinLevel(\Auth::user()->id);
			$data['list'] = $this->_model->getListing($srch_params, $this->_offset);

			return Helper::rj('Record found', 200, $data);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id) {
		try {
			$data['details'] = $this->_model->getListing(['id' => $id]);

			return Helper::rj('Record found', 200, $data);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(UserRequest $request) {
		return $this->__formPost($request);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(UserRequest $request, $id) {
		return $this->__formPost($request, $id);
	}

	public function uploadAvatar(Request $request) {
		try {
			$fileValidations = \App\Models\File::$fileValidations['image'];
			$validationRules = [
				'user_id' => 'nullable|exists:users,id',
				'avatar' => 'required|mimes:' . $fileValidations['mime'] . '|max:' . $fileValidations['max'],
			];

			$validationMessages = [
				'user_id.required' => 'The user is required',
				'user_id.exists' => 'The selected user is invalid.',
			];

			$validator = \Validator::make($request->all(), $validationRules, $validationMessages);
			if ($validator->fails()) {
				return Helper::rj('Error.', 400, [
					'errors' => $validator->errors(),
				]);
			}

			$userId = $request->get('user_id');
			$userId = $userId ? $userId : \Auth::user()->id;
			$data = $this->_model->getListing([
				'id' => $userId,
			]);
			$response = $this->_model->uploadAvatar($data, $userId, $request);
			$status = $response['status'];
			if ($status == 200) {
				$data = $this->_model->getListing([
					'id' => $userId,
				]);
				$data = $this->_model->userInit($data, false);
			}
			return Helper::rj($response['message'], $status, [
				'details' => $data,
			]);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}

	public function updatePassword(UpdatePassword $request) {
		try {
			$data = \Auth::user();
			// dd($data);
			$input = $request->all();
			$input['password'] = \Hash::make($input['password']);

			$data->update($input);

			return Helper::rj('Password has been successfully updated.', 200);
		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		try {
			$response = $this->_model->delete($id);
			return Helper::rj($response['message'], $response['status'], [
				'details' => $response['data'],
			]);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}

	/**
	 * Form post action
	 *
	 * @param  Request $request [description]
	 * @param  string  $id      [description]
	 * @return [type]           [description]
	 */
	private function __formPost(UserRequest $request, $id = 0) {
		try {
			$isOwnAcc = true;
			//
			// if this is not own account, it will
			// require role.
			//
			if (Auth::user()->id != $id) {
				$isOwnAcc = false;
			}
			$input = $request->all();
			$response = $this->_model->store($input, $id, $request);
			return Helper::rj($response['message'], $response['status'], [
				'details' => $response['data'],
			]);
		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}
}
