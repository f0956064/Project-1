<?php

namespace App\Http\Controllers;

use App\Models\GameSlotResult;
use App\Models\GameWinner;
use App\Models\UserGuess;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameWinnerProcessorController extends Controller
{
    /**
     * Process winners for the current date.
     * Publicly accessible — no auth required.
     * Call this endpoint once results have been published.
     *
     * GET /process-winners
     */
    public function process(Request $request)
    {
        $today = now()->toDateString(); // YYYY-MM-DD

        $processed = 0;
        $skipped   = 0;
        $errors    = [];

        try {
            // 1. Fetch all game results for today
            $results = GameSlotResult::with('mode')
                ->whereDate('result_date', $today)
                ->get();

            if ($results->isEmpty()) {
                return response()->json([
                    'status'  => 'ok',
                    'message' => "No results found for {$today}.",
                ]);
            }

            foreach ($results as $result) {
                $mode = $result->mode;
                // dd($mode);
                if (!$mode) {
                    $errors[] = "Mode not found for result ID {$result->id}.";
                    continue;
                }

                // 2. Find all correct guesses for this result
                $winningGuesses = UserGuess::where('date', $today)
                    ->where('game_location_id', $result->game_location_id)
                    ->where('game_slot_id', $result->game_slot_id)
                    ->where('game_mode_id', $result->game_mode_id)
                    ->where('guess', $result->result_value)
                    ->get();

                foreach ($winningGuesses as $guess) {
                    // Skip if already recorded to avoid double-credit
                    $alreadyCreated = GameWinner::where('user_id', $guess->user_id)
                        ->where('game_id', $result->game_location_id)
                        ->where('slot_id', $result->game_slot_id)
                        ->where('game_mode_id', $result->game_mode_id)
                        ->where('date', $today)
                        ->where('guess_number', $result->result_value)
                        ->exists();

                    if ($alreadyCreated) {
                        $skipped++;
                        continue;
                    }
                    // dd($guess->amount, $mode->win_amount);
                    // 3. Calculate winning amount: bet_amount * win_amount
                    $winningAmount = $guess->amount * $mode->win_amount;
                    // dd($winningAmount);

                    DB::transaction(function () use ($guess, $result, $winningAmount, $today) {
                        // 4. Add amount to user wallet
                        $wallet = UserWallet::where('user_id', $guess->user_id)->first();
                        if ($wallet) {
                            $wallet->increment('amount', $winningAmount);
                        } else {
                            UserWallet::create([
                                'user_id' => $guess->user_id,
                                'amount'  => $winningAmount,
                            ]);
                        }

                        // 5. Insert into game_winners
                        GameWinner::create([
                            'user_id'        => $guess->user_id,
                            'game_id'        => $result->game_location_id,
                            'slot_id'        => $result->game_slot_id,
                            'game_mode_id'   => $result->game_mode_id,
                            'bet_amount'     => $guess->amount,
                            'winning_amount' => $winningAmount,
                            'guess_number'   => $result->result_value,
                            'date'           => $today,
                        ]);
                    });

                    $processed++;
                }
            }

            return response()->json([
                'status'    => 'success',
                'date'      => $today,
                'processed' => $processed,
                'skipped'   => $skipped,
                'errors'    => $errors,
                'message'   => "{$processed} winner(s) credited for {$today}.",
            ]);

        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
