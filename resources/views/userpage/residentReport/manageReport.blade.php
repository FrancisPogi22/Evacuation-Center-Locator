<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    @if ($operation == 'archived')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    @endif
</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <div class="main-content">
            <div class="label-container">
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-{{ $operation == 'manage' ? 'flag' : 'journal-bookmark-fill' }}"></i>
                    </div>
                </div>
                <span>{{ strtoupper($operation) }} REPORT</span>
            </div>
            <hr>
            @if ($operation == 'manage')
                <div class="map-border">
                    <div class="reporting-map" id="map"></div>
                </div>
                <div class="report-markers" hidden>
                    <div class="markers-header">
                        <p>Markers</p>
                    </div>
                    <div class="marker-container reports">
                        <div class="count-container emergency" hidden>
                            <img src="{{ asset('assets/img/Emergency.png') }}">
                            <span id="emergency-count"></span>
                        </div>
                        <div class="count-container incident" hidden>
                            <img src="{{ asset('assets/img/Incident.png') }}">
                            <span id="incident-count"></span>
                        </div>
                        <div class="count-container flooded" hidden>
                            <img src="{{ asset('assets/img/Flooded.png') }}">
                            <span id="flooded-count"></span>
                        </div>
                        <div class="count-container roadblocked" hidden>
                            <img src="{{ asset('assets/img/Roadblocked.png') }}">
                            <span id="roadblocked-count"></span>
                        </div>
                    </div>
                </div>
                @include('userpage.residentReport.archivedReportModal')
            @else
                @if (!$yearList->isEmpty())
                    <div class="page-button-container manage-evacuee">
                        <select id="changeYearSelect" class="form-control form-select">
                            @foreach ($yearList as $year)
                                <option value="{{ $year->year }}">
                                    {{ $year->year }}</option>
                            @endforeach
                        </select>
                        <select id="changeArchivedReportType" class="form-control form-select">
                            @foreach ($reportType as $type)
                                <option value="{{ $type }}">
                                    {{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <section class="table-container">
                    <div class="table-content">
                        <header class="table-label">Archived Report Table</header>
                        <table class="table" id="reportTable" width="100%">
                            <thead>
                                <tr>
                                    <th colspan="2">Details</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Photo</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </section>
                <div class="modal fade" id="locationModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <header class="modal-label-container">
                                <h1 class="modal-label">Report Location</h1>
                            </header>
                            <div class="modal-body">
                                <div class="map-border">
                                    <div class="form-map" id="map"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @include('userpage.changePasswordModal')
        </div>
    </div>

    @include('partials.script')
    <script defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googleMap.key') }}&callback=initMap&v=weekly">
    </script>
    @if ($operation == 'archived')
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    @include('partials.toastr')
    <script type="text/javascript">
        let map;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 14.246261,
                    lng: 121.12772
                },
                zoom: 13,
                clickableIcons: false,
                draggableCursor: 'pointer',
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                }
            });
        }

        @if ($operation == 'manage')
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let activeInfoWindow, id, url, emergencyArchiveBtn,
                noReport = [
                    false,
                    false,
                    false
                ],
                reportMarkers = [
                    [],
                    [],
                    []
                ];

            function ajaxRequest(type) {
                const condition = type == "Incident" ? 0 : type == "Emergency" ? 1 : 2,
                    url = type == "Incident" ?
                    "{{ route('incident.get', ['manage', 'null', 'null']) }}" :
                    type == "Emergency" ?
                    "{{ route('emergency.get', ['manage', 'null', 'null']) }}" :
                    "{{ route('area.get', ['manage', 'null', 'null']) }}";

                return new Promise((resolve, reject) => {
                    $.ajax({
                        method: 'GET',
                        url: url,
                        success(response) {
                            let isPending, status, ariaType, button, updateSection,
                                action = "",
                                counts = condition == 0 ? {
                                    "Incident": 0
                                } : condition == 1 ? {
                                    "Emergency": 0
                                } : {
                                    "Flooded": 0,
                                    "Roadblocked": 0
                                };

                            while (reportMarkers[condition].length)
                                reportMarkers[condition].pop().setMap(null);

                            if (response.length) {
                                response.forEach(report => {
                                    type = report.type;
                                    status = report.status;
                                    isPending = status == "Pending";
                                    ariaType = condition != 2 ? type : "Area";
                                    counts[type] = counts[type] + 1;

                                    approveBtn = `<button class="btn btn-sm btn-success approveBtn" aria-report-type="${ariaType}">
                                        <i class="bi bi-${ condition != 2 ? (isPending ? 'bookmark-plus' : 'bookmark-check') : 'check-circle' }"></i>
                                            ${condition != 2 ? `Set as ${condition == 1 ? 'Rescu' : 'Resolv'}${isPending ? 'ing' : 'ed'}` : "Approve"}
                                        </button>`;

                                    @if (auth()->user()->is_disable == 0)
                                        action = `<div class="info-description">
                                                <span>Actions</span>
                                                <hr class="info-window-hr">
                                                <div class="info-window-action-container">
                                                    <div class="status" hidden>
                                                        <p>${report.id}</p>
                                                    </div>
                                                    ${isPending ? `${approveBtn}
                                                    <button class="btn btn-sm btn-danger removeBtn" aria-report-type="${ariaType}">
                                                        <i class="bi bi-x-circle"></i> Remove
                                                    </button>` :
                                                    `${condition != 2 ? (status == "Resolved" || status == "Rescued") ?
                                                    "" : approveBtn :
                                                    `<button class="btn btn-sm btn-primary updateBtn">
                                                        <i class="bi bi-chat-square-text"></i> Update
                                                    </button>`}
                                                    ${(status == "Approved" || status == "Resolved" || status == "Rescued") ?
                                                    `<button class="btn btn-sm btn-danger
                                                        archive${condition == 1 && status == "Rescued" ? "Emergency" : ""}Btn"
                                                        aria-report-type="${ariaType}">
                                                        <i class="bi bi-box-arrow-in-down-right"></i> Archive
                                                    </button>` : ''}`}
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
                                                .replace('picture', type),
                                            scaledSize: new google.maps.Size(35, 35)
                                        },
                                        animation: isPending ?
                                            google.maps.Animation.BOUNCE : null
                                    });

                                    reportMarker.addListener('click', () => {
                                        if (map.getZoom() < 15) {
                                            map.setZoom(18)
                                            map.panTo(reportMarker.getPosition());
                                        }
                                    });

                                    reportMarkers[condition].push(reportMarker);

                                    updateSection = condition == 2 ?
                                        `<div class="info-description update" ${report.update.length == 0 && ("hidden")}>
                                                <span>Updates: </span>
                                                <div class="info-window-update-container">
                                                    ${
                                                        pastDate = "",
                                                        report.update.map((update) => {
                                                            let currentDate = formatDateTime(update.update_time, 'date');
                                                            let dateOutput = currentDate !== pastDate ? `<div class="update-date">${formatDateTime(update.update_time, 'date')}</div>` : '';
                                                            pastDate = currentDate;

                                                            return `${dateOutput}
                                                                <p class="update-details-container">
                                                                    <small>
                                                                        as of ${formatDateTime(update.update_time, 'time')}
                                                                    </small><br>
                                                                    <span class="update-details">
                                                                        ${update.update_details}
                                                                    </span>
                                                                </p>`;
                                                        }).join('')
                                                    }
                                                </div>
                                            </div>
                                            <form class="reportUpdateForm" hidden>
                                                @csrf
                                                <p hidden>${report.id}</p>
                                                <div class="mx-2 mt-2">
                                                    <label>Add New Update</label>
                                                    <textarea type="text" name="update" class="form-control" cols="50" rows="10"></textarea>
                                                </div>
                                                <center>
                                                    <button class="sendUpdateBtn"><i class="bi bi-send"></i> Submit</button>
                                                </center>
                                            </form>` : '';

                                    const infoWindow = new google.maps.InfoWindow({
                                        content: `
                                        <div class="reportContainer">
                                            <div class="info-description">
                                            <span>Report Date:</span>
                                                ${formatDateTime(report.report_time)}
                                            </div>
                                            <div class="info-description">
                                                <span>Report Type: </span>
                                                <medium>
                                                    ${type}
                                                </medium>
                                            </div>
                                            <div class="info-description status">
                                                <span>Status:</span>
                                                <span class="status-content bg-${
                                                    isPending ?
                                                        "warning" : (status == "Resolving" || status == "Rescuing") ? "primary" : "success"
                                                }">
                                                    ${report.status}
                                                </span>
                                            </div>
                                            <div class="info-description details" ${report.details || "hidden"}>
                                                <span>Details: </span>
                                                <div class="info-window-details-container">
                                                    ${report.details}
                                                </div>
                                            </div>
                                            ${report.photo ?
                                                `<div class="info-description photo">
                                                    <span>Image: </span>
                                                    <div class="${type}" hidden>
                                                        ${report.latitude}, ${report.longitude}
                                                    </div>
                                                    <button class="btn btn-sm btn-primary toggleImageBtn">
                                                        <i class="bi bi-chevron-expand"></i> View
                                                    </button>
                                                    <img src="/reports_image/${report.photo}" class="form-control" hidden>
                                                </div>` : ""}
                                            ${updateSection}
                                            ${action}
                                        </div>
                                        `
                                    });

                                    reportMarker.addListener('click', () => {
                                        activeInfoWindow?.close();
                                        activeInfoWindow = infoWindow;
                                        infoWindow.open(map, reportMarker);
                                    });
                                });

                                noReport[condition] = false;
                            } else
                                noReport[condition] = true;

                            for (const key in counts) {
                                let markerLength = 0;

                                if (key == "Incident")
                                    markerLength = reportMarkers[0].length;
                                else if (key == "Emergency")
                                    markerLength = reportMarkers[1].length;
                                else
                                    markerLength = reportMarkers[2].filter(
                                        marker => marker
                                        .getIcon()
                                        .url.includes(key)
                                    ).length;

                                const element = $(`.${key.toLowerCase()}`);
                                if (counts[key] > 0 && markerLength > 0) {
                                    $(`#${key.toLowerCase()}-count`).text(
                                        `${key.toUpperCase()} - ${counts[key]}`);
                                    element.prop('hidden', 0);
                                } else {
                                    element.prop('hidden', 1);
                                }
                            }

                            resolve();
                        }
                    });
                });
            }

            function checkNoReports(type) {
                let hide = true;

                if (noReport[0] && noReport[1] && noReport[2])
                    showInfoMessage('No resident reports to manage.');
                else {
                    hide = false;
                    if (noReport[0] && (type == "All" || type == "Incident")) showInfoMessage('No incident report.');
                    if (noReport[1] && (type == "All" || type == "Emergency")) showInfoMessage('No emergency report.');
                    if (noReport[2] && (type == "All" || type == "Area")) showInfoMessage('No area report.');
                }

                $('.report-markers').prop('hidden', hide);
            }
        @else
            '{{ !$yearList->isEmpty() }}' ?
            sessionStorage.setItem('archiveReportYear', $('#changeYearSelect option:first').val()):
                sessionStorage.setItem('archiveReportYear', '-1');

            '{{ !$yearList->isEmpty() }}' ?
            sessionStorage.setItem('archiveReportType', $('#changeArchivedReportType option:first').val()):
                sessionStorage.setItem('archiveReportType', 'Null');

            $('#changeYearSelect').val(sessionStorage.getItem('archiveReportYear'));
            $('#changeArchivedReportType').val(sessionStorage.getItem('archiveReportType'));

            let reportTable, reportMarker
            url = "{{ route('resident.report.get', 'year') }}"
                .replace('year', sessionStorage.getItem('archiveReportYear'));
        @endif

        $(document).ready(() => {
            @if ($operation == 'manage')
                let requestPromises = [
                        ajaxRequest("Emergency"),
                        ajaxRequest("Incident"),
                        ajaxRequest("Area")
                    ],
                    modal = $('#archivedReportModal'),
                    archiveFormValidator;

                Promise.all(requestPromises)
                    .then(() => {
                        checkNoReports('All');

                        let type = sessionStorage.getItem('report_type'),
                            lat = sessionStorage.getItem('report_latitude'),
                            lng = sessionStorage.getItem('report_longitude');

                        if (type != undefined && type != 'null') openReportDetails(type, lat, lng);
                    });

                @if (auth()->user()->is_disable == 0)
                    $(document).on('click', '.toggleImageBtn', function() {
                        let markers = $(this).prev().attr("class") == "Incident" ?
                            reportMarkers[0] : reportMarkers[2];
                        toggleShowImageBtn($(this), $(this).next(), markers);
                    });

                    $(document).on('click', '.updateBtn', function() {
                        if ($(this).text().includes('Update')) {
                            const container = $(this).closest('.gm-style-iw-d');
                            container.animate({
                                scrollTop: container.prop('scrollHeight')
                            }, 800);
                        }

                        const updateForm = $(this).parent().parent().prev(),
                            updateDiv = updateForm.prev(),
                            isPrimary = this.textContent.includes('Update'),
                            text = updateDiv ? updateDiv.text().split(':')[1]?.trim() : "";

                        updateDiv.prop('hidden', isPrimary || (!isPrimary && text == ""));
                        updateForm.prop('hidden', !isPrimary);
                        updateForm.find('textarea').val('');
                        $(this).html(
                            `<i class="bi bi-${isPrimary ? 'x-circle' : 'chat-square-text'}"></i> ${isPrimary ? 'Cancel' : 'Update'}`
                        );
                        setInfoWindowButtonStyles($(this), isPrimary ? 'var(--color-yellow' :
                            'var(--color-primary');
                    });

                    $(document).on('click', '.approveBtn', function() {
                        let reportType = $(this).attr('aria-report-type');

                        confirmModal(
                            `Are you sure you want to ${reportType != "Area" ?  "change the status of" : "approve"} this report?`
                        ).then((result) => {
                            if (!result.isConfirmed) return;

                            let url = reportType == "Incident" ?
                                "{{ route('incident.change.status', 'reportId') }}" :
                                reportType == "Emergency" ?
                                "{{ route('emergency.change.status', 'reportId') }}" :
                                "{{ route('area.approve', 'reportId') }}";

                            submitHandler($(this), 'PATCH', 'approve', url, reportType);
                        });
                    });

                    $(document).on('click', '.sendUpdateBtn', function() {
                        let form = $(this).parent().parent();

                        form.validate({
                            rules: {
                                update: 'required'
                            },
                            messages: {
                                update: 'Please enter update details.'
                            },
                            errorElement: 'span',
                            submitHandler() {
                                confirmModal(
                                        "Are you sure you want to add update to this report?")
                                    .then((result) => {
                                        if (!result.isConfirmed) return;
                                        submitHandler(form, 'PATCH', 'update',
                                            "{{ route('area.update', 'reportId') }}",
                                            "Area");
                                    });
                            }
                        });
                    });

                    $(document).on('click', '.removeBtn, .archiveBtn', function() {
                        const operation = $(this).text().includes('Remove') ? 'remove' : 'archive';

                        confirmModal(`Are you sure you want to ${operation} this report?`).then((
                            result) => {
                            if (!result.isConfirmed) return;

                            let reportType = $(this).attr('aria-report-type'),
                                isRemove = operation == 'remove',
                                url = reportType == "Incident" ? (
                                    isRemove ?
                                    "{{ route('incident.remove', 'reportId') }}" :
                                    "{{ route('incident.archive', 'reportId') }}"
                                ) : reportType == "Emergency" ?
                                "{{ route('emergency.remove', 'reportId') }}" :
                                isRemove ?
                                "{{ route('area.remove', 'reportId') }}" :
                                "{{ route('area.archive', 'reportId') }}";

                            submitHandler($(this), isRemove ? 'DELETE' : 'PATCH',
                                operation, url, reportType);
                        });
                    });

                    $(document).on('click', '.archiveEmergencyBtn', function() {
                        emergencyArchiveBtn = $(this);
                        modal.modal('show');
                    });

                    $(document).on('click', '#archiveReportBtn', function() {
                        archiveFormValidator = $('#archivedReportForm').validate({
                            rules: {
                                details: 'required'
                            },
                            messages: {
                                details: 'Please enter details.'
                            },
                            errorElement: 'span',
                            showErrors() {
                                this.defaultShowErrors();

                                $('#image-error').text('Please select an image.')
                                    .prop('style', `display: ${$('#areaInputImage').val() == '' ?
                                        'block' : 'none'} !important`);
                            },
                            submitHandler() {
                                if ($('#areaInputImage').val() == '') return;

                                confirmModal('Are you sure about the info you added?').then((
                                    result) => {
                                    if (!result.isConfirmed) return;

                                    submitHandler(emergencyArchiveBtn, "POST", "archive",
                                        "{{ route('emergency.archive', 'reportId') }}",
                                        "Emergency");
                                });
                            }
                        });
                    });

                    function submitHandler(element, type, operation, url, reportType) {
                        let data = element.serialize(),
                            processData = true,
                            contentType = 'application/x-www-form-urlencoded';

                        if (reportType != "Area") {
                            element = element.prev();
                            if (operation == "remove") element = element.prev();
                        } else
                        if (operation != 'update') element = element.parent().parent().prev();

                        if (reportType == "Emergency" && operation == "archive") {
                            data = new FormData($('#archivedReportForm')[0]);
                            contentType = false;
                            processData = false;
                        }

                        $.ajax({
                            type: type,
                            url: url.replace('reportId', element.find('p:first').text()),
                            data: data,
                            contentType: contentType,
                            processData: processData,
                            success(response) {
                                response.status == "warning" ?
                                    showWarningMessage(response.message) : (showSuccessMessage(
                                        `Successfully ${(operation == "approve" && reportType != "Area") ? "change the status of" : `${operation}d`} the report.`
                                    ),modal.modal('hide'));
                            },
                            error: showErrorMessage
                        });
                    }

                   modal.on('hidden.bs.modal', () => {
                        $('#archivedReportForm')[0].reset();
                        $('#selectedReportImage').attr('src', '').prop('hidden', 1);
                        $('#image-error').text('').prop('hidden', 1);
                        $('#imageBtn').html('<i class="bi bi-image"></i> Select');
                        setInfoWindowButtonStyles($('#imageBtn'), 'var(--color-primary');
                        archiveFormValidator && archiveFormValidator.resetForm();
                    });
                @endif

                Echo.channel('incident-report').listen('IncidentReport', (e) => {
                    ajaxRequest("Incident").then(() => checkNoReports("Incident"));
                });

                Echo.channel('emergency-report').listen('EmergencyReport', (e) => {
                    ajaxRequest("Emergency").then(() => checkNoReports("Emergency"));
                });

                Echo.channel('area-report').listen('AreaReport', (e) => {
                    ajaxRequest("Area").then(() => checkNoReports("Area"));
                });
            @else
                if ('{{ $yearList->isEmpty() }}')
                    showInfoMessage('There is no archived report.');

                reportTable = $('#reportTable').DataTable({
                    ordering: false,
                    responsive: true,
                    processing: false,
                    serverSide: true,
                    ajax: url,
                    button: [
                        'csv',
                        'excel'
                    ],
                    columns: [{
                            data: 'id',
                            name: 'id',
                            visible: false
                        }, {
                            data: 'details',
                            name: 'details'
                        },
                        {
                            data: 'type',
                            name: 'type',
                            width: '10%'
                        },
                        {
                            data: 'report_time',
                            name: 'report_time',
                            width: '10%'
                        },
                        {
                            data: 'photo',
                            name: 'photo',
                            width: '10%'
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
                            data: 'location',
                            name: 'location',
                            width: '1rem'
                        }
                    ],
                    columnDefs: [{
                            targets: 2,
                            visible: !(sessionStorage.getItem('archiveReportType') == "Emergency" ||
                                sessionStorage.getItem('archiveReportType') == "Incident")
                        },
                        {
                            target: 3,
                            render: data => formatDateTime(data)
                        }
                    ]
                });

                $(document).on('change', '#changeYearSelect, #changeArchivedReportType', function() {
                    $(this).attr('id') == 'changeYearSelect' ?
                        sessionStorage.setItem('archiveReportYear', $(this).val()) :
                        sessionStorage.setItem('archiveReportType', $(this).val());

                    let type = sessionStorage.getItem('archiveReportType');

                    switch (type) {
                        case 'Emergency':
                            url = "{{ route('emergency.get', ['archived', 'year', 'type']) }}";
                            break;
                        case 'Incident':
                            url = "{{ route('incident.get', ['archived', 'year', 'type']) }}";
                            break;
                        case 'Flooded':
                        case 'Roadblocked':
                            url = "{{ route('area.get', ['archived', 'year', 'type']) }}";
                            break;
                        default:
                            url = "{{ route('resident.report.get', 'year') }}";
                    }

                    reportTable.clear();
                    reportTable.ajax.url(url
                        .replace('year', sessionStorage.getItem("archiveReportYear"))
                        .replace('type', type)).load();
                });

                $(document).on('click', '.overlay-text', function() {
                    let reportPhotoUrl = $(this).closest('.image-wrapper').find('.report-img').attr('src'),
                        overlay = $(
                            `<div class="overlay show"><img src="${reportPhotoUrl}" class="overlay-image"></div>`
                        );
                    $('body').append(overlay).on('click', () => overlay.remove());
                });

                $(document).on('click', '.viewLocationBtn', function() {
                    let data = getRowData(this, reportTable),
                        position = {
                            lat: parseFloat(data.latitude),
                            lng: parseFloat(data.longitude)
                        };

                    map.setCenter(position);
                    map.setZoom(15);

                    if (reportMarker) {
                        reportMarker.setPosition(position);
                        reportMarker.setIcon({
                            url: "{{ asset('assets/img/picture.png') }}"
                                .replace('picture', data.type),
                            scaledSize: new google.maps.Size(35, 35)
                        });
                    } else {
                        reportMarker = new google.maps.Marker({
                            position,
                            map,
                            icon: {
                                url: "{{ asset('assets/img/picture.png') }}"
                                    .replace('picture', data.type),
                                scaledSize: new google.maps.Size(35, 35)
                            },
                        });
                    }

                    $('#locationModal').modal('show');
                })
            @endif
        });
    </script>
</body>

</html>
