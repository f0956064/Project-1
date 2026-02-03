<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameSetting;
use Illuminate\Http\Request;

class GameSettingController extends Controller
{
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);

        $this->_module = 'Game Settings';
        $this->_routePrefix = 'game-settings';
        $this->_model = new GameSetting();
    }

    public function index(Request $request)
    {
        $this->initIndex();
        
        $data = GameSetting::first();
        if (!$data) {
            // Should be seeded, but just in case
            $data = GameSetting::create([
                'id' => 1,
                'show_games' => 1,
                'deposit' => 1,
                'withdrawal' => 1
            ]);
        }
        
        $this->_data['data'] = $data;

        return view('admin.game_settings.index', $this->_data);
    }

    public function update(Request $request)
    {
        try {
            $data = GameSetting::first();
            if (!$data) {
                return response()->json(['status' => 'error', 'message' => 'Record not found'], 404);
            }

            $column = $request->input('column');
            $value = $request->input('value');
            
            if (in_array($column, ['show_games', 'deposit', 'withdrawal'])) {
                $data->$column = $value;
                $data->save();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Status updated successfully',
                ]);
            }
            
            return response()->json(['status' => 'error', 'message' => 'Invalid column'], 400);

        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
