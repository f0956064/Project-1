<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use App\Models\UserWallet;
use Illuminate\Http\Request;

class ReferController extends Controller
{
    public function index(Request $request)
    {
        $wallet = UserWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['amount' => 0]
        );

        $user = $request->user();
        $referralCode = $user->referral_code ?? '';

        $content = SiteContent::where('slug', 'refer-and-earn')->first();
        $referText = $content ? $content->long_description : 'Refer your friends and earn cashback!';

        return view('front.pages.refer.index', [
            'wallet' => $wallet,
            'referralCode' => $referralCode,
            'referText' => $referText,
        ]);
    }
}
