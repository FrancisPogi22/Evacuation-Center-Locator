<div class="modal fade" id="feedbackModal" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <header class="modal-label-container">
                <h1 class="modal-label">Feedback Form</h1>
                <button type="button" data-bs-dismiss="modal" aria-label="Close" id="closeModalBtn">
                    <i class="bi bi-x-lg"></i>
                </button>
            </header>
            <div class="modal-body">
                <form id="feedbackForm">
                    @csrf
                    <div class="form-content">
                        <input type="text" name="evacuationId" id="evacuationId" hidden>
                        <div class="field-container">
                            <div id="checkbox-input-container">
                                <div class="checkbox-container">
                                    <input type="checkbox" id="clean_facilities" name="clean_facilities" class="checkbox">
                                    <label for="clean_facilities">Clean Facilities</label>
                                </div>
                                <div class="checkbox-container">
                                    <input type="checkbox" id="responsive_aid" name="responsive_aid" class="checkbox">
                                    <label for="responsive_aid">Responsive Aid</label>
                                </div>
                                <div class="checkbox-container">
                                    <input type="checkbox" id="safe_evacuation" name="safe_evacuation" class="checkbox">
                                    <label for="safe_evacuation">Safe Evacuation</label>
                                </div>
                                <div class="checkbox-container">
                                    <input type="checkbox" id="sufficient_food_supply" name="sufficient_food_supply" class="checkbox">
                                    <label for="sufficient_food_supply">Sufficient Food Supply</label>
                                </div>
                                <div class="checkbox-container">
                                    <input type="checkbox" id="comfortable_evacuation" name="comfortable_evacuation" class="checkbox">
                                    <label for="comfortable_evacuation">Comfortable Evacuation</label>
                                </div>
                                <div class="checkbox-container">
                                    <input type="checkbox" id="well_managed_evacuation" name="well_managed_evacuation" class="checkbox">
                                    <label for="well_managed_evacuation">Well-Managed Evacuation</label>
                                </div>
                            </div>
                            <span id="feedback-error" class="error" hidden></span>
                        </div>
                        <div class="form-button-container">
                            <button id="sendFeedbackBtn" class="modalBtn btn-submit">
                                <div id="btn-loader" hidden>
                                    <div id="loader-inner"></div>
                                </div>
                                <span id="btn-text">Send</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
