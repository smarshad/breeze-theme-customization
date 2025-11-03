<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */

    public function showForgotPassword()
    {
        $bodyCss = getAuthPageCss();
        return view('newauth.password.forgot-password', compact('bodyCss'));
    }


    public function sentPasswordResetLink(Request $request){
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        \Log::info('Password reset requested', [
            'email' => $request->email,
            'ip' => $request->ip(),
        ]);

        $status = ResetPassword::sendResetLink($request->only('email'));
        
        if ($status === ResetPassword::RESET_LINK_SENT) {
            return back()->with('status', 'A password reset link has been sent to your email.');
        } else {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'We could not find a user with that email address.']);
        }
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
