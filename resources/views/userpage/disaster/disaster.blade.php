<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <main class="main-content">
            <div class="label-container">
                <i class="bi bi-tropical-storm"></i>
                <span>MANAGE DISASTER INFORMATION</span>
            </div>
            <hr>
            @if (auth()->user()->is_disable == 0 && $operation == 'manage')
                <div class="page-button-container">
                    <button class="btn-submit" id="addDisasterData">
                        <i class="bi bi-cloud-plus"></i>Add Disaster
                    </button>
                </div>
            @endif
            <section class="table-container">
                <div class="table-content">
                    <header class="table-label">Disaster Information Table</header>
                    <table class="table" id="disasterTable" width="100%">
                        <thead>
                            <tr>
                                <th colspan="2">Disaster Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>
            @include('userpage.disaster.disasterModal')
            @include('userpage.changePasswordModal')
        </main>
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
            let disasterTable = $('#disasterTable').DataTable({
                language: {
                    emptyTable: '<div class="message-text">There are no disaster data available.</div>'
                },
                ordering: false,
                responsive: true,
                processing: false,
                serverSide: true,
                ajax: "{{ route('disaster.display', $operation) }}",
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
                        data: 'status',
                        name: 'status',
                        width: '10%'
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
                    targets: 3,
                    visible: {{ auth()->user()->is_disable }} == 0 ? true : false
                }]
            });

            @if (auth()->user()->is_disable == 0)
                let disasterId, defaultFormData, status, operation, validator,
                    modalLabelContainer = $('.modal-label-container'),
                    modalLabel = $('.modal-label'),
                    formButton = $('#submitDisasterBtn'),
                    modal = $('#disasterModal');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                validator = $("#disasterForm").validate({
                    rules: {
                        name: 'required'
                    },
                    messages: {
                        name: 'Please Enter Disaster Name.'
                    },
                    errorElement: 'span',
                    submitHandler: disasterFormHandler
                });

                $(document).on('click', '#addDisasterData', () => {
                    modalLabelContainer.removeClass('bg-warning');
                    modalLabel.text('Add Disaster');
                    formButton.addClass('btn-submit').removeClass('btn-update').text('Add');
                    operation = "add";
                    modal.modal('show');
                });

                $(document).on('click', '#updateDisaster', function() {
                    let {
                        id,
                        name
                    } = getRowData(this, disasterTable);
                    disasterId = id;
                    $('#disasterName').val(name);
                    modalLabelContainer.addClass('bg-warning');
                    modalLabel.text('Update Disaster');
                    formButton.addClass('btn-update').removeClass('btn-submit').text('Update');
                    operation = "update";
                    modal.modal('show');
                    defaultFormData = $('#disasterForm').serialize();
                });

                $(document).on('click', '#archiveDisaster', function() {
                    alterDisasterData('archive',
                        "{{ route('disaster.archive', ['disasterId', 'archive']) }}"
                        .replace('disasterId', getRowData(this, disasterTable).id));
                });

                $(document).on('click', '#unArchiveDisaster', function() {
                    alterDisasterData('unarchive',
                        "{{ route('disaster.archive', ['disasterId', 'unarchive']) }}".replace(
                            'disasterId', getRowData(this, disasterTable).id));
                });

                $(document).on('change', '#changeDisasterStatus', function() {
                    status = $(this).val();
                    alterDisasterData('change', "{{ route('disaster.change.status', 'disasterId') }}"
                        .replace('disasterId', getRowData(this, disasterTable).id));
                });

                modal.on('hidden.bs.modal', () => {
                    validator.resetForm();
                    $('#disasterForm')[0].reset();
                });

                function alterDisasterData(operation, url) {
                    confirmModal(
                            `Do you want to ${operation == 'change' ? 'change the status of' : operation} this disaster?`
                        )
                        .then((result) => {
                            return !result.isConfirmed ? $('#changeDisasterStatus').val('') :
                                $.ajax({
                                    type: 'PATCH',
                                    data: {
                                        status: status
                                    },
                                    url: url,
                                    success() {
                                        disasterTable.draw();
                                        showSuccessMessage(
                                            `Disaster successfully ${operation == "change" ? "changed status" : operation}.`
                                        );
                                    },
                                    error() {
                                        showErrorMessage();
                                    }
                                });
                        });
                }

                function disasterFormHandler(form) {
                    let formData = $(form).serialize();
                    let url = operation == 'add' ? "{{ route('disaster.create') }}" :
                        "{{ route('disaster.update', 'disasterId') }}".replace('disasterId',
                            disasterId);
                    let type = operation == 'add' ? "POST" : "PATCH";

                    confirmModal(`Do you want to ${operation} this disaster?`).then((result) => {
                        if (!result.isConfirmed) return;

                        return operation == 'update' && defaultFormData == formData ?
                            showWarningMessage() :
                            $.ajax({
                                data: formData,
                                url: url,
                                type: type,
                                success(response) {
                                    response.status == 'warning' ? showWarningMessage(response
                                        .message) : (
                                        showSuccessMessage(
                                            `Disaster successfully ${operation == "add" ? "added" : "updated"}.`
                                        ),
                                        modal.modal('hide'), disasterTable.draw());
                                },
                                error() {
                                    showErrorMessage();
                                }
                            });
                    });
                }
            @endif
        });
    </script>
</body>

</html>
