<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+zwdN9+nJOZ" crossorigin="anonymous">
    <link rel="shortcut icon" href="{{ asset('assets/img/e-ligtas-logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <title>{{ config('app.name') }}</title>
</head>

<body id="login-container">
    <div class="wrapper">
        <div class="header-section drop-shadow-lg"></div>

        <div class="login-section relative m-auto">
            <div class="login-content flex justify-around">
                <div class="header-desc pt-5">
                    <h1 class="text-white tracking-wide font-bold">{{ config('app.name') }}</h1>
                    <div class="pt-4">
                        <p class="text-slate-400">E-LIGTAS help you to locate evacuation center and to share
                            knowledge about disaster preparedness.</p>
                    </div>
                </div>
                <div class="login-form-section h-3/4 bg-slate-100 drop-shadow-2xl rounded">
                    <form action="{{ route('login') }}" method="POST" class="px-3">
                        @csrf
                        <div class="my-3">
                            <input type="email" name="email" class="form-control"
                                value="{{ !empty(old('email')) ? old('email') : null }}" placeholder="Email Address"
                                required>
                        </div>
                        <div class="my-3 relative">
                            <input type="password" name="password" id="password" class="form-control p-3"
                                autocomplete="off" placeholder="Password">
                            <i class="bi bi-eye-slash absolute cursor-pointer text-2xl " id="show-password"></i>
                        </div>
                        <div class="login-btn">
                            <button type="submit" class="btn-submit text-white rounded w-full font-extrabold bg-slate-700 hover:bg-slate-800">Login</button>
                        </div>
                    </form>
                    <form action="{{ route('guideline.resident') }}" method="POST" class="py-2 px-3">
                        @method('GET')
                        @csrf
                        <button type="submit"
                            class="btn-resident text-white rounded w-full font-extrabold bg-red-600 hover:bg-red-700 ">
                            Continue as resident
                        </button>
                    </form>
                    <div class="flex justify-center text-center mt-6 text-sky-600">
                        <a href="">Forgotten password?</a>
                    </div>
                    <hr class="my-10 mx-4">
                </div>
            </div>
        </div>
        <div class="bottom-section text-white l-0 w-full pb-5">
            <hr>
            <p id="year" class="text-slate-400"></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>

    <script>
        document.getElementById("year").innerHTML = "E-LIGTAS @ " + new Date().getFullYear();

        let eyeicon = document.getElementById("show-password"),
            password = document.getElementById("password");
        eyeicon.onclick = function() {
            if (password.type == "password") {
                this.classList.toggle("bi-eye");
                password.type = "text";
            } else {
                this.classList.toggle("bi-eye");
                password.type = "password";
            }
        }
    </script>

</body>

</html>
