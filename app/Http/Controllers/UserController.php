<?php
namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
			$this->initIndex();

			$user = \Auth::user();
			$srch_params = $request->all();
			$srch_params['role_gte'] = $this->_model->myRoleMinLevel($user->id);
			$this->_data['data'] = $this->_model->getListing($srch_params, $this->_offset);
			$this->_data['filters'] = $this->_model->getFilters();
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
	public function store(UserRequest $request) {
		return $this->__formPost($request);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $id) {
		try {
			$this->_data['data'] = $this->_model->getListing(['id' => $id]);
			$this->_data['module'] = $this->_data['data']->full_name;
			$breadcrumb = [
				route($this->_routePrefix . '.index') => str_plural($this->_module),
				'#' => $this->_data['module'],
			];

			$this->_data['roles'] = $this->_data['data']->roles->pluck('title')->toArray();
			$this->_data['routePrefix'] = $this->_routePrefix;

			return $this->modal();
		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
	}

	public function export(Request $request) {
		try {
			$user = \Auth::user();
			$srch_params = $request->all();
			$srch_params['role_gte'] = $this->_model->myRoleMinLevel($user->id);
			$data = $this->_model->getListing($srch_params, $this->_offset);

			$this->_data['columns'] = array(
				'First Name',
				'Last Name',
				'Email Name',
				'Phone',
			);

			foreach ($data as $value) {
				$this->_data['data'][] = [
					$value->first_name,
					$value->last_name,
					$value->email,
					$value->phone,
				];
			}

			return \App\Helpers\Helper::exportCsv($this->_data, 'user');
		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}
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
	public function update(UserRequest $request, $id) {
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
			} else {
				return redirect()
					->route($this->_routePrefix . '.index')
					->with('error', $response['message']);
			}

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
			$roles = [];
			$userRoles = [];
			$ownAccount = true;

			$this->initUIGeneration($id, false);
			extract($this->_data);
			if ($id) {
				$this->_data['data'] = $this->_model->getListing([
					'id' => $id,
					'id_greater_than' => \Auth::user()->id,
					'with' => ['wallet']
				]);

				$return = \App\Helpers\Helper::notValidData($this->_data['data'], $this->_routePrefix . '.index');
				if ($return) {
					return $return;
				}

				$moduleName = 'Edit ' . $this->_module;
				$userRoles = $this->_data['data']->roles->pluck('id')->toArray();
			}

			if (Auth::user()->id != $id) {
				$ownAccount = false;
				$roleModel = new \App\Models\Role;
				$userMinRole = $this->_model->myRoleMinLevel(\Auth::user()->id);
				$roles = $roleModel->getListing([
					'level_gte' => $userMinRole,
					'orderBy' => 'roles__level',
				])
					->pluck('title', 'id')
					->all();
			}
			$status = \App\Helpers\Helper::makeSimpleArray($this->_model->statuses, 'id,name');
			$this->_data['form'] = [
				'route' => $this->_routePrefix . ($id ? '.update' : '.store'),
				'back_route' => route($this->_routePrefix . '.index'),
				'include_scripts' => '<script src="' . asset('admin-form-plugins/form-controls.js') . '"></script>',
				'fields' => [
					'username' => [
						'type' => 'text',
						'label' => 'Username',
						'help' => 'Maximum 50 characters',
						'attributes' => ['required' => true],
						/*'row_width'=> 'col-lg-6 col-md-6 col-sm-12 col-xs-12',
							    'label_width' => 'col-lg-12 col-sm-12',
						*/
					],
					'first_name' => [
						'type' => 'text',
						'label' => 'First Name',
						'help' => 'Maximum 100 characters',
						'attributes' => ['required' => true],
						/*'row_width'=> 'col-lg-6 col-md-6 col-sm-12 col-xs-12',
							    'label_width' => 'col-lg-12 col-sm-12',
						*/
					],
					'last_name' => [
						'type' => 'text',
						'label' => 'Last Name',
						'help' => 'Maximum 100 characters',
					],
					'email' => [
						'type' => 'email',
						'label' => 'Email',
						'help' => 'Maximum 255 characters',
						'attributes' => ['required' => true],
					],
					'max_withdrawal' => [
						'type' => 'number',
						'label' => 'Max Withdrawal',
						'value' => isset($this->_data['data']->wallet->max_withdrawal) ? $this->_data['data']->wallet->max_withdrawal : 5,
						'attributes' => [
							'min' => 0,
						],
					],
					'phone' => [
						'type' => 'text',
						'label' => 'Phone',
						'help' => 'Maximum 12 characters',
						'attributes' => ['required' => true],
					],
					'password' => [
						'type' => 'password',
						'label' => 'Password',
						'help' => \App\Models\User::$passwordRequirementText,
					],
					'confirm-password' => [
						'type' => 'password',
						'label' => 'Confirm Password',
					],
					'avatar' => [
						'type' => 'file',
						'label' => 'Avatar',
						'value' => isset($data->profile_pic) ? $data->profile_pic : [],
						'attributes' => [
							'cropper' => true,
							'ratio' => 1 / 1,
						],
					],
					// 'appointments[]' => [
					//                 'type'          => 'file',
					//                 'label'         => 'Attachments',
					//                 'attributes'    => ['multiple' => true],
					//                 'value'         => isset($data->appointment) ? $data->appointment : []
					// ],
					'role_id' => [
						'type' => 'checkbox',
						'label' => 'Role',
						'options' => $roles,
						'attributes' => ['width' => 'col-lg-4 col-md-4 col-sm-12 col-xs-12'],
						'value' => $userRoles,
						// if you want to single role for an User
						// change to radio instead of checkbox.
						// Comment upper value tag. And
						// uncomment value tag from below.
						// 'value'     => isset($userRoles[0]) ? $userRoles[0] : 0
					],
					'status' => [
						'type' => 'radio',
						'label' => 'Status',
						'options' => $status,
						'value' => isset($data->status) ? $data->status : 1,
						'attributes' => ['width' => 'col-lg-4 col-md-4 col-sm-12 col-xs-12'],
					],
				],
			];

			if ($ownAccount) {
				unset($this->_data['form']['back_route']);
				unset($this->_data['form']['fields']['role_id']);
				unset($this->_data['form']['fields']['status']);
				unset($this->_data['form']['fields']['username']['attributes']['required']);
				unset($this->_data['form']['fields']['email']['attributes']['required']);

				$this->_data['form']['fields']['username']['attributes']['readonly'] = true;
				$this->_data['form']['fields']['email']['attributes']['readonly'] = true;
			}

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
	protected function __formPost(UserRequest $request, $id = 0) {
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

			if (in_array($response['status'], [200, 201])) {
				if (!$isOwnAcc) {
					return redirect()
						->route($this->_routePrefix . '.index')
						->with('success', $response['message']);
				} else {
					return redirect()
						->route($this->_routePrefix . '.edit', $id)
						->with('success', $response['message']);
				}
			} else {
				return redirect()
					->route($this->_routePrefix . '.index')
					->with('error', $response['message']);
			}

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::rj($e->getMessage(), 500);
		}

	}
}
