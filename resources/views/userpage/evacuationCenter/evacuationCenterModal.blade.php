<div class="modal fade" id="evacuationCenterModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header justify-center bg-green-600">
                <h1 class="modal-title fs-5 text-white font-extrabold"></h1>
            </div>
            <div class="modal-body">
                <form id="evacuationCenterForm">
                    @csrf
                    <div class="bg-slate-50 pt-3 pb-2 rounded">
                        <div class="flex-auto">
                            <div class="flex flex-wrap">
                                <input type="text" id="operation" hidden>
                                <div class="field-container">
                                    <label class="mb-0">Name</label>
                                    <input type="text" name="name" class="form-control"
                                        autocomplete="off" placeholder="Enter Name" id="name">
                                </div>
                                <div class="field-container">
                                    <label class="mb-0">Barangay</label>
                                    <select name="barangayName" class="form-select" id="barangayName">
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
                                <div class="field-container">
                                    <label class="mb-0">Location</label>
                                    <div class="border-2 rounded-md border-slate-400">
                                        <div class="h-96 rounded" id="map"></div>
                                    </div>
                                    <input type="text" name="latitude" id="latitude" hidden>
                                    <input type="text" name="longitude" id="longitude" hidden>
                                    <span id="location-error" class="error"></span>
                                </div>
<<<<<<< Updated upstream
                                <div class="w-full p-4">
=======
                                <div class="w-full px-4 pt-3 pb-4">
>>>>>>> Stashed changes
                                    <button id="saveEvacuationCenterBtn"
                                        class="btn-submit p-2 float-right">Create</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
