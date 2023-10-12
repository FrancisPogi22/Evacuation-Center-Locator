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
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-megaphone"></i>
                    </div>
                </div>
                <span>INCIDENT REPORT</span>
            </div>
            <hr>
            @if ($operation == 'pending')
                <section class="table-container">
                    <div class="table-content">
                        <header class="table-label">Pending Incident Report</header>
                        <table class="table" id="pendingReport" width="100%">
                            <thead>
                                <tr>
                                    <th colspan="2">Description</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Photo</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
            {{-- @guest
                <section class="incident-report">
                    <div class="incidentReportTable">
                        @foreach ($incidentReport as $report)
                            <div class="incident-report-container">
                                <div class="incident-report-content">
                                    <div class="report-photo-container">
                                        <img class="report-photo" src="{{ asset('reports_image/' . $report->photo) }}"
                                            alt="logo">
                                    </div>
                                    <div class="incident-report-details">
                                        <div class="pt-2">
                                            <p class="fw-bold">Report Description</p>
                                            <p class="fs-6 text-secondary">{{ $report->description }}</p>
                                        </div>
                                        <div class="py-2">
                                            <p class="fw-bold">Report Location</p>
                                            <p class="fs-6 text-secondary">{{ $report->location }}</p>
                                        </div>
                                        <div class="py-2">
                                            <p class="fw-bold">Report Status :
                                                <span class="status-content bg-success">{{ $report->status }}</span>
                                            </p>
                                        </div>
                                        <p class="pb-2 fw-bold">Date Reported: <span
                                                class="text-danger">{{ date('Y') }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endguest --}}
            @auth
                @if ($operation == 'report')
                    <section class="table-container">
                        <div class="table-content">
                            <header class="table-label">Incident Report</header>
                            <table class="table" id="incidentReports" width="100%">
                                <thead>
                                    <tr>
                                        <th colspan="2">Description</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Photo</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif
            @endauth
        </main>
        @guest
            @include('userpage.incidentReport.incidentReportModal')
            <div class="report-button">
                <div class="report-form">
                    <a href="javascript:void(0)" id="reportIncident">
                        <i class="bi bi-megaphone text-white"></i>
                    </a>
                </div>
            </div>
        @endguest
        @include('userpage.changePasswordModal')
    </div>

    @include('partials.script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    @include('partials.toastr')
    <script>
        $(document).ready(() => {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                @auth
                let pendingReport, incidentReport;

                @if ($operation == 'pending')
                    pendingReport = $('#pendingReport').DataTable({
                        ordering: false,
                        responsive: true,
                        processing: false,
                        serverSide: true,
                        ajax: "{{ route('report.pending', $operation) }}",
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
                                data: 'location',
                                name: 'location'
                            },
                            {
                                data: 'status',
                                name: 'status',
                                width: '10%',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'photo',
                                name: 'photo',
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
                            }
                        ],
                        columnDefs: [{
                            targets: 5,
                            visible: {{ auth()->user()->is_disable }} == 0 ? true : false
                        }]
                    });
                @endif

                @if ($operation == 'report')
                    incidentReports = $('#incidentReports').DataTable({
                        ordering: false,
                        responsive: true,
                        processing: false,
                        serverSide: true,
                        ajax: "{{ route('report.accident', $operation) }}",
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
                                data: 'location',
                                name: 'location'
                            },
                            {
                                data: 'status',
                                name: 'status',
                                width: '10%',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'photo',
                                name: 'photo',
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
                            }
                        ],
                        columnDefs: [{
                            targets: 5,
                            visible: {{ auth()->user()->is_disable }} == 0 ? true : false
                        }]
                    });
                @endif

                @if (auth()->user()->is_disable == 0)
                    $(document).on('click', '#approveIncidentReport', function() {
                        alterIncidentReport('approve', getRowData(this, pendingReport).id);
                    });

                    $(document).on('click', '#declineIncidentReport', function() {
                        alterIncidentReport('decline', getRowData(this, pendingReport).id);
                    });

                    @if ($operation == 'report')
                        $(document).on('click', '#archiveIncidentReport', function() {
                            alterIncidentReport('archive', getRowData(this, incidentReports).id);
                        });

                        $(document).on('click', '#unArchiveIncidentReport', function() {
                            alterIncidentReport('unarchive', getRowData(this, incidentReports).id);
                        });
                    @endif

                    function alterIncidentReport(operation, reportId) {
                        confirmModal(`Do you want to ${operation} this report?`).then((result) => {
                            if (!result.isConfirmed) return;

                            let route = {
                                approve: "{{ route('report.approve', 'reportId') }}",
                                decline: "{{ route('report.decline', 'reportId') }}",
                                archive: "{{ route('report.archive', ['reportId', 'archive']) }}",
                                unarchive: "{{ route('report.archive', ['reportId', 'unarchive']) }}"
                            };
                            route[operation] = route[operation].replace('reportId', reportId);

                            $.ajax({
                                method: operation == 'archive' || operation == 'unarchive' ? 'PATCH' :
                                    operation == 'approve' ? 'POST' : 'DELETE',
                                url: route[operation],
                                success() {
                                    showSuccessMessage(`Incident report successfully ${operation}d.`);
                                    @if ($operation == 'pending')
                                        pendingReport.draw();
                                    @else
                                        incidentReports.draw();
                                    @endif
                                },
                                error() {
                                    showErrorMessage();
                                }
                            });
                        });
                    }
                @endif
            @endauth
            @guest
            let reportId, report_description, report_location, validator, operation, modal = $(
                    '#createAccidentReportModal'),
                modalLabelContainer = $('.modal-label-container'),
                modalLabel = $('.modal-label'),
                formButton = $('#reportIncidentBtn');

            @if ($operation == 'pending')
                let pendingReport = $('#pendingReport').DataTable({
                    ordering: false,
                    responsive: true,
                    processing: false,
                    serverSide: true,
                    ajax: "{{ route('resident.report.pending', $operation) }}",
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
                            data: 'location',
                            name: 'location'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'photo',
                            name: 'photo',
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
                        }
                    ]
                });
            @endif

            validator = $("#reportForm").validate({
                rules: {
                    description: 'required',
                    location: 'required'
                },
                messages: {
                    description: 'Please Enter Incident Description.',
                    location: 'Please Enter Incident Location.'
                },
                errorElement: 'span',
                submitHandler: formSubmitHandler
            });

            $(document).on('click', '#reportIncident', () => {
                operation = "report";
                modalLabelContainer.removeClass('bg-warning');
                modalLabel.text('Report Incident');
                formButton.addClass('btn-submit').removeClass('btn-update').text('Report');
                modal.modal('show');
            });

            $(document).on('click', '#updateIncidentReport', function() {
                let {
                    id,
                    description,
                    location
                } = getRowData(this, pendingReport);
                operation = "update";
                reportId = id;
                $('#description').val(description);
                $('#location').val(location);
                report_description = description;
                report_location = location;
                modalLabelContainer.addClass('bg-warning');
                modalLabel.text('Update Report Incident');
                formButton.removeClass('btn-submit').addClass('btn-update').text("Update");
                modal.modal('show');
            });

            $(document).on('click', '#revertIncidentReport', function() {
                reportId = getRowData(this, pendingReport).id;

                confirmModal('Do you want to revert your report?').then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('resident.report.revert', 'reportId') }}"
                            .replace('reportId', reportId),
                        success() {
                            revertReport(reportId);
                        },
                        error() {
                            showErrorMessage();
                        }
                    });
                });
            });

            $('#report_photo').change(function() {
                let reader = new FileReader();

                reader.onload = (e) => {
                    $('#preview-image').attr('src', e.target.result);
                }

                reader.readAsDataURL(this.files[0]);
            });

            function formSubmitHandler(form) {
                let formData = new FormData(form);

                confirmModal(`Do you want to ${operation} this incident?`).then((result) => {
                    if (!result.isConfirmed) return;

                    let url = operation == "update" ?
                        "{{ route('resident.report.incident.update', 'reportId') }}".replace('reportId',
                            reportId) : "{{ route('resident.report.accident') }}";

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: formData,
                        contentType: false,
                        processData: false,
                        success(response) {
                            let status = response.status,
                                message = response.message;

                            return status == 'warning' ? showWarningMessage(message) : status ==
                                'blocked' ? (modal.modal('hide'), showWarningMessage(message)) : (
                                    showSuccessMessage(
                                        `Report Successfully ${operation == "update" ? 'updated' : 'submitted'}, Thank for your concern.`
                                    ),
                                    modal.modal('hide'), pendingReport.draw());
                        },
                        error() {
                            showErrorMessage();
                        }
                    });
                });
            }

            function revertReport(reportId) {
                $.ajax({
                    type: "PATCH",
                    url: "{{ route('resident.report.update', 'reportId') }}".replace('reportId', reportId),
                    success() {
                        showSuccessMessage('Incident report successfully reverted.');
                        pendingReport.draw();
                    },
                    error() {
                        showErrorMessage();
                    }
                });
            }

            modal.on('hidden.bs.modal', () => {
                validator.resetForm();
                $('#reportForm')[0].reset();
            });
        @endguest

        $(document).on('click', '.overlay-text', function() {
        let reportPhotoUrl = $(this).closest('.image-wrapper').find('.report-img').attr('src');
        displayReportPhoto(reportPhotoUrl);
        });

        // Echo.channel('incident-report-event').listen('IncidentReportEvent', (e) => {
        // pendingReport.draw();
        // @auth
        // incidentReports.draw();
        // @endauth
        // });
        });
    </script>
</body>

</html>
