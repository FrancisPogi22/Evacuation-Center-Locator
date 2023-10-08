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
                        <i class="bi bi-info-circle"></i>
                    </div>
                </div>
                <span>ABOUT</span>
            </div>
            <hr>
            <section class="about-content">
                <div class="location-section">
                    <div class="text-center">
                        <p>Address</p>
                    </div>
                    <div class="location-detail">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>CDDRRMO Address:</span>
                        <hr>
                        <a
                            href="https://www.google.com/maps/place/Retail+Plaza+City+of+Cabuyao/@14.2772989,121.1214,17z/data=!3m1!4b1!4m6!3m5!1s0x3397d8604aa8f17d:0x4e0371b3a9d5540e!8m2!3d14.2772937!4d121.1235887!16s%2Fg%2F11bxg2qw2w">
                            <p class="addressData">2nd Floor, Cabuyao Retail Plaza 4025
                                Cabuyao, Philippines
                            </p>
                        </a>
                    </div>
                    <div class="location-detail">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>CSWD Address:</span>
                        <hr>
                        <a
                            href="https://www.google.com/maps/place/Retail+Plaza+City+of+Cabuyao/@14.2772989,121.1214,17z/data=!3m1!4b1!4m6!3m5!1s0x3397d8604aa8f17d:0x4e0371b3a9d5540e!8m2!3d14.2772937!4d121.1235887!16s%2Fg%2F11bxg2qw2w">
                            <p class="addressData">2nd Floor, Cabuyao Retail Plaza 4025
                                Cabuyao, Philippines
                            </p>
                        </a>
                    </div>
                </div>
                <div class="right-side">
                    <div class="social-section">
                        <div><span>Social</span></div>
                        <hr>
                        <a href="https://www.facebook.com/CabuyaoCDRRMO">
                            <p><i class="bi bi-facebook"></i>CDRRMO CABUYAO</p>
                        </a>
                        <hr>
                        <a href="#">
                            <p><i class="bi bi-twitter"></i> Example 2</p>
                        </a>
                        <hr>
                        <a href="#">
                            <p><i class="bi bi-youtube"></i> Example 3</p>
                        </a>
                        <hr>
                        <a href="#">
                            <p><i class="bi bi-instagram"></i> Example 4</p>
                        </a>
                    </div>
                    <div class="telephone-section">
                        <div><span>Contact</span></div>
                        <hr>
                        <p><i class="bi bi-telephone-outbound-fill"></i> +49 5081 159</p>
                        <hr>
                        <a href="https://www.facebook.com/messages/t/242799609519367">
                            <p><i class="bi bi-messenger"></i> CDRRMO CABUYAO</p>
                        </a>
                        <hr>
                        <p> <i class="bi bi-envelope-at"></i> cdrrmocabuyao@gmail.com</p>
                    </div>
                </div>
            </section>
        </main>
        @auth
            @include('userpage.changePasswordModal')
        @endauth
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
    @endauth
</body>

</html>
