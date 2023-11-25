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
                        <i class="bi bi-house-{{ $operation == 'active' ? 'gear' : 'slash' }}"></i>
                    </div>
                </div>
                <span>{{ $operation == 'active' ? 'MANAGE' : 'ARCHIVED' }} EVACUATION CENTER</span>
            </div>
            <hr>
            @if ($operation == 'active')
                <div class="page-button-container">
                    <button class="btn-submit" id="addEvacuationCenter">
                        <i class="bi bi-house-down-fill"></i>
                        Add Evacuation Center
                    </button>
                </div>
            @endif
            <section class="table-container">
                <div class="table-content">
                    <header class="table-label">Evacuation Center Table</header>
                    <table class="table" id="evacuationCenterTable" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th colspan="3">Barangay</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>
            @include('userpage.evacuationCenter.evacuationCenterModal')
            @include('userpage.changePasswordModal')
        </main>
    </div>

    @include('partials.script')
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
        let evacuationCenterTable = $('#evacuationCenterTable').DataTable({
            ordering: false,
            responsive: true,
            processing: false,
            serverSide: true,
            ajax: "{{ route('evacuation.center.get', ['manage', $operation]) }}",
            columns: [{
                    data: 'id',
                    name: 'id'
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
                    width: '10%'
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

        let map, marker;

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 14.246261,
                    lng: 121.12772
                },
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
                            url: "{{ asset('assets/img/Default.png') }}",
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
            let evacuationCenterId, operation, validator, defaultFormData, status,
                modalLabelContainer = $('.modal-label-container'),
                modalLabel = $('.modal-label'),
                formButton = $('#createEvacuationCenterBtn'),
                modal = $('#evacuationCenterModal'),
                saveBtnClicked = false;

            validator = $("#evacuationCenterForm").validate({
                rules: {
                    name: 'required',
                    barangayName: 'required'
                },
                messages: {
                    name: 'Please enter evacuation center name.',
                    barangayName: 'Please select a barangay.'
                },
                showErrors: function() {
                    this.defaultShowErrors();

                    if (!marker && saveBtnClicked)
                        $('#location-error').text('Please select a location.').
                    prop('style', 'display: block !important');
                },
                errorElement: 'span',
                submitHandler: formSubmitHandler
            });

            $(document).on('click', '#addEvacuationCenter', () => {
                modalLabelContainer.removeClass('bg-warning');
                modalLabel.text('Add Evacuation Center');
                formButton.addClass('btn-submit').removeClass('btn-update').text('Add');
                operation = "add";
                modal.modal('show');
            });

            $(document).on('click', '#updateEvacuationCenter', function() {
                let {
                    id,
                    name,
                    latitude,
                    longitude,
                    capacity,
                    barangay_name
                } = getRowData(this, evacuationCenterTable);
                evacuationCenterId = id;
                modalLabelContainer.addClass('bg-warning');
                modalLabel.text('Update Evacuation Center');
                formButton.addClass('btn-update').removeClass('btn-submit').text('Update');
                operation = "update";
                $('#name').val(name);
                $('#latitude').val(latitude);
                $('#longitude').val(longitude);
                $(`#barangayName, option[value="${barangay_name}"`).prop('selected', 1);

                marker = new google.maps.Marker({
                    position: {
                        lat: parseFloat(latitude),
                        lng: parseFloat(longitude)
                    },
                    map: map,
                    icon: {
                        url: "{{ asset('assets/img/Default.png') }}",
                        scaledSize: new google.maps.Size(35, 35),
                    },
                });

                modal.modal('show');
                defaultFormData = $('#evacuationCenterForm').serialize();
            });

            $(document).on('click', '#archiveEvacuationCenter', function() {
                let url = "{{ route('evacuation.center.archive', ['evacuationCenterId', 'archive']) }}"
                    .replace(
                        'evacuationCenterId', getRowData(this, evacuationCenterTable).id);
                alterEvacuationCenter(url, 'PATCH', 'archive');
            })

            $(document).on('click', '#unArchiveEvacuationCenter', function() {
                let url =
                    "{{ route('evacuation.center.archive', ['evacuationCenterId', 'unarchive']) }}"
                    .replace(
                        'evacuationCenterId', getRowData(this, evacuationCenterTable).id);
                alterEvacuationCenter(url, 'PATCH', 'unarchive');
            })

            $(document).on('change', '#changeEvacuationStatus', function() {
                status = $(this).val();
                let url = "{{ route('evacuation.center.change.status', 'evacuationCenterId') }}"
                    .replace('evacuationCenterId', getRowData(this, evacuationCenterTable).id);
                alterEvacuationCenter(url, 'PATCH', 'change');
            })

            modal.on('hidden.bs.modal', () => {
                validator && validator.resetForm();
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

            formButton.click(() => saveBtnClicked = true);

            function formSubmitHandler(form) {
                if (!marker) return;

                confirmModal(`Do you want to ${operation} this evacuation center?`).then((result) => {
                    if (!result.isConfirmed) return;

                    let formData = $(form).serialize();

                    return operation == 'update' && defaultFormData == formData ?
                        showWarningMessage() :
                        $.ajax({
                            data: formData,
                            url: operation == 'add' ? "{{ route('evacuation.center.create') }}" :
                                "{{ route('evacuation.center.update', 'evacuationCenterId') }}".
                            replace('evacuationCenterId', evacuationCenterId),
                            method: operation == 'add' ? 'POST' : 'PUT',
                            success(response) {
                                response.status == "warning" ? showWarningMessage(response
                                    .message) : (showSuccessMessage(
                                    `Successfully ${operation == 'add' ? 'added' : 'updated'} evacuation center.`
                                ), evacuationCenterTable.draw(), modal.modal('hide'));
                            },
                            error: showErrorMessage
                        });
                });
            }

            function alterEvacuationCenter(url, type, operation) {
                confirmModal(
                    `Do you want to ${operation == "change" ? "change the status of" : operation} this evacuation center?`
                ).then((result) => {
                    return !result.isConfirmed ? $('#changeEvacuationStatus').val('') :
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            method: type,
                            data: {
                                status
                            },
                            url: url,
                            success() {
                                let operationList = {
                                    archive: "archived",
                                    unarchive: "unarchived",
                                    change: "changed the status of"
                                };

                                showSuccessMessage(
                                    `Successfully ${operationList[operation]} evacuation center.`
                                );
                                evacuationCenterTable.draw();
                            },
                            error: showErrorMessage
                        });
                });
            }
        });
    </script>
</body>

</html>
