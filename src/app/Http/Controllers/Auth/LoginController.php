<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\LoginService;
use App\Services\Auth\OtpService;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\OtpRequest;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected $bodyCss;

    public function __construct(protected LoginService $loginService, protected OtpService $otpService)
    {
        $this->bodyCss = 'class="authentication-bg bg-primary authentication-bg-pattern d-flex align-items-center pb-0 vh-100"';
    }

    public function showLogin()
    {
        $bodyCss = $this->bodyCss;
        return view('newauth/login', compact('bodyCss'));
    }

    public function login(LoginRequest $request)
    {
        return $this->loginService->handleLogin($request->validated());
    }

    public function showOtpForm(Request $request)
    {
        $bodyCss = $this->bodyCss;
        return view('authotp.verify-login-otp', compact('bodyCss'));
    }

    public function verifyOtp(OtpRequest $request)
    {
        return $this->otpService->handleOtp($request->validated(), $request);
    }
}
