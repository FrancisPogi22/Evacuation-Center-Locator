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
                'Active'    => 'success',
                // 'Disabled'  => 'danger',
                // 'Suspended' => 'warning'
                'Inactive'  => 'warning',
                'Archived'  => 'danger'
            }
                . '">' . $account->status . '</div></div>')
            ->addColumn('action', function ($account) use ($operation) {
                // if (auth()->user()->is_disable == 1) return;
                // $staticOption = '<option value="disableAccount">Disable Account</option><option value="suspendAccount">Suspend Account</option>';
                // $actionBtns   = '<div class="action-container"><select class="form-select actionSelect">
                // <option value="" disabled selected hidden>Select Action</option>' . '<option value="updateAccount">Update Account</option>';
                // if ($operation == "active") {
                //     $actionBtns .= $user->is_suspend == 0 && $user->is_disable == 0
                //         ? $staticOption
                //         : ($user->is_suspend == 1
                //             ? '<option value="openAccount">Open Account</option>'
                //             : '<option value="enableAccount">Enable Account</option>'
                //         );
                //     $actionBtns .= '<option value="archiveAccount">Archive Account</option></select>';
                // } else {
                //     $actionBtns .= ($user->is_suspend == 0 && $user->is_disable == 0)
                //         ? $staticOption
                //         : ($user->is_suspend == 1
                //             ? '<option value="openAccount">Open Account</option>'
                //             : '<option value="enableAccount">Enable Account</option>'
                //         );
                //     $actionBtns .= '<option value="unArchiveAccount">Unarchive Account</option></select>';
                // }
                // return $actionBtns . '</div>';
                $actionBtns = '<div class="action-container"><select class="form-select actionSelect">
                    <option value="" disabled selected hidden>Select Action</option>
                    <option value="updateAccount">Update Account</option>';

                $actionBtns .= $account->status == "Archived"
                    ? '<option value="unArchiveAccount">Unarchive Account</option></select>'
                    : ($operation == "active" ? ($account->status == "Active"
                        ? '<option value="inactiveAccount">Inactive Account</option>'
                        : '<option value="activeAccount">Active Account</option>') .
                        '<option value="archiveAccount">Archive Account</option></select>'
                        : '</select>');

                return $actionBtns . '</div>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function createAccount(Request $request)
    {
        $createAccountValidation = Validator::make($request->all(), [
            'organization' => 'required',
            'name'         => 'required',
            'email'        => 'required|email|unique:user,email',
            'position'     => 'required'
        ]);

        if ($createAccountValidation->fails())
            return response(['status' => 'warning', 'message' => $createAccountValidation->errors()->first()]);

        $defaultPassword   = Str::password(15);
        $userAccountData   = $this->user->create([
            'organization' => $request->organization,
            'position'     => $request->position,
            'name'         => Str::title(trim($request->name)),
            'email'        => trim($request->email),
            'password'     => Hash::make($defaultPassword),
            'status'       => "Active",
            'is_disable'   => 0,
            // 'is_suspend'   => 0,
            'is_archive'   => 0
        ]);
        Mail::to(trim($request->email))->send(new UserCredentialsMail([
            'email'        => trim($request->email),
            'organization' => $request->organization,
            'position'     => Str::upper($request->position),
            'password'     => $defaultPassword
        ]));
        $this->logActivity->generateLog($userAccountData->id, $userAccountData->name, 'created a new account');

        return response([]);
    }

    public function updateAccount(Request $request, $userId)
    {
        $updateAccountValidation = Validator::make($request->all(), [
            'organization' => 'required',
            'name'         => 'required',
            'position'     => 'required',
            'email'        => 'required|email'
        ]);

        if ($updateAccountValidation->fails())
            return response(['status' => 'warning', 'message' => $updateAccountValidation->errors()->first()]);

        $userAccount = $this->user->find($userId);
        $userAccount->update([
            'organization' => $request->organization,
            'name'         => Str::title(trim($request->name)),
            'position'     => $request->position,
            'email'        => trim($request->email)
        ]);
        $this->logActivity->generateLog($userId, $userAccount->name, 'updated a account');

        return response([]);
    }

    public function activeAccount($userId, $operation)
    {
        $userAccount = $this->user->find($userId);
        $userAccount->update([
            'status' => $operation == "active" ? "Active" : "Inactive",
            'is_disable' => $operation == "active" ? 0 : 1
        ]);
        $this->logActivity->generateLog($userId, $userAccount->name, $operation . "d a account");

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
                'current_password' => 'required',
                'password'         => 'required',
                'confirmPassword'  => 'required|same:password'
            ]);

            if ($changePasswordValidation->fails())
                return response(['status' => 'warning', 'message' => $changePasswordValidation->errors()->first()]);

            $userAccount = $this->user->find($userId);
            $userAccount->update(['password' => Hash::make(trim($request->password))]);
            $this->logActivity->generateLog($userId, $userAccount->name, 'changed a password');
            return response([]);
        }

        return response(['status' => 'warning', 'message' => "Current password doesn't match."]);
    }

    public function archiveAccount($userId, $operation)
    {
        $userAccount = $this->user->find($userId);
        $userAccount->update([
            'is_archive' => $operation == "archive" ? 1 : 0,
            'status' => $operation == "archive" ? "Archived" : "Active"
        ]);
        $this->logActivity->generateLog($userId, $userAccount->name, $operation . "d a account");

        return response([]);
    }
}
