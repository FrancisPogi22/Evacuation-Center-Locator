<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const html = $('html'),
        logo = $('#logo'),
        themeIcon = $('#themeIcon'),
        themeText = $('#themeText'),
        themeIconResident = $('#themeIconResident'),
        theme = localStorage.getItem('theme');

    @auth
    let changePasswordValidation, currentPassword = $('#current_password'),
        password = $('#password'),
        modal = $('#changePasswordModal'),
        confirmPassword = $('#confirmPassword'),
        btnContainer = $('#change-button-container'),
        passwordShowIcon = $('#showPassword'),
        confirmPasswordShowIcon = $('#showConfirmPassword'),
        changePasswordForm = $('#changePasswordForm'),
        eyeIcon = $('.toggle-password'),
        checkPasswordIcon = $('.checkPassword'),
        btnLoader = $('#resetPasswordBtn #btn-loader'),
        btnText = $('#resetPasswordBtn #btn-text'),
        current_password = "";
    @endauth
    $(document).ready(() => {
        theme == 'dark' ? enableDarkMode() : disableDarkMode();

        $(document).on('click', '#imageBtn', () => {
            event.preventDefault();
            $('#inputImage').click();
        });

        $(document).on('change', '#inputImage', function() {
            if (this.files[0]) {
                if (!['image/jpeg', 'image/jpg', 'image/png'].includes(this.files[0].type)) {
                    $(this).val('');
                    $('#selectedReportImage').attr('src', '').prop('hidden', 1);
                    $('#imageBtn').html('<i class="bi bi-image"></i>Select');
                    setInfoWindowButtonStyles($('#imageBtn'), 'var(--color-primary');
                    $('#image-error').text('Please select an image file.')
                        .prop('style', 'display: block !important');

                    return;
                } else
                    $('#image-error').prop('style', 'display: none !important');

                let reader = new FileReader(),
                    container = $(this).closest('.gm-style-iw-d');

                reader.onload = (e) => {
                    $('#selectedReportImage').attr('src', e.target.result);
                };
                reader.readAsDataURL(this.files[0]);
                $('#imageBtn').html('<i class="bi bi-arrow-repeat"></i>Change');
                setInfoWindowButtonStyles($('#imageBtn'), 'var(--color-yellow');
                $('#selectedReportImage').prop('hidden', 0);
                container.animate({
                    scrollTop: container.prop('scrollHeight')
                }, 500);
            } else {
                $('#selectedReportImage').attr('src', '').prop('hidden', 1);
                $('#imageBtn').html('<i class="bi bi-image"></i>Select');
                setInfoWindowButtonStyles($('#imageBtn'), 'var(--color-primary');
            }
        });

        @auth
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
            submitHandler(form) {
                confirmModal('Do you want to change your password?').then((result) => {
                    if (!result.isConfirmed) return;

                    return currentPassword.val() == password.val() ? (showWarningMessage(),
                            modal.modal('hide')) :
                        $.ajax({
                            method: "PUT",
                            url: $('#changePasswordRoute').data('route'),
                            data: $(form).serialize(),
                            beforeSend() {
                                btnLoader.prop('hidden', 0);
                                btnText.text('Changing');
                                $('#changePasswordForm input, #resetPasswordBtn, #closeChangePasswordBtn')
                                    .prop('disabled', 1);
                            },
                            success(response) {
                                return response.status == "warning" ? showWarningMessage(
                                        response.message) :
                                    (showSuccessMessage('Password successfully changed.'),
                                        $(form)[0].reset(), currentPassword.text(""), modal
                                        .modal('hide'));
                            },
                            error: showErrorMessage,
                            complete() {
                                btnLoader.prop('hidden', 1);
                                btnText.text('Change');
                                $('#changePasswordForm input, #resetPasswordBtn, #closeChangePasswordBtn')
                                    .prop('disabled', 0);
                            }
                        });
                });
            }
        });

        $(document).on('keyup', '#current_password', function() {
            current_password = $('#current_password').val();
            clearTimeout($(this).data('checkingDelay'));
            $(this).data('checkingDelay', setTimeout(() => {
                let checkPasswordRoute = $('#checkPasswordRoute').data('route'),
                    newPass = $('.new-pass'),
                    confirmPass = $('.confirm-pass');

                if (current_password == "") {
                    btnContainer.add(checkPasswordIcon.removeClass('bi-check2-circle')
                        .addClass('bi-x-circle')).prop('hidden', 1);
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
                                'bi-x-circle error').prop('hidden', 0);
                            eyeIcon.removeClass('bi-eye').addClass('bi-eye-slash');
                            password.add(confirmPassword).val("").prop('type',
                                'password').prop('disabled', 1);
                            newPass.add(confirmPass).add(btnContainer).prop(
                                'hidden', 1);
                        } else {
                            password.add(confirmPassword).add(btnContainer.prop(
                                'hidden', 0)).prop('disabled', 0);
                            $('#resetPasswordBtn').prop('disabled', 0);
                            newPass.add(confirmPass).add(btnContainer).add(
                                checkPasswordIcon.removeClass(
                                    'bi-x-circle error').addClass(
                                    'bi-check2-circle success')).prop('hidden',
                                0);
                        }
                    }
                });
            }, 500));
        });

        modal.on('hidden.bs.modal', () => {
            resetChangePasswordForm();
            btnContainer.prop('hidden', 1);
            $('.new-pass, .confirm-pass').prop('hidden', 1);
            checkPasswordIcon.removeClass('success').removeClass('error').prop('hidden', 1);
            changePasswordValidation.resetForm();
        });

        $(document).on('click', '.toggle-password', function() {
            let currentPasswordInput = $('#current_password');

            if (current_password == "") {
                currentPasswordInput.prop('style', 'border-color:red !important');
                setTimeout(() => {
                    currentPasswordInput.removeAttr('style');
                }, 1000);
            } else {
                let inputElement = $($(this).data('target'));

                currentPasswordInput.removeAttr('style');
                inputElement.prop('type', inputElement.prop('type') == 'password' ? 'text' :
                    'password');
                $(this).toggleClass('bi-eye-slash bi-eye');
            }
        });

        $(document).on('click', '.sub-btn', function() {
            $(this).next('.sub-menu').toggleClass('active');
            $(this).find('.dropdown').toggleClass('rotate');
        });

        @if (auth()->user()->organization == 'CDRRMO')
            getNotifications();

            $(document).on('click', '.dropdown-notification', function() {
                const list = $(this),
                    lat = list.attr('aria-lat'),
                    lng = list.attr('aria-long'),
                    type = list.attr('aria-type');

                sessionStorage.setItem('report_type', type);
                sessionStorage.setItem('report_latitude', lat);
                sessionStorage.setItem('report_longitude', lng);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: 'PATCH',
                    url: "{{ route('notification.remove', 'reportId') }}"
                        .replace('reportId', list.attr('aria-id')),
                    success() {
                        const currentLocation =
                            "{{ route('manage.report', 'manage') }}";

                        window.location.href == currentLocation ?
                            (openReportDetails(type, lat, lng),
                                getNotifications()) : window.location.href =
                            currentLocation;
                    }
                });
            });

            Echo.channel('notification').listen('Notification', (e) => {
                getNotifications();
            });
        @endif
    @endauth
    $(document).on('click', '.changeTheme', () =>
        html.attr('data-theme') == 'dark' ? disableDarkMode() : enableDarkMode());

    @guest$('#emergencyBtn').on('click', () => {
        if (!navigator.geolocation) return showInfoMessage('Geolocation is not supported by this browser.');

        confirmModal('Are you in need of help or rescue?').then((result) => {
            if (!result.isConfirmed) return;

            let emergencyWatchID, attempt = 0,
                requestSuccess = 0;

            emergencyWatchID = navigator.geolocation.watchPosition(
                (position) => {
                    if (position.coords.accuracy <= 500) {
                        navigator.geolocation.clearWatch(emergencyWatchID);
                        $.post({
                            url: "{{ route('resident.emergency.report') }}",
                            data: {
                                _token: "{{ csrf_token() }}",
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude
                            },
                            beforeSend: function() {
                                $('#emergencyBtn').prop('disabled', true);
                            },
                            success: function(response) {
                                if (response.status == "blocked")
                                    showWarningMessage(response.message);
                                else {
                                    requestSuccess = 1;
                                    showAlertMessage(response.status ==
                                        "duplicate" ? response.message :
                                        "A rescue request has been sent. If it's safe, please remain where you are and try to stay calm. Your safety is our priority."
                                    );
                                }
                            },
                            complete: function() {
                                $('#emergencyBtn').prop('disabled', false);
                            }
                        });
                    } else {
                        attempt = attempt + 1;

                        if (attempt == 2)
                            setTimeout(() => {
                                if (requestSuccess == 0) {
                                    showAlertMessage('Cannot get your current location.',
                                        'error', '#dc2626', '#ef4444');
                                    navigator.geolocation.clearWatch(emergencyWatchID);
                                }
                            }, 5000);
                    }
                },
                (error) => {
                    let message;
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            message =
                                'Request for geolocation denied.';
                            break;
                        case error.TIMEOUT:
                        case error.POSITION_UNAVAILABLE:
                            message = 'Cannot get your current location.';
                            break;
                    }
                    showWarningMessage(message);
                    navigator.geolocation.clearWatch(emergencyWatchID);
                }, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                }
            );
        });
    });

    function showAlertMessage(text, icon = 'info', iconColor = '#1d4ed8', confirmButtonColor = '#2682fa') {
        return Swal.fire({
            title: 'Message',
            text,
            icon,
            iconColor,
            showDenyButton: false,
            confirmButtonText: 'Close',
            confirmButtonColor,
            allowOutsideClick: false
        });
    }
    @endguest
    });

    @auth
    @if (auth()->user()->organization == 'CDRRMO')
        function getNotifications() {
            $.get('{{ route('notifications.get') }}', (notifications) => {
                let count = notifications.length;

                $('.bi-bell-fill').html(`<div id="notification-count-container" ${count == 0 ? 'hidden' : ''}>
                        <span id="notification-count">${count}</span></div>`);

                $('.dropdown-menu.notification').html(count > 0 ? notifications.map(notification => `
                    <li class="dropdown-notification" aria-id="${notification.id}"
                        aria-type="${notification.type}" aria-lat="${notification.latitude}"
                        aria-long="${notification.longitude}">
                        <b class="report-time">${formatDateTime(notification.report_time)}</b><br>
                        <center>New ${notification.type.toLowerCase()} report</center>
                    </li>
                `).join('') : '<div class="empty-notification">No new report notification</div>');
            });
        }

        function openReportDetails(type, lat, lng) {
            const markerIndex = {
                'Incident': 0,
                'Emergency': 1,
                'Flooded': 2,
                'Roadblocked': 2
            };

            reportMarkers[markerIndex[type]].forEach(marker => {
                let reportMarker = marker.getPosition();

                if (reportMarker.lat() == lat && reportMarker.lng() == lng) {
                    google.maps.event.trigger(marker, 'click');
                    sessionStorage.setItem('report_type', 'null');

                    return false;
                }
            });
        }
    @endif

    function resetChangePasswordForm() {
        current_password = "";
        currentPassword.text("");
        changePasswordForm[0].reset();
        eyeIcon.removeClass('bi-eye').addClass('bi-eye-slash');
        password.add(confirmPassword).prop('type', 'password').prop('disabled', 1);
    }

    function changeButton(button, text, primary = false) {
        button.html(
            `<i class="bi bi-${primary ? 'image' : 'arrow-repeat'}"></i>${primary ? 'Select' : 'Change'} ${text}`);
        setInfoWindowButtonStyles(button, `var(--color-${primary ? 'primary' : 'yellow'}`);
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
        const icon = isView ? '<i class="bi bi-chevron-contract"></i>Hide' :
            '<i class="bi bi-chevron-expand"></i>View';
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
        html.attr('data-theme', "dark");
        logo.attr('src', "{{ asset('assets/img/E-LIGTAS-Logo-White.png') }}");
        themeIcon.removeClass('bi-moon').addClass('bi-sun');
        themeIconResident.removeClass('bi-sun-fill').addClass('bi-moon-fill');
        themeText.text('Light Mode');
        localStorage.setItem('theme', 'dark');
        $('hr').addClass('bg-white');
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
        logo.attr('src', "{{ asset('assets/img/E-LIGTAS-Logo-Black.png') }}");
        html.attr('data-theme', "light");
        themeIcon.removeClass('bi-sun').addClass('bi-moon');
        themeIconResident.removeClass('bi-moon-fill').addClass('bi-sun-fill');
        themeText.text('Dark Mode');
        localStorage.setItem('theme', 'light');
        $('hr').removeClass('bg-white').addClass('bg-dark');
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

    function scrollToElement(element) {
        $('html, body').animate({
            scrollTop: $(element).offset().top - 15
        }, 500);
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
