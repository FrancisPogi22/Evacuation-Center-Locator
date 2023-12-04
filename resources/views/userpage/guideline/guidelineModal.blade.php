@auth
    <div class="modal fade" id="guidelineModal" data-bs-backdrop="static" aria-hidden="true">
        <form id="guidelineForm">
            @csrf
            <div class="modal-dialog modal-xl">
                <div class="modal-guideline-image">
                    <header class="modal-label-container">
                        <h1>Guideline Image</h1>
                    </header>
                    <div class="modal-body">
                        <div class="field-container guideline-field">
                            <div class="guideline-img">
                                <img src="{{ asset('assets/img/E-LIGTAS-Logo-Black.png') }}" class="guidelineImage"
                                    alt="Picture">
                                <input type="file" name="guidelineImg" class="guidelineImgInput" id="guidelineImgInput"
                                    hidden>
                                <a href="javascript:void(0)" class="btn-submit guidelineImgBtn"><i
                                        class="bi bi-image"></i>Choose Image</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-content">
                    <header class="modal-label-container">
                        <h1 class="modal-label"></h1>
                        <button type="button" data-bs-dismiss="modal" aria-label="Close" id="closeModalBtn">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </header>
                    <div class="modal-body">
                        <div class="form-content">
                            <div class="field-container guideline-field">
                                <div class="guideline-input">
                                    <label for="type">Guideline Type</label>
                                    <input type="text" name="type" class="form-control" autocomplete="off"
                                        placeholder="Enter Guideline Type" id="guidelineType">
                                </div>
                            </div>
                            <div class="appendInput">
                                <a class="btn-update" id="addGuideInput"><i class="bi bi-plus-lg"></i></a>
                            </div>
                            <div class="form-button-container">
                                <button id="submitGuidelineBtn" class="modalBtn">
                                    <div id="btn-loader">
                                        <div id="loader-inner"></div>
                                    </div>
                                    <span class="btn-text"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="guide-forms" id="guideContentFields">
            </div>
        </form>
    </div>
@endauth
