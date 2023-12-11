<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use Illuminate\Support\Facades\DB;
use App\Mail\SendResetPasswordLink;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    private $user, $logActivity;

    public function __construct()
    {
        $this->user        = new User;
        $this->logActivity = new ActivityUserLog;
    }

    public function authUser(Request $request)
    {
        if (auth()->attempt($request->only('email', 'password'))) return $this->checkUserAccount();

        return back()->withInput()->with('warning', 'Incorrect User Credentials.');
    }

    public function findAccount(Request $request)
    {
        $verifyEmailValidation = Validator::make($request->all(), [
            'email' => 'required|email|exists:user'
        ]);

        if ($verifyEmailValidation->fails()) return back()->with('warning', 'Email address is not exist.');

        $token = Str::random(124);
        DB::table('password_resets')->insert([
            'email'      => trim($request->email),
            'token'      => $token,
            'created_at' => now()->addHours(3)
        ]);
        Mail::to($request->email)->send(new SendResetPasswordLink(['token' => $token]));

        return back()->with('success', 'We have sent you an email with a link to reset your password.');
    }

    public function resetPasswordForm($token)
    {
        return (!$passwordReset = DB::table('password_resets')->where('token', $token)->first()) ? redirect('/')->with('warning', 'Unauthorized Token.') : (Carbon::parse($passwordReset->created_at)->isPast() ? redirect('/')->with('warning', 'Token Expired.') : view('authentication.resetPasswordForm', compact('token')));
    }

    public function resetPassword(Request $request)
    {
        $resetPasswordValidation = Validator::make($request->all(), [
            'email'                 => 'required|email|exists:user',
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($resetPasswordValidation->fails()) return back()->withInput()->with('warning', implode('<br>', $resetPasswordValidation->errors()->all()));

        $email = $request->email;
        $this->user->where('email', $email)->update(['password' => Hash::make($request->password)]);
        DB::table('password_resets')->where('email', $email)->delete();

        return redirect('/')->with('success', 'Your password has been changed.');
    }

    public function logout()
    {
        $this->logActivity->generateLog('Logged out account');
        auth()->logout();
        session()->flush();
        return redirect('/login')->with('success', 'Successfully Logged out.');
    }

    private function checkUserAccount()
    {
        if (!auth()->check()) return back();

        $userAuthenticated = auth()->user();

        if ($userAuthenticated->status != 'Active') {
            auth()->logout();
            session()->flush();

            return back()->withInput()->with('warning', 'Your account is not accessible, please reach out to admin.');
        }

        $this->logActivity->generateLog('Logged in account');

        return redirect("/" . strtolower($userAuthenticated->organization) . "/dashboard")->with('success', "Welcome $userAuthenticated->name.");
    }
}
