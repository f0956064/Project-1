<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Role extends Authenticatable {
	use HasApiTokens, Notifiable;

	protected $table = 'roles';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'pid',
		'user_id',
		'title',
		'slug',
		'level',
		'status',
	];

	public $orderBy = [];

	public function getUser() {
		return $this->belongsTo('App\Models\User', 'user_id');
	}

	public function users() {
		return $this->hasMany('App\Models\UserRole', 'role_id', 'id');
	}

	public function permissions() {
		return $this->hasMany('App\Models\PermissionRole', 'rid', 'id');
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
				->when(isset($srch_params['title']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".title", "LIKE", "%{$srch_params['title']}%");
				})
				->when(isset($srch_params['user_id']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".user_id", $srch_params['user_id']);
				})
				->when(isset($srch_params['status']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".status", $srch_params['status']);
				})
				->when(isset($srch_params['level_gt']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".level", ">", $srch_params['level_gt']);
				})
				->when(isset($srch_params['level_gte']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".level", ">=", $srch_params['level_gte']);
				});

			if (isset($srch_params['slug'])) {
				return $listing->where($this->table . '.slug', '=', $srch_params['slug'])
					->first();
			}
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
			$userId = \Auth::user()->id;

			if ($id) {
				$data = $this->getListing(['id' => $id, 'user_id' => $userId]);

				$return = \App\Helpers\Helper::notValidData($data, $this->_routePrefix . '.index');
				if ($return) {
					return $return;
				}

				$data->update($input);
			} else {
				$userModel = new \App\Models\User();
				$myLevel = $userModel->myRoleMinLevel($userId);
				$input['user_id'] = $userId;
				$input['slug'] = \App\Helpers\Helper::getUniqueSlug($input['title'], 'roles', 'slug');
				$input['level'] = ++$myLevel;
				$data = $this->create($input);
			}

			return \App\Helpers\Helper::resp('Changes has been successfully saved.', 200, $data);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public function remove($id) {
		try {
			$userId = \Auth::user()->id;
			$data = $this->getListing(['id' => $id, 'user_id' => $userId]);

			if (!$data) {
				return \App\Helpers\Helper::resp('Not a valid data', 400);
			}

			$users = $data->users();
			$permissions = $data->permissions();
			if ($data->delete()) {
				$users->delete();
				$permissions->delete();
			}

			return \App\Helpers\Helper::resp('Record has been successfully deleted.', 200, $data);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}
}
