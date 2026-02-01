<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = UserProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'bank_name' => '',
                'account_number' => '',
                'ifsc_code' => '',
                'paytm_detail' => '',
                'upi_address' => '',
                'google_pay_number' => '',
            ]
        );

        return view('front.pages.profile.edit', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'ifsc_code' => ['nullable', 'string', 'max:50'],
            'paytm_detail' => ['nullable', 'string', 'max:255'],
            'upi_address' => ['nullable', 'string', 'max:255'],
            'google_pay_number' => ['nullable', 'string', 'max:50'],
        ]);

        $profile = UserProfile::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'bank_name' => '',
                'account_number' => '',
                'ifsc_code' => '',
                'paytm_detail' => '',
                'upi_address' => '',
                'google_pay_number' => '',
            ]
        );

        $profile->update($request->only([
            'bank_name', 'account_number', 'ifsc_code',
            'paytm_detail', 'upi_address', 'google_pay_number',
        ]));

        return redirect()->route('front.profile.edit')->with('success', 'Profile updated successfully.');
    }
}
