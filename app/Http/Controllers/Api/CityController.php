<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\LocationCity;
use Illuminate\Http\Request;

class CityController extends Controller {
	//

	public function __construct($parameters = array()) {
		parent::__construct($parameters);

		$this->_module = 'City';
		$this->_routePrefix = 'cities';
		$this->_model = new LocationCity();
	}
	public function index(Request $request) {
		try {
			$srch_params = $request->all();
			$srch_params['status'] = 1;
			// $srch_params['orderBy']    = "location_cities__city_name";

			$data['list'] = $this->_model->getListing($srch_params);
			$data['list']->makeHidden(['status', 'created_at']);
			return Helper::rj('Record found', 200, $data);
		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}

	}

}
