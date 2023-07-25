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
                    <div class="text-2xl text-white">
                        <i class="bi bi-house-gear p-2 bg-slate-600"></i>
                    </div>
                </div>
                <span class="text-xl font-bold">MANAGE EVACUATION CENTER</span>
            </div>
            <hr class="mt-4 mb-3">
            @if (auth()->user()->status == 'Active')
                <div class="create-section">
                    <button class="btn-submit p-2 createEvacuationCenter">
                        <i class="bi bi-house-down-fill pr-2"></i>
                        Add Evacuation Center
                    </button>
                </div>
            @endif
            <div class="table-container p-3 shadow-lg rounded-lg">
                <div class="block w-full overflow-auto pb-2">
                    <header class="text-2xl font-semibold">Evacuation Center Table</header>
                    <table id="evacuationCenterTable" class="table" width="100%">
                        <thead class="thead-light">
                            <tr>
<<<<<<< Updated upstream
                                <th></th>
=======
                                <th>ID</th>
>>>>>>> Stashed changes
                                <th>Name</th>
                                <th>Barangay</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
<<<<<<< Updated upstream
                                <th>Status</th>
                                <th>Action</th>
=======
                                <th class="w-4">Status</th>
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
                @include('userpage.evacuationCenter.evacuationCenterModal')
            @endif
            @auth
                @include('userpage.changePasswordModal')
            @endauth
        </div>
    </div>

    <script src="{{ asset('assets/js/script.js') }}"></script>
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
<<<<<<< Updated upstream
        let evacuationCenterTable = $('.evacuationCenterTable').DataTable({
            order: [
                [1, 'asc']
            ],
            language: {
                emptyTable: 'No evacuation center added yet',
            },
=======
        let evacuationCenterTable = $('#evacuationCenterTable').DataTable({
            language: {
                emptyTable: '<div class="text-red-600 font-extrabold">No evacuation center added.</div>'
            },
            ordering: false,
>>>>>>> Stashed changes
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
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    width: '4px'
                }
            ]
        });

        @if (auth()->user()->status == 'Active')
            let evacuationCenterId, defaultFormData, status, map, marker, saveBtnClicked = false;

            function initMap() {
                map = new google.maps.Map(document.getElementById("map"), {
                    center: {
                        lat: 14.2471423,
                        lng: 121.1366715
                    },
                    zoom: 13,
                    clickableIcons: false,
                    mapTypeId: 'terrain',
                    styles: mapTypeStyleArray
                });

                map.addListener("click", (event) => {
                    let location = event.latLng;

                    if (marker) {
                        marker.setMap(null);
                    }

                    marker = new google.maps.Marker({
                        position: location,
                        map: map,
                        icon: {
                            url: "{{ asset('assets/img/evacMarkerDefault.png') }}",
                            scaledSize: new google.maps.Size(35, 35),
                        },
                    });

                    $('input[name="latitude"]').val(location.lat());
                    $('input[name="longitude"]').val(location.lng());
                    $('#location-error').hide();
                });
            }

            $(document).ready(function() {
                let validator = $("#evacuationCenterForm").validate({
                    rules: {
                        name: {
                            required: true
                        },
                        barangayName: {
                            required: true
                        }
                    },
                    messages: {
                        name: {
                            required: 'Please enter evacuation center name.'
                        },
                        barangayName: {
                            required: 'Please select a barangay.'
                        }
                    },
                    showErrors: function(errorMap, errorList) {
                        this.defaultShowErrors();

                        if (!marker && saveBtnClicked) {
                            $('#location-error').
                            text('Please select a location.').
                            show();
                        }
                    },
                    errorElement: 'span',
                    submitHandler: formSubmitHandler
                });

            $(document).on('click', '.createEvacuationCenter', function() {
                $('.modal-header').removeClass('bg-yellow-500').addClass('bg-green-600');
                $('.modal-title').text('Add Evacuation Center');
                $('#saveEvacuationCenterBtn').removeClass('btn-edit').addClass('btn-submit').text('Add');
                $('#operation').val('create');
                $('#evacuationCenterModal').modal('show');
            });

                $(document).on('click', '.updateEvacuationCenter', function() {
                    let data = getRowData(this);
                    evacuationCenterId = data['id'];

                $('.modal-header').removeClass('bg-green-600').addClass('bg-yellow-500');
                $('.modal-title').text('Edit Evacuation Center');
                $('#saveEvacuationCenterBtn').removeClass('btn-submit').addClass('btn-edit').text('Save');
                $('#operation').val('update');
                $('#name').val(data['name']);
                $('#latitude').val(data['latitude']);
                $('#longitude').val(data['longitude']);
                $(`#barangayName, option[value="${data['barangay_name']}"`).prop('selected', true);

                    marker = new google.maps.Marker({
                        position: {
                            lat: parseFloat(data['latitude']),
                            lng: parseFloat(data['longitude'])
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
                    evacuationCenterId = getRowData(this)['id'];
                    let url = "{{ route('evacuation.center.remove', ':evacuationCenterId') }}".replace(
                        ':evacuationCenterId', evacuationCenterId);
                    alterEvacuationCenter(url, "DELETE", "remove");
                })

                $(document).on('change', '.changeEvacuationStatus', function() {
                    evacuationCenterId = getRowData(this)['id'];
                    status = $(this).val();
                    let url = "{{ route('evacuation.center.change.status', ':evacuationCenterId') }}"
                        .replace(
                            ':evacuationCenterId', evacuationCenterId);
                    alterEvacuationCenter(url, "PATCH", "change");
                })

                $('#evacuationCenterModal').on('hidden.bs.modal', function() {
                    validator.resetForm();
                    $('#evacuationCenterForm')[0].reset();

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

                $('#saveEvacuationCenterBtn').click(() => {
                    saveBtnClicked = true;
                });

                function getRowData(element) {
                    let currentRow = $(element).closest('tr');

                    if (evacuationCenterTable.responsive.hasHidden()) {
                        currentRow = currentRow.prev('tr');
                    }

                    return evacuationCenterTable.row(currentRow).data();
                }

                function formSubmitHandler(form) {
                    if (!marker) {
                        return;
                    }

                    let operation = $('#operation').val(),
                        url, type, formData = $(form).serialize(),
                        modal = $('#evacuationCenterModal');

                    if (operation == 'create') {
                        url = "{{ route('evacuation.center.create') }}";
                        type = "POST";
                    } else {
                        url = "{{ route('evacuation.center.update', ':evacuationCenterId') }}".
                        replace(':evacuationCenterId', evacuationCenterId);
                        type = "PUT";
                    }

                    confirmModal(`Do you want to ${operation} this evacuation center?`).then((result) => {
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
                                    if (response.status == "warning") {
                                        toastr.warning(response.message, 'Warning');
                                    } else {
                                        toastr.success(
                                            `Successfully ${operation}d evacuation center.`,
                                            'Success');
                                        evacuationCenterTable.draw();
                                        modal.modal('hide');
                                    }
                                },
                                error: function() {
                                    modal.modal('hide');
                                    toastr.error(
                                        'An error occurred while processing your request.',
                                        'Error');
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
                                    toastr.success(
                                        `Successfully ${operation == "remove" ? "removed" : "changed the status of"} evacuation center.`,
                                        'Success');
                                    evacuationCenterTable.draw();
                                },
                                error: function() {
                                    toastr.error(
                                        'An error occurred while processing your request.',
                                        'Error');
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
