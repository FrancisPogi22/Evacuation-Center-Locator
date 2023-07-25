<div class="modal fade" id="evacueeInfoFormModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header justify-center bg-green-600">
                <h1 class="modal-title fs-5 text-center text-white font-extrabold"></h1>
            </div>
            <div class="modal-body">
                <form id="evacueeInfoForm">
                    @csrf
                    <div class="bg-slate-50 pt-3 pb-2 rounded">
                        <div class="flex-auto">
                            <div class="flex flex-wrap">
                                <input type="text" id="operation" hidden>
<<<<<<< Updated upstream
                                <div class="w-full mb-3 lg:w-3/12 px-4">
                                    <label class="mb-0">House Hold #</label>
                                    <input type="number" name="houseHoldNumber" class="form-control"
                                        autocomplete="off" placeholder="HH#">
                                </div>
                                <div class="w-full mb-3 lg:w-9/12 px-4">
                                    <label class="mb-0">Full Name</label>
                                    <input type="text" name="fullName" class="form-control"
                                        autocomplete="off" placeholder="Enter Full Name">
                                </div>
                                <div class="w-full mb-3 lg:w-6/12 px-4">
                                    <label class="mb-0">Sex</label>
=======
                                <div class="w-full mb-3 lg:w-3/12 px-4 field-container">
                                    <label>House Hold #</label>
                                    <input type="number" name="houseHoldNumber" class="form-control"
                                        autocomplete="off" placeholder="HH#">
                                </div>
                                <div class="w-full mb-3 lg:w-9/12 px-4 field-container">
                                    <label>Full Name</label>
                                    <input type="text" name="fullName" class="form-control"
                                        autocomplete="off" placeholder="Enter Full Name">
                                </div>
                                <div class="w-full mb-3 lg:w-6/12 px-4 field-container">
                                    <label>Sex</label>
>>>>>>> Stashed changes
                                    <select name="sex" class="form-select">
                                        <option value="">Select Sex</option>
                                        <option value="Male">
                                            Male
                                        </option>
                                        <option value="Female">
                                            Female
                                        </option>
                                    </select>
                                </div>
<<<<<<< Updated upstream
                                <div class="w-full mb-3 lg:w-6/12 px-4">
                                    <label class="mb-0">Age</label>
=======
                                <div class="w-full mb-3 lg:w-6/12 px-4 field-container">
                                    <label>Age</label>
>>>>>>> Stashed changes
                                    <input type="number" name="age" class="form-control"
                                        autocomplete="off" placeholder="Enter Age">
                                </div>
                                <div class="flex flex-wrap w-full field-container" id="dateFormFieldsContainer">
                                    <div class="w-full mb-3 lg:w-6/12 px-4" id="dateEntryContainer">
                                        <label class="mb-0">Date Entry</label>
                                        <input type="text" name="dateEntry" id="dateEntry"
                                            class="form-control" autocomplete="off"
                                            placeholder="Select Date Entry">
                                    </div>
<<<<<<< Updated upstream
                                    <div class="w-full mb-3 lg:w-6/12 px-4" id="dateOutContainer">
                                        <label class="mb-0">Date Out</label>
=======
                                    <div class="w-full mb-3 lg:w-6/12 px-4 field-container" id="dateOutContainer">
                                        <label>Date Out</label>
>>>>>>> Stashed changes
                                        <input type="text" name="dateOut" id="dateOut"
                                            class="form-control" autocomplete="off"
                                            placeholder="Select Date Out">
                                    </div>
                                </div>
<<<<<<< Updated upstream
                                <div class="w-full mb-3 lg:w-6/12 px-4">
                                    <label class="mb-0">Barangay</label>
=======
                                <div class="w-full mb-3 lg:w-6/12 px-4 field-container">
                                    <label>Barangay</label>
>>>>>>> Stashed changes
                                    <select name="barangay" class="form-select">
                                        <option value="">Select Barangay</option>
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
<<<<<<< Updated upstream
                                <div class="w-full mb-3 lg:w-6/12 px-4">
                                    <label class="mb-0">Disaster</label>
=======
                                <div class="w-full mb-3 lg:w-6/12 px-4 field-container">
                                    <label>Disaster</label>
>>>>>>> Stashed changes
                                    <select name="disasterType" id="disasterType" class="form-select">
                                        <option value="">Select Disaster</option>
                                        {{-- @if ($disasterList != null)
                                            @foreach ($disasterList as $disaster)
                                                <option value="{{ $disaster->type }}">
                                                    {{ $disaster->type }}</option>
                                            @endforeach
                                        @endif --}}
                                    </select>
                                </div>
                                <div class="w-full px-4 hidden field-container" id="typhoonSelectContainer">
                                    <div class="w-full mb-3">
                                        <select name="typhoon" id="typhoon" class="form-select">
                                            <option value="">Select Typhoon</option>
                                            {{-- @foreach ($typhoonList as $typhoon)
                                                <option value="{{ $typhoon->id }}">
                                                    {{ $typhoon->name }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>
                                <div class="w-full px-4 hidden field-container" id="flashfloodSelectContainer">
                                    <div class="w-full mb-3">
                                        <select name="flashflood" id="flashflood" class="form-select">
                                            <option value="">Select Location</option>
                                            {{-- @foreach ($flashfloodList as $flashflood)
                                                <option value="{{ $flashflood->id }}">
                                                    {{ $flashflood->location }}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>
                                <input type="text" name="disasterInfo" hidden>
                                <div class="field-container" id="evacuationSelectContainer">
                                    <label class="mb-0">Evacuation Assigned</label>
                                    <select name="evacuationAssigned" class="form-select">
                                        <option value="">Select Evacuation Assigned</option>
                                        @foreach ($evacuationList as $evacuationCenter)
                                            <option value="{{ $evacuationCenter->name }}">
                                                {{ $evacuationCenter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="text" name="defaultEvacuationAssigned" hidden>
                                <div class="w-full px-4">
                                    <div class="flex justify-around flex-wrap">
                                        <div class="checkbox-container px-2">
                                            <input type="checkbox" id="fourps" name="fourps"
                                                class="checkbox">
                                            <label for="fourps">4ps</label>
                                        </div>
                                        <div class="checkbox-container px-2">
                                            <input type="checkbox" id="pwd" name="pwd"
                                                class="checkbox">
                                            <label for="pwd">PWD</label>
                                        </div>
                                        <div class="checkbox-container px-2">
                                            <input type="checkbox" id="pregnant" name="pregnant"
                                                class="checkbox">
                                            <label for="pregnant">Pregnant</label>
                                        </div>
                                        <div class="checkbox-container px-2">
                                            <input type="checkbox" id="lactating" name="lactating"
                                                class="checkbox">
                                            <label for="lactating">Lactating</label>
                                        </div>
                                        <div class="checkbox-container px-2">
                                            <input type="checkbox" id="student" name="student"
                                                class="checkbox">
                                            <label for="student">Student</label>
                                        </div>
                                        <div class="checkbox-container px-2">
                                            <input type="checkbox" id="working" name="working"
                                                class="checkbox">
                                            <label for="working">Working</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full p-4">
                                    <button id="saveEvacueeInfoBtn"
                                        class="btn-submit p-2 float-right">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
