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
        <div class="main-content">
            <div class="label-container">
                <i class="bi bi-flag"></i>
                <span>MANAGE HAZARD REPORTS</span>
            </div>
            <hr>
            <div class="map-border">
                <div class="hazard-map" id="map"></div>
            </div>
            <div class="hazard-markers" hidden>
                <div class="markers-header">
                    <p>Marker Counts</p>
                </div>
                <div class="marker-container hazard">
                    <div class="count-container pending" hidden>
                        <img src="{{ asset('assets/img/reportMarker.png') }}">
                        <span id="pending-count"></span>
                    </div>

                    <div class="count-container flooded" hidden>
                        <img src="{{ asset('assets/img/floodedMarker.png') }}">
                        <span id="flooded-count"></span>
                    </div>
                    <div class="count-container roadblock" hidden>
                        <img src="{{ asset('assets/img/roadblock.png') }}">
                        <span id="roadblock-count"></span>
                    </div>
                </div>
            </div>
            @include('userpage.changePasswordModal')
        </div>
    </div>

    @include('partials.script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googleMap.key') }}&callback=initMap&v=weekly">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    @include('partials.toastr')
    <script type="text/javascript">
        let map, activeInfoWindow, id, url, reportMarkers = [];

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 14.246261,
                    lng: 121.12772
                },
                zoom: 13,
                zoomControl: false,
                clickableIcons: false,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                }
            });
        }

        function ajaxRequest() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('hazard.get') }}",
                    success: (response) => {
                        let status, picture, action, button,
                            counts = {
                                "Pending": 0,
                                "Flooded": 0,
                                "Roadblock": 0
                            };

                        while (reportMarkers.length) reportMarkers.pop().setMap(null);

                        if (response.length) {
                            response.forEach(report => {
                                status = report.status == "Pending";

                                if (status) {
                                    counts["Pending"] = (counts["Pending"] || 0) + 1;
                                    picture = "reportMarker";
                                    button = `<button class="btn btn-sm btn-success verifyBtn">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </button>`;
                                } else {
                                    counts[report.type] = (counts[report.type] || 0) + 1;
                                    picture = report.type == "Flooded" ? "floodedMarker" :
                                        "roadblock";
                                    button = `<button class="btn btn-sm btn-primary updateBtn">
                                            <i class="bi bi-chat-square-text-fill"></i>
                                        </button>`;
                                }

                                @if (auth()->user()->is_disable == 0)
                                    action = `<div class="info-description">
                                            <span>Actions</span>
                                            <hr class="info-window-hr">
                                            <div class="info-window-action-container">
                                                ${button}
                                                <button class="btn btn-sm btn-danger removeBtn">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                </button>
                                            </div>
                                        </div>`;
                                @endif

                                let reportMarker = new google.maps.Marker({
                                    position: {
                                        lat: parseFloat(report.latitude),
                                        lng: parseFloat(report.longitude)
                                    },
                                    map,
                                    icon: {
                                        url: "{{ asset('assets/img/picture.png') }}"
                                            .replace('picture', picture),
                                        scaledSize: new google.maps.Size(35, 35)
                                    }
                                });

                                reportMarkers.push(reportMarker);

                                const infoWindow = new google.maps.InfoWindow({
                                    content: `
                                    <div class="info-description">
                                        <span>Type:</span> ${report.type}
                                    </div>
                                    <div class="info-description status">
                                        <span>Status:</span>
                                        <span class="status-content bg-${status ? "warning" : "success"}">
                                            ${report.status}
                                        </span>
                                    </div>
                                    <div class="info-description update" ${!report.update && "hidden"}>
                                        <span>Update:</span> ${report.update}
                                    </div>
                                    <form class="reportUpdateForm" hidden>
                                        @csrf
                                        <p hidden>${report.id}</p>
                                        <p hidden>${report.update}</p>
                                        <div class="mx-1 mt-2">
                                            <textarea type="text" name="update" class="form-control"></textarea>
                                        </div>
                                        <center>
                                            <button class="sendUpdateBtn">Submit</button>
                                        </center>
                                    </form>
                                    ${action || ""}
                                    `
                                });

                                reportMarker.addListener('click', () => {
                                    activeInfoWindow?.close();
                                    activeInfoWindow = infoWindow;
                                    infoWindow.open(map, reportMarker);
                                });
                            });

                            for (const key in counts) {
                                if (counts[key] > 0) {
                                    $(`#${key.toLowerCase()}-count`).text(
                                        `${key.toUpperCase()} - ${counts[key]}`);
                                    $(`.${key.toLowerCase()}`).prop('hidden', false);
                                } else
                                    $(`.${key.toLowerCase()}`).prop('hidden', true);
                            }

                            $('.hazard-markers').prop('hidden', false);
                        } else {
                            showInfoMessage("No Hazard Reports", "Info");
                            $('.hazard-markers').prop('hidden', true);
                        }

                        resolve();
                    }
                });
            });
        }

        $(document).ready(() => {
            ajaxRequest();

            @if (auth()->user()->is_disable == 0)
                $(document).on('click', '.updateBtn', function() {
                    const updateForm = $(this).parent().parent().prev(),
                        updateDiv = updateForm.prev(),
                        isPrimary = $(this).hasClass('btn-primary');
                    const text = updateDiv ? updateDiv.text().split(':')[1]?.trim() : "";

                    updateDiv.prop('hidden', isPrimary || (!isPrimary && text == ""));
                    updateForm.prop('hidden', !isPrimary);
                    updateForm.find('textarea').val(text);
                    $(this).html(`<i class="bi bi-${isPrimary ? 'back' : 'chat-square-text-fill'}"></i>`)
                        .removeClass(isPrimary ? 'btn-primary' : 'btn-warning')
                        .addClass(isPrimary ? 'btn-warning' : 'btn-primary');
                });

                $(document).on('click', '.verifyBtn', function() {
                    confirmModal("Are you sure you want to approve this report?").then((result) => {
                        if (!result.isConfirmed) return;

                        submitHandler($(this), 'PATCH', 'verify',
                            "{{ route('hazard.verify', 'reportId') }}");
                    });
                });

                $(document).on('click', '.sendUpdateBtn', function() {
                    let form = $(this).parent().parent();

                    form.validate({
                        rules: {
                            update: 'required'
                        },
                        messages: {
                            update: 'Please enter update.'
                        },
                        errorElement: 'span',
                        submitHandler: function() {
                            confirmModal("Are you sure you want to add update to this report?")
                                .then((result) => {
                                    if (!result.isConfirmed) return;
                                    submitHandler(form, 'PATCH', 'update',
                                        "{{ route('hazard.update', 'reportId') }}");
                                });
                        }
                    });
                });

                $(document).on('click', '.removeBtn', function() {
                    confirmModal("Are you sure you want to remove this report?").then((result) => {
                        if (!result.isConfirmed) return;

                        submitHandler($(this), 'DELETE', 'remove',
                            "{{ route('hazard.remove', 'reportId') }}");
                    });
                });

                function submitHandler(element, type, operation, url) {
                    if (operation != 'update')
                        element = element.parent().parent().parent();

                    if (operation == "update" && element.find('p:last').text() == element.find('textarea').val())
                        return showWarningMessage("No changes were made.");

                    $.ajax({
                        type: type,
                        url: url.replace('reportId', element.find('p:first').text()),
                        data: element.serialize(),
                        success(response) {
                            response.status == "warning" ?
                                showWarningMessage(response.message) : showSuccessMessage(
                                    `Successfully ${operation == 'verify' ? 'verified' : operation == 'update' ? 'add update to' : 'removed'} the report.`
                                );
                        },
                        error() {
                            showErrorMessage();
                        }
                    });
                }

                // Echo.channel('hazard-report').listen('HazardReport', (e) => {
                //     ajaxRequest();
                // });
            @endif
        });
    </script>
</body>

</html>
