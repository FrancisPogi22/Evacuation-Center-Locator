<div class="modal fade" id="userAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-center bg-green-600">
                <h1 class="modal-title fs-5 text-center text-white font-extrabold"></h1>
            </div>
            <div class="modal-body">
                <form id="accountForm">
                    @csrf
                    <div class="bg-slate-50 pt-3 pb-2 rounded">
                        <div class="flex-auto">
                            <div class="flex flex-wrap">
                                <input type="text" id="accountId" hidden>
                                <input type="text" id="operation" hidden>
                                @if (auth()->user()->position == 'President' || auth()->user()->position == 'Focal')
                                    <div class="field-container" id="organization-container">
                                        <label class="mb-0">Organization</label>
                                        <select type="text" name="organization" class="form-select" id="organization"
                                            placeholder="Enter Organization">
                                            <option value="">Select Organization</option>
                                            <option value="CDRRMO">CDRRMO</option>
                                            <option value="CSWD">CSWD</option>
                                        </select>
                                    </div>
                                    <div class="field-container" id="position-container">
                                        <label class="mb-0">Position</label>
                                        <select type="text" name="position" class="form-select" id="position"
                                            placeholder="Enter Position">
                                            <option value="">Select Position</option>
                                            <option value="President">President</option>
                                            <option value="Focal">Focal</option>
                                        </select>
                                    </div>
                                    <div class="field-container" id="suspend-container">
                                        <label class="mb-0">Suspend Time</label>
                                        <input type="text" name="suspend_time" class="form-control" id="suspend"
                                            placeholder="Select Suspend Time">
                                    </div>
                                @else
                                    <input type="hidden" name="organization" class="form-control" id="organization">
                                    <input type="hidden" name="position" class="form-control" id="position">
                                @endif
                                <div class="field-container" id="email-container">
                                    <label class="mb-0">Email Address</label>
                                    <input type="text" name="email" class="form-control" id="email"
                                        placeholder="Enter Email Address">
                                </div>
<<<<<<< Updated upstream
                                <div class="w-full p-4">
=======
                                <div class="w-full px-4 pt-3 pb-4">
>>>>>>> Stashed changes
                                    <button id="saveProfileDetails" class="btn-submit p-2 float-right">Create</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
