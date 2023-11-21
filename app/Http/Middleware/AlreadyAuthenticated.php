<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AlreadyAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        return auth()->check()
            ? redirect()->route('dashboard.' . (auth()->user()->organization == "CDRRMO" ? 'cdrrmo' : 'cswd'))->with('warning', 'Request Can`t Perform.')
            : $next($request);
    }
}
