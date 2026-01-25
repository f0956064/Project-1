<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller {

	public function __construct($parameters = array()) {
		parent::__construct($parameters);

		$this->_module = 'Role';
		$this->_routePrefix = 'roles';
		$this->_model = new Role();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		try {
			$this->initIndex();

			$manageRole = \App\Models\Permission::checkModulePermissions(['manageRole'], 'PermissionController');
			$this->_data['permission'] = array_merge($this->_data['permission'], $manageRole);
			$this->_data['userId'] = \Auth::user()->id;
			$userModel = new \App\Models\User();
			$myLevel = $userModel->myRoleMinLevel($this->_data['userId']);
			$srch_params = $request->all();
			$srch_params['level_gt'] = $myLevel;

			if (!$request->has('orderBy')) {
				$srch_params['orderBy'] = 'roles__level';
			}
			$this->_data['data'] = $this->_model->getListing($srch_params, $this->_offset);

			$this->_data['orderBy'] = $this->_model->orderBy;
			return view('admin.' . $this->_routePrefix . '.index', $this->_data)
				->with('i', ($request->input('page', 1) - 1) * $this->_offset);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request) {
		return $this->__formUiGeneration($request);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		return $this->__formPost($request);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id) {
		return $this->__formUiGeneration($request, $id);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		return $this->__formPost($request, $id);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		try {
			$response = $this->_model->remove($id);

			if ($response['status'] == 200) {
				return redirect()
					->route($this->_routePrefix . '.index')
					->with('success', $response['message']);
			}
			return redirect()
				->route($this->_routePrefix . '.index')
				->with('error', $response['message']);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}

	/**
	 * ui parameters for form add and edit
	 *
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	protected function __formUiGeneration(Request $request, $id = '') {
		try {
			$this->initUIGeneration($id, false);
			extract($this->_data);

			if ($id) {
				$userId = \Auth::user()->id;
				$data = $this->_model->getListing(['id' => $id, 'user_id' => $userId]);

				$return = \App\Helpers\Helper::notValidData($data, $this->_routePrefix . '.index');
				if ($return) {
					return $return;
				}
			}

			$this->_data['form'] = [
				'route' => $this->_routePrefix . ($id ? '.update' : '.store'),
				'back_route' => route($this->_routePrefix . '.index'),
				'fields' => [
					'title' => [
						'type' => 'text',
						'label' => 'Title',
						'help' => 'Maximum 255 characters',
						'attributes' => ['required' => true],
					],
				],
			];

			return view('admin.components.admin-form', $this->_data);

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
	protected function __formPost(Request $request, $id = '') {
		try {
			$this->validate($request, [
				'title' => 'required|max:255',
			]);

			$input = $request->all();
			$response = $this->_model->store($input, $id, $request);

			if (in_array($response['status'], [200, 201])) {
				return redirect()
					->back()
					->with('success', $response['message']);
			}
			return redirect()
				->back()
				->with('error', $response['message']);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}
}
