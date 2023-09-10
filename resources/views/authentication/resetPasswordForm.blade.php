@include('partials.authHeader')

<body class="auth-body">
    <div class="wrapper">
        <header class="header-section"></header>
        <section class="res-password-cotainer">
            <div class="res-password-content">
                <div class="res-password-form">
                    <form action="{{ route('resetPassword') }}" method="POST">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="res-header-container">
                            <h1>Reset Password Form</h1>
                        </div>
                        <hr>
                        <div class="res-input-form">
                            <label>Email Address</label>
                            <input type="email" name="email" id="email" class="form-control p-2.5"
                                value="{{ !empty(old('email')) ? old('email') : null }}" placeholder="Email Address"
                                required autofocus>
                        </div>
                        <div class="res-input-form">
                            <label>Password</label>
                            <input type="password" name="password" id="password" class="form-control p-2.5"
                                placeholder="Enter Password" autocomplete="off" required autofocus>
                        </div>
                        <div class="res-input-form mb-4">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control p-2.5"
                                placeholder="Enter Confirm Password" autocomplete="off" onpaste="return false;" required
                                autofocus>
                        </div>
                        <hr>
                        <div class="res-button-container">
                            <button type="submit" class="btn-submit">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <footer class="res-bottom-container">
            <hr>
            <p>E-LIGTAS @ {{ date('Y') }}</p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    @include('partials.toastr')
</body>

</html>
