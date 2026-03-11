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
        $locations = GameLocation::where('is_active', 1)->orderBy('id')->get();
        
        $logos = \App\Models\File::whereIn('id', $locations->pluck('logo')->filter()->unique()->values())
            ->with('cdn')
            ->get()
            ->keyBy('id');

        return view('front.pages.results.index', [
            'locations' => $locations,
            'logos' => $logos,
        ]);
    }

    public function show(Request $request, $location_id)
    {
        $location = GameLocation::where('id', $location_id)->where('is_active', 1)->firstOrFail();
        
        $slots = GameSlot::where('game_id', $location_id)
            ->where('is_active', 1)
            ->orderBy('id', 'asc') // Based on user preference for slot ordering
            ->get();

        $results = GameSlotResult::where('game_location_id', $location_id)
            ->with(['slot', 'mode'])
            ->where('result_date', '>=', now()->subDays(30)->format('Y-m-d'))
            ->orderBy('result_date', 'desc')
            ->get();

        $groupedResults = [];
        foreach ($results as $result) {
            $date = $result->result_date;
            $slotId = $result->game_slot_id;
            $modeName = strtolower($result->mode->name);
            
            if (!isset($groupedResults[$date])) {
                $groupedResults[$date] = [];
            }
            if (!isset($groupedResults[$date][$slotId])) {
                $groupedResults[$date][$slotId] = [];
            }
            
            $groupedResults[$date][$slotId][$modeName] = $result->result_value;
        }

        return view('front.pages.results.show', [
            'location' => $location,
            'slots' => $slots,
            'groupedResults' => $groupedResults,
        ]);
    }
}
