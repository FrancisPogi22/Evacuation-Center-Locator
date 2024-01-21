@auth
    <div class="modal fade" id="guidelineModal" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <header class="modal-label-container">
                    <h1 class="modal-label"></h1>
                    <button type="button" data-bs-dismiss="modal" aria-label="Close" id="closeModalBtn">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </header>
                <div class="modal-body">
                    <form id="guidelineForm">
                        @csrf
                        <div class="form-content">
                            <div class="field-container">
                                <label for="type">Guideline Type</label>
                                <input type="text" name="type" id="type" class="form-control" autocomplete="off"
                                    placeholder="e.g Typhoon">
                            </div>
                            <div id="guideline-img-container">
                                <div id="cover-img-container">
                                    <label>Cover Image</label>
                                    <input type="file" class="form-control" id="coverImage" name="coverImage"
                                        accept=".jpeg, .jpg, .png" hidden>
                                    <img src="/assets/img/Select-Image.svg" class="type-img" id="coverImagePreview">
                                    <span id="image-error" class="error" hidden>Please select an image file.</span>
                                    <button class="btn btn-sm btn-primary" id="selectCoverImage">
                                        <i class="bi bi-image"></i>Select Cover Image
                                    </button>
                                </div>
                                <div id="content-img-container">
                                    <label>Content Image</label>
                                    <input type="file" class="form-control" id="contentImage" name="contentImage"
                                        accept=".jpeg, .jpg, .png" hidden>
                                    <img src="/assets/img/Select-Image.svg" class="type-img" id="contentImagePreview">
                                    <span id="image-error" class="error" hidden>Please select an image file.</span>
                                    <button class="btn btn-sm btn-primary" id="selectContentImage">
                                        <i class="bi bi-image"></i>Select Content Image
                                    </button>
                                </div>
                            </div>
                            <div class="form-button-container">
                                <button id="submitGuidelineBtn" class="modalBtn">
                                    <div id="btn-loader" hidden>
                                        <div id="loader-inner"></div>
                                    </div>
                                    <span id="btn-text"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endauth
