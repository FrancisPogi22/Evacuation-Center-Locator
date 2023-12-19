<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <div class="main-content">
            <div class="label-container">
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                </div>
                <span>MANAGE MARKERS</span>
            </div>
            <hr>
            <div class="page-button-container">
                <button class="btn-submit" id="addMarker">
                    <i class="bi bi-plus-lg"></i>Add Marker
                </button>
                @include('userpage.residentReport.markersModal')
            </div>
            <div id="loader" class="show">
                <div id="loading-text">Getting Markers...</div>
                <div id="loader-inner"></div>
            </div>
            <section class="mng-marker-container">
            </section>
            @include('userpage.changePasswordModal')
        </div>
    </div>

    @include('partials.script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    @include('partials.toastr')
    <script>
        $(document).ready(() => {
            let disasterId, defaultFormData, operation, validator,
                markerImageChanged = false,
                form = $("#markerForm"),
                modalLabelContainer = $('.modal-label-container'),
                modalLabel = $('.modal-label'),
                formButton = $('#submitMarkerBtn'),
                modal = $('#markerModal'),
                selectMarkerImage = $('#imageBtn'),
                markerImage = $('#markerImagePreview'),
                error = ('#image-error');

            initMarkers();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            validator = form.validate({
                rules: {
                    name: 'required',
                    description: 'required'
                },
                messages: {
                    name: 'Please Enter Marker Name.',
                    description: 'Please Enter Marker Description.'
                },
                errorElement: 'span',
                submitHandler(form) {
                    if ($('#markerImage').val() == "") {
                        $(error).text('Please select an image.').prop('style',
                            'display: block !important');
                    } else {
                        let formData = new FormData(form);

                        confirmModal(`Do you want to ${operation} this marker?`).then((result) => {
                            if (!result.isConfirmed) return;

                            return operation == 'update' && defaultFormData == formData ?
                                (showWarningMessage(), modal.modal('hide')) :
                                $.ajax({
                                    data: formData,
                                    url: operation == 'add' ? "{{ route('marker.add') }}" :
                                        "{{ route('disaster.update', 'disasterId') }}".replace(
                                            'disasterId', disasterId),
                                    method: "POST",
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    beforeSend() {
                                        $('#btn-loader').prop('hidden', 0);
                                        $('#btn-text').text(operation == 'add' ?
                                            'Adding' : 'Updating');
                                        $('input, #submitMarkerBtn, #closeModalBtn')
                                            .prop('disabled', 1);
                                    },
                                    success(response) {
                                        if (response.status == 'warning')
                                            return showWarningMessage(response.message);

                                        let emptyGuideline = $('.empty-data-container'),
                                            {
                                                id,
                                                name,
                                                description,
                                                image
                                            } = response;

                                        $('#btn-loader').removeClass('show');
                                        formButton.prop('disabled', 0);

                                        if (operation == 'add') {
                                            emptyGuideline.length > 0 && emptyGuideline
                                                .remove();
                                            showSuccessMessage(
                                                `Marker successfully ${operation == "add" ? "added" : "updated"}.`
                                            )
                                            createMarkerWidget(id, image, name, description)
                                            modal.modal('hide')
                                        }
                                    },
                                    error: showErrorMessage,
                                    complete() {
                                        $('#btn-loader').prop('hidden', 1);
                                        $('#btn-text').text(
                                            `${operation[0].toUpperCase()}${operation.slice(1)}`
                                        );
                                        $('input, #submitMarkerBtn, #closeModalBtn')
                                            .prop('disabled', 0);
                                    }
                                });
                        });
                    }
                }
            });

            $('#addMarker').click(() => {
                modalLabelContainer.removeClass('bg-warning');
                modalLabel.text('Add Marker');
                formButton.addClass('btn-submit').removeClass('btn-update').find('#btn-text').text('Add');
                operation = "add";
                modal.modal('show');
            });

            $('#markerImage').change(function() {
                let imageInput = $(this);

                if (this.files[0]) {
                    if (!['image/jpeg', 'image/jpg', 'image/png'].includes(this.files[0].type)) {
                        if (operation == "create" || markerImageChanged) {
                            imageInput.val('');
                            imageInput.find('#selectedMarkerImage').attr('src', markerImageChanged ?
                                guidelineItem.find('.marker-image').attr('src') :
                                '/assets/img/Select-Image.svg');

                            if (markerImageChanged) markerImageChanged = false;
                        }
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        markerImage.attr('src', e.target.result);
                    };
                    reader.readAsDataURL(this.files[0]);
                    markerImageChanged = true;
                    $(error).prop('style', 'display: none !important');
                } else {
                    $(error).text('Please select an image.').prop('style',
                        'display: block !important');
                    markerImage.attr('src', '/assets/img/Select-Image.svg');
                }
            });

            selectMarkerImage.click((e) => {
                e.preventDefault();
                $('#markerImage').click();
            });

            function initMarkers() {
                $.ajax({
                    url: "{{ route('marker.display') }}",
                    type: 'GET',
                    beforeSend() {
                        $('#loader').prop('hidden', 0);
                    },
                    success(response) {
                        $('#loader').prop('hidden', 1);

                        if (response.markerData.length == 0) {
                            showEmptyData();
                        } else {
                            response.markerData.forEach(marker => {
                                createMarkerWidget(marker.id, marker.image, marker.name, marker
                                    .description);
                            });
                        }
                    },
                    error: showErrorMessage
                });
            }

            function showEmptyData() {
                $('.marker-container').append(`<div class="empty-data-container">
                    <img src="{{ asset('assets/img/Empty-Guideline.svg') }}" alt="Picture">
                    <p>No markers created.</p>
                </div>`);
            }

            function createMarkerWidget(id, image, name, description) {
                $('.mng-marker-container').append(`<div class="marker-widget">
                    <div class="marker-image-container">
                        <img class="marker-image" src="{{ asset('markers/${image}') }}" alt="Image">
                    </div>
                    <div class="widget-desc-container">
                        <input type="text" name="markerId" id="markerId" value="${id}" hidden>
                        <div class="marker-desc-container">
                            <label class="marker-label">Name:</label>
                            <p class="marker-name">${name}</p>
                            <label class="marker-label">Description:</label>
                            <p class="marker-desc">
                                ${description}
                            </p>
                        </div>
                        <div class="marker-btn-container">
                            <button class="btn-update markerEdit">Edit</button>
                            <button class="btn-remove markerRemove">Remove</button>
                        </div>
                    </div>
                </div>`);
            }
        });
    </script>
</body>

</html>
