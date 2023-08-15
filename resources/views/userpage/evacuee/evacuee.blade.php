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
            <div class="label-container">
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <span>MANAGE EVACUEE INFORMATION</span>
            </div>
            <hr>
            <div class="page-button-container">
                <button id="recordEvacueeBtn" data-toggle="modal" data-target="#evacueeInfoFormModal"
                    class="btn-submit">
                    <i class="bi bi-person-down "></i>
                    Record Evacuees Info
                </button>
            </div>
            <div class="table-container">
                <div class="table-content">
                    <header class="table-label">Evacuees Informations Table</header>
                    <table class="table evacueeTable" width="100%">
                        <thead class="thead-light">
                            <tr class="table-row">
                                <th colspan="2">Barangay</th>
                                <th>Date Entry</th>
                                <th>Evacuation Assigned</th>
                                <th>Families</th>
                                <th>No. of Individuals</th>
                                <th>Male</th>
                                <th>Female</th>
                                <th>Senior Citizen</th>
                                <th>Minors</th>
                                <th>Infants</th>
                                <th>PWD</th>
                                <th>Pregnant</th>
                                <th>Lactating</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @include('userpage.evacuee.evacueeInfoFormModal')
        @include('userpage.changePasswordModal')
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
            let evacueeId, defaultFormData, modal = $('#evacueeInfoFormModal'),
                dateEntryInput = datePicker("#date_entry");
            const fieldNames = [
                    'infants', 'minors', 'senior_citizen', 'pwd',
                    'pregnant', 'lactating', 'families', 'individuals',
                    'male', 'female'
                ],
                rules = {
                    disaster_id: 'required',
                    date_entry: 'required',
                    barangay: 'required',
                    evacuation_assigned: 'required'
                },
                messages = {
                    disaster_id: 'Please select disaster.',
                    date_entry: 'Please enter date entry.',
                    barangay: 'Please enter barangay.',
                    evacuation_assigned: 'Please enter evacuation center assigned.'
                };

            let evacueeTable = $('.evacueeTable').DataTable({
                language: {
                    emptyTable: '<div class="message-text">No evacuees data added yet.</div>'
                },
                ordering: false,
                responsive: true,
                processing: false,
                serverSide: true,
                ajax: "{{ route('evacuee.info.get') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        visible: false
                    },
                    {
                        data: 'barangay',
                        name: 'barangay'
                    },
                    {
                        data: 'date_entry',
                        name: 'date_entry'
                    },
                    {
                        data: 'evacuation_assigned',
                        name: 'evacuation_assigned'
                    },
                    {
                        data: 'families',
                        name: 'families',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'individuals',
                        name: 'individuals',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'male',
                        name: 'male',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'female',
                        name: 'female',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'senior_citizen',
                        name: 'senior_citizen',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'minors',
                        name: 'minors',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'infants',
                        name: 'infants',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'pwd',
                        name: 'pwd',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'pregnant',
                        name: 'pregnant',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'lactating',
                        name: 'lactating',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '1rem'
                    }
                ],
            });

            fieldNames.forEach(fieldName => {
                rules[fieldName] = {
                    required: true,
                    number: true
                };
                messages[fieldName] = {
                    required: `Please enter ${fieldName}.`,
                    number: `Please enter a valid number for ${fieldName}.`
                };
            });

            const validator = $("#evacueeInfoForm").validate({
                rules,
                messages,
                errorElement: 'span',
                submitHandler: formSubmitHandler
            });

            $(document).on('click', '#recordEvacueeBtn', function() {
                $('.modal-label-container').removeClass('bg-warning').addClass('bg-success');
                $('.modal-label').text('Record Evacuee Information');
                $('#recordEvacueeInfoBtn').removeClass('btn-update').addClass('btn-submit').text('Record');
                $('#operation').val('record');
                modal.modal('show');
            });

            $(document).on('click', '#updateEvacueeBtn', function() {
                $('.modal-label-container').removeClass('bg-success').addClass('bg-warning');
                $('.modal-label').text('Update Evacuee Information');
                $('#recordEvacueeInfoBtn').removeClass('btn-submit').addClass('btn-update').text('Update');

                let data = getRowData(this, evacueeTable);
                evacueeId = data.id;

                for (const index in data) {
                    if (['action', 'DT_RowIndex', 'id'].includes(index)) continue;

                    const fields = index == 'barangay' ? 'select[name="barangay"]' : index ==
                        'evacuation_assigned' ? 'select[name="evacuation_assigned"]' :
                        `#${index}`;

                    $(fields).val(data[index]);
                }

                $('#operation').val('update');
                modal.modal('show');
                defaultFormData = $('#evacueeInfoForm').serialize();
            });

            modal.on('hidden.bs.modal', function() {
                validator.resetForm();
                $('#evacueeInfoForm')[0].reset();
            });

            function formSubmitHandler(form) {
                let operation = $('#operation').val(),
                    formData = $(form).serialize();
                let url = operation == 'record' ? "{{ route('evacuee.info.record') }}" :
                    "{{ route('evacuee.info.update', 'evacueeId') }}".replace('evacueeId', evacueeId);
                let type = operation == 'record' ? "POST" : "PUT";

                confirmModal(`Do you want to ${operation} this evacuee info?`).then((result) => {
                    if (result.isConfirmed) {
                        return operation == 'update' && defaultFormData == formData ?
                            showWarningMessage('No changes were made.') :
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: formData,
                                url,
                                type,
                                success(response) {
                                    response.status == 'warning' ? showWarningMessage(response
                                        .message) : (modal.modal('hide'), evacueeTable.draw(),
                                        showSuccessMessage(
                                            `Successfully ${operation}${operation == 'record' ? 'ed new' : 'd the'} evacuee info.`
                                        ));
                                },
                                error() {
                                    showErrorMessage();
                                }
                            });
                    }
                });
            }
        });
    </script>
</body>

</html>
