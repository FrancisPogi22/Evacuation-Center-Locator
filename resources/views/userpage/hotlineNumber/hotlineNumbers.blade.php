<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <main class="main-content">
            <div class="label-container">
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-telephone"></i>
                    </div>
                </div>
                <span>HOTLINE NUMBERS</span>
            </div>
            <hr>
            <section class="hotline-content">
                @if ($operation == 'manage')
                    <div class="page-button-container">
                        <button class="btn-submit" id="addNumberBtnModal">
                            <i class="bi bi-telephone-plus"></i>Add Hotline Number
                        </button>
                    </div>
                @endif
                <div class="number-section">
                    @if ($operation == 'manage')
                        @include('userpage.hotlineNumber.hotlineForm')
                    @endif
                    @forelse ($hotlineNumbers as $hotlineNumber)
                        <div class="hotline-container">
                            @if ($operation == 'manage')
                                <div class="id-container">
                                    <b>(ID - {{ $hotlineNumber->id }})</b>
                                </div>
                            @endif
                            <div class="hotline-data-parent-container">
                                <div class="hotline-logo-container-list">
                                    <div class="hotline-image-container-list">
                                        <img src="{{ $hotlineNumber->logo ? '/hotline_logo/' . $hotlineNumber->logo : asset('assets/img/Empty-Data.svg') }}"
                                            class="hotline-preview-image-list" alt="logo">
                                    </div>
                                </div>
                                <div class="hotline-details-container">
                                    <div class="hotline-data-container">
                                        <b>{{ $hotlineNumber->label }}</b>
                                    </div>
                                    <hr>
                                    <div class="hotline-data-container last-data">
                                        {{ $hotlineNumber->number }}
                                    </div>
                                    <div class="hotline-form-button-container-list">
                                        <a href="tel:+{{ preg_replace('/\D/', '', $hotlineNumber->number) }}"
                                            class="btn-submit">
                                            <i class="bi bi-telephone-outbound"></i>Call
                                        </a>
                                        @if ($operation == 'manage')
                                            <button class="btn-update updateNumber" data-id="{{ $hotlineNumber->id }}">
                                                <i class="bi bi-pencil-square"></i>Update</button>
                                            <button class="btn-remove removeNumber" data-id="{{ $hotlineNumber->id }}">
                                                <i class="bi bi-trash3"></i>Remove</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-data-container">
                            <img src="{{ asset('assets/img/Empty-Hotline.svg') }}" alt="Picture">
                            <p>No hotline numbers added yet.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
        @include('userpage.changePasswordModal')
    </div>

    @include('partials.script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    @include('partials.toastr')
    @auth
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
            integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
            crossorigin="anonymous"></script>
        @if ($operation == 'manage')
            <script>
                $(document).ready(() => {
                    let operation, hotlineLabel, hotlineNumber, hotlineId, validator,
                        hotlineItem = "",
                        hotlineLogoChanged = false,
                        btntext = $('#btnText'),
                        btnLoader = $('#btn-loader'),
                        formBtn = $('#addNumberBtn'),
                        logoError = $('#image-error'),
                        changeLogoBtn = $('#imageBtn'),
                        hotlineLogo = $('.hotlineLogo'),
                        hotlineForm = $('#hotlineForm'),
                        previewLogo = $('#hotline-preview-image');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    validator = hotlineForm.validate({
                        rules: {
                            label: 'required',
                            number: 'required'
                        },
                        messages: {
                            label: 'Please enter number label.',
                            number: 'Please enter hotline number.'
                        },
                        errorElement: 'span',
                        submitHandler(form, e) {
                            let formData = new FormData(form);

                            confirmModal(`Do you want to ${operation} this hotline number?`).then((result) => {
                                if (!result.isConfirmed) return;

                                operation == "update" && hotlineLabel == $('#hotlineLabel').val() &&
                                    hotlineNumber == $('#hotlineNumber').val() && !hotlineLogoChanged ?
                                    showWarningMessage() :
                                    $.ajax({
                                        data: formData,
                                        url: operation == 'add' ? "{{ route('hotline.add') }}" :
                                            "{{ route('hotline.update', 'hotlineId') }}".replace(
                                                'hotlineId', hotlineId),
                                        method: "POST",
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        beforeSend() {
                                            btntext.text(operation == 'add' ? 'Adding' : 'Updating');
                                            btnLoader.prop('hidden', 0);
                                            $('input, #closeFormBtn, .hotline-content button')
                                                .prop('disabled', 1);
                                        },
                                        success(response) {
                                            if (response.status == 'warning')
                                                return showWarningMessage(response.message);

                                            let {
                                                label,
                                                number,
                                                hotlineId,
                                                hotlineLogo
                                            } = response;

                                            if (operation == "update") {
                                                if (hotlineLogoChanged)
                                                    hotlineItem.find('.hotline-preview-image-list')
                                                    .attr('src', hotlineLogo == '' ?
                                                        'assets/img/Empty-Data.svg' : previewLogo.attr(
                                                            'src'));
                                                hotlineItem.find('.hotline-data-container:first b')
                                                    .text(label);
                                                hotlineItem.find('.hotline-data-container:last')
                                                    .text(number);
                                                replaceHotlineItem();
                                            } else {
                                                $('.number-section').append(`
                                                    <div class="hotline-container">
                                                        @if ($operation == 'manage')
                                                            <div class="id-container">
                                                                <b>(ID - ${hotlineId} )</b>
                                                            </div>
                                                        @endif
                                                        <div class="hotline-data-parent-container">
                                                            <div class="hotline-logo-container-list">
                                                                <div class="hotline-image-container-list">
                                                                    <img src="/${hotlineLogo ? `hotline_logo/${hotlineLogo}` : 'assets/img/Empty-Data.svg'}"
                                                                        class="hotline-preview-image-list" alt="logo">
                                                                </div>
                                                            </div>
                                                            <div class="hotline-details-container">
                                                                <div class="hotline-data-container">
                                                                    <b>${label}</b>
                                                                </div>
                                                                <hr>
                                                                <div class="hotline-data-container last-data">
                                                                    ${number}
                                                                </div>
                                                                <div class="hotline-form-button-container-list">
                                                                    <a href="tel:${number.replace(/\D/g, '')}" class="btn-submit">
                                                                        <i class="bi bi-telephone-outbound"></i>Call
                                                                    </a>
                                                                    @if ($operation == 'manage')
                                                                        <button class="btn-update updateNumber" data-id="${hotlineId}">
                                                                            <i class="bi bi-pencil-square"></i>Update</button>
                                                                        <button class="btn-remove removeNumber" data-id="${hotlineId}">
                                                                            <i class="bi bi-trash3"></i>Remove</button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>`);
                                                $(".empty-data-container").remove();
                                                hotlineForm.prop('hidden', 1);
                                            }
                                            showSuccessMessage(
                                                `Hotline number successfully ${operation == "add" ? "added" : "updated"}.`
                                            );
                                            resetHotlineForm();
                                            hotlineItem = "";
                                            $('.btn-remove.removeNumber').prop('hidden', 0);
                                        },
                                        error: showErrorMessage,
                                        complete(jqXHR) {
                                            btntext.text(`${operation[0].toUpperCase()}${operation.slice(1)}`);
                                            btnLoader.prop('hidden', 1);
                                            $('input, #closeFormBtn, .hotline-content button')
                                                .prop('disabled', 0);
                                            if (jqXHR.responseJSON.status != 'warning') operation = "";
                                        }
                                    });
                            });
                        }
                    });

                    $('#addNumberBtnModal').click(() => {
                        if (operation != 'add') {
                            if (hotlineItem) {
                                replaceHotlineItem();
                                resetHotlineForm();
                                hotlineItem = "";
                            }

                            $(".empty-data-container").prop('hidden', $(".empty-data-container").length > 0);
                            operation = "add";
                            validator.resetForm();
                            hotlineForm.prop('hidden', 0);
                            formBtn.removeClass('bg-warning');
                            btntext.text('Add');
                            scrollToElement('#hotlineForm');
                            $('.btn-remove.removeNumber').prop('hidden', 0);
                        }
                    });

                    $(document).on('click', '.updateNumber', function() {
                        validator.resetForm();
                        hotlineItem && replaceHotlineItem();
                        hotlineItem = $(this).closest('.hotline-container');
                        hotlineId = $(this).data('id');
                        replaceHotlineItem(false);
                        changeButton();
                        hotlineLabel = hotlineItem.find('.hotline-data-container:first').text().trim();
                        hotlineNumber = hotlineItem.find('.hotline-data-container.last-data').text().trim();
                        previewLogo.attr('src', hotlineItem.find('.hotline-preview-image-list').attr('src'));
                        $('#hotlineLabel').val(hotlineLabel);
                        $('#hotlineNumber').val(hotlineNumber);
                        btntext.text('Update');
                        formBtn.addClass('bg-warning');
                        operation = "update";
                        hotlineLogoChanged = false;
                        scrollToElement('#hotlineForm');
                        $('.btn-remove.removeNumber').prop('hidden', 1);
                    });

                    $(document).on('click', '.removeNumber', function() {
                        confirmModal(`Do you want to remove this hotline number?`).then((result) => {
                            if (!result.isConfirmed) return;

                            hotlineItem = $(this).closest('.hotline-container');
                            hotlineId = $(this).data('id');

                            $.ajax({
                                url: "{{ route('hotline.remove', 'hotlineId') }}".replace(
                                    'hotlineId', hotlineId),
                                method: "DELETE",
                                success(response) {
                                    response.status == 'warning' ? showWarningMessage(response
                                        .message) : (hotlineItem.remove(), showSuccessMessage(
                                            `Hotline number successfully removed.`),
                                        hotlineItem = "");

                                    if ($('.hotline-container').length == 0)
                                        $('.number-section').append(`<div class="empty-data-container">
                                            <img src="{{ asset('assets/img/Empty-Hotline.svg') }}" alt="Picture">
                                            <p>No hotline numbers added yet.</p>
                                        </div>`);
                                },
                                error: showErrorMessage
                            });
                        });
                    });

                    changeLogoBtn.click(() => $('#hotlineLogo').click());

                    $('#hotlineLogo').change(function() {
                        if (this.files[0]) {
                            if (!['image/jpeg', 'image/jpg', 'image/png'].includes(this.files[0].type)) {
                                if (operation == "add" || hotlineLogoChanged) {
                                    $(this).val('');
                                    previewLogo.attr('src', hotlineLogoChanged && operation == 'update' ?
                                        hotlineItem.find('.hotline-preview-image-list').attr('src') :
                                        '/assets/img/Select-Image.svg');
                                    if (hotlineLogoChanged) hotlineLogoChanged = false;
                                }
                                logoError.text('Please select an image file.')
                                    .prop('style', 'display: block !important');
                                changeButton(operation !== 'update');
                                return;
                            }
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewLogo.attr('src', e.target.result);
                            };
                            reader.readAsDataURL(this.files[0]);
                            hotlineLogoChanged = true;
                            logoError.prop('style', 'display: none !important');
                            changeButton();
                        } else {
                            previewLogo.attr('src', operation == 'add' ? '/assets/img/Select-Image.svg' :
                                hotlineItem.find('.hotline-preview-image-list').attr('src'));
                            operation == 'add' && changeButton(true);
                        }
                    });

                    $('#closeFormBtn').click((e) => {
                        e.preventDefault();
                        if (operation != 'add') hotlineItem.prop('hidden', 0);
                        $(".empty-data-container").prop('hidden', $(".empty-data-container").length > 1);
                        hotlineForm.prop('hidden', 1);
                        resetHotlineForm();
                        hotlineItem = "";
                        operation = "";
                        $('.btn-remove.removeNumber').prop('hidden', 0);
                    });

                    function resetHotlineForm() {
                        hotlineForm[0].reset();
                        hotlineLogoChanged = false;
                        previewLogo.attr('src', '/assets/img/Select-Image.svg');
                        changeButton(true);
                    }

                    function replaceHotlineItem(bool = true) {
                        hotlineForm.prop('hidden', bool);
                        hotlineItem.prop('hidden', !bool);
                    }

                    function changeButton(primary = false) {
                        changeLogoBtn.html(`<i class="bi bi-image"></i>${primary ? 'Select' : 'Change'} Logo`);
                        setInfoWindowButtonStyles(changeLogoBtn, `var(--color-${primary ? 'primary' : 'yellow'}`);
                    }
                });
            </script>
        @endif
    @endauth
</body>

</html>
