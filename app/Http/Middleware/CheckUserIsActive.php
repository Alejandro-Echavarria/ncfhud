<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verify if the user is active
        if (Auth::check() && !Auth::user()->is_active) {
            // If user is inactive, redirect to the login page
            Auth::logout();
            return redirect()->route('login');
        }

        return $next($request);
    }
}
