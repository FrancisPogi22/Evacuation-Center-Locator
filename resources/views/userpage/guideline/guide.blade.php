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
                        <i class="bi bi-file-earmark-richtext"></i>
                    </div>
                </div>
                <span>{{ strtoupper($guidelineLabel) }} GUIDES</span>
            </div>
            <hr>
            <div class="guide-header">
                <a href="{{ route('eligtas.guideline') }}" class="btn-submit">
                    <i class="bi bi-book"></i>View Guidelines
                </a>
            </div>
            <section class="guide-items-section">
                <div class="guides-container">
                    @forelse ($guide as $guide)
                        <div class="guide-content">
                            <div class="guide-label">{{ $guide->label }}</div>
                            <div class="guide-item">
                                <div class="guide-img">
                                    <img
                                        src="{{ $guide->guide_photo ? asset('guideline_image/' . $guide->guide_photo) : asset('assets/img/empty-data.svg') }}">
                                </div>
                                <div class="guide-details">
                                    <h1>{{ $guide->label }}</h1>
                                    <p>{{ $guide->content }}</p>
                                </div>
                                @auth
                                    @if (auth()->user()->is_disable == 0)
                                        <div class="guide-btn-container">
                                            <div class="guide-update-btn">
                                                <button class="btn-update updateGuideBtn" data-guide="{{ $guide->id }}">
                                                    <i class="bi bi-pencil-square"></i> Update
                                                </button>
                                            </div>
                                            <div class="guide-remove-btn">
                                                <button class="btn-remove removeGuideBtn" data-guide="{{ $guide->id }}">
                                                    <i class="bi bi-trash3-fill"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    @empty
                        <div class="empty-guide">
                            <img src="{{ asset('assets/img/empty-data.svg') }}" alt="Picture">
                            <p>No guide uploaded.</p>
                        </div>
                    @endforelse
                </div>
                <div class="weather-section">
                    <div class="current-temp-container">
                        <p class="current-temp"></p>
                        <p class="feels-like"></p>
                    </div>
                    <div class="location-description">
                        <p class="weather-desc"></p>
                        <p>Cabuyao, Laguna</p>
                    </div>
                    <div class="weather-img">
                        <img class="weather-icon" alt="icon">
                    </div>
                </div>
            </section>
            @include('userpage.guideline.guideModal')
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
        <script>
            fetch(
                    "https://api.openweathermap.org/data/2.5/weather?q=Cabuyao&appid={{ config('services.openWeather.key') }}&units=metric"
                ).then(response => response.json())
                .then(data => {
                    let {
                        main,
                        weather
                    } = data;
                    $('.current-temp').text(`${Math.round(main.temp)}°C`);
                    $('.feels-like').text(`Feels like ${Math.round(main.feels_like)}°C`);
                    $('.weather-desc').text(`${weather[0].description[0].toUpperCase()}${weather[0].description.slice(1)}`);
                    $('.weather-icon').attr('src', `http://openweathermap.org/img/wn/${weather[0].icon}@4x.png`);
                });
        </script>
        @if (auth()->user()->is_disable == 0)
            <script>
                $(document).ready(() => {
                    let guideId, validator, guideWidget, guideWidgetItem, defaultFormData, guideLabel,
                        guideContent, currentGuide, guideImageChanged = false,
                        guidelineId = $('.guidelineId').val(),
                        modal = $('#guideModal'),
                        modalLabel = $('.modal-label'),
                        modalLabelContainer = $('.modal-label-container'),
                        guideBtn = $('.guideImgBtn'),
                        guideImgInput = $('.guidePhoto'),
                        formButton = $('#submitGuideBtn'),
                        guides = $('.guide-content');

                    guides.click(function() {
                        this.classList.toggle('active');
                    })

                    validator = $("#guideForm").validate({
                        rules: {
                            label: 'required',
                            content: 'required'
                        },
                        messages: {
                            label: 'Please Enter Guide Label.',
                            content: 'Please Enter Guide Content.'
                        },
                        errorElement: 'span',
                        submitHandler: guideFormHandler
                    });

                    $(document).on('click', '.updateGuideBtn', function() {
                        currentGuide = this.closest('.guide-content');
                        guideWidget = $(this).closest('.guide-content');
                        guideWidgetItem = guideWidget.find('.guide-item');
                        guideId = $(this).data('guide');
                        modalLabelContainer.addClass('bg-warning');
                        modalLabel.text('Update Guide');
                        formButton.addClass('btn-update').removeClass('btn-submit').text('Update');
                        $('#image_preview_container').attr('src', guideWidgetItem.find('img').attr('src'));
                        guideLabel = guideWidgetItem.find('h1').text();
                        guideContent = guideWidgetItem.find('p').text();
                        $('#label').val(guideLabel);
                        $('#content').val(guideContent);

                        if (guideWidgetItem.find('img').attr('src').split('/').pop().split('.')[0] != "empty-data")
                            changeImageBtn('change');

                        modal.modal('show');
                    });

                    $(document).on('click', '.guideImgBtn', () => {
                        guideImgInput.click();
                    });

                    $(document).on('change', '#guidePhoto', function() {
                        let reader = new FileReader();

                        reader.onload = (e) => $('.guideImage').attr('src', e.target.result);
                        reader.readAsDataURL(this.files[0]);
                        guideImageChanged = true;
                        changeImageBtn('change');
                    });

                    $(document).on('click', '.removeGuideBtn', function() {
                        currentGuide = $(this.closest('.guide-content'));
                        guideId = $(this).data('guide');
                        confirmModal('Do you want to remove this guide?').then((result) => {
                            if (!result.isConfirmed) return;

                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: guideId,
                                url: "{{ route('guide.remove', 'guideId') }}".replace(
                                    'guideId', guideId),
                                method: "DELETE",
                                success(response) {
                                    return response.status == 'warning' ? showWarningMessage(
                                        response.message) : (showSuccessMessage(
                                            'Guide removed successfully.'),
                                        currentGuide.remove());
                                },
                                error: () => showErrorMessage()
                            });
                        });
                    });

                    function guideFormHandler(form) {
                        let formData = new FormData(form);

                        confirmModal(`Do you want to update this guide?`).then((result) => {
                            if (!result.isConfirmed) return;

                            return guideLabel == $('#label').val() && guideContent == $('#content').val() &&
                                !guideImageChanged ? showWarningMessage() :
                                $.ajax({
                                    data: formData,
                                    url: "{{ route('guide.update', 'guideId') }}".replace('guideId',
                                        guideId),
                                    method: "POST",
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success({
                                        status,
                                        message,
                                        label,
                                        content,
                                        guide_photo
                                    }) {
                                        if (status == 'warning') return showWarningMessage(message);

                                        if (guideImageChanged) $(currentGuide).find('.guide-img img').attr(
                                            'src', `{{ asset('guideline_image/${guide_photo}') }}`);

                                        let guideDetails = currentGuide.querySelector('.guide-details');
                                        guideDetails.querySelector('h1').textContent = label;
                                        guideDetails.querySelector('p').textContent = content;
                                        currentGuide.querySelector('.guide-label').textContent = label;
                                        showSuccessMessage(`Guide successfully updated.`);
                                        modal.modal('hide');
                                    },
                                    error: () => showErrorMessage()
                                });
                        });
                    }

                    function changeImageBtn(action) {
                        if (action == 'remove')
                            guideBtn.removeClass('bg-primary').html('<i class="bi bi-image"></i>Select Image');
                        else
                            guideBtn.addClass('bg-primary').html('<i class="bi bi-arrow-repeat"></i>Change Image');
                    }

                    modal.on('hidden.bs.modal', () => {
                        guideImageChanged = false;
                        validator.resetForm();
                        $('#guideForm')[0].reset();
                    });
                });
            </script>
        @endif
    @endauth
</body>

</html>
