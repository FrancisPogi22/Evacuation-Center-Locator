<div class="modal fade" id="archivedReportModal" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <header class="modal-label-container">
                <h1 class="modal-label">Add Emergency Info</h1>
                <button type="button" data-bs-dismiss="modal" aria-label="Close" id="closeModalBtn">
                    <i class="bi bi-x-lg"></i>
                </button>
            </header>
            <div class="modal-body">
                <form id="archivedReportForm">
                    <div class="form-content">
                        <div class="field-container">
                            <label>Report Details</label>
                            <textarea type="text" class="form-control" name="details" rows="5" placeholder="Enter Details"
                                autocomplete="off"></textarea>
                        </div>
                        <div class="field-container">
                            <label>Image</label>
                            <input type="file" class="form-control" id="inputImage" name="image"
                                accept=".jpeg, .jpg, .png" hidden>
                            <button class="btn btn-sm btn-primary" id="imageBtn">
                                <i class="bi bi-image"></i>Select
                            </button>
                            <img id="selectedReportImage" src="" class="form-control" hidden>
                            <span id="image-error" class="error" hidden>Please select an image file.</span>
                        </div>
                        <div class="form-button-container">
                            <button id="archiveReportBtn" class="btn-submit">Archive</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
