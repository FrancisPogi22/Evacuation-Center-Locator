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
        <main class="main-content">
            <div class="label-container">
                <i class="bi bi-person-gear"></i>
                <span>MANAGE USERS ACCOUNT</span>
            </div>
            <hr>
            @if (auth()->user()->is_disable == 0)
                <div class="page-button-container">
                    <button class="btn-submit" id="createUserAccount">
                        <i class="bi bi-person-fill-add"></i>
                        Create User Account
                    </button>
                </div>
            @endif
            <section class="table-container">
                <div class="table-content">
                    <header class="table-label">User Accounts Table</header>
                    <table class="table" id="accountTable" width="100%">
                        <thead>
                            <tr>
                                <th colspan="2">Name</th>
                                <th>Email Address</th>
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
                @include('userpage.userAccount.userAccountModal')
                @include('userpage.changePasswordModal')
            </section>
        </main>
    </div>

    @include('partials.script')
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
    <script>
        $(document).ready(() => {
            let accountTable = $('#accountTable').DataTable({
                language: {
                    emptyTable: '<div class="message-text">No accounts added yet.</div>'
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
                        data: 'name',
                        name: 'name'
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
                ],
                columnDefs: [{
                    targets: 5,
                    visible: {{ auth()->user()->is_disable }} == 0 ? true : false
                }]
            });
            @if (auth()->user()->is_disable == 0)
                let userId, validator, defaultFormData, dateSuspendTime = datePicker("#suspend"),
                    operation, modal = $('#userAccountModal'),
                    modalLabelContainer = $('.modal-label-container'),
                    modalLabel = $('.modal-label'),
                    formButton = $('#saveProfileDetails');

                validator = $("#accountForm").validate({
                    rules: {
                        organization: 'required',
                        position: 'required',
                        name: 'required',
                        email: 'required',
                        suspend_time: 'required'
                    },
                    messages: {
                        organization: 'Please select an organization.',
                        position: 'Please select a position.',
                        name: 'Please enter name.',
                        email: 'Please enter an email address.',
                        suspend_time: 'Please enter a suspension time.'
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
                    let selectedAction = $(this).val(),
                        {
                            id,
                            organization,
                            position,
                            name,
                            email
                        } = getRowData(this, accountTable);
                    userId = id;

                    switch (selectedAction) {
                        case 'disableAccount':
                            confirmModal('Do you want to disable this account?').then((result) => {
                                return !result.isConfirmed ? $(this).val('') :
                                    $.ajax({
                                        type: "PATCH",
                                        url: "{{ route('account.disable', 'userId') }}"
                                            .replace('userId', userId),
                                        success() {
                                            showSuccessMessage(
                                                'Successfully disabled account.');
                                            accountTable.draw();
                                        },
                                        error() {
                                            showErrorMessage();
                                        }
                                    })
                            });
                            break;

                        case 'enableAccount':
                            confirmModal('Do you want to enable this account?').then((result) => {
                                return !result.isConfirmed ? $(this).val('') :
                                    $.ajax({
                                        type: "PATCH",
                                        url: "{{ route('account.enable', 'userId') }}"
                                            .replace('userId', userId),
                                        success() {
                                            showSuccessMessage(
                                                'Successfully enabled account.');
                                            accountTable.draw();
                                        },
                                        error() {
                                            showErrorMessage();
                                        }
                                    });
                            });
                            break;

                        case 'updateAccount':
                            modalLabelContainer.removeClass('bg-success').addClass('bg-warning');
                            modalLabel.text('Update User Account');
                            formButton.removeClass('btn-submit').addClass('btn-update').text('Update');
                            $('#suspend-container').prop('hidden', true);
                            $('#organization').val(organization);
                            $('#position').val(position);
                            $('#name').val(name);
                            $('#email').val(email);
                            operation = "update";
                            defaultFormData = $('#accountForm').serialize();
                            modal.modal('show');
                            break;

                        case 'archiveAccount':
                            confirmModal('Do you want to archive this user account?').then((result) => {
                                return !result.isConfirmed ? $(this).val('') :
                                    $.ajax({
                                        type: "PATCH",
                                        url: "{{ route('account.archive', 'userId') }}"
                                            .replace('userId', userId),
                                        success() {
                                            showSuccessMessage(
                                                'Successfully archive account.');
                                            accountTable.draw();
                                        },
                                        error() {
                                            showErrorMessage();
                                        }
                                    });
                            });
                            break;

                        case 'suspendAccount':
                            modalLabelContainer.removeClass('bg-success').addClass('bg-warning');
                            modalLabel.text('Suspend User Account');
                            formButton.removeClass('btn-submit').addClass('btn-update').text('Suspend');
                            $('#organization').val(organization);
                            $('#position').val(position);
                            $('#name').val(name);
                            $('#email').val(email);
                            operation = "suspend";
                            defaultFormData = $('#accountForm').serialize();
                            modal.modal('show');
                            break;

                        case 'openAccount':
                            confirmModal('Do you want to open this user account?').then((result) => {
                                return !result.isConfirmed ? $(this).val('') :
                                    $.ajax({
                                        type: "PATCH",
                                        url: "{{ route('account.open', 'userId') }}"
                                            .replace('userId', userId),
                                        success() {
                                            showSuccessMessage(
                                                'Successfully opened account.');
                                            accountTable.draw();
                                        },
                                        error() {
                                            showErrorMessage();
                                        }
                                    });
                            });
                            break;

                        default:
                            break;
                    }
                });

                $(document).on('click', '#createUserAccount', () => {
                    modalLabelContainer.removeClass('bg-warning');
                    modalLabel.text('Create User Account');
                    formButton.addClass('btn-submit').removeClass('btn-update').text('Create');
                    $('#suspend-container, #suspend').prop('hidden', true).prop('disabled', true);
                    operation = "create";
                    modal.modal('show');
                });

                modal.on('hidden.bs.modal', () => {
                    validator.resetForm();
                    $('#suspend-container, #suspend').prop('hidden', false).prop('disabled', false);
                    $('.actionSelect').val('');
                    $('#accountForm')[0].reset();
                });

                function formSubmitHandler(form) {
                    let formData = $(form).serialize();
                    let url = {
                        create: "{{ route('account.create') }}",
                        update: "{{ route('account.update', 'userId') }}".replace('userId', userId),
                        suspend: "{{ route('account.suspend', 'userId') }}".replace('userId', userId)
                    } [operation];
                    let type = operation == 'create' ? "POST" : "PUT";

                    confirmModal(`Do you want to ${operation} this user details?`).then((result) => {
                        if (!result.isConfirmed) return;

                        return operation == 'update' && defaultFormData == formData ?
                            showWarningMessage('No changes were made.') :
                            $.ajax({
                                data: formData,
                                url: url,
                                type: type,
                                success(response) {
                                    response.status == "warning" ? showWarningMessage(response
                                        .message) : (showSuccessMessage(
                                        `Successfully ${operation}${operation == 'create' ? 'd' : operation == 'update' ? 'd' : 'ed'} user account.`
                                    ), modal.modal('hide'), accountTable.draw())
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
