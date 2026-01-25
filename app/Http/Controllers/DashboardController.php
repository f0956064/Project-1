<?php

namespace App\Http\Controllers;

class DashboardController extends Controller {

	public function index() {
		try {
			$data = [];
			$data['breadcrumb'] = [
				'#' => 'Dashboard',
			];

			return view('admin.index', $data);
		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}
}
