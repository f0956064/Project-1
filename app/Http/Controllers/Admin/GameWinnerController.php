<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameLocation;
use App\Models\GameMode;
use App\Models\GameSlot;
use App\Models\GameWinner;
use Illuminate\Http\Request;

class GameWinnerController extends Controller
{
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);

        $this->_module      = 'Game Winners';
        $this->_routePrefix = 'game-winners';
        $this->_model       = new GameWinner();
    }

    public function index(Request $request)
    {
        $this->initIndex();
        $srch_params = $request->all();

        $query = GameWinner::query()
            ->join('users', 'game_winners.user_id', '=', 'users.id')
            ->join('game_locations', 'game_winners.game_id', '=', 'game_locations.id')
            ->join('game_slots', 'game_winners.slot_id', '=', 'game_slots.id')
            ->join('game_modes', 'game_winners.game_mode_id', '=', 'game_modes.id')
            ->select(
                'game_winners.id',
                'game_winners.user_id',
                'game_winners.game_id',
                'game_winners.slot_id',
                'game_winners.game_mode_id',
                'game_winners.bet_amount',
                'game_winners.winning_amount',
                'game_winners.guess_number',
                'game_winners.date',
                'game_winners.created_at',
                \DB::raw("CONCAT(users.first_name, ' ', users.last_name) as user_name"),
                'users.username',
                'game_locations.name as location_name',
                'game_slots.name as slot_name',
                'game_modes.name as mode_name'
            )
            ->orderBy('game_winners.id', 'DESC');

        // Filters
        if (!empty($srch_params['game_id'])) {
            $query->where('game_winners.game_id', $srch_params['game_id']);
        }
        if (!empty($srch_params['slot_id'])) {
            $query->where('game_winners.slot_id', $srch_params['slot_id']);
        }
        if (!empty($srch_params['game_mode_id'])) {
            $query->where('game_winners.game_mode_id', $srch_params['game_mode_id']);
        }
        if (!empty($srch_params['date'])) {
            $query->whereDate('game_winners.date', $srch_params['date']);
        }
        if (!empty($srch_params['user_name'])) {
            $query->where(function ($q) use ($srch_params) {
                $q->where('users.first_name', 'LIKE', '%' . $srch_params['user_name'] . '%')
                  ->orWhere('users.last_name', 'LIKE', '%' . $srch_params['user_name'] . '%')
                  ->orWhere('users.username', 'LIKE', '%' . $srch_params['user_name'] . '%');
            });
        }

        $this->_data['data'] = $query->paginate($this->_offset);

        $locations = GameLocation::pluck('name', 'id')->toArray();
        $slots     = GameSlot::pluck('name', 'id')->toArray();
        $modes     = GameMode::pluck('name', 'id')->toArray();

        $this->_data['filters'] = [
            'fields' => [
                'user_name' => [
                    'label' => 'User Name',
                    'type'  => 'text',
                    'attributes' => ['class' => 'form-control'],
                ],
                'game_id' => [
                    'label'   => 'Game Location',
                    'type'    => 'select',
                    'options' => $locations,
                    'attributes' => ['class' => 'form-control select2'],
                ],
                'slot_id' => [
                    'label'   => 'Game Slot',
                    'type'    => 'select',
                    'options' => $slots,
                    'attributes' => ['class' => 'form-control select2'],
                ],
                'game_mode_id' => [
                    'label'   => 'Game Mode',
                    'type'    => 'select',
                    'options' => $modes,
                    'attributes' => ['class' => 'form-control select2'],
                ],
                'date' => [
                    'label' => 'Date',
                    'type'  => 'date',
                    'attributes' => [
                        'class'        => 'form-control datepicker',
                        'autocomplete' => 'off',
                    ],
                ],
            ],
            'reset' => route($this->_routePrefix . '.index'),
        ];

        return view('admin.game_winners.index', $this->_data)
            ->with('i', ($request->input('page', 1) - 1) * $this->_offset);
    }
}
