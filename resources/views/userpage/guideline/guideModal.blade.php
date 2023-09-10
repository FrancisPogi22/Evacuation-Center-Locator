@auth
    <div class="modal fade" id="guideModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <header class="modal-label-container">
                    <h1 class="modal-label"></h1>
                </header>
                <div class="modal-body">
                    <form id="guideForm">
                        @csrf
                        <div class="form-content">
                            <div class="field-container guide-input-container">
                                <div class="guide-field">
                                    <div class="image-container">
                                        <img src="{{ asset('assets/img/e-ligtas-logo.png') }}" alt="Pictue"
                                            class="myProfile" id="image_preview_container">
                                        <span>
                                            <input type="file" name="guidePhoto" id="guidePhoto"
                                                class="form-control">
                                        </span>
                                    </div>
                                    <div class="guide-field-container">
                                        <div class="field-container">
                                            <label for="label">Guide Description</label>
                                            <input type="text" name="label" id="label" class="form-control" autocomplete="off"
                                                placeholder="Enter Guide Description">
                                        </div>
                                        <div class="field-container">
                                            <label for="content">Guide Content</label>
                                            <textarea name="content" id="content" class="form-control" autocomplete="off" placeholder="Enter Guide Content" rows="7"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-button-container">
                                <button id="submitGuideBtn"></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endauth
