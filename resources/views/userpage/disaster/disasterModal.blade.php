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
                        <div class="form-button-container">
                            <button id="submitDisasterBtn"></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
