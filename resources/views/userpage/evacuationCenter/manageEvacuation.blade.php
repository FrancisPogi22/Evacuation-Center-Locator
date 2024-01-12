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
                    <header class="table-label">List of Evacuation Centers</header>
                    <table class="table" id="evacuationCenterTable" width="100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th colspan="3">Barangay</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th>Facilities</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </section>
            @include('userpage.evacuationCenter.evacuationCenterModal')
            @include('userpage.evacuationCenter.facilitiesModal')
            @include('userpage.changePasswordModal')
        </main>
    </div>

    @include('partials.script')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googleMap.key') }}&libraries=places&callback=initMap&v=weekly"
        defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    @include('partials.toastr')
    <script type="text/javascript">
        let map, marker, reportSubmitting = false,
            facilityList = [],
            prevFacilityItem,
            evacuationCenterTable = $('#evacuationCenterTable').DataTable({
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
                    },
                    {
                        data: 'facilities',
                        name: 'facilities',
                        visible: false
                    }
                ],
                columnDefs: [{
                    targets: 5,
                    render: function(data) {
                        let color = data == 'Active' ? 'success' : data == 'Inactive' ? 'danger' :
                            'warning';

                        return `<div class="status-container">
                                    <div class="status-content bg-${color}">
                                        ${data}
                                    </div>
                                </div>
                            `;
                    }
                }]
            });

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 14.246261,
                    lng: 121.12772
                },
                zoom: 13,
                clickableIcons: false,
                draggableCursor: 'pointer',
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
                }
            });

            map.addListener("click", (event) => {
                if (reportSubmitting) return;

                setMarker(event.latLng);
            });

            const autocomplete = new google.maps.places.Autocomplete(document.getElementById('searchPlace'));

            autocomplete.addListener('place_changed', function() {
                const selectedPlace = autocomplete.getPlace();

                if (selectedPlace.geometry)
                    setMarker({
                        lat: selectedPlace.geometry.location.lat(),
                        lng: selectedPlace.geometry.location.lng()
                    });
            });
        }

        function setMarker(coordinates) {
            if (marker)
                marker.setPosition(coordinates);
            else {
                marker = new google.maps.Marker({
                    position: coordinates,
                    map: map,
                    draggable: true,
                    icon: {
                        url: "{{ asset('assets/img/Default.png') }}",
                        scaledSize: new google.maps.Size(35, 35)
                    },
                    label: {
                        text: 'Evacuation Location',
                        className: 'report-marker-label'
                    }
                });
            }

            map.setZoom(16);
            map.panTo(coordinates);
            $('#latitude').val(coordinates.lat);
            $('#longitude').val(coordinates.lng);
            $('#location-error').text('').prop('style', 'display: none');
        }

        $(document).ready(() => {
            let evacuationCenterId, operation, validator, defaultFormData, status,
                modalLabelContainer = $('.modal-label-container'),
                modalLabel = $('.modal-label'),
                formButton = $('#createEvacuationCenterBtn'),
                modal = $('#evacuationCenterModal'),
                btnLoader = $('#btn-loader'),
                btnText = $('#btn-text'),
                facilityItemContainer = $('.facility-item-container'),
                facilityError = $('#new-facility-error'),
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
                        $('#location-error').text('Please select a location.')
                        .prop('style', 'display: block !important')
                        .prop('hidden', 0);

                    if (facilityList.length == 0 && saveBtnClicked) facilityError
                        .text('Please add a facility.')
                        .prop('style', 'display: block !important')
                        .prop('hidden', 0);
                },
                errorElement: 'span',
                submitHandler(form) {
                    if (!marker || $('#searchPlace').is(':focus') || facilityList.length == 0) return;

                    confirmModal(`Do you want to ${operation} this evacuation center?`).then((result) => {
                        if (!result.isConfirmed) return;

                        let formData = $(form).serialize();
                        $.each(facilityList, function(index, value) {
                            formData += '&facilities[]=' + encodeURIComponent(value);
                        });

                        return operation == 'update' && defaultFormData == formData ?
                            (showWarningMessage(), modal.modal('hide')) :
                            $.ajax({
                                data: formData,
                                url: operation == 'add' ?
                                    "{{ route('evacuation.center.create') }}" :
                                    "{{ route('evacuation.center.update', 'evacuationCenterId') }}"
                                    .replace('evacuationCenterId', evacuationCenterId),
                                method: operation == 'add' ? 'POST' : 'PUT',
                                beforeSend() {
                                    reportSubmitting = true;
                                    btnLoader.prop('hidden', 0);
                                    btnText.text(operation == 'add' ?
                                        'Adding' : 'Updating');
                                    $('input, select, #createEvacuationCenterBtn, #closeModalBtn')
                                        .prop('disabled', 1);
                                },
                                success(response) {
                                    $('#btn-loader').addClass('show');
                                    formButton.prop('disabled', 0);
                                    response.status == "warning" ? showWarningMessage(
                                        response.message) : (showSuccessMessage(`Successfully
                                            ${operation == 'add' ? 'added' : 'updated'} evacuation center.`),
                                        evacuationCenterTable.draw(), modal.modal('hide'));
                                },
                                error: showErrorMessage,
                                complete() {
                                    reportSubmitting = false;
                                    btnLoader.prop('hidden', 1);
                                    btnText.text(
                                        `${operation[0].toUpperCase()}${operation.slice(1)}`
                                    );
                                    $('input, select, #createEvacuationCenterBtn, #closeModalBtn')
                                        .prop('disabled', 0);
                                }
                            });
                    });
                }
            });

            $(document).on('click', '#addEvacuationCenter', () => {
                modalLabelContainer.removeClass('bg-warning');
                modalLabel.text('Add Evacuation Center');
                formButton.addClass('btn-submit').removeClass('btn-update');
                btnText.text('Add');
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
                    barangay_name,
                    facilities
                } = getRowData(this, evacuationCenterTable),
                    checkboxValues = $('.checkbox-container input.checkbox').map((index, element) =>
                        $(element).attr('value')).get();

                evacuationCenterId = id;
                modalLabelContainer.addClass('bg-warning');
                modalLabel.text('Update Evacuation Center');
                formButton.addClass('btn-update').removeClass('btn-submit');
                btnText.text('Update');
                operation = "update";
                $('#name').val(name);
                $('#latitude').val(latitude);
                $('#longitude').val(longitude);
                $(`#barangayName, option[value="${barangay_name}"`).prop('selected', 1);
                facilityList = facilities.split(',');
                facilityList.forEach(facility => {
                    $(':checkbox[value="' + facility + '"]').prop('checked', true);

                    facilityItemContainer.append(checkboxValues.includes(facility) ?
                        `<div class="facility-item">${facility}</div>` :
                        `<div class="facility-item" value="${facility}">
                            <span>${facility}</span>
                            <div class="facility-item-btn-container">
                                <button class="updateFacilityBtn btn-update"><i class="bi bi-pencil-square"></i></button>
                                <button class="removeFacilityBtn btn-remove"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </div>`);
                });

                facilityItemContainer.prop('hidden', 0)

                marker = new google.maps.Marker({
                    position: {
                        lat: parseFloat(latitude),
                        lng: parseFloat(longitude)
                    },
                    map: map,
                    icon: {
                        url: "{{ asset('assets/img/Default.png') }}",
                        scaledSize: new google.maps.Size(35, 35)
                    },
                });

                modal.modal('show');
                defaultFormData = $('#evacuationCenterForm').serialize();
                $.each(facilityList, function(index, value) {
                    defaultFormData += '&facilities[]=' + encodeURIComponent(value);
                });
            });

            $(document).on('click', '#archiveEvacuationCenter', function() {
                let url = "{{ route('evacuation.center.archive', ['evacuationCenterId', 'archive']) }}"
                    .replace(
                        'evacuationCenterId', getRowData(this, evacuationCenterTable).id);
                alterEvacuationCenter(url, 'PATCH', 'archive');
            });

            $(document).on('click', '#unArchiveEvacuationCenter', function() {
                let url =
                    "{{ route('evacuation.center.archive', ['evacuationCenterId', 'unarchive']) }}"
                    .replace(
                        'evacuationCenterId', getRowData(this, evacuationCenterTable).id);
                alterEvacuationCenter(url, 'PATCH', 'unarchive');
            });

            $(document).on('change', '.changeEvacuationStatus', function() {
                status = $(this).val();
                let url = "{{ route('evacuation.center.change.status', 'evacuationCenterId') }}"
                    .replace('evacuationCenterId', getRowData(this, evacuationCenterTable).id);
                alterEvacuationCenter(url, 'PATCH', 'change');
            })

            $(document).on('click', '.checkFacilities', function() {
                let {
                    name,
                    facilities
                } = getRowData(this, evacuationCenterTable);

                modalLabelContainer.removeClass('bg-warning');
                modalLabel.text('Facilities List');
                $('.evac-facility-label').text(name);
                $('.facilitiy-label').remove();
                facilities = facilities.split(',');
                facilities.forEach(facility => {
                    $('.facilitiy-list').append(`
                        <div class="facilitiy-label">
                            <i class="bi bi-circle-fill"></i>${facility}
                        </div>
                    `);
                });
                $('#facilitiesModal').modal('show');
            });

            $('.checkbox').on('click', function() {
                let checkbox = $(this),
                    value = checkbox.attr('value');

                if (checkbox.prop("checked")) {
                    facilityList.push(value);
                    facilityItemContainer.prop('hidden', 0)
                        .append(`<div class="facility-item">${value}</div>`);
                    facilityError.prop('style', 'display: none').prop('hidden', 1);
                } else {
                    facilityList.splice(facilityList.indexOf(value), 1);
                    $('.facility-item:contains("' + value + '")').remove();
                    if ($('.facility-item').length == 0 && saveBtnClicked)
                        facilityError.prop('style', 'display: block !important').prop('hidden', 0);
                    hideFacilitiesList();
                }
            });

            $('#addFacilityBtn').on('click', function(e) {
                e.preventDefault();

                if (!$.trim($("#newFacility").val()))
                    return facilityError.text('Please enter a facility.')
                        .prop('style', 'display: block !important')
                        .prop('hidden', 0);
                else
                    facilityError.prop('style', 'display: none').prop('hidden', 1);

                let facilityInput = $("#newFacility").val().split(' ').map(w => w.charAt(0)
                    .toUpperCase() + w.slice(1).toLowerCase()).join(' ');

                if ($(this).text().includes('Add')) {

                    if (checkDuplicateValue(facilityList, facilityInput)) return;

                    facilityList.push(facilityInput);
                    $("#newFacility").val('');

                    facilityItemContainer.prop('hidden', 0).append(`
                        <div class="facility-item" value="${facilityInput}">
                            <span>${facilityInput}</span>
                            <div class="facility-item-btn-container">
                                <button class="updateFacilityBtn btn-update"><i class="bi bi-pencil-square"></i></button>
                                <button class="removeFacilityBtn btn-remove"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </div>
                    `);
                } else {
                    if (facilityInput == prevFacilityItem.attr('value'))
                        return facilityError
                            .text('Value should not be identical to the one you are currently updating.')
                            .prop('style', 'display: block !important')
                            .prop('hidden', 0);

                    let facilitySaved = facilityList.filter(value =>
                        value != prevFacilityItem.attr('value'));

                    if (checkDuplicateValue(facilityList, facilityInput)) return;

                    facilityList[facilityList.indexOf(prevFacilityItem.attr('value'))] = facilityInput;
                    prevFacilityItem.attr('value', facilityInput);
                    prevFacilityItem.find('span').text(facilityInput);
                    $('#cancelFacilityUpdateBtn').click();
                }
            });

            $(document).on('click', '.removeFacilityBtn', function(e) {
                e.preventDefault();
                let facilityItem = $(this).parent().parent();

                facilityList.splice(facilityList.indexOf(facilityItem.attr('value')), 1);
                facilityItem.remove();
                hideFacilitiesList();
            });

            $(document).on('click', '.updateFacilityBtn', function(e) {
                e.preventDefault();
                let facilityItem = $(this).parent().parent();
                if (prevFacilityItem) prevFacilityItem.show();
                prevFacilityItem = facilityItem;

                $("#newFacility").val(facilityItem.attr('value'));
                $('#addFacilityBtn').text('Save').css('background', '#ffcb2f');
                $('#cancelFacilityUpdateBtn').prop('hidden', 0);
                facilityItem.hide();
                hideFacilitiesList();
            });

            $('#cancelFacilityUpdateBtn').click(function(e) {
                e.preventDefault();

                $("#newFacility").val('');
                $('#addFacilityBtn').text('Add Facility').css('background', '#2682fa');
                $('#cancelFacilityUpdateBtn, #new-facility-error').prop('hidden', 1);
                facilityItemContainer.prop('hidden', !$('.facility-item').length > 0);
                facilityError.prop('style', 'display: none').prop('hidden', 1);
                if (prevFacilityItem) prevFacilityItem.show();
                prevFacilityItem = '';
            });

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
                facilityList = [];
                prevFacilityItem = '';
                $('.facility-item').remove();
                $('#cancelFacilityUpdateBtn').click();
            });

            formButton.click(() => saveBtnClicked = true);

            function alterEvacuationCenter(url, type, operation) {
                confirmModal(
                    `Do you want to ${operation == "change" ? "change the status of" : operation} this evacuation center?`
                ).then((result) => {
                    !result.isConfirmed ? $('.changeEvacuationStatus').val('') :
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

            function checkDuplicateValue(list, value) {
                if (list.includes(value)) {
                    facilityError.text('This facility is already added.')
                        .prop('style', 'display: block !important')
                        .prop('hidden', 0);

                    return true;
                }

                return false;
            }

            function hideFacilitiesList() {
                if ($('.facility-item:visible').length == 0)
                    facilityItemContainer.prop('hidden', 1);
            }
        });
    </script>
</body>

</html>
