<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.headPackage')
</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <div class="main-content">
            <div class="label-container">
                <div class="icon-container">
                    <div class="icon-content">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                </div>
                <span>DASHBOARD</span>
            </div>
            <hr>
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
                                        <button type="button" data-bs-dismiss="modal" aria-label="Close"
                                            id="closeModalBtn">
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
                                                    <select class="form-control form-select" name="disaster_id"
                                                        id="disaster_id">
                                                    </select>
                                                </div>
                                                <div class="form-button-container">
                                                    <button class="btn-submit" id="btnSubmit">Generate</button>
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
            <section class="widget-container">
                <div class="widget">
                    <div class="widget-content">
                        <div class="content-description">
                            <div class="wigdet-header">
                                <p>Evacuee (On Evacuation)</p>
                                <i class="bi bi-people"></i>
                            </div>
                            <p id="totalEvacuee">{{ $totalEvacuee }}</p>
                            <span>Total</span>
                        </div>
                    </div>
                </div>
                @if (auth()->user()->organization == 'CSWD')
                    <div class="widget">
                        <div class="widget-content">
                            <div class="content-description">
                                <div class="wigdet-header">
                                    <p>Evacuation Center (Active)</p>
                                    <i class="bi bi-house-heart"></i>
                                </div>
                                <p>{{ $activeEvacuation }}</p>
                                <span>Total</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="widget">
                        <div class="widget-content">
                            <div class="content-description">
                                <div class="wigdet-header">
                                    <p>Today's Reports</p>
                                    <i class="bi bi-megaphone"></i>
                                </div>
                                <p id="totalReport">{{ $residentReport }}</p>
                                <span>Total</span>
                            </div>
                        </div>
                    </div>
                @endif
            </section>
            @if (auth()->user()->organization == 'CDRRMO')
                <figure class="chart-container report">
                    <div id="report-chart" class="bar-graph"></div>
                </figure>
            @else
                @foreach ($disasterData as $count => $disaster)
                    @if ($disaster['totalEvacuee'] != 0)
                        <figure class="chart-container">
                            <div id="evacueePie{{ $count + 1 }}" class="pie-chart evacuee"></div>
                            <div id="evacueeGraph{{ $count + 1 }}" class="bar-graph evacuee"></div>
                        </figure>
                    @endif
                @endforeach
            @endif
        </div>
        @include('userpage.changePasswordModal')
    </div>

    @include('partials.script')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous"></script>
    @include('partials.toastr')
    <script>
        $(document).ready(() => {
            let validator, modal = $("#generateReportModal"),
                form = $('#generateReportForm'),
                disasterList = $('#disaster-list'),
                searchResults = $('#disaster_id');

            $('#disaster_year').change(function() {
                $.get(`{{ route('fetch.disasters', 'disasterYear') }}`
                        .replace('disasterYear', $(this).val()))
                    .done(response => {
                        searchResults.empty().append(response.map(disaster =>
                            `<option class="searchResult" value="${disaster.id}">${disaster.name}</option>`
                        ));
                        disasterList.prop('hidden', 0);
                    }).fail(showErrorMessage);
            });

            validator = form.validate({
                rules: {
                    disaster_year: 'required'
                },
                messages: {
                    disaster_year: 'Please select year.'
                },
                errorElement: 'span'
            });

            $("#btnSubmit").click(() => {
                if (validator.form()) {
                    form.attr('action', '{{ route('generate.evacuee.data') }}').off('submit').submit();
                    modal.modal('hide');
                }
            });

            modal.on('hidden.bs.modal', () => {
                validator && validator.resetForm();
                searchResults.empty();
                disasterList.prop('hidden', 1);
                form[0].reset();
            });

            @if (auth()->user()->organization == 'CDRRMO')
                reportData();

                Echo.channel('incident-report').listen('IncidentReport', (e) => {
                    $("#totalReport").text(e.totalReport);
                });

                Echo.channel('notification').listen('Notification', (e) => {
                    reportData();
                });
            @else
                evacueeData();

                Echo.channel('active-evacuees').listen('ActiveEvacuees', (e) => {
                    $("#totalEvacuee").text(e.activeEvacuees);
                    evacueeData();
                });
            @endif
        });

        @if (auth()->user()->organization == 'CDRRMO')
            function reportData() {
                $.get("{{ route('fetchReportData') }}").done(response => {
                    const color = {
                        'Emergency': '#ef4444',
                        'Incident': '#ffcb2f',
                        'Flooded': '#2682fa',
                        'Roadblocked': '#000000'
                    };

                    Highcharts.chart('report-chart', {
                        title: {
                            text: 'Resident Report Count',
                            align: 'center'
                        },
                        subtitle: {
                            text: `From ${formatDateTime(response['start_date'], 'date')} to ${formatDateTime(new Date(), 'date')}`,
                            align: 'center'
                        },
                        series: response['data'].map(({
                            type,
                            data
                        }) => ({
                            name: type,
                            color: color[type],
                            data: data.map(({
                                report_date,
                                report_count
                            }) => ({
                                x: new Date(report_date).getTime(),
                                y: report_count
                            }))
                        })),
                        yAxis: {
                            title: {
                                text: 'Count'
                            }
                        },
                        xAxis: {
                            type: 'datetime',
                            labels: {
                                formatter: function() {
                                    return formatDateTime(this.value, 'date');
                                }
                            }
                        },
                        exporting: false,
                        credits: {
                            enabled: false
                        }
                    });
                });
            }
        @else
            function evacueeData() {
                $.ajax({
                    url: "{{ route('fetchDisasterData') }}",
                    method: 'GET',
                    dataType: 'json',
                    success(disasterData) {
                        disasterData.forEach((disaster, count) => {
                            if (disaster['totalEvacuee'] != 0) {
                                initializePieChart(disaster, count);
                                initializeBarGraph(disaster, count);
                            }
                        });
                    },
                    error: () => showErrorMessage("Unable to fetch data.")
                });
            }

            function initializePieChart(disaster, count) {
                Highcharts.chart(`evacueePie${count + 1}`, {
                    chart: {
                        type: 'pie'
                    },
                    title: {
                        text: `As Affected of ${disaster.disasterName}`
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.y}</b>'
                    },
                    plotOptions: {
                        pie: {
                            dataLabels: {
                                enabled: true,
                                style: {
                                    textOutline: 'none'
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'Evacuee',
                        colorByPoint: true,
                        data: [{
                                name: 'Male',
                                y: parseInt(disaster.totalMale),
                                color: '#0284c7'
                            },
                            {
                                name: 'Female',
                                y: parseInt(disaster.totalFemale),
                                color: '#f43f5e'
                            }
                        ]
                    }],
                    exporting: false,
                    credits: {
                        enabled: false
                    },
                });
            }

        function initializeBarGraph(disaster, count) {
            Highcharts.chart(`evacueeGraph${count + 1}`, {
                chart: {
                    type: 'bar'
                },
                title: false,
                xAxis: {
                    categories: ['SENIOR CITIZEN', 'MINORS', 'INFANTS', 'PWD', 'PREGNANT', 'LACTATING']
                },
                yAxis: {
                    allowDecimals: false,
                    title: {
                        text: 'Estimated Numbers'
                    }
                },
                legend: {
                    reversed: true
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true,
                            style: {
                                textOutline: 'none'
                            }
                        }
                    },
                    series: {
                        stacking: 'normal',
                        dataLabels: {
                            enabled: true,
                            formatter: function() {
                                if (this.y != 0) {
                                    return this.y;
                                } else {
                                    return null;
                                }
                            }
                        }
                    }
                },
                series: [{
                    name: 'SENIOR CITIZEN',
                    data: [parseInt(disaster.totalSeniorCitizen), '', '', '', '', ''],
                    color: '#e74c3c'
                }, {
                    name: 'MINORS',
                    data: ['', parseInt(disaster.totalMinors), '', '', '', ''],
                    color: '#3498db'
                }, {
                    name: 'INFANTS',
                    data: ['', '', parseInt(disaster.totalInfants), '', '', ''],
                    color: '#2ecc71'
                }, {
                    name: 'PWD',
                    data: ['', '', '', parseInt(disaster.totalPwd), '', ''],
                    color: '#1abc9c'
                }, {
                    name: 'PREGNANT',
                    data: ['', '', '', '', parseInt(disaster.totalPregnant), ''],
                    color: '#e67e22'
                }, {
                    name: 'LACTATING',
                    data: ['', '', '', '', '', parseInt(disaster.totalLactating)],
                    color: '#9b59b6'
                }],
                exporting: false,
                credits: {
                    enabled: false
                },
            });
        }
    </script>
</body>

</html>
