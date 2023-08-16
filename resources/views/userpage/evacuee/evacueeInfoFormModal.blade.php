<div class="modal fade" id="evacueeInfoFormModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-label-container">
                <h1 class="modal-label"></h1>
            </div>
            <div class="modal-body">
                <form id="evacueeInfoForm">
                    @csrf
                    <div class="form-content">
                        <div class="col-lg-6 field-container">
                            <label class="">Disaster</label>
                            <select name="disaster_id" id="disaster_id" class="form-select">
                                <option value="" hidden disabled selected>Select Disaster</option>
                                @foreach ($disasterList as $disaster)
                                    <option value="{{ $disaster->id }}">
                                        {{ $disaster->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 field-container" id="dateEntryContainer">
                            <label class="">Date Entry</label>
                            <input type="text" name="date_entry" id="date_entry" class="form-control"
                                autocomplete="off" placeholder="Select Date Entry">
                        </div>
                        <div class="field-container">
                            <label>Barangay</label>
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
                        <div class="field-container" id="evacuationSelectContainer">
                            <label>Evacuation Assigned</label>
                            <select name="evacuation_assigned" class="form-select">
                                <option value="" hidden selected disabled>Select Evacuation Assigned
                                </option>
                                @foreach ($evacuationList as $evacuationCenter)
                                    <option value="{{ $evacuationCenter->name }}">
                                        {{ $evacuationCenter->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 field-container">
                            <label>Infants</label>
                            <input type="number" name="infants" id="infants" class="form-control" autocomplete="off"
                                placeholder="Infants">
                        </div>
                        <div class="col-lg-4 field-container">
                            <label>Minors</label>
                            <input type="number" name="minors" id="minors" class="form-control" autocomplete="off"
                                placeholder="Minors">
                        </div>
                        <div class="col-lg-4 field-container">
                            <label>Senior Citizen</label>
                            <input type="number" name="senior_citizen" id="senior_citizen" class="form-control"
                                autocomplete="off" placeholder="Senior Citizen">
                        </div>
                        <div class="col-lg-4 field-container">
                            <label>PWD</label>
                            <input type="number" name="pwd" id="pwd" class="form-control" autocomplete="off"
                                placeholder="PWD">
                        </div>
                        <div class="col-lg-4 field-container">
                            <label>Pregnant</label>
                            <input type="number" name="pregnant" id="pregnant" class="form-control" autocomplete="off"
                                placeholder="Pregnant">
                        </div>
                        <div class="col-lg-4 field-container">
                            <label>Lactating</label>
                            <input type="number" name="lactating" id="lactating" class="form-control"
                                autocomplete="off" placeholder="Lactating">
                        </div>
                        <div class="col-lg-3 field-container">
                            <label>Families</label>
                            <input type="number" name="families" id="families" class="form-control"
                                autocomplete="off" placeholder="Families">
                        </div>
                        <div class="col-lg-3 field-container">
                            <label>No. Individual</label>
                            <input type="number" name="individuals" id="individuals" class="form-control"
                                autocomplete="off" placeholder="No. Individual">
                        </div>
                        <div class="col-lg-3 field-container">
                            <label>Male</label>
                            <input type="number" name="male" id="male" class="form-control"
                                autocomplete="off" placeholder="Male">
                        </div>
                        <div class="col-lg-3 field-container">
                            <label>Female</label>
                            <input type="number" name="female" id="female" class="form-control"
                                autocomplete="off" placeholder="Female">
                        </div>
                        <div class="field-container">
                            <label>Remarks</label>
                            <textarea type="text" name="remarks" id="remarks" rows="5" class="form-control" autocomplete="off"
                                placeholder="Leave a remarks..."></textarea>
                        </div>
                        <div class="form-button-container">
                            <button id="recordEvacueeInfoBtn" class="btn-submit">Record</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
