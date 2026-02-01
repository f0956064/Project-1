<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserDeposit;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);

        $this->_module = 'Deposit';
        $this->_routePrefix = 'finance.deposits';
        $this->_model = new UserDeposit();
    }

    public function index(Request $request)
    {
        $this->initIndex();
        $srch_params = $request->all();
        $srch_params['with'] = ['user'];

        $this->_data['data'] = $this->_model->getListing($srch_params, $this->_offset);
        $this->_data['orderBy'] = $this->_model->orderBy;
        $this->_data['filters'] = []; // can be added later

        return view('admin.finance.deposits.index', $this->_data)
            ->with('i', ($request->input('page', 1) - 1) * $this->_offset);
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $deposit = UserDeposit::where('id', $id)->lockForUpdate()->first();
                if (!$deposit) {
                    throw new \RuntimeException('Record not found.');
                }

                if ((int) $deposit->is_approved === 1) {
                    return;
                }

                $deposit->is_approved = 1;
                $deposit->save();

                $wallet = UserWallet::where('user_id', $deposit->user_id)->lockForUpdate()->first();
                if (!$wallet) {
                    $wallet = UserWallet::create(['user_id' => $deposit->user_id, 'wallet_balance' => 0]);
                }

                $wallet->wallet_balance = (float) $wallet->wallet_balance + (float) $deposit->amount;
                $wallet->save();
            });

            return redirect()->route($this->_routePrefix . '.index')->with('success', 'Deposit approved and wallet credited.');
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return redirect()->route($this->_routePrefix . '.index')->with('error', 'Unable to approve deposit.');
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $deposit = $this->_model->getListing(['id' => $id]);
            if (!$deposit) {
                return redirect()->route($this->_routePrefix . '.index')->with('error', 'Record not found.');
            }

            if ((int) $deposit->is_approved !== 1) {
                $deposit->is_approved = 2;
                $deposit->save();
            }

            return redirect()->route($this->_routePrefix . '.index')->with('success', 'Deposit rejected.');
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return redirect()->route($this->_routePrefix . '.index')->with('error', 'Unable to reject deposit.');
        }
    }
}

