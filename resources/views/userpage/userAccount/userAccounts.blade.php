<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <div class="main-content">
            <div class="grid grid-cols-1">
                <div class="grid col-end-1">
                    <div class="text-2xl text-white">
                        <i class="bi bi-person-gear p-2 bg-slate-600"></i>
                    </div>
                </div>
                <span class="text-xl font-bold">MANAGE ACCOUNTS</span>
            </div>
            <hr class="mt-4 mb-3">
            @if (auth()->user()->is_disable == 0)
                <div class="create-section">
                    <button class="btn-submit p-2 createUserAccount">
                        <i class="bi bi-person-fill-add pr-2"></i>
                        Create User Account
                    </button>
                </div>
            @endif
            <div class="table-container p-3 shadow-lg rounded-lg">
                <div class="block w-full overflow-auto pb-2">
                    <header class="text-2xl font-semibold mb-3">User Accounts Table</header>
                    <table class="table accountTable" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th colspan="2">Email Address</th>
                                <th>Organization</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                @if (auth()->user()->is_disable == 0)
                    @include('userpage.userAccount.userAccountModal')
                @endif
                @include('userpage.changePasswordModal')
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    @include('partials.toastr')
    @auth
        <script>
            $(document).ready(function() {
                let accountTable = $('.accountTable').DataTable({
                    language: {
                        emptyTable: '<div class="no-data">No accounts added yet.</div>',
                    },
                    ordering: false,
                    responsive: true,
                    processing: false,
                    serverSide: true,
                    ajax: "{{ route('account.display.users') }}",
                    columns: [{
                            data: 'id',
                            name: 'id',
                            visible: false
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'organization',
                            name: 'organization'
                        },
                        {
                            data: 'position',
                            name: 'position'
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
                @if (auth()->user()->is_disable == 0)
                    let userId, defaultFormData, dateSuspendTime = datePicker("#suspend");

                    let validator = $("#accountForm").validate({
                        rules: {
                            organization: {
                                required: true
                            },
                            position: {
                                required: true
                            },
                            email: {
                                required: true
                            },
                            suspend_time: {
                                required: true
                            }
                        },
                        messages: {
                            organization: {
                                required: 'Please select an organization.'
                            },
                            position: {
                                required: 'Please select a position.'
                            },
                            email: {
                                required: 'Please enter an email address.'
                            },
                            suspend_time: {
                                required: 'Please enter a suspension time.'
                            }
                        },
                        errorElement: 'span',
                        submitHandler: formSubmitHandler
                    });

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $(document).on('change', '.actionSelect', function() {
                        let selectedAction = $(this).val();
                        let currentRow = $(this).closest('tr');

                        if (accountTable.responsive.hasHidden())
                            currentRow = currentRow.prev('tr');

                        let data = accountTable.row(currentRow).data();
                        userId = data['id'];

                        if (selectedAction == 'disableAccount') {
                            confirmModal('Do you want to disable this account?').then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        type: "PUT",
                                        url: "{{ route('account.disable', ':userId') }}"
                                            .replace(':userId', userId),
                                        success: function() {
                                            toastr.success('Successfully disabled account.',
                                                'Success');
                                            accountTable.draw();
                                        },
                                        error: function() {
                                            showErrorMessage();
                                        }
                                    });
                                } else {
                                    $('.actionSelect').val('');
                                }
                            });
                        } else if (selectedAction == 'enableAccount') {
                            confirmModal('Do you want to enable this account?').then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        type: "PUT",
                                        url: "{{ route('account.enable', ':userId') }}"
                                            .replace(':userId', userId),
                                        success: function() {
                                            toastr.success('Successfully enabled account.',
                                                'Success');
                                            accountTable.draw();
                                        },
                                        error: function() {
                                            showErrorMessage();
                                        }
                                    });
                                } else {
                                    $('.actionSelect').val('');
                                }
                            });
                        } else if (selectedAction == 'updateAccount') {
                            $('.modal-header').removeClass('bg-green-600').addClass('bg-yellow-500');
                            $('.modal-title').text('Update User Account');
                            $('#saveProfileDetails').removeClass('btn-submit').addClass('btn-update').text(
                                'Update');
                            $('#suspend-container').prop('hidden', true);
                            $('#organization').val(data['organization']);
                            $('#position').val(data['position']);
                            $('#email').val(data['email']);
                            $('#account_operation').val('update');
                            $('#userAccountModal').modal('show');
                            defaultFormData = $('#accountForm').serialize();
                        } else if (selectedAction == 'removeAccount') {
                            confirmModal('Do you want to remove this user account?').then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        type: "DELETE",
                                        url: "{{ route('account.remove', ':userId') }}"
                                            .replace(':userId', userId),
                                        success: function() {
                                            toastr.success('Successfully removed account.',
                                                'Success');
                                            accountTable.draw();
                                        },
                                        error: function() {
                                            showErrorMessage();
                                        }
                                    });
                                } else {
                                    $('.actionSelect').val('');
                                }
                            });
                        } else if (selectedAction == 'suspendAccount') {
                            $('.modal-header').removeClass('bg-green-600').addClass('bg-yellow-500');
                            $('.modal-title').text('Suspend User Account');
                            $('#saveProfileDetails').removeClass('btn-submit').addClass('btn-update').text(
                                'Suspend');
                            $('#organization').val(data['organization']);
                            $('#position').val(data['position']);
                            $('#email').val(data['email']);
                            $('#account_operation').val('suspend');
                            $('#userAccountModal').modal('show');
                            defaultFormData = $('#accountForm').serialize();
                        } else if (selectedAction == 'openAccount') {
                            confirmModal('Do you want to open this user account?').then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        type: "PUT",
                                        url: "{{ route('account.open', ':userId') }}"
                                            .replace(':userId', userId),
                                        success: function() {
                                            toastr.success('Successfully opened account.',
                                                'Success');
                                            accountTable.draw();
                                        },
                                        error: function() {
                                            showErrorMessage();
                                        }
                                    });
                                } else {
                                    $('.actionSelect').val('');
                                }
                            });
                        }
                    });

                    $(document).on('click', '.createUserAccount', function() {
                        $('.modal-header').removeClass('bg-yellow-500').addClass('bg-green-600');
                        $('.modal-title').text('Create User Account');
                        $('#saveProfileDetails').removeClass('btn-update').addClass('btn-submit').text(
                            'Create');
                        $('#suspend-container').prop('hidden', true);
                        $('#suspend').prop('disabled', true);
                        $('#account_operation').val('create');
                        $('#userAccountModal').modal('show');
                    });

                    $('#userAccountModal').on('hidden.bs.modal', function() {
                        validator.resetForm();
                        $('#suspend-container').prop('hidden', false);
                        $('#suspend').prop('disabled', false);
                        $('.actionSelect').val('');
                        $('#accountForm')[0].reset();
                    });

                    function formSubmitHandler(form) {
                        let operation = $('#account_operation').val(),
                            url, type, formData = $(form).serialize(),
                            modal = $('#userAccountModal');

                        url = operation == 'create' ? "{{ route('account.create') }}" :
                            operation == 'update' ? "{{ route('account.update', 'userId') }}".replace('userId',
                                userId) :
                            "{{ route('account.suspend', 'userId') }}".replace('userId', userId);

                        type = operation == 'create' ? "POST" : "PUT";

                        confirmModal(`Do you want to ${operation} this user details?`).then((result) => {
                            if (result.isConfirmed) {
                                if (operation == 'update' && defaultFormData == formData) {
                                    showWarningMessage('No changes were made.');
                                    return;
                                }
                                $.ajax({
                                    data: formData,
                                    url: url,
                                    type: type,
                                    success: function(response) {
                                        if (response.status == "warning") {
                                            showWarningMessage(response.message);
                                        } else {
                                            showSuccessMessage(`Successfully ${operation}${operation == 'create' ? 'd' : operation == 'update' ? 'd' : 'ed'} user account.`);
                                            modal.modal('hide');
                                            accountTable.draw();
                                        }
                                    },
                                    error: function() {
                                        showErrorMessage();
                                    }
                                });
                            }
                        });
                    }
                @endif
            });
        </script>
    @endauth
</body>

</html>
