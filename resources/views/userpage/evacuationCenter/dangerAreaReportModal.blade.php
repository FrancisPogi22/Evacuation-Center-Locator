<div class="modal fade" id="reportDangerousAreaModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <header class="modal-label-container">
                <h1 class="modal-label"></h1>
            </header>
            <div class="modal-body">
                <form id="dangerousAreaReportForm">
                    @csrf
                    <div class="form-content">
                        <div class="field-container">
                            <label for="report_type">Report Type</label>
                            <select name="report_type" id="report_type" class="form-select">
                                <option value="" hidden selected disabled>Select Report Type</option>
                                <option value="Flooded Area">Flooded Area</option>
                            </select>
                        </div>
                        <div class="field-container">
                            <label>Location</label>
                            <div class="map-border">
                                <div class="form-map" id="map"></div>
                            </div>
                            <input type="text" name="latitude" id="latitude" hidden>
                            <input type="text" name="longitude" id="longitude" hidden>
                            <span id="location-error" class="error" hidden></span>
                        </div>
                        <div class="form-button-container">
                            <button id="reportDangerousAreaBtn"></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
