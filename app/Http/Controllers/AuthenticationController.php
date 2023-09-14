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
        if (auth()->attempt($request->only('email', 'password')))
            return $this->checkUserAccount();

        return back()->withInput()->with('warning', 'Incorrect User Credentials.');
    }

    public function findAccount(Request $request)
    {
        $verifyEmailValidation = Validator::make($request->all(), [
            'email' => 'required|email|exists:user'
        ]);

        if ($verifyEmailValidation->fails())
            return back()->with('warning', 'Email address is not exist.');

        try {
            $token = Str::random(124);
            DB::table('password_resets')->insert([
                'email'      => $request->email,
                'token'      => $token,
                'created_at' => now()->addHours(3)
            ]);
            Mail::to($request->email)->send(new SendResetPasswordLink(['token' => $token]));
            return back()->with('success', 'We have sent you an email with a link to reset your password.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while processing your request.');
        }
    }

    public function resetPasswordForm($token)
    {
        $passwordReset = DB::table('password_resets')->where('token', $token)->first();
        return !$passwordReset ? redirect('/')->with('warning', 'Unauthorized Token.') : (Carbon::parse($passwordReset->created_at)->isPast() ? redirect('/')->with('warning', 'Token Expired.') : view('authentication.resetPasswordForm', compact('token')));
    }

    public function resetPassword(Request $request)
    {
        $resetPasswordValidation = Validator::make($request->all(), [
            'email'                 => 'required|email|exists:user',
            'password'              => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($resetPasswordValidation->fails())
            return back()->withInput()->with('warning', $resetPasswordValidation->errors()->first());

        $this->user->where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        DB::table('password_resets')->where('email', $request->email)->delete();
        return redirect('/')->with('success', 'Your password has been changed.');
    }

    public function logout()
    {
        $this->logActivity->generateLog(null, Str::of(auth()->user()->name)->title() . 'Logged Out');
        auth()->logout();
        session()->flush();
        return redirect('/')->with('success', 'Successfully Logged out ');
    }

    private function checkUserAccount()
    {
        if (!auth()->check()) return back();

        $userAuthenticated = auth()->user();
        $userOrganization  = $userAuthenticated->organization;
        $userName          = $userAuthenticated->name;

        if ($userAuthenticated->is_suspend == 1 && $userAuthenticated->suspend_time <= Carbon::now()->format('Y-m-d H:i:s')) {
            $this->user->find($userAuthenticated->id)->update([
                'status'       => 'Active',
                'is_suspend'   => 0,
                'suspend_time' => null
            ]);
        } else if ($userAuthenticated->is_suspend == 1) {
            $suspendTime = Carbon::parse($userAuthenticated->suspend_time)->format('F j, Y H:i:s');
            auth()->logout();
            session()->flush();
            return back()->withInput()->with('warning', 'Your account has been suspended until ' . $suspendTime);
        }

        $this->logActivity->generateLog(null, Str::of($userName)->title() . ' Logged In');
        return redirect("/" . Str::of($userOrganization)->lower() . "/dashboard")->with('success', "Welcome to " . $userName);
    }
}
