<?php

namespace App\Http\Middleware;

use Closure;

class EnsureTokenIsValid
{
    public function handle($request, Closure $next)
    {
        if ($request->input('token') !== 'my-secret-token')
            return redirect('home');

        return $next($request);
    }
}
