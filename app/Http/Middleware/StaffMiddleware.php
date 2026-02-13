<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class StaffMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'staff' || !Auth::user()->active) {
            abort(403, 'Unauthorized.');
        }
        return $next($request);
    }
}
