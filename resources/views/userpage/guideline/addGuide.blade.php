@if(Auth::check() && Auth::user()->user_role == '1')
    <div class="modal fade" id="createGuideModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-red-900">
                    <h1 class="modal-title fs-5 text-center text-white">{{ config('app.name') }}</h1>
                </div>
                <div class="modal-body">
                    <form id="createGuideForm" name="createGuideForm">
                        @csrf
                        <input type="hidden" name="create_guide_id">
                        <div class="mb-3">
                            <label for="guide_description" class="flex items-center justify-center">Guide
                                Description</label>
                            <input type="text" name="guide_description" class="form-control" autocomplete="off"
                                placeholder="Enter Guide Description">
                            <span class="text-danger error-text guide_description_error"></span>
                        </div>
                        <div class="mb-3">
                            <label for="guide_content" class="flex items-center justify-center">Guide Content</label>
                            <textarea name="guide_content" class="form-control" autocomplete="off" placeholder="Enter Guide Content" rows="5"></textarea>
                            <span class="text-danger error-text guide_content_error"></span>
                        </div>
                        <div class="modal-footer">
                            <button type="button"
                                class="bg-slate-700 text-white p-2 rounded shadow-lg hover:shadow-xl"
                                data-bs-dismiss="modal">Close</button>
                            <button id="submitGuideBtn"
                                class="bg-red-700 text-white p-2 rounded shadow-lg hover:shadow-xl">Post
                                Guide</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
