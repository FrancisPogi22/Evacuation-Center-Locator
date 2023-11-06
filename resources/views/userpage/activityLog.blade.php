<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <main class="main-content">
            <div class="label-container">
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-card-list"></i>
                    </div>
                </div>
                <span>USER ACTIVITY LOG</span>
            </div>
            <hr>
            <section class="table-container">
                <div class="table-content">
                    <header class="table-label">User Activity Log Table</header>
                    <table class="table" id="activityTable" width="100%">
                        <thead>
                            <tr>
                                <th colspan="3">Activity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
        @include('userpage.userAccount.userAccountModal')
        @include('userpage.changePasswordModal')
    </div>

    @include('partials.script')
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
            let userId, defaultFormData, dateSuspendTime = datePicker("#suspend"),
                modal = $('#userAccountModal'),
                activityLogTable = $('#activityTable').DataTable({
                    ordering: false,
                    responsive: true,
                    processing: false,
                    serverSide: true,
                    ajax: "{{ route('activity.log') }}",
                    columns: [{
                            data: 'id',
                            name: 'id',
                            visible: false
                        },
                        {
                            data: 'user_id',
                            name: 'user_id',
                            visible: false,
                            searchable: false
                        },
                        {
                            data: 'activity',
                            name: 'activity',
                        },
                        {
                            data: 'action',
                            name: 'action',
                            width: '10%',
                            orderable: false,
                            searchable: false
                        }
                    ]
                }),
                validator = $("#accountForm").validate({
                    rules: {
                        suspend_time: 'required'
                    },
                    messages: {
                        suspend_time: 'Please enter a suspension time.'
                    },
                    errorElement: 'span',
                    submitHandler(form) {
                        confirmModal(`Do you want to suspend this user?`).then((result) => {
                            if (!result.isConfirmed) return;

                            $.ajax({
                                data: $(form).serialize(),
                                url: "{{ route('account.suspend', 'userId') }}".replace(
                                    'userId', userId),
                                method: "PUT",
                                success(response) {
                                    response.status == 'warning' ?
                                        showWarningMessage(response.message) :
                                        (showSuccessMessage(`User successfully suspended.`),
                                            $('#closeModalBtn').click(), activityLogTable.draw()
                                        );
                                },
                                error: showErrorMessage
                            });
                        });
                    }
                });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on('click', '#disableBtn', function() {
                confirmModal("Do you want to disable this user?")
                    .then((result) => {
                        if (!result.isConfirmed) return;

                        $.ajax({
                            method: "PATCH",
                            url: "{{ route('account.disable', 'userId') }}".replace('userId',
                                getRowData(this, activityLogTable).user_id),
                            success(response) {
                                response.status == 'warning' ?
                                    showWarningMessage(response.message) :
                                    (activityLogTable.draw(), showSuccessMessage(
                                        'User successfully disabled.'));
                            },
                            error: showErrorMessage
                        });
                    });

            });

            $(document).on('click', '#suspendBtn', function() {
                $('.modal-label-container').removeClass('bg-success').addClass('bg-warning');
                $('.modal-label').text('Suspend User Account');
                $('#saveProfileDetails').removeClass('btn-submit').addClass('btn-update').text('Suspend');
                $('#organization-container').add('#position-container').add('#name-container').add(
                    '#email-container').prop('hidden', 1);
                $('#userAccountModal').modal('show');
                defaultFormData = $('#accountForm').serialize();
                userId = getRowData(this, activityLogTable).user_id;
            });

            $(document).on('change', '#suspend', () => {
                $('.flatpickr-calendar').prop('hidden', 1);
            });
        });
    </script>
</body>

</html>
