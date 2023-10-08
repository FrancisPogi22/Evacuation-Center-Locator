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
                        <i class="bi bi-card-checklist"></i>
                    </div>
                </div>
                <span>USER ACTIVITY LOG</span>
            </div>
            <hr>
            <section class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>Date Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($userActivityLogs as $activityLog)
                            <tr>
                                <td>{{ $activityLog->name . ' ' . $activityLog->activity . ': ' . $activityLog->data_name }}
                                </td>
                                <td>{{ $activityLog->date_time }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No activity logs available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
