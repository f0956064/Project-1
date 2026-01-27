<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\UserDeposit;
use App\Models\UserWallet;
use App\Models\UserWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function deposit(Request $request)
    {
        $wallet = UserWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['amount' => 0]
        );

        return view('front.pages.wallet.deposit', [
            'wallet' => $wallet,
        ]);
    }

    public function depositStore(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'mobile_no' => ['nullable', 'string', 'max:20'],
            'payment_mode' => ['required', 'string', 'max:50'],
        ]);

        UserDeposit::create([
            'user_id' => $request->user()->id,
            'amount' => (float) $request->input('amount'),
            'mobile_no' => $request->input('mobile_no'),
            'payment_mode' => $request->input('payment_mode'),
            'is_approved' => 0,
        ]);

        return redirect()
            ->route('front.wallet.deposit')
            ->with('success', 'Deposit request submitted. It will be credited after admin approval.');
    }

    public function depositHistory(Request $request)
    {
        $wallet = UserWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['amount' => 0]
        );

        $deposits = (new UserDeposit())->getListing([
            'user_id' => $request->user()->id,
        ]);

        return view('front.pages.wallet.deposit-history', [
            'wallet' => $wallet,
            'deposits' => $deposits,
        ]);
    }

    public function withdraw(Request $request)
    {
        $wallet = UserWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['amount' => 0]
        );

        return view('front.pages.wallet.withdraw', [
            'wallet' => $wallet,
        ]);
    }

    public function withdrawStore(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_mode' => ['required', 'string', 'max:50'],
        ]);

        try {
            DB::transaction(function () use ($request) {
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

                // Hold funds immediately; admin approval will just mark status.
                $wallet->amount = (float) $wallet->amount - $amount;
                $wallet->save();

                UserWithdrawal::create([
                    'user_id' => $request->user()->id,
                    'amount' => $amount,
                    'payment_mode' => $request->input('payment_mode'),
                    'is_approved' => 0,
                ]);
            });

            return redirect()
                ->route('front.wallet.withdraw')
                ->with('success', 'Withdrawal request submitted.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('front.wallet.withdraw')
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return redirect()
                ->route('front.wallet.withdraw')
                ->withInput()
                ->with('error', 'Unable to submit withdrawal right now.');
        }
    }

    public function withdrawHistory(Request $request)
    {
        $wallet = UserWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['amount' => 0]
        );

        $withdrawals = (new UserWithdrawal())->getListing([
            'user_id' => $request->user()->id,
        ]);

        return view('front.pages.wallet.withdraw-history', [
            'wallet' => $wallet,
            'withdrawals' => $withdrawals,
        ]);
    }
}

