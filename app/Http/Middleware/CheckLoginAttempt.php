<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CheckLoginAttempt
{
    public function handle(Request $request, Closure $next)
    {
        $userEmail    = $request->email;
        $decaySeconds = 10;
        $endTime      = $request->session()->get($userEmail . 'end_time', null);
        $attempts     = $request->session()->get($userEmail . 'login_attempts', 0);

        if ($endTime != null) {
            if ($endTime <= now()) {
                $request->session()->forget($userEmail . 'end_time');
            } else {
                return back()->withInput()->with([
                    'limit'   => Carbon::parse(now()->addSeconds($decaySeconds)),
                    'seconds' => now()->diffInSeconds($endTime),
                    'warning' => 'Login attempts are temporarily disabled, Please wait.'
                ]);
            }
        }

        if (auth()->attempt($request->only('email', 'password')))
            return $next($request);

        $attempts++;
        $request->session()->put($userEmail . 'login_attempts', $attempts);

        if ($attempts >= 4) {
            return redirect('/recoverAccount')->with([
                'failed' => true,
                'email_attempt' =>  $userEmail,
                'error_message' => "The password you entered was not valid."
            ]);
        }

        if ($endTime <= now() && $endTime != null) {
            $request->session()->put($userEmail . 'login_attempts', $attempts - 1);
            $request->session()->forget($userEmail . 'end_time');
        }

        if ($attempts >= 3) {
            $seconds = Carbon::parse(now()->addSeconds($decaySeconds));
            $request->session()->put($userEmail . 'end_time', $seconds);
            return back()->withInput()->with([
                'limit'   => $seconds,
                'seconds' => now()->diffInSeconds(now()->addSeconds($decaySeconds))
            ]);
        }

        return $next($request);
    }
}
