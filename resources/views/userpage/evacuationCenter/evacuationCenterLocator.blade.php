<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    {{-- @vite(['resources/js/app.js']) --}}
</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <div class="main-content">
            <div class="label-container">
                <i class="bi bi-search"></i>
                <span>EVACUATION CENTER LOCATOR</span>
            </div>
            <hr>
            <div class="locator-content">
                <div class="locator-header">
                    <div class="header-title"><span>Cabuyao City Map</span></div>
                </div>
                <div class="map-section">
                    <div class="locator-map" id="map"></div>
                </div>
            </div>
            <div class="evacuation-button-container">
                <div class="evacuation-markers">
                    <div class="markers-header">
                        <p>Markers</p>
                    </div>
                    <div class="marker-container">
                        <div class="markers">
                            <img src="{{ asset('assets/img/evacMarkerActive.png') }}" alt="Icon">
                            <span class="fw-bold">Active</span>
                        </div>
                        <div class="markers">
                            <img src="{{ asset('assets/img/evacMarkerInactive.png') }}" alt="Icon">
                            <span class="fw-bold">Inactive</span>
                        </div>
                        <div class="markers">
                            <img src="{{ asset('assets/img/evacMarkerFull.png') }}" alt="Icon">
                            <span class="fw-bold">Full</span>
                        </div>
                        <div class="markers" id="flood-marker">
                            <img src="{{ asset('assets/img/floodedMarker.png') }}" alt="Icon">
                            <span class="fw-bold">Flooded</span>
                        </div>
                        <div class="markers" id="roadblock-marker">
                            <img src="{{ asset('assets/img/roadBlock.png') }}" alt="Icon">
                            <span class="fw-bold">Roadblock</span>
                        </div>
                        <div class="markers" id="user-marker" hidden>
                            <img src="{{ asset('assets/img/userMarker.png') }}" alt="Icon">
                            <span class="fw-bold">You</span>
                        </div>
                    </div>
                </div>
                <div class="locator-button-container">
                    <button type="button" id="locateNearestBtn" disabled>
                        <i class="bi bi-search"></i>Locate Nearest Active Evacuation</button>
                    <button type="button" id="pinpointCurrentLocationBtn">
                        <i class="bi bi-geo-fill"></i>Pinpoint Current Location</button>
                </div>
            </div>
            <div class="table-container">
                <div class="table-content">
                    <header class="table-label">Evacuation Centers Table</header>
                    <table class="table" id="evacuationCenterTable" width="100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Barangay</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Capacity</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        @include('userpage.changePasswordModal')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    @include('partials.script')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googleMap.key') }}&callback=initMap&v=weekly"
        defer></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    @include('partials.toastr')
    <script>
        let map, activeInfoWindow, userMarker, userBounds, directionDisplay, evacuationCentersData, rowData,
            prevNearestEvacuationCenter, evacuationCenterTable, findNearestActive, reportMarker, reportWindow,
            watchId = null,
            locating = false,
            geolocationBlocked = false,
            reportButtonClicked = false,
            hasActiveEvacuationCenter = false,
            evacuationCenterJson = [],
            evacuationCenterMarkers = [],
            hazardMarkers = [],
            activeEvacuationCenters = [];

        const options = {
            enableHighAccuracy: true
        };

        const errorCallback = () => {
            showWarningMessage(
                'Request for geolocation denied. To use this feature, please allow the browser to locate you.'
            );
            $('#locateNearestBtn').removeAttr('disabled');
            locating = false;
            geolocationBlocked = true;
        };

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 14.246261,
                    lng: 121.12772
                },
                zoom: 13,
                zoomControl: false,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                }
            });

            directionDisplay = new google.maps.DirectionsRenderer({
                map,
                suppressMarkers: true,
                preserveViewport: true,
                markerOptions: {
                    icon: {
                        url: "{{ asset('assets/img/userMarker.png') }}",
                        scaledSize: new google.maps.Size(35, 35)
                    }
                }
            });

            const stopBtnContainer = document.createElement('div');
            stopBtnContainer.className = 'stop-btn-container';
            stopBtnContainer.innerHTML =
                `<button id="stopLocatingBtn" class="btn-remove">
                    <i class="bi bi-stop-circle"></i>Stop Locating
                </button>`;
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(stopBtnContainer);

            if ('{{ $prefix }}' == 'resident') {
                const reportBtnContainer = document.createElement('div');
                reportBtnContainer.className = 'report-btn-container';
                reportBtnContainer.innerHTML =
                    `<button id="reportHazardBtn" class="btn-update">
                        <i class="bi bi-exclamation-circle-fill"></i>Report Hazard Area
                    </button>`;
                map.controls[google.maps.ControlPosition.BOTTOM_RIGHT].push(reportBtnContainer);
            }
        }

        function initMarkers(markersData, type, markersArray) {
            while (markersArray.length) markersArray.pop().setMap(null);

            markersData.forEach((data) => {
                let picture = type == "evacuationCenter" ?
                    data.status == "Active" ? "evacMarkerActive" :
                    data.status == "Inactive" ? "evacMarkerInactive" :
                    "evacMarkerFull" :
                    data.type == "Flooded" ? "floodedMarker" :
                    "roadBlock";

                let marker = generateMarker({
                    lat: parseFloat(data.latitude),
                    lng: parseFloat(data.longitude)
                }, "{{ asset('assets/img/picture.png') }}".replace('picture', picture));

                markersArray.push(marker);

                let content = type == "evacuationCenter" ?
                    `<div class="info-description">
                            <span>Name:</span> ${data.name}
                    </div>
                    <div class="info-description">
                        <span>Barangay:</span> ${data.barangay_name}
                    </div>
                    <div class="info-description">
                        <span>Capacity:</span> ${data.capacity}
                    </div>
                    <div class="info-description status">
                        <span>Status:</span>
                        <span class="status-content bg-${getStatusColor(data.status)}">
                            ${data.status}
                        </span>
                    </div>` :
                    `<div class="info-description">
                        <span>Type:</span> ${data.type}
                    </div>
                    <div class="info-description update" ${!data.update && "hidden"}>
                        <span>Update:</span> ${data.update}
                    </div>`;

                generateInfoWindow(marker, content);
            });
        }

        function generateInfoWindow(marker, content) {
            if (!locating) closeInfoWindow();

            const infoWindow = new google.maps.InfoWindow({
                content
            });

            marker.addListener('click', () => {
                if (!marker.icon.url.includes('reportMarker')) {
                    closeInfoWindow();
                    activeInfoWindow = infoWindow;
                }

                if (marker.icon.url.includes('userMarker'))
                    zoomToUserLocation();

                openInfoWindow(infoWindow, marker);
            });

            if (marker.icon.url.includes('reportMarker')) {
                reportMarker = marker;
                reportWindow = infoWindow;
                openInfoWindow(infoWindow, marker);
            }
        }

        function generateMarker(position, icon) {
            return new google.maps.Marker({
                position,
                map,
                icon: {
                    url: icon,
                    scaledSize: new google.maps.Size(35, 35)
                }
            });
        }

        function generateCircle(center) {
            return new google.maps.Circle({
                map,
                center,
                radius: 14,
                fillColor: "#557ed8",
                fillOpacity: 0.3,
                strokeColor: "#557ed8",
                strokeOpacity: 0.8,
                strokeWeight: 2
            });
        }

        function openInfoWindow(infoWindow, marker) {
            infoWindow.open(map, marker);
        }

        function request(origin, destination) {
            return {
                origin,
                destination,
                travelMode: google.maps.TravelMode.WALKING
            };
        }

        function getStatusColor(status) {
            return status == 'Active' ? 'success' : status == 'Inactive' ? 'danger' : 'warning';
        }

        function getKilometers(response) {
            return (response.routes[0].legs[0].distance.value / 1000).toFixed(2);
        }

        function newLatLng(lat, lng) {
            return new google.maps.LatLng(lat, lng);
        }

        function scrollMarkers() {
            $('#user-marker').prop('hidden', false);
            $('.evacuation-markers').animate({
                scrollLeft: $('#user-marker').position().left + $('.evacuation-markers').scrollLeft()
            }, 500);
        }

        function scrollToMap() {
            $('html, body').animate({
                scrollTop: $('.locator-content').offset().top - 15
            }, 500);
        }

        function zoomToUserLocation() {
            map.panTo(userMarker.getPosition());
            map.setZoom(18);
        }

        function closeInfoWindow() {
            activeInfoWindow?.close();
        }

        function getUserLocation(locating = false) {
            return new Promise((resolve, reject) => {
                if (!navigator.geolocation) {
                    showInfoMessage('Geolocation is not supported by this browser.');
                    $('#locateNearestBtn').removeAttr('disabled');
                    return;
                }

                navigator.geolocation.getCurrentPosition((position) => {
                    position.coords.accuracy <= 250 ?
                        (geolocationBlocked = false, resolve(position)) :
                        getUserLocation();
                }, errorCallback, options);
            });
        }

        function setMarker(userlocation) {
            userMarker ?
                (userMarker.setMap(map),
                    userBounds.setMap(map),
                    userMarker.setPosition(userlocation),
                    userBounds.setCenter(userMarker.getPosition())) :
                (userMarker = generateMarker(userlocation,
                        "{{ asset('assets/img/userMarker.png') }}"),
                    userBounds = generateCircle(userMarker.getPosition()));
        }

        async function getEvacuationCentersDistance() {
            $('#locateNearestBtn').attr('disabled', true);
            evacuationCenterJson.length = 0;
            activeEvacuationCenters.length = 0;

            for (const data of evacuationCentersData) {
                if (data.status != 'Inactive') {
                    activeEvacuationCenters.push(data);
                }
            }

            if (activeEvacuationCenters.length == 0) {
                hasActiveEvacuationCenter = false;
                if (locating && findNearestActive) {
                    $('#stopLocatingBtn').click();
                    showWarningMessage('There are currently no active evacuation centers.');
                }
            } else {
                hasActiveEvacuationCenter = true;
                if (!geolocationBlocked) {
                    const position = await getUserLocation();
                    const promises = activeEvacuationCenters.map(data => {
                        return new Promise(resolve => {
                            const direction = new google.maps.DirectionsService();
                            direction.route(request(
                                    newLatLng(position.coords.latitude, position.coords
                                        .longitude),
                                    newLatLng(data.latitude, data.longitude)),
                                (response, status) => {
                                    if (status == 'OK') {
                                        evacuationCenterJson.push({
                                            id: data.id,
                                            status: data.status,
                                            latitude: data.latitude,
                                            longitude: data.longitude,
                                            distance: parseFloat(getKilometers(response))
                                        });
                                        resolve();
                                    }
                                }
                            );
                        });
                    });

                    await Promise.all(promises);
                    if (evacuationCenterJson.length > 1) {
                        const unique = new Set();
                        evacuationCenterJson = evacuationCenterJson
                            .filter(({
                                id,
                                latitude,
                                longitude
                            }) => {
                                const identifier = `${id}-${latitude}-${longitude}`;
                                return unique.has(identifier) ? false : unique.add(identifier);
                            })
                            .sort((a, b) => a.distance - b.distance);
                    }
                }
            }

            $('#locateNearestBtn').removeAttr('disabled');
        }

        function locateEvacuationCenter() {
            watchId = navigator.geolocation.watchPosition(async (position) => {
                geolocationBlocked = false;

                if (position.coords.accuracy <= 250) {
                    if (findNearestActive && evacuationCenterJson.length == 0) {
                        await getEvacuationCentersDistance();
                        if (!hasActiveEvacuationCenter) return;
                    }

                    const {
                        latitude,
                        longitude
                    } = findNearestActive ?
                        evacuationCenterJson[0] : rowData;

                    const directionService = new google.maps.DirectionsService();

                    directionService.route(
                        request(
                            newLatLng(position.coords.latitude, position.coords.longitude),
                            newLatLng(latitude, longitude)
                        ),
                        function(response, status) {
                            if (status == 'OK' && locating) {
                                setMarker(response.routes[0].legs[0].start_location);
                                generateInfoWindow(
                                    userMarker,
                                    `<div class="info-window-container">
                                        <center>You are here.</center>
                                        <div class="info-description">
                                            <span>Pathway distance to evacuation: </span>
                                            ${getKilometers(
                                                response
                                            )} km
                                        </div>
                                    </div>`
                                );

                                if ($('.stop-btn-container').is(':hidden')) {
                                    directionDisplay.setMap(map);
                                    scrollToMap();
                                    var bounds = new google.maps.LatLngBounds();
                                    response.routes[0].legs.forEach(({
                                            steps
                                        }) =>
                                        steps.forEach(({
                                                start_location,
                                                end_location
                                            }) =>
                                            (bounds.extend(start_location), bounds.extend(
                                                end_location))
                                        )
                                    );
                                    map.fitBounds(bounds);
                                    $('.stop-btn-container').show();
                                    scrollMarkers();
                                }

                                directionDisplay.setDirections(response);
                                if (findNearestActive)
                                    prevNearestEvacuationCenter = evacuationCenterJson[0];
                            }
                        }
                    );
                }
            }, errorCallback, options);
        }

        function ajaxRequest(type = "evacuationCenter") {
            let url = type == "reportHazard" ?
                '{{ $prefix }}' == 'resident' ?
                "{{ route('resident.hazard.get') }}" :
                "{{ route('cswd.hazard.get') }}" :
                '{{ $prefix }}' == 'resident' ?
                "{{ route('resident.evacuation.center.get', 'locator') }}" :
                "{{ route('evacuation.center.get', 'locator') }}";

            return new Promise((resolve, reject) => {
                $.ajax({
                    method: 'GET',
                    url: url,
                    success: (response) => {
                        let data = response;

                        console.log(data)

                        if (type == "evacuationCenter") {
                            data = data.data;
                            evacuationCentersData = data;
                            getEvacuationCentersDistance();
                        }

                        initMarkers(data, type, type == "evacuationCenter" ?
                            evacuationCenterMarkers : hazardMarkers);
                        resolve();
                    }
                });
            });
        }

        $(document).ready(() => {
            ajaxRequest('reportHazard');
            ajaxRequest().then(() => {
                evacuationCenterTable = $('#evacuationCenterTable').DataTable({
                    language: {
                        emptyTable: '<div class="message-text">There are currently no evacuation centers available.</div>'
                    },
                    ordering: false,
                    responsive: true,
                    data: evacuationCentersData,
                    columns: [{
                            data: 'id',
                            name: 'id',
                            visible: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'barangay_name',
                            name: 'barangay_name'
                        },
                        {
                            data: 'latitude',
                            name: 'latitude',
                            visible: false
                        },
                        {
                            data: 'longitude',
                            name: 'longitude',
                            visible: false
                        },
                        {
                            data: 'capacity',
                            name: 'capacity',
                            width: '1rem',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
                            width: '15%'
                        },
                        {
                            data: 'action',
                            width: '1rem',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: [{
                        targets: 6,
                        render: function(data) {
                            return `<div class="status-container">
                                    <div class="status-content bg-${getStatusColor(data)}">
                                        ${data}
                                    </div>
                                </div>`;
                        }
                    }]
                });
            });

            $(document).on("click", "#pinpointCurrentLocationBtn", function() {
                if (!locating)
                    getUserLocation().then((position) => {
                        setMarker(newLatLng(position.coords.latitude, position.coords.longitude));
                        generateInfoWindow(userMarker,
                            `<div class="info-window-container">
                                <div class="info-description">
                                    <center>You are here.</center>
                                </div>
                            </div>`);
                        scrollToMap();
                        zoomToUserLocation();
                        scrollMarkers();
                    });
            });

            $(document).on("click", "#locateNearestBtn, .locateEvacuationCenter", function() {
                if (!locating) {
                    findNearestActive = !$(this).hasClass('locateEvacuationCenter');
                    rowData = findNearestActive ? null : getRowData(this, evacuationCenterTable);
                    locating = true;
                    locateEvacuationCenter();
                }
            });

            $(document).on("click", "#stopLocatingBtn", function() {
                locating = false;
                watchId && (navigator.geolocation.clearWatch(watchId), watchId = null);
                directionDisplay?.setMap(null);
                userMarker?.setMap(null);
                userBounds?.setMap(null);
                closeInfoWindow();
                $('.stop-btn-container').hide();
                map.setCenter(newLatLng(14.246261, 121.12772));
                map.setZoom(13);
                $('#user-marker').prop('hidden', true);
            });

            $(document).on("click", "#reportHazardBtn", function() {
                if (this.textContent == 'Report Hazard Area' || !reportButtonClicked) {
                    map.setOptions({
                        draggableCursor: 'pointer'
                    });
                    showInfoMessage(
                        'Click on the map to pinpoint the location of the hazard. Click the button again to cancel.'
                    );
                    $('#reportHazardBtn').html(
                        '<i class="bi bi-stop-circle"></i>Cancel Reporting'
                    ).addClass('btn-remove');
                    google.maps.event.addListener(map, 'click', function(event) {
                        const coordinates = event.latLng;

                        if (reportMarker) {
                            reportMarker.setPosition(coordinates);
                            openInfoWindow(reportWindow, reportMarker);
                            $('[name="latitude"]').val(coordinates.lat());
                            $('[name="longitude"]').val(coordinates.lng());
                        } else {
                            generateInfoWindow(
                                generateMarker(
                                    coordinates, "{{ asset('assets/img/reportMarker.png') }}"
                                ),
                                `<form id="reportHazardForm">
                                    @csrf
                                    <input type="text" name="latitude" value="${coordinates.lat()}" hidden>
                                    <input type="text" name="longitude" value="${coordinates.lng()}" hidden>
                                    <div class="mx-1">
                                        <label>Report Type</label>
                                        <select name="type" class="form-select">
                                            <option value="" hidden selected disabled>Select Report Type</option>
                                            <option value="Flooded">Flooded</option>
                                            <option value="Roadblock">Roadblock</option>
                                        </select>
                                        <center>
                                            <button id="reportBtn">Submit</button>
                                        <center>
                                    </div>
                                </form>`
                            );
                        }
                    });
                } else {
                    map.setOptions({
                        draggableCursor: 'default'
                    });
                    $('#reportHazardBtn').html(
                        '<i class="bi bi-exclamation-circle-fill"></i>Report Hazard Area'
                    ).removeClass('btn-remove');
                    reportMarker?.setMap(null);
                    reportMarker = null;
                    google.maps.event.clearListeners(map, 'click');
                }

                if (!reportButtonClicked) reportButtonClicked = true;
            });

            $(document).on('click', '#reportBtn', function() {
                $('#reportHazardForm').validate({
                    rules: {
                        type: 'required'
                    },
                    messages: {
                        type: 'Please select report type.'
                    },
                    errorElement: 'span',
                    submitHandler: function(form) {
                        confirmModal('Are you sure you want to report this area?').then((
                            result) => {
                            if (!result.isConfirmed) return;

                            $.post("{{ route('resident.hazard.report') }}", $(form)
                                .serialize(),
                                function(response) {
                                    if (response.status == "warning")
                                        showErrorMessage(response.message);
                                    else {
                                        showSuccessMessage(
                                            'Report submitted successfully.');
                                        $('#reportHazardBtn').click();
                                    }
                                }).fail(showErrorMessage);
                        });
                    }
                });
            });

            // Echo.channel('hazard-report').listen('HazardReport', (e) => {
            //     ajaxRequest('reportHazard');
            // });

            // Echo.channel('evacuation-center-locator').listen('EvacuationCenterLocator', (e) => {
            //     ajaxRequest().then(() => {
            //         if (locating && (rowData != null || prevNearestEvacuationCenter != null)) {
            //             const {
            //                 id,
            //                 status,
            //                 latitude,
            //                 longitude
            //             } = findNearestActive ? prevNearestEvacuationCenter : rowData;

            //             const isCenterUnavailable = findNearestActive ?
            //                 !evacuationCentersData.some(evacuationCenter =>
            //                     evacuationCenter.id == id && ['Active', 'Full'].includes(
            //                         evacuationCenter.status)) :
            //                 !evacuationCentersData.some(evacuationCenter =>
            //                     evacuationCenter.id == id),

            //                 isLocationUpdated = !evacuationCentersData.some(
            //                     evacuationCenter =>
            //                     evacuationCenter.latitude == latitude &&
            //                     evacuationCenter.longitude == longitude);

            //             if (isCenterUnavailable || isLocationUpdated) {
            //                 $('#stopLocatingBtn').click();
            //                 showWarningMessage(
            //                     isCenterUnavailable ?
            //                     'The evacuation center you are locating is no longer available.' :
            //                     'The location of the evacuation center you are locating is updated.'
            //                 );

            //                 if (findNearestActive) prevNearestEvacuationCenter = null;
            //             }
            //         }

            //         evacuationCenterTable.clear().rows.add(evacuationCentersData).draw();
            //     });
            // });
        });
    </script>
</body>

</html>
