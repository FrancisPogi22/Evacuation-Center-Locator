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
                        <i class="bi bi-person-circle"></i>
                    </div>
                </div>
                <span>MY ACCOUNT</span>
            </div>
            <hr>
            <section class="user-profile-container">
                <div class="profile-section">
                    <div class="profile-img">
                        <img src="{{ asset('assets/img/Profile.png') }}" alt="Profile" id="profile">
                    </div>
                    <p>{{ auth()->user()->name }}</p>
                </div>
                <div class="edit-profile-btn">
                    <button class="btn-update" id="updateProfileBtn">
                        <i class="bi bi-pencil-square"></i>Edit Profile
                    </button>
                </div>
                <hr>
                <div class="profile-details-container">
                    <div class="details-section col-lg-2">
                        <label class="profile-details-label">Position</label>
                        <p class="profile-details">{{ auth()->user()->position }}</p>
                    </div>
                    <div class="details-section col-lg-4">
                        <label class="profile-details-label">Organization</label>
                        @if (auth()->user()->organization == 'CDRRMO')
                            <p class="profile-details">Cabuyao City Disaster Risk Reduction
                                and Management Office (CDRRMO)</p>
                        @else
                            <p class="profile-details">City Social Welfare and
                                Development (CSWD)
                            </p>
                        @endif
                    </div>
                    <div class="details-section col-lg-4">
                        <label class="profile-details-label">Email Address</label>
                        <p class="profile-details">{{ auth()->user()->email }}</p>
                    </div>
                    <div class="details-section col-lg-2">
                        <label class="profile-details-label">Account Status</label>
                        <p class="profile-details">{{ auth()->user()->status }}</p>
                    </div>
                </div>
            </section>
        </main>
        @include('userpage.userAccount.userAccountModal')
        @include('userpage.changePasswordModal')
    </div>

    @include('partials.script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    @include('partials.toastr')
    <script>
        $(document).ready(() => {
            let defaultFormData, validator, operation, modal = $('#userAccountModal'),
                modalLabelContainer = $('.modal-label-container'),
                modalLabel = $('.modal-label'),
                formButton = $('#saveProfileDetails'),
                accountId = '{{ auth()->user()->id }}';

            validator = $("#accountForm").validate({
                rules: {
                    organization: 'required',
                    position: 'required',
                    email: 'required'
                },
                messages: {
                    organization: 'Please Enter Your Organization.',
                    position: 'Please Enter Your Position.',
                    email: 'Please Enter Your Email Address.'
                },
                errorElement: 'span',
                submitHandler: formSubmitHandler
            });

            $(document).on('click', '#updateProfileBtn', () => {
                modalLabelContainer.removeClass('bg-success').addClass('bg-warning');
                modalLabel.text('Update Profile Account');
                formButton.removeClass('btn-submit').addClass('btn-update').text('Update');
                $('#suspend-container').hide();
                operation = "update";
                $('#organization').val('{{ auth()->user()->organization }}');
                $('#position').val('{{ auth()->user()->position }}');
                $('#email').val('{{ auth()->user()->email }}');
                modal.modal('show');
                defaultFormData = $('#accountForm').serialize();
            });

            modal.on('hidden.bs.modal', () => {
                validator.resetForm();
                $('#accountForm')[0].reset();
            });

            function formSubmitHandler(form) {
                let formData = $(form).serialize();

                confirmModal('Do you want to update your details?').then((result) => {
                    if (!result.isConfirmed) return;

                    return operation == 'update' && defaultFormData == formData ?
                        showWarningMessage() :
                        $.ajax({
                            url: "{{ route('account.update', 'accountId') }}".replace('accountId',
                                accountId),
                            method: 'PUT',
                            data: formData,
                            success(response) {
                                response.status == 'warning' ? showWarningMessage(response
                                    .message) : showSuccessMessage(
                                    'Successfully updated the account details, Please wait...', true
                                );
                            },
                            error: showErrorMessage
                        });
                });
            }
        });
    </script>
</body>

</html>
