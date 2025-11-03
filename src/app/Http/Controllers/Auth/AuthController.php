<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password AS ResetPassword;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
class AuthController extends Controller
{
    protected $bodyCss;
    public function __construct()
    {
        $this->bodyCss = 'class="authentication-bg bg-primary authentication-bg-pattern d-flex align-items-center pb-0 vh-100"';
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

    

    // Forgot password
    public function showForgotPassword(){
        $bodyCss = $this->bodyCss;
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

    public function changePasswordForm(Request $request)
    {
        $bodyCss = $this->bodyCss;
        return view('newauth.password.reset-password', compact('bodyCss','request'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = ResetPassword::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        \Log::info('Password Reset', [
            'email' => $request->email,
            'ip' => $request->ip(),
        ]);

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status == ResetPassword::PASSWORD_RESET) {
            \Log::info('Password reset SUCCESS', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'status' => 'PASSWORD_RESET',
            ]);
    
            return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
        } else {
            \Log::warning('Password reset FAILED', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'status' => $status,
            ]);
    
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Failed to reset password. Please check your email or token.']);
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }


}
