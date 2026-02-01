<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use App\Models\UserWallet;
use Illuminate\Http\Request;

class HelplineController extends Controller
{
    public function index(Request $request)
    {
        $wallet = UserWallet::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['amount' => 0]
        );

        $content = SiteContent::where('slug', 'helpline')->first();
        $helplineText = $content ? $content->long_description : '';
        $helplinePhone = \Config::get('settings.helpline_phone') ?? '';

        return view('front.pages.helpline.index', [
            'wallet' => $wallet,
            'helplineText' => $helplineText,
            'helplinePhone' => $helplinePhone,
        ]);
    }
}
