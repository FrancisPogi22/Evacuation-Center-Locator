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
                                @include('userpage.hotlineNumber.hotlineNumberModal')
                            </div>
                        @endif
                    @endif
                @endauth
                <div class="number-section">
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
                                    <div class="header-btn-container">
                                        @if (auth()->user()->is_disable == 0)
                                            <button class="btn-update updateNumber"
                                                data-id="{{ $hotlineNumber->id }}"><i class="bi bi-pencil"></i></button>
                                            <button class="btn-remove removeNumber"
                                                data-id="{{ $hotlineNumber->id }}"><i class="bi bi-trash3"></i></button>
                                        @endif
                                    </div>
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
                        hotlineItem, formBtn = $('#addNumberBtn'),
                        previewLogo = $('#hotlinePreviewLogo'),
                        form = $('#hotlineForm'),
                        modalLabelContainer = $('.modal-label-container'),
                        modalLabel = $('.modal-label'),
                        hotlineLogo = $('.hotlineLogo'),
                        hotlineLogoBtn = $('#selectLogo'),
                        modal = $('#addHotlineNumberModal');

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    validator = $('#hotlineForm').validate({
                        rules: {
                            label: 'required',
                            number: 'required'
                        },
                        messages: {
                            label: 'Please enter number label.',
                            number: 'Please enter hotline number.'
                        },
                        errorElement: 'span',
                        submitHandler: hotlineNumberHandler
                    });

                    $(document).on('click', '#addNumberBtnModal', () => {
                        modalLabelContainer.removeClass('bg-warning');
                        modalLabel.text('Add Hotline Number');
                        formBtn.addClass('btn-submit').removeClass('btn-update').text('Add');
                        operation = "add";
                        changeLogoColor();
                        modal.modal('show');
                    });

                    $(document).on('click', '.updateNumber', function() {
                        modalLabelContainer.addClass('bg-warning');
                        modalLabel.text('Update Hotline Number');
                        formBtn.addClass('btn-update').removeClass('btn-submit').text('Update');
                        hotlineItem = $(this).closest('.hotline-container');
                        hotlineLabel = hotlineItem.find('.hotline-label span').text();
                        hotlineNumber = hotlineItem.find('.hotline-number p').text();
                        previewLogo.attr('src', hotlineItem.find('.hotlineLogo').attr('src'));
                        hotlineId = $(this).data('id');

                        if (previewLogo.attr('src').split('/').pop().split('.')[0] != "empty-data")
                            changeLogoBtn('change');

                        $('#hotlineLabel').val(hotlineLabel);
                        $('#hotlineNumber').val(hotlineNumber);
                        operation = "update";
                        modal.modal('show');
                    });

                    $(document).on('click', '.removeNumber', function() {
                        let hotlineNumberItem = $(this).closest('.hotline-container');
                        hotlineId = $(this).data('id');

                        confirmModal(`Do you want to remove this hotline number?`).then((result) => {
                            if (!result.isConfirmed) return;

                            $.ajax({
                                url: "{{ route('hotline.remove', 'hotlineId') }}".replace(
                                    'hotlineId', hotlineId),
                                method: "DELETE",
                                success(response) {
                                    response.status == 'warning' ? showWarningMessage(response
                                        .message) : (hotlineNumberItem.remove(),
                                        showSuccessMessage(
                                            `Hotline Number successfully removed.`),
                                        modal.modal('hide'));
                                },
                                error: () => showErrorMessage()
                            });
                        });
                    });

                    $(document).on('click', '#selectLogo', () => {
                        $('#hotlineLogo').click();
                    });

                    $(document).on('change', '#hotlineLogo', function() {
                        let reader = new FileReader();

                        hotlineLogoChanged = true;
                        reader.onload = (e) => previewLogo.attr('src', e.target.result);
                        reader.readAsDataURL(this.files[0]);
                        changeLogoBtn('change');
                    });

                    modal.on('hidden.bs.modal', () => {
                        validator.resetForm();
                        hotlineLogoChanged = false;
                        changeLogoBtn('remove');
                        changeLogoColor()
                        form[0].reset();
                    });

                    function hotlineNumberHandler(form) {
                        let formData = new FormData(form);

                        confirmModal(`Do you want to ${operation} this hotline number?`).then((result) => {
                            if (!result.isConfirmed) return;

                            return operation == "update" && hotlineLabel == $('#hotlineLabel').val() &&
                                hotlineNumber == $('#hotlineNumber').val() && !hotlineLogoChanged ?
                                showWarningMessage() :
                                $.ajax({
                                    data: formData,
                                    url: operation == 'add' ? "{{ route('hotline.add') }}" :
                                        "{{ route('hotline.update', 'hotlineId') }}".replace('hotlineId',
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
                                                    hotlineItem.find('.hotlineLogo').attr('src', previewLogo
                                                        .attr('src'));

                                                hotlineItem.find('.hotline-label span').text(label);
                                                hotlineItem.find('.hotline-number p').text(number);
                                            } else {
                                                let newHotlineNumber = `
                                                <div class="hotline-container">
                                                    <div class="hotline-logo">
                                                        <img src="${hotlineLogo ? `/assets/img/${hotlineLogo}` : '/assets/img/empty-data.svg'}" class="hotlineLogo" alt="logo">
                                                    </div>
                                                    <div class="hotline-content">
                                                        <div class="hotline-header">
                                                            <div class="hotline-label">
                                                                <i class="bi bi-hospital"></i>
                                                                <span>${label}</span>
                                                            </div>
                                                            <div class="header-btn-container">
                                                                <button class="btn-update updateNumber" data-id="${hotlineId}"><i class="bi bi-pencil"></i></button>
                                                                <button class="btn-remove removeNumber" data-id="${hotlineId}"><i class="bi bi-trash3"></i></button>
                                                            </div>
                                                        </div>
                                                        <div class="hotline-number">
                                                            <hr>
                                                            <p>${number}</p>
                                                        </div>
                                                    </div>
                                                </div>`;
                                                $('.number-section').append(newHotlineNumber);
                                            }

                                            showSuccessMessage(
                                                `Hotline number successfully ${operation == "add" ? "added" : "updated"}.`
                                            );
                                            modal.modal('hide');
                                            hotlineItem = "";
                                        }
                                    },
                                    error: () => showErrorMessage()
                                });
                        });
                    }

                    function checkThemeColor() {
                        return sessionStorage.getItem('theme') == 'dark' ? 'white' : 'black';
                    }

                    function changeLogoColor() {
                        previewLogo.attr('src',
                            `{{ asset('assets/img/e-ligtas-logo-${checkThemeColor()}.png') }}`);
                    }

                    function changeLogoBtn(action) {
                        if (action == 'remove')
                            hotlineLogoBtn.removeClass('bg-primary').html('<i class="bi bi-image"></i>Select Logo');
                        else
                            hotlineLogoBtn.addClass('bg-primary').html('<i class="bi bi-arrow-repeat"></i>Change Logo');
                    }
                });
            </script>
        @endif
    @endauth
</body>

</html>
