<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OtpVerificationController extends Controller
{
    /**
     * Show OTP verification form.
     */
    public function showForm(Request $request)
    {
        $userId = $request->session()->get('otp_verify_user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login to receive OTP again.');
        }
        $user = User::find($userId);
        if (!$user || $user->verified) {
            $request->session()->forget('otp_verify_user_id');
            return redirect()->route('login')->with('success', $user && $user->verified ? 'Already verified. You can login.' : 'Session invalid. Please login.');
        }
        return view('auth.verify-otp', ['frontAuth' => true]);
    }

    /**
     * Verify OTP and activate user.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $userId = $request->session()->get('otp_verify_user_id');
        if (!$userId) {
            return redirect()->route('customer.login')->with('error', 'Session expired. Please login to receive OTP again.');
        }

        $user = User::find($userId);
        if (!$user || $user->verified) {
            $request->session()->forget('otp_verify_user_id');
            return redirect()->route('customer.login')->with($user && $user->verified ? 'success' : 'error', $user && $user->verified ? 'Already verified. You can login.' : 'Session invalid.');
        }

        if ($user->otp !== $request->input('otp')) {
            return redirect()->back()->withInput()->withErrors(['otp' => 'Invalid OTP.']);
        }

        if ($user->otp_expires_at && Carbon::now()->isAfter($user->otp_expires_at)) {
            return redirect()->back()->withInput()->withErrors(['otp' => 'OTP has expired. Please request a new one from login.']);
        }

        $user->verified = 1;
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        $request->session()->forget('otp_verify_user_id');

        return redirect()->route('customer.login')->with('success', 'Account verified successfully. You can login now.');
    }

    /**
     * Resend OTP (e.g. from OTP page).
     */
    public function resend(Request $request)
    {
        $userId = $request->session()->get('otp_verify_user_id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login to receive OTP again.');
        }

        $user = User::find($userId);
        if (!$user || $user->verified) {
            $request->session()->forget('otp_verify_user_id');
            return redirect()->route('login');
        }

        $otp = (string) random_int(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(15);
        $user->save();

        OtpService::send($user, $otp);

        return redirect()->back()->with('success', 'OTP sent again. Check your email/phone.');
    }
}
