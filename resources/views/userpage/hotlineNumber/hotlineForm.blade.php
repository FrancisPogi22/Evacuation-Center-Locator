<form id="hotlineForm" hidden>
    @csrf
    <div class="hotline-form-container">
        <div class="hotline-logo-container">
            <div class="hotline-image-container">
                <img src="{{ asset('assets/img/Select-Image.svg') }}" alt="logo" id="hotline-preview-image">
                <input type="file" name="logo" class="form-control" id="hotlineLogo" hidden>
                <span id="image-error" class="error" hidden>Please select an image file.</span>
            </div>
            <button class="btn btn-sm btn-primary" id="imageBtn">
                <i class="bi bi-image"></i>Select Logo
            </button>
        </div>
        <div class="hotline-details-container">
            <div class="hotline-form-content">
                <div>
                    <label for="label">Label</label>
                    <input type="text" name="label" class="form-control" autocomplete="off"
                        placeholder="Enter Label" id="hotlineLabel">
                </div>
                <div>
                    <label for="label" class="last-label">Number</label>
                    <input type="text" name="number" id="hotlineNumber" class="form-control"
                        placeholder="Enter Number" autocomplete="off">
                </div>
                <div class="hotline-form-button-container">
                    <button class="btn-submit modalBtn" id="addNumberBtn">
                        <div id="btn-loader" hidden>
                            <div id="loader-inner"></div>
                        </div>
                        <div id="btnText">Add</div>
                    </button>
                    <button class="btn-remove" id="closeFormBtn">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</form>
