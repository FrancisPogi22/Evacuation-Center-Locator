<div class="modal fade" id="markerModal" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <header class="modal-label-container">
                <h1 class="modal-label"></h1>
                <button type="button" data-bs-dismiss="modal" aria-label="Close" id="closeModalBtn">
                    <i class="bi bi-x-lg"></i>
                </button>
            </header>
            <div class="modal-body">
                <form id="markerForm">
                    @csrf
                    <div class="form-content">
                        <div class="field-container">
                            <label>Marker Name</label>
                            <input type="text" class="form-control" name="name" id="name"
                                placeholder="Enter Marker Name" autocomplete="off">
                        </div>
                        <div class="field-container">
                            <label>Marker Description</label>
                            <textarea type="text" class="form-control" name="description" id="description" rows="3"
                                placeholder="Enter Marker Description" autocomplete="off"></textarea>
                        </div>
                        <div class="field-container">
                            <label>Marker Image</label>
                            <input type="file" class="form-control" id="markerImage" name="image"
                                accept=".jpeg, .jpg, .png" hidden>
                            <img src="/assets/img/Select-Image.svg" class="form-control" id="markerImagePreview">
                            <span id="image-error" class="error" hidden>Please select an image file.</span>
                            <button class="btn btn-sm btn-primary" id="imageBtn">
                                <i class="bi bi-image"></i>Select Marker Image
                            </button>
                        </div>
                        <div class="form-button-container">
                            <button id="submitMarkerBtn" class="btn-submit modalBtn">
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
