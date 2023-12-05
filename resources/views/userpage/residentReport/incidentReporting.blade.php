<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <main class="main-content">
            <div class="label-container">
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-megaphone"></i>
                    </div>
                </div>
                <span>REPORT INCIDENT</span>
            </div>
            <hr>
            <div class="page-button-container" hidden>
                <button class="btn-table-primary" id="retryGeolocation">
                    <i class="bi bi-geo"></i>Get Current Location Again
                </button>
            </div>
            <div class="map-border">
                <div class="area-map" id="map">
                    <div id="loader" class="show">
                        <div id="loader-inner"></div>
                        <div id="loading-text">Getting your location...</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @include('partials.script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googleMap.key') }}&callback=&v=weekly"
        defer></script>
    @include('partials.toastr')
    <script>
        let map, reportMarker, reportWindow, userMarker,
            isClicked = false,
            reportSubmitting = false,
            btnContainer = $('.page-button-container');

        function initMap(coords, zoom) {
            if (map) {
                map = null;
                reportMarker = null;
                reportWindow = null;
            }

            const loader = document.createElement('div'),
                stopBtnContainer = document.createElement('div');

            loader.id = 'loader';
            loader.innerHTML = '<div id="loader-inner"></div><div id="loading-text">Getting your location...</div>';

            stopBtnContainer.className = 'stop-btn-container';
            stopBtnContainer.innerHTML =
                '<button id="cancelReportingBtn" class="btn-remove"><i class="bi bi-stop-circle"></i>Cancel Report</button>';

            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: coords ? coords.lat : 14.246261,
                    lng: coords ? coords.lng : 121.12772
                },
                zoom: zoom,
                clickableIcons: false,
                draggableCursor: 'pointer',
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                },
                styles: localStorage.getItem('theme') == 'dark' ? mapDarkModeStyle : mapLightModeStyle
            });

            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(stopBtnContainer);
            map.controls[google.maps.ControlPosition.CENTER].push(loader);

            map.addListener("click", (event) => {
                if (reportSubmitting) return;

                $('.stop-btn-container').show();
                let coordinates = event.latLng;

                if (reportMarker) {
                    reportMarker.setPosition(coordinates);
                    reportWindow.open(map, reportMarker);
                    $('[name="latitude"]').val(coordinates.lat());
                    $('[name="longitude"]').val(coordinates.lng());
                } else {
                    if (!isClicked) showInfoMessage('You can drag the marker to adjust the location.');

                    isClicked = true;
                    reportWindow = new google.maps.InfoWindow({
                        content: `<form id="reportAreaForm">
                            @csrf
                            <input type="text" name="latitude" value="${coordinates.lat()}" hidden>
                            <input type="text" name="longitude" value="${coordinates.lng()}" hidden>
                            <div id="reportAreaFormContainer">
                                <div>
                                    <label>Details</label>
                                    <textarea type="text" name="details" class="form-control" cols="50" rows="10"></textarea>
                                </div>
                                <div class="mt-2">
                                    <label>Image</label>
                                    <input type="file" name="image" class="form-control" id="inputImage" accept=".jpeg, .jpg, .png" hidden>
                                    <div class="info-window-action-container report-area">
                                        <button class="btn btn-sm btn-primary" id="imageBtn">
                                            <i class="bi bi-image"></i>Select
                                        </button>
                                    </div>
                                    <img id="selectedReportImage" src="" class="form-control" hidden>
                                    <span id="image-error" class="error" hidden>Please select an image file.</span>
                                </div>
                                <center>
                                    <button id="submitReportBtn" class="modalBtn">
                                        <div id="defaultBtnText">
                                            <i class="bi bi-send"></i>
                                            Submit
                                        </div>
                                        <div id="loadingBtnText" hidden>
                                            <div id="btn-loader">
                                                <div id="loader-inner"></div>
                                            </div>
                                            Submitting
                                        </div>
                                    </button>
                                <center>
                            </div>
                        </form>`
                    });
                    reportMarker = new google.maps.Marker({
                        position: coordinates,
                        map: map,
                        draggable: true,
                        icon: {
                            url: "{{ asset('assets/img/Reporting.png') }}",
                            scaledSize: new google.maps.Size(35, 35)
                        },
                        label: {
                            text: 'Report Location',
                            className: 'report-marker-label'
                        }
                    });
                    reportMarker.addListener('click', () => reportWindow.open(map, reportMarker));
                    reportWindow.open(map, reportMarker);
                    reportMarker.addListener('drag', () => reportWindow.close());
                    reportMarker.addListener('dragend', () => {
                        reportWindow.open(map, reportMarker);
                        $('[name="latitude"]').val(reportMarker.getPosition().lat());
                        $('[name="longitude"]').val(reportMarker.getPosition().lng());
                    });
                }
            });
        }

        function setReportingMap(coords, zoom, success, error) {
            initMap(coords, zoom);
            $('#loader').removeClass('show');
            success && btnContainer.prop('hidden', error ? 0 : 1);
            error && $('#retryGeolocation').prop('disabled', 0);
        }

        function resetMapView() {
            map.setCenter({
                lat: userMarker ? userMarker.getPosition().lat() : 14.246261,
                lng: userMarker ? userMarker.getPosition().lng() : 121.12772
            });
            map.setZoom(userMarker ? 18 : 13);
        }

        function getCurrentPosition() {
            if (!navigator.geolocation) {
                setReportingMap(null, 13, null, null);
                return showInfoMessage('Geolocation is not supported by this browser.');
            }

            let currentWatchID;

            currentWatchID = navigator.geolocation.watchPosition(
                (position) => {
                    if (position.coords.accuracy <= 500) {
                        const coords = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };

                        setReportingMap(coords, 18, true, null);
                        showInfoMessage('Click on the map to pinpoint the location of the incident.');

                        userMarker = new google.maps.Marker({
                            position: coords,
                            map,
                            icon: {
                                url: "{{ asset('assets/img/User.png') }}",
                                scaledSize: new google.maps.Size(35, 35),
                            }
                        });

                        userMarker.addListener('click', () => {
                            map.panTo(userMarker.getPosition());
                            map.setZoom(19);
                        });


                    } else {
                        setTimeout(() => {
                            setReportingMap(null, 13, true, true);
                            showWarningMessage('Cannot get your current location.');
                        }, 5000);
                    }

                    navigator.geolocation.clearWatch(currentWatchID);
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
                            btnContainer.prop('hidden', 0);
                            message = 'Cannot get your current location.';
                            break;
                    }

                    setReportingMap(null, 13, null, true);
                    showWarningMessage(message);
                    navigator.geolocation.clearWatch(currentWatchID);
                }, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                }
            );
        }

        $(document).ready(() => {
            getCurrentPosition();

            $(document).on('click', '#submitReportBtn', () => {
                $('#reportAreaForm').validate({
                    rules: {
                        details: 'required',
                    },
                    messages: {
                        details: 'Please enter the details of the incident.',
                    },
                    errorElement: 'span',
                    showErrors() {
                        this.defaultShowErrors();

                        $('#image-error').text('Please select an image.')
                            .prop('style', `display: ${$('#inputImage').val() == '' ?
                                'block' : 'none'} !important`);
                    },
                    submitHandler(form) {
                        if ($('#inputImage').val() == '') return;

                        confirmModal('Are you sure you want to report this incident?').then((
                            result) => {
                            if (!result.isConfirmed) return;

                            $.ajax({
                                type: 'POST',
                                url: "{{ route('resident.incident.report') }}",
                                data: new FormData(form),
                                cache: false,
                                contentType: false,
                                processData: false,
                                beforeSend() {
                                    reportSubmitting = true;
                                    $('#defaultBtnText').hide();
                                    $('#loadingBtnText').prop('hidden', 0);
                                    $('textarea, #submitReportBtn, #imageBtn, #cancelReportingBtn')
                                        .prop('disabled', 1);
                                },
                                success(response) {
                                    const status = response.status;

                                    status == "warning" || status == "blocked" ?
                                        showWarningMessage(response.message) :
                                        showSuccessMessage(
                                            'Report submitted successfully.');

                                    status != "blocked" && (
                                        $('#cancelReportingBtn').prop('disabled', 0),
                                        $('#cancelReportingBtn').click(),
                                        $('.stop-btn-container').hide());
                                },
                                error: showErrorMessage,
                                complete() {
                                    reportSubmitting = false;
                                    $('#defaultBtnText').show();
                                    $('#loadingBtnText').prop('hidden', 1);
                                    $('textarea, #submitReportBtn, #imageBtn, #cancelReportingBtn')
                                        .prop('disabled', 0);
                                }
                            });
                        });
                    }
                });
            });

            $(document).on('click', '#retryGeolocation', function() {
                $('.stop-btn-container').hide();
                $('#loader').addClass('show');
                resetMapView();
                reportMarker?.setMap(null);
                getCurrentPosition();
                $(this).prop('disabled', 1);
            });

            $(document).on('click', '#cancelReportingBtn', function() {
                resetMapView();
                reportMarker.setMap(null);
                reportMarker = null;
                reportWindow = null;
                $('.stop-btn-container').hide();
            });
        });
    </script>
</body>

</html>
