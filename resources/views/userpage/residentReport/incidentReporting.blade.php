<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    {{-- @vite(['resources/js/app.js']) --}}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        let map, reportMarker, reportWindow, timeout = false;

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
                styles: sessionStorage.getItem('theme') == 'dark' ? mapDarkModeStyle : mapLightModeStyle
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
                                            <i class="bi bi-image-fill"></i>
                                            Select
                                        </button>
                                    </div>
                                    <img id="selectedAreaImage" src="" class="form-control" hidden>
                                    <span id="image-error" class="error" hidden>Please select an image file.</span>
                                </div>
                                <center>
                                    <button id="submitAreaBtn"><i class="bi bi-send-fill"></i> Submit</button>
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
            geoLocation ?
                showInfoMessage('Click on the map to pinpoint the location of the incident.') :
                showWarningMessage(
                    `${timeout ? 'Geolocation service failed' :
                    'Your browser lacks geolocation support or you\'ve denied access'}. Therefore, you need to manually navigate to your location.`
                );
        }

        $(document).ready(() => {
            if (!navigator.geolocation) {
                executeInitMap();
            } else {
                navigator.geolocation.getCurrentPosition(
                    ({
                        coords
                    }) => executeInitMap({
                        lat: coords.latitude,
                        lng: coords.longitude
                    }, 18, true),
                    (error) => {
                        if (error.code === error.TIMEOUT) timeout = true;
                        executeInitMap();
                    }, {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
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

            $(document).on('click', '#imageBtn', () => {
                event.preventDefault();
                $('#areaInputImage').click();
            });

            $(document).on('change', '#areaInputImage', function() {
                if (this.files && this.files[0]) {
                    let reader = new FileReader(),
                        container = $(this).closest('.gm-style-iw-d'),
                        imageBtn = $('#imageBtn'),
                        imageErr = $('#image-error'),
                        selectedAreaImg = $('#selectedAreaImage');

                    if (!['image/jpeg', 'image/jpg', 'image/png'].includes(this.files[0].type)) {
                        $('#areaInputImage').val('');
                        selectedAreaImg.attr('src', '').attr('hidden', true);
                        imageBtn.html('<i class="bi bi-image-fill"></i> Select');
                        setInfoWindowButtonStyles(imageBtn, 'var(--color-primary');
                        imageErr.prop('style', 'display: block !important');
                        return;
                    } else
                        imageErr.prop('style', 'display: none !important');

                    reader.onload = function(e) {
                        selectedAreaImg.attr('src', e.target.result);
                    };
                    reader.readAsDataURL(this.files[0]);
                    imageBtn.html('<i class="bi bi-arrow-repeat"></i> Change');
                    setInfoWindowButtonStyles(imageBtn, 'var(--color-yellow');
                    selectedAreaImg.attr('hidden', false);
                    container.animate({
                        scrollTop: container.prop('scrollHeight')
                    }, 500);
                }
            });
        });
    </script>
