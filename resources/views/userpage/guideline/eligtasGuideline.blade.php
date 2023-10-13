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
                <div class="guideline-container">
                    @foreach ($guidelineData as $guidelineItem)
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
                                        <img src="{{ asset('assets/img/cdrrmo-logo.png') }}" alt="Logo">
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
                                        <img src="{{ asset('assets/img/cdrrmo-logo.png') }}" alt="Logo">
                                        <div class="guideline-type">
                                            <p>{{ $guidelineItem->type }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endguest
                        </div>
                    @endforeach
                    @auth
                        <div class="guideline-btn">
                            <div class="btn-container">
                                <button id="createGuidelineBtn">
                                    <i class="btn-submit bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                        @include('userpage.guideline.guidelineModal')
                    @endauth
                </div>
            </section>
            @include('userpage.changePasswordModal')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    @include('partials.script')
    @auth
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
            integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
            crossorigin="anonymous"></script>
        @include('partials.toastr')
        @if (auth()->user()->is_disable == 0)
            <script>
                $(document).ready(() => {
                    let guidelineId, guidelineWidget, guidelineItem, defaultFormData, operation, guideField = 0,
                        guidelineType, modal = $('#guidelineModal'),
                        addGuideInput = $('#addGuideInput'),
                        modalLabel = $('.modal-label'),
                        modalLabelContainer = $('.modal-label-container'),
                        formButton = $('#submitGuidelineBtn');
                    const guideContentFields = document.getElementById("guideContentFields");

                    const validator = $("#guidelineForm").validate({
                        rules: {
                            type: 'required'
                        },
                        messages: {
                            type: 'Please Enter Guideline Type.'
                        },
                        errorElement: 'span',
                        submitHandler: formSubmitHandler
                    });

                    $(document).on('click', '#createGuidelineBtn', () => {
                        operation = "create";
                        modalLabelContainer.removeClass('bg-warning');
                        modalLabel.text('Create Guideline');
                        formButton.text('Create').add(addGuideInput).addClass('btn-submit').removeClass(
                            'btn-update');
                        modal.modal('show');
                    });

                    $(document).on('click', '#updateGuidelineBtn', function() {
                        modalLabelContainer.addClass('bg-warning');
                        modalLabel.text('Update Guideline');
                        formButton.text('Update').add(addGuideInput).addClass('btn-update').removeClass(
                            'btn-submit');
                        guidelineWidget = this.closest('.guideline-widget');
                        guidelineItem = guidelineWidget.querySelector('.guidelines-item');
                        guidelineId = guidelineItem.getAttribute('href').split('/').pop();
                        let guidelineType = guidelineItem.querySelector('.guideline-type p').innerText;
                        $('#guidelineType').val(guidelineType);
                        guidelineType = guidelineType;
                        operation = "update";
                        modal.modal('show');
                        defaultFormData = $('#guidelineForm').serialize();
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
                                        'Guideline removed successfully.', true);
                                },
                                error: () => showErrorMessage()
                            });
                        });
                    });

                    $(document).on('click', '#addGuideInput', function() {
                        const newGuideInputField = document.createElement("div");
                        newGuideInputField.classList.add("guide-field");
                        newGuideInputField.innerHTML = `
                        <div class="image-container">
                            <img src="{{ asset('assets/img/e-ligtas-logo-black.png') }}" alt="Picture"
                                class="guidePicture" id="image_preview_container${guideField}">
                                <span>
                                    <input type="file" name="guidePhoto[]" id="guidePhoto${guideField}" class="form-control guidePhoto">
                                </span>
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
                                <a href="javascript:void(0)" id="removeGuideField"><i class="bi bi-trash3-fill"></i>Remove</a>
                            </div>
                        </div>`;
                        guideContentFields.appendChild(newGuideInputField);
                        guideField++;
                    });

                    $(document).on('change', '.guidePhoto', function() {
                        let reader = new FileReader();
                        let guideField = $(this).attr('id').replace('guidePhoto', '');

                        reader.onload = (e) => {
                            $(`#image_preview_container${guideField}`).attr('src', e.target.result);
                        }
                        reader.readAsDataURL(this.files[0]);
                    });

                    $(document).on('click', '#removeGuideField', function() {
                        $(this).closest('.guide-field').remove();
                        guideField--;
                    });

                    function formSubmitHandler(form) {
                        let formData = new FormData(form);
                        let url = operation == 'create' ? "{{ route('guideline.create') }}" :
                            "{{ route('guideline.update', 'guidelineId') }}".replace('guidelineId',
                                guidelineId);

                        confirmModal(`Do you want to ${operation} this guideline?`).then((result) => {
                            if (!result.isConfirmed) return;

                            return operation == "update" && guidelineType == $('#guidelineType').val() &&
                                guideField <= 0 ?
                                showWarningMessage() :
                                $.ajax({
                                    data: formData,
                                    url: url,
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

                    modal.on('hidden.bs.modal', () => {
                        validator.resetForm();
                        guideField = 0;
                        guideContentFields.innerHTML = '';
                        $('#guidelineForm')[0].reset();
                    });
                });
            </script>
        @endif
    @endauth
</body>

</html>
