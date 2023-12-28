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
                @include('userpage.markers.markersModal')
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
            let disasterId, operation, validator, markerName, markerDescription, markerId, markerItem,
                markerImageChanged = false,
                form = $("#markerForm"),
                modalLabelContainer = $('.modal-label-container'),
                modalLabel = $('.modal-label'),
                formButton = $('#submitMarkerBtn'),
                modal = $('#markerModal'),
                selectMarkerImage = $('#imageBtn'),
                markerImage = $('#markerImagePreview'),
                imageBtnPreview = $('.preview-button-text'),
                error = ('#image-error'),
                btnText = $('#btn-text'),
                btnLoader = $('#btn-loader'),
                markerContainer = $('.mng-marker-container');

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
                    if ($('#markerImage').val() == "" && checkOperation()) {
                        toggleError();
                    } else {
                        let formData = new FormData(form);

                        confirmModal(`Do you want to ${operation} this marker?`).then((result) => {
                            if (!result.isConfirmed) return;

                            return markerName == $('#name').val() && markerDescription == $(
                                    '#description').val() && !markerImageChanged ?
                                (showWarningMessage(), modal.modal('hide')) :
                                $.ajax({
                                    data: formData,
                                    url: checkOperation() ? "{{ route('marker.add') }}" :
                                        "{{ route('marker.update', 'markerId') }}".replace(
                                            'markerId', markerId),
                                    method: "POST",
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    beforeSend() {
                                        toggleBtnLoader(0);
                                        $('#btn-text').text(checkOperation() ? 'Adding' :
                                            'Updating');
                                        toggleModalProperty(1);
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

                                        btnLoader.removeClass('show');
                                        formButton.prop('disabled', 0);

                                        if (checkOperation()) {
                                            emptyGuideline.length > 0 && (emptyGuideline
                                                .remove(), markerContainer.css('display',
                                                    'grid'));
                                            createMarkerWidget(id, image, name, description);
                                        } else {
                                            markerItem.find('.marker-name, .marker-desc').text((
                                                name, description));

                                            markerImageChanged && markerItem.find(
                                                '.marker-image').attr('src',
                                                `{{ asset('markers/${image}') }}`);
                                        }

                                        modal.modal('hide');
                                        showSuccessMessage(
                                            `Marker successfully ${checkOperation() ? 'added' : 'updated'}.`
                                        );
                                    },
                                    error: showErrorMessage,
                                    complete() {
                                        toggleBtnLoader(1);
                                        $('#btn-text').text(
                                            `${operation[0].toUpperCase()}${operation.slice(1)}`
                                        );
                                        toggleModalProperty(0);
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
                imageBtnPreview.text("Select Marker Image");
                operation = "add";
                modal.modal('show');
            });

            $(document).on('click', '.markerEdit', function() {
                setMarkerItem($(this));

                let name = markerItem.find('.marker-name').text(),
                    description = markerItem.find('.marker-desc').text().trim();

                $('#markerImagePreview').attr('src', markerItem.find('.marker-image').attr('src'));
                modalLabelContainer.addClass('bg-warning');
                modalLabel.text('Update Guideline');
                formButton.addClass('btn-update').removeClass('btn-submit');
                btnText.text('Update');
                $('#name').val(name);
                $('#description').val(description);
                markerName = name;
                markerDescription = description;
                operation = "update";
                modal.modal('show');
            });

            $(document).on('click', '.markerRemove', function() {
                setMarkerItem($(this));
                confirmModal('Do you want to remove this marker?').then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('marker.remove', 'markerId') }}"
                            .replace('markerId', markerId),
                        method: "DELETE",
                        success(response) {
                            if (response.status == 'warning')
                                showWarningMessage(response.message)

                            showSuccessMessage('Marker removed successfully.');
                            markerItem.remove();

                            if (checkMarkersData(markerContainer.find('.marker-widget')))
                                showEmptyData();
                        },
                        error: showErrorMessage
                    });
                });
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
                    changeimagePreviewBtn(true);
                } else {
                    toggleError();
                    changeimagePreviewBtn(false);
                    markerImage.attr('src', '/assets/img/Select-Image.svg');
                }
            });

            selectMarkerImage.click((e) => {
                e.preventDefault();
                $('#markerImage').click();
            });

            modal.on('hidden.bs.modal', () => {
                validator.resetForm();
                form[0].reset();
                markerImageChanged = false;
                $('#markerImagePreview').attr('src', '/assets/img/Select-Image.svg');
            });

            function changeimagePreviewBtn(isChange) {
                selectMarkerImage.removeClass(isChange ? 'btn-table-primary' : 'btn-update').addClass(isChange ?
                    'btn-update' : 'btn-table-primary').find(imageBtnPreview).text(isChange ?
                    'Change Marker Image' : 'Select Marker Image');
            }

            function checkMarkersData(object) {
                return object.length == 0;
            }

            function toggleError() {
                $(error).text('Please select an image.').prop('style',
                    'display: block !important');
            }

            function checkOperation() {
                return operation == 'add';
            }

            function toggleModalProperty(isDisable) {
                $('input, #submitMarkerBtn, #closeModalBtn')
                    .prop('disabled', isDisable);
            }

            function setMarkerItem(item) {
                markerItem = item.closest('.marker-widget');
                markerId = markerItem.find('#markerId').val();
            }

            function toggleBtnLoader(isHidden) {
                $('#btn-loader').prop('hidden', isHidden);
            }

            function initMarkers() {
                $.ajax({
                    url: "{{ route('marker.display') }}",
                    type: 'GET',
                    beforeSend() {
                        toggleLoader(0);
                    },
                    success(response) {
                        toggleLoader(1);
                        if (checkMarkersData(response.markerData)) {
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

            function toggleLoader(isHidden) {
                $('#loader').prop('hidden', isHidden);
            }

            function showEmptyData() {
                markerContainer.css('display', 'contents');
                markerContainer.append(`<div class="empty-data-container">
                    <img src="{{ asset('assets/img/Empty-Guideline.svg') }}" alt="Picture">
                    <p>No markers created.</p>
                </div>`);
            }

            function createMarkerWidget(id, image, name, description) {
                markerContainer.append(`<div class="marker-widget">
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
