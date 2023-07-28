<div class="modal fade" id="guidelineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-center bg-green-600">
                <h1 class="modal-title fs-5 text-white font-extrabold">Create Guideline Form</h1>
            </div>
            <div class="modal-body">
                <form id="guidelineForm">
                    @csrf
                    <div class="bg-slate-50 pt-3 pb-2 rounded">
                        <div class="flex-auto">
                            <div class="flex flex-wrap">
                                <input type="text" id="guideline_operation" hidden>
                                <div class="field-container">
                                    <label>Guideline Type</label>
                                    <input type="text" name="type" class="form-control" autocomplete="off"
                                        placeholder="Enter Guideline Type" id="guidelineType">
                                </div>
                                <div class="w-full px-4 pt-2 pb-3">
                                    <button id="submitGuidelineBtn" class="btn-submit p-2 float-right">Create</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
