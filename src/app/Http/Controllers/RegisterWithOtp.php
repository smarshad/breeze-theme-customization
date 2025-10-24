<?php

namespace App\Http\Controllers;

use App\Mail\SendOtpMail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\EmailOtp;
use Illuminate\Support\Carbon;
use App\Rules\MobileNo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class RegisterWithOtp extends Controller
{
    protected $bodyCss;
    public function __construct()
    {
        $this->bodyCss = 'class="authentication-bg bg-primary authentication-bg-pattern d-flex align-items-center pb-0 vh-100"';
    }

    public function create()
    {
        $bodyCss = $this->bodyCss;
        return view('authotp/register', compact('bodyCss'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'lowercase', 'max:255', 'unique:' . User::class],
            'mobile_no' => ['required', 'string', new MobileNo,  'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $otp = rand(100000, 999999);

        EmailOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expired_at' => Carbon::now()->addMinute(10),
            ]
        );

        try {
            Mail::to($request->email)->send(new SendOtpMail($otp));
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('OTP email failed: ' . $e->getMessage());
    
            // Optional: show user-friendly message
            return back()->with('error', 'Failed to send OTP. Please try again later.');
        }
        // Store user input securely in session
        $request->session()->put('users', [
            'name' => $request->name,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
            'password' => Hash::make($request->password),
        ]);
        return redirect()->route('verify.otp');
    }

    public function verifyOtp(){
        $bodyCss = $this->bodyCss;
        return view('authotp/verify-otp', compact('bodyCss'));
    }

    public function verifyOtpStore(Request $request){
        $request->validate(['otp' => ['required','numeric','digits:6']]);
        $users = $request->session()->get('users');
        $checkEmailOtp = EmailOtp::where('email', $users['email'])
        ->where('otp', $request->otp)
        ->where('expired_at', '>=', Carbon::now())
        ->first();

        if(!$checkEmailOtp){
            Log::error('OTP verification failed', ['email' => $users['email'], 'otp_entered' => $request->otp]);
            return redirect()->back()->withInput()->with(['error' => 'Invalid OTP Or OTP is expired!']);
        }
        Log::info('OTP verified successfully', ['email' => $users['email']]);

        $user = User::create([
            'email' => $users['email'],
            'name' => $users['name'],
            'mobile_no' => $users['mobile_no'],
            'password' => $users['password'],
        ]);
        unset($users['password']);
        Auth::login($user);
        Log::info('User successfully Registered', $users);
        $checkEmailOtp->delete();
        $request->session()->forget('users');
        return redirect()->route('dashboard1');
    }
}
