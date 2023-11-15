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
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googleMap.key') }}&v=weekly" defer>
    </script>
    @include('partials.toastr')
    <script>
        let map, reportMarker, reportWindow, btnContainer = $('.page-button-container');

        function initMap(userLocation, zoom) {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: userLocation ? userLocation.lat : 14.246261,
                    lng: userLocation ? userLocation.lng : 121.12772
                },
                zoom: zoom,
                clickableIcons: false,
                draggableCursor: 'pointer',
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                },
                styles: localStorage.getItem('theme') == 'dark' ? mapDarkModeStyle : mapLightModeStyle
            });

            map.addListener("click", (event) => {
                let coordinates = event.latLng;

                if (reportMarker) {
                    reportMarker.setPosition(coordinates);
                    reportWindow.open(map, reportMarker);
                    $('[name="latitude"]').val(coordinates.lat());
                    $('[name="longitude"]').val(coordinates.lng());
                } else {
                    showInfoMessage('You can drag the marker to adjust the location.');

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
                                    <input type="file" name="image" class="form-control" id="areaInputImage" accept=".jpeg, .jpg, .png" hidden>
                                    <div class="info-window-action-container report-area">
                                        <button class="btn btn-sm btn-primary" id="imageBtn">
                                            <i class="bi bi-image"></i>
                                            Select
                                        </button>
                                    </div>
                                    <img id="selectedReportImage" src="" class="form-control" hidden>
                                    <span id="image-error" class="error" hidden>Please select an image file.</span>
                                </div>
                                <center>
                                    <button id="submitAreaBtn"><i class="bi bi-send"></i> Submit</button>
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
                        }
                    });

                    reportMarker.addListener('click', () => {
                        reportWindow.open(map, reportMarker);
                    });

                    reportWindow.open(map, reportMarker);

                    reportMarker.addListener('drag', () => {
                        reportWindow.close();
                    });

                    reportMarker.addListener('dragend', () => {
                        reportWindow.open(map, reportMarker);
                        $('[name="latitude"]').val(reportMarker.getPosition().lat());
                        $('[name="longitude"]').val(reportMarker.getPosition().lng());
                    });
                }
            });
        }

        function executeInitMap(userLocation, zoom = 13, geoLocation = false) {
            initMap(userLocation, zoom);
            $('#loader').removeClass('show');
            geoLocation && showInfoMessage('Click on the map to pinpoint the location of the incident.');
        }

        function getCurrentPosition() {
            let currentWatchID;

            currentWatchID = navigator.geolocation.watchPosition(
                (position) => {
                    console.log(position.coords.accuracy)

                    if (position.coords.accuracy <= 500) {
                        navigator.geolocation.clearWatch(currentWatchID);
                        currentWatchID = null;

                        const coords = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };

                        executeInitMap(coords, 18, true)

                        const userMarker = new google.maps.Marker({
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

                        btnContainer.prop('hidden', 1);
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
                        case error.POSITION_OUT_OF_BOUNDS:
                            btnContainer.prop('hidden', 0);
                            message = 'Cannot get your current location.';
                            break;
                    }
                    showWarningMessage(message);
                    navigator.geolocation.clearWatch(currentWatchID);
                    currentWatchID = null;
                    $('#retryGeolocation').prop('disabled', 0);
                    executeInitMap();
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        $(document).ready(() => {
            if (!navigator.geolocation) {
                executeInitMap();
            } else {
                getCurrentPosition();
            }

            $(document).on('click', '#submitAreaBtn', () => {
                $('#reportAreaForm').validate({
                    rules: {
                        details: 'required',
                    },
                    messages: {
                        details: 'Please enter the details of the incident.',
                    },
                    errorElement: 'span',
                    showErrors: function() {
                        this.defaultShowErrors();

                        $('#image-error').text('Please select an image.')
                            .prop('style', `display: ${$('#areaInputImage').val() == '' ?
                                'block' : 'none'} !important`);
                    },
                    submitHandler: function(form) {
                        if ($('#areaInputImage').val() == '') return;

                        confirmModal('Are you sure you want to report this incident?').then((
                            result) => {
                            if (!result.isConfirmed) return;

                            let formData = new FormData(form);

                            $.ajax({
                                type: 'POST',
                                url: "{{ route('resident.incident.report') }}",
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success(response) {
                                    const status = response.status;

                                    status == "warning" || status == "blocked" ?
                                        showWarningMessage(response.message) :
                                        showSuccessMessage(
                                            'Report submitted successfully.');

                                    status != "warning" && (reportMarker.setMap(
                                            null), reportMarker = null,
                                        reportWindow = null);
                                },
                                error: showErrorMessage
                            });
                        });
                    }
                });
            });

            $(document).on('click', '#retryGeolocation', () => {
                $(this).prop('disabled', 1);
                getCurrentPosition();
            });
        });
    </script>
</body>

</html>
