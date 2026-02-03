<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\GameLocation;
use App\Models\GameMode;
use App\Models\GameSlot;
use App\Models\GameSetting;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function locations(Request $request)
    {
        $locationModel = new GameLocation();
        $locations = $locationModel->getListing([
            'is_active' => 1,
        ]);

        $logos = File::whereIn('id', $locations->pluck('logo')->filter()->unique()->values())
            ->with('cdn')
            ->get()
            ->keyBy('id');

        $gameSettings = GameSetting::first();

        return view('front.pages.locations.index', [
            'locations' => $locations,
            'logos' => $logos,
            'gameSettings' => $gameSettings
        ]);
    }

    public function slots(Request $request, $game_location_id)
    {
        $locationModel = new GameLocation();
        $location = $locationModel->getListing(['id' => $game_location_id]);
        if (!$location || (int) $location->is_active !== 1) {
            abort(404);
        }

        $slotModel = new GameSlot();
        $slots = $slotModel->getListing([
            'game_id' => $game_location_id,
            'is_active' => 1,
        ]);

        $logos = File::whereIn('id', $slots->pluck('logo')->filter()->unique()->values())
            ->with('cdn')
            ->get()
            ->keyBy('id');

        return view('front.pages.slots.index', [
            'location' => $location,
            'slots' => $slots,
            'logos' => $logos,
        ]);
    }

    public function modes(Request $request, $game_location_id, $game_slot_id)
    {
        $locationModel = new GameLocation();
        $location = $locationModel->getListing(['id' => $game_location_id]);
        if (!$location || (int) $location->is_active !== 1) {
            abort(404);
        }

        $slotModel = new GameSlot();
        $slot = $slotModel->getListing([
            'id' => $game_slot_id,
            'game_id' => $game_location_id,
        ]);
        if (!$slot || (int) $slot->is_active !== 1) {
            abort(404);
        }

        $modeModel = new GameMode();
        $modes = $modeModel->getListing([
            'slot_id' => $game_slot_id,
            'is_active' => 1,
        ]);

        $logos = File::whereIn('id', $modes->pluck('logo')->filter()->unique()->values())
            ->with('cdn')
            ->get()
            ->keyBy('id');

        return view('front.pages.modes.index', [
            'location' => $location,
            'slot' => $slot,
            'modes' => $modes,
            'logos' => $logos,
        ]);
    }
}

