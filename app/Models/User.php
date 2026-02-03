<?php

namespace App\Models;

use App\Helpers\Helper;
use App\Models\File;
use Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable {
	use HasApiTokens, Notifiable, SoftDeletes;

	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'username',
		'first_name',
		'last_name',
		'email',
		'phone',
		'referral_code',
		'referred_by_id',
		'password',
		'remember_token',
		'otp',
		'otp_expires_at',
		'status', // 0 = inactive and email unverified, 1 = active, 2 = inactive and email verified, 3 = user rejected by admin
		'verified',
		'login_attempt',
		'name_initials',
		'name_initial_color_type',
	];

	public $statuses = [
		0 => [
			'id' => 0,
			'name' => 'Pending email verification',
			'badge' => 'warning',
		],
		1 => [
			'id' => 1,
			'name' => 'Approved',
			'badge' => 'success',
		],
		2 => [
			'id' => 2,
			'name' => 'Pending acceptance',
			'badge' => 'danger',
		],
		3 => [
			'id' => 3,
			'name' => 'Rejected',
			'badge' => 'secondary',
		],
		4 => [
			'id' => 3,
			'name' => 'Blocked',
			'badge' => 'secondary',
		],
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		// 'email',
		'email_verified_at',
		'role_title',
		'password',
		'remember_token',
		'otp',
		'otp_expires_at',
		'created_at',
		'updated_at',
		'deleted_at',
		'profile_pic',
	];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'email_verified_at' => 'datetime',
		'otp_expires_at' => 'datetime',
	];

	// for avatar
	protected $appends = [
		'avatar',
		'full_name',
	];

	public static $passwordValidator = [
		'required',
		'min:6', // must be at least 6 characters in length
		'max:16', // must be less than 16 characters in length
		// 'regex:/[a-z]/', // must contain at least one lowercase letter
		'regex:/[a-zA-Z]/', // must contain atleast one uppercase letter
		'regex:/[0-9]/', // must contain at least one digit
		// 'regex:/[@$!%*#?&]/' // must contain a special character.
	];

	public static $passwordRequirementText = 'Password must contains 6-16 characters in length, one lowercase, one uppercase letter, and a digit.';

	public $orderBy = [];

	public function getFilters($routeName = 'users.index') {
		$roleModel = new \App\Models\Role();
		$userMinRole = $this->myRoleMinLevel(\Auth::user()->id);
		$roles = $roleModel->getListing([
			'level_gte' => $userMinRole,
			'orderBy' => 'roles__level',
		])
			->pluck('title', 'slug')
			->all();
		$status = \App\Helpers\Helper::makeSimpleArray($this->statuses, 'id,name');
		return [
			'reset' => route($routeName),
			'fields' => [
				'full_name' => [
					'type' => 'text',
					'label' => 'Name',
				],
				'email' => [
					'type' => 'text',
					'label' => 'Email',
				],
				'phone' => [
					'type' => 'text',
					'label' => 'Phone',
				],
				'role' => [
					'type' => 'select',
					'label' => 'Role',
					'options' => $roles,
				],
				'status' => [
					'type' => 'select',
					'label' => 'Status',
					'attributes' => [
						'id' => 'select-status',
					],
					'options' => $status,
				],
				'created_at' => [
					'type' => 'date',
					'label' => 'Created At',
				],
			],
		];
	}

	public function getFullNameAttribute() {
		return $this->first_name . ' ' . $this->last_name;
	}

	public function getAvatarAttribute() {
		return File::file($this->profile_pic, 'no-profile.jpg');
	}

	public function profile_pic() {
		$entityType = isset(File::$fileType['user_avatar']['type']) ? File::$fileType['user_avatar']['type'] : 0;
		return $this->hasOne('App\Models\File', 'entity_id', 'id')
			->where('entity_type', $entityType);
	}

	/*public function getAppointmentAttribute() {
			$appo = [];
			foreach ($this->appointments as $key => $value) {
				$appo[] = File::file($value);
			}
			return $appo;
		}

		public function appointments() {
			return $this->hasMany('App\Models\File', 'entity_id', 'id')
				->where('entity_type', 2);
		}
	*/

	public function verifyUser() {
		return $this->hasOne('App\Models\VerifyUser');
	}

	public function roles() {
		// return $this->hasMany('App\Models\UserRole');
		return $this->belongsToMany('App\Models\Role', 'user_roles');
	}

	public function wallet() {
		return $this->hasOne(\App\Models\UserWallet::class, 'user_id');
	}

	public function deposits() {
		return $this->hasMany(\App\Models\UserDeposit::class, 'user_id');
	}

	public function withdrawals() {
		return $this->hasMany(\App\Models\UserWithdrawal::class, 'user_id');
	}

	public function guesses() {
		return $this->hasMany(\App\Models\UserGuess::class, 'user_id');
	}

	public function profile() {
		return $this->hasOne(\App\Models\UserProfile::class, 'user_id');
	}

	public function referredBy() {
		return $this->belongsTo(\App\Models\User::class, 'referred_by_id');
	}

	public function AauthAcessToken() {
		return $this->hasMany('\App\Models\OauthAccessToken');
	}

	public function getListing($srch_params = [], $offset = 0) {
		try {
			$select = [
				$this->table . ".*",
				"r.title AS role_title",
			];

			if (isset($srch_params['select'])) {
				$select = $srch_params['select'];
			}

			$listing = self::select($select)
				->when(isset($srch_params['with']), function ($q) use ($srch_params) {
					return $q->with($srch_params['with']);
				})
				->addSelect(\DB::raw("CONCAT({$this->table}.first_name, ' ', {$this->table}.last_name) AS full_name"))
				->join("user_roles AS ur", function ($join) {
					$join->on($this->table . ".id", "ur.user_id");
				})
				->join("roles AS r", function ($join) {
					$join->on("r.id", "ur.role_id");
				})
				->when(isset($srch_params['role_slug']), function ($q) use ($srch_params) {
					return $q->where('r.slug', $srch_params['role_slug']);
				})
				->when(isset($srch_params['id_gte']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".id", ">=", $srch_params['id_gte']);
				})
				->when(isset($srch_params['name']), function ($q) use ($srch_params) {
					return $q->whereRaw("{$this->table}.first_name LIKE '{$srch_params['name']}%'");
				})
				->when(isset($srch_params['full_name']), function ($q) use ($srch_params) {
					return $q->whereRaw("CONCAT({$this->table}.first_name, ' ', {$this->table}.last_name) LIKE '%{$srch_params['full_name']}%'");
				})
				->when(isset($srch_params['email']), function ($q) use ($srch_params) {
					return $q->whereRaw("{$this->table}.email LIKE '%{$srch_params['email']}%'");
				})
				->when(isset($srch_params['role']), function ($q) use ($srch_params) {
					return $q->where("r.slug", $srch_params['role']);
				})
				->when(isset($srch_params['role_gte']), function ($q) use ($srch_params) {
					return $q->where("r.level", ">=", $srch_params['role_gte']);
				})
				->when(isset($srch_params['created_at']), function ($q) use ($srch_params) {
					return $q->whereDate($this->table . ".created_at", $srch_params['created_at']);
				})
				->when(isset($srch_params['status']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".status", $srch_params['status']);
				});

			if (isset($srch_params['username'])) {
				return $listing->where($this->table . '.username', '=', $srch_params['username'])
					->first();
			}
			if (isset($srch_params['id'])) {
				return $listing->where($this->table . '.id', '=', $srch_params['id'])
					->first();
			}

			if (isset($srch_params['count'])) {
				return $listing->get()->count();
			}

			if (isset($srch_params['orderBy'])) {
				$this->orderBy = \App\Helpers\Helper::manageOrderBy($srch_params['orderBy']);
				foreach ($this->orderBy as $key => $value) {
					$listing->orderBy($key, $value);
				}
			} else {
				$listing->orderBy('id', 'ASC');
			}

			if (isset($srch_params['groupBy'])) {
				$groupBy = \App\Helpers\Helper::manageGroupBy($srch_params['groupBy']);
				foreach ($groupBy as $value) {
					$listing->groupBy($value);
				}
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

	public function myRoleMinLevel($user_id) {
		$levels = $this->myRoles([
			'user_id' => $user_id,
			'first' => true,
		], false);

		return $levels->level;
	}

	public function myRoles($srch_params = [], $requiredRoles = true) {
		try {
			if (!isset($srch_params['user_id'])) {
				$srch_params['user_id'] = \Auth::user()->id;
			}

			$roles = self::select("r.*")
				->join("user_roles AS ur", function ($join) use ($srch_params) {
					$join->on("users.id", "ur.user_id");
				})
				->join("roles AS r", function ($join) use ($srch_params) {
					$join->on("r.id", "ur.role_id");
				})
				->when($srch_params['user_id'], function ($q) use ($srch_params) {
					return $q->where("users.id", $srch_params['user_id']);
				})
				->orderBy('r.level', 'ASC');

			if (isset($srch_params['first']) && $srch_params['first']) {
				return $roles->first();
			}

			$roles = $roles->get();

			if ($requiredRoles) {
				return $roles->pluck('slug')->toArray();
			}

			return $roles;

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public function store($input = [], $id = 0, $request = null) {
		try {
			if (!empty($input['password'])) {
				$input['password'] = Hash::make($input['password']);
			} else {
				$input = array_except($input, array('password'));
			}

			$data = null;
			$avatar = [];
			$user_id = \Auth::user()->id;
			$isOwnAcc = true;
			$responseStatus = 200;

			//
			// if input has full name, it will
			// split into first name & last name
			//
			if (isset($input['full_name'])) {
				$full_name = $this->splitNameFromFullName($input['full_name']);
				$input = array_merge($input, $full_name);
			}

			if (isset($input['first_name'])) {
				$initials = Helper::generateNameInitials($input['first_name'], $input['last_name']);
				$input = array_merge($input, $initials);
			}

			//
			// if this is not own account, it will
			// require role.
			//
			if ($user_id != $id) {
				$isOwnAcc = false;
			}

			if (!$id) {
				$input['username'] = Helper::randomString(15);
				$data = $this->create($input);

				$responseStatus = 201;
			} else {
				if (isset($input['email'])) {
					unset($input['email']);
				}
				$data = $this->getListing([
					'id' => $id,
					'id_greater_than' => $user_id,
				]);

				if (!$data) {
					return \App\Helpers\Helper::resp('Not a valid data', 400);
				}

				if ($data->update($input)) {
					$data = $this->getListing([
						'id' => $id,
						'id_greater_than' => $user_id,
					]);
				}
			}

			$this->uploadAvatar($data, $id, $request);

			if (isset($input['delete_files'])) {
				$entityType = isset(File::$fileType['user_avatar']['type']) ? File::$fileType['user_avatar']['type'] : 0;
				\App\Models\File::deleteFiles([
					'id_in' => $input['delete_files'],
					'entity_type' => $entityType,
					'entity_id' => $data->id,
				], true);
			}

			//
			// if not owner changing their profile
			// then set role
			//
			if (!$isOwnAcc && isset($input['role_id']) && $input['role_id']) {
				if ($id) {
					\App\Models\UserRole::where('user_id', $id)->delete();
				}

				if (is_array($input['role_id'])) {
					$userRoles = [];
					foreach ($input['role_id'] as $roleId) {
						$userRoles[] = [
							'user_id' => $data->id,
							'role_id' => $roleId,
						];
					}

					\App\Models\UserRole::insert($userRoles);
				} else {
					\App\Models\UserRole::create([
						'user_id' => $data->id,
						'role_id' => $input['role_id'],
					]);
				}
			}

			if (array_key_exists('max_withdrawal', $input)) {
				// dd($input);
				$userWallet = UserWallet::where('user_id', $data->id)->first();
				// dd($userWallet);
				if ($userWallet) {
					$userWallet->max_withdrawal = $input['max_withdrawal'];
					$userWallet->save();
				} else {
					UserWallet::create([
						'user_id' => $data->id,
						'amount' => 0,
						'max_withdrawal' => $input['max_withdrawal'],
					]);
				}
			}

			return \App\Helpers\Helper::resp('Changes has been successfully saved.', $responseStatus, $data);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public function uploadAvatar($data = [], $id = 0, $request = null) {
		try {
			$avatar = $data->profile_pic;
			$file = \App\Models\File::upload($request, 'avatar', 'user_avatar', $data->id);

			// if file has successfully uploaded
			// and previous file exists, it will
			// delete old file.
			if ($file && $avatar) {
				\App\Models\File::deleteFile($avatar, true);
			}

			return \App\Helpers\Helper::resp('Changes has been successfully saved.', 200, $file);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public function remove($id = null) {
		try {
			$data = $this->getListing([
				'id' => $id,
				'id_greater_than' => \Auth::user()->id,
			]);

			if (!$data) {
				return \App\Helpers\Helper::resp('Not a valid data', 400);
			}

			$data->delete();

			return \App\Helpers\Helper::resp('Record has been successfully deleted.', 200, $data);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public function userInit($user, $requiredToken = true) {
		try {
			$data = [];
			if ($requiredToken) {
				$data['token'] = $user->createToken('MyApp')->accessToken;
			}

			$roles = $user->roles->pluck('slug')->toArray();
			$user->makeHidden(['verified']);

			$user->roles->makeHidden([
				'id',
				'pid',
				'user_id',
				'status',
				'level',
				'created_at',
				'updated_at',
				'pivot',
			]);

			$data['user'] = $user;
			$data['site'] = \App\Models\SiteSetting::select("key", "val", "field_label", "field_type")
				->where("is_visible", 1)
				->get();

			return $data;

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public function splitNameFromFullName($full_name = '') {
		$data = [];
		if ($full_name) {
			$splitName = explode(' ', $full_name);
			$data['first_name'] = $splitName[0];
			$data['last_name'] = substr($full_name, strlen($data['first_name']), strlen($full_name) - 1);
		}
		return $data;
	}
}
