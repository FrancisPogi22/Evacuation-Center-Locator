@if (auth()->user()->is_disable == 0)
    <div class="modal fade" id="evacuationCenterModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-label-container">
                    <h1 class="modal-label"></h1>
                </div>
                <div class="modal-body">
                    <form id="evacuationCenterForm">
                        @csrf
                        <div class="form-content">
                            <input type="text" id="operation" hidden>
                            <div class="field-container">
                                <label>Evacuation Center    Name</label>
                                <input type="text" name="name" class="form-control" autocomplete="off"
                                    placeholder="Enter Evacuation Center Name" id="name">
                            </div>
                            <div class="field-container">
                                <label>Barangay</label>
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
                                <label>Capacity</label>
                                <input type="number" name="capacity" class="form-control" autocomplete="off"
                                    placeholder="Enter Capacity" id="capacity">
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
                                <button id="createEvacuationCenterBtn" class="btn-submit">Create</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
