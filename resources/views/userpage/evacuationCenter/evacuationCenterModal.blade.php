<div class="modal fade" id="evacuationCenterModal" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <header class="modal-label-container">
                <h1 class="modal-label"></h1>
                <button type="button" data-bs-dismiss="modal" aria-label="Close" id="closeModalBtn">
                    <i class="bi bi-x-lg"></i>
                </button>
            </header>
            <div class="modal-body">
                <form id="evacuationCenterForm">
                    @csrf
                    <div class="form-content facilities" hidden>
                        <div id="checkbox-input-container">
                            <div class="add-facilities-btn">
                                <button class="btn-submit viewFacilities">View Facilities</button>
                                @include('userpage.evacuationCenter.feedbackForm')
                            </div>
                            <div class="checkbox-container">
                                <input type="checkbox" id="comfort_room" name="comfort_room" class="checkbox">
                                <label for="comfort_room">Comfort Room</label>
                            </div>
                        </div>
                        <span id="facilities-error" class="error" hidden></span>
                    </div>
                    <div class="form-content manage" hidden>
                        <div class="field-container">
                            <label for="name">Evacuation Center Name</label>
                            <input type="text" name="name" class="form-control" autocomplete="off"
                                placeholder="Enter Evacuation Center Name" id="name">
                        </div>
                        <div class="field-container">
                            <label for="barangayName">Barangay</label>
                            <select name="barangayName" class="form-select" id="barangayName">
                                <option value="" hidden selected disabled>Select Barangay</option>
                                <option value="Baclaran">Baclaran</option>
                                <option value="Banay-Banay">Banay-Banay</option>
                                <option value="Banlic">Banlic</option>
                                <option value="Bigaa">Bigaa</option>
                                <option value="Butong">Butong</option>
                                <option value="Casile">Casile</option>
                                <option value="Diezmo">Diezmo</option>
                                <option value="Gulod">Gulod</option>
                                <option value="Mamatid">Mamatid</option>
                                <option value="Marinig">Marinig</option>
                                <option value="Niugan">Niugan</option>
                                <option value="Pittland">Pittland</option>
                                <option value="Pulo">Pulo</option>
                                <option value="Sala">Sala</option>
                                <option value="San Isidro">San Isidro</option>
                                <option value="Barangay I Poblacion">Barangay I Poblacion</option>
                                <option value="Barangay II Poblacion">Barangay II Poblacion</option>
                                <option value="Barangay III Poblacion">Barangay III Poblacion</option>
                            </select>
                        </div>
                        <div class="field-container">
                            <label>Search Place</label>
                            <input type="text" id="searchPlace" class="form-control" placeholder="Enter Place">
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
                        <div class="field-container">
                            <label>Facilities</label>
                            <div class="common-facility-table-container">
                                <table class="table" id="commonFacilityTable" width="100%">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Common Facilities</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="checkbox-container">
                                                    <input type="checkbox" id="Childcare" value="Childcare"
                                                        class="checkbox">
                                                    <label for="Childcare">Childcare</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="checkbox-container">
                                                    <input type="checkbox" id="Medical Assistance"
                                                        value="Medical Assistance" class="checkbox">
                                                    <label for="Medical Assistance">Medical Assistance</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="checkbox-container">
                                                    <input type="checkbox" id="Family Reunification"
                                                        value="Family Reunification" class="checkbox">
                                                    <label for="Family Reunification">Family Reunification</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="checkbox-container">
                                                    <input type="checkbox" id="Elderly Assistance"
                                                        value="Elderly Assistance" class="checkbox">
                                                    <label for="Elderly Assistance">Elderly Assistance</label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="checkbox-container">
                                                    <input type="checkbox" id="Food Preparation"
                                                        value="Food Preparation" class="checkbox">
                                                    <label for="Food Preparation">Food Preparation</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="checkbox-container">
                                                    <input type="checkbox" id="Charging Station"
                                                        value="Charging Station" class="checkbox">
                                                    <label for="Charging Station">Charging Station</label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="facility-input-container">
                                <input type="text" id="newFacility" class="form-control"
                                    placeholder="Enter Additional Facilitiy">
                                <div id="facility-button-container">
                                    <button id="addFacilityBtn">
                                        Add Facility
                                    </button>
                                    <button id="cancelFacilityUpdateBtn" class="btn-remove" hidden>
                                        Cancel
                                    </button>
                                </div>
                            </div>
                            <span id="new-facility-error" class="error" hidden>Please enter a facility.</span>
                            <div class="facility-item-container" hidden>
                                <div class="facility-item-label">
                                    List of Facilities Added
                                </div>
                            </div>
                        </div>
                        <div class="form-button-container">
                            <button id="createEvacuationCenterBtn" class="modalBtn">
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
