<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <title>{{ config('app.name') }}</title>
</head>

<body>
    <div class="wrapper">
        @include('sweetalert::alert')
        @include('partials.header')
        @include('partials.sidebar')

        <x-messages />

        <h1 class="text-center bg-slate-600 my-2 text-white text-4xl p-3 font-bold">E-Ligtas Guides</h1>

        <div class="guide-btn flex justify-end">
            @if (auth()->check() && auth()->user()->user_role == 'CDRRMO' || auth()->check() && auth()->user()->user_role == 'CSWD')
                <a href="javascript:void(0)" id="createGuideBtn"
                    class="bg-green-700 hover:bg-green-800 p-2 m-2 rounded font-medium text-white drop-shadow-xl transition ease-in-out delay-150 hover:scale-105 duration-100">
                    <i class="bi bi-plus-lg mr-2"></i> Create Guide
                </a>
                <input type="hidden" class="guideline_id" value="{{ $guidelineId }}">
                @include('userpage.guideline.addGuide')
            @endif
        </div>

        <div class="main-content">
            @foreach ($guide as $guide)
                <div class="guide-container">
                    <div class="guide-content relative mx-2.5 my-2">
                        <div class="label relative bg-slate-600 text-white cursor-pointer p-3 uppercase">
                            {{ $guide->label }}
                        </div>
                        <div class="content relative h-0 overflow-hidden drop-shadow-lg bg-slate-50">
                            <p class="mb-2">
                                {{ $guide->content }}
                            </p>
                            @if (auth()->check() && auth()->user()->user_role == 'CDRRMO' || auth()->check() && auth()->user()->user_role == 'CSWD')
                                <div class="action-btn py-2 flex justify-start">
                                    <a href="#edit{{ $guide->id }}" data-bs-toggle="modal">
                                        <button type="submit"
                                            class="bg-slate-600 p-2 text-white rounded drop-shadow-lg hover:bg-slate-700">
                                            <i class="bi bi-pencil text-sm mr-2"></i>Edit
                                        </button>
                                    </a>
                                    <a href="{{ route('remove.guide.cdrrmo', $guide->id) }}">
                                        <button type="submit"
                                            class="bg-red-700 ml-2 p-2 text-white rounded drop-shadow-lg hover:bg-red-800">
                                            <i class="bi bi-trash mr-2"></i>Remove
                                        </button>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @include('userpage.guideline.updateGuide')
            @endforeach
        </div>
    </div>

    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"
        integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    @if (auth()->check())
        <script type="text/javascript">
            $(document).ready(function() {
                const accordion = document.getElementsByClassName('guide-content');

                for (i = 0; i < accordion.length; i++) {
                    accordion[i].addEventListener('click', function() {
                        this.classList.toggle('active')
                    })
                }

                $('#createGuideBtn').click(function() {
                    $('#create_guide_id').val('');
                    $('#guideline_id').val('');
                    $('#createGuideForm').trigger("reset");
                    $('#createGuideModal').modal('show');
                });

                $('#submitGuideBtn').click(function(e) {
                    var guideline_id = $('.guideline_id').val();
                    e.preventDefault();

                    Swal.fire({
                        icon: 'question',
                        title: 'Would you like to post this guide?',
                        showDenyButton: true,
                        confirmButtonText: 'Yes, post it.',
                        confirmButtonColor: '#334155',
                        denyButtonText: 'Double Check'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                data: $('#createGuideForm').serialize(),
                                url: "{{ route('add.guide.cdrrmo', ':guideline_id') }}"
                                    .replace(
                                        ':guideline_id', guideline_id),
                                type: "POST",
                                dataType: 'json',
                                beforeSend: function(data) {
                                    $(document).find('span.error-text').text('');
                                },
                                success: function(data) {
                                    if (data.status == 0) {
                                        $.each(data.error, function(prefix, val) {
                                            $('span.' + prefix + '_error').text(val[
                                                0]);
                                        });
                                        Swal.fire({
                                            icon: 'error',
                                            confirmButtonText: 'Understood',
                                            confirmButtonColor: '#334155',
                                            title: "{{ config('app.name') }}",
                                            text: 'Failed to Post E-LIGTAS Guide.'
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'success',
                                            title: "{{ config('app.name') }}",
                                            text: 'E-LIGTAS Guide Successfully Posted.',
                                            confirmButtonText: 'OK',
                                            confirmButtonColor: '#334155'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $('#createGuideForm')[0].reset();
                                                $('#createGuideModal').modal(
                                                    'hide');
                                                location.reload();
                                            }
                                        });
                                    }
                                },
                                error: function(data) {
                                    Swal.fire({
                                        icon: 'error',
                                        confirmButtonText: 'Understood',
                                        confirmButtonColor: '#334155',
                                        title: "{{ config('app.name') }}",
                                        text: 'Something went wrong, try again later.'
                                    });
                                }
                            });
                        }
                    })
                });
            });
        </script>
    @endif
</body>

</html>
