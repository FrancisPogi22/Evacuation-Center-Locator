<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
</head>

<body>
    <div class="wrapper">
        @include('sweetalert::alert')
        @include('partials.header')
        @include('partials.sidebar')
        <div class="main-content">
            <div class="grid grid-cols-1">
                <div class="grid col-end-1">
                    <div class="text-2xl text-white">
                        <i class="bi bi-person-circle p-2 bg-slate-600"></i>
                    </div>
                </div>
                <span class="text-xl font-bold">MY PROFILE</span>
            </div>
            <hr class="mt-4">
            <div class="user-profile-container rounded shadow-lg mt-3">
                <div class="profile-section flex justify-center items-center py-3">
                    <div class="bg-slate-300 w-60 h-60 rounded-full overflow-hidden border-4 border-blue-500">
                        <img src="{{ asset('assets/img/profile.png') }}" alt="Profile" id="profile">
                    </div>
                </div>
                @if (auth()->user()->status == 'Active')
                    <div class="flex justify-end pb-2 pr-4">
                        <button class="btn-edit w-36" id="editProfileBtn">
                            <i class="bi bi-pencil pr-2"></i>
                            Edit Profile
                        </button>
                    </div>
                @endif
                <hr class="mx-6 mb-3">
                <form id='userProfileForm' class="relative flex-auto">
                    <div class="flex flex-wrap text-center pb-2">
                        <div class="details-section lg:w-2/12">
                            <label class="bg-red-700 rounded-t profile-details-label">Position</label>
                            <p class="profile-details rounded-b">{{ auth()->user()->position }}</p>
                        </div>
                        <div class="details-section lg:w-4/12">
                            <label class="bg-red-700 rounded-t profile-details-label">Organization</label>
                            @if (auth()->user()->organization == 'CDRRMO')
                                <p class="profile-details rounded-b">Cabuyao Disaster Risk Reduction
                                    and Management Office ({{ auth()->user()->organization }})</p>
                            @else
                                <p class="profile-details rounded-b">City Social Welfare and
                                    Development ({{ auth()->user()->organization }})
                                </p>
                            @endif
                        </div>
                        <div class="details-section lg:w-4/12">
                            <label class="bg-red-700 rounded-t profile-details-label">Email
                                Address</label>
                            <p class="profile-details rounded-b">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="details-section lg:w-2/12">
                            <label class="bg-red-700 rounded-t profile-details-label">Account Status</label>
                            <p class="profile-details rounded-b">{{ auth()->user()->status }}</p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @if (auth()->user()->status == 'Active')
            @include('userpage.userAccount.userAccountModal')
        @endif
        @include('userpage.changePasswordModal')
    </div>

    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    @include('partials.toastr')
    @if (auth()->user()->status == 'Active')
        <script>
            $(document).ready(function() {
                let defaultFormData;

                $('#editProfileBtn').click(function() {
                    $('.modal-header').removeClass('bg-green-600').addClass('bg-yellow-500');
                    $('.modal-title').text('Edit Profile Account');
                    $('#saveProfileDetails').removeClass('btn-submit').addClass('btn-edit').text('Update');
                    $('#suspend-container').hide();
                    $('#operation').val('update');
                    $('#accountId').val('{{ auth()->user()->id }}');
                    $('#organization').val('{{ auth()->user()->organization }}');
                    $('#position').val('{{ auth()->user()->position }}');
                    $('#email').val('{{ auth()->user()->email }}');
                    $('#userAccountModal').modal('show');
                    defaultFormData = $('#accountForm').serialize();
                });

                let validator = $("#accountForm").validate({
                    rules: {
                        organization: {
                            required: true
                        },
                        position: {
                            required: true
                        },
                        email: {
                            required: true
                        }
                    },
                    messages: {
                        organization: {
                            required: 'Please Enter Your Organization.'
                        },
                        position: {
                            required: 'Please Enter Your Position.'
                        },
                        email: {
                            required: 'Please Enter Your Email Address.'
                        }
                    },
                    errorElement: 'span',
                    submitHandler: formSubmitHandler
                });

                function formSubmitHandler(form) {
                    let accountid = $('#accountId').val(),
                        operation = $('#operation').val(),
                        formData = $(form).serialize();

                    confirmModal('Do you want to update this user details?').then((result) => {
                        if (result.isConfirmed) {
                            if (operation == 'update' && defaultFormData == formData) {
                                messageModal('Info', 'No changes were made.', 'info', '#B91C1C');
                                $('#userAccountModal').modal('hide');
                                return;
                            }
                            $.ajax({
                                url: "{{ route('account.update', ':accountid') }}"
                                    .replace(':accountid', accountid),
                                method: 'PUT',
                                data: formData,
                                dataType: 'json',
                                beforeSend: function() {
                                    $(document).find('span.error-text').text('');
                                },
                                success: function(response) {
                                    if (response.status == 0) {
                                        $.each(response.error, function(prefix, val) {
                                            $('span.' + prefix + '_error').text(val[0]);
                                        });
                                        messageModal('Warning', `Failed to update user details.`,
                                            'warning', '#FFDF00');
                                    } else {
                                        messageModal('Success',
                                            `Successfully updated the user details.`, 'success',
                                            '#3CB043'
                                        ).then(function() {
                                            $('#userAccountModal').modal('hide');
                                            location.reload();
                                        });
                                    }
                                },
                                error: function() {
                                    $('#userAccountModal').modal('hide');
                                    messageModal('Warning',
                                        'Something went wrong, Try again later.', 'warning',
                                        '#FFDF00');
                                }
                            });
                        }
                    });
                }

                $('#userAccountModal').on('hidden.bs.modal', function() {
                    validator.resetForm();
                    $(document).find('span.error-text').text('');
                    $('#accountForm').trigger("reset");
                });
            });
        </script>
    @endif
</body>

</html>
