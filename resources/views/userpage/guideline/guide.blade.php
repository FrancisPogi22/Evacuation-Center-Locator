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
                <a href="{{ auth()->check() ? route('eligtas.guideline') : route('resident.eligtas.guideline') }}"
                    class="btn-submit"><i class="bi bi-book"></i>View Guidelines
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
                                        src="{{ $guide->guide_photo ? asset('guideline_image/' . $guide->guide_photo) : asset('assets/img/Empty-Data.svg') }}">
                                </div>
                                <div class="guide-details">
                                    <h1>{{ $guide->label }}</h1>
                                    <p>{{ $guide->content }}</p>
                                </div>
                                @auth
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
                                @endauth
                            </div>
                        </div>
                    @empty
                        <div class="empty-data-container">
                            <img src="{{ asset('assets/img/Empty-Data.svg') }}" alt="Picture">
                            <p>No guide uploaded.</p>
                        </div>
                    @endforelse
                </div>
                <div class="weather-section">
                    <h4>Weather Forecast</h4>
                    <p class="current-time"></p>
                    <div class="weather-header">
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
                    <div class="weather-day">
                        <div class="sunrise-container">
                            <div class="sunrise-header">
                                <i class="bi bi-sunrise"></i>
                                <span>Sunrise</span>
                            </div>
                            <div class="sunset-details">
                                <p id="sunrise"></p>
                            </div>
                        </div>
                        <div class="sunset-container">
                            <div class="sunset-header">
                                <i class="bi bi-sunset"></i>
                                <span>Sunset</span>
                            </div>
                            <div class="sunset-details">
                                <p id="sunset"></p>
                            </div>
                        </div>
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
    @endauth
    <script>
        $(document).ready(() => {
            updateTime();
            setInterval(updateTime, 1000);
            fetch(
                    "https://api.openweathermap.org/data/2.5/weather?q=Cabuyao&appid={{ config('services.openWeather.key') }}&units=metric"
                ).then(response => response.json())
                .then(data => {
                    let {
                        main,
                        weather,
                        wind
                    } = data;

                    $('#sunrise').text(formatDateTime(data.sys.sunrise * 1000, "time"));
                    $('#sunset').text(formatDateTime(data.sys.sunset * 1000, "time"));
                    $('.current-temp').text(`${Math.round(main.temp)}°C`);
                    $('.feels-like').text(`Feels like ${Math.round(main.feels_like)}°C`);
                    $('.weather-desc').text(
                        `${weather[0].description[0].toUpperCase()}${weather[0].description.slice(1)}`);
                    $('.weather-icon').attr('src',
                        `http://openweathermap.org/img/wn/${weather[0].icon}@4x.png`);
                });

            $('.guide-content').click(function() {
                this.classList.toggle('active');
            });

            @auth
            let guideId, validator, guideWidget, guideWidgetItem, defaultFormData, guideLabel,
                guideContent, currentGuide, guideImageChanged = false,
                guidelineId = $('.guidelineId').val(),
                modal = $('#guideModal'),
                modalLabel = $('.modal-label'),
                form = $("#guideForm"),
                modalLabelContainer = $('.modal-label-container'),
                guideBtn = $('.guideImgBtn'),
                guideImgInput = $('.guidePhoto'),
                formButton = $('#submitGuideBtn');

            validator = form.validate({
                rules: {
                    label: 'required',
                    content: 'required'
                },
                messages: {
                    label: 'Please Enter Guide Label.',
                    content: 'Please Enter Guide Content.'
                },
                errorElement: 'span',
                submitHandler(form) {
                    let formData = new FormData(form);

                    confirmModal(`Do you want to update this guide?`).then((result) => {
                        if (!result.isConfirmed) return;

                        return guideLabel == $('#label').val() && guideContent == $('#content')
                            .val() && !guideImageChanged ? showWarningMessage() :
                            $.ajax({
                                data: formData,
                                url: "{{ route('guide.update', 'guideId') }}".replace(
                                    'guideId', guideId),
                                method: "POST",
                                cache: false,
                                contentType: false,
                                processData: false,
                                beforeSend() {
                                    $('#btn-loader').addClass('show');
                                    formButton.prop('disabled', 1);
                                },
                                success({
                                    status,
                                    message,
                                    label,
                                    content,
                                    guide_photo
                                }) {
                                    $('#btn-loader').removeClass('show');
                                    formButton.prop('disabled', 0);

                                    if (status == 'warning')
                                        return showWarningMessage(message);

                                    if (guideImageChanged)
                                        $(currentGuide).find('.guide-img img').attr('src',
                                            `{{ asset('guideline_image/${guide_photo}') }}`
                                        );

                                    let guideDetails = currentGuide.querySelector(
                                        '.guide-details');
                                    guideDetails.querySelector('h1').textContent = label;
                                    guideDetails.querySelector('p').textContent = content;
                                    currentGuide.querySelector('.guide-label').textContent =
                                        label;
                                    showSuccessMessage(`Guide successfully updated.`);
                                    modal.modal('hide');
                                },
                                error: showErrorMessage
                            });
                    });
                }
            });

            $(document).on('click', '.updateGuideBtn', function() {
                currentGuide = this.closest('.guide-content');
                guideWidget = $(this).closest('.guide-content');
                guideWidgetItem = guideWidget.find('.guide-item');
                guideId = $(this).data('guide');
                modalLabelContainer.addClass('bg-warning');
                modalLabel.text('Update Guide');
                formButton.addClass('btn-update').removeClass('btn-submit').append('Update');
                $('#image_preview_container').attr('src', guideWidgetItem.find('img').attr('src'));
                guideLabel = guideWidgetItem.find('h1').text();
                guideContent = guideWidgetItem.find('p').text();
                $('#label').val(guideLabel);
                $('#content').val(guideContent);

                if (guideWidgetItem.find('img').attr('src').split('/').pop().split('.')[0] !=
                    "empty-data")
                    changeImageBtn('change');

                modal.modal('show');
            });

            $('.guideImgBtn').click(() => guideImgInput.click());

            $('#guidePhoto').change(function() {
                let reader = new FileReader();

                guideImageChanged = true;
                reader.onload = (e) => ($('.guideImage').attr('src', e.target.result), changeImageBtn(
                    'change'));
                reader.readAsDataURL(this.files[0]);
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
                        url: "{{ route('guide.remove', 'guideId') }}".replace(
                            'guideId', guideId),
                        method: "DELETE",
                        success(response) {
                            if (response.status == 'warning')
                                return showWarningMessage(response.message);

                            let guidesContainer = $('.guides-container');

                            showSuccessMessage('Guide removed successfully.');
                            currentGuide.remove();

                            if (guidesContainer.text().trim() == "") {
                                guidesContainer.append(`<div class="empty-data-container">
                                            <img src="{{ asset('assets/img/Empty-Data.svg') }}" alt="Picture">
                                            <p>No guide uploaded.</p>
                                        </div>`);
                            }
                        },
                        error: showErrorMessage
                    });
                });
            });

            function changeImageBtn(action) {
                action == 'remove' ?
                    guideBtn.removeClass('bg-primary').html('<i class="bi bi-image"></i>Select Image') :
                    guideBtn.addClass('bg-primary').html('<i class="bi bi-arrow-repeat"></i>Change Image');
            }

            modal.on('hidden.bs.modal', () => {
                guideImageChanged = false;
                validator.resetForm();
                form[0].reset();
            });
        @endauth

        function updateTime() {
            $(".current-time").text(`as of ${formatDateTime(new Date(), "time")}`);
        }
        });
    </script>
</body>

</html>
