<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ActivityUserLog;
use Yajra\DataTables\DataTables;
use App\Mail\UserCredentialsMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class UserAccountsController extends Controller
{
    private $user, $logActivity;

    public function __construct()
    {
        $this->user        = new User;
        $this->logActivity = new ActivityUserLog;
    }

    public function userAccounts($operation)
    {
        $userAccounts = $this->user->where('is_archive', $operation == "active" ? 0 : 1);
        $userId       = auth()->user()->id;
        $userAccounts = auth()->user()->organization == "CSWD" ? $userAccounts->whereNotIn('id', [$userId]) :
            $userAccounts->where('organization', 'CDRRMO')->whereNotIn('id', [$userId]);

        return DataTables::of($userAccounts)
            ->addColumn('status', fn ($account) => '<div class="status-container"><div class="status-content bg-' . match ($account->status) {
                'Active'   => 'success',
                'Inactive' => 'warning',
                'Archived' => 'danger'
            }
                . '">' . $account->status . '</div></div>')
            ->addColumn('action', function ($user) use ($operation) {
                return '<div class="action-container"><select class="form-select actionSelect">' .
                    '<option value="" disabled selected hidden>Select Action</option>' .
                    '<option value="updateAccount">Update Account</option>' .
                    ($operation == 'active' ?
                        '<option value="' . ($user->is_disable == 0 ? 'inactiveAccount' : 'activeAccount') . '">' .
                        ($user->is_disable == 0 ? 'Disable' : 'Enable') . ' Account</option>' : '') .
                    '<option value="' . ($operation == 'active' ? 'archiveAccount' : 'unArchiveAccount') . '">' .
                    ($operation == 'active' ? 'Archive' : 'Unarchive') . ' Account</option>' .
                    '</select></div>';
            })->rawColumns(['status', 'action'])->make(true);
    }

    public function createAccount(Request $request)
    {
        $createAccountValidation = Validator::make($request->all(), [
            'name'         => 'required',
            'email'        => 'required|email|unique:user,email',
            'position'     => 'required',
            'organization' => 'required'
        ]);

        if ($createAccountValidation->fails())
            return response(['status' => 'warning', 'message' => implode('<br>', $createAccountValidation->errors()->all())]);

        $defaultPassword   = Str::password(15);
        $userAccountData   = $this->user->create([
            'name'         => Str::title(trim($request->name)),
            'email'        => trim($request->email),
            'status'       => "Active",
            'position'     => $request->position,
            'password'     => Hash::make($defaultPassword),
            'is_disable'   => 0,
            'is_archive'   => 0,
            'organization' => $request->organization
        ]);
        Mail::to(trim($request->email))->send(new UserCredentialsMail([
            'email'        => trim($request->email),
            'position'     => Str::upper($request->position),
            'password'     => $defaultPassword,
            'organization' => $request->organization
        ]));
        $this->logActivity->generateLog('Created a new account(ID - ' . $userAccountData->id . ')');

        return response([]);
    }

    public function updateAccount(Request $request, $userId)
    {
        $updateAccountValidation = Validator::make($request->all(), [
            'name'         => 'required',
            'email'        => 'required|email',
            'position'     => 'required',
            'organization' => 'required'
        ]);

        if ($updateAccountValidation->fails()) return response(['status' => 'warning', 'message' => implode('<br>', $updateAccountValidation->errors()->all())]);

        $this->user->find($userId)->update([
            'name'         => Str::title(trim($request->name)),
            'email'        => trim($request->email),
            'position'     => $request->position,
            'organization' => $request->organization
        ]);
        $this->logActivity->generateLog('Updated a account(ID - ' . $userId . ')');

        return response([]);
    }

    public function toggleAccountStatus($userId, $operation)
    {
        $account = $this->user->find($userId);

        if ($operation == "Inactive")
            Auth::logoutOtherDevices(Crypt::decrypt($account->password));

        $account->update([
            'status'     => $operation == "active" ? "Active" : "Inactive",
            'is_disable' => $operation == "active" ? 0 : 1
        ]);
        $this->logActivity->generateLog('Disabled a account(ID - ' . $userId . ')');

        return response([]);
    }

    public function enableAccount($userId)
    {
        $this->user->find($userId)->update([
            'status'     => 'Active',
            'is_disable' => 0
        ]);
        $this->logActivity->generateLog('Enabled a account(ID - ' . $userId . ')');

        return response([]);
    }

    public function checkPassword(Request $request)
    {
        return Hash::check($request->current_password, auth()->user()->password) ? response([]) : response(['status' => 'warning']);
    }

    public function resetPassword(Request $request, $userId)
    {
        if (Hash::check($request->current_password, auth()->user()->password)) {
            $changePasswordValidation = Validator::make($request->all(), [
                'password'         => 'required',
                'confirmPassword'  => 'required|same:password',
                'current_password' => 'required'
            ]);

            if ($changePasswordValidation->fails()) return response(['status' => 'warning', 'message' => implode('<br>', $changePasswordValidation->errors()->all())]);

            $this->user->find($userId)->update(['password' => Hash::make(trim($request->password))]);
            $this->logActivity->generateLog('changed a password(ID - ' . $userId . ')');

            return response([]);
        }

        return response(['status' => 'warning', 'message' => "Current password doesn't match."]);
    }

    public function archiveAccount($userId, $operation)
    {
        $userAccount = $this->user->find($userId);
        $userAccount->update([
            'status'     => $operation == 'archive' ? 'Archived' : ($userAccount->is_disable == 0 ? 'Active' : 'Inactive'),
            'is_archive' => $operation == 'archive' ? 1 : 0
        ]);
        $this->logActivity->generateLog(ucfirst($operation) . 'd a account(ID - ' . $userId . ')');

        return response([]);
    }
}
