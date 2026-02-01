<?php

namespace App\Services;

use App\Models\User;
use App\Models\SiteTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Send OTP to user via email and/or SMS (if email/phone given).
     *
     * @param  \App\Models\User  $user
     * @param  string  $otp
     * @return void
     */
    public static function send(User $user, string $otp): void
    {
        if (!empty($user->email)) {
            self::sendEmail($user, $otp);
        }
        if (!empty($user->phone)) {
            self::sendSms($user, $otp);
        }
    }

    /**
     * Send OTP via email.
     */
    public static function sendEmail(User $user, string $otp): void
    {
        try {
            $template = SiteTemplate::where([
                'template_name' => 'customer_otp',
                'status' => 1,
                'template_type' => 1,
            ])->first();

            $fullName = trim($user->first_name . ' ' . $user->last_name) ?: 'User';
            $data = [
                'otp' => $otp,
                'name' => $fullName,
                'email' => $user->email,
            ];

            if ($template) {
                SiteTemplate::sendMail($user->email, $fullName, $data, 'customer_otp');
            } else {
                Mail::raw(
                    "Your OTP for verification is: {$otp}. Valid for 15 minutes. - " . (\Config::get('settings.company_name') ?? 'App'),
                    function ($m) use ($user, $fullName) {
                        $m->to($user->email, $fullName)
                            ->subject('Your verification OTP');
                    }
                );
            }
        } catch (\Exception $e) {
            Log::warning('OTP email send failed: ' . $e->getMessage());
        }
    }

    /**
     * Send OTP via SMS. Extend with Twilio/Nexmo etc. when configured.
     */
    public static function sendSms(User $user, string $otp): void
    {
        try {
            $template = SiteTemplate::where([
                'template_name' => 'customer_otp_sms',
                'status' => 1,
                'template_type' => 2,
            ])->first();

            $message = $template && $template->subject
                ? str_replace('{{otp}}', $otp, $template->subject)
                : "Your OTP is: {$otp}. Valid for 15 minutes.";

            // Placeholder: log or integrate with SMS gateway (Twilio, etc.)
            if (config('services.sms_driver')) {
                // e.g. Twilio::send($user->phone, $message);
            }
            Log::info('OTP SMS (stub) for ' . $user->phone . ': ' . $message);
        } catch (\Exception $e) {
            Log::warning('OTP SMS send failed: ' . $e->getMessage());
        }
    }
}
