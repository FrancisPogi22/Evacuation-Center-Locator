<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPosition
{
    public function handle(Request $request, Closure $next)
    {
        return auth()->check() && auth()->user()->position == "Focal" || auth()->user()->position == "President" ? $next($request) : back()->with('warning', "Request Can't Perform.");
    }
}
