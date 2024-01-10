<div class="modal fade" id="feedbackModal" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
                            <label for="feedback">Feedback</label>
                            <textarea type="text" name="feedback" class="form-control" autocomplete="off"
                                placeholder="Share your feedback..." id="feedback"></textarea>
                        </div>
                        <div class="form-button-container">
                            <button class="modalBtn btn-submit">
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
