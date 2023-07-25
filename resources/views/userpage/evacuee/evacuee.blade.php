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
        @include('sweetalert::alert')
        @include('partials.header')
        @include('partials.sidebar')
        <div class="main-content">
            <div class="grid grid-cols-1">
                <div class="grid col-end-1">
                    <div class="text-2xl text-white">
                        <i class="bi bi-people p-2 bg-slate-600"></i>
                    </div>
                </div>
                <span class="text-xl font-bold">MANAGE EVACUEE INFORMATION</span>
            </div>
            <hr class="mt-4">
            <div class="flex flex-wrap justify-end text-white gap-3 my-3">
                <button id="returnEvacueeBtn" class="btn-primary p-2">
                    <i class="bi bi-person-up pr-2"></i>
                    Returning Home
                </button>
                <button id="recordEvacueeBtn" data-toggle="modal" data-target="#evacueeInfoFormModal"
                    class="btn-submit p-2">
                    <i class="bi bi-person-down pr-2"></i>
                    Record Evacuee Info
                </button>
            </div>
            @include('userpage.evacuee.evacueeInfoFormModal')
            <div class="table-container p-3 shadow-lg rounded-lg">
                <div class="block w-full overflow-auto pb-2">
                    <header class="text-2xl font-semibold mb-3">Evacuee Informations</header>
                    <table id="evacueeTable" class="table" width="100%">
                        <thead class="thead-light">
                            <tr class="table-row">
                                <th>Id</th>
                                <th>House Hold #</th>
                                <th>
                                    <input type="checkbox" class="w-4 h-4 accent-blue-600" id="selectAllCheckBox">
                                </th>
                                <th>Full Name</th>
                                <th>Sex</th>
                                <th>Age</th>
                                <th>Barangay</th>
                                <th>Date Entry</th>
                                <th>Date Out</th>
                                <th>Disaster Type</th>
                                <th>Disaster Id</th>
                                <th>Disaster Info</th>
                                <th>Evacuation Center</th>
                                <th>4Ps</th>
                                <th>PWD</th>
                                <th>Pregnant</th>
                                <th>Lactating</th>
                                <th>Student</th>
                                <th>Working</th>
                                <th class="w-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="evacueeArchiveSelect flex flex-wrap justify-end font-semibold gap-3 my-3">
                <select name="archiveEvacueeDataTyphoon" class="form-select" id="archiveEvacueeDataTyphoon">
                    <option value="None">Select Typhoon</option>
                    {{-- @foreach ($typhoonList as $typhoon)
                        <option value="{{ $typhoon->name }}">
                            {{ $typhoon->name }}</option>
                    @endforeach --}}
                </select>
                <select name="archiveEvacueeDataFlashflood" class="form-select" id="archiveEvacueeDataFlashflood">
                    <option value="None">Select Flashflood Location</option>
                    {{-- @foreach ($flashfloodList as $flashflood)
                        <option value="{{ $flashflood->location }}">
                            {{ $flashflood->location }}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="table-container p-3 shadow-lg rounded-lg">
                <div class="block w-full overflow-auto pb-2">
                    <header class="text-2xl font-semibold mb-3">Archived Evacuee Informations</header>
                    <table id="archivedEvacueeTable" class="table" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th>House Hold #</th>
                                <th>Full Name</th>
                                <th>Sex</th>
                                <th>Age</th>
                                <th>Barangay</th>
                                <th>Date Entry</th>
                                <th>Date Out</th>
                                <th>Disaster Type</th>
                                <th>Disaster Info</th>
                                <th>Evacuation Center</th>
                                <th>4Ps</th>
                                <th>PWD</th>
                                <th>Pregnant</th>
                                <th>Lactating</th>
                                <th>Student</th>
                                <th>Working</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @include('userpage.changePasswordModal')
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
    <script>
        $(document).ready(function() {
<<<<<<< Updated upstream
            let evacueeTable = $('.evacueeTable').DataTable({
                order: [
                    [1, 'asc']
                ],
                language: {
                    emptyTable: 'No evacuee data recorded'
                },
=======
            let evacueeTable = $('#evacueeTable').DataTable({
                ordering: false,
>>>>>>> Stashed changes
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
                        data: 'house_hold_number',
                        name: 'house_hold_number',
                        width: '8%'
                    },
                    {
                        data: 'select',
                        name: 'select',
                        orderable: false,
                        searchable: false,
                        selectRow: true
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'sex',
                        name: 'sex'
                    },
                    {
                        data: 'age',
                        name: 'age'
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
                        data: 'date_out',
                        name: 'date_out'
                    },
                    {
                        data: 'disaster_type',
                        name: 'disaster_type'
                    },
                    {
                        data: 'disaster_id',
                        name: 'disaster_id',
                        visible: false
                    },
                    {
                        data: 'disaster_info',
                        name: 'disaster_info'
                    },
                    {
                        data: 'evacuation_assigned',
                        name: 'evacuation_assigned'
                    },
                    {
                        data: 'fourps',
                        name: 'fourps',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'PWD',
                        name: 'PWD',
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
                        data: 'student',
                        name: 'student',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'working',
                        name: 'working',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],

                drawCallback: function() {
                    $('#selectAllCheckBox').prop('checked', false);
                    convertBooleanToYesNo(this.api(), 'evacueeTable');
                }
            });

            function convertBooleanToYesNo(api, table) {
                api.rows().every(function() {
                    let columnIndex,
                        columnName = [
                            '4Ps',
                            'PWD',
                            'pregnant',
                            'lactating',
                            'student',
                            'working'
                        ];

                    table == 'evacueeTable' ?
                        columnIndex = 11 :
                        columnIndex = 10;


                    for (let i = 0; i < columnName.length; i++) {
                        this.data()[columnName[i]] == 1 ?
                            this.data()[columnName[i]] = 'Yes' :
                            this.data()[columnName[i]] = 'No';
                        $(`td:eq(${i+columnIndex})`, this.node()).text(this.data()[columnName[i]]);
                    }
                });
            }

            $('#recordEvacueeBtn').click(function() {
                $('.modal-header').removeClass('bg-yellow-500').addClass('bg-green-600');
                $('.modal-title').text('Record Evacuee Information');
                $('#saveEvacueeInfoBtn').removeClass('btn-edit').addClass('btn-submit').text('Save');
                $('#dateFormFieldsContainer').hide();
                $('#evacuationSelectContainer').removeClass('hidden');
                $('#operation').val('record');
                $('#evacueeInfoFormModal').modal('show');
            });

            let evacueeId, defaultFormData;

            $(document).on('click', '.editEvacueeBtn', function() {
                $('.modal-header').removeClass('bg-green-600').addClass('bg-yellow-500');
                $('.modal-title').text('Edit Evacuee Information');
                $('#saveEvacueeInfoBtn').removeClass('btn-submit').addClass('btn-edit').text('Save');
                $('#dateFormFieldsContainer').show();

                let currentRow = $(this).closest('tr');

                if (evacueeTable.responsive.hasHidden()) {
                    currentRow = currentRow.prev('tr');
                }

                let data = evacueeTable.row(currentRow).data();

                evacueeId = data['id'];
                $('input[name="houseHoldNumber"]').val(data['house_hold_number']);
                $('input[name="fullName"]').val(data['full_name']);
                $(`input[name="sex"], option[value="${data['sex']}"]`).prop('selected', true);
                $('input[name="age"]').val(data['age']);
                dateEntryInput.setDate(data['date_entry']);

                if (data['date_out'] == null) {
                    $('#dateOutContainer').hide();
                    $('#dateEntryContainer').removeClass('lg:w-6/12');
                    dateOutInput.setDate('');
                } else {
                    $('#dateOutContainer').show();
                    $('#dateEntryContainer').addClass('lg:w-6/12');
                    dateOutInput.setDate(data['date_out']);
                }

                $(`option[value="${data['barangay']}"]`).prop('selected', true);
                $(`option[value="${data['disaster_type']}"]`).prop('selected', true);
                $(`option[value="${data['disaster_id']}"]`).prop('selected', true);

                if (data['disaster_type'] == 'Typhoon') {
                    $('#flashflood option:selected').prop('selected', false);
                    $('#flashfloodSelectContainer').hide();
                    $('#typhoonSelectContainer').show();
                } else {
                    $('#typhoon option:selected').prop('selected', false);
                    $('#typhoonSelectContainer').hide();
                    $('#flashfloodSelectContainer').show();
                }

                $('input[name="disasterInfo"]').val(data['disaster_info']);

                if ($(`option[value="${data['evacuation_assigned']}"]`).length) {
                    $('#evacuationSelectContainer').removeClass('hidden');
                    $(`option[value="${data['evacuation_assigned']}"]`).prop('selected', true);
                } else {
                    $('#evacuationSelectContainer').addClass('hidden');
                    $('input[name="defaultEvacuationAssigned"]').val(data['evacuation_assigned']);
                }

                dataName = ['4Ps', 'PWD', 'pregnant', 'lactating', 'student', 'working'];
                checkbox = ['fourps', 'pwd', 'pregnant', 'lactating', 'student', 'working'];

                for (let i = 0; i < dataName.length; i++) {
                    data[dataName[i]] == 'Yes' ?
                        $(`input[name="${checkbox[i]}"]`).prop('checked', true) :
                        $(`input[name="${checkbox[i]}"]`).prop('checked', false);
                }

                $('#operation').val('edit');
                $('#evacueeInfoFormModal').modal('show');
                defaultFormData = $('#evacueeInfoForm').serialize();
            });

            $('#disasterType').on('change', function(e) {
                let disasterType = e.target.value;

                if (disasterType == 'Typhoon') {
                    $('#typhoonSelectContainer').show();
                    $('#flashflood').val('');
                    $('#flashfloodSelectContainer').hide();
                } else if (disasterType == 'Flashflood') {
                    $('#typhoonSelectContainer').hide();
                    $('#typhoon').val('');
                    $('#flashfloodSelectContainer').show();
                } else {
                    $('#typhoon').val('');
                    $('#typhoonSelectContainer').hide();
                    $('#flashflood').val('');
                    $('#flashfloodSelectContainer').hide();
                }

                $('input[name="disasterInfo"]').val('');
            });

            $("#typhoon").on('change', function(e) {
                let selectedText = $('#typhoon option:selected').text();
                $('input[name="disasterInfo"]').val(selectedText.trim());
            });

            $("#flashflood").change(function() {
                let selectedText = $('#flashflood option:selected').text();
                $('input[name="disasterInfo"]').val(selectedText.trim());
            });

            function datePicker(id) {
                return flatpickr(id, {
                    enableTime: true,
                    allowInput: true,
                    timeFormat: "h:i K",
                    dateFormat: "D, M j, Y h:i K",
                    minuteIncrement: 1,
                    secondIncrement: 1,
                    position: "below center",
                });
            }

            let dateEntryInput = datePicker("#dateEntry"),
                dateOutInput = datePicker("#dateOut");

            let validator = $("#evacueeInfoForm").validate({
                rules: {
                    houseHoldNumber: {
                        required: true,
                        min: 1,
                    },
                    fullName: {
                        required: true,
                    },
                    sex: {
                        required: true,
                    },
                    age: {
                        required: true,
                        min: 1,
                    },
                    dateEntry: {
                        required: true,
                    },
                    dateOut: {
                        required: true,
                    },
                    barangay: {
                        required: true,
                    },
                    disasterType: {
                        required: true,
                    },
                    typhoon: {
                        required: true,
                    },
                    flashflood: {
                        required: true,
                    },
                    evacuationAssigned: {
                        required: true,
                    },
                },
                messages: {
                    houseHoldNumber: {
                        required: 'Please enter House Hold Number.',
                        min: 'Please enter a number greater than zero.',
                    },
                    fullName: {
                        required: 'Please enter Full Name.',
                    },
                    sex: {
                        required: 'Please select Sex.',
                    },
                    age: {
                        required: 'Please enter Age.',
                        min: 'Please enter a number greater than zero.',
                    },
                    dateEntry: {
                        required: 'Please select Date Entry.',
                    },
                    dateOut: {
                        required: 'Please select Date Out.',
                    },
                    barangay: {
                        required: 'Please select Barangay.',
                    },
                    disasterType: {
                        required: 'Please select Disaster.',
                    },
                    typhoon: {
                        required: 'Please select Typhoon.',
                    },
                    flashflood: {
                        required: 'Please select Flashflood.',
                    },
                    evacuationAssigned: {
                        required: 'Please select Evacuation Center.',
                    },
                },
                errorElement: 'span',
                submitHandler: formSubmitHandler,
            });

            function formSubmitHandler(form, e) {
                let operation = $('#operation').val(),
                    url = "",
                    type = "",
                    hideModal = false,
                    formData = $(form).serialize(),
                    modal = $('#evacueeInfoFormModal');

                if (operation == 'record') {
                    url = "{{ route('evacuee.info.record') }}";
                    type = "POST";
                } else {
                    url = "{{ route('evacuee.info.update', 'evacueeId') }}".replace('evacueeId', evacueeId);
                    type = "PUT";
                    hideModal = true;
                }

                confirmModal(`Do you want to ${operation} this evacuee info?`).then((result) => {
                    if (result.isConfirmed) {
                        if (operation == 'edit' && defaultFormData == formData) {
                            modal.modal('hide');
                            messageModal(
                                'Info',
                                'No changes were made.',
                                'info',
                                '#B91C1C',
                            );
                            return;
                        }
                        $.ajax({
                            data: formData,
                            url: url,
                            type: type,
                            dataType: 'json',
                            success: function(response) {
                                if (response.condition == 0) {
                                    messageModal(
                                        'Warning',
                                        'Please fill up the form correctly.',
                                        'warning',
                                        '#FFDF00',
                                    );
                                } else {
                                    if (hideModal) {
                                        modal.modal('hide');
                                    }

                                    evacueeTable.draw();

                                    operation == 'record' ?
                                        operation += "ed new" :
                                        operation += "ed the";

                                    messageModal(
                                        'Success',
                                        `Successfully ${operation} evacuee info.`,
                                        'success',
                                        '#3CB043',
                                    );
                                }
                            },
                            error: function(jqXHR, error, data) {
                                if (hideModal) {
                                    modal.modal('hide');
                                }

                                messageModal(
                                    jqXHR.status,
                                    data,
                                    'error',
                                    '#B91C1C',
                                );
                            }
                        });
                    }
                });
            }

            $('#evacueeInfoFormModal').on('hidden.bs.modal', function() {
                validator.resetForm();
                $('#evacueeInfoForm').trigger("reset");
            });

            $('#selectAllCheckBox').click(function() {
                let checkBox = $('.evacueeTable tbody tr td input[type="checkbox"]');

                $(this).is(':checked') ?
                    checkBox.each(function() {
                        if (!$(this).is(':disabled'))
                            $(this).prop('checked', true);
                    }) :
                    checkBox.each(function() {
                        if (!$(this).is(':disabled'))
                            $(this).prop('checked', false);
                    });
            });

            $('#returnEvacueeBtn').on('click', function() {
                let id = [],
                    checked = $('.evacueeTable tbody tr td input[type="checkbox"]:checked');

                if (checked.length > 0) {
                    $(checked).each(function() {
                        id.push($(this).val());
                    });
                }

                if (id.length > 0) {
                    message = "";

                    id.length == 1 ?
                        message = "Is this evacuee going back home?" :
                        message = "Are these evacuees going back to their homes?";

                    confirmModal(message).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                data: {
                                    evacueeIds: id,
                                },
                                url: "{{ route('evacuee.info.update.dateout') }}",
                                type: "PATCH",
                                dataType: 'json',
                                success: function(response) {
                                    evacueeTable.draw();
                                    archivedEvacueeTable.draw();

                                    messageModal(
                                        'Success',
                                        'Successfully update the evacuee/s date out.',
                                        'success',
                                        '#3CB043',
                                    );
                                },
                                error: function(jqXHR, error, data) {
                                    $('#selectAllCheckBox').prop('checked', false);

                                    messageModal(
                                        jqXHR.status,
                                        data,
                                        'error',
                                        '#B91C1C',
                                    );
                                }
                            });
                        }
                    });
                } else {
                    $('#selectAllCheckBox').prop('checked', false);

                    messageModal(
                        'Warning',
                        'Please select at least one evacuee.',
                        'warning',
                        '#FFDF00',
                    );
                }
            });

<<<<<<< Updated upstream
            let archivedEvacueeTable = $('.archivedEvacueeTable').DataTable({
                order: [
                    [1, 'asc']
                ],
                language: {
                    emptyTable: 'No archived evacuee data'
                },
=======
            let archivedEvacueeTable = $('#archivedEvacueeTable').DataTable({
                ordering: false,
>>>>>>> Stashed changes
                responsive: true,
                processing: false,
                serverSide: true,
                ajax: "{{ route('evacuee.info.get.archived', 'disasterInfo') }}".replace(
                    'disasterInfo',
                    'None'),
                columns: [{
                        data: 'house_hold_number',
                        name: 'house_hold_number',
                        width: '8%'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'sex',
                        name: 'sex'
                    },
                    {
                        data: 'age',
                        name: 'age'
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
                        data: 'date_out',
                        name: 'date_out'
                    },
                    {
                        data: 'disaster_type',
                        name: 'disaster_type'
                    },
                    {
                        data: 'disaster_info',
                        name: 'disaster_info'
                    },
                    {
                        data: 'evacuation_assigned',
                        name: 'evacuation_assigned'
                    },
                    {
                        data: 'fourps',
                        name: 'fourps',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'PWD',
                        name: 'PWD',
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
                        data: 'student',
                        name: 'student',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'working',
                        name: 'working',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function() {
                    convertBooleanToYesNo(this.api(), 'archivedEvacueeTable');
                }
            });

            function initializeDataTable(url) {
                archivedEvacueeTable.clear();
                archivedEvacueeTable.ajax.url(url).load();
            }

            $('#archiveEvacueeDataTyphoon').on('change', function() {
                $('#archiveEvacueeDataFlashflood').val('None');

                initializeDataTable(
                    "{{ route('evacuee.info.get.archived', 'disasterId') }}".replace(
                        'disasterId', $(this).val())
                );
            });

            $('#archiveEvacueeDataFlashflood').on('change', function() {
                $('#archiveEvacueeDataTyphoon').val('None');

                initializeDataTable(
                    "{{ route('evacuee.info.get.archived', 'disasterId') }}".replace(
                        'disasterId', $(this).val())
                );
            });

        });
    </script>
</body>

</html>
