<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIfLocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        // 
        if($user && $user->is_locked){
            auth()->logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account has been locked by an administrator']);
        }

        // If session is locked (local lock screen)
        if ($user && session('locked', false)) {
            if ($request->routeIs('lock.show') || $request->routeIs('lock.unlock') || $request->routeIs('lock.lock')) {
                return $next($request);
            }
            return redirect()->route('lock.show');
        }
        return $next($request);
    }
}
