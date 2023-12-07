<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckCredential
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->is_disable == 1) {
            auth()->logout();
            session()->flush();
            return redirect('/login')->with('warning', 'Your account is not accessible, please reach out to admin.');
        } else {
            return $next($request);
        }
    }
}
