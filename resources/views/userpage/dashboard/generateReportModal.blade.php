<div class="report-container">
    <p>Current Disaster:
        <span>{{ $onGoingDisasters->isEmpty() ? 'No Disaster' : implode(' | ', $onGoingDisasters->pluck('name')->toArray()) }}</span>
    </p>
    @if (auth()->user()->position == 'President' || (auth()->user()->position == 'Focal' && !$disaster->isEmpty()))
        <div class="generate-button-container">
            <button type="button" data-bs-toggle="modal" data-bs-target="#generateReportModal"
                class="btn-submit generateBtn">
                <i class="bi bi-printer"></i>
                Generate Disaster Data
            </button>
            <div class="modal fade" id="generateReportModal" data-bs-backdrop="static" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-label-container">
                            <h1 class="modal-label">Generate Excel Report</h1>
                            <button type="button" data-bs-dismiss="modal" aria-label="Close" id="closeModalBtn">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" id="generateReportForm">
                                @csrf
                                <div class="form-content">
                                    <div class="field-container">
                                        <label for="disaster_year">Disaster Year</label>
                                        <select class="form-control form-select" name="disaster_year"
                                            id="disaster_year">
                                            <option value="" selected hidden disabled>Select year
                                            </option>
                                            @foreach ($disaster as $disasterYear)
                                                <option value="{{ $disasterYear->year }}">
                                                    {{ $disasterYear->year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field-container" id="disaster-list" hidden>
                                        <label for="disaster_name">Available Disaster</label>
                                        <select class="form-control form-select" name="disaster_id" id="disaster_id">
                                        </select>
                                    </div>
                                    <div class="form-button-container">
                                        <button class="btn-submit modalBtn" id="btnSubmit">
                                            <div id="btn-loader">
                                                <div id="loader-inner"></div>
                                            </div>
                                            Generate
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
