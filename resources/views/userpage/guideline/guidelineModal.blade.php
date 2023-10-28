@auth
    @if (auth()->user()->is_disable == 0)
        <div class="modal fade" id="guidelineModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <header class="modal-label-container">
                        <h1 class="modal-label"></h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </header>
                    <div class="modal-body">
                        <form id="guidelineForm">
                            @csrf
                            <div class="form-content">
                                <div class="field-container guideline-field">
                                    <div class="guideline-img">
                                        <img src="{{ asset('assets/img/e-ligtas-logo-black.png') }}" class="guidelineImage"
                                            alt="Picture">
                                        <input type="file" name="guidelineImg" class="guidelineImgInput" id="guidelineImgInput" hidden>
                                        <a href="javascript:void(0)" class="btn-submit guidelineImgBtn"><i class="bi bi-image"></i>Choose Image</a>
                                    </div>
                                    <div class="guideline-input">
                                        <label for="type">Guideline Type</label>
                                        <input type="text" name="type" class="form-control" autocomplete="off"
                                            placeholder="Enter Guideline Type" id="guidelineType">
                                    </div>
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
    @endif
@endauth
