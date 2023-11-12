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
                    <p id="user-name">{{ auth()->user()->name }}</p>
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
                        <p class="profile-details" id="user-position">{{ auth()->user()->position }}</p>
                    </div>
                    <div class="details-section col-lg-4" id="user-organization">
                        <label class="profile-details-label">Organization</label>
                        @if (auth()->user()->organization == 'CDRRMO')
                            <p class="profile-details" data-organization="CDRRMO">>Cabuyao City Disaster Risk Reduction
                                and Management Office (CDRRMO)</p>
                        @else
                            <p class="profile-details" data-organization="CSWD">City Social Welfare and
                                Development (CSWD)
                            </p>
                        @endif
                    </div>
                    <div class="details-section col-lg-4">
                        <label class="profile-details-label">Email Address</label>
                        <p class="profile-details" id="user-email">{{ auth()->user()->email }}</p>
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
                organization = $('#organization'),
                position = $('#position'),
                name = $('#name'),
                email = $('#email'),
                form = $('#accountForm'),
                modalLabel = $('.modal-label'),
                formButton = $('#saveProfileDetails'),
                accountId = '{{ auth()->user()->id }}';

            validator = form.validate({
                rules: {
                    name: 'required',
                    organization: 'required',
                    position: 'required',
                    email: 'required'
                },
                messages: {
                    name: 'Please Enter Your Name.',
                    organization: 'Please Enter Your Organization.',
                    position: 'Please Enter Your Position.',
                    email: 'Please Enter Your Email Address.'
                },
                errorElement: 'span',
                submitHandler(form) {
                    let formData = $(form).serialize();

                    confirmModal('Do you want to update your details?').then((result) => {
                        if (!result.isConfirmed) return;

                        return operation == 'update' && defaultFormData == formData ?
                            showWarningMessage() :
                            $.ajax({
                                url: "{{ route('account.update', 'accountId') }}".replace(
                                    'accountId', accountId),
                                method: 'PUT',
                                data: formData,
                                success(response) {
                                    if (response.status == 'warning') showWarningMessage(
                                        response.message);

                                    $('#user-name').text(name.val());
                                    $('#user-position').text(position.val());
                                    $('#user-organization').find('p').text(organization
                                        .val() == "CSWD" ?
                                        "City Social Welfare and Development (CSWD)" :
                                        "Cabuyao City Disaster Risk Reduction and Management Office (CDRRMO)"
                                    );
                                    $('#user-email').text(email.val());
                                    showSuccessMessage(
                                        'Successfully updated the account details.');
                                    modal.modal('hide');
                                },
                                error: showErrorMessage
                            });
                    });
                }
            });

            $(document).on('click', '#updateProfileBtn', () => {
                modalLabelContainer.removeClass('bg-success').addClass('bg-warning');
                modalLabel.text('Update Profile Account');
                formButton.removeClass('btn-submit').addClass('btn-update').text('Update');
                $('#suspend-container').hide();
                operation = "update";
                organization.val($('#user-organization').find('p').data('organization'));
                name.val($('#user-name').text());
                email.val($('#user-email').text());
                $('#position-container, #name-container, #email-container').prop('hidden', 0);
                initPositionOption(organization.val());
                modal.modal('show');
                defaultFormData = form.serialize();
            });

            $('#organization').change(function() {
                initPositionOption($(this).val());
            });

            modal.on('hidden.bs.modal', () => {
                validator && validator.resetForm();
                form[0].reset();
            });

            function checkPosition(position) {
                return position == "CSWD" ? '<option value="Focal">Focal</option>' :
                    '<option value="President">President</option><option value="Vice President">Vice President</option>';
            }

            function initPositionOption(organization) {
                position.empty();
                position.append(checkPosition(organization));
            }
        });
    </script>
</body>

</html>
