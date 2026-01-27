<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserWallet;
use App\Models\UserWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);

        $this->_module = 'Withdrawal';
        $this->_routePrefix = 'finance.withdrawals';
        $this->_model = new UserWithdrawal();
    }

    public function index(Request $request)
    {
        $this->initIndex();
        $srch_params = $request->all();
        $srch_params['with'] = ['user'];

        $this->_data['data'] = $this->_model->getListing($srch_params, $this->_offset);
        $this->_data['orderBy'] = $this->_model->orderBy;
        $this->_data['filters'] = [];

        return view('admin.finance.withdrawals.index', $this->_data)
            ->with('i', ($request->input('page', 1) - 1) * $this->_offset);
    }

    public function approve(Request $request, $id)
    {
        try {
            $withdrawal = $this->_model->getListing(['id' => $id]);
            if (!$withdrawal) {
                return redirect()->route($this->_routePrefix . '.index')->with('error', 'Record not found.');
            }

            $withdrawal->is_approved = 1;
            $withdrawal->save();

            return redirect()->route($this->_routePrefix . '.index')->with('success', 'Withdrawal approved.');
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return redirect()->route($this->_routePrefix . '.index')->with('error', 'Unable to approve withdrawal.');
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $withdrawal = UserWithdrawal::where('id', $id)->lockForUpdate()->first();
                if (!$withdrawal) {
                    throw new \RuntimeException('Record not found.');
                }

                if ((int) $withdrawal->is_approved === 2) {
                    return;
                }

                // Refund wallet if not approved already
                if ((int) $withdrawal->is_approved !== 1) {
                    $wallet = UserWallet::where('user_id', $withdrawal->user_id)->lockForUpdate()->first();
                    if (!$wallet) {
                        $wallet = UserWallet::create(['user_id' => $withdrawal->user_id, 'amount' => 0]);
                    }
                    $wallet->amount = (float) $wallet->amount + (float) $withdrawal->amount;
                    $wallet->save();
                }

                $withdrawal->is_approved = 2;
                $withdrawal->save();
            });

            return redirect()->route($this->_routePrefix . '.index')->with('success', 'Withdrawal rejected (wallet refunded if pending).');
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return redirect()->route($this->_routePrefix . '.index')->with('error', 'Unable to reject withdrawal.');
        }
    }
}

