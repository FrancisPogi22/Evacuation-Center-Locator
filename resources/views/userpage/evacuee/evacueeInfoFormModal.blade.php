@if (auth()->user()->is_disable == 0)
    <div class="modal fade" id="evacueeInfoFormModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <header class="modal-label-container">
                    <h1 class="modal-label"></h1>
                </header>
                <div class="modal-body">
                    <form id="evacueeInfoForm">
                        @csrf
                        <div class="form-content">
                            <div class="field-container toggle-form-button">
                                <button type="button" id="newRecordBtn" class="btn-submit">
                                    <i class="bi bi-file-earmark-plus"></i>
                                    Add new record
                                </button>
                                <button type="button" id="existingRecordBtn" class="btn-submit">
                                    <i class="bi bi-search"></i>
                                    Find existing record
                                </button>
                            </div>
                            <div class="field-container hidden_field" hidden>
                                <input type="text" name="form_type" id="formType" class="form-control">
                                <input type="text" name="family_id" id="family_id" class="form-control">
                            </div>
                            <div class="field-container searchContainer" hidden>
                                <div class="custom-dropdown">
                                    <label for="searchInput">Search Family Record</label>
                                    <input type="text" id="searchInput" class="form-control"
                                        placeholder="Search Family Head">
                                    <div class="dropdown-options" hidden id="dropdownOptions">
                                        <ul id="searchResults"></ul>
                                    </div>
                                </div>
                            </div>
                            <div class="field-container" hidden>
                                <label for="barangay">Barangay</label>
                                <select name="barangay" class="form-select">
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
                            <div class="col-lg-6 field-container" hidden>
                                <label for="family_head">Family Head</label>
                                <input type="text" name="family_head" id="family_head" class="form-control"
                                    autocomplete="off" placeholder="Family Head">
                            </div>
                            <div class="col-lg-6 field-container" hidden id="birthDateContainer">
                                <label for="birth_date">Birth Date</label>
                                <input type="text" name="birth_date" id="birth_date" class="form-control"
                                    autocomplete="off" placeholder="Select Birth Date">
                            </div>
                            <div class="col-lg-6 field-container" hidden>
                                <label for="male">Male</label>
                                <input type="number" name="male" id="male" class="form-control"
                                    autocomplete="off" placeholder="Male">
                            </div>
                            <div class="col-lg-6 field-container" hidden>
                                <label for="female">Female</label>
                                <input type="number" name="female" id="female" class="form-control"
                                    autocomplete="off" placeholder="Female">
                            </div>
                            <div class="col-lg-4 field-container" hidden>
                                <label for="infants">Infants</label>
                                <input type="number" name="infants" id="infants" class="form-control"
                                    autocomplete="off" placeholder="Infants">
                            </div>
                            <div class="col-lg-4 field-container" hidden>
                                <label for="minors">Minors</label>
                                <input type="number" name="minors" id="minors" class="form-control"
                                    autocomplete="off" placeholder="Minors">
                            </div>
                            <div class="col-lg-4 field-container" hidden>
                                <label for="senior_citizen">Senior Citizen</label>
                                <input type="number" name="senior_citizen" id="senior_citizen" class="form-control"
                                    autocomplete="off" placeholder="Senior Citizen">
                            </div>
                            <div class="col-lg-4 field-container" hidden>
                                <label for="pwd">PWD</label>
                                <input type="number" name="pwd" id="pwd" class="form-control"
                                    autocomplete="off" placeholder="PWD">
                            </div>
                            <div class="col-lg-4 field-container" hidden>
                                <label for="pregnant">Pregnant</label>
                                <input type="number" name="pregnant" id="pregnant" class="form-control"
                                    autocomplete="off" placeholder="Pregnant">
                            </div>
                            <div class="col-lg-4 field-container" hidden>
                                <label for="lactating">Lactating</label>
                                <input type="number" name="lactating" id="lactating" class="form-control"
                                    autocomplete="off" placeholder="Lactating">
                            </div>
                            <div class="field-container" hidden>
                                <label for="disaster_id">Disaster</label>
                                <select name="disaster_id" class="form-select">
                                    <option value="" hidden disabled selected>Select Disaster</option>
                                    @foreach ($disasterList as $disaster)
                                        <option value="{{ $disaster->id }}">
                                            {{ $disaster->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field-container" hidden id="evacuationSelectContainer">
                                <label for="evacuation_id">Evacuation Assigned</label>
                                <select name="evacuation_id" class="form-select">
                                    <option value="" hidden selected disabled>Select Evacuation Assigned
                                    </option>
                                    @foreach ($evacuationList as $evacuationCenter)
                                        <option value="{{ $evacuationCenter->id }}">
                                            {{ $evacuationCenter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-button-container" hidden>
                                <button id="recordEvacueeInfoBtn"></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
