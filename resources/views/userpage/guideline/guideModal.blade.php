@auth
    <div class="modal fade" id="guideModal" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <header class="modal-label-container">
                    <h1 class="modal-label"></h1>
                    <button type="button" data-bs-dismiss="modal" aria-label="Close" id="closeModalBtn">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </header>
                <div class="modal-body">
                    <form id="guideForm">
                        @csrf
                        <div class="form-content">
                            <div class="field-container guide-input-container">
                                <div class="guide-field">
                                    <div class="guide-img-field">
                                        <img src="{{ asset('assets/img/E-LIGTAS-Logo-White.png') }}" alt="Picture"
                                            class="guideImage" id="image_preview_container">
                                        <input type="file" name="guidePhoto" class="guidePhoto" id="guidePhoto"
                                            class="form-control" hidden>
                                        <a href="javascript:void(0)" class="btn-submit guideImgBtn"><i
                                                class="bi bi-image"></i>Choose Image</a>
                                    </div>
                                    <div class="guide-field-container">
                                        <div class="field-container">
                                            <label for="label">Guide Description</label>
                                            <input type="text" name="label" id="label" class="form-control"
                                                autocomplete="off" placeholder="Enter Guide Description">
                                        </div>
                                        <div class="field-container">
                                            <label for="content">Guide Content</label>
                                            <textarea name="content" id="content" class="form-control" autocomplete="off" placeholder="Enter Guide Content"
                                                rows="7"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-button-container">
                                <button id="submitGuideBtn">
                                    <div id="btn-loader">
                                        <div id="loader-inner"></div>
                                    </div>
                                    <span class="btn-text"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endauth
