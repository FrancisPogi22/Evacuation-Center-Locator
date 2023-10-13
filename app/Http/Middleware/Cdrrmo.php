<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cdrrmo
{
    public function handle(Request $request, Closure $next): Response
    {
        return auth()->check() && auth()->user()->organization == 'CDRRMO' ? $next($request) : back()->with('warning', "Request Can't Perform.");
    }
}
