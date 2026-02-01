<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\GameLocation;
use App\Models\GameSlot;
use Illuminate\Http\Request;

class GameTimingController extends Controller
{
    public function index(Request $request)
    {
        $locations = GameLocation::where('is_active', 1)->orderBy('id')->get();
        $slotsByLocation = [];
        foreach ($locations as $loc) {
            $slots = GameSlot::where('game_id', $loc->id)
                ->where('is_active', 1)
                ->orderBy('start_time')
                ->get();
            $slotsByLocation[] = [
                'location' => $loc,
                'slots' => $slots,
            ];
        }

        return view('front.pages.game-timing.index', [
            'slotsByLocation' => $slotsByLocation,
        ]);
    }
}
