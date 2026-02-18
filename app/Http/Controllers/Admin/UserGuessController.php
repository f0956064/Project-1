<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameLocation;
use App\Models\GameMode;
use App\Models\GameSlot;
use App\Models\UserGuess;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserGuessController extends Controller
{
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);

        $this->_module = 'User Guesses';
        $this->_routePrefix = 'user-guesses';
        $this->_model = new UserGuess();
    }

    public function index(Request $request)
    {
        $this->initIndex();
        $srch_params = $request->all();
        $srch_params['with'] = ['user', 'location', 'slot', 'mode'];

        $this->_data['data'] = $this->_model->getListing($srch_params, $this->_offset);
        $this->_data['orderBy'] = $this->_model->orderBy;

        $locations = GameLocation::pluck('name', 'id')->toArray();
        $slots = GameSlot::pluck('name', 'id')->toArray();
        $modes = GameMode::pluck('name', 'id')->toArray();

        $this->_data['filters'] = [
            'fields' => [
                'game_location_id' => [
                    'label' => 'Game Location',
                    'type' => 'select',
                    'options' => $locations,
                    'attributes' => [
                        'class' => 'form-control select2',
                    ],
                ],
                'game_slot_id' => [
                    'label' => 'Game Slot',
                    'type' => 'select',
                    'options' => $slots,
                    'attributes' => [
                        'class' => 'form-control select2',
                    ],
                ],
                'game_mode_id' => [
                    'label' => 'Game Mode',
                    'type' => 'select',
                    'options' => $modes,
                    'attributes' => [
                        'class' => 'form-control select2',
                    ],
                ],
                'date' => [
                    'label' => 'Date',
                    'type' => 'date',
                    'attributes' => [
                        'class' => 'form-control datepicker',
                        'autocomplete' => 'off',
                    ],
                ],
            ],
            'reset' => route($this->_routePrefix . '.index'),
        ];

        return view('admin.user_guesses.index', $this->_data)
            ->with('i', ($request->input('page', 1) - 1) * $this->_offset);
    }
    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $guess = $this->_model->getListing(['id' => $id]);

            if (!$guess) {
                 return redirect()->route($this->_routePrefix . '.index')->with('error', 'Record not found.');
            }

            // Refund logic
            $userWallet = UserWallet::where('user_id', $guess->user_id)->first();
            if ($userWallet) {
                $userWallet->amount += $guess->amount;
                $userWallet->save();
            } else {
                 // Create wallet if it doesn't exist
                 UserWallet::create([
                     'user_id' => $guess->user_id,
                     'amount' => $guess->amount,
                     'max_withdrawal' => 0
                 ]);
            }

            $guess->delete();

            DB::commit();

            return redirect()->route($this->_routePrefix . '.index')->with('success', 'User guess deleted and amount refunded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route($this->_routePrefix . '.index')->with('error', $e->getMessage());
        }
    }
}
