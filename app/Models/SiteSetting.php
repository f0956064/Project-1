<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SiteSetting extends Model {
	protected $table = 'site_settings';
	public $timestamps = false;

	protected $fillable = [
		'key',
		'val',
		'field_label',
		'field_type',
		'field_options',
		'group_name',
		'is_required',
		'is_encrypted',
		'is_visible',
	];

	protected $appends = [
		'field_type_details',
	];

	protected $hidden = [
		'is_encrypted',
		'is_visible',
	];

	public static $fieldTypes = [
		'1' => 'text',
		'2' => 'textarea',
		'3' => 'email',
		'4' => 'number',
		'5' => 'select',
		'6' => 'radio',
		'7' => 'checkbox',
		'8' => 'password',
		'9' => 'file',
		'10' => 'switch',
	];

	public $orderBy = [];

	public function getFieldTypeDetailsAttribute() {
		return self::$fieldTypes[$this->field_type];
	}

	public function getFieldOptionsAttribute($value) {
		if ($value) {
			return json_decode($value);
		}
		return null;
	}

	public static function makeCacheSetting($value = '') {
		try {
			$filePath = base_path('bootstrap/cache/settings.php');
			$settings = [];
			foreach (self::get() as $key => $value) {
				$settings[$value->key] = $value->is_encrypted ? Crypt::decryptString($value->val) : $value->val;
			}
			file_put_contents($filePath, json_encode($settings));
			\Artisan::call('config:cache');

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
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
				->when(isset($srch_params['status']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".status", $srch_params['status']);
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

				$return = \App\Helpers\Helper::notValidData($data, $this->_routePrefix . '.edit', ['manipulation']);
				if ($return) {
					return $return;
				}

				$data->update($input);
			} else {
				$siteContent = $this->create($input);
			}

			return \App\Helpers\Helper::resp('Changes has been successfully saved.', 200, $data);

		} catch (Exception $e) {
			\App\Models\ErrorLog::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

}
