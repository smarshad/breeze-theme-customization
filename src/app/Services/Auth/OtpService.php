<?php

namespace App\Services\Auth;

use App\Models\EmailOtp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Handle the service logic.
     */
    public function handleOtp(array $data, $request)
    {
        // 1st check if session has user data
        // 2nd Fetch Otp
        // 3rd Check Otp is expired or not
        // 4th Validate
        // 4th if expired return with error
        // 5. Authenticate user after successful OTP
        // 6. Cleanup (session and db row) and success redirect

        $users = $request->session()->get('users');

        if (!$users || !isset($users['email'], $users['password'])) {
            Log::warning('OTP verification attempted without session data.', [
                'session_users' => $users,
            ]);
            return redirect()->route('login')->with(['error' => 'Session expired or invalid. Please log in again.']);
        }

        $email = $users['email'];
        $checkOtp = EmailOtp::where(['email' => $email])->first();

        if (!$checkOtp) {
            Log::error('No OTP found for email.', ['email' => $email, 'otp_entered' => $data['otp']]);
            return back()->withInput()->with(['error' => 'No OTP found. Please request a new one!']);
        }

        if ($checkOtp->expired_at < Carbon::now()) {
            Log::error('OTP expired for email.', ['email' => $email, 'otp_entered' => $data['otp']]);
            return back()->withInput()->with(['error' => 'The OTP has expired. Please request a new one!']);
        }

        if ($checkOtp->otp != $data['otp']) {
            Log::error('Invalid OTP entered.', [
                'email' => $email,
                'otp_entered' => $data['otp'],
            ]);
            return back()->withInput()->with(['error' => 'Invalid OTP entered.']);
        }

        if (!Auth::attempt(['email' => $email, 'password' => $users['password']])) {
            Log::error('Login failed after OTP verification.', ['email' => $email]);
            $back = '<b><a href="' . route('login') . '">Back to Login</a></b>';
            return back()->withInput()->with(['error' => 'Invalid Email Or Password ' . $back]);
        }
        $request->session()->forget('users');
        $checkOtp->delete();
        Log::info('User successfully verified OTP and logged in.', [
            'email' => $email,
        ]);
        return redirect()->route('dasboard1');
    }
}
