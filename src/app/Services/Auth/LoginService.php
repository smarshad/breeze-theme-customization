<?php

namespace App\Services\Auth;

use App\Mail\SendOtpMail;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class LoginService
{

    public function handleLogin(array $data)
    {
        // $validated = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if(!$user){
            Log::warning('Login Failed: Invalid Email',['email' => $data['email']]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Invalid Email']);
        }

        session(['users' =>[
            'email' => $data['email'],
            'password' => $data['password']
        ]]);

        return $user->enable_two_factor_auth ? $this->handleTwoFactor($data['email']) : $this->authenticateUser($data);
    }

    protected function handleTwoFactor($email){ 
        $otp = random_int(100000, 999999);
        $expiry = (int) config('auth.otp_expiry', 10); // configurable from config/auth.php
        EmailOtp::updateOrCreate(['email' => $email], [
            'email' => $email,
            'otp'   => $otp,
            'expired_at' => Carbon::now()->addMinutes($expiry)
        ]);
       

        Mail::to($email)->send(new SendOtpMail($otp, 'OTP For Login'));
        return redirect()->route('verify.login.otp');
    }

    protected function authenticateUser($request){
        $request->authenticate();
        $request->session()->regenerate();
        Log::info('User successfully logged in.', ['email' => $request->email]);
        return redirect()->intended(route('dashboard1', absolute: false));
    }
}
