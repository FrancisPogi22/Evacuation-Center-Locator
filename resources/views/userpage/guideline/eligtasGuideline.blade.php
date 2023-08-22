<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <div class="main-content">
            <div class="label-container">
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-book"></i>
                    </div>
                </div>
                <span>E-LIGTAS GUIDELINES</span>
            </div>
            <hr>
            <div class="content-item">
                <div class="guideline-container">
                    @foreach ($guideline as $guidelineItem)
                        <div class="guideline-widget">
                            @auth
                                @if (auth()->user()->is_disable == 0)
                                    <button id="updateGuidelineBtn">
                                        <i class="btn-update bi bi-pencil-square"></i>
                                    </button>
                                    <button id="removeGuidelineBtn">
                                        <i class="btn-remove bi bi-x-lg"></i>
                                    </button>
                                @endif
                                <a class="guidelines-item"
                                    href="{{ route('guide.display', Crypt::encryptString($guidelineItem->id)) }}">
                                    <div class="guideline-content">
                                        <img src="{{ asset('assets/img/cdrrmo-logo.png') }}" alt="logo">
                                        <div class="guideline-type">
                                            <p>{{ $guidelineItem->type }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endauth
                            @guest
                                <a class="guidelines-item"
                                    href="{{ route('resident.guide', Crypt::encryptString($guidelineItem->id)) }}">
                                    <div class="guideline-content">
                                        <img src="{{ asset('assets/img/cdrrmo-logo.png') }}" alt="logo">
                                        <div class="guideline-type">
                                            <p>{{ $guidelineItem->type }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endguest
                        </div>
                    @endforeach
                    @if (auth()->check() && auth()->user()->is_disable == 0)
                        <div class="guideline-btn">
                            <div class="btn-container">
                                <button id="createGuidelineBtn">
                                    <i class="btn-submit bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                        @include('userpage.guideline.guidelineModal')
                    @endif
                </div>
            </div>
        </div>
        @include('userpage.changePasswordModal')
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
        @if (auth()->user()->is_disable == 0)
            <script>
                $(document).ready(() => {
                    let guidelineId, defaultFormData, operation, modal = $('#guidelineModal');

                    const validator = $("#guidelineForm").validate({
                        rules: {
                            type: 'required'
                        },
                        messages: {
                            type: 'Please Enter Guideline Type.'
                        },
                        errorElement: 'span',
                        submitHandler: formSubmitHandler
                    });

                    $(document).on('click', '#createGuidelineBtn', () => {
                        operation = "create";
                        $('.modal-label-container').removeClass('bg-warning');
                        $('.modal-label').text('Create Guideline');
                        $('#submitGuidelineBtn').removeClass('btn-update').text('Create');
                        modal.modal('show');
                    });

                    $(document).on('click', '#updateGuidelineBtn', function() {
                        $('.modal-label-container').addClass('bg-warning');
                        $('.modal-label').text('Update Guideline');
                        $('#submitGuidelineBtn').addClass('btn-update').text('Update');
                        let guidelineWidget = this.closest('.guideline-widget');
                        let guidelineItem = guidelineWidget.querySelector('.guidelines-item');
                        guidelineId = guidelineItem.getAttribute('href').split('/').pop();
                        let guidelineLabel = guidelineItem.querySelector('.guideline-type p').innerText
                            .toLowerCase();
                        $('#guidelineType').val(guidelineLabel);
                        operation = "update";
                        modal.modal('show');
                        defaultFormData = $('#guidelineForm').serialize();
                    });

                    $(document).on('click', '#removeGuidelineBtn', function() {
                        guidelineWidget = this.closest('.guideline-widget');
                        guidelineItem = guidelineWidget.querySelector('.guidelines-item');
                        guidelineId = guidelineItem.getAttribute('href').split('/').pop();

                        confirmModal('Do you want to remove this guideline?').then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        guidelineId: guidelineId
                                    },
                                    url: "{{ route('guideline.remove', 'guidelineId') }}"
                                        .replace('guidelineId', guidelineId),
                                    type: "PATCH",
                                    success() {
                                        showSuccessMessage('Guideline removed successfully.', true);
                                    },
                                    error() {
                                        showErrorMessage();
                                    }
                                });
                            }
                        });
                    });

                    function formSubmitHandler(form) {
                        let formData = $(form).serialize();
                        let url = operation == 'create' ? "{{ route('guideline.create') }}" :
                            "{{ route('guideline.update', 'guidelineId') }}".replace('guidelineId',
                                guidelineId);
                        let type = operation == 'create' ? "POST" : "PUT";

                        confirmModal(`Do you want to ${operation} this guideline?`).then((result) => {
                            if (result.isConfirmed) {
                                return operation == 'update' && defaultFormData == formData ? showWarningMessage(
                                        'No changes were made.') :
                                    $.ajax({
                                        data: formData,
                                        url,
                                        type,
                                        success(response) {
                                            response.status == 'warning' ? owWarningMessage(response
                                                .message) : (modal.modal('hide'), showSuccessMessage(
                                                `Guideline successfully ${operation}d, Please wait...`,
                                                true));
                                        },
                                        error() {
                                            showErrorMessage();
                                        }
                                    });
                            }
                        });
                    }

                    modal.on('hidden.bs.modal', () => {
                        validator.resetForm();
                        $('#guidelineForm')[0].reset();
                    });
                });
            </script>
        @endif
    @endauth
</body>

</html>
