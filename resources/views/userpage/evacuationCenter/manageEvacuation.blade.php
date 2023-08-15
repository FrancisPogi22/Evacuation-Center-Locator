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
            <div class="label-container">
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-house-gear"></i>
                    </div>
                </div>
                <span>MANAGE EVACUATION CENTER</span>
            </div>
            <hr>
            @if (auth()->user()->is_disable == 0)
                <div class="page-button-container">
                    <button class="btn-submit" id="createEvacuationCenter">
                        <i class="bi bi-house-down-fill"></i>
                        Add Evacuation Center
                    </button>
                </div>
            @endif
            <div class="table-container">
                <div class="table-content">
                    <header class="table-label">Evacuation Center Table</header>
                    <table class="table evacuationCenterTable" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th colspan="2">Name</th>
                                <th>Barangay</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            @include('userpage.evacuationCenter.evacuationCenterModal')
            @include('userpage.changePasswordModal')
        </div>
    </div>

    @include('partials.script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googleMap.key') }}&callback=initMap&v=weekly">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    @include('partials.toastr')
    <script type="text/javascript">
        let evacuationCenterTable = $('.evacuationCenterTable').DataTable({
            language: {
                emptyTable: '<div class="message-text">No evacuation center added yet.</div>'
            },
            ordering: false,
            responsive: true,
            processing: false,
            serverSide: true,
            ajax: "{{ route('evacuation.center.get', 'manage') }}",
            columns: [{
                    data: 'id',
                    name: 'id',
                    visible: false
                }, {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'barangay_name',
                    name: 'barangay_name'
                },
                {
                    data: 'latitude',
                    name: 'latitude',
                    visible: false
                },
                {
                    data: 'longitude',
                    name: 'longitude',
                    visible: false
                },
                {
                    data: 'status',
                    name: 'status',
                    width: '10%',
                },
                {
                    data: 'action',
                    name: 'action',
                    width: '1rem',
                    orderable: false,
                    searchable: false
                }
            ],
            columnDefs: [{
                targets: 5,
                render: function(data) {
                    let color = data == 'Active' ? 'success' : data == 'Inactive' ? 'danger' :
                        'warning';

                    return `
                        <div class="status-container">
                            <div class="status-content bg-${color}">
                                ${data}
                            </div>
                        </div>
                    `;
                }
            }]
        });

        @if (auth()->user()->is_disable == 0)
            let evacuationCenterId, defaultFormData, status, map, marker, current_status, saveBtnClicked = false;

            function initMap() {
                map = new google.maps.Map(document.getElementById("map"), {
                    center: { lat: 14.246261, lng: 121.12772 },
                    zoom: 13,
                    clickableIcons: false,
                    mapTypeControlOptions: {
                        style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                    }
                });

                map.addListener("click", (event) => {
                    let location = event.latLng;

                    if (marker) {
                        marker.setPosition(location);
                    } else {
                        marker = new google.maps.Marker({
                            position: location,
                            map: map,
                            icon: {
                                url: "{{ asset('assets/img/evacMarkerDefault.png') }}",
                                scaledSize: new google.maps.Size(35, 35)
                            }
                        });
                    }

                    $('#latitude').val(location.lat());
                    $('#longitude').val(location.lng());
                    $('#location-error').text('').prop('style', 'display: none');
                });
            }

            $(document).ready(() => {
                const validator = $("#evacuationCenterForm").validate({
                    rules: {
                        name: 'required',
                        barangayName: 'required',
                        capacity: {
                            required: true,
                            number: true
                        }
                    },
                    messages: {
                        name: 'Please enter evacuation center name.',
                        barangayName: 'Please select a barangay.',
                        capacity: {
                            required: 'Please enter evacuation center capacity.',
                            number: 'Please enter a valid number.'
                        }
                    },
                    showErrors: function(errorMap, errorList) {
                        this.defaultShowErrors();

                        if (!marker && saveBtnClicked)
                            $('#location-error').text('Please select a location.').
                            prop('style', 'display: block !important');
                    },
                    errorElement: 'span',
                    submitHandler: formSubmitHandler
                });

                $(document).on('click', '#createEvacuationCenter', function() {
                    $('.modal-label-container').removeClass('bg-warning').addClass('bg-success');
                    $('.modal-label').text('Create Evacuation Center');
                    $('#createEvacuationCenterBtn').removeClass('btn-update').addClass('btn-submit').text(
                        'Add');
                    $('#operation').val('create');
                    $('#evacuationCenterModal').modal('show');
                });

                $(document).on('click', '.updateEvacuationCenter', function() {
                    let {
                        id,
                        name,
                        latitude,
                        longitude,
                        capacity,
                        barangay_name
                    } = getRowData(this, evacuationCenterTable);
                    evacuationCenterId = id;
                    $('.modal-label-container').removeClass('bg-success').addClass('bg-warning');
                    $('.modal-label').text('Update Evacuation Center');
                    $('#createEvacuationCenterBtn').removeClass('btn-submit').addClass('btn-update').text(
                        'Update');
                    $('#operation').val('update');
                    $('#name').val(name);
                    $('#latitude').val(latitude);
                    $('#longitude').val(longitude);
                    $('#capacity').val(capacity);
                    $(`#barangayName, option[value="${barangay_name}"`).prop('selected', true);

                    marker = new google.maps.Marker({
                        position: {
                            lat: parseFloat(latitude),
                            lng: parseFloat(longitude)
                        },
                        map: map,
                        icon: {
                            url: "{{ asset('assets/img/evacMarkerDefault.png') }}",
                            scaledSize: new google.maps.Size(35, 35),
                        },
                    });

                    $('#evacuationCenterModal').modal('show');
                    defaultFormData = $('#evacuationCenterForm').serialize();
                });

                $(document).on('click', '.removeEvacuationCenter', function() {
                    let url = "{{ route('evacuation.center.remove', ':evacuationCenterId') }}".replace(
                        ':evacuationCenterId', getRowData(this, evacuationCenterTable).id);
                    alterEvacuationCenter(url, 'DELETE', 'remove');
                })

                $(document).on('change', '.changeEvacuationStatus', function() {
                    status = $(this).val();
                    let url = "{{ route('evacuation.center.change.status', ':evacuationCenterId') }}"
                        .replace(
                            ':evacuationCenterId', getRowData(this, evacuationCenterTable).id);
                    alterEvacuationCenter(url, 'PATCH', 'change');
                })

                $('#evacuationCenterModal').on('hidden.bs.modal', function() {
                    validator.resetForm();
                    $('#evacuationCenterForm').trigger('reset');

                    if (marker) {
                        marker.setMap(null);
                        marker = undefined;
                    }

                    map.setCenter({
                        lat: 14.2471423,
                        lng: 121.1366715
                    });

                    map.setZoom(13);
                    saveBtnClicked = false;
                });

                $('#createEvacuationCenterBtn').click(() => {
                    saveBtnClicked = true;
                });

                function formSubmitHandler(form) {
                    if (!marker) return;

                    let operation = $('#operation').val(),
                        url, type, formData = $(form).serialize(),
                        modal = $('#evacuationCenterModal');

                    url = operation == 'create' ? "{{ route('evacuation.center.create') }}" :
                        "{{ route('evacuation.center.update', ':evacuationCenterId') }}".
                    replace(':evacuationCenterId', evacuationCenterId);

                    type = operation == 'create' ? 'POST' : 'PUT';

                    confirmModal(`Do you want to ${operation} this evacuation center?`).then((result) => {
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
                                    response.status == "warning" ? showWarningMessage(response
                                        .message) : (showSuccessMessage(
                                        `Successfully ${operation}d evacuation center.`
                                    ), evacuationCenterTable.draw(), modal.modal(
                                        'hide'));
                                },
                                error: function() {
                                    modal.modal('hide');
                                    showErrorMessage();
                                }
                            });
                        }
                    });
                }

                function alterEvacuationCenter(url, type, operation) {
                    confirmModal(
                        `Do you want to ${operation == "remove" ? "remove" : "change the status of"} this evacuation center?`
                    ).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                type: type,
                                data: {
                                    status: status
                                },
                                url: url,
                                success: function() {
                                    showSuccessMessage(
                                        `Successfully ${operation == "remove" ? "removed" : "changed the status of"} evacuation center.`
                                    );
                                    evacuationCenterTable.draw();
                                },
                                error: function() {
                                    showErrorMessage();
                                }
                            });
                        } else {
                            $('.changeEvacuationStatus').val('');
                        }
                    });
                }
            });
        @endif
    </script>
</body>

</html>
