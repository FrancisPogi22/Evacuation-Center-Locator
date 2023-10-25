<div class="modal fade" id="addHotlineNumberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-label-container">
                <h1 class="modal-label"></h1>
            </div>
            <div class="modal-body">
                <form id="hotlineForm">
                    @csrf
                    <div class="form-content">
                        <div class="input-field-section">
                            <div class="logo-container">
                                <img src="{{ asset('assets/img/e-ligtas-logo-black.png') }}" alt="logo"
                                    id="hotlinePreviewLogo">
                                <input type="file" name="logo" class="form-control" id="hotlineLogo" hidden>
                                <a href="javascript:void(0)" class="btn-table-primary" id="selectLogo"><i
                                        class="bi bi-image"></i>Select
                                    Logo</a>
                            </div>
                            <div class="input-container">
                                <div class="field-container">
                                    <label for="label">Hotline Number Label</label>
                                    <input type="text" name="label" class="form-control" autocomplete="off"
                                        placeholder="e.g Police" id="hotlineLabel">
                                </div>
                                <div class="field-container">
                                    <label for="number">Hotline Number</label>
                                    <input type="number" name="number" class="form-control" autocomplete="off"
                                        placeholder="e.g 12345678910" id="hotlineNumber">
                                </div>
                            </div>
                        </div>
                        <div class="form-button-container">
                            <button class="btn-submit" id="addNumberBtn"></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
