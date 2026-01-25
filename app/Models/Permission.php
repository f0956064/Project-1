<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Permission extends Authenticatable {
	use HasApiTokens, Notifiable;

	protected $table = 'permissions';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'menu_id',
		'p_type',
		'class',
		'method',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	public function getFilters($routeName = 'permissions.index') {
		return [
			'reset' => route($routeName),
			'fields' => [
				'p_type' => [
					'type' => 'text',
					'label' => 'Permission Type',
				],
				'class' => [
					'type' => 'text',
					'label' => 'Module',
				],
				'method' => [
					'type' => 'text',
					'label' => 'Function',
				],
			],
		];
	}

	public $orderBy = [];

	public function getListing($srch_params = [], $offset = 0) {
		try {

			$select = [
				$this->table . ".*",
				"m.menu",
			];

			if (isset($srch_params['select'])) {
				$select = $srch_params['select'];
			}

			$listing = self::select($select)
				->when(isset($srch_params['with']), function ($q) use ($srch_params) {
					return $q->with($srch_params['with']);
				})
				->leftJoin('master_menus AS m', function ($join) {
					$join->on('m.id', $this->table . '.menu_id');
				})
				->when((isset($srch_params['role_id']) || isset($srch_params['role_id_in'])), function ($q) use ($srch_params) {
					$q->join('permission_roles AS pr', function ($join) use ($srch_params) {
						$join->on('pr.pid', '=', 'permissions.id')
							->when(isset($srch_params['role_id']), function ($q) use ($srch_params) {
								return $q->where('pr.rid', $srch_params['role_id']);
							})
							->when(isset($srch_params['role_id_in']), function ($q) use ($srch_params) {
								return $q->whereIn('pr.rid', $srch_params['role_id_in']);
							});
					});
				})
				->when(isset($srch_params['p_type']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".p_type", "LIKE", "%{$srch_params['p_type']}%");
				})
				->when(isset($srch_params['class']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".class", $srch_params['class']);
				})
				->when(isset($srch_params['menu_id']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".menu_id", $srch_params['menu_id']);
				})
				->when(isset($srch_params['method']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".method", $srch_params['method']);
				});

			if (isset($srch_params['id'])) {
				return $listing->where($this->table . '.id', '=', $srch_params['id'])
					->first();
			}

			if (isset($srch_params['count'])) {
				return $listing->count();
			}

			if (isset($srch_params['orderBy'])) {
				$this->orderBy = \App\Helpers\Helper::manageOrderBy($srch_params['orderBy']);
				foreach ($this->orderBy as $key => $value) {
					$listing->orderBy($key, $value);
				}
			} else {
				$listing->orderBy($this->table . '.id', 'DESC');
			}

			if (isset($srch_params['get_sql']) && $srch_params['get_sql']) {
				return \App\Helpers\Helper::getSql([
					$listing->toSql(),
					$listing->getBindings(),
				]);
			}

			if ($offset) {
				$listing = $listing->paginate($offset);
			} else {
				$listing = $listing->get();
			}

			return $listing;
		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public function store($input = [], $id = 0, $request = null) {
		try {
			if ($id) {
				$data = $this->getListing(['id' => $id]);

				$return = \App\Helpers\Helper::notValidData($data, $this->_routePrefix . '.index');
				if ($return) {
					return $return;
				}

				$data->update($input);
			} else {
				if (!$input['method']) {
					$data = [];
					$pType = $input['p_type'];
					$defaultMethods = [
						[
							'method' => 'index',
							'name' => 'List',
						],
						[
							'method' => 'create',
							'name' => 'Add',
						],
						[
							'method' => 'edit',
							'name' => 'Edit',
						],
						[
							'method' => 'destroy',
							'name' => 'Delete',
						],
					];
					foreach ($defaultMethods as $method) {
						$input['method'] = $method['method'];
						$input['p_type'] = $pType . ' ' . $method['method'];

						$data[] = $input;
					}
					$data = $this->insert($data);
				} else {
					if (isset($input['method']) && $input['method']) {
						$methods = explode(',', $input['method']);
						if (count($methods) > 1) {
							$data = [];
							$pType = explode(",", $input['p_type']);
							foreach ($methods as $key => $method) {
								$input['method'] = trim($method);
								$input['p_type'] = $pType[$key] ?? $method;

								$data[] = $input;
							}

							$data = $this->insert($data);
							return \App\Helpers\Helper::resp('Changes has been successfully saved.', 200, $data);
						}
					}

					$data = $this->create($input);

				}
			}

			return \App\Helpers\Helper::resp('Changes has been successfully saved.', 200, $data);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public static function checkPermission($className = '', $methodName = '', $userRoles = []) {
		try {
			if (!$userRoles) {
				$user = \Auth::user();
				$userModel = new \App\Models\User();
				$userRoles = $userModel->myRoles([
					'user_id' => $user->id,
				], false);
				$userRoles = $userRoles->pluck('id')->toArray();
			}

			// if this is a super admin
			// set all permission
			if (in_array(1, $userRoles)) {
				return true;
			}

			$className = !$className ? Helper::getController() : $className;
			$methodName = !$methodName ? Helper::getMethod() : $methodName;

			$permission = self::select("permissions.*")
				->addSelect(\DB::raw("IF(pr.pid, 1, 0) AS has_permission"))
				->leftJoin("permission_roles AS pr", function ($q) use ($userRoles) {
					$q->on("pr.pid", "=", "permissions.id")
						->whereIn("pr.rid", $userRoles);
				})
				->where("permissions.class", "=", $className)
				->where("permissions.method", "=", $methodName)
				->first();

			// if result found
			if ($permission) {
				// if permission found
				if ($permission->has_permission) {
					return true;
				}

				return false;
			}

			// if permission not entered on parent
			// table, then it will be accessible
			// by all users.
			return true;

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	/**
	 * Check module wise permissions. It checks given methods permissions
	 * from given class name. and return method wise permission result.
	 */

	public static function checkModulePermissions($methods = ['index', 'create', 'edit', 'destroy'], $class = '') {
		try {
			$user = \Auth::user();
			$userModel = new \App\Models\User;
			$userRoles = $userModel->myRoles([
				'user_id' => $user->id,
			], false);
			$userRoles = $userRoles->pluck('id')->toArray();

			// if this is super admin, grant all access
			if (in_array(1, $userRoles)) {
				$permission = [];
				foreach ($methods as $value) {
					$permission[$value] = true;
				}
				return $permission;
			}

			$all_permission = TRUE;
			$roles = null;

			if (!$class) {
				$class = Helper::getController();
			}

			//
			// get methodwise permissions
			//

			$permission = self::where('class', $class)
				->whereIn('method', $methods)
				->get();

			if ($permission) {
				$permission = $permission->toArray();
				$permission_ids = Helper::makeSimpleArray($permission, "id", TRUE);

				if ($permission_ids) {
					$roles = \App\Models\PermissionRole::whereIn('rid', $userRoles)
						->whereIn('pid', $permission_ids)
						->get();

					if ($roles->count()) {
						$roles = $roles->toArray();
						$roles = Helper::makeSimpleArray($roles, "pid", TRUE);
						$module_permission = array();
						foreach ($permission as $key => $val) {
							$module_permission[$val['method']] = in_array($val['id'], $roles);
						}
						$permission = $module_permission;
					} else {
						$permission = [];
						$all_permission = FALSE;
					}
				}
			}

			if (count($methods) > count($permission)) {
				foreach ($methods as $val) {
					if (!isset($permission[$val])) {
						$permission[$val] = $all_permission;
					}
				}
			}

			if (!$permission || !$roles) {
				$permission = array();
				foreach ($methods as $val) {
					$permission[$val] = $all_permission;
				}
			}

			return $permission;

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public function setBulkMenu($input = []) {
		$permissionIds = $input['permission_ids'] ? explode(',', $input['permission_ids']) : null;
		if ($permissionIds) {
			$menuId = end($input['menu_id']);
			$data = self::whereIn('id', $permissionIds)->update(['menu_id' => $menuId]);
			return \App\Helpers\Helper::resp('Menu added', 200);
		}

		return \App\Helpers\Helper::resp('No permission has chosen', 400);
	}

	public function deleteBulkPermissions($input = []) {
		$permissionIds = $input['permission_ids'] ? explode(',', $input['permission_ids']) : null;
		if ($permissionIds) {
			$data = self::whereIn('id', $permissionIds)->delete();
			return \App\Helpers\Helper::resp('Permission deleted', 200);
		}

		return \App\Helpers\Helper::resp('No permission has chosen', 400);
	}

	public function manageRoleView($model, $routePrefix, $permission, $accordionParent, $val) {
		return view('admin.' . $routePrefix . '.manage-role-accordion', [
			'model' => $model,
			'routePrefix' => $routePrefix,
			'permission' => $permission,
			'accordionParent' => $accordionParent . $val['id'],
			'menus' => $val['children'],
		])->render();
	}
}
