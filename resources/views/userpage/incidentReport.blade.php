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
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-megaphone"></i>
                    </div>
                </div>
                <span>INCIDENT REPORT</span>
            </div>
            <hr>
            <div class="table-container">
                <div class="table-content">
                    <header class="table-label">Pending Incident Report</header>
                    <table class="table pendingReport" width="100%">
                        <thead class="thead-light">
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
            </div>
            @guest
                <div class="incident-report">
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
                                        <p class="pb-2 fw-bold">Date Reported: <span class="text-danger">July 22,
                                                2002</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endguest
            @auth
                <br>
                <div class="table-container">
                    <div class="table-content">
                        <header class="table-label">Incident Report</header>
                        <table class="table incidentReports" width="100%">
                            <thead class="thead-light">
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
                </div>
            </div>
        @endauth
        @guest
            <div class="modal fade" id="createAccidentReportModal" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-label-container bg-success">
                            <h1 class="modal-label">Incident Report</h1>
                        </div>
                        <div class="modal-body">
                            <form id="reportForm" name="reportForm" enctype="multipart/form-data">
                                @csrf
                                <div class="form-content">
                                    <input type="text" id="operation" hidden>
                                    <div class="field-container">
                                        <label>Report Description</label>
                                        <textarea type="text" id="description" name="description" class="form-control" rows="5"
                                            placeholder="Enter Incident Description" autocomplete="off"></textarea>
                                    </div>
                                    <div class="field-container">
                                        <label>Report Location</label>
                                        <input type="text" id="location" name="location" class="form-control"
                                            placeholder="Enter Incident Location" autocomplete="off">
                                    </div>
                                    <div class="field-container">
                                        <label>Report Photo</label>
                                        <input type="file" id="photo" name="photo"
                                            class="form-control form-control-lg" placeholder="Enter Incident Location"
                                            autocomplete="off" accept=".jpeg">
                                    </div>
                                    <div class="form-button-container">
                                        <button id="reportIncidentBtn" class="btn-submit">Report</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="report-button">
                <div class="report-form">
                    <a href="#createAccidentReportModal" data-bs-toggle="modal">
                        <i class="bi bi-megaphone text-white"></i>
                    </a>
                </div>
            </div>
        @endguest
        @auth
            @include('userpage.changePasswordModal')
        @endauth
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
            let pendingReport = $('.pendingReport').DataTable({
                language: {
                    emptyTable: '<div class="message-text">There are currently no pending reports.</div>',
                },
                ordering: false,
                responsive: true,
                processing: false,
                serverSide: true,
                ajax: "{{ route('report.pending') }}",
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
                    },
                ]
            });

            let incidentReports = $('.incidentReports').DataTable({
                language: {
                    emptyTable: '<div class="message-text">There are currently no reports.</div>',
                },
                ordering: false,
                responsive: true,
                processing: false,
                serverSide: true,
                ajax: "{{ route('report.accident') }}",
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
                    },
                ]
            });

            @if (auth()->user()->is_disable == 0)
                $(document).on('click', '.approveIncidentReport', function() {
                    alterIncidentReport('approve', getRowData(this, pendingReport).id);
                });

                $(document).on('click', '.declineIncidentReport', function() {
                    alterIncidentReport('decline', getRowData(this, pendingReport).id);
                });

                $(document).on('click', '.removeIncidentReport', function() {
                    alterIncidentReport('remove', getRowData(this, incidentReports).id);
                });

                function alterIncidentReport(operation, reportId) {
                    confirmModal(`Do you want to ${operation} this report?`).then((result) => {
                        if (result.isConfirmed) {
                            let url = operation == 'approve' ? "{{ route('report.approve', ':reportId') }}"
                                .replace(':reportId',
                                    reportId) : operation == 'decline' ?
                                "{{ route('report.decline', ':reportId') }}".replace(':reportId',
                                    reportId) : "{{ route('report.remove', ':reportId') }}".replace(
                                    ':reportId', reportId);

                            let type = operation == 'approve' ? "POST" : operation == 'decline' ? "DELETE" :
                                "PATCH";

                            $.ajax({
                                type: type,
                                url: url,
                                success: function() {
                                    showSuccessMessage(
                                        `Incident report successfully ${operation}d.`);
                                    pendingReport.draw();
                                    incidentReports.draw();
                                },
                                error: function() {
                                    showErrorMessage();
                                }
                            });
                        }
                    });
                }
            @endif
        @endauth
        @guest
        const modal = $('#createAccidentReportModal');

        let pendingReport = $('.pendingReport').DataTable({
            language: {
                emptyTable: '<div class="message-text">You have no pending reports.</div>'
            },
            ordering: false,
            responsive: true,
            processing: false,
            serverSide: true,
            ajax: "{{ route('resident.report.pending') }}",
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
                },
            ]
        });

        const validator = $("#reportForm").validate({
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

        $('#report_photo').change(function() {
            let reader = new FileReader();

            reader.onload = (e) => {
                $('#preview-image').attr('src', e.target.result);
            }

            reader.readAsDataURL(this.files[0]);
        });

        function formSubmitHandler(form, e) {
            let formData = new FormData(form);
            e.preventDefault();

            confirmModal('Do you want to report this incident?').then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('resident.report.accident') }}",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success(response) {
                            if (response.status == 'success') {
                                showSuccessMessage(
                                    'Successfully reported, Thank for your concern.');
                                $('#reportForm')[0].reset();
                                modal.modal('hide');
                                pendingReport.draw();
                            } else if (response.status == 'warning') {
                                showWarningMessage(response.message);
                            } else if (response.status == 'blocked') {
                                $('#reportForm')[0].reset();
                                modal.modal('hide');
                                showWarningMessage(response.message);
                            }
                        },
                        error() {
                            showErrorMessage();
                        }
                    });
                }
            });
        }

        $(document).on('click', '#revertIncidentReport', function() {
            let reportId = getRowData(this, pendingReport).id;

            confirmModal('Do you want to revert your report?').then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('resident.report.revert', ':reportId') }}"
                            .replace(':reportId', reportId),
                        success: function() {
                            revertReport(reportId);
                        },
                        error: function() {
                            showErrorMessage();
                        }
                    });
                }
            });
        });

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
        });
        @endguest

        // Echo.channel('incident-report').listen('IncidentReport', (e) => {
        // pendingReport.draw();
        // incidentReports.draw();
        // })
        });
    </script>
</body>

</html>
