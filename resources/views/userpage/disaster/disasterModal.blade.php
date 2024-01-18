<div class="modal fade" id="disasterModal" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <header class="modal-label-container">
                <h1 class="modal-label"></h1>
                <button type="button" data-bs-dismiss="modal" aria-label="Close" id="closeModalBtn">
                    <i class="bi bi-x-lg"></i>
                </button>
            </header>
            <div class="modal-body">
                <form id="disasterForm">
                    @csrf
                    <div class="form-content">
                        <div class="field-container">
                            <label for="name">Disaster Name</label>
                            <input type="text" name="name" class="form-control" autocomplete="off"
                                placeholder="e.g. Ondoy" id="disasterName">
                        </div>
                        <div class="field-container">
                            <label for="type">Type</label>
                            <select name="type" class="form-select" id="type">
                                <option value="" hidden selected disabled>Select Type</option>
                                <option value="Typhoon">Typhoon</option>
                                <option value="Flood">Flood</option>
                                <option value="Wildfire">Wildfire</option>
                                <option value="Volcanic Eruption">Volcanic Eruption</option>
                                <option value="Structure Fire">Structure Fire</option>
                                <option value="Tsunami">Tsunami</option>
                                <option value="Landslide">Landslide</option>
                                <option value="Sinkhole">Sinkhole</option>
                                <option value="Pandemic">Pandemic</option>
                                <option value="Epidemic">Epidemic</option>
                            </select>
                        </div>
                        <div class="form-button-container">
                            <button id="submitDisasterBtn" class="modalBtn">
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
