<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\SendOtpMail;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    protected $bodyCss;
    public function __construct()
    {
        $this->bodyCss = 'class="authentication-bg bg-primary authentication-bg-pattern d-flex align-items-center pb-0 vh-100"';
    }

    public function showRegister()
    {
        $bodyCss = $this->bodyCss;
        return view('newauth/register', compact('bodyCss'));
    }

    public function showLogin()
    {
        $bodyCss = $this->bodyCss;
        return view('newauth/login', compact('bodyCss'));
    }

    public function login(LoginRequest $request)
    {

        $request->validate([
            'email' => ['required', 'email', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            \Log::error('Invalid email :', $request->email);
            return redirect()->withInput()->with(['error' => 'Invalid Email']);
        }
        $request->session()->put('users', [
            'email' => $request->email,
            'password' => $request->password,
        ]);
        if ($user->enable_two_factor_auth) {
            $otp = rand(100000, 999999);
            EmailOtp::updateOrCreate(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'otp' => $otp,
                    'expired_at' => Carbon::now()->addMinute(10),
                ]
            );
            Mail::to($request->email)->send(new SendOtpMail($otp, 'OTP For Login'));
            return redirect()->route('verify.login.otp');
        } else {
            $request->authenticate();
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard1', absolute: false));
        }
    }

    public function verifyOtp(Request $request)
    {
        $bodyCss = $this->bodyCss;
        return view('authotp.verify-login-otp', compact('bodyCss'));
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'lowercase', 'max:255', 'unique:' . User::class],
            'mobile_no' => ['required', 'string', 'mobile_no',  'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
            'password' => Hash::make($request->password),
        ]);
        // dd($user);
        $user->save();

        // login user if two factor is not available

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard1', absolute: false));
    }


    public function verifyOtpStore(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'numeric', 'digits:6']
        ], [
            'otp.required' => 'Please enter the OTP sent to your email.',
            'otp.numeric'  => 'The OTP must be a number.',
            'otp.digits'   => 'The OTP must be exactly 6 digits.',
        ]);
        $users = $request->session()->get('users');
        $checkOtp = EmailOtp::where('email', $users['email'])->first();

        if (!$checkOtp) {
            Log::error('No OTP found. Please request a new one For ', [
                'email' => $users['email'],
                'otp_entered' => $request->otp
            ]);
            return redirect()->back()->withInput()->with(['error' => 'No OTP found. Please request a new one!']);
        }

        if ($checkOtp->expired_at < Carbon::now()) {
            \Log::error('OTP Expired For', [
                'email' => $users['email'],
                'otp_entered' => $request->otp,
                'expired_at' => $checkOtp->expired_at,
            ]);

            return redirect()->back()
                ->withInput()
                ->with(['error' => 'The OTP has expired. Please request a new one!']);
        }

        if ($checkOtp->otp != $request->otp) {
            \Log::error('Invalid OTP entered', [
                'email' => $users['email'],
                'otp_entered' => $request->otp
            ]);

            return redirect()->back()
                ->withInput()
                ->with(['error' => 'Invalid OTP entered!']);
        }

        if (!Auth::attempt(['email' => $users['email'], 'password' => $users['password']])) {
            \Log::error('Login Error', ['email' => $users['email']]);
            $back = '<b><a href="'.route('login').'">Back to Login</a>';
            return redirect()->back()->withInput()->with(['error' => 'Invalid Email Or Password '.$back]);
        }

        $request->session()->forget('users');
        $checkOtp->delete();

        return redirect()->route('dashboard1');
    }

    public function update(Request $request): RedirectResponse
    {

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);


        return back()->with('password', 'password-updated');
    }

    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->noContent();
    }
}
