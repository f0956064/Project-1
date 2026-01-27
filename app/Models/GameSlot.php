<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSlot extends Model
{
    protected $table = 'game_slots';

    protected $fillable = [
        'game_id',
        'name',
        'logo',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $hidden = [
        'updated_at',
    ];

    public $orderBy = [];

    public function getFilters($routeName = 'game.slots.index', $game_location_id = null) {
        if ($game_location_id !== null) {
            $resetRoute = route($routeName, ['game_location_id' => $game_location_id]);
        } else {
            $resetRoute = route($routeName);
        }
        
        return [
            'reset' => $resetRoute,
            'fields' => [
                'name' => [
                    'type' => 'text',
                    'label' => 'Name',
                ],
                'is_active' => [
                    'type' => 'select',
                    'label' => 'Status',
                    'attributes' => [
                        'id' => 'select-status',
                    ],
                    'options' => [
                        0 => 'Inactive',
                        1 => 'Active',
                    ],
                ],
            ],
        ];
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
                ->when(isset($srch_params['game_id']), function ($q) use ($srch_params) {
                    return $q->where($this->table . '.game_id', '=', $srch_params['game_id']);
                })
                ->when(isset($srch_params['name']), function ($q) use ($srch_params) {
                    return $q->where($this->table . ".name", "LIKE", "%{$srch_params['name']}%");
                })
                ->when(isset($srch_params['is_active']), function ($q) use ($srch_params) {
                    return $q->where($this->table . '.is_active', '=', $srch_params['is_active']);
                });

            if (isset($srch_params['id'])) {
                return $listing->where($this->table . '.id', '=', $srch_params['id'])
                    ->first();
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
            $data = null;
            if ($id) {
                $data = $this->getListing(['id' => $id]);

                if (!$data) {
                    return \App\Helpers\Helper::resp('Not a valid data', 400);
                }

                $data->update($input);
            } else {
                $data = $this->create($input);
            }

            // Handle file upload
            if ($request && $request->hasFile('logo')) {
                $file = \App\Models\File::upload($request, 'logo', 'game_slot_logo', $data->id);
                if ($file && is_object($file) && isset($file->id)) {
                    $data->logo = $file->id;
                    $data->save();
                }
            }

            return \App\Helpers\Helper::resp('Changes has been successfully saved.', 200, $data);

        } catch (Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return Helper::resp($e->getMessage(), 500);
        }
    }

    public function remove($id = null) {
        try {
            $data = $this->getListing([
                'id' => $id,
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

    public function location()
    {
        return $this->belongsTo(GameLocation::class, 'game_id');
    }

    public function modes()
    {
        return $this->hasMany(GameMode::class, 'slot_id');
    }
}
