<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Helper {

	/**
	 * Show date with date format.
	 * @param date / datetime $date
	 * @param boolean $showTime [show time also]
	 * @param string $dateFormat custom date format [any custom date format, which is not default]
	 * @param string $timezone [User timezone]
	 * @version:    1.0.0.5
	 * @author:     Somnath Mukherjee
	 */
	public static function showdate($date, $showTime = true, $dateFormat = '', $timezone = '') {
		if (self::check_valid_date($date)) {
			$customFormat = true;
			if (!$dateFormat) {
				$customFormat = false;
				$dateFormat = \Config::get('settings.date_format');
			}
			$gdt = explode(" ", $date);
			if ($showTime && isset($gdt[1]) && self::checktime($gdt[1])) {
				if (!$customFormat) {
					$dateFormat .= " " . \Config::get('settings.time_format');
				}
				$date = (string) date($dateFormat, strtotime($date));

				return $date;
			} else {
				$date = (string) date($dateFormat, strtotime($date));
				return $date;
			}
		} else {
			return false;
		}
	}

	/**
	 * Search text with html tag
	 * @param:      html string [string]
	 * @param:      tag name [string]
	 * @return:     tag value if found else boolean
	 * @version:    1.0.0.1
	 * @author:     Somnath Mukherjee
	 */

	public static function text_within_tag($string, $tagname) {
		$pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
		preg_match($pattern, $string, $matches);
		return isset($matches[1]) ? $matches[1] : false;
	}

	/**
	 * Check valid time
	 * @param:      time as string
	 * @author:     Somnath Mukherjee
	 * @version:    1.0.0.1
	 */
	public static function checktime($time = '00:00:00') {
		list($hour, $min, $sec) = explode(':', $time);

		if ($hour == 0 && $min == 0 && $sec == 0) {
			return false;
		}

		if ($hour < 0 || $hour > 23 || !is_numeric($hour)) {
			return false;
		}
		if ($min < 0 || $min > 59 || !is_numeric($min)) {
			return false;
		}
		if ($sec < 0 || $sec > 59 || !is_numeric($sec)) {
			return false;
		}
		return true;
	}

	/**
	 * Check valid date
	 * @param:      date as string
	 * @author:     Somnath Mukherjee
	 * @version:    1.0.0.2
	 */
	public static function check_valid_date($date = '') {
		if ($date != "") {
			$date = date("Y-m-d", strtotime($date));
			$dt = explode("-", $date);
			return checkdate((int) $dt[1], (int) $dt[2], (int) $dt[0]);
		}

		return false;
	}

	/**
	 * Get clean url.
	 * @params:         url string
	 * @version:        1.0.0.1
	 * @author:         Somnath Mukherjee
	 */

	public static function getcleanurl($name) {
		/*$name = trim($name);
			        $url  = preg_replace('/[^A-Za-z0-9\-]/', '-', $name);
			        $url  = strtolower(str_replace("-----", "-", $url));
			        $url  = strtolower(str_replace("----", "-", $url));
			        $url  = strtolower(str_replace("---", "-", $url));
			        $url  = strtolower(str_replace("--", "-", $url));

		*/

		return str_slug($name);
	}

	public static function getUniqueSlug($title = '', $table = '', $column = 'slug', $stringFormat = true) {
		$title = self::getcleanurl($title);
		$condition[$column] = $title;

		$nor = \DB::table($table)
			->where($condition)
			->count();

		if (!$nor) {
			return $title;
		}

		if ($stringFormat) {
			$title .= '-' . chr(rand(65, 90));
		} else {
			$title .= rand(0, 9);
		}

		return self::getUniqueSlug($title, $table, $column);
	}

	/**
	 * Check for directory name
	 * @param:      directory name (mandatory).
	 * @author:     Somnath Mukherjee.
	 * @version:    1.0.0.1
	 *
	 * NOTE:        It checks only into user panels assets folder
	 *              if specified directory not exist then it will create it
	 */
	public static function check_directory($dir_name = '') {
		if ($dir_name == "") {
			return false;
		}

		$filePath = public_path($dir_name);
		if (!file_exists($filePath)) {
			$oldmask = umask(0);
			mkdir($filePath, 0755);

			// copying index file
			self::cp_index($filePath);
			umask($oldmask);
		}
		return true;
	}

	/**
	 * Copy index.html file to the destination folder.
	 *
	 * @param [string] $dest Destination folder
	 */
	public static function cp_index($dest = '', $source = '') {
		$dest = rtrim($dest, "/") . '/index.html';
		$source = !$source ? 'index.html' : $source;
		@copy(public_path($source), $dest);
	}

	/**
	 * if this is not a valid data then it will
	 * redirct to the listing page
	 *
	 * @param  array  $data
	 * @param  string $redirectRoute redirect route
	 * @param  mixed $param extra param
	 * @return boolean
	 */
	public static function notValidData($data = [], $redirectRoute = '', $param = []) {
		if (!$data) {
			if ($redirectRoute) {
				return redirect()
					->route($redirectRoute, $param)
					->with('error', 'Not a valid data.');
			}

			return self::rj('No record found', 204);
		}

		return false;
	}

	public static function price($price = 0) {
		return '$' . number_format($price, 0);
	}

	/**
	 * Make associative array from 2d array.
	 *
	 * @param:      2d array (mandatory)
	 *
	 * @param:      fields name (mandatory).  First field for associative array's Key and
	 *              Second field for associative array's value.
	 *
	 *              NOTE: if you want to put only one field, then it will create
	 *              key and value with the same name or value.
	 *
	 * @param:      If you want to create a simple array then pass TRUE. It will not
	 *              create any associative array.
	 *
	 * @author:     Somnath Mukherjee.
	 *
	 * @version:    1.0.0.2
	 */

	public static function makeSimpleArray($marray = array(), $fields = '', $make_single = false) {
		if ($fields == "") {
			return false;
		}

		$fields = explode(",", $fields);
		$key = null;
		$val = null;
		if (count($fields) > 1) {
			$key = trim($fields[0]);
			$val = trim($fields[1]);
		} else {
			$key = $val = trim($fields[0]);
		}

		$sarray = array();
		if (is_array($marray) and !empty($marray)) {
			if (!$make_single) {
				foreach ($marray as $k => $v) {
					$v = (array) $v;
					$sarray[$v[$key]] = trim($v[$val]);
				}
			} else {
				foreach ($marray as $k) {
					$k = (array) $k;
					$sarray[] = $k[$key];
				}
			}
		}

		return $sarray;
	}

	public static function getMethod() {
		$method = explode("@", \Route::currentRouteAction());
		return $method = end($method);
	}

	public static function getController() {
		$controller = explode("@", \Route::currentRouteAction());
		$controller = explode('\\', $controller[0]);
		return $controller = end($controller);
	}

	public static function randomNumber() {
		return mt_rand(100000, 999999);
	}
	public static function randomString($size = 15) {
		return Str::random($size);

		/*
			        $alpha_key = '';
			        $keys      = range('A', 'Z');

			        for ($i = 0; $i < 2; $i++) {
			            $alpha_key .= $keys[array_rand($keys)];
			        }

			        $length = $size - 2;

			        $key  = '';
			        $keys = range(0, 9);

			        for ($i = 0; $i < $length; $i++) {
			            $key .= $keys[array_rand($keys)];
			        }

		*/
	}

	public static function checkFolder($location = '') {
		if (!\File::exists($location)) {
			return \File::makeDirectory($location);
		}
		return true;
	}

	public static function getAttr($attributes = [], $return = FALSE) {
		$param = '';
		if (!empty($attributes)) {
			foreach ($attributes as $k => $v) {
				if (!$return) {
					echo ' ' . $k . '="' . $v . '"';
				} else {
					$param .= ' ' . $k . '="' . $v . '"';
				}
			}
		}

		return $param;
	}

	/**
	 * Response Json
	 * 200 OK
	 * 201 Created
	 * 202 Accepted
	 * 203 Non-Authoritative Information
	 * 204 No Content
	 * 205 Reset Content
	 * 206 Partial Content
	 * 207 Multi-Status
	 * 208 Already Reported
	 * 226 IM Used
	 *
	 * 400 Bad Request
	 * 401 Unauthorized
	 * 402 Payment Required
	 * 403 Forbidden
	 * 404 Not Found
	 * 405 Method Not Allowed
	 * 406 Not Acceptable
	 * 407 Proxy Authentication Required
	 * 408 Request Timeout
	 *
	 * 500 Internal Server Error
	 * 501 Not Implemented
	 * 502 Bad Gateway
	 */
	public static function rj($message = '', $headerStatus = 200, $data = []) {
		// if(empty($data)){
		//     $data = [
		//         'message'   => 'No record found!',
		//         'data'      => []
		//     ];
		//     $headerStatus   = 204;
		// }

		// $data['message']    = !isset($data['message']) ? 'No record found' : $data['message'];
		// $data['data']       = !isset($data['data']) ? (object)[] : (array)$data['data'];

		$data = self::resp($message, $headerStatus, $data);

		return response()->json($data, $headerStatus);
	}

	public static function resp($message = '', $status = 200, $data = []) {
		return [
			'status' => $status,
			'message' => $message,
			'data' => $data,
		];
	}

	/**
	 * Format sort by clause from url.
	 *
	 *
	 */
	public static function manageOrderBy($orderBy = '') {
		if ($orderBy) {
			$orderBy = explode(",", $orderBy);
			$orderByArr = [];
			foreach ($orderBy as $key => $value) {
				if ($value) {
					$value = str_replace("__", ".", $value);
					$orderValue = substr($value, 0, 1);
					if ($orderValue == '-') {
						$orderByArr[substr($value, 1)] = 'DESC';
					} else {
						$orderByArr[$value] = 'ASC';
					}
				}
			}

			return $orderByArr;
		}

		return False;
	}

	public static function manageGroupBy($groupBy = '') {
		if ($groupBy) {
			$groupBy = explode(",", $groupBy);
			$groupByArr = [];
			foreach ($groupBy as $key => $value) {
				if ($value) {
					$groupByArr[] = str_replace("__", ".", $value);
				}
			}

			return $groupByArr;
		}

		return False;
	}

	public static function getUserLatLong($data = array()) {
		$lat = '';
		$lng = '';

		if (isset($data['zip']) and $data['zip']) {
			$location = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($data['zip']) . "&sensor=false&key=" . env('GOOGLE_API_KEY'));
			$location = json_decode($location);
			$location = (array) $location;

			if (!empty($location['results'])) {
				$location = (array) $location['results'][0]->geometry;

				$lat = $location["location"]->lat;
				$lng = $location["location"]->lng;
			}
		} elseif ((!isset($data['zip']) or (isset($data['zip']) and !$data['zip'])) and ((isset($data['lat']) and isset($data['lng'])) and ($data['lat'] != "" and $data['lng'] != ""))) {
			$lat = $data['lat'];
			$lng = $data['lng'];
		}

		if ($lat == '' and $lng == '') {
			$location = file_get_contents('http://ip-api.com/json/' . $_SERVER['REMOTE_ADDR']);

			$location = json_decode($location);
			$lat = $location->lat;
			$lng = $location->lon;
		}

		return ['lat' => $lat, 'lng' => $lng];
	}

	public static function callAPI($method, $url, $data, $header = ['Content-Type: application/json']) {
		$curl = curl_init();
		switch ($method) {
		case "POST":
			curl_setopt($curl, CURLOPT_POST, 1);
			if ($data) {
				if (in_array("Content-Type: application/x-www-form-urlencoded", $header)) {
					$data = http_build_query($data);
				} else {
					$data = json_encode($data);
				}
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			}
			break;
		case "PUT":
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			if ($data) {
				$data = json_encode($data);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			}
			break;
		default:
			if ($data) {
				$url = sprintf("%s?%s", $url, http_build_query($data));
			}

		}
		// OPTIONS:
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		// EXECUTE:
		$result = curl_exec($curl);
		if (!$result) {die("Connection Failure");}
		curl_close($curl);

		$result = json_decode($result, true);
		return $result;
	}

	public static function sort($route = '', $fieldName = '', $orderBy = [], $routeParam = []) {
		$srchParams = \Request::all();
		$sortBy = explode(",", \Request::input('orderBy'));
		$sortIcon = \Config::get('settings.icon_sort_none');
		$matchFound = false;

		if (!empty($orderBy)) {
			foreach ($orderBy as $key => $value) {
				$key = str_replace(".", "__", $key);
				if ($key == $fieldName) {
					$matchFound = true;
					if ($value == 'ASC') {
						$sortBy = array_diff($sortBy, [$key]);
						$sortBy[] = '-' . $key;
						$sortIcon = \Config::get('settings.icon_sort_desc');
					} else {
						$sortBy = array_diff($sortBy, ['-' . $key]);
						// $sortBy[]   = $key;
						$sortIcon = \Config::get('settings.icon_sort_asc');
					}
				}
			}
		}

		if (!$matchFound) {
			$sortBy[] = $fieldName;
		}
		$srchParams['orderBy'] = implode(",", $sortBy);

		if ($routeParam) {
			$srchParams = array_merge($srchParams, $routeParam);
		}

		return '<a href="' . route($route, $srchParams) . '" class="icon-sorting">' . $sortIcon . '</a>';
	}

	public static function getUrlSegment($request = null, $segment = 0) {
		if ($request->is('api/*')) {
			$segment++;
		}

		return $segment;
	}

	public static function getSql($data = [], $print = true) {
		if (isset($data[0]) && isset($data[1])) {
			$sql = \Str::replaceArray('?', $data[1], $data[0]);
			if ($print) {
				dd($sql);
			}

			return $sql;
		}

		return false;
	}

	public static function exportCsv($param = [], $fileName = 'report') {
		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=" . $fileName . date("Y-m-d") . ".csv",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0",
		);

		$columns = $param['columns'] ?? [];

		$callback = function () use ($param, $columns) {
			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);

			foreach ($param['data'] as $value) {
				fputcsv($file, $value);
			}
			fclose($file);
		};

		return \Illuminate\Support\Facades\Response::stream($callback, 200, $headers);
	}

	public static function importCsv($request = null, $fileName = 'file') {
		$file = $request->file($fileName);

		// File Details
		$filename = $file->getClientOriginalName();
		$extension = $file->getClientOriginalExtension();
		$tempPath = $file->getRealPath();
		$fileSize = $file->getSize();
		$mimeType = $file->getMimeType();

		// Valid File Extensions
		$valid_extension = array("csv");

		// 2MB in Bytes
		$maxFileSize = 2097152;

		// Check file extension
		if (in_array(strtolower($extension), $valid_extension)) {

			// Check file size
			if ($fileSize <= $maxFileSize) {

				// File upload location
				$location = 'assets\uploads';

				if (\Storage::disk('public')->putFileAs($location, $file, $filename)) {
					// Reading file
					$file = fopen('../storage/app/public/assets/uploads/' . $filename, 'r');
					$importData = array();
					$columns = [];
					$i = 0;

					while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
						$num = count($filedata);
						// if ($i == 0) {
						// 	$i++;
						// 	continue;
						// }
						for ($c = 0; $c < $num; $c++) {
							if ($i == 0) {
								$columns[$c] = $filedata[$c];
							} else {
								$importData[$i][$columns[$c]] = $filedata[$c];

							}
						}
						$i++;
					}
					fclose($file);
					@unlink($filepath);

					return self::resp('File content', 200, $importData);

				}
				return self::resp('Internal error occured.', 400);

			} else {
				return self::resp('File too large. File must be less than 2MB.', 400);
			}

		}
		return self::resp('Invalid file extension.', 400);
	}

	public static function getLogoColorCode($code = null) {
		$nameInitialColorTypes = [
			1 => [
				'id' => 1,
				'background' => 'soft-success',
				'text' => 'success',
			],
			2 => [
				'id' => 2,
				'background' => 'soft-warning',
				'text' => 'warning',
			],
			3 => [
				'id' => 3,
				'background' => 'soft-primary',
				'text' => 'primary',
			],
			4 => [
				'id' => 4,
				'background' => 'soft-info',
				'text' => 'info',
			],
			5 => [
				'id' => 5,
				'background' => 'soft-danger',
				'text' => 'danger',
			],
			6 => [
				'id' => 6,
				'background' => 'soft-secondary',
				'text' => 'muted',
			],
			7 => [
				'id' => 7,
				'background' => 'dark',
				'text' => 'white',
			],
			8 => [
				'id' => 8,
				'background' => 'success',
				'text' => 'white',
			],
			9 => [
				'id' => 9,
				'background' => 'warning',
				'text' => 'white',
			],
			10 => [
				'id' => 10,
				'background' => 'primary',
				'text' => 'white',
			],
			11 => [
				'id' => 11,
				'background' => 'info',
				'text' => 'white',
			],
			12 => [
				'id' => 12,
				'background' => 'danger',
				'text' => 'white',
			],
		];

		return $code ? $nameInitialColorTypes[$code] : $nameInitialColorTypes;
	}

	public static function generateNameInitials($title = '', $lastTitle = '') {
		$title = $title . ' ' . $lastTitle;
		$nameParts = explode(' ', trim($title));
		$firstName = array_shift($nameParts);
		$lastName = array_pop($nameParts);
		$initials = (
			mb_substr($firstName, 0, 1) .
			mb_substr($lastName, 0, 1)
		);

		$nameInitialColorTypes = self::getLogoColorCode();
		$data = [
			'name_initials' => $initials,
			'name_initial_color_type' => rand(1, count($nameInitialColorTypes)),
		];
		return $data;
	}

	public static function genLogo($logo = [], $data = null, $size = 'xs', $shape = 'rounded-circle', $class = '', $style = '') {
		if ($logo['id'] || !$data->name_initials) {
			return '<img src="' . $logo['thumb'] . '" class="' . $shape . ' avatar-' . $size . ' ' . $class . '" ' . $style . 'style="max-height: 70px;">';
		} else {
			$initialType = self::getLogoColorCode($data->name_initial_color_type);
			return '<div class="avatar-' . $size . ' ' . $class . '">
                <span class="avatar-title ' . $shape . ' bg-' . $initialType['background'] . ' text-' . $initialType['text'] . ' font-size-16">' . $data->name_initials . '</span>
            </div>';
		}
	}
}
