<div class="modal fade" id="disasterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-center bg-green-600">
                <h1 class="modal-title fs-5 text-white font-extrabold">Create Disaster Form</h1>
            </div>
            <div class="modal-body">
                <form id="disasterForm">
                    @csrf
                    <div class="bg-slate-50 pt-3 rounded">
                        <div class="flex-auto">
                            <div class="flex flex-wrap">
                                <input type="text" id="operation" hidden>
                                <div class="field-container">
                                    <label class="mb-0">Name</label>
                                    <input type="text" name="name" class="form-control" autocomplete="off"
                                        placeholder="Enter Name" id="disasterName">
                                </div>
<<<<<<< Updated upstream
                                <div class="field-container">
                                    <label class="mb-0">Location</label>
                                    <input type="text" name="location" class="form-control" autocomplete="off"
                                        placeholder="Enter Location" id="location">
                                </div>
                                <div class="w-full p-4 -mt-3">
=======
                                <div class="w-full px-4 pt-3 pb-4">
>>>>>>> Stashed changes
                                    <button id="submitDisasterBtn"
                                        class="btn-submit p-2 float-right">Create</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
