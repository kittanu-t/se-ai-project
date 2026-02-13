<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'user' || !Auth::user()->active) {
            abort(403, 'Unauthorized.');
        }
        return $next($request);
    }
}