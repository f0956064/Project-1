<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\GameLocation;
use App\Models\GameSlotResult;
use App\Models\GameSlot;
use App\Models\UserWallet;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    public function index(Request $request)
    {
        $wallet = UserWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['amount' => 0]
        );

        $locations = GameLocation::where('is_active', 1)->orderBy('id')->get();
        $resultsByLocation = [];
        foreach ($locations as $loc) {
            $slots = GameSlot::where('game_id', $loc->id)->where('is_active', 1)->orderBy('start_time')->get();
            $slotResults = [];
            foreach ($slots as $slot) {
                $results = GameSlotResult::where('game_slot_id', $slot->id)
                    ->orderBy('result_date', 'desc')
                    ->limit(10)
                    ->get();
                $slotResults[] = [
                    'slot' => $slot,
                    'results' => $results,
                ];
            }
            $resultsByLocation[] = [
                'location' => $loc,
                'slotResults' => $slotResults,
            ];
        }

        return view('front.pages.results.index', [
            'wallet' => $wallet,
            'resultsByLocation' => $resultsByLocation,
        ]);
    }
}
