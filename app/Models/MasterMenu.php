<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterMenu extends Model {
	public $timestamps = false;
	protected $table = 'master_menus';
	protected $fillable = [
		'parent_id',
		'display_order',
		'status',
		'class',
		'method',
		'query_params',
		'menu',
		'url',
		'icon',
	];
	private $parent_rec = array();

	public $orderBy = [];

	public $statuses = [
		0 => [
			'id' => 0,
			'name' => 'Disabled',
			'badge' => 'warning',
		],
		1 => [
			'id' => 1,
			'name' => 'Enabled',
			'badge' => 'success',
		],
	];

	public function getFilters($routeName = 'menus.index') {
		$status = \App\Helpers\Helper::makeSimpleArray($this->statuses, 'id,name');
		return [
			'reset' => route($routeName),
			'fields' => [
				'menu' => [
					'type' => 'text',
					'label' => 'Name',
				],
				'status' => [
					'type' => 'select',
					'label' => 'Status',
					'attributes' => [
						'id' => 'select-status',
					],
					'options' => $status,
				],
			],
		];
	}

	public function children() {
		return $this->hasMany('App\Models\MasterMenu', 'parent_id');
	}

	public function getQueryParamsAttribute($value) {
		return (array) json_decode($value);
	}

	public function getListing($srch_params = [], $offset = 0) {
		try {
			$select = [
				$this->table . ".*",
			];

			if (isset($srch_params['select'])) {
				$select = $srch_params['select'];
			}

			$listing = self::select($select)
				->when(isset($srch_params['with']), function ($q) use ($srch_params) {
					return $q->with($srch_params['with']);
				})
				->when(isset($srch_params['menu']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".menu", "LIKE", "%{$srch_params['menu']}%");
				})
				->when(isset($srch_params['parent_id']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".parent_id", $srch_params['parent_id']);
				})
				->when(isset($srch_params['status']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".status", $srch_params['status']);
				});

			if (isset($srch_params['id'])) {
				return $listing->where($this->table . '.id', '=', $srch_params['id'])
					->first();
			}

			if (isset($srch_params['count'])) {
				return $listing->count();
			}

			if (isset($srch_params['orderBy'])) {
				$orderBy = \App\Helpers\Helper::manageOrderBy($srch_params['orderBy']);
				foreach ($orderBy as $key => $value) {
					$listing->orderBy($key, $value);
				}
			} else {
				$listing->orderBy($this->table . '.display_order', 'ASC');
			}

			if (isset($srch_params['groupBy'])) {
				$groupBy = \App\Helpers\Helper::manageGroupBy($srch_params['groupBy']);
				foreach ($groupBy as $value) {
					$listing->groupBy($value);
				}
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
				$data = $this->find($id);

				$return = \App\Helpers\Helper::notValidData($data, $this->_routePrefix . '.index');
				if ($return) {
					return $return;
				}

				$data->update($input);
			} else {
				$data = $this->create($input);

				// if parent id exists
				// it will insert roles and
				// assign permission.
				if (isset($input['permission_required']) && $input['permission_required']) {
					$menuName = ucwords(str_replace(" ", "_", strtolower($input['menu'])));
					$permissionList = null;
					$permissionAdd = null;
					$permissionEdit = null;
					$permissionDelete = null;
					if ($input['permission_list']) {
						$permissionList = \App\Models\Permission::where([
							'class' => $input['class'],
							'method' => $input['permission_list'],
						])->first();
						if (!$permissionList) {
							$permissionList = \App\Models\Permission::create([
								'menu_id' => $data->id,
								'p_type' => $menuName . ' List',
								'class' => $input['class'],
								'method' => $input['permission_list'],
							]);
						}
					}

					if ($input['permission_add']) {
						$permissionAdd = \App\Models\Permission::where([
							'class' => $input['class'],
							'method' => $input['permission_add'],
						])->first();
						if (!$permissionAdd) {
							$permissionAdd = \App\Models\Permission::create([
								'menu_id' => $data->id,
								'p_type' => $menuName . ' Add',
								'class' => $input['class'],
								'method' => $input['permission_add'],
							]);
						}
					}

					if ($input['permission_edit']) {
						$permissionEdit = \App\Models\Permission::where([
							'class' => $input['class'],
							'method' => $input['permission_edit'],
						])->first();
						if (!$permissionEdit) {
							$permissionEdit = \App\Models\Permission::create([
								'menu_id' => $data->id,
								'p_type' => $menuName . ' Edit',
								'class' => $input['class'],
								'method' => $input['permission_edit'],
							]);
						}
					}

					if ($input['permission_delete']) {
						$permissionDelete = \App\Models\Permission::where([
							'class' => $input['class'],
							'method' => $input['permission_delete'],
						])->first();
						if (!$permissionDelete) {
							$permissionDelete = \App\Models\Permission::create([
								'menu_id' => $data->id,
								'p_type' => $menuName . ' Delete',
								'class' => $input['class'],
								'method' => $input['permission_delete'],
							]);
						}
					}

					$currentUserRole = \Auth::user()->roles->pluck('id')->toArray();
					$input['role_ids'] = isset($input['role_ids']) ? $input['role_ids'] : [];
					$input['role_ids'] = array_merge($input['role_ids'], $currentUserRole);
					$roleModel = new \App\Models\Role();
					$superAdminRole = $roleModel->getListing(['slug' => 'super-admin']);
					if ($currentUserRole != $superAdminRole->id) {
						$input['role_ids'][] = $superAdminRole->id;
					}
					$rolePermission = [];

					foreach ($input['role_ids'] as $role) {

						if ($permissionList) {
							$isExists = \App\Models\PermissionRole::where([
								'pid' => $permissionList->id,
								'rid' => $role,
							])->first();

							if (!$isExists) {
								$rolePermission[] = [
									'pid' => $permissionList->id,
									'rid' => $role,
								];
							}
						}

						if ($permissionAdd) {
							$isExists = \App\Models\PermissionRole::where([
								'pid' => $permissionAdd->id,
								'rid' => $role,
							])->first();

							if (!$isExists) {
								$rolePermission[] = [
									'pid' => $permissionAdd->id,
									'rid' => $role,
								];
							}
						}

						if ($permissionEdit) {
							$isExists = \App\Models\PermissionRole::where([
								'pid' => $permissionEdit->id,
								'rid' => $role,
							])->first();

							if (!$isExists) {
								$rolePermission[] = [
									'pid' => $permissionEdit->id,
									'rid' => $role,
								];
							}
						}

						if ($permissionDelete) {
							$isExists = \App\Models\PermissionRole::where([
								'pid' => $permissionDelete->id,
								'rid' => $role,
							])->first();

							if (!$isExists) {
								$rolePermission[] = [
									'pid' => $permissionDelete->id,
									'rid' => $role,
								];
							}
						}

					}

					\App\Models\PermissionRole::insert($rolePermission);
				}
			}

			return \App\Helpers\Helper::resp('Changes has been successfully saved.', 200, $data);
		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public function remove($parent_id, $id) {
		try {
			$data = MasterMenu::find($id);

			if (!$data) {
				return \App\Helpers\Helper::resp('Not a valid data', 400);
			}

			$children = $data->children;
			if ($data->delete() && $children->count()) {
				$children->delete();
			}

			return \App\Helpers\Helper::resp('Record has been successfully deleted.', 200, $data);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public static function getMenu($pid = 0) {
		try {
			$menu = self::select()
				->where([
					'status' => '1',
					'parent_id' => $pid,
				])
				->orderBy('display_order', 'ASC')
				->get();

			if (empty($menu)) {
				return false;
			}

			$menu = $menu->toArray();
			$menus = array();
			foreach ($menu as $key => $val) {
				if ($val['class'] && $val['method']) {
					$permission = \App\Models\Permission::checkPermission($val['class'], $val['method']);
					if ($permission) {
						$menus[$key] = $val;
						$menus[$key]['child'] = self::getMenu($val['id']);
					}
				} else {
					$menus[$key] = $val;
					$menus[$key]['child'] = self::getMenu($val['id']);
				}
			}

			return $menus;

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public function getParentMenu($id) {
		$p = $this->__getPreviousRecord($id);
		return $this->parent_rec;
	}

	private function __getPreviousRecord($id = 0) {
		try {
			$cat = self::where('id', $id)->first();

			if (!$cat) {
				$this->parent_rec = array_reverse($this->parent_rec);
				return false;
			}

			$this->parent_rec[] = $cat->toArray();
			$this->__getPreviousRecord($cat->parent_id);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public function getMenuHierarchyWithPermission($srch_params = [], $parent_id = 0) {
		$permissionModel = new Permission;
		$menu = $this->where('parent_id', $parent_id)
			->where('status', $srch_params['status'])
			->orderBy('display_order', 'ASC')
			->get()
			->toArray();

		if ($menu) {
			foreach ($menu as $key => $val) {
				$srch_params['menu_id'] = $val['id'];
				$menu[$key]['permissions'] = $permissionModel->getListing($srch_params);
				$menu[$key]['children'] = $this->getMenuHierarchyWithPermission($srch_params, $val['id']);
			}
		}

		return $menu;
	}
}
