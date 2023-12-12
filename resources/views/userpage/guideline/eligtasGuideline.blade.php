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
                        <button type="submit" class="search-icon" title="Search"><i class="bi bi-search"></i></button>
                    </form>
                    @auth
                        <button class="btn-submit" id="createGuidelineBtn">
                            <i class="bi bi-journal-plus"></i>Create Guideline
                        </button>
                        @include('userpage.guideline.guidelineModal')
                    @endauth
                </div>
                <div class="guideline-container">
                    @forelse ($guidelineData as $guidelineItem)
                        <div class="guideline-box">
                            <img src="{{ $guidelineItem->cover_image ? asset('guideline_image/' . $guidelineItem->cover_image) : asset('assets/img/Guideline-Cover.svg') }}"
                                class="cover-image">
                            <img src="{{ asset('guide_image/' . $guidelineItem->content_image) }}" class="content-image"
                                hidden>
                            <div class="guideline-body">
                                <div class="guideline-id">@auth{{ '(ID - ' . $guidelineItem->id . ')' }}@endauth
                                </div>
                                <div class="guideline-title">{{ $guidelineItem->type }}</div>
                                <div class="guideline-action-container">
                                    <button aria-id='{{ $guidelineItem->id }}' class="viewGuidelineBtn">
                                        <i class="bi bi-file-text"></i>View
                                    </button>
                                    @auth
                                        <button class="updateGuidelineBtn btn-update">
                                            <i class="bi bi-pencil-square"></i>Update
                                        </button>
                                        <button class="removeGuidelineBtn btn-remove">
                                            <i class="bi bi-journal-minus"></i>Remove
                                        </button>
                                    @endauth
                                </div>
                            </div>
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
            @auth
            let validator, guidelineId, guidelineItem, guidelineType, operation,
                guidelineImageChanged = false,
                btnText = $('#btn-text'),
                btnLoader = $('#btn-loader'),
                modal = $('#guidelineModal'),
                modalLabel = $('.modal-label'),
                modalDialog = $('.modal-dialog'),
                contentImage = $('#contentImage'),
                formBtn = $('#submitGuidelineBtn'),
                guidelineForm = $("#guidelineForm"),
                selectCoverImage = $('#selectCoverImage'),
                selectContentImage = $('#selectContentImage'),
                guidelineContainer = $('.guideline-container'),
                modalLabelContainer = $('.modal-label-container');

            validator = guidelineForm.validate({
                rules: {
                    type: 'required'
                },
                messages: {
                    type: 'Please Enter Guideline Type.'
                },
                errorElement: 'span',
                showErrors() {
                    this.defaultShowErrors();

                    contentImage.nextAll('span:first').text('Please select an image.')
                        .prop('style', `display: ${contentImage.val() == '' &&
                        guidelineImageChanged ?
                        'block' : 'none'} !important`);
                },
                submitHandler(form) {
                    if ($('#contentImage').val() == "") return;

                    let formData = new FormData(form);

                    confirmModal(`Do you want to ${operation} this guideline?`).then((result) => {
                        if (!result.isConfirmed) return;

                        return operation == "update" && guidelineType == $('#type')
                            .val() && !guidelineImageChanged ?
                            (showWarningMessage(), modal.modal('hide')) :
                            $.ajax({
                                data: formData,
                                url: operation == 'create' ?
                                    "{{ route('guideline.create') }}" :
                                    "{{ route('guideline.update', 'guidelineId') }}"
                                    .replace('guidelineId', guidelineId),
                                method: "POST",
                                cache: false,
                                contentType: false,
                                processData: false,
                                beforeSend() {
                                    btnLoader.prop('hidden', 0);
                                    btnText.text(operation == 'create' ?
                                        'Creating' : 'Updating');
                                    $('input, #selectCoverImage, #selectContentImage, #submitGuidelineBtn, #closeModalBtn')
                                        .prop('disabled', 1);
                                },
                                success(response) {
                                    if (response.status == 'warning')
                                        return showWarningMessage(response.message);

                                    let emptyGuideline = $('.empty-data-container'),
                                        {
                                            id,
                                            type,
                                            cover,
                                            content
                                        } = response;

                                    cover = cover ?
                                        `guideline_image/${cover}` :
                                        'assets/img/Guideline-Cover.svg';

                                    content = `guide_image/${content}`;

                                    if (operation == 'create') {
                                        if (guidelineContainer.find(emptyGuideline)
                                            .length > 0) emptyGuideline.remove();

                                        guidelineContainer.append(`<div class="guideline-box">
                                            <img src="{{ asset('${cover}') }}" class="cover-image">
                                            <img src="{{ asset('${content}') }}" class="content-image" hidden>
                                            <div class="guideline-body">
                                                <div class="guideline-id">@auth${'(ID - ' + id + ')'}@endauth</div>
                                                <h5 class="guideline-title">${type}</h5>
                                                <div class="guideline-action-container">
                                                    <button aria-id='${id}' class="viewGuidelineBtn">
                                                        <i class="bi bi-file-text"></i>View
                                                    </button>
                                                    @auth
                                                    <button class="updateGuidelineBtn btn-update">
                                                        <i class="bi bi-pencil-square"></i>Update
                                                    </button>
                                                    <button class="removeGuidelineBtn btn-remove">
                                                        <i class="bi bi-journal-minus"></i>Remove
                                                    </button>
                                                    @endauth
                                                </div>
                                            </div>
                                        </div>`);
                                    } else {
                                        guidelineItem.find('.guideline-title')
                                            .text(type);

                                        guidelineItem.find('.cover-image')
                                            .attr('src', cover);

                                        guidelineItem.find('.content-image')
                                            .attr('src', content);
                                    }
                                    modal.modal('hide');
                                    showSuccessMessage(
                                        `Guideline successfully ${operation}d.`);
                                },
                                error: showErrorMessage,
                                complete() {
                                    btnLoader.prop('hidden', 1);
                                    btnText.text(
                                        `${operation[0].toUpperCase()}${operation.slice(1)}`
                                    );
                                    $('input, #selectCoverImage, #selectContentImage, #submitGuidelineBtn, #closeModalBtn')
                                        .prop('disabled', 0);
                                }
                            });
                    });
                }
            });

            $('#createGuidelineBtn').click(() => {
                operation = "create";
                modalLabelContainer.removeClass('bg-warning');
                modalLabel.text('Create Guideline');
                formBtn.addClass('btn-submit').removeClass('btn-update');
                btnText.text('Create');
                changeButton(selectCoverImage, 'Cover Image', true);
                changeButton(selectContentImage, 'Content Image', true);
                modal.modal('show');
            });

            $(document).on('click', '.updateGuidelineBtn', function() {
                guidelineItem = $(this).closest('div.guideline-box');
                guidelineId = $(this).prev().attr('aria-id');
                let guidelineLabel = guidelineItem.find('.guideline-title').text();
                modalLabelContainer.addClass('bg-warning');
                modalLabel.text('Update Guideline');
                formBtn.addClass('btn-update').removeClass('btn-submit');
                btnText.text('Update');
                $('#type').val(guidelineLabel);
                guidelineType = guidelineLabel;
                operation = "update";
                modal.modal('show');
            });

            $(document).on('click', '.removeGuidelineBtn', function() {
                guidelineItem = $(this).closest('div.guideline-box');
                guidelineId = $(this).prev().prev().attr('aria-id');

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
                            guidelineItem.remove();

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

            modal.on('hidden.bs.modal', () => {
                validator.resetForm();
                guidelineForm[0].reset();
                guidelineImageChanged = false;
                $('#coverImagePreview, #contentImagePreview').attr('src', '/assets/img/Select-Image.svg');
            });

            selectCoverImage.click((e) => {
                e.preventDefault();
                $('#coverImage').click()
            });

            selectContentImage.click((e) => {
                e.preventDefault();
                contentImage.click()
            });

            $('#coverImage, #contentImage').change(function() {
                let imageInput = $(this),
                    buttonID = imageInput.nextAll('button:first').attr('id'),
                    changeImageBtn = $(`#${buttonID}`),
                    buttonText = `${buttonID == 'selectCoverImage' ? 'Cover' : 'Content' } Image`;

                if (this.files[0]) {
                    if (!['image/jpeg', 'image/jpg', 'image/png'].includes(this.files[0].type)) {
                        if (operation == "create" || guidelineImageChanged) {
                            imageInput.val('');
                            imageInput.next('img').attr('src', guidelineImageChanged ?
                                guidelineItem.find('.cover-image').attr('src') :
                                '/assets/img/Select-Image.svg');
                            if (guidelineImageChanged) guidelineImageChanged = false;
                        }
                        imageInput.nextAll('span:first').text('Please select an image file.')
                            .prop('style', 'display: block !important');
                        changeButton(changeImageBtn, buttonText, operation !== 'update');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imageInput.next('img').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(this.files[0]);
                    guidelineImageChanged = true;
                    imageInput.nextAll('span:first').prop('style', 'display: none !important');
                    changeButton(changeImageBtn, buttonText);
                } else {
                    imageInput.attr('id') == 'contentImage' &&
                        imageInput.nextAll('span:first').text('Please select an image file.')
                        .prop('style', 'display: block !important');

                    imageInput.next('img').attr('src', '/assets/img/Select-Image.svg');
                    changeButton(changeImageBtn, buttonText, true);
                }
            });
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
                    if (response.guidelineData == null)
                        return showWarningMessage('No guidelines uploaded.');

                    response.guidelineData.forEach(guideline => {
                        var guidelineId = guideline.id;

                        $(".guideline-box:has(.guideline-id:contains('" + guidelineId + "'))")
                            .toggle(false);
                        $(".guideline-box:not(:has(.guideline-id:contains('" + guidelineId +
                            "')))").toggle(true);
                    });
                },
                error: showErrorMessage
            });
        });

        $('#search_guideline').on('keyup', function() {
            let searchValue = $(this).val().trim();

            if (searchValue == "") $('.guideline-box').toggle(searchValue === "").filter(':visible').removeAttr(
                'hidden');
        });

        $(document).on('click', '.viewGuidelineBtn', function() {
            let id = $(this).attr('aria-id');

            window.location.href = '{{ $prefix }}' != 'resident' ?
                `{{ route('eligtas.guide', '') }}/${id}` : `{{ route('resident.eligtas.guide', '') }}/${id}`;
        });

        });
    </script>
</body>

</html>
