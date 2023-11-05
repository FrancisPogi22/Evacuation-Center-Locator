<script>
    const body = $('body'),
        themeIcon = $('#themeIcon'),
        themeText = $('#themeText'),
        themeIconResident = $('#themeIconResident'),
        theme = sessionStorage.getItem('theme');

    $(document).ready(() => {
        @auth
        let changePasswordValidation,
            badge = $('#badge'),
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

        changePasswordValidation = changePasswordForm.validate({
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

        $(document).on('keyup', '#current_password', function() {
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
                    method: 'POST',
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

        // Echo.channel('notification').listen('Notification', (e) => {
        //     let {
        //         area,
        //         incident
        //     } = e.notifications;
        //     const dropdownMenu = $('#notification-container .dropdown-menu');

        //     if (area.length > 0 || incident.length > 0) {
        //         const badge = document.createElement('span');
        //         const container = document.querySelector('#notification-container');
        //         dropdownMenu.empty();
        //         badge.innerHTML =
        //             `<span class="badge" id="badge">${area.length + incident.length}</span>`;
        //         container.insertBefore(badge, container.firstChild);

        //         area.forEach((areaNotification) => {
        //             dropdownMenu.append(`
        //                 <li>
        //                     <a href="{{ route('manage.report', 'manage') }}" class="dropdown-item">
        //                         <p>Resident reported a area: ${areaNotification.type}</p>
        //                     </a>
        //                 </li>
        //             `);
        //         });

        //         incident.forEach((incidentNotification) => {
        //             dropdownMenu.append(`
        //                 <li>
        //                     <a href="{{ route('manage.report', 'manage') }}" class="dropdown-item">
        //                         <p>Resident report a incident: ${incidentNotification.details}</p>
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

    $(document).on('click', '.changeTheme', () => {
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
            theme: "light"
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
                method: "PUT",
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

    function setInfoWindowButtonStyles(button, bgColor) {
        button.css({
            'background-color': bgColor + ')',
            'border-color': bgColor + ')'
        }).hover(
            function() {
                $(this).css({
                    'background-color': bgColor + '-hover)',
                    'border-color': bgColor + '-hover)'
                });
            },
            function() {
                $(this).css({
                    'background-color': bgColor + ')',
                    'border-color': bgColor + ')'
                });
            }
        );
    }

    function toggleShowImageBtn(button, content, markers) {
        const [latitude, longitude] = button.prev().text().split(',');
        markers.find(marker => {
            let position = marker.getPosition();
            if (position.lat() == latitude && position.lng() == longitude)
                google.maps.event.trigger(marker, 'click');
        });

        const isView = button.text().includes('View');
        const icon = isView ? '<i class="bi bi-chevron-contract"></i> Hide' :
            '<i class="bi bi-chevron-expand"></i> View';
        const bgColor = isView ? 'var(--color-red' : 'var(--color-primary';

        button.html(icon);
        content.attr('hidden', !isView);
        setInfoWindowButtonStyles(button, bgColor);

        button.closest('.gm-style-iw-d').animate({
            scrollTop: button.closest('.info-description.photo').position().top - 6
        }, 500);

    }

    function formatDateTime(dateTimeString, condition) {
        let arguments;

        switch (condition) {
            case 'date':
                arguments = {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                };
                break;
            case 'time':
                arguments = {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                };
                break;
            default:
                arguments = {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                };
        }

        return new Date(dateTimeString).toLocaleString('en-US', arguments);
    }

    function enableDarkMode() {
        body.addClass('dark-mode');
        themeIcon.removeClass('bi-moon').addClass('bi-sun');
        themeIconResident.removeClass('bi-sun-fill').addClass('bi-moon-fill');
        themeText.text('Light Mode');
        sessionStorage.setItem('theme', 'dark');
        $('hr').addClass('bg-white');
        $('#logo').attr('src', '{{ asset('assets/img/E-LIGTAS-Logo-White.png') }}');
        if (typeof map != 'undefined') {
            map.setOptions({
                styles: mapDarkModeStyle
            });

            if (typeof directionDisplay != 'undefined') {
                directionDisplay.setOptions({
                    polylineOptions: {
                        strokeColor: '#ffffff',
                        strokeOpacity: 1.0,
                        strokeWeight: 3
                    }
                });

                if (directionDisplay.getDirections() != null)
                    directionDisplay.setDirections(directionDisplay.getDirections());
            }


            if (typeof userBounds != 'undefined')
                userBounds.setOptions({
                    fillColor: "#ffffff",
                    fillOpacity: 0.3,
                    strokeColor: "#ffffff",
                    strokeOpacity: 0.8,
                    strokeWeight: 2
                });
        }
    }

    function disableDarkMode() {
        body.removeClass('dark-mode');
        themeIcon.removeClass('bi-sun').addClass('bi-moon');
        themeIconResident.removeClass('bi-moon-fill').addClass('bi-sun-fill');
        themeText.text('Dark Mode');
        sessionStorage.setItem('theme', 'light');
        $('hr').removeClass('bg-white').addClass('bg-dark');
        $('#logo').attr('src', '{{ asset('assets/img/E-LIGTAS-Logo-Black.png') }}');
        if (typeof map != 'undefined') {
            map.setOptions({
                styles: mapLightModeStyle
            });

            if (typeof directionDisplay != 'undefined') {
                directionDisplay.setOptions({
                    polylineOptions: {
                        strokeColor: '#3388FF',
                        strokeOpacity: 1.0,
                        strokeWeight: 3
                    }
                });

                if (directionDisplay.getDirections() != null)
                    directionDisplay.setDirections(directionDisplay.getDirections());
            }

            if (typeof userBounds != 'undefined')
                userBounds.setOptions({
                    fillColor: "#3388FF",
                    fillOpacity: 0.3,
                    strokeColor: "#3388FF",
                    strokeOpacity: 0.8,
                    strokeWeight: 2
                });
        }
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

    function showSuccessMessage(message) {
        return toastr.success(message, 'Success');
    }

    function showInfoMessage(message) {
        return toastr.info(message, 'Info');
    }

    function showErrorMessage(message = 'An error occurred while processing your request.') {
        return toastr.error(message, 'Error');
    }

    const mapLightModeStyle = [{
        "featureType": "poi.business",
        "stylers": [{
            "visibility": "off"
        }]
    }];

    const mapDarkModeStyle = [{
            "featureType": "all",
            "elementType": "labels.text.fill",
            "stylers": [{
                "color": "#b3b3b3"
            }]
        },
        {
            "featureType": "all",
            "elementType": "labels.text.stroke",
            "stylers": [{
                    "color": "#212529"
                },
                {
                    "weight": 3
                },
                {
                    "gamma": 0.84
                }
            ]
        },
        {
            "featureType": "landscape",
            "elementType": "geometry",
            "stylers": [{
                "color": "#1a528b"
            }]
        },
        {
            "featureType": "landscape",
            "elementType": "geometry.fill",
            "stylers": [{
                "color": "#1e293b"
            }]
        },
        {
            "featureType": "road",
            "elementType": "geometry.fill",
            "stylers": [{
                "color": "#475569"
            }]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry.fill",
            "stylers": [{
                "color": "#d8aa1e"
            }]
        },
        {
            "featureType": "road.highway",
            "elementType": "geometry.stroke",
            "stylers": [{
                "color": "#475569"
            }]
        },
        {
            "featureType": "road.local",
            "elementType": "geometry.stroke",
            "stylers": [{
                "color": "#212529"
            }]
        },
        {
            "featureType": "water",
            "elementType": "geometry",
            "stylers": [{
                "color": "#111d37"
            }]
        },
        {
            "featureType": "water",
            "elementType": "geometry.fill",
            "stylers": [{
                "color": "#111d37"
            }]
        },
        {
            "featureType": "poi",
            "elementType": "geometry",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "transit.station.airport",
            "elementType": "geometry",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "landscape.man_made",
            "elementType": "geometry.fill",
            "stylers": [{
                "visibility": "off"
            }]
        },
        {
            "featureType": "landscape.man_made",
            "elementType": "geometry.stroke",
            "stylers": [{
                "color": "#ffc107"
            }]
        },
        {
            "featureType": "poi.business",
            "stylers": [{
                "visibility": "off"
            }]
        }
    ];
</script>
