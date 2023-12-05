@auth
    <div class="modal fade" id="changePasswordModal" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <header class="change-modal-label-container bg-warning">
                    <h1 class="change-modal-label">Change Password</h1>
                    <button type="button" data-bs-dismiss="modal" aria-label="Close" id="closeChangePasswordBtn">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </header>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        @csrf
                        <div class="form-content">
                            <input type="hidden" id="checkPasswordRoute"
                                data-route="{{ route('account.check.password') }}">
                            <input type="hidden" id="changePasswordRoute"
                                data-route="{{ route('account.reset.password', auth()->user()->id) }}">
                            <div class="field-container">
                                <label for="current_password">Current Password</label>
                                <input type="text" name="current_password" class="form-control" id="current_password"
                                    autocomplete="off" placeholder="Enter Current Password">
                                <i class="bi bi-x-circle checkPassword" hidden></i>
                            </div>
                            <div class="field-container new-pass" hidden>
                                <label for="password">New Password</label>
                                <input type="password" name="password" id="password" class="form-control"
                                    autocomplete="off" placeholder="Enter New Password" disabled>
                                <i class="bi bi-eye-slash toggle-password" id="showPassword" data-target="#password"></i>
                            </div>
                            <div class="field-container confirm-pass" hidden>
                                <label for="confirmPassword">Confirm Password</label>
                                <input type="password" name="confirmPassword" id="confirmPassword" class="form-control"
                                    autocomplete="off" placeholder="Enter Confirm Password" onpaste="return false;"
                                    disabled>
                                <i class="bi bi-eye-slash toggle-password" id="showConfirmPassword"
                                    data-target="#confirmPassword"></i>
                            </div>
                            <div id="change-button-container">
                                <button id="resetPasswordBtn" class="btn-update modalBtn" disabled hidden>
                                    <div id="btn-loader" hidden>
                                        <div id="loader-inner"></div>
                                    </div>
                                    <span id="btn-text">Change</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endauth
