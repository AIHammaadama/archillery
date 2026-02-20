<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && $request->session()->has('last_activity')) {
            $lastActivity = $request->session()->get('last_activity');
            $expiresAt = now()->subMinutes(config('session.lifetime'));
            if ($lastActivity < $expiresAt) {
                Auth::logout();
                $request->session()->invalidate();
                return redirect('login')->with('error', 'Your session has expired. Please log in again.');
            }
        }
        return $next($request);
    }
}
