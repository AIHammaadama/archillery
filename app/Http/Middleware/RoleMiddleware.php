<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $roles Comma-separated list of role slugs
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $roles)
    {
        if (!auth()->check()) {
            abort(403, 'You must be logged in to access this resource');
        }

        $allowedRoles = explode(',', $roles);
        
        if (!auth()->user()->hasAnyRole($allowedRoles)) {
            abort(403, 'You do not have permission to access this resource');
        }

        return $next($request);
    }
}
