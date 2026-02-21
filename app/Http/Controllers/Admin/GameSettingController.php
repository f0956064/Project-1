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
        $this->_data['game_notice'] = \App\Models\GameNotice::first();

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

    public function updateNotice(Request $request)
    {
        $request->validate([
            'description' => 'required|string'
        ]);

        $notice = \App\Models\GameNotice::first();
        if ($notice) {
            $notice->update(['description' => $request->description]);
        } else {
            \App\Models\GameNotice::create(['description' => $request->description]);
        }

        return redirect()->back()->with('success', 'Game notice updated successfully.');
    }

    public function updateBanner(Request $request)
    {
        $request->validate([
            'banner_image.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $gameSetting = GameSetting::first();
        if (!$gameSetting) {
            $gameSetting = GameSetting::create([
                'id' => 1,
                'show_games' => 1,
                'deposit' => 1,
                'withdrawal' => 1
            ]);
        }

        if ($request->hasFile('banner_image')) {
            $files = \App\Models\File::upload($request, 'banner_image', 'home_banner', $gameSetting->id);
            if ($files) {
                return redirect()->back()->with('success', 'Home banner(s) uploaded successfully.');
            }
        }

        return redirect()->back()->with('error', 'Failed to upload home banner.');
    }

    public function deleteBanner(Request $request, $id)
    {
        try {
            $file = \App\Models\File::findOrFail($id);
            if ($file->entity_type == \App\Models\File::$fileType['home_banner']['type']) {
                \App\Models\File::deleteFile($file, true);
                return redirect()->back()->with('success', 'Banner deleted successfully.');
            }
            return redirect()->back()->with('error', 'Invalid file type.');
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return redirect()->back()->with('error', 'Failed to delete banner.');
        }
    }
}
