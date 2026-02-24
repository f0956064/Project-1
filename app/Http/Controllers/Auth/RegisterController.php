<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserWallet;
use App\Models\Role;
use App\Services\OtpService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        // $this->middleware('guest');
    }

    /**
     * Handle registration: customer (frontend) uses OTP flow; admin uses standard flow.
     */
    public function register(Request $request)
    {
        if ($request->is('admin*')) {
            $this->validator($request->all())->validate();
            $user = $this->create($request->all());
            $this->guard()->login($user);
            return $this->registered($request, $user) ?: redirect($this->redirectPath());
        }

        return $this->registerCustomerWithOtp($request);
    }

    /**
     * Customer registration: create with verified=0, send OTP, redirect to OTP page.
     */
    protected function registerCustomerWithOtp(Request $request)
    {
        $data = $request->all();

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required_without:email', 'nullable', 'string', 'max:25'],
            'email' => ['required_without:phone', 'nullable', 'string', 'email', 'max:255'],
            'password' => array_merge(User::$passwordValidator, ['confirmed']),
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withInput($request->except('password', 'password_confirmation'))->withErrors($validator);
        }

        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;

        $existingByEmail = $email ? User::where('email', $email)->first() : null;
        $existingByPhone = $phone ? User::where('phone', $phone)->first() : null;
        $existing = $existingByEmail ?? $existingByPhone;

        if ($existing) {
            if ($existing->verified) {
                if ($email && $existing->email === $email) {
                    $validator->errors()->add('email', 'The email has already been taken.');
                } else {
                    $validator->errors()->add('phone', 'The phone has already been taken.');
                }
                return redirect()->back()->withInput($request->except('password', 'password_confirmation'))->withErrors($validator);
            }
            return redirect()->route('login')
                ->with('error', 'User already exists. Please login.');
        }

        $initials = Helper::generateNameInitials($data['first_name'], $data['last_name']);
        $otp = (string) random_int(100000, 999999);
        $otpExpires = Carbon::now()->addMinutes(15);

        $user = User::create([
            'username' => Helper::randomString(15),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($data['password']),
            'status' => 1,
            'verified' => 1,
            'name_initials' => $initials['name_initials'] ?? null,
            'name_initial_color_type' => $initials['name_initial_color_type'] ?? 1,
            'otp' => $otp,
            'otp_expires_at' => $otpExpires,
        ]);

        $customerRole = Role::where('title', 'customer')->first();
        if ($customerRole) {
            UserRole::create(['user_id' => $user->id, 'role_id' => $customerRole->id]);
        }

        UserWallet::firstOrCreate(
            ['user_id' => $user->id],
            ['amount' => 0]
        );

        // OtpService::send($user, $otp);

        // $request->session()->put('otp_verify_user_id', $user->id);

        return redirect()->route('customer.login')->with('success', 'User registered successfully. Please login.');
    }

    protected function validator(array $data)
    {
        $rules = [
            'password' => User::$passwordValidator,
        ];
        if (isset($data['full_name']) && !empty(trim($data['full_name']))) {
            $rules['full_name'] = ['required', 'string', 'max:255'];
            $rules['phone'] = ['required', 'string', 'max:25'];
            $rules['email'] = ['nullable', 'string', 'email', 'max:255', 'unique:users'];
        } else {
            $rules['first_name'] = ['required', 'string', 'max:255'];
            $rules['last_name'] = ['required', 'string', 'max:255'];
            $rules['phone'] = ['nullable', 'string', 'max:25'];
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users'];
        }
        return Validator::make($data, $rules);
    }

    protected function create(array $data)
    {
        if (isset($data['full_name']) && !empty(trim($data['full_name']))) {
            $parts = explode(' ', trim($data['full_name']), 2);
            $data['first_name'] = $parts[0];
            $data['last_name'] = $parts[1] ?? '';
        }
        $initials = Helper::generateNameInitials($data['first_name'], $data['last_name']);
        $user = User::create([
            'username' => Helper::randomString(15),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status' => 1,
            'verified' => 1,
            'name_initials' => $initials['name_initials'] ?? null,
            'name_initial_color_type' => $initials['name_initial_color_type'] ?? 1,
        ]);

        UserWallet::firstOrCreate(
            ['user_id' => $user->id],
            ['amount' => 0]
        );

        return $user;
    }

    public function showRegistrationForm()
    {
        return view('auth.register', ['frontAuth' => true]);
    }

    /**
     * Verify user by token (e.g. from email link).
     */
    public function verifyUser($token)
    {
        $user = User::where('remember_token', $token)->first();
        if ($user) {
            $user->verified = 1;
            $user->remember_token = null;
            $user->save();
            return redirect()->route('login')->with('success', __('Your account is verified. You can login now.'));
        }
        return redirect()->route('login')->with('error', __('Invalid or expired verification link.'));
    }
}
