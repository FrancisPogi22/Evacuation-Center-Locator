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
        <div class="main-content">
            <div class="grid grid-cols-1">
                <div class="grid col-end-1">
                    <div class="text-white text-2xl">
                        <i class="bi bi-tropical-storm p-2 bg-slate-600"></i>
                    </div>
                </div>
                <span class="text-xl font-bold">MANAGE DISASTER INFORMATION</span>
            </div>
            <hr class="mt-4 mb-3">
            @if (auth()->user()->status == 'Active')
                <div class="create-section">
                    <button class="btn-submit p-2" id="createDisasterData">
                        <i class="bi bi-cloud-plus pr-2"></i>
                        Add Disaster
                    </button>
                </div>
            @endif
            <div class="table-container p-3 shadow-lg rounded-lg">
                <header class="text-2xl font-semibold mb-3">Disaster Information Table</header>
                <div class="block w-full overflow-auto pb-2">
                    <table class="table disasterTable" width="100%">
                        <thead class="thead-light">
                            <tr>
<<<<<<< Updated upstream
                                <th></th>
                                <th>Disaster Name</th>
                                <th>Status</th>
                                <th width="20%">Action</th>
=======
                                <th colspan="2">Disaster Name</th>
                                <th class="w-6">Status</th>
                                <th class="w-4">Action</th>
>>>>>>> Stashed changes
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            @if (auth()->user()->status == 'Active')
                @include('userpage.disaster.disasterModal')
            @endif
            @include('userpage.changePasswordModal')
        </div>
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
    @include('userpage.changePasswordModal')
    @include('partials.toastr')
    <script>
        let disasterTable = $('.disasterTable').DataTable({
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            processing: false,
            serverSide: true,
            ajax: "{{ route('disaster.display') }}",
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
                    name: 'status'
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
            let disasterId, defaultFormData, current_status, status;

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let validator = $("#disasterForm").validate({
                rules: {
                    name: {
                        required: true
                    }
                },
                messages: {
                    name: {
                        required: 'Please enter Disaster Name.'
                    }
                },
                errorElement: 'span',
                submitHandler: disasterFormHandler
            });

            $(document).on('click', '#createDisasterData', function() {
                $('.modal-header').removeClass('bg-yellow-500').addClass('bg-green-600');
                $('.modal-title').text('Add Disaster');
                $('#submitDisasterBtn').removeClass('btn-edit').addClass('btn-submit').text('Add');
                $('#operation').val('create');
                $('#disasterModal').modal('show');
            });

            $(document).on('click', '.updateDisaster', function() {
                let data = getRowData(this);
                disasterId = data['id'];
                $('.modal-header').removeClass('bg-green-600').addClass('bg-yellow-500');
                $('.modal-title').text('Edit Disaster');
                $('#submitDisasterBtn').removeClass('btn-submit').addClass('btn-edit').text('Save');
                $('#disasterName').val(data['name']);
                $('#operation').val('update');
                $('#disasterModal').modal('show');
                defaultFormData = $('#disasterForm').serialize();
            });

            $(document).on('click', '.removeDisaster', function() {
                disasterId = getRowData(this)['id'];
                alterDisasterData('remove');
            });

            $(document).on('change', '.changeDisasterStatus', function() {
                disasterId = getRowData(this)['id'];
                current_status = getRowData(this)['status']
                status = $(this).val();
                alterDisasterData('change');
            });

            $('#disasterModal').on('hidden.bs.modal', function() {
                validator.resetForm();
                $('#disasterForm')[0].reset();
            });

            function alterDisasterData(operation) {
                confirmModal(`Do you want to ${operation} this disaster?`).then((result) => {
                    if (operation == 'change' && status == current_status) {
                        toastr.warning('No changes were made.', 'Warning');
                        $('.changeDisasterStatus').val('');
                        return;
                    }
                    if (result.isConfirmed) {
                        let url, type;

                        if (operation == 'remove') {
                            url = "{{ route('disaster.remove', ':disasterId') }}"
                                .replace(':disasterId', disasterId);
                            type = "PATCH";
                        } else {
                            url = "{{ route('disaster.change.status', ':disasterId') }}"
                                .replace(':disasterId', disasterId);
                            type = "PATCH";
                        }

                        $.ajax({
                            type: type,
                            data: {
                                status: status
                            },
                            url: url,
                            success: function() {
                                toastr.success(`Disaster successfully ${operation}d.`, 'Success');
                                disasterTable.draw();
                            },
                            error: function() {
                                toastr.error(
                                    'An error occurred while processing your request.',
                                    'Error');
                            }
                        });
                    } else {
                        $('.changeDisasterStatus').val('');
                    }
                });
            }

            function disasterFormHandler(form) {
                let operation = $('#operation').val(),
                    url = "",
                    type = "",
                    formData = $(form).serialize();

                url = operation == 'create' ? "{{ route('disaster.create') }}" :
                    "{{ route('disaster.update', 'disasterId') }}".replace('disasterId',
                        disasterId);

                type = operation == 'create' ? "POST" : "PUT";

                confirmModal(`Do you want to ${operation} this disaster?`).then((result) => {
                    if (result.isConfirmed) {
                        if (operation == 'update' && defaultFormData == formData) {
                            toastr.warning('No changes were made.', 'Warning');
                            return;
                        }
                        $.ajax({
                            data: formData,
                            url: url,
                            type: type,
                            success: function(response) {
                                if (response.status == 'success') {
                                    toastr.success(`Disaster successfully ${operation}d.`, 'Success');
                                    $('#disasterModal').modal('hide');
                                    disasterTable.draw();
                                } else if (response.status == 'warning') {
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

            function getRowData(element) {
                let currentRow = $(element).closest('tr');

                if (disasterTable.responsive.hasHidden()) {
                    currentRow = currentRow.prev('tr');
                }

                return disasterTable.row(currentRow).data();
            }
        @endif
    </script>
</body>

</html>
