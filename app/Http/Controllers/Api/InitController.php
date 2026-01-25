<?php
namespace App\Http\Controllers\Api;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;

class InitController extends Controller {
	public function initialDetails(Request $request) {
		try {
			$user = \Auth::user();
			$userModel = new User;
			$data = $userModel->userInit($user, false);
			return Helper::rj('Record found', 200, $data);
		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}

	public function customerInit(Request $request) {
		try {
			$validator = Validator::make($request->all(), [
				'entity_type' => 'required',
				'entity' => 'required',
			]);
			$input = $request->all();
			$user_id = \Auth::user()->id;
			$userModel = new User();
			$success = $userModel->userInit(\Auth::user(), false);

			return Helper::rj('Record found', 200, $success);
		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}

	}
	public function siteInitialDetails(Request $request) {
		try {

			$data['site'] = \App\SiteSetting::select("key", "val", "field_label", "field_type")
				->where("is_visible", 1)
				->get();

			return Helper::rj('Record found', 200, $data);
		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}

	public function updateSiteSetting(Request $request, $key = '') {
		try {
			$validationRules = [
				'val' => 'required',
			];

			$validator = Validator::make($request->all(), $validationRules);
			if ($validator->fails()) {
				return Helper::rj('Error.', 400, [
					'errors' => $validator->errors(),
				]);
			}

			$input = $request->all();
			$data = \App\Models\SiteSetting::where('key', $key)->first();
			$return = \App\Helpers\Helper::notValidData($data);
			if ($return) {
				return $return;
			}

			$data->update($input);

			\App\Models\SiteSetting::makeCacheSetting();

			return Helper::rj('Record has been successfully updated.', 200);
		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}
}
