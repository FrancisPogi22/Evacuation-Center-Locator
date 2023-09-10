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
                <i class="bi bi-telephone"></i>
                <span>HOTLINE NUMBERS</span>
            </div>
            <hr>
            <section class="hotline-content mt-3">
                <div class="number-section">
                    <div>
                        <div class="fw-bold">
                            <i class="bi bi-hospital"></i>
                            Hotline Numbers:
                        </div>
                        <hr class="mt-3">
                        <p class="my-3">+12 3341 562 341</p>
                    </div>
                    <div class="hotline-container">
                        <span class="fw-bold">
                            <i class="bi bi-fire"></i>
                            Hotline Numbers:
                        </span>
                        <hr class="mt-3">
                        <p class="my-3">+12 3341 562 341</p>
                    </div>
                    <div class="hotline-container">
                        <span class="fw-bold">
                            <i class="bi bi-droplet"></i>
                            Hotline Numbers:
                        </span>
                        <hr class="mt-3">
                        <p class="my-3">+12 3341 562 341</p>
                    </div>
                    <div class="hotline-container">
                        <span class="fw-bold">
                            <i class="bi bi-tree"></i>
                            Hotline Numbers:
                        </span>
                        <hr class="mt-3">
                        <p class="mt-3">+12 3341 562 341</p>
                    </div>
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
    @auth
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
            integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
            crossorigin="anonymous"></script>
    @endauth
</body>

</html>
