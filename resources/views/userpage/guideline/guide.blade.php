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
                <span>{{ strtoupper($guideline->type) }} GUIDES</span>
            </div>
            <hr>
            <div class="guide-header">
                <a href="{{ auth()->check() ? route('eligtas.guideline') : route('resident.eligtas.guideline') }}"
                    class="btn-submit"><i class="bi bi-book"></i>View Guidelines
                </a>
            </div>
            <section class="guide-item-section">
                <div class="guide-container">
                    <div class="guide-image-container">
                        <div id="download-guide-container">
                            <button id="downloadGuideBtn">
                                <i class="bi bi-download"></i> Download
                            </button>
                        </div>
                        <img src="{{ asset('guide_image/' . $guideline->content_image) }}" id="guide-image"
                            alt="Picture">

                    </div>
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

            $('#downloadGuideBtn').click(function() {
                $(this).prop('disabled', 1);
                let imageSrc = $(this).parent().next().attr('src');

                fetch(imageSrc)
                    .then(response => response.blob())
                    .then(blob => {
                        let blobUrl = URL.createObjectURL(blob);
                        let downloadLink = Object.assign(document.createElement('a'), {
                            href: blobUrl,
                            download: 'guide_image.jpg'
                        });
                        document.body.appendChild(downloadLink).click();
                        document.body.removeChild(downloadLink);
                        URL.revokeObjectURL(blobUrl);
                        showSuccessMessage(`${downloadLink.download} has been downloaded`);
                        setTimeout(() => $(this).prop('disabled', 0), 5000);
                    });
            });

            function updateTime() {
                $(".current-time").text(`as of ${formatDateTime(new Date(), "time")}`);
            }
        });
    </script>
</body>

</html>
