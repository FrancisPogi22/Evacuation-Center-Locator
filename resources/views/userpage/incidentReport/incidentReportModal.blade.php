<div class="modal fade" id="createAccidentReportModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <header class="modal-label-container">
                <h1 class="modal-label"></h1>
            </header>
            <div class="modal-body">
                <form id="reportForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-content">
                        <div class="field-container">
                            <label for="description">Report Description</label>
                            <textarea type="text" id="description" name="description" class="form-control" rows="5"
                                placeholder="Enter Incident Description" autocomplete="off"></textarea>
                        </div>
                        <div class="field-container">
                            <label for="location">Report Location</label>
                            <input type="text" id="location" name="location" class="form-control"
                                placeholder="Enter Incident Location" autocomplete="off">
                        </div>
                        <div class="field-container">
                            <label for="photo">Report Photo</label>
                            <input type="file" id="photo" name="photo" class="form-control form-control-lg" accept=".jpeg">
                            <span>*This field is optional.</span>
                        </div>
                        <div class="form-button-container">
                            <button id="reportIncidentBtn"></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
