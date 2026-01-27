<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\UserWallet;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $wallet = UserWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['amount' => 0]
        );

        return view('front.pages.menu.index', [
            'wallet' => $wallet,
        ]);
    }
}

