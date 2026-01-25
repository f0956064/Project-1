<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model {

	protected $table = 'error_logs';

	protected $fillable = [
		'user_id',
		'line_number',
		'class_name',
		'method_name',
		'error_message',
		'request_params',
	];

	public function getListing($srch_params = [], $offset = 0) {
		try {
			$select = [
				$this->table . ".*",
			];

			if (isset($srch_params['select'])) {
				$select = $srch_params['select'];
			}

			$listing = self::select($select)
				->when(isset($srch_params['status']), function ($q) use ($srch_params) {
					return $q->where($this->table . ".status", $srch_params['status']);
				});

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

			if (isset($srch_params['id'])) {
				return $listing->where($this->table . '.id', '=', $srch_params['id'])
					->first();
			}

			if (isset($srch_params['first'])) {
				return $listing->first();
			}

			if (isset($srch_params['count'])) {
				return $listing->count();
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
			self::Log($e);
			return Helper::resp($e->getMessage(), 500);
		}
	}

	public static function Log($exception = null) {
		if ($exception) {
			$requestedParam = json_encode(request()->all());
			$backtrace = $e->getTrace();
			$currentMethod = $backtrace[0]['function'] ?? null;
			$currentClass = $backtrace[0]['class'] ?? null;

			self::create([
				'user_id' => \Auth::user() ? \Auth::user()->id : 0,
				'line_number' => method_exists($exception, 'getLine') ? $exception->getLine() : 0,
				'class_name' => $currentClass,
				'method_name' => $currentMethod,
				'error_message' => method_exists($exception, 'getMessage') ? $exception->getMessage() : $exception,
				'request_params' => $requestedParam,
			]);
		}
	}
}
