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
                        <div id="disasterFormContainer">
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
                        </div>
                        <div id="archiveDamageContainer" hidden>
                            <div class="field-container">
                                <label>Add Damages</label>
                                <select name="barangay" class="form-select" id="barangay">
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
                                <input type="text" name="description" class="form-control" autocomplete="off"
                                    placeholder="Fallen Pole" id="description">
                            </div>
                            <div class="field-container">
                                <input type="number" name="quantity" class="form-control" autocomplete="off"
                                    placeholder="e.g 10" id="quantity">
                            </div>
                            <div class="field-container">
                                <input type="number" name="cost" class="form-control" autocomplete="off"
                                    placeholder="e.g 3500" id="cost">
                                <button id="addDamageBtn" class="modalBtn mt-2">
                                    <span>Add Damage</span>
                                </button>
                            </div>
                            <div class="field-container">
                                <label>Damages List</label>
                                <hr class="m-0">
                                <div id="damage_list"></div>
                            </div>
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
