<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\UserDeposit;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDepositController extends Controller
{
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);

        $this->_module = 'User Deposit';
        $this->_routePrefix = 'user-deposits';
        $this->_model = new UserDeposit();
    }

    public function index(Request $request)
    {
        $this->initIndex();
        $srch_params = $request->all();
        $srch_params['with'] = ['user'];

        $this->_data['data'] = $this->_model->getListing($srch_params, $this->_offset);
        $this->_data['orderBy'] = $this->_model->orderBy;
        
        return view('admin.user_deposits.index', $this->_data)
            ->with('i', ($request->input('page', 1) - 1) * $this->_offset);
    }

    public function approve($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $deposit = UserDeposit::where('id', $id)->lockForUpdate()->first();
                if (!$deposit) {
                    throw new \RuntimeException('Record not found.');
                }

                if ((int) $deposit->is_approved !== 0) {
                    return; // Already processed
                }

                $deposit->is_approved = 1; // Approved
                $deposit->save();

                $wallet = UserWallet::where('user_id', $deposit->user_id)->lockForUpdate()->first();
                if (!$wallet) {
                    $wallet = UserWallet::create([
                        'user_id' => $deposit->user_id, 
                        'amount' => 0,
                        'max_withdrawal' => 5 // Default value
                    ]);
                }

                $wallet->amount = (float) $wallet->amount + (float) $deposit->amount;
                $wallet->save();
            });

            return redirect()->route($this->_routePrefix . '.index')->with('success', 'Deposit approved successfully.');
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return redirect()->route($this->_routePrefix . '.index')->with('error', 'Something went wrong.');
        }
    }

    public function reject($id)
    {
        try {
            $deposit = UserDeposit::find($id);
            if (!$deposit) {
                return redirect()->route($this->_routePrefix . '.index')->with('error', 'Record not found.');
            }

            if ((int) $deposit->is_approved === 0) {
                $deposit->is_approved = 2; // Rejected
                $deposit->save();
                return redirect()->route($this->_routePrefix . '.index')->with('success', 'Deposit rejected successfully.');
            }
            
            return redirect()->route($this->_routePrefix . '.index')->with('error', 'Deposit already processed.');
            
        } catch (\Exception $e) {
            \App\Models\ErrorLog::Log($e);
            return redirect()->route($this->_routePrefix . '.index')->with('error', 'Something went wrong.');
        }
    }
}
