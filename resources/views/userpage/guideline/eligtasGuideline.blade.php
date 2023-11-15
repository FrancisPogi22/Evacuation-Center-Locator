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
                    <form class="search-container" id="searchGuidelineForm">
                        @csrf
                        <input type="text" name="guideline_name" id="search_guideline" class="form-control"
                            placeholder="Search Guideline" autocomplete="off" required>
                        <button type="submit" class="search-icon"><i class="bi bi-search"></i></button>
                    </form>
                    @auth
                        @if (auth()->user()->is_disable == 0)
                            <button class="btn-submit" id="createGuidelineBtn">
                                <i class="bi bi-plus-lg"></i>Create Guideline
                            </button>
                            @include('userpage.guideline.guidelineModal')
                        @endif
                    @endauth
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
                                <a class="guidelines-item" href="{{ route('eligtas.guide', $guidelineItem->id) }}">
                                    <div class="guideline-content">
                                        <img
                                            src="{{ $guidelineItem->guideline_img ? asset('guideline_image/' . $guidelineItem->guideline_img) : asset('assets/img/Empty-Guideline.svg') }}">
                                        <div class="guideline-type">
                                            <p>{{ $guidelineItem->type }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endauth
                            @guest
                                <a class="guidelines-item" href="{{ route('resident.eligtas.guide', $guidelineItem->id) }}">
                                    <div class="guideline-content">
                                        <img
                                            src="{{ $guidelineItem->guideline_img ? asset('guideline_image/' . $guidelineItem->guideline_img) : asset('assets/img/Empty-Guideline.svg') }}">
                                        <div class="guideline-type">
                                            <p>{{ $guidelineItem->type }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endguest
                        </div>
                    @empty
                        <div class="empty-data-container">
                            <img src="{{ asset('assets/img/Empty-Guideline.svg') }}" alt="Picture">
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
            integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
            crossorigin="anonymous"></script>
    @endauth
    <script>
        $(document).ready(() => {
            let guidelineContainer = $('.guideline-container');

            @auth
            @if (auth()->user()->is_disable == 0)
                let validator, guidelineId, guidelineWidget, guidelineItem, operation, guideField = 0,
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
                    guideContentFields = $("#guideContentFields");

                validator = guidelineForm.validate({
                    rules: {
                        type: 'required'
                    },
                    messages: {
                        type: 'Please Enter Guideline Type.'
                    },
                    errorElement: 'span',
                    submitHandler(form) {
                        let formData = new FormData(form);

                        confirmModal(`Do you want to ${operation} this guideline?`).then((result) => {
                            if (!result.isConfirmed) return;

                            return operation == "update" && guidelineType == $('#guidelineType')
                                .val() && checkGuideFields() && !guidelineImgChanged ?
                                showWarningMessage() :
                                $.ajax({
                                    data: formData,
                                    url: operation == 'create' ?
                                        "{{ route('guideline.create') }}" :
                                        "{{ route('guideline.update', 'guidelineId') }}"
                                        .replace(
                                            'guidelineId', guidelineId),
                                    method: "POST",
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success(response) {
                                        if (response.status == 'warning')
                                            return showWarningMessage(response.message)

                                        let emptyGuideline = $('.empty-data-container'),
                                            {
                                                guideline_id,
                                                type,
                                                guideline_img
                                            } = response;

                                        if (operation == 'create') {
                                            if (guidelineContainer.find(emptyGuideline)
                                                .length > 0) emptyGuideline.remove();

                                            guideline_img = guideline_img ?
                                                `guideline_image/${guideline_img}` :
                                                'assets/img/Empty-Data.svg';
                                            guidelineContainer.append(initGuidelineItem(
                                                guideline_id, guideline_img, type));
                                        } else {
                                            if (guidelineImgChanged)
                                                $(guidelineWidget).find(
                                                    '.guideline-content img').attr(
                                                    'src',
                                                    `{{ asset('guideline_image/${guideline_img}') }}`
                                                );

                                            guidelineWidget.querySelector(
                                                '.guideline-type p').textContent = type;
                                        }
                                        modal.modal('hide');
                                        showSuccessMessage(
                                            `Guideline successfully ${operation}d.`);
                                    },
                                    error: showErrorMessage
                                });
                        });
                    }
                });

                $('#createGuidelineBtn').click(() => {
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

                    if (guidelineImg.attr('src').split('/').pop().split('.')[0] != "Empty-Guideline")
                        changeImageBtn('change');

                    guidelineType = guidelineLabel;
                    operation = "update";
                    modal.modal('show');
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
                            url: "{{ route('guideline.remove', 'guidelineId') }}"
                                .replace('guidelineId', guidelineId),
                            method: "DELETE",
                            success(response) {
                                if (response.status == 'warning')
                                    showWarningMessage(response.message)

                                showSuccessMessage('Guideline removed successfully.');
                                guidelineWidget.remove();

                                if (guidelineContainer.text().trim() == "") {
                                    guidelineContainer.append(`<div class="empty-data-container">
                                            <img src="{{ asset('assets/img/Empty-Guideline.svg') }}" alt="Picture">
                                            <p>No guidelines uploaded.</p>
                                        </div>`);
                                }
                            },
                            error: showErrorMessage
                        });
                    });
                });

                $(document).on('click', '#addGuideInput', () => {
                    guideContentFields.append(`
                        <div class="guide-field">
                            <div class="image-container">
                                <div class="guide-image">
                                    <div class="image-preview">
                                        <a href="javascript:void(0)" class="btn-remove removeImage" id="removeImage${guideField}"
                                            hidden><i class="bi bi-trash3"></i></a>
                                        <img src="{{ asset('assets/img/E-Ligtas-Logo-${checkThemeColor()}.png') }}" alt="Image"
                                            class="guideImage" id="image_preview_container${guideField}">
                                    </div>
                                    <input type="file" name="guidePhoto[]" id="guidePhoto${guideField}"
                                        class="form-control guidePhoto" hidden>
                                    <a href="javascript:void(0)" class="btn-submit selectImage" id="selectImage${guideField}"><i
                                            class="bi bi-image"></i>Choose Image</a>
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
                        </div>
                    </div>`);
                    changeModalSize('change');
                    guideField++;
                });

                $(document).on('click', '.selectImage', function() {
                    $(`#guidePhoto${$(this).attr('id').replace('selectImage', '')}`).click();
                });

                $('.guidelineImgBtn').click(() => guidelineImgInput.click());

                $('#guidelineImgInput').change(function() {
                    let reader = new FileReader();

                    guidelineImgChanged = true;
                    reader.onload = (e) => (guidelineImg.attr('src', e.target.result),
                        changeImageBtn('change'));
                    reader.readAsDataURL(this.files[0]);
                });

                $(document).on('click', '.removeImage', function() {
                    let guideFieldId = $(this).attr('id').replace('removeImage', '');

                    $(`#guidePhoto${guideFieldId}`).val('');
                    $(`#image_preview_container${guideFieldId}`).attr('src',
                        `{{ asset('assets/img/E-Ligtas-Logo-${checkThemeColor()}.png') }}`);
                    $(this).prop('hidden', 1);
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
                    $(`#removeImage${guideField}`).prop('hidden', 0);
                });

                $(document).on('click', '#removeGuideField', function() {
                    $(this).closest('.guide-field').remove();

                    if (checkGuideFields()) changeModalSize('remove');
                });

                modal.on('hidden.bs.modal', () => {
                    if (guidelineImgChanged) changeImageColor();

                    guidelineImgChanged = false;
                    removeGuidelinebtn.prop('hidden', 1);
                    changeImageBtn('remove');
                    changeModalSize('remove');
                    validator.resetForm();
                    guideField = 0;
                    guideContentFields.html("");
                    guidelineForm[0].reset();
                });

                function initGuidelineItem(id, img, type) {
                    return `<div class="guideline-widget">
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
                                            href="{{ route('eligtas.guide', '') }}/${id}">
                                            <div class="guideline-content">
                                                <img src="{{ asset('${img}') }}">
                                                <div class="guideline-type">
                                                    <p>${type}</p>
                                                </div>
                                            </div>
                                        </a>
                                    @endauth
                                </div>`;
                }

                function checkGuideFields() {
                    return guideContentFields.text().trim() == '' ? true : false;
                }

                function checkThemeColor() {
                    return localStorage.getItem('theme') == 'dark' ? 'White' : 'Black';
                }

                function changeImageColor() {
                    guidelineImg.attr('src',
                        `{{ asset('assets/img/E-Ligtas-Logo-${checkThemeColor()}.png') }}`);
                }

                function changeImageBtn(action) {
                    action == 'remove' ?
                        guidelineBtn.removeClass('bg-primary').html('<i class="bi bi-image"></i>Choose Image') :
                        guidelineBtn.addClass('bg-primary').html('<i class="bi bi-arrow-repeat"></i>Change Image');
                }

                function changeModalSize(action) {
                    action == 'remove' ?
                        modalDialog.removeClass('modal-xl').addClass('modal-lg') :
                        modalDialog.removeClass('modal-lg').addClass('modal-xl');
                }
            @endif
        @endauth

        $('#searchGuidelineForm').on('submit', (e) => {
            e.preventDefault();

            $.ajax({
                url: "{{ auth()->check() ? route('guideline.search') : route('resident.guideline.search') }}",
                data: {
                    guideline_name: $('#search_guideline').val()
                },
                method: "GET",
                success(response) {
                    if (response.guidelineData == null) return showWarningMessage(
                        'No guidelines uploaded.');

                    guidelineContainer.empty();
                    response.guidelineData.forEach(guideline => {
                        let result_guideline_img = guideline.guideline_img ?
                            `guideline_image/${guideline.guideline_img}` :
                            'assets/img/Empty-Data.svg';

                        guidelineContainer.append(initGuidelineItem(guideline.id,
                            result_guideline_img, guideline.type));
                    });
                    $('#search_guideline').val("");
                },
                error: showErrorMessage
            });
        });

        function initGuidelineItem(id, img, type) {
            return `<div class="guideline-widget">
                @auth
                    @if (auth()->user()->is_disable == 0)
                        <button id="updateGuidelineBtn">
                            <i class="btn-update bi bi-pencil-square"></i>
                        </button>
                        <button id="removeGuidelineBtn">
                            <i class="btn-remove bi bi-x-lg"></i>
                        </button>
                    @endif
                    <a class="guidelines-item" href="{{ route('eligtas.guide', '') }}/${id}">
                        <div class="guideline-content">
                            <img src="{{ asset('${img}') }}">
                            <div class="guideline-type">
                                <p>${type}</p>
                            </div>
                        </div>
                    </a>
                @endauth
                @guest
                    <a class="guidelines-item" href="{{ route('resident.eligtas.guide', '') }}/${id}">
                        <div class="guideline-content">
                            <img src="{{ asset('${img}') }}">
                            <div class="guideline-type">
                                <p>${type}</p>
                            </div>
                        </div>
                    </a>
                @endguest
            </div>`;
        }
        });
    </script>
</body>

</html>
