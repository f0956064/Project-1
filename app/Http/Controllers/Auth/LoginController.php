<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */
    protected $maxAttempts=3;
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo   = '/admin/home';
    protected $view         = 'admin.login';

    public function __construct()
    {
        $segment = request()->segment(1);
        if($segment != 'admin' && !request()->is('customer*')) {
            $this->redirectTo = '/';
            $this->view = 'front.download';
        }

        if($segment === 'customer') {
             $this->redirectTo = 'customer/home';
            $this->view = 'auth.login';
        }
    }

    public function showLoginForm()
    {
        if (Auth::user()) {
            return redirect()->intended($this->redirectTo);
        }
        $frontAuth = !request()->is('admin*');
        return view($this->view, compact('frontAuth'));
    }
    
    public function redirectTo()
    {
        if (request()->get('r')) {
            return request()->get('r');
        }
        // Base redirect on where the user logged in from (request URL), not constructor
        if (request()->is('admin*')) {
            return '/admin/home';
        }
        return '/';
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // Customization: Validate if client status is active (1)
        $loginField = $request->get($this->username());
        $client = $this->findUserByLoginField($loginField);

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        if (empty($client)) {
            return $this->sendFailedLoginResponse($request, 'Please check your mobile/email or password.');
        }

        // Customization: If client status is inactive (0) return failed_status error.
        if ($client->status === 0) {
            return $this->sendFailedLoginResponse($request, 'Your Account is blocked. Please conract administrator.');
        }

        if (\Hash::check($request->get('password'), $client->password)) {
            // Frontend: unverified customer → resend OTP and redirect to OTP page
            if (!$client->verified && (request()->is('login') || request()->is('customer/*'))) {
                $this->clearLoginAttempts($request);
                $otp = (string) random_int(100000, 999999);
                $client->otp = $otp;
                $client->otp_expires_at = \Carbon\Carbon::now()->addMinutes(15);
                $client->save();
                \App\Services\OtpService::send($client, $otp);
                $request->session()->put('otp_verify_user_id', $client->id);
                return redirect()->route('otp.verify.form')->with('success', 'OTP sent to your email/phone. Please verify to activate your account.');
            }
            if (!$client->verified) {
                return $this->sendFailedLoginResponse($request, 'Account is not verified yet.');
            }
            \Auth::login($client, $request->boolean('remember'));
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            return $this->sendLoginResponse($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Send the response after the user was authenticated.
     * Admin login always redirects to /admin/home; frontend to / (no intended URL override).
     */
    protected function sendLoginResponse(Request $request)
    {
        $path = $this->redirectTo();
        if ($request->expectsJson()) {
            return response()->json(['redirect' => $path]);
        }
        return redirect()->to($path);
    }

    protected function findUserByLoginField($value)
    {
        if (empty($value)) {
            return null;
        }
        if (strpos($value, '@') !== false) {
            return User::where('email', $value)->first();
        }
        return User::where('phone', $value)->first();
    }

    public function username()
    {
        $segment = request()->segment(1);
        if ($segment === 'admin') {
            return 'email';
        }
        return 'mobile_or_email';
    }

    protected function validateLogin(Request $request)
    {
        $field = $this->username();
        $rules = ['password' => 'required'];
        $rules[$field] = $field === 'mobile_or_email' ? 'required|string' : 'required|string|email';
        $request->validate($rules);
    }

    protected function credentials(Request $request)
    {
        $field = $this->username();
        $value = $request->get($field);
        if ($field === 'mobile_or_email') {
            $key = strpos($value, '@') !== false ? 'email' : 'phone';
            return ['password' => $request->get('password'), 'status' => 1, $key => $value];
        }
        return array_merge($request->only($field, 'password'), ['status' => 1]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $field
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request, $trans = 'auth.failed')
    {
        $errors = [$this->username() => trans($trans)];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    public function adminLogout()
    {
        //Auth::guard('admin')->logout();
        auth()->logout();
        // redirect to homepage
        return redirect('admin/login');

    }

    public function logout()
    {
        auth()->logout();
        // redirect to homepage
        return redirect('customer/login');

    }
}
