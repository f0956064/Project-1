<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\UserDeposit;
use App\Models\UserGuess;
use App\Models\UserWallet;
use App\Models\UserWithdrawal;
use Illuminate\Http\Request;

class TransactionHistoryController extends Controller
{
    public function index(Request $request)
    {
        $wallet = UserWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['amount' => 0]
        );

        $userId = $request->user()->id;
        $deposits = UserDeposit::where('user_id', $userId)->orderBy('id', 'desc')->get();
        $withdrawals = UserWithdrawal::where('user_id', $userId)->orderBy('id', 'desc')->get();
        $bets = UserGuess::where('user_id', $userId)->with(['location', 'slot', 'mode'])->orderBy('id', 'desc')->get();

        $transactions = collect();
        foreach ($deposits as $d) {
            $transactions->push((object)[
                'date' => $d->created_at,
                'type' => 'deposit',
                'amount' => $d->amount,
                'status' => $d->is_approved,
                'detail' => 'Deposit',
            ]);
        }
        foreach ($withdrawals as $w) {
            $transactions->push((object)[
                'date' => $w->created_at,
                'type' => 'withdrawal',
                'amount' => -$w->amount,
                'status' => $w->is_approved,
                'detail' => 'Withdrawal',
            ]);
        }
        foreach ($bets as $b) {
            $transactions->push((object)[
                'date' => $b->created_at,
                'type' => 'bet',
                'amount' => -$b->amount,
                'status' => 1,
                'detail' => 'Bet - ' . ($b->location ? $b->location->name : '') . ' / ' . ($b->guess ?? ''),
            ]);
        }
        $transactions = $transactions->sortByDesc('date')->values();

        return view('front.pages.transaction-history.index', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }
}
