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
            <div class="grid grid-cols-1">
                <div class="grid col-end-1">
                    <div class="text-2xl text-white">
                        <i class="bi bi-megaphone p-2 bg-slate-600"></i>
                    </div>
                </div>
                <span class="text-xl font-bold">INCIDENT REPORT</span>
            </div>
            <hr class="mt-4">
<<<<<<< Updated upstream
            <div class="report-table shadow-lg p-4 rounded my-3">
                <header class="text-2xl font-semibold">Incident Pending Report</header>
                <table class="table pendingReport display nowrap" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th class="w-px">Report ID</th>
                            <th>Report Description</th>
                            <th>Accident Location</th>
                            <th class="w-5">Status</th>
                            <th style="width:20%;">Actual Photo</th>
                            <th class="w-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="report-table shadow-lg p-4 rounded mb-3">
                <header class="text-2xl font-semibold">Incident Report</header>
                <table class="table incidentReports display nowrap" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th class="w-px">Report ID</th>
                            <th>Report Description</th>
                            <th>Accident Location</th>
                            <th class="w-5">Status</th>
                            <th style="width:20%;">Actual Photo</th>
                            @auth
=======
            <div class="table-container p-3 shadow-lg rounded my-3">
                <header class="text-2xl font-semibold mb-3">Pending Incident Report Table</header>
                <div class="block w-full overflow-auto pb-2">
                    <table id="pendingReport" class="table" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th colspan="2">Report Description</th>
                                <th>Accident Location</th>
                                <th class="w-4">Status</th>
                                <th class="w-28">Actual Photo</th>
>>>>>>> Stashed changes
                                <th class="w-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
<<<<<<< Updated upstream
            @guest
                <div class="modal fade" id="createAccidentReportModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-green-600 text-white">
                                <h1 class="modal-title fs-5 text-center text-white">Incident Report Form</h1>
                            </div>
                            <div class="modal-body">
                                <form id="reportForm" name="reportForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="description" class="flex items-center justify-center">Report
                                            Description</label>
                                        <input type="text" id="description" name="description" class="form-control"
                                            placeholder="Enter Incident Description" autocomplete="off">
=======
            @auth
                <div class="table-container p-3 shadow-lg rounded mt-20">
                    <header class="text-2xl font-semibold mb-3">Incident Report Table</header>
                    <div class="block w-full overflow-auto pb-2">
                        <table id="incidentReports" class="table" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th colspan="2">Report Description</th>
                                    <th>Accident Location</th>
                                    <th class="w-4">Status</th>
                                    <th class="w-28">Actual Photo</th>
                                    <th class="w-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endauth
            @guest
                {{-- customize display ng incident reports in cabuyao o wag nalang --}}
            @endguest
        </div>
        @guest
            <div class="modal fade" id="createAccidentReportModal" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-green-600 text-white justify-center">
                            <h1 class="modal-title fs-5 font-bold">Incident Report Form</h1>
                        </div>
                        <div class="modal-body">
                            <form id="reportForm" name="reportForm" enctype="multipart/form-data">
                                @csrf
                                <div class="bg-slate-50 pt-3 pb-2 rounded">
                                    <div class="flex-auto">
                                        <div class="flex flex-wrap">
                                            <input type="text" id="operation" hidden>
                                            <div class="field-container">
                                                <label>Report Description</label>
                                                <textarea type="text" id="description" name="description" class="form-control"
                                                    placeholder="Enter Incident Description" rows="5" autocomplete="off"></textarea>
                                            </div>
                                            <div class="field-container">
                                                <label>Report Location</label>
                                                <input type="text" id="location" name="location" class="form-control"
                                                    placeholder="Enter Incident Location" autocomplete="off">
                                            </div>
                                            <div class="field-container">
                                                <label>Report Photo</label>
                                                <input type="file" id="photo" name="photo"
                                                    class="form-control form-control-lg"
                                                    placeholder="Enter Incident Location" autocomplete="off">
                                            </div>
                                            <div class="w-full px-4 pt-3 pb-4">
                                                <button id="reportIncidentBtn"
                                                    class="btn-submit p-2 float-right">Report</button>
                                            </div>
                                        </div>
>>>>>>> Stashed changes
                                    </div>
                                    <div class="mb-3">
                                        <label for="location" class="flex items-center justify-center">Report
                                            Location</label>
                                        <input type="text" id="location" name="location" class="form-control"
                                            placeholder="Enter Incident Location" autocomplete="off">
                                    </div>
                                    <div class="mb-3">
                                        <label for="photo" class="flex items-center justify-center">Report
                                            Photo</label>
                                        <input type="file" id="photo" name="photo" class="form-control"
                                            placeholder="Enter Incident Location" autocomplete="off">
                                    </div>
                                    <div class="modal-footer text-white">
                                        <button id="reportIncidentBtn"
                                            class="bg-green-600 p-2 rounded shadow-lg hover:bg-green-700 transition duration-200">Report
                                            Incident</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endguest
        </div>
        @guest
            <div class="report-button">
                <div class="report-form">
                    <a class="bg-slate-700 hover:bg-slate-800 p-3 fs-4 rounded-full" href="javascript:void(0)"
                        id="createReport">
                        <i class="bi bi-megaphone text-white"></i>
                    </a>
                </div>
            </div>
        @endguest
        @auth
            @include('userpage.changePasswordModal')
        @endauth
    </div>

    <script src="{{ asset('assets/js/script.js') }}"></script>
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
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            @auth
<<<<<<< Updated upstream
            let pendingReport = $('.pendingReport').DataTable({
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
=======
            let pendingReport = $('#pendingReport').DataTable({
                ordering: false,
>>>>>>> Stashed changes
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
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'photo',
                        name: 'photo',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

<<<<<<< Updated upstream
            let incidentReports = $('.incidentReports').DataTable({
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
=======
            let incidentReports = $('#incidentReports').DataTable({
                ordering: false,
>>>>>>> Stashed changes
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
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'photo',
                        name: 'photo',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            @if (auth()->user()->status == 'Active')
                let reportId;

                $('body').on('click', '.approveIncidentReport', function() {
                    reportId = getPendingRowData(this)['id'];
                    alterIncidentReport('approve');
                });

                $('body').on('click', '.declineIncidentReport', function() {
                    reportId = getPendingRowData(this)['id'];
                    alterIncidentReport('decline');
                });

                $('body').on('click', '.removeIncidentReport', function() {
                    reportId = getIncidentRowData(this)['id'];
                    alterIncidentReport('remove');
                });

                function alterIncidentReport(operation) {
                    confirmModal(`Do you want to ${operation} this report?`).then((result) => {
                        if (result.isConfirmed) {
                            let url, type;

                            if (operation == 'approve') {
                                url = "{{ route('report.approve', ':reportId') }}"
                                    .replace(':reportId', reportId);
                                type = "POST";
                            } else if (operation == 'decline') {
                                url = "{{ route('report.decline', ':reportId') }}"
                                    .replace(':reportId', reportId);
                                type = "DELETE";
                            } else {
                                url = "{{ route('report.remove', ':reportId') }}"
                                    .replace(':reportId', reportId);
                                type = "PATCH";
                            }

                            $.ajax({
                                type: type,
                                url: url,
                                success: function() {
                                    toastr.success(
                                        `Incident report successfully ${operation}d.`,
                                        'Success');
                                    pendingReport.draw();
                                    incidentReports.draw();
                                },
                                error: function() {
                                    toastr.error(
                                        'An error occurred while processing your request.',
                                        'Error');
                                }
                            });
                        }
                    });
                }

                function getPendingRowData(element) {
                    let currentRow = $(element).closest('tr');

                    if (pendingReport.responsive.hasHidden()) {
                        currentRow = currentRow.prev('tr');
                    }

                    return pendingReport.row(currentRow).data();
                }

                function getIncidentRowData(element) {
                    let currentRow = $(element).closest('tr');

                    if (incidentReports.responsive.hasHidden()) {
                        currentRow = currentRow.prev('tr');
                    }

                    return incidentReports.row(currentRow).data();
                }
            @endif
        @endauth
        @guest
<<<<<<< Updated upstream
        let pendingReport = $('.pendingReport').DataTable({
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
=======
        let pendingReport = $('#pendingReport').DataTable({
            ordering: false,
>>>>>>> Stashed changes
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
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'photo',
                    name: 'photo',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

<<<<<<< Updated upstream
        let incidentReports = $('.incidentReports').DataTable({
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
=======
        let incidentReports = $('#incidentReports').DataTable({
            ordering: false,
>>>>>>> Stashed changes
            responsive: true,
            processing: false,
            serverSide: true,
            ajax: "{{ route('resident.report.display') }}",
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
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'photo',
                    name: 'photo',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#createReport').click(function() {
            $('#reportForm').trigger("reset");
            $('#createAccidentReportModal').modal('show');
        });

        $('#report_photo').change(function() {
            let reader = new FileReader();

            reader.onload = (e) => {
                $('#preview-image').attr('src', e.target.result);
            }

            reader.readAsDataURL(this.files[0]);

        });

        let validator = $("#reportForm").validate({
            rules: {
                description: {
                    required: true
                },
                location: {
                    required: true
                }
            },
            messages: {
                description: {
                    required: 'Please Enter Incident Description.'
                },
                location: {
                    required: 'Please Enter Incident Location.'
                }
            },
            errorElement: 'span',
            submitHandler: formSubmitHandler,
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
                        success: function(response) {
                            if (response.status == 'success') {
                                toastr.success(
                                    'Successfully reported, Thank for your concern.',
                                    'Success');
                                $('#reportForm')[0].reset();
                                $('#createAccidentReportModal').modal('hide');
                                pendingReport.draw();
                            } else if (response.status == 'warning') {
                                toastr.warning(response.message, 'Warning');
                            } else if (response.status == 'blocked') {
                                $('#reportForm')[0].reset();
                                $('#createAccidentReportModal').modal('hide');
                                toastr.warning(response.message, 'Warning');
                            }
                        },
                        error: function() {
                            toastr.error(
                                'An error occurred while processing your request.',
                                'Error');
                        }
                    });
                }
            });
        }

        $('body').on('click', '.revertIncidentReport', function() {
            let reportId = getPendingRowData(this)['id'];

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
                            toastr.error(
                                'An error occurred while processing your request.',
                                'Error');
                        }
                    });
                }
            });
        });

        function revertReport(reportId) {
            $.ajax({
                type: "PUT",
                url: "{{ route('resident.report.update', ':reportId') }}".replace(':reportId',
                    reportId),
                success: function() {
                    toastr.success('Incident report successfully reverted.', 'Success');
                    pendingReport.draw();
                },
                error: function() {
                    toastr.error(
                        'An error occurred while processing your request.',
                        'Error');
                }
            });
        }

        function getPendingRowData(element) {
            let currentRow = $(element).closest('tr');

            if (pendingReport.responsive.hasHidden()) {
                currentRow = currentRow.prev('tr');
            }

            return pendingReport.row(currentRow).data();
        }

        $('#createAccidentReportModal').on('hidden.bs.modal', function() {
            validator.resetForm();
        });
        @endguest

        // Echo.channel('incident-report').listen('IncidentReport', (e) => {
        //     pendingReport.draw();
        //     incidentReports.draw();
        // })
        });
    </script>
</body>

</html>
