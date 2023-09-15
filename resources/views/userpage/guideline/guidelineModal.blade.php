@auth
    <div class="modal fade" id="guidelineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <header class="modal-label-container">
                    <h1 class="modal-label"></h1>
                </header>
                <div class="modal-body">
                    <form id="guidelineForm">
                        @csrf
                        <div class="form-content">
                            <div class="field-container">
                                <label for="type">Guideline Type</label>
                                <input type="text" name="type" class="form-control" autocomplete="off"
                                    placeholder="Enter Guideline Type" id="guidelineType">
                            </div>
                            <div class="field-container guide-section" id="guideContentFields">
                            </div>
                            <div class="appendInput">
                                <a class="btn-update" id="addGuideInput"><i class="bi bi-plus-lg"></i></a>
                            </div>
                            <div class="form-button-container">
                                <button id="submitGuidelineBtn"></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endauth
