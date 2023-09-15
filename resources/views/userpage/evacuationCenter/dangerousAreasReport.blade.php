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
        <main class="main-content">
            <div class="label-container">
                <i class="bi bi-exclamation-triangle"></i>
                <span>DANGER AREAS REPORT</span>
            </div>
            <hr>
            @guest
                <div class="page-button-container">
                    <button class="btn-submit" id="reportDangerArea">
                        <i class="bi bi-cloud-plus"></i>Report Danger Area
                    </button>
                </div>
            @endguest
            <section class="table-container">
                <div class="table-content">
                    <header class="table-label">Danger Areas Report Table</header>
                    <table class="table" id="dangerousAreasReports" width="100%">
                        <thead>
                            <tr>
                                <th colspan="2">Description</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>
            @guest
                @include('userpage.evacuationCenter.dangerAreaReportModal')
            @endguest
            @include('userpage.changePasswordModal')
        </main>

        @include('partials.script')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
        </script>
        @guest
            <script defer
                src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googleMap.key') }}&callback=initMap&v=weekly">
            </script>
        @endguest
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
            integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
            crossorigin="anonymous"></script>
        @include('partials.toastr')
        <script>
            @guest
            let map, marker;

            function initMap() {
                map = new google.maps.Map(document.getElementById("map"), {
                    center: {
                        lat: 14.246261,
                        lng: 121.12772
                    },
                    zoom: 13,
                    clickableIcons: false,
                    mapTypeControlOptions: {
                        style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                    }
                });

                map.addListener("click", (event) => {
                    let location = event.latLng;

                    if (marker) {
                        marker.setPosition(location);
                    } else {
                        marker = new google.maps.Marker({
                            position: location,
                            map: map,
                            icon: {
                                url: "{{ asset('assets/img/evacMarkerDefault.png') }}",
                                scaledSize: new google.maps.Size(35, 35)
                            }
                        });
                    }

                    $('#latitude').val(location.lat());
                    $('#longitude').val(location.lng());
                    $('#location-error').text('').prop('style', 'display: none');
                });
            }
            @endguest

            $(document).ready(() => {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                @auth
                let dangerousAreasReports = $('#dangerousAreasReports').DataTable({
                    language: {
                        emptyTable: '<div class="message-text">There are currently no dangerous areas reports.</div>',
                    },
                    ordering: false,
                    responsive: true,
                    processing: false,
                    serverSide: true,
                    ajax: "{{ route('report.dangerous.areas.cswd', $operation) }}",
                    columns: [{
                            data: 'id',
                            name: 'id',
                            visible: false
                        },
                        {
                            data: 'description',
                            name: 'description'
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
                            data: 'status',
                            name: 'status',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            width: '1rem',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    columnDefs: [{
                        targets: 5,
                        visible: {{ auth()->user()->is_disable }} == 0 ? true : false
                    }]
                });

                @if (auth()->user()->is_disable == 0)
                    $(document).on('click', '#confirmDangerAreaReport', function() {
                        alterIncidentReport('confirm', getRowData(this, dangerousAreasReports).id);
                    });

                    $(document).on('click', '#rejectDangerAreaReport', function() {
                        alterIncidentReport('reject', getRowData(this, dangerousAreasReports).id);
                    });

                    $(document).on('click', '#archiveDangerAreaReport', function() {
                        alterIncidentReport('archive', getRowData(this, dangerousAreasReports).id);
                    });

                    $(document).on('click', '#unArchiveDangerAreaReport', function() {
                        alterIncidentReport('unarchive', getRowData(this, dangerousAreasReports).id);
                    });

                    function alterIncidentReport(operation, dangerAreaId) {
                        confirmModal(`Do you want to ${operation} this report?`)
                            .then((result) => {
                                if (result.isConfirmed) {
                                    let url = operation == 'confirm' ?
                                        "{{ route('report.dangerous.areas.confirm', 'dangerAreaId') }}"
                                        .replace('dangerAreaId', dangerAreaId) : operation == 'reject' ?
                                        "{{ route('report.dangerous.areas.reject', 'dangerAreaId') }}".replace(
                                            'dangerAreaId', dangerAreaId) : operation == 'archive' ?
                                        "{{ route('report.dangerous.areas.archive', ['dangerAreaId', 'archive']) }}"
                                        .replace('dangerAreaId', dangerAreaId) :
                                        "{{ route('report.dangerous.areas.archive', ['dangerAreaId', 'unarchive']) }}"
                                        .replace('dangerAreaId', dangerAreaId);

                                    let type = operation == 'confirm' ? "POST" : operation == "reject" ?
                                        "DELETE" :
                                        "PATCH";

                                    $.ajax({
                                        type: type,
                                        url: url,
                                        success() {
                                            showSuccessMessage(
                                                `Danger area successfully ${operation == "reject" ? "rejected" : operation}d.`
                                            );
                                            dangerousAreasReports.draw();
                                        },
                                        error() {
                                            showErrorMessage();
                                        }
                                    });
                                }
                            });
                    }
                @endif
            @endauth
            @guest
            let validator, defaultFormData, dangerousAreasReports, reportId, operation, saveBtnClicked = false,
                modalLabelContainer = $('.modal-label-container'),
                modalLabel = $('.modal-label'),
                formButton = $('#reportDangerousAreaBtn'),
                modal = $('#reportDangerousAreaModal');

            dangerousAreasReports = $('#dangerousAreasReports').DataTable({
                language: {
                    emptyTable: '<div class="message-text">There are currently no dangerous areas reports.</div>',
                },
                ordering: false,
                responsive: true,
                processing: false,
                serverSide: true,
                ajax: "{{ route('resident.report.danger.areas', $operation) }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        visible: false
                    },
                    {
                        data: 'description',
                        name: 'description'
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
                        data: 'status',
                        name: 'status',
                        width: '10%',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        width: '1rem',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            validator = $("#dangerousAreaReportForm").validate({
                rules: {
                    report_type: 'required'
                },
                messages: {
                    report_type: 'Please Select Report Type.'
                },
                showErrors: function(errorMap, errorList) {
                    this.defaultShowErrors();

                    if (!marker && saveBtnClicked)
                        $('#location-error').text('Please select a location.').prop('style',
                            'display: block !important');
                },
                errorElement: 'span',
                submitHandler: formSubmitHandler
            });

            $(document).on('click', '#reportDangerArea', () => {
                modalLabelContainer.removeClass('bg-warning');
                modalLabel.text('Report Dangerous Area');
                formButton.addClass('btn-submit').removeClass('btn-update').text('Report');
                operation = "report";
                modal.modal('show');
            });

            $(document).on('click', '#updateDangerousAreaReport', function() {
                let {
                    id,
                    description,
                    latitude,
                    longitude
                } = getRowData(this, dangerousAreasReports);
                reportId = id;
                modalLabelContainer.addClass('bg-warning');
                modalLabel.text('Update Report Dangerous Area');
                formButton.addClass('btn-update').removeClass('btn-submit').text('Update');
                operation = "update";
                $('#report_type').val(description);
                $('#latitude').val(latitude);
                $('#longitude').val(longitude);

                marker = new google.maps.Marker({
                    position: {
                        lat: parseFloat(latitude),
                        lng: parseFloat(longitude)
                    },
                    map: map,
                    icon: {
                        url: "{{ asset('assets/img/evacMarkerDefault.png') }}",
                        scaledSize: new google.maps.Size(35, 35),
                    },
                });

                modal.modal('show');
                defaultFormData = $('#dangerousAreaReportForm').serialize();
            });

            $(document).on('click', '#revertDangerousAreaReport', function() {
                reportId = getRowData(this, dangerousAreasReports).id;

                confirmModal('Do you want to revert your report?').then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('resident.report.revert.danger.area.report', 'reportId') }}"
                                .replace('reportId', reportId),
                            success() {
                                revertDangerAreaReport(reportId);
                            },
                            error() {
                                showErrorMessage();
                            }
                        });
                    }
                });
            });

            $(document).on('click', '#reportDangerAreaBtn', () => {
                saveBtnClicked = true;
            });

            function formSubmitHandler(form) {
                if (!marker) return;

                confirmModal(
                    `Do you want to ${operation == 'update' ? 'update your report' : 'report as dangerous this area'}?`
                ).then((result) => {
                    if (!result.isConfirmed) return;

                    let formData = $(form).serialize();
                    let url = operation == 'update' ?
                        "{{ route('resident.report.update.danger.area', 'reportId') }}".replace('reportId',
                            reportId) : "{{ route('resident.report.dangerous.area') }}";
                    let type = operation == 'update' ? 'PUT' : 'POST';

                    return operation == 'update' && defaultFormData == formData ?
                        showWarningMessage('No changes were made.') :
                        $.ajax({
                            data: formData,
                            url: url,
                            type: type,
                            success(response) {
                                return response.status == 'warning' ? showWarningMessage(response.message) :
                                    response.status == 'blocked' ? (modal.modal('hide'), showWarningMessage(
                                        response.message)) : (showSuccessMessage(
                                        `Danger area successfully ${operation == 'update' ? 'updated' : 'reported'}.`
                                    ), modal.modal('hide'), dangerousAreasReports.draw());
                            },
                            error() {
                                showErrorMessage();
                            }
                        });
                });
            }

            function revertDangerAreaReport(reportId) {
                $.ajax({
                    type: "PATCH",
                    url: "{{ route('resident.report.update', 'reportId') }}".replace(
                        'reportId', reportId),
                    success() {
                        showSuccessMessage('Danger area report successfully reverted.');
                        dangerousAreasReports.draw();
                    },
                    error() {
                        showErrorMessage();
                    }
                });
            }

            modal.on('hidden.bs.modal', () => {
                validator.resetForm();
                $('#dangerousAreaReportForm')[0].reset();

                if (marker) {
                    marker.setMap(null);
                    marker = undefined;
                }

                map.setCenter({
                    lat: 14.2471423,
                    lng: 121.1366715
                });
                map.setZoom(13);
                saveBtnClicked = false;
            });
            @endguest

            // Echo.channel('incident-report-event').listen('IncidentReportEvent', (e) => {
            // pendingReport.draw();
            // incidentReports.draw();
            // })
            });
        </script>
</body>

</html>
