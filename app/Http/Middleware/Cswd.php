<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cswd
{
    public function handle(Request $request, Closure $next)
    {
        return auth()->check() && auth()->user()->organization == "CSWD" ? $next($request) : back()->with('warning', "Request Can't Perform.");
    }
}
