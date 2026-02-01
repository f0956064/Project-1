<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    protected $table = 'user_wallets';

    protected $fillable = [
        'user_id',
        'wallet_balance',
        'max_withdrawal',
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
            if ($id) {
                $data = $this->getListing(['id' => $id]);
                if (!$data) {
                    return Helper::resp('Not a valid data', 400);
                }
                $data->update($input);
            } else {
                $data = $this->create($input);
            }

            return Helper::resp('Changes has been successfully saved.', 200, $data);
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return Helper::resp($e->getMessage(), 500);
        }
    }
}

