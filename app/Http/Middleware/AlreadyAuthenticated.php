<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AlreadyAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $userOrganization = auth()->user()->organization;
            return $userOrganization  == "CDRRMO" ? redirect()->route('dashboard.cdrrmo')->with('warning', 'Request Can`t Perform.') :
                redirect()->route('dashboard.cswd')->with('warning', 'Request Can`t Perform.');
        }

        return $next($request);
    }
}
