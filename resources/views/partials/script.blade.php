<script>
    @auth
    let currentPassword = $('#currentPassword'),
        password = $('#password'),
        confirmPassword = $('#confirmPassword'),
        resetPasswordBtn = $('#resetPasswordBtn'),
        passwordShowIcon = $('#showPassword'),
        confirmPasswordShowIcon = $('#showConfirmPassword'),
        changePasswordForm = $('#changePasswordForm'),
        changePasswordModal = $('#changePasswordModal'),
        eyeIcon = $('.toggle-password'),
        checkPasswordIcon = $('.checkPassword'),
        current_password = "";

    $(document).ready(function() {
        let changePasswordValidation = changePasswordForm.validate({
            rules: {
                password: 'required',
                confirmPassword: 'required'
            },
            messages: {
                password: 'Password field is required.',
                confirmPassword: 'Confirm password field is required.'
            },
            errorElement: 'span',
            submitHandler: changePasswordHandler
        });

        $(document).on('input', '#current_password', function() {
            current_password = $('#current_password').val();

            clearTimeout($(this).data('checkingDelay'));

            $(this).data('checkingDelay', setTimeout(function() {
                let checkPasswordRoute = $('#checkPasswordRoute').data('route');

                if (current_password == "") {
                    checkPasswordIcon.removeClass('bi-check2-circle').addClass(
                        'bi-x-circle').prop('hidden', true);
                    changePasswordValidation.resetForm();
                    resetChangePasswordForm();
                    return;
                }

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: checkPasswordRoute,
                    data: {
                        current_password: current_password
                    },
                    success(response) {
                        if (response.status == "warning") {
                            current_password = "";
                            checkPasswordIcon.removeClass(
                                'bi-check2-circle success').addClass(
                                'bi-x-circle error').prop('hidden', false);
                            eyeIcon.removeClass('bi-eye').addClass('bi-eye-slash');
                            password.add(confirmPassword).val("").prop('type',
                                'password').prop('disabled', true);
                        } else {
                            checkPasswordIcon.removeClass('bi-x-circle error')
                                .addClass('bi-check2-circle success').prop('hidden',
                                    false);
                            password.add(confirmPassword).add(resetPasswordBtn)
                                .prop('disabled', false);
                        }
                    }
                });
            }, 500));
        });

        changePasswordModal.on('hidden.bs.modal', function() {
            resetChangePasswordForm();
            checkPasswordIcon.removeClass('success').removeClass('error').prop('hidden', true);
            changePasswordValidation.resetForm();
        });

        $(document).on('click', '.toggle-password', function() {
            const currentPasswordInput = $('#current_password');

            if (current_password == "") {
                currentPasswordInput.css('border-color', 'red');
                setTimeout(function() {
                    currentPasswordInput.removeAttr('style');
                }, 1000);
            } else {
                currentPasswordInput.removeAttr('style');
                const inputElement = $($(this).data('target'));
                inputElement.prop('type', inputElement.prop('type') == 'password' ? 'text' :
                    'password');
                $(this).toggleClass('bi-eye-slash bi-eye');
            }
        });
    });

    function datePicker(id) {
        return flatpickr(id, {
            enableTime: true,
            allowInput: true,
            static: false,
            timeFormat: "h:i K",
            dateFormat: "D, M j, Y h:i K",
            minuteIncrement: 1,
            secondIncrement: 1,
            position: "below center"
        });
    }

    function resetChangePasswordForm() {
        current_password = "";
        currentPassword.text("").removeAttr('class');
        changePasswordForm[0].reset();
        eyeIcon.removeClass('bi-eye').addClass('bi-eye-slash');
        password.add(confirmPassword).prop('type', 'password').prop('disabled', true);
    }

    function changePasswordHandler(form) {
        let changePasswordRoute = $('#changePasswordRoute').data('route');

        confirmModal('Do you want to change your password?').then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "PUT",
                    url: changePasswordRoute,
                    data: $(form).serialize(),
                    success(response) {
                        return response.status == "warning" ? showWarningMessage(response.message) :
                            (showSuccessMessage('Password successfully changed.'), form[0].reset(),
                                currentPassword.text(""), changePasswordModal.modal('hide'));
                    },
                    error() {
                        showErrorMessage();
                    }
                });
            }
        });
    }
    @endauth

    $(document).ready(function() {
        $(document).on('click', '.overlay-text', function() {
            var reportPhotoUrl = $(this).closest('.image-wrapper').find('.report-img').attr('src');
            var overlay = $('<div class="overlay show"><img src="' + reportPhotoUrl +
                '" class="overlay-image"></div>');
            $('body').append(overlay);
            overlay.click(() => {
                overlay.remove();
            });
        });
    });

    function confirmModal(text) {
        return Swal.fire({
            title: 'Confirmation',
            text: text,
            icon: 'info',
            iconColor: '#1d4ed8',
            showDenyButton: true,
            confirmButtonText: 'Yes',
            confirmButtonColor: '#15803d',
            denyButtonText: 'No',
            denyButtonColor: '#B91C1C',
            allowOutsideClick: false
        });
    }

    function getRowData(row, table) {
        let currentRow = $(row).closest('tr');

        if (table.responsive && table.responsive.hasHidden()) {
            currentRow = currentRow.prev('tr');
        }

        return table.row(currentRow).data();
    }

    function showWarningMessage(message) {
        return toastr.warning(message, 'Warning');
    }

    function showSuccessMessage(message, shouldReload) {
        return toastr.success(message, 'Success', {
            onHidden() {
                if (shouldReload) location.reload();
            }
        });
    }

    function showInfoMessage(message) {
        return toastr.info(message, 'Info');
    }

    function showErrorMessage(message = 'An error occurred while processing your request.') {
        return toastr.error(message, 'Error');
    }
</script>
