<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function showProfile(Request $request): View
    {
        return view('admin.users.profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function twoFactorAuth(Request $request)
    {
        try {
            $user = User::find($request->user()->id);
            if(!$user){
                \Log::error('Two factor auth toggle failed - User not found : ',$request->user()->id);
                return redirect()->route('profile.edit')->with('error', 'User not found.');
            }
            $enable_two_factor_auth = $request->enable_two_factor_auth;
            $user->enable_two_factor_auth = $enable_two_factor_auth ? 0 : 1;
            $user->save();

            $statusText = $user->enable_two_factor_auth ? 'Enabled' : 'Disabled';

            \Log::info("Two Factor Auth <b>{$statusText}</b> successfully by user ID: {$user->id}");

            return redirect()->route('profile.edit')->with(['success' => "Two Factor Authentication <b>{$statusText}</b> successfully."]);

        } catch (\Exception $e) {
            \Log::info("Error toggling Two Factor Auth: ".$e->getMessage(), ['user_id' => $request->user()->id ?? NULL, 'stack' => $e->getTraceAsString()]);
            return redirect()->back()->with(['error' => 'Something went wrong while updating Two Factor Authentication.']);
        }   
    }
    /**
     * Delete the user's account.
     */

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
