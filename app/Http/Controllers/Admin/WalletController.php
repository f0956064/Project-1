<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserWallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);

        $this->_module = 'Wallet';
        $this->_routePrefix = 'finance.wallets';
        $this->_model = new UserWallet();
    }

    public function index(Request $request)
    {
        $this->initIndex();
        $srch_params = $request->all();
        $srch_params['with'] = ['user'];

        $this->_data['data'] = $this->_model->getListing($srch_params, $this->_offset);
        $this->_data['orderBy'] = $this->_model->orderBy;
        $this->_data['filters'] = [];

        return view('admin.finance.wallets.index', $this->_data)
            ->with('i', ($request->input('page', 1) - 1) * $this->_offset);
    }

    public function edit(Request $request, $id)
    {
        $this->initUIGeneration($id);
        $wallet = $this->_model->getListing(['id' => $id, 'with' => ['user']]);
        if (!$wallet) {
            return redirect()->route($this->_routePrefix . '.index')->with('error', 'Record not found.');
        }

        return view('admin.finance.wallets.edit', [
            'wallet' => $wallet,
            'module' => $this->_data['module'] ?? 'Wallet',
            'breadcrumb' => $this->_data['breadcrumb'] ?? [],
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $wallet = $this->_model->getListing(['id' => $id]);
        if (!$wallet) {
            return redirect()->route($this->_routePrefix . '.index')->with('error', 'Record not found.');
        }

        $wallet->amount = (float) $request->input('amount');
        $wallet->save();

        return redirect()->route($this->_routePrefix . '.index')->with('success', 'Wallet updated.');
    }
}

