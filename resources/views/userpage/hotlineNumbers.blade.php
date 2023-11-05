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
                @auth
                    @if (auth()->user()->is_disable == 0)
                        @if (auth()->user()->position == 'President' || auth()->user()->position == 'Focal')
                            <div class="page-button-container">
                                <button class="btn-submit" id="addNumberBtnModal">
                                    <i class="bi bi-telephone-plus"></i>Add Hotline Number
                                </button>
                            </div>
                        @endif
                    @endif
                @endauth
                <div class="number-section">
                    @auth
                        <form id="hotlineForm" hidden>
                            @csrf
                            <div class="hotline-container">
                                <div class="hotline-logo">
                                    <img src="{{ asset('assets/img/e-ligtas-logo-black.png') }}" alt="logo"
                                        id="hotlinePreviewLogo">
                                    <input type="file" name="logo" class="form-control" id="hotlineLogo" hidden>
                                    <a href="javascript:void(0)" class="btn-table-primary" id="selectLogo">
                                        <i class="bi bi-image"></i>Select Logo</a>
                                </div>
                                <div class="hotline-details">
                                    <div class="hotline-header">
                                        <div class="hotline-label">
                                            <input type="text" name="label" class="form-control" autocomplete="off"
                                                placeholder="Enter Hotline Label" id="hotlineLabel">
                                        </div>
                                    </div>
                                    <div class="hotline-number">
                                        <hr>
                                        <input type="number" name="number" id="hotlineNumber" class="form-control"
                                            placeholder="Enter Hotline Number" autocomplete="off">
                                    </div>
                                    <div class="add-hotline-btn">
                                        <button class="btn-submit" id="addNumberBtn"></button>
                                        <button class="btn-remove" id="closeFormBtn">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endauth
                    @foreach ($hotlineNumbers as $hotlineNumber)
                        <div class="hotline-container">
                            <div class="hotline-logo">
                                <img src="{{ $hotlineNumber->logo ? asset('assets/img/' . $hotlineNumber->logo) : asset('assets/img/empty-data.svg') }}"
                                    class="hotlineLogo" alt="logo">
                            </div>
                            <div class="hotline-details">
                                <div class="hotline-header">
                                    <div class="hotline-label">
                                        <i class="bi bi-hospital"></i>
                                        <span>{{ $hotlineNumber->label }}</span>
                                    </div>
                                    @auth
                                        <div class="header-btn-container">
                                            @if (auth()->user()->is_disable == 0)
                                                <button class="btn-update updateNumber"
                                                    data-id="{{ $hotlineNumber->id }}"><i class="bi bi-pencil"></i></button>
                                                <button class="btn-remove removeNumber"
                                                    data-id="{{ $hotlineNumber->id }}"><i class="bi bi-trash3"></i></button>
                                            @endif
                                        </div>
                                    @endauth
                                </div>
                                <div class="hotline-number">
                                    <hr>
                                    <p>{{ $hotlineNumber->number }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </main>
        @auth
            @include('userpage.changePasswordModal')
        @endauth
    </div>

    @include('partials.script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    @include('partials.toastr')
    @auth
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
            integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
            crossorigin="anonymous"></script>
        @if (auth()->user()->is_disable == 0)
            <script>
                $(document).ready(() => {
                    let operation, hotlineLabel, hotlineNumber, hotlineId, validator, hotlineLogoChanged = false,
                        hotlineItem = "",
                        formBtn = $('#addNumberBtn'),
                        previewLogo = $('#hotlinePreviewLogo'),
                        hotlineForm = $('#hotlineForm'),
                        hotlineLogo = $('.hotlineLogo'),
                        hotlineLogoBtn = $('#selectLogo');

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
                        submitHandler(form) {
                            let formData = new FormData(form);

                            confirmModal(`Do you want to ${operation} this hotline number?`).then((result) => {
                                if (!result.isConfirmed) return;

                                return operation == "update" && hotlineLabel == $('#hotlineLabel').val() &&
                                    hotlineNumber == $('#hotlineNumber').val() && !hotlineLogoChanged ?
                                    showWarningMessage() :
                                    $.ajax({
                                        data: formData,
                                        url: operation == 'add' ? "{{ route('hotline.add') }}" :
                                            "{{ route('hotline.update', 'hotlineId') }}".replace(
                                                'hotlineId',
                                                hotlineId),
                                        method: "POST",
                                        cache: false,
                                        contentType: false,
                                        processData: false,
                                        success(response) {
                                            if (response.status == 'warning') {
                                                showWarningMessage(response.message);
                                            } else {
                                                let {
                                                    label,
                                                    number,
                                                    hotlineLogo,
                                                    hotlineId
                                                } = response;

                                                if (operation == "update") {
                                                    if (hotlineLogoChanged)
                                                        hotlineItem.find('.hotlineLogo').attr('src',
                                                            previewLogo
                                                            .attr('src'));

                                                    hotlineItem.find('.hotline-label span').text(label);
                                                    hotlineItem.find('.hotline-number p').text(number);
                                                    replaceHotlineItem();
                                                    resetHotlineForm();
                                                } else {
                                                    $('.number-section').append(`
                                                        <div class="hotline-container">
                                                            <div class="hotline-logo">
                                                                <img src="${hotlineLogo ? `/assets/img/${hotlineLogo}` : '/assets/img/empty-data.svg'}" class="hotlineLogo" alt="logo">
                                                            </div>
                                                            <div class="hotline-details">
                                                                <div class="hotline-header">
                                                                    <div class="hotline-label">
                                                                        <i class="bi bi-hospital"></i>
                                                                        <span>${label}</span>
                                                                    </div>
                                                                    <div class="header-btn-container">
                                                                        @auth
                                                                            <div class="header-btn-container">
                                                                                @if (auth()->user()->is_disable == 0)
                                                                                    <button class="btn-update updateNumber" data-id="${hotlineId}"><i class="bi bi-pencil"></i></button>
                                                                                    <button class="btn-remove removeNumber" data-id="${hotlineId}"><i class="bi bi-trash3"></i></button>
                                                                                @endif
                                                                            </div>
                                                                        @endauth
                                                                    </div>
                                                                </div>
                                                                <div class="hotline-number">
                                                                    <hr>
                                                                    <p>${number}</p>
                                                                </div>
                                                            </div>
                                                        </div>`);
                                                    resetHotlineForm();
                                                    hotlineForm.prop('hidden', 1);
                                                }
                                                showSuccessMessage(
                                                    `Hotline number successfully ${operation == "add" ? "added" : "updated"}.`
                                                );
                                                hotlineItem = "";
                                            }
                                        },
                                        error: showErrorMessage
                                    });
                            });
                        }
                    });

                    $('#addNumberBtnModal').click(() => {
                        if (hotlineItem) {
                            replaceHotlineItem();
                            resetHotlineForm();
                            hotlineItem = "";
                        }

                        operation = "add";
                        changeLogoColor();
                        validator.resetForm();
                        hotlineForm.prop('hidden', 0);
                        formBtn.removeClass('bg-warning').text('Add');
                    });

                    $(document).on('click', '.updateNumber', function() {
                        validator.resetForm();
                        hotlineItem && replaceHotlineItem();
                        hotlineItem = $(this).closest('.hotline-container');
                        hotlineForm.prop('hidden', 0);
                        hotlineItem.prop('hidden', 1);
                        hotlineLabel = hotlineItem.find('.hotline-label span').text();
                        hotlineNumber = hotlineItem.find('.hotline-number p').text();
                        hotlineId = $(this).data('id');
                        previewLogo.attr('src', hotlineItem.find('.hotlineLogo').attr('src'));
                        changeLogoBtn(previewLogo.attr('src').split('/').pop().split('.')[0] != "empty-data" ?
                            'change' : 'remove');
                        $('#hotlineLabel').val(hotlineLabel);
                        $('#hotlineNumber').val(hotlineNumber);
                        formBtn.addClass('bg-warning').text('Update');
                        operation = "update";
                    });

                    $(document).on('click', '.removeNumber', function() {
                        hotlineItem = $(this).closest('.hotline-container');
                        hotlineId = $(this).data('id');

                        confirmModal(`Do you want to remove this hotline number?`).then((result) => {
                            if (!result.isConfirmed) return;

                            $.ajax({
                                url: "{{ route('hotline.remove', 'hotlineId') }}".replace(
                                    'hotlineId', hotlineId),
                                method: "DELETE",
                                success(response) {
                                    response.status == 'warning' ? showWarningMessage(response
                                        .message) : (hotlineItem.remove(), showSuccessMessage(
                                            `Hotline number successfully removed.`),
                                        hotlineItem = "");
                                },
                                error: showErrorMessage
                            });
                        });
                    });

                    $('#selectLogo').click(() => {
                        $('#hotlineLogo').click();
                    });

                    $('#hotlineLogo').change(function() {
                        let reader = new FileReader();

                        hotlineLogoChanged = true;
                        reader.onload = (e) => $('#hotlinePreviewLogo').attr('src', e.target.result);
                        reader.readAsDataURL(this.files[0]);
                        changeLogoBtn('change');
                    });

                    $('#closeFormBtn').click((e) => {
                        e.preventDefault();

                        if (operation != 'add') hotlineItem.prop('hidden', 0);

                        hotlineForm.prop('hidden', 1);
                        resetHotlineForm();
                        hotlineItem = "";
                    });

                    function resetHotlineForm() {
                        hotlineForm[0].reset();
                        changeLogoBtn('remove');
                        hotlineLogoChanged = false;
                    }

                    function replaceHotlineItem() {
                        hotlineForm.prop('hidden', 1);
                        hotlineItem.prop('hidden', 0);
                    }

                    function checkThemeColor() {
                        return sessionStorage.getItem('theme') == 'dark' ? 'white' : 'black';
                    }

                    function changeLogoColor() {
                        previewLogo.attr('src',
                            `{{ asset('assets/img/e-ligtas-logo-${checkThemeColor()}.png') }}`);
                    }

                    function changeLogoBtn(action) {
                        $('#selectLogo').toggleClass('bg-warning', action != 'remove').html(action == 'remove' ?
                            '<i class="bi bi-image"></i>Select Logo' : '<i class="bi bi-arrow-repeat"></i>Change Logo');
                    }
                });
            </script>
        @endif
    @endauth
</body>

</html>
