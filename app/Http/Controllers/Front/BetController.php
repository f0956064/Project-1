<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\GameLocation;
use App\Models\GameMode;
use App\Models\GameSlot;
use App\Models\UserGuess;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BetController extends Controller
{
    public function index(Request $request, $game_location_id, $game_slot_id, $game_mode_id)
    {
        [$location, $slot, $mode] = $this->resolveGame($game_location_id, $game_slot_id, $game_mode_id);

        $wallet = UserWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['amount' => 0]
        );

        $bets = (new UserGuess())->getListing([
            'user_id' => $request->user()->id,
            'game_location_id' => $location->id,
            'game_slot_id' => $slot->id,
            'game_mode_id' => $mode->id,
            'date' => date('Y-m-d'),
        ]);

        return view('front.pages.bets.index', [
            'location' => $location,
            'slot' => $slot,
            'mode' => $mode,
            'wallet' => $wallet,
            'bets' => $bets,
        ]);
    }

    public function store(Request $request, $game_location_id, $game_slot_id, $game_mode_id)
    {
        [$location, $slot, $mode] = $this->resolveGame($game_location_id, $game_slot_id, $game_mode_id);

        $request->validate([
            'guess' => ['required', 'numeric', 'min:' . $mode->min_bet, 'max:' . $mode->max_bet],
            'amount' => ['required', 'numeric', 'min:1'],
        ]);

        try {
            DB::transaction(function () use ($request, $location, $slot, $mode) {
                $wallet = UserWallet::where('user_id', $request->user()->id)
                    ->lockForUpdate()
                    ->first();

                if (!$wallet) {
                    $wallet = UserWallet::create(['user_id' => $request->user()->id, 'amount' => 0]);
                }

                $amount = (float) $request->input('amount');
                if ((float) $wallet->amount < $amount) {
                    throw new \RuntimeException('Insufficient wallet balance.');
                }

                $wallet->amount = (float) $wallet->amount - $amount;
                $wallet->save();

                UserGuess::create([
                    'user_id' => $request->user()->id,
                    'date' => date('Y-m-d'),
                    'game_location_id' => $location->id,
                    'game_slot_id' => $slot->id,
                    'game_mode_id' => $mode->id,
                    'guess' => $request->input('guess'),
                    'amount' => $amount,
                ]);
            });

            return redirect()
                ->route('front.bets.index', [$location->id, $slot->id, $mode->id])
                ->with('success', 'Bet placed successfully.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('front.bets.index', [$location->id, $slot->id, $mode->id])
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return redirect()
                ->route('front.bets.index', [$location->id, $slot->id, $mode->id])
                ->withInput()
                ->with('error', 'Unable to place bet right now.');
        }
    }

    private function resolveGame($game_location_id, $game_slot_id, $game_mode_id)
    {
        $location = (new GameLocation())->getListing(['id' => $game_location_id]);
        if (!$location || (int) $location->is_active !== 1) {
            abort(404);
        }

        $slot = (new GameSlot())->getListing(['id' => $game_slot_id, 'game_id' => $game_location_id]);
        if (!$slot || (int) $slot->is_active !== 1) {
            abort(404);
        }

        $mode = (new GameMode())->getListing(['id' => $game_mode_id, 'slot_id' => $game_slot_id]);
        if (!$mode || (int) $mode->is_active !== 1) {
            abort(404);
        }

        return [$location, $slot, $mode];
    }

    public function myBet(Request $request)
    {
        $wallet = UserWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['amount' => 0]
        );

        $bets = UserGuess::where('user_id', $request->user()->id)
            ->with(['location', 'slot', 'mode'])
            ->orderBy('id', 'desc')
            ->paginate(20);

        $winnings = \App\Models\GameWinner::where('user_id', $request->user()->id)
            ->with([
                'user',
            ])
            ->join('game_locations', 'game_winners.game_id', '=', 'game_locations.id')
            ->join('game_slots', 'game_winners.slot_id', '=', 'game_slots.id')
            ->join('game_modes', 'game_winners.game_mode_id', '=', 'game_modes.id')
            ->select(
                'game_winners.*',
                'game_locations.name as location_name',
                'game_slots.name as slot_name',
                'game_modes.name as mode_name'
            )
            ->orderBy('game_winners.id', 'desc')
            ->paginate(20, ['*'], 'winning_page');

        return view('front.pages.my-bet.index', [
            'wallet'   => $wallet,
            'bets'     => $bets,
            'winnings' => $winnings,
        ]);
    }
}

