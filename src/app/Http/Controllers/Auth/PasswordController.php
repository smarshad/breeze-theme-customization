<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Services\Auth\ForgotPasswordService;
use App\Services\Auth\ResetPasswordService;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
dd();
    public function __construct(protected ForgotPasswordService $forgotPasswordServicefor, protected ResetPasswordService $resetPasswordService) {}

    public function showForgotPassword()
    {
        $bodyCss = getAuthPageCss();
        return view('newauth.password.forgot-password', compact('bodyCss'));
    }

    public function sentPasswordResetLink(ForgotPasswordRequest $request)
    {
        return $this->forgotPasswordServicefor->sentLink($request);
    }

    public function changePasswordForm(Request $request)
    {
        $bodyCss = getAuthPageCss();
        return view('newauth.password.reset-password', compact('bodyCss', 'request'));
    }

    public function resetPassword(PasswordResetRequest $request)
    {
        return $this->resetPasswordService->resetPassword($request);
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

        return back()->with('status', 'password-updated');
    }
}
