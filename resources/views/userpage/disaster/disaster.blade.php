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
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-cloud-{{ $operation == 'manage' ? 'upload' : 'slash' }}"></i>
                    </div>
                </div>
                <span>{{ strtoupper($operation) }} DISASTER</span>
            </div>
            <hr>
            @if ($operation == 'manage')
                <div class="page-button-container">
                    <button class="btn-submit" id="addDisasterData">
                        <i class="bi bi-cloud-plus"></i>Add Disaster
                    </button>
                </div>
                @include('userpage.disaster.disasterModal')
            @endif
            <section class="table-container">
                <div class="table-content">
                    <header class="table-label">Disaster Information Table</header>
                    <table class="table" id="disasterTable" width="100%">
                        <thead>
                            <tr>
                                <th colspan="2">Disaster Name</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>
            @include('userpage.changePasswordModal')
        </main>
    </div>

    @include('partials.script')
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
                ordering: false,
                responsive: true,
                processing: false,
                serverSide: true,
                ajax: "{{ route('disaster.display', [$operation, 'none']) }}",
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
                        data: 'year',
                        name: 'year',
                        width: '10%'
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
                ]
            });

            let disasterId, defaultFormData, operation, validator,
                form = $("#disasterForm"),
                modalLabelContainer = $('.modal-label-container'),
                modalLabel = $('.modal-label'),
                formButton = $('#submitDisasterBtn'),
                modal = $('#disasterModal');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            validator = form.validate({
                rules: {
                    name: 'required'
                },
                messages: {
                    name: 'Please Enter Disaster Name.'
                },
                errorElement: 'span',
                submitHandler(form) {
                    let formData = $(form).serialize();

                    confirmModal(`Do you want to ${operation} this disaster?`).then((result) => {
                        if (!result.isConfirmed) return;

                        return operation == 'update' && defaultFormData == formData ?
                            showWarningMessage() :
                            $.ajax({
                                data: formData,
                                url: operation == 'add' ? "{{ route('disaster.create') }}" :
                                    "{{ route('disaster.update', 'disasterId') }}".replace(
                                        'disasterId', disasterId),
                                method: operation == 'add' ? "POST" : "PATCH",
                                beforeSend() {
                                    $('#btn-loader').addClass('show');
                                    formButton.prop('disabled', 1);
                                },
                                success(response) {
                                    $('#btn-loader').addClass('show');
                                    formButton.prop('disabled', 0);
                                    response.status == 'warning' ? showWarningMessage(
                                        response
                                        .message) : (
                                        showSuccessMessage(
                                            `Disaster successfully ${operation == "add" ? "added" : "updated"}.`
                                        ), modal.modal('hide'), disasterTable.draw());
                                },
                                error: showErrorMessage
                            });
                    });
                }
            });

            $('#addDisasterData').click(() => {
                modalLabelContainer.removeClass('bg-warning');
                modalLabel.text('Add Disaster');
                formButton.addClass('btn-submit').removeClass('btn-update').append('Add');
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
                formButton.addClass('btn-update').removeClass('btn-submit').append('Update');
                operation = "update";
                modal.modal('show');
                defaultFormData = form.serialize();
            });

            $(document).on('click', '#archiveDisaster', function() {
                alterDisasterData('archive',
                    "{{ route('disaster.archive', ['disasterId', 'archive']) }}", this);
            });

            $(document).on('click', '#unArchiveDisaster', function() {
                alterDisasterData('unarchive',
                    "{{ route('disaster.archive', ['disasterId', 'unarchive']) }}", this);
            });

            $(document).on('change', '#changeDisasterStatus', function() {
                alterDisasterData('change',
                    "{{ route('disaster.change.status', 'disasterId') }}", this, $(this).val());
            });

            modal.on('hidden.bs.modal', () => {
                validator && validator.resetForm();
                form[0].reset();
            });

            function alterDisasterData(operation, url, btn, status = null) {
                confirmModal(
                        `Do you want to ${operation == 'change' ? 'change the status of' : operation} this disaster?`
                    )
                    .then((result) => {
                        return !result.isConfirmed ? $('#changeDisasterStatus').val('') :
                            $.ajax({
                                method: 'PATCH',
                                data: {
                                    status: status
                                },
                                url: url.replace('disasterId',
                                    getRowData(btn, disasterTable).id),
                                success(response) {
                                    response.status == 'warning' ?
                                        showWarningMessage(response.message) :
                                        (disasterTable.draw(), showSuccessMessage(
                                            `Disaster successfully ${operation == "change" ? "changed status" : operation}.`
                                        ));
                                },
                                error: showErrorMessage
                            });
                    });
            }

            function disasterFormHandler(form) {
                let formData = $(form).serialize();

                confirmModal(`Do you want to ${operation} this disaster?`).then((result) => {
                    if (!result.isConfirmed) return;

                    return operation == 'update' && defaultFormData == formData ?
                        showWarningMessage() :
                        $.ajax({
                            data: formData,
                            url: operation == 'add' ? "{{ route('disaster.create') }}" :
                                "{{ route('disaster.update', 'disasterId') }}".replace(
                                    'disasterId',
                                    disasterId),
                            method: operation == 'add' ? "POST" : "PATCH",
                            success(response) {
                                response.status == 'warning' ? showWarningMessage(response
                                    .message) : (
                                    showSuccessMessage(
                                        `Disaster successfully ${operation == "add" ? "added" : "updated"}.`
                                    ), $('#closeModalBtn').click(), disasterTable.draw());
                            },
                            error: showErrorMessage
                        });
                });
            }
        });
    </script>
</body>

</html>
