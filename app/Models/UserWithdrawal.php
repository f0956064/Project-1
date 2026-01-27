<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class UserWithdrawal extends Model
{
    protected $table = 'user_withdrawals';

    protected $fillable = [
        'user_id',
        'amount',
        'payment_mode',
        'is_approved',
    ];

    protected $hidden = [
        'updated_at',
    ];

    public $orderBy = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getListing($srch_params = [], $offset = 0)
    {
        try {
            $select = [
                $this->table . '.*',
            ];

            if (isset($srch_params['select'])) {
                $select = $srch_params['select'];
            }

            $listing = self::select($select)
                ->when(isset($srch_params['with']), function ($q) use ($srch_params) {
                    return $q->with($srch_params['with']);
                })
                ->when(isset($srch_params['user_id']), function ($q) use ($srch_params) {
                    return $q->where($this->table . '.user_id', '=', $srch_params['user_id']);
                })
                ->when(isset($srch_params['is_approved']), function ($q) use ($srch_params) {
                    return $q->where($this->table . '.is_approved', '=', $srch_params['is_approved']);
                });

            if (isset($srch_params['id'])) {
                return $listing->where($this->table . '.id', '=', $srch_params['id'])->first();
            }

            if (isset($srch_params['first']) && $srch_params['first']) {
                return $listing->first();
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
                return $listing->paginate($offset);
            }

            return $listing->get();
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return Helper::resp($e->getMessage(), 500);
        }
    }

    public function store($input = [], $id = 0)
    {
        try {
            $data = null;
            $responseStatus = 200;

            if ($id) {
                $data = $this->getListing(['id' => $id]);
                if (!$data) {
                    return Helper::resp('Not a valid data', 400);
                }
                $data->update($input);
            } else {
                $data = $this->create($input);
                $responseStatus = 201;
            }

            return Helper::resp('Changes has been successfully saved.', $responseStatus, $data);
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return Helper::resp($e->getMessage(), 500);
        }
    }

    public function remove($id = null)
    {
        try {
            $data = $this->getListing(['id' => $id]);
            if (!$data) {
                return Helper::resp('Not a valid data', 400);
            }

            $data->delete();

            return Helper::resp('Record has been successfully deleted.', 200, $data);
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return Helper::resp($e->getMessage(), 500);
        }
    }
}

