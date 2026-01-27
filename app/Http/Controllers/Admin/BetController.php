<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserGuess;
use Illuminate\Http\Request;

class BetController extends Controller
{
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);

        $this->_module = 'Bets';
        $this->_routePrefix = 'finance.bets';
        $this->_model = new UserGuess();
    }

    public function index(Request $request)
    {
        $this->initIndex();
        $srch_params = $request->all();
        $srch_params['with'] = ['user', 'location', 'slot', 'mode'];

        $this->_data['data'] = $this->_model->getListing($srch_params, $this->_offset);
        $this->_data['orderBy'] = $this->_model->orderBy;
        $this->_data['filters'] = [];

        return view('admin.finance.bets.index', $this->_data)
            ->with('i', ($request->input('page', 1) - 1) * $this->_offset);
    }
}

