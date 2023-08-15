@include('partials.authHeader')

<body class="auth-body">
    <div class="wrapper">
        <header class="header-section"></header>
        <div class="auth-content">
            <div class="auth-container">
                <div class="auth-header-desc">
                    <h1>{{ config('app.name') }}</h1>
                    <div class="auth-header-title">
                        <p>E-LIGTAS can help you to locate an evacuation centers in Cabuyao, Laguna, as well as provide
                            disaster preparedness information.</p>
                    </div>
                </div>
                <div class="auth-form-section">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="auth-email-container">
                            <input type="email" name="email" class="form-control"
                                value="{{ !empty(old('email')) ? old('email') : null }}" placeholder="Email Address"
                                required>
                        </div>
                        <div class="auth-password-container">
                            <input type="password" name="password" id="authPassword" class="form-control"
                                autocomplete="off" placeholder="Password">
                            <i class="bi bi-eye-slash" id="showAuthPassword"></i>
                        </div>
                        <div class="auth-btn-container">
                            <button type="submit" class="btn-login">Login</button>
                        </div>
                    </form>
                    <form action="{{ route('resident.guideline') }}" method="POST" class="py-2 px-3">
                        @method('GET')
                        @csrf
                        <button type="submit" class="btn-resident">
                            Continue as resident
                        </button>
                    </form>
                    <div class="forgot-password-container">
                        <a href="{{ route('recoverAccount') }}">Forgotten password?</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="auth-bottom-section">
            <hr>
            <p>E-LIGTAS @ {{ date('Y') }}</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    @include('partials.toastr')
    <script>
        $(document).ready(() => {
            $(document).on('click', '#showAuthPassword', function() {
                const authPassword = $("#authPassword");
                authPassword.attr('type', authPassword.attr('type') == 'password' ? 'text' : 'password');
                $(this).toggleClass("bi-eye-slash bi-eye");
            });
        });
    </script>
</body>

</html>
