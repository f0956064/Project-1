<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

class UserGuess extends Model
{
    protected $table = 'user_guesses';

    protected $fillable = [
        'user_id',
        'date',
        'game_location_id',
        'game_slot_id',
        'game_mode_id',
        'guess',
        'amount',
    ];

    protected $hidden = [
        'updated_at',
    ];

    public $orderBy = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function location()
    {
        return $this->belongsTo(GameLocation::class, 'game_location_id');
    }

    public function slot()
    {
        return $this->belongsTo(GameSlot::class, 'game_slot_id');
    }

    public function mode()
    {
        return $this->belongsTo(GameMode::class, 'game_mode_id');
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
                ->when(isset($srch_params['date']), function ($q) use ($srch_params) {
                    return $q->where($this->table . '.date', '=', $srch_params['date']);
                })
                ->when(isset($srch_params['game_location_id']), function ($q) use ($srch_params) {
                    return $q->where($this->table . '.game_location_id', '=', $srch_params['game_location_id']);
                })
                ->when(isset($srch_params['game_slot_id']), function ($q) use ($srch_params) {
                    return $q->where($this->table . '.game_slot_id', '=', $srch_params['game_slot_id']);
                })
                ->when(isset($srch_params['game_mode_id']), function ($q) use ($srch_params) {
                    return $q->where($this->table . '.game_mode_id', '=', $srch_params['game_mode_id']);
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

