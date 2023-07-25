<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="flex justify-center p-3 rounded-t bg-yellow-500">
                <h1 class="fs-5 text-white p-1 font-extrabold">Change Password</h1>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    @csrf
                    <div class="bg-slate-50 pt-3 pb-2 rounded">
                        <div class="flex-auto">
                            <div class="flex flex-wrap">
                                <input type="hidden" id="checkPasswordRoute"
                                    data-route="{{ route('account.check.password') }}">
                                <input type="hidden" id="changePasswordRoute"
                                    data-route="{{ route('account.reset.password', auth()->user()->id) }}">
                                <input type="text" id="operation" hidden>
                                <div class="field-container">
                                    <label class="mb-0">Current Password</label>
                                    <input type="text" name="current_password" class="form-control"
                                        id="current_password" autocomplete="off">
                                    <span class="text-xs text-red-600 italic" id="currentPassword"></span>
                                </div>
                                <div class="field-container mb-2 relative">
<<<<<<< Updated upstream
                                    <label class="mb-0">New Password</label>
                                    <input type="password" name="password" id="password" class="form-control"
=======
                                    <label>New Password</label>
                                    <input type="password" name="password" id="password" class="form-control mb-2"
>>>>>>> Stashed changes
                                        autocomplete="off" disabled>
                                    <i class="bi bi-eye-slash absolute cursor-pointer text-lg mt-1"
                                        id="showPassword"></i>
                                </div>
                                <div class="field-container mb-2 relative">
                                    <label class="mb-0">Confirm Password</label>
                                    <input type="password" name="confirmPassword" id="confirmPassword"
                                        class="form-control mb-2" autocomplete="off" onpaste="return false;" disabled>
                                    <i class="bi bi-eye-slash absolute cursor-pointer text-lg mt-1"
                                        id="showConfirmPassword"></i>
                                </div>
<<<<<<< Updated upstream
                                <div class="w-full p-4">
                                    <button id="changePasswordBtn" class="btn-edit p-2 float-right"
                                        disabled>Save</button>
=======
                                <div class="w-full px-4 pt-3 pb-4">
                                    <button id="resetPasswordBtn" class="btn-update p-2 float-right"
                                        disabled>Change</button>
>>>>>>> Stashed changes
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
