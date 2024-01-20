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
                    <header class="table-label">List of Disasters</header>
                    <table class="table" id="disasterTable" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Disaster Name</th>
                                <th>Type</th>
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
            @include('userpage.disaster.damagesModal')
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
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'type',
                        name: 'type',
                        width: '10%'
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

            let disasterId, defaultFormData, operation, validator, archiveId,
                form = $("#disasterForm"),
                modalLabelContainer = $('.modal-label-container'),
                modalLabel = $('.modal-label'),
                formButton = $('#submitDisasterBtn'),
                modal = $('#disasterModal'),
                damageList = [];

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            validator = form.validate({
                rules: {
                    name: 'required',
                    type: 'required',
                },
                messages: {
                    name: 'Please Enter Disaster Name.',
                    type: 'Please Select Disaster Type.'
                },
                errorElement: 'span',
                submitHandler(form) {
                    let formData = $(form).serialize();

                    if ($("#submitDisasterBtn").text().includes("Archive")) return;

                    confirmModal(`Do you want to ${operation} this disaster?`).then((result) => {
                        if (!result.isConfirmed) return;

                        return operation == 'update' && defaultFormData == formData ?
                            (showWarningMessage(), modal.modal('hide')) :
                            $.ajax({
                                data: formData,
                                url: operation == 'add' ? "{{ route('disaster.create') }}" :
                                    "{{ route('disaster.update', 'disasterId') }}".replace(
                                        'disasterId', disasterId),
                                method: operation == 'add' ? "POST" : "PATCH",
                                beforeSend() {
                                    $('#btn-loader').prop('hidden', 0);
                                    $('#btn-text').text(operation == 'add' ?
                                        'Adding' : 'Updating');
                                    $('input, select, #submitDisasterBtn, #closeModalBtn')
                                        .prop('disabled', 1);
                                },
                                success(response) {
                                    $('#btn-loader').removeClass('show');
                                    formButton.prop('disabled', 0);

                                    response.status == 'warning' ? showWarningMessage(
                                        response
                                        .message) : (
                                        showSuccessMessage(
                                            `Disaster successfully ${operation == "add" ? "added" : "updated"}.`
                                        ), modal.modal('hide'), disasterTable.draw());
                                },
                                error: showErrorMessage,
                                complete() {
                                    $('#btn-loader').prop('hidden', 1);
                                    $('#btn-text').text(
                                        `${operation[0].toUpperCase()}${operation.slice(1)}`
                                    );
                                    $('input, select, #submitDisasterBtn, #closeModalBtn')
                                        .prop('disabled', 0);
                                }
                            });
                    });
                }
            });

            $('#addDisasterData').click(() => {
                $("#disasterFormContainer").prop("hidden", 0);
                $("#archiveDamageContainer").prop("hidden", 1);
                modalLabelContainer.removeClass('bg-warning');
                modalLabel.text('Add Disaster');
                formButton.addClass('btn-submit').removeClass('btn-update').find('#btn-text').text('Add');
                operation = "add";
                modal.modal('show');
            });

            $(document).on('click', '#updateDisaster', function() {
                $("#disasterFormContainer").prop("hidden", 0);
                $("#archiveDamageContainer").prop("hidden", 1);
                let {
                    id,
                    name,
                    type
                } = getRowData(this, disasterTable);
                disasterId = id;
                $('#disasterName').val(name);
                $(`#type, option[value="${type}"`).prop('selected', 1);
                modalLabelContainer.addClass('bg-warning');
                modalLabel.text('Update Disaster');
                formButton.addClass('btn-update').removeClass('btn-submit')
                    .find('#btn-text').text('Update');
                operation = "update";
                modal.modal('show');
                defaultFormData = form.serialize();
            });

            $(document).on('click', '#unArchiveDisaster', function() {
                alterDisasterData('unarchive',
                    "{{ route('disaster.archive', ['disasterId', 'unarchive']) }}", this);
            });

            $(document).on('click', '#archiveDisaster', function() {
                archiveId = getRowData(this, disasterTable).id;

                $("#archiveDamageContainer").prop("hidden", 0);
                $("#disasterFormContainer").prop("hidden", 1);
                modalLabelContainer.addClass('bg-warning');
                modalLabel.text('Add Disaster Damages');
                formButton.addClass('btn-update').removeClass('btn-submit')
                    .find('#btn-text').text('Archive');
                operation = "update";
                modal.modal('show');
            });

            $("#submitDisasterBtn").on('click', function() {
                alterDisasterData('archive',
                    "{{ route('disaster.archive', ['disasterId', 'archive']) }}", archiveId);
            });

            $(document).on('click', '#unArchiveDisaster', function() {
                alterDisasterData('unarchive',
                    "{{ route('disaster.archive', ['disasterId', 'unarchive']) }}", this);
            });

            $(document).on('change', '.changeDisasterStatus', function() {
                alterDisasterData('change',
                    "{{ route('disaster.change.status', 'disasterId') }}", this, $(this).val());
            });

            $(document).on('click', '.viewDamages', function() {
                let {
                    id,
                    name
                } = getRowData(this, disasterTable);

                $.ajax({
                    method: 'GET',
                    url: "{{ route('disaster.get.damages', 'disasterId') }}".replace('disasterId',
                        id),
                    success: function(response) {
                        console.log(response.damages)

                        var damagesContainer = $('.damage-list-container');

                        $(".damage-disaster-label").text(name);
                        damagesContainer.html(response.damages.length > 0 ?
                            response.damages.map(damage => {
                                // Check if barangay is new
                                var displayBarangay = damage.barangay !== $("#barangay")
                                    .val();

                                return `<div class="damage-item bg-white border rounded mt-2">
                                            <div class="m-2 border p-2 rounded">
                                                ${displayBarangay ? "Barangay: " + damage.barangay : ''}
                                            </div>
                                            <div class="m-2 border p-2 rounded">
                                                Description: ${damage.description}
                                            </div>
                                            <div class="m-2 border p-2 rounded">
                                                Quantity: ${damage.total_quantity}
                                            </div>
                                            <div class="m-2 border p-2 rounded">
                                                Cost: ${damage.total_cost}
                                            </div>
                                        </div>`;
                            }).join('') : '<p>No damages found.</p>');
                        $("#damagesModal").modal('show');
                    }
                });

            });

            $('#addDamageBtn').on('click', function(e) {
                e.preventDefault();

                if (["#description", "#barangay", "#quantity", "#cost"]
                    .some(selector => $(selector).val() === "")) return;

                $('#damage_list').append(`
                        <div class="damage-item bg-white border rounded mt-2">
                            <div class="m-2 border p-2 rounded">
                                ${$("#barangay").val()}
                            </div>
                            <div class="m-2 border p-2 rounded">
                                ${$("#description").val()}
                            </div>
                            <div class="m-2 border p-2 rounded">
                                ${$("#quantity").val()}

                            </div>
                            <div class="m-2 border p-2 rounded">
                                ${$("#cost").val()}
                            </div
                        </div>
                    `);

                damageList.push({
                    "description": $("#description").val(),
                    "quantity": $("#quantity").val(),
                    "cost": $("#cost").val(),
                    "barangay": $("#barangay").val(),
                    "disaster_id": archiveId
                });

                $("#description, #barangay, #quantity, #cost").val('');
            });

            modal.on('hidden.bs.modal', () => {
                validator && validator.resetForm();
                form[0].reset();
                $('.damage_item').remove();
            });

            function alterDisasterData(operation, url, btn, status = null) {
                confirmModal(
                        `Do you want to ${operation == 'change' ? 'change the status of' : operation} this disaster?`
                    )
                    .then((result) => {
                        !result.isConfirmed ? $('.changeDisasterStatus').val('') :
                            $.ajax({
                                method: 'PATCH',
                                data: {
                                    status: status,
                                    damages: damageList
                                },
                                url: operation == "archive" ? url.replace('disasterId', btn) : url.replace(
                                    'disasterId', getRowData(btn, disasterTable).id),
                                success(response) {
                                    response.status == 'warning' ?
                                        showWarningMessage(response.message) :
                                        (disasterTable.draw(), showSuccessMessage(
                                            `Disaster successfully ${operation == "change" ? "changed status" : operation}.`
                                        ), modal.modal('hide'));
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
                        (showWarningMessage(), modal.modal('hide')) :
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
