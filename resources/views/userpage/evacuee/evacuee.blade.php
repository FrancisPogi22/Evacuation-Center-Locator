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
                        <i class="bi bi-person-{{ $operation == 'manage' ? 'gear' : 'slash' }}"></i>
                    </div>
                </div>
                <span>{{ $operation == 'manage' ? 'MANAGE EVACUEE' : 'EVACUEE HISTORY' }}</span>
            </div>
            <hr>
            <div class="page-button-container manage-evacuee">
                @if ($operation == 'manage')
                    @if (auth()->user()->is_disable == 0)
                        @if (!$disasterList->isEmpty())
                            <select id="changeEvacueeDataSelect" class="form-control form-select">
                                @foreach ($disasterList as $disaster)
                                    <option value="{{ $disaster->id }}">
                                        {{ $disaster->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn-submit" id="changeEvacueeDataBtn"></button>
                        @endif
                    @endif
                @else
                    @if (!$archiveDisasterList->isEmpty())
                        <select id="changeYearSelect" class="form-control form-select">
                            @foreach ($yearList as $year)
                                <option value="{{ $year }}">
                                    {{ $year }}</option>
                            @endforeach
                        </select>
                        <select id="changeArchiveEvacueeDataSelect" class="form-control form-select">
                            @foreach ($archiveDisasterList as $disaster)
                                <option value="{{ $disaster->id }}">
                                    {{ $disaster->name }}</option>
                            @endforeach
                        </select>
                    @endif
                @endif
            </div>
            <section class="table-container">
                <div class="table-content">
                    <div class="evacuee-table-header">
                        <header class="table-label">Evacuees Informations Table</header>
                        @if (auth()->user()->is_disable == 0 && $operation == 'manage' && !$disasterList->isEmpty())
                            <div class="page-button-container manage-evacuee-table">
                                <button id="recordEvacueeBtn" data-toggle="modal" data-target="#evacueeInfoFormModal"
                                    class="btn-submit">
                                    <i class="bi bi-person-add "></i>
                                    Record Evacuees Info
                                </button>
                                <button id="changeEvacueeStatusBtn" class="btn-submit"></button>
                            </div>
                        @endif
                    </div>
                    <table class="table" id="evacueeTable" width="100%">
                        <thead class="table-border">
                            <tr class="table-row">
                                <th colspan="2">
                                    <input type="checkbox" id="selectAllCheckBox">
                                </th>
                                <th colspan="2">Family Head</th>
                                <th>Birth Date</th>
                                <th>Barangay</th>
                                <th>Evacuation Assigned</th>
                                <th>Evacuation Id</th>
                                <th>Disaster Id</th>
                                <th>Headcount</th>
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
                        <tbody></tbody>
                    </table>
                </div>
            </section>
        </main>
        @include('userpage.evacuee.evacueeInfoFormModal')
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
        if ('{{ $operation }}' == 'manage') {
            if (!sessionStorage.getItem('status'))
                sessionStorage.setItem('status', 'Evacuated');

            $('#changeEvacueeDataBtn').html(
                sessionStorage.getItem('status') == 'Evacuated' ?
                '<i class="bi bi-house pr-2"></i> Show Returned to Residence' :
                '<i class="bi bi-hospital pr-2"></i> Show Evacuee'
            );

            $('#changeEvacueeStatusBtn').html(
                sessionStorage.getItem('status') == 'Evacuated' ?
                '<i class="bi bi-person-up"></i> Returning Home' :
                '<i class="bi bi-person-down"></i> Evacuated Again'
            );

            '{{ !$disasterList->isEmpty() }}' ?
            sessionStorage.setItem('ongoingDisaster', $('#changeEvacueeDataSelect option:first').val()):
                (sessionStorage.setItem('ongoingDisaster', '-1'),
                    $('.page-button-container.manage-evacuee').prop('hidden', 1));

            $('#changeEvacueeDataSelect').val(sessionStorage.getItem('ongoingDisaster'));
        } else {
            '{{ !$yearList->isEmpty() }}' ?
            sessionStorage.setItem('archiveYear', $('#changeYearSelect option:first').val()):
                sessionStorage.setItem('archiveYear', '-1');

            '{{ !$archiveDisasterList->isEmpty() }}' ?
            sessionStorage.setItem('archiveDisaster', $('#changeArchiveEvacueeDataSelect option:first').val()):
                (sessionStorage.setItem('archiveDisaster', '-1'),
                    $('.page-button-container.manage-evacuee').prop('hidden', 1));

            $('#changeYearSelect').val(sessionStorage.getItem('archiveYear'));
            $('#changeArchiveEvacueeDataSelect').val(sessionStorage.getItem('archiveDisaster'));
        }

        $(document).ready(() => {
            if ('{{ $operation }}' == 'manage' && '{{ $disasterList->isEmpty() }}')
                showInfoMessage('There is no on going disaster.');

            if ('{{ $operation }}' == 'archived' && '{{ $archiveDisasterList->isEmpty() }}')
                showInfoMessage('There is no archived disaster.');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let url, evacueeId, operation, defaultFormData, validator, evacueeTable,
                modal = $('#evacueeInfoFormModal'),
                birthDateInput = datePicker("#birth_date", false),
                modalLabelContainer = $('.modal-label-container'),
                modalLabel = $('.modal-label'),
                formButton = $('#recordEvacueeInfoBtn'),
                selectAllCheckBox = $('#selectAllCheckBox'),
                evacueeInfoForm = $('#evacueeInfoForm'),
                modalDialog = $('.modal-dialog'),
                searchInput = $('#searchInput'),
                searchResults = $('#searchResults'),
                dropdownOptions = $('#dropdownOptions'),
                formType = $('#formType'),
                familyId = $('#family_id'),
                submitButtonContainer = $('.form-button-container'),
                fieldContainer = $('.field-container'),
                fieldContainerSearch = $('.searchContainer'),
                hiddenFieldContainer = $('.hidden_field'),
                formButtonContainer = $('.toggle-form-button'),
                fieldNames = [
                    'infants', 'minors', 'senior_citizen', 'pwd',
                    'pregnant', 'lactating', 'male', 'female'
                ],
                rules = {
                    family_head: 'required',
                    birth_date: 'required',
                    disaster_id: 'required',
                    barangay: 'required',
                    evacuation_id: 'required'
                },
                messages = {
                    family_head: 'Please enter family head.',
                    birth_date: 'Please select birth date.',
                    disaster_id: 'Please select disaster.',
                    barangay: 'Please enter barangay.',
                    evacuation_id: 'Please enter evacuation center assigned.'
                };

            url = '{{ $operation }}' == 'manage' ?
                "{{ route('evacuee.info.get', [$operation, 'disaster', 'status']) }}"
                .replace('status', sessionStorage.getItem('status'))
                .replace('disaster', sessionStorage.getItem("ongoingDisaster")) :
                "{{ route('evacuee.info.get', [$operation, 'disaster', 'archive']) }}"
                .replace('disaster', sessionStorage.getItem("archiveDisaster"));

            evacueeTable = $('#evacueeTable').DataTable({
                ordering: false,
                responsive: true,
                processing: false,
                serverSide: false,
                ajax: url,
                columns: [{
                        data: 'id',
                        name: 'id',
                        visible: false
                    },
                    {
                        data: 'select',
                        name: 'select',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'family_head',
                        name: 'family_head'
                    },
                    {
                        data: 'family_id',
                        name: 'family_id',
                        visible: false
                    },
                    {
                        data: 'birth_date',
                        name: 'birth_date'
                    },
                    {
                        data: 'barangay',
                        name: 'barangay'
                    },
                    {
                        data: 'evacuation_assigned',
                        name: 'evacuation_assigned'
                    },
                    {
                        data: 'evacuation_id',
                        name: 'evacuation_id',
                        visible: false
                    },
                    {
                        data: 'disaster_id',
                        name: 'disaster_id',
                        visible: false
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
                columnDefs: [{
                        targets: 18,
                        visible: {{ auth()->user()->is_disable }} == 1 || '{{ $operation }}' ==
                            'archived' ? false : true
                    },
                    {
                        targets: 1,
                        visible: '{{ $operation }}' == 'archived' ? false : true
                    }
                ]
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

            validator = $("#evacueeInfoForm").validate({
                rules,
                messages,
                errorElement: 'span',
                submitHandler(form) {
                    let formData = $(form).serialize();

                    confirmModal(`Do you want to ${operation} this evacuee info?`).then((result) => {
                        if (!result.isConfirmed) return;

                        let ids = ['infants', 'minors', 'senior_citizen', 'pwd', 'lactating'];

                        if (parseFloat($('#male').val()) + parseFloat($('#female').val()) < ids
                            .reduce((sum, id) => sum + parseFloat($(`#${id}`).val()), 0))
                            return showWarningMessage("Number of members isn't correct.");

                        return operation == 'update' && defaultFormData == formData ?
                            showWarningMessage() :
                            $.ajax({
                                data: formData,
                                url: operation == 'record' ?
                                    "{{ route('evacuee.info.record') }}" :
                                    "{{ route('evacuee.info.update', 'evacueeId') }}".replace(
                                        'evacueeId',
                                        evacueeId),
                                type: operation == 'record' ? "POST" : "PUT",
                                success(response) {
                                    response.status == 'warning' ? showWarningMessage(response
                                        .message) : (modal.modal('hide'), evacueeTable
                                        .draw(),
                                        showSuccessMessage(
                                            `Successfully ${operation == 'record' ? 'recorded new' : 'updated the'} evacuee info.`
                                        ));
                                    initializeDataTable(url);
                                },
                                error: showErrorMessage
                            });
                    });
                }
            });

            $(document).on('click', '#recordEvacueeBtn', () => {
                modalLabelContainer.removeClass('bg-warning');
                modalLabel.text('Record Evacuee Information');
                formButton.addClass('btn-submit').removeClass('btn-update').text('Record');
                operation = "record";
                modal.modal('show');
            });

            $(document).on('click', '#updateEvacueeBtn', function() {
                modalLabelContainer.addClass('bg-warning');
                modalLabel.text('Update Evacuee Information');
                formButton.addClass('btn-update').removeClass('btn-submit').text('Update');
                modalDialog.addClass('modal-lg');
                fieldContainer.add(submitButtonContainer).prop('hidden', 0);
                hiddenFieldContainer.add(formButtonContainer).add(fieldContainerSearch).prop('hidden',
                    1);

                let selectTag = ['barangay', 'evacuation_id', 'disaster_id'],
                    data = getRowData(this, evacueeTable);
                evacueeId = data.id;

                for (const index in data) {
                    if (['action', 'DT_RowIndex', 'id'].includes(index)) continue;

                    $(selectTag.includes(index) ? `select[name="${index}"]` : `#${index}`).val(data[index]);
                }

                operation = "update";
                modal.modal('show');
                defaultFormData = $('#evacueeInfoForm').serialize();
            });

            modal.on('hidden.bs.modal', () => {
                validator && validator.resetForm();
                $('#evacueeInfoForm')[0].reset();
                fieldContainer.add(submitButtonContainer).prop('hidden', 1);
                formButtonContainer.prop('hidden', 0);
                modalDialog.removeClass('modal-lg');
            });

            $(document).on('click', '.rowCheckBox', function() {
                let row = $(this).closest('tr'),
                    childRow = row.next('.child');

                row.add(childRow).toggleClass('selectedRow', $(this).is(':checked'));
                selectAllCheckBox.prop('checked', $('.rowCheckBox:checked').length === $(
                    '.rowCheckBox').length);
            });

            selectAllCheckBox.click(function() {
                let checkBox = $('.table tbody tr td input[type="checkbox"]');

                $(this).is(':checked') ?
                    checkBox.each(function() {
                        $(this).prop('checked', 1);
                        $(this).closest('tr').addClass('selectedRow');
                    }) :
                    checkBox.each(function() {
                        $(this).prop('checked', 0);
                        $(this).closest('tr').removeClass('selectedRow');
                    });
            });

            $('#changeEvacueeStatusBtn').on('click', function() {
                let id = [],
                    checked = $('.table tbody tr td input[type="checkbox"]:checked');

                if (checked.length > 0)
                    $(checked).each(function() {
                        id.push($(this).val());
                    })
                else {
                    selectAllCheckBox.prop('checked', 0);
                    return showWarningMessage('Please select at least one evacuee.');
                }

                confirmModal(`Are these evacuees ${sessionStorage.getItem('status') == 'Evacuated' ?
                    'going back to their homes' : 'evacuated again'}?`).then((result) => {
                    if (!result.isConfirmed) return;

                    let status = sessionStorage.getItem('status') == 'Evacuated' ?
                        'Return Home' : 'Evacuated';

                    $.ajax({
                        data: {
                            evacueeIds: id,
                            status: status
                        },
                        url: "{{ route('evacuee.info.update.status') }}",
                        method: "PATCH",
                        success(response) {
                            evacueeTable.draw();
                            showSuccessMessage(
                                `Successfully updated the evacuee status to ${status.toLowerCase()}.`
                            );
                            selectAllCheckBox.prop('checked', 0);
                            initializeDataTable(url);
                        },
                        error() {
                            selectAllCheckBox.prop('checked', 0);
                            showErrorMessage();
                        }
                    });
                });
            });

            $(document).on('click', '#changeEvacueeDataBtn', function() {
                sessionStorage.setItem('status', this.textContent.includes('Show Returned') ?
                    'Return Home' : 'Evacuated');
                $(this).html(this.textContent.includes('Show Returned') ?
                    '<i class="bi bi-hospital pr-2"></i> Show Evacuee' :
                    '<i class="bi bi-house pr-2"></i> Show Returned to Residence');
                $('#changeEvacueeStatusBtn').html(
                    sessionStorage.getItem('status') == 'Evacuated' ?
                    '<i class="bi bi-person-up"></i> Returning Home' :
                    '<i class="bi bi-person-down"></i> Evacuated Again'
                );
                url = "{{ route('evacuee.info.get', [$operation, 'disaster', 'status']) }}"
                    .replace('disaster', sessionStorage.getItem("ongoingDisaster"))
                    .replace('status', sessionStorage.getItem('status'));
                $('#selectAllCheckBox').prop('checked', 0);
                initializeDataTable(url);
            });

            $('#changeEvacueeDataSelect').on('change', function() {
                sessionStorage.setItem('ongoingDisaster', $(this).val());
                url = "{{ route('evacuee.info.get', [$operation, 'disaster', 'status']) }}"
                    .replace('disaster', sessionStorage.getItem("ongoingDisaster"))
                    .replace('status', sessionStorage.getItem('status'));
                initializeDataTable(url);
            });

            $('#changeYearSelect').on('change', function() {
                sessionStorage.setItem('archiveYear', $(this).val());

                $.ajax({
                    url: "{{ route('disaster.display', ['archived', 'year']) }}"
                        .replace('year', sessionStorage.getItem('archiveYear')),
                    method: 'GET',
                    success(data) {
                        $('#changeArchiveEvacueeDataSelect').empty();

                        if (!data) return;

                        data.forEach(disaster => {
                            $('#changeArchiveEvacueeDataSelect').append(
                                `<option value="${disaster.id}">${disaster.name}</option>`
                            );
                        });
                        sessionStorage.setItem('archiveDisaster', $(
                            '#changeArchiveEvacueeDataSelect option:first').val());
                        $('#changeArchiveEvacueeDataSelect').val(sessionStorage.getItem(
                            'archiveDisaster'));
                        url =
                            "{{ route('evacuee.info.get', [$operation, 'disaster', 'archive']) }}"
                            .replace('disaster', sessionStorage.getItem("archiveDisaster"));
                        initializeDataTable(url);
                    },
                    error: showErrorMessage
                });
            });

            $('#changeArchiveEvacueeDataSelect').on('change', function() {
                sessionStorage.setItem('archiveDisaster', $(this).val());
                url = "{{ route('evacuee.info.get', [$operation, 'disaster', 'archive']) }}"
                    .replace('disaster', sessionStorage.getItem("archiveDisaster"));
                initializeDataTable(url);
            });

            $(document).on('click', '#newRecordBtn, #existingRecordBtn', function() {
                modalDialog.addClass('modal-lg');
                evacueeInfoForm[0].reset();
                validator.resetForm();
                this.textContent.includes('Add new record') ?
                    showForm('new', true, true, false) :
                    showForm('existing', false, false, true);
            });

            searchInput.on('keyup', function() {
                let value = $(this).val();

                if (!value) return dropdownOptions.prop('hidden', 1);

                $.ajax({
                    url: `{{ route('family.record.get', ['data', 'searchData']) }}`
                        .replace('data', value),
                    method: 'GET',
                    success(data) {
                        searchResults.empty();

                        if (data.length == 0) return dropdownOptions.prop('hidden', 1);

                        data.forEach(familyRecord => {
                            searchResults.append(
                                `<li class="searchResult" data-id="${familyRecord.id}">
                                    ${familyRecord.family_head} - ${familyRecord.birth_date}
                                </li>`
                            );
                        });
                        dropdownOptions.prop('hidden', 0);
                    },
                    error: showErrorMessage
                });
            });

            searchResults.on('click', (e) => {
                let target = $(e.target);

                searchInput.val($.trim(target.text()));
                dropdownOptions.prop('hidden', 1);
                $.ajax({
                    url: `{{ route('family.record.get', ['data', 'all']) }}`
                        .replace('data', target.data('id')),
                    method: 'GET',
                    success(data) {
                        showForm('existing', true, true, false);
                        familyId.val(data.id);

                        for (const key in data) {
                            if (['id', 'individuals', 'disaster_id', 'evacuation_id', 'user_id']
                                .includes(key)) continue;

                            let targetElement = key == 'barangay' ?
                                $('select[name="barangay"]') : $(`#${key}`);
                            targetElement.val(data[key]);
                        }
                    },
                    error: showErrorMessage
                });
            });

            function formSubmitHandler(form) {
                let formData = $(form).serialize();

                if (!$('#recordEvacueeInfoBtn').is(':visible')) return;

                confirmModal(`Do you want to ${operation} this evacuee info?`).then((result) => {
                    if (!result.isConfirmed) return;

                    return operation == 'update' && defaultFormData == formData ?
                        showWarningMessage() :
                        $.ajax({
                            data: formData,
                            url: operation == 'record' ? "{{ route('evacuee.info.record') }}" :
                                "{{ route('evacuee.info.update', 'evacueeId') }}".replace('evacueeId',
                                    evacueeId),
                            type: operation == 'record' ? "POST" : "PUT",
                            success(response) {
                                response.status == 'warning' ? showWarningMessage(response
                                    .message) : ($('#closeModalBtn').click(), evacueeTable.draw(),
                                    showSuccessMessage(
                                        `Successfully ${operation == 'record' ? 'recorded new' : 'updated the'} evacuee info.`
                                    ));
                                initializeDataTable(url);
                            },
                            error: showErrorMessage
                        });
                });
            }

            function showForm(formTypeValue, fieldContainerVisible,
                formButtonVisible, fieldContainerSearchVisible) {
                formType.val(formTypeValue);
                fieldContainer.prop('hidden', !fieldContainerVisible);
                submitButtonContainer.prop('hidden', !formButtonVisible);
                fieldContainerSearch.prop('hidden', !fieldContainerSearchVisible);
                formButtonContainer.prop('hidden', 0);
                hiddenFieldContainer.prop('hidden', 1);
            }

            function initializeDataTable(url) {
                evacueeTable.clear();
                evacueeTable.ajax.url(url).load();
            }
        });
    </script>
</body>

</html>
