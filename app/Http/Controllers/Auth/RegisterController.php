<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Helpers\Helper;
use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
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
        $rules['refercode'] = ['nullable', 'string', 'max:50'];
        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        if (isset($data['full_name']) && !empty(trim($data['full_name']))) {
            $parts = explode(' ', trim($data['full_name']), 2);
            $data['first_name'] = $parts[0];
            $data['last_name'] = $parts[1] ?? '';
        }
        $initials = Helper::generateNameInitials($data['first_name'], $data['last_name']);
        $referredBy = null;
        if (!empty($data['refercode'] ?? '')) {
            $referredBy = User::where('referral_code', $data['refercode'])->first();
        }
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
            'referral_code' => strtoupper(Helper::randomString(6)),
            'referred_by_id' => $referredBy ? $referredBy->id : null,
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
     *
     * @param  string  $token
     * @return \Illuminate\Http\RedirectResponse
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
