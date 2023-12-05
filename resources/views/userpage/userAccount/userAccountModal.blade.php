<div class="modal fade" id="userAccountModal" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <header class="modal-label-container">
                <h1 class="modal-label"></h1>
                <button type="button" data-bs-dismiss="modal" id="closeModalBtn">
                    <i class="bi bi-x-lg"></i>
                </button>
            </header>
            <div class="modal-body">
                <form id="accountForm">
                    @csrf
                    <div class="form-content">
                        <div class="field-container" id="organization-container">
                            <label for="organization">Organization</label>
                            <select name="organization" class="form-select" id="organization"
                                placeholder="Enter Organization">
                                <option value="" hidden selected disabled>Select Organization</option>
                                @if (auth()->user()->organization == 'CDRRMO')
                                    <option value="CDRRMO">CDRRMO</option>
                                @else
                                    <option value="CDRRMO">CDRRMO</option>
                                    <option value="CSWD">CSWD</option>
                                @endif
                            </select>
                        </div>
                        <div class="field-container" id="position-container" hidden>
                            <label for="position">Position</label>
                            <select name="position" class="form-select" id="position" placeholder="Enter Position">
                            </select>
                        </div>
                        <div class="field-container" id="name-container" hidden>
                            <label for="name">Full Name</label>
                            <input type="text" name="name" class="form-control" id="name"
                                placeholder="Enter Full Name">
                        </div>
                        <div class="field-container" id="email-container" hidden>
                            <label for="email">Email Address</label>
                            <input type="email" name="email" class="form-control" id="email"
                                placeholder="Enter Email Address">
                        </div>
                        <div class="form-button-container">
                            <button id="saveProfileDetails" class="modalBtn">
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
