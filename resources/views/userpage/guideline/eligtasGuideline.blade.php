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
                        <i class="bi bi-book"></i>
                    </div>
                </div>
                <span>E-LIGTAS GUIDELINES</span>
            </div>
            <hr>
            <section class="content-item">
                <div class="guideline-header">
                    @auth
                        <button class="btn-submit" id="createGuidelineBtn">
                            <i class="bi bi-plus-lg"></i> Create Guideline
                        </button>
                        @include('userpage.guideline.guidelineModal')
                    @endauth
                    <form
                        action="{{ auth()->check() ? route('guideline.search') : route('resident.guideline.search') }}"
                        method="POST" class="search-container">
                        @method('GET')
                        @csrf
                        <input type="text" name="guideline_name" id="search_guideline" class="form-control"
                            placeholder="Search Guideline" autocomplete="off" required>
                        <button type="submit" class="search-icon"><i class="bi bi-search"></i></button>
                    </form>
                </div>
                <div class="guideline-container">
                    @forelse ($guidelineData as $guidelineItem)
                        <div class="guideline-widget">
                            @auth
                                @if (auth()->user()->is_disable == 0)
                                    <button id="updateGuidelineBtn">
                                        <i class="btn-update bi bi-pencil-square"></i>
                                    </button>
                                    <button id="removeGuidelineBtn">
                                        <i class="btn-remove bi bi-x-lg"></i>
                                    </button>
                                @endif
                                <a class="guidelines-item"
                                    href="{{ route('eligtas.guide', Crypt::encryptString($guidelineItem->id)) }}">
                                    <div class="guideline-content">
                                        <img
                                            src="{{ $guidelineItem->guideline_img ? asset('guideline_image/' . $guidelineItem->guideline_img) : asset('assets/img/empty-data.svg') }}">
                                        <div class="guideline-type">
                                            <p>{{ $guidelineItem->type }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endauth
                            @guest
                                <a class="guidelines-item"
                                    href="{{ route('resident.eligtas.guide', Crypt::encryptString($guidelineItem->id)) }}">
                                    <div class="guideline-content">
                                        <img
                                            src="{{ $guidelineItem->guideline_img ? asset('guideline_image/' . $guidelineItem->guideline_img) : asset('assets/img/empty-data.svg') }}">
                                        <div class="guideline-type">
                                            <p>{{ $guidelineItem->type }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endguest
                        </div>
                    @empty
                        <div class="empty-guidelines">
                            <img src="{{ asset('assets/img/empty-data.svg') }}" alt="Picture">
                            <p>No guidelines uploaded.</p>
                        </div>
                    @endforelse
                </div>
            </section>
            @include('userpage.changePasswordModal')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    @include('partials.script')
    @include('partials.toastr')
    @auth
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
            integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
            crossorigin="anonymous"></script>
        @if (auth()->user()->is_disable == 0)
            <script>
                $(document).ready(() => {
                    let validator, guidelineId, guidelineWidget, guidelineItem, defaultFormData, operation, guideField = 0,
                        guidelineType, modal = $('#guidelineModal'),
                        removeGuidelinebtn = $('.removeGuidelineImg'),
                        guidelineBtn = $('.guidelineImgBtn'),
                        guidelineImgChanged = false,
                        guidelineImg = $('.guidelineImage'),
                        guidelineImgInput = $('#guidelineImgInput'),
                        addGuideInput = $('#addGuideInput'),
                        modalLabel = $('.modal-label'),
                        modalDialog = $('.modal-dialog'),
                        modalLabelContainer = $('.modal-label-container'),
                        guidelineForm = $("#guidelineForm"),
                        formBtn = $('#submitGuidelineBtn'),
                        guideContentFields = document.getElementById("guideContentFields");

                    validator = guidelineForm.validate({
                        rules: {
                            type: 'required'
                        },
                        messages: {
                            type: 'Please Enter Guideline Type.'
                        },
                        errorElement: 'span',
                        submitHandler: guidelineFormSubmit
                    });

                    $(document).on('click', '#createGuidelineBtn', () => {
                        operation = "create";
                        modalLabelContainer.removeClass('bg-warning');
                        modalLabel.text('Create Guideline');
                        formBtn.text('Create').add(addGuideInput).addClass('btn-submit').removeClass(
                            'btn-update');
                        changeImageColor();
                        modal.modal('show');
                    });

                    $(document).on('click', '#updateGuidelineBtn', function() {
                        guidelineWidget = this.closest('.guideline-widget');
                        guidelineItem = guidelineWidget.querySelector('.guidelines-item');
                        guidelineId = guidelineItem.getAttribute('href').split('/').pop();

                        let guidelineLabel = guidelineItem.querySelector('.guideline-type p').innerText;

                        modalLabelContainer.addClass('bg-warning');
                        modalLabel.text('Update Guideline');
                        formBtn.text('Update').add(addGuideInput).addClass('btn-update').removeClass(
                            'btn-submit');
                        $('#guidelineType').val(guidelineLabel);
                        guidelineImg.attr('src', guidelineWidget.querySelector('.guideline-content img')
                            .getAttribute('src'));

                        if (guidelineImg.attr('src').split('/').pop().split('.')[0] != "empty-data")
                            changeImageBtn('change');

                        guidelineType = guidelineLabel;
                        operation = "update";
                        modal.modal('show');
                        defaultFormData = guidelineForm.serialize();
                    });

                    $(document).on('click', '#removeGuidelineBtn', function() {
                        guidelineWidget = this.closest('.guideline-widget');
                        guidelineItem = guidelineWidget.querySelector('.guidelines-item');
                        guidelineId = guidelineItem.getAttribute('href').split('/').pop();

                        confirmModal('Do you want to remove this guideline?').then((result) => {
                            if (!result.isConfirmed) return;

                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: guidelineId,
                                url: "{{ route('guideline.remove', 'guidelineId') }}"
                                    .replace('guidelineId', guidelineId),
                                method: "DELETE",
                                success(response) {
                                    return response.status == 'warning' ? showWarningMessage(
                                        response.message) : showSuccessMessage(
                                        'Guideline removed successfully, Please wait...', true);
                                },
                                error: () => showErrorMessage()
                            });
                        });
                    });

                    $(document).on('click', '#addGuideInput', () => {
                        let newGuideInputField = document.createElement("div");
                        newGuideInputField.classList.add("guide-field");
                        newGuideInputField.innerHTML = `
                        <div class="image-container">
                            <div class="guide-image">
                                <div class="image-preview">
                                    <a href="javascript:void(0)" class="btn-remove removeImage" id="removeImage${guideField}" hidden><i class="bi bi-trash3"></i></a>
                                    <img src="{{ asset('assets/img/e-ligtas-logo-${checkThemeColor()}.png') }}" alt="Image"
                                        class="guideImage" id="image_preview_container${guideField}">
                                </div>
                                <input type="file" name="guidePhoto[]" id="guidePhoto${guideField}" class="form-control guidePhoto" hidden>
                                <a href="javascript:void(0)" class="btn-submit selectImage" id="selectImage${guideField}"><i class="bi bi-image"></i>Choose Image</a>
                            </div>
                            </div>
                            <div class="guide-field-container">
                                <div class="field-container">
                                    <label>Guide Label</label>
                                    <input type="text" name="label[]" class="form-control" autocomplete="off"
                                        placeholder="Enter Guide Label">
                                </div>
                                <div class="field-container">
                                    <label>Guide Content</label>
                                    <textarea name="content[]" class="form-control" autocomplete="off" placeholder="Enter Guide Content" rows="7"></textarea>
                                </div>
                                <button class="btn-remove" id="removeGuideField"><i class="bi bi-trash3-fill"></i>Remove</button>
                            </div>
                        </div>`;
                        guideContentFields.appendChild(newGuideInputField);
                        changeModalSize('change');
                        guideField++;
                    });

                    $(document).on('click', '.selectImage', function() {
                        $(`#guidePhoto${$(this).attr('id').replace('selectImage', '')}`).click();
                    });

                    $(document).on('click', '.guidelineImgBtn', () => {
                        guidelineImgInput.click();
                    });

                    $(document).on('change', '#guidelineImgInput', function() {
                        let reader = new FileReader();

                        guidelineImgChanged = true;
                        reader.onload = (e) => guidelineImg.attr('src', e.target.result);
                        reader.readAsDataURL(this.files[0]);
                        changeImageBtn('change');
                    });

                    $(document).on('click', '.removeImage', function() {
                        let guideFieldId = $(this).attr('id').replace('removeImage', '');

                        $(`#guidePhoto${guideFieldId}`).val('');
                        $(`#image_preview_container${guideFieldId}`).attr('src',
                            `{{ asset('assets/img/e-ligtas-logo-${checkThemeColor()}.png') }}`);
                        $(this).prop('hidden', true);
                        $(`#selectImage${guideFieldId}`).removeClass('bg-primary').html(
                            '<i class="bi bi-image"></i>Choose Image');
                    });

                    $(document).on('change', '.guidePhoto', function() {
                        let reader = new FileReader(),
                            guideField = $(this).attr('id').replace('guidePhoto', '');

                        reader.onload = (e) => $(`#image_preview_container${guideField}`).attr('src', e.target
                            .result);
                        reader.readAsDataURL(this.files[0]);
                        $(`#selectImage${guideField}`).addClass('bg-primary').html(
                            '<i class="bi bi-arrow-repeat"></i>Change Image');
                        $(`#removeImage${guideField}`).prop('hidden', false);
                    });

                    $(document).on('click', '#removeGuideField', function() {
                        $(this).closest('.guide-field').remove();

                        if (checkGuideFields()) changeModalSize('remove');
                    });

                    modal.on('hidden.bs.modal', () => {
                        if (guidelineImgChanged) changeImageColor();

                        guidelineImgChanged = false;
                        removeGuidelinebtn.prop('hidden', true);
                        changeImageBtn('remove');
                        changeModalSize('remove');
                        validator.resetForm();
                        guideField = 0;
                        guideContentFields.innerHTML = '';
                        guidelineForm[0].reset();
                    });

                    function guidelineFormSubmit(form) {
                        let formData = new FormData(form);

                        confirmModal(`Do you want to ${operation} this guideline?`).then((result) => {
                            if (!result.isConfirmed) return;

                            return operation == "update" && guidelineType == $('#guidelineType').val() &&
                                checkGuideFields() && !guidelineImgChanged ?
                                showWarningMessage() :
                                $.ajax({
                                    data: formData,
                                    url: operation == 'create' ? "{{ route('guideline.create') }}" :
                                        "{{ route('guideline.update', 'guidelineId') }}".replace('guidelineId',
                                            guidelineId),
                                    method: "POST",
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success(response) {
                                        response.status == 'warning' ? showWarningMessage(response
                                            .message) : (modal.modal('hide'), showSuccessMessage(
                                            `Guideline successfully ${operation}d, Please wait...`, true
                                        ));
                                    },
                                    error: () => showErrorMessage()
                                });
                        });
                    }

                    function checkGuideFields() {
                        return guideContentFields.textContent.trim() == '' ? true : false;
                    }

                    function checkThemeColor() {
                        return sessionStorage.getItem('theme') == 'dark' ? 'white' : 'black';
                    }

                    function changeImageColor() {
                        guidelineImg.attr('src',
                            `{{ asset('assets/img/e-ligtas-logo-${checkThemeColor()}.png') }}`);
                    }

                    function changeImageBtn(action) {
                        if (action == 'remove')
                            guidelineBtn.removeClass('bg-primary').html('<i class="bi bi-image"></i>Choose Image');
                        else
                            guidelineBtn.addClass('bg-primary').html('<i class="bi bi-arrow-repeat"></i>Change Image');
                    }

                    function changeModalSize(action) {
                        if (action == 'remove')
                            modalDialog.removeClass('modal-xl').addClass('modal-lg');
                        else
                            modalDialog.removeClass('modal-lg').addClass('modal-xl');
                    }
                });
            </script>
        @endif
    @endauth
</body>

</html>
