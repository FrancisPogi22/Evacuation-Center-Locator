<script>
    const body = $('body'),
        themeIcon = $('#themeIcon'),
        themeText = $('#themeText'),
        theme = sessionStorage.getItem('theme');

    @auth
    let badge = $('#badge'),
        currentPassword = $('#currentPassword'),
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
    @endauth
    $(document).ready(() => {
        @auth
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

        changePasswordModal.on('hidden.bs.modal', () => {
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

        $(document).on('click', '.sub-btn', function() {
            $(this).next('.sub-menu').toggleClass('active');
            $(this).find('.dropdown').toggleClass('rotate');
        });

        $(document).on('click', '#notification-container button', function() {
            badge.prop('hidden', true).text(0);
        });

        // Echo.channel('notification').listen('NotificationEvent', (e) => {
        //     let {
        //         hazard,
        //         incident
        //     } = e.notifications;
        //     const dropdownMenu = $('#notification-container .dropdown-menu');

        //     if (hazard.length > 0 || incident.length > 0) {
        //         const badge = document.createElement('span');
        //         const container = document.querySelector('#notification-container');
        //         dropdownMenu.empty();
        //         badge.innerHTML =
        //             `<span class="badge" id="badge">${hazard.length + incident.length}</span>`;
        //         container.insertBefore(badge, container.firstChild);

        //         hazard.forEach((hazardNotification) => {
        //             dropdownMenu.append(`
        //                 <li>
        //                     <a href="{{ route('manage.hazard.report') }}" class="dropdown-item">
        //                         <p>Resident reported a hazard: ${hazardNotification.type}</p>
        //                     </a>
        //                 </li>
        //             `);
        //         });

        //         incident.forEach((incidentNotification) => {
        //             dropdownMenu.append(`
        //                 <li>
        //                     <a href="{{ route('incident.report', 'pending') }}" class="dropdown-item">
        //                         <p>Resident report a incident: ${incidentNotification.description}</p>
        //                         <span class="report_time">${incidentNotification.report_time}</span>
        //                     </a>
        //                 </li>
        //             `);
        //         });
        //     } else {
        //         dropdownMenu.html('<div class="empty-notification">No notification.</div>');
        //     }
        // });
    @endauth
    theme == 'dark' ? enableDarkMode() : disableDarkMode();

    $(document).on('click', '#changeTheme', () => {
        body.hasClass('dark-mode') ? disableDarkMode() : enableDarkMode();
    });
    });
    @auth

    function datePicker(id, enableTime = true) {
        const dateFormat = enableTime ? "F j, Y h:i K" : "F j, Y";

        return flatpickr(id, {
            enableTime,
            allowInput: true,
            static: false,
            timeFormat: "h:i K",
            dateFormat,
            minuteIncrement: 1,
            secondIncrement: 1,
            position: "below center",
            theme: "light",
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
        confirmModal('Do you want to change your password?').then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                type: "PUT",
                url: $('#changePasswordRoute').data('route'),
                data: $(form).serialize(),
                success(response) {
                    return response.status == "warning" ? showWarningMessage(response.message) :
                        (showSuccessMessage('Password successfully changed.'), $(form)[0].reset(),
                            currentPassword.text(""), changePasswordModal.modal('hide'));
                },
                error() {
                    showErrorMessage();
                }
            });
        });
    }
    @endauth
    function displayReportPhoto(reportPhotoUrl) {
        let overlay = $('<div class="overlay show"><img src="' + reportPhotoUrl +
            '" class="overlay-image"></div>');
        $('body').append(overlay);
        overlay.click(() => {
            overlay.remove();
        });
    }

    function enableDarkMode() {
        body.addClass('dark-mode');
        themeIcon.removeClass('bi-moon').addClass('bi-brightness-high');
        themeText.text('Light Mode');
        sessionStorage.setItem('theme', 'dark');
        $('hr').addClass('bg-white');
        $('#logo').attr('src', '{{ asset("assets/img/e-ligtas-logo-white.png") }}');
    }

    function disableDarkMode() {
        body.removeClass('dark-mode');
        themeIcon.removeClass('bi-brightness-high').addClass('bi-moon');
        themeText.text('Dark Mode');
        sessionStorage.setItem('theme', 'light');
        $('hr').removeClass('bg-white').addClass('bg-dark');
        $('#logo').attr('src', '{{ asset("assets/img/e-ligtas-logo-black.png") }}');
    }

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

        if (table.responsive && table.responsive.hasHidden()) currentRow = currentRow.prev('tr');

        return table.row(currentRow).data();
    }

    function showWarningMessage(message = "No changes were made.") {
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
