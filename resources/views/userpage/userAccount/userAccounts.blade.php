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
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-person-{{ $operation == 'active' ? 'gear' : 'slash' }}"></i>
                    </div>
                </div>
                <span>{{ $operation == 'active' ? 'MANAGE' : 'ARCHIVED' }} ACCOUNT</span>
            </div>
            <hr>
            @if ($operation == 'active')
                <div class="page-button-container">
                    <button class="btn-submit" id="createUserAccount">
                        <i class="bi bi-person-fill-add"></i>
                        Create User Account
                    </button>
                </div>
            @endif
            <section class="table-container">
                <div class="table-content">
                    <header class="table-label">{{ auth()->user()->organization == 'CDRRMO' ? 'CDRRMO' : 'User' }}
                        Accounts Table</header>
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
            </section>
            @include('userpage.userAccount.userAccountModal')
            @include('userpage.changePasswordModal')
        </main>
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
            let accountTable = $('#accountTable').DataTable({
                ordering: false,
                responsive: true,
                processing: false,
                serverSide: true,
                ajax: "{{ route('account.display.users', $operation) }}",
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
                ]
            });
            let userId, validator, defaultFormData,
                operation, modal = $('#userAccountModal'),
                form = $('#accountForm'),
                organizationContainer = $('#organization-container'),
                positionContainer = $('#position-container'),
                nameContainer = $('#name-container'),
                emailContainer = $('#email-container'),
                positionInput = $('#position'),
                modalLabelContainer = $('.modal-label-container'),
                modalLabel = $('.modal-label'),
                formButton = $('#saveProfileDetails');

            validator = form.validate({
                rules: {
                    organization: 'required',
                    position: 'required',
                    name: 'required',
                    email: 'required'
                },
                messages: {
                    organization: 'Please select an organization.',
                    position: 'Please select a position.',
                    name: 'Please enter full name.',
                    email: 'Please enter an email address.'
                },
                errorElement: 'span',
                submitHandler(form) {

                    confirmModal(`Do you want to ${operation} this user details?`).then((result) => {
                        if (!result.isConfirmed) return;

                        let formData = $(form).serialize();

                        return operation == 'update' && defaultFormData == formData ?
                            showWarningMessage() :
                            $.ajax({
                                data: formData,
                                url: operation == 'create' ?
                                    "{{ route('account.create') }}" :
                                    "{{ route('account.update', 'userId') }}".replace(
                                        'userId', userId),
                                method: operation == 'create' ? "POST" : "PUT",
                                success(response) {
                                    response.status == "warning" ? showWarningMessage(
                                        response.message) : (showSuccessMessage(
                                        `Successfully ${operation}d user account.`
                                    ), modal.modal('hide'), accountTable.draw())
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
                    case 'activeAccount':
                        ajaxRequest('active',
                            "{{ route('account.active', ['userId', 'active']) }}"
                            .replace(
                                'userId', userId));
                        break;

                    case 'inactiveAccount':
                        ajaxRequest('inactive',
                            "{{ route('account.active', ['userId', 'inactive']) }}"
                            .replace(
                                'userId', userId));
                        break;

                    case 'updateAccount':
                        changeModalProperties('Update User Account', 'Update');
                        positionContainer.add(nameContainer).add(emailContainer).prop('hidden',
                            0);
                        initPositionOption(organization);
                        $('#organization').val(organization);
                        $('#position').val(position);
                        $('#name').val(name);
                        $('#email').val(email);
                        operation = "update";
                        defaultFormData = form.serialize();
                        modal.modal('show');
                        break;

                    case 'archiveAccount':
                        ajaxRequest('archive',
                            "{{ route('account.archive', ['userId', 'archive']) }}"
                            .replace('userId', userId));
                        break;

                    case 'unArchiveAccount':
                        ajaxRequest('unarchive',
                            "{{ route('account.archive', ['userId', 'unarchive']) }}"
                            .replace(
                                'userId', userId));
                        break;
                }
            });

            $('#organization').change(function() {
                initPositionOption($(this).val());
                positionContainer.add(nameContainer).add(emailContainer).prop('hidden', 0);
            });

            $('#createUserAccount').click(() => {
                modalLabelContainer.removeClass('bg-warning');
                modalLabel.text('Create User Account');
                formButton.addClass('btn-submit').removeClass('btn-update').text('Create');
                operation = "create";
                modal.modal('show');
            });

            modal.on('hidden.bs.modal', () => {
                positionContainer.add(nameContainer).add(emailContainer).prop('hidden', 1);
                organizationContainer.prop('hidden', 0);
                $('.actionSelect').val('');
                form[0].reset();
            });

            function checkPosition(position) {
                return position == "CSWD" ? '<option value="Focal">Focal</option>' :
                    '<option value="President">President</option><option value="Vice President">Vice President</option>';
            }

            function changeModalProperties(headerText, buttonText) {
                modalLabelContainer.removeClass('bg-success').addClass('bg-warning');
                modalLabel.text(headerText);
                formButton.removeClass('btn-submit').addClass('btn-update').text(buttonText);
            }

            function initPositionOption(organization) {
                positionInput.empty();
                positionInput.append(checkPosition(organization));
            }

            function ajaxRequest(operation, url) {
                confirmModal(`Do you want to ${operation} this account?`).then((result) => {
                    return !result.isConfirmed ? $('.actionSelect').val('') :
                        $.ajax({
                            method: "PATCH",
                            url: url,
                            success() {
                                showSuccessMessage(
                                    `Successfully ${operation}${operation == "open" ? 'ed' : 'd'} account.`
                                );
                                accountTable.draw();
                            },
                            error: showErrorMessage
                        })
                });
            }
        });
    </script>
</body>

</html>
