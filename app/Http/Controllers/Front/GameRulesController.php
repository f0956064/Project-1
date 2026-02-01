<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\GameLocation;
use App\Models\GameMode;
use App\Models\GameSlot;
use Illuminate\Http\Request;

class GameRulesController extends Controller
{
    public function index(Request $request)
    {
        $minDeposit = \Config::get('settings.min_deposit') ?? 200;
        $minWithdraw = \Config::get('settings.min_withdraw') ?? 500;
        $maxWithdrawalPerDay = \Config::get('settings.max_withdrawal_per_day') ?? 1;

        $locations = GameLocation::where('is_active', 1)->orderBy('id')->get();
        $rulesByLocation = [];
        foreach ($locations as $loc) {
            $slots = GameSlot::where('game_id', $loc->id)->where('is_active', 1)->get();
            $modesBySlot = [];
            foreach ($slots as $slot) {
                $modes = GameMode::where('slot_id', $slot->id)->where('is_active', 1)->get();
                foreach ($modes as $mode) {
                    $modesBySlot[] = [
                        'type' => $mode->type ?? $mode->name,
                        'play' => $mode->play_amount ?? 10,
                        'win' => $mode->win_amount ?? 0,
                        'min_bet' => $mode->min_bet ?? 5,
                        'max_bet' => $mode->max_bet ?? 100,
                    ];
                }
            }
            if (!empty($modesBySlot)) {
                $rulesByLocation[] = [
                    'location' => $loc,
                    'modes' => $modesBySlot,
                ];
            }
        }

        return view('front.pages.game-rules.index', [
            'minDeposit' => $minDeposit,
            'minWithdraw' => $minWithdraw,
            'maxWithdrawalPerDay' => $maxWithdrawalPerDay,
            'rulesByLocation' => $rulesByLocation,
        ]);
    }
}
