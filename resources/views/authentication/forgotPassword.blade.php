@include('partials.authHeader')

<body class="auth-body">
    <div class="wrapper">
        <header class="header-section"></header>
        <section class="recover-container">
            <div class="recover-content">
                <div class="recover-form">
                    <form action="{{ route('findAccount') }}" method="POST">
                        @csrf
                        <div class="form-header">
                            <h1>Find Your Account</h1>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>Please enter your email address to search your
                                account.</label>
                            <input type="email" name="email" class="form-control p-2.5" placeholder="Email Address"
                                required>
                        </div>
                        <hr>
                        <div class="button-container">
                            <a class="btn-remove" href="{{ route('login') }}" class="text-white">Cancel</a>
                            <button type="submit" class="btn-submit">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <footer class="rec-bottom-section">
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
