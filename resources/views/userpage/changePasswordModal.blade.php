<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="change-modal-label-container bg-warning">
                <h1 class="change-modal-label">Change Password</h1>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    @csrf
                    <div class="form-content">
                        <input type="hidden" id="checkPasswordRoute"
                            data-route="{{ route('account.check.password') }}">
                        <input type="hidden" id="changePasswordRoute"
                            data-route="{{ route('account.reset.password', auth()->user()->id) }}">
                        <div class="field-container">
                            <label>Current Password</label>
                            <input type="text" name="current_password" class="form-control" id="current_password"
                                autocomplete="off" placeholder="Enter Current Password">
                            <i class="bi bi-x-circle checkPassword" hidden></i>
                        </div>
                        <div class="field-container">
                            <label>New Password</label>
                            <input type="password" name="password" id="password" class="form-control"
                                autocomplete="off" placeholder="Enter New Password" disabled>
                            <i class="bi bi-eye-slash toggle-password" id="showPassword" data-target="#password"></i>
                        </div>
                        <div class="field-container">
                            <label>Confirm Password</label>
                            <input type="password" name="confirmPassword" id="confirmPassword" class="form-control"
                                autocomplete="off" placeholder="Enter Confirm Password" onpaste="return false;"
                                disabled>
                            <i class="bi bi-eye-slash toggle-password" id="showConfirmPassword"
                                data-target="#confirmPassword"></i>
                        </div>
                        <div id="change-button-container">
                            <button id="resetPasswordBtn" class="btn-update" disabled>Change</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
