<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LockScreenController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $bodyCss = 'class="authentication-bg bg-primary authentication-bg-pattern d-flex align-items-center pb-0 vh-100"';
        // If globally locked, redirect to login with message
        if ($user->is_locked) {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been locked by an administrator.',
            ]);
        }

        return view('newauth.lock', compact('user', 'bodyCss'));
    }

    public function lock(Request $request)
    {
        $request->session()->put('locked', true);
        $request->session()->put('locked_intended_url', url()->previous());

        return redirect()->route('lock.show');
    }

    public function unlock(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.'],
            ]);
        }

        // Clear session lock
        $request->session()->forget('locked');
        $request->session()->regenerate();

        $intended = $request->session()->pull('locked_intended_url', url('/'));

        return redirect()->to($intended);
    }

    // Optional: Admin-only lock/unlock actions
    public function adminLock($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        $user->update(['is_locked' => true]);
        return back()->with('status', "User {$user->name} locked successfully.");
    }

    public function adminUnlock($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        $user->update(['is_locked' => false]);
        return back()->with('status', "User {$user->name} unlocked successfully.");
    }
}
