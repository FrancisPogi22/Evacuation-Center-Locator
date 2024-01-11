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
            @include('userpage.dashboard.generateReportModal')
            <section class="widget-container">
                <div class="widget">
                    <div class="widget-content">
                        <div class="content-description">
                            <div class="wigdet-header">
                                <p>Evacuee (On Evacuation)</p>
                                <img src="{{ asset('assets/img/On-Evacuation.png') }}" alt="icon">
                            </div>
                            <p id="evacuated">{{ $evacuated }}</p>
                            <span>Total</span>
                        </div>
                    </div>
                </div>
                <div class="widget">
                    <div class="widget-content">
                        <div class="content-description">
                            <div class="wigdet-header">
                                <p>Evacuee (Returned Home)</p>
                                <img src="{{ asset('assets/img/Return-Home.png') }}" alt="icon">
                            </div>
                            <p id="returnedHome">{{ $returnedHome }}</p>
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
                                    <img src="{{ asset('assets/img/Active-Logo.png') }}" alt="icon">
                                </div>
                                <p id="activeEvacuation">{{ $activeEvacuation }}</p>
                                <span>Total</span>
                            </div>
                        </div>
                    </div>
                    <div class="widget">
                        <div class="widget-content">
                            <div class="content-description">
                                <div class="wigdet-header">
                                    <p>Evacuation Center (Inactive)</p>
                                    <img src="{{ asset('assets/img/Inactive-Logo.png') }}" alt="icon">
                                </div>
                                <p id="inactiveEvacuation">{{ $inactiveEvacuation }}</p>
                                <span>Total</span>
                            </div>
                        </div>
                    </div>
                    <div class="widget">
                        <div class="widget-content">
                            <div class="content-description">
                                <div class="wigdet-header">
                                    <p>Evacuation Center (Full)</p>
                                    <img src="{{ asset('assets/img/Full-Logo.png') }}" alt="icon">
                                </div>
                                <p id="fullEvacuation">{{ $fullEvacuation }}</p>
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
                                    <img src="{{ asset('assets/img/Todays-Report.png') }}" alt="icon">
                                </div>
                                <p id="todayReport">{{ $todayReport }}</p>
                                <span>Total</span>
                            </div>
                        </div>
                    </div>
                    <div class="widget">
                        <div class="widget-content">
                            <div class="content-description">
                                <div class="wigdet-header">
                                    <p>Resolving Reports</p>
                                    <img src="{{ asset('assets/img/Resolving-Report.png') }}" alt="icon">
                                </div>
                                <p id="resolvingReport">{{ $resolvingReport }}</p>
                                <span>Total</span>
                            </div>
                        </div>
                    </div>
                    <div class="widget">
                        <div class="widget-content">
                            <div class="content-description">
                                <div class="wigdet-header">
                                    <p>Resolved Reports</p>
                                    <img src="{{ asset('assets/img/Resolved-Report.png') }}" alt="icon">
                                </div>
                                <p id="resolvedReport">{{ $resolvedReport }}</p>
                                <span>Total</span>
                            </div>
                        </div>
                    </div>
                @endif
            </section>
            @if (auth()->user()->organization == 'CDRRMO')
                <figure class="chart-container report">
                    <div id="loader" class="show">
                        <div id="loading-text">Getting Report Data...</div>
                        <div id="loader-inner"></div>
                    </div>
                    <div id="report-chart" class="bar-graph"></div>
                </figure>
            @else
                <div class="dasboard-content">
                    <div id="loader" class="show">
                        <div id="loading-text">Getting Evacuees Data...</div>
                        <div id="loader-inner"></div>
                    </div>
                    <div class="static-graph">
                        <div class="evac-list">
                            <div class="list-header">
                                <p>List of Evacuation Center</p>
                                <select class="form-control feedBackOptions">
                                    <option value="" hidden selected disabled>Select Feedback</option>
                                    <option value="clean_facilities">Clean Facilities</option>
                                    <option value="responsive_aid">Responsive Aid</option>
                                    <option value="safe_evacutaion">Safe Evacutaion</option>
                                    <option value="sufficient_food_supply">Sufficient Food Supply</option>
                                    <option value="comfortable_evacuation">Comfortable Evacuation</option>
                                    <option value="well_managed_evacuation">Well Managed Evacuation</option>
                                </select>
                            </div>
                            <div class="evac-list-container">
                                <div class="empty-data" hidden>
                                    <p>No Data Found.</p>
                                </div>
                                <table class="table" id="feedbackTable" width="100%" hidden>
                                    <thead>
                                        <tr>
                                            <th>Evacuation</th>
                                            <th>Feedback Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="pie-container" hidden>
                            <div class="pie-label"><span>Total of Evacuees per Barangay</span></div>
                            <div class="pie-content">
                                <div class="pie-figure"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bar-container">
                        <figure class="bar-figure"></figure>
                    </div>
                </div>
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
            $(document).on('change', '.feedBackOptions', function() {
                if ($('.feedBackOptions').val() == '') return;

                $.ajax({
                    type: "GET",
                    url: "{{ route('get.top.evac', 'feedBackType') }}".replace('feedBackType',
                        $('.feedBackOptions').val()),
                    success(response) {
                        let noFeedback = 1;
                        $('tbody').empty();

                        response.topEvacList.forEach(evacuation => {
                            if (evacuation.feedback_total > 0) {
                                $('.empty-data').prop('hidden', 1);
                                $('#feedbackTable').prop('hidden', 0);
                                $('tbody').append(`
                                    <tr>
                                        <td>
                                            ${evacuation.name}
                                        </td>
                                        <td>
                                            ${evacuation.feedback_total}
                                        </td>
                                    </tr>
                                `);

                                noFeedback = 0;
                            }
                        });

                        if (noFeedback) {
                            $('#feedbackTable').prop('hidden', 1);
                            $('.empty-data').prop('hidden', 0);
                        }
                    },
                    error: showErrorMessage
                });
            });

            let validator, modal = $("#generateReportModal"),
                form = $('#generateReportForm'),
                disasterList = $('#disaster-list'),
                generateBtn = $("#btnSubmit"),
                searchResults = $('#disaster_id'),
                btnLoader = $('#btn-loader'),
                btnText = $('#btn-text');

            $('#disaster_year').change(function() {
                $.get(`{{ route('searchDisaster', 'disasterYear') }}`
                        .replace('disasterYear', $(this).val()))
                    .done(response => {
                        searchResults.empty().append(response.map(disaster =>
                            `<option class="searchResult" value="${disaster.id}">${disaster.name}</option>`
                        ));
                        disasterList.prop('hidden', 0);
                    }).fail(showErrorMessage);
            });

            $('.pie-container').click(function() {
                this.classList.toggle('active');
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

            generateBtn.click(() => {
                if (validator.form()) {
                    generateBtn.prop("disabled", 1);
                    $.ajax({
                        type: "POST",
                        url: '{{ route('generate.evacuee.data') }}',
                        data: form.serialize(),
                        xhrFields: {
                            responseType: 'blob'
                        },
                        beforeSend() {
                            btnLoader.prop('hidden', 0);
                            btnText.text('Generating');
                            $('select, #btnSubmit, #closeModalBtn')
                                .prop('disabled', 1);
                        },
                        success(response) {
                            let blob = new Blob([response], {
                                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                }),
                                link = document.createElement('a');

                            link.href = window.URL.createObjectURL(blob);
                            link.download = 'evacuee-data.xlsx';
                            link.click();
                            modal.modal('hide');
                        },
                        error: showErrorMessage,
                        complete() {
                            btnLoader.prop('hidden', 1);
                            btnText.text('Generate');
                            $('select, #btnSubmit, #closeModalBtn')
                                .prop('disabled', 0);
                        }
                    });
                }
            });

            modal.on('hidden.bs.modal', () => {
                validator && validator.resetForm();
                searchResults.empty();
                disasterList.prop('hidden', 1);
                generateBtn.prop("disabled", 0);
                form[0].reset();
            });

            @if (auth()->user()->organization == 'CDRRMO')
                reportData();

                Echo.channel('incident-report').listen('IncidentReport', (e) => {
                    $("#todayReport").text(e.todayReport);
                    $("#resolvingReport").text(e.resolvingReport);
                    $("#resolvedReport").text(e.resolvedReport);
                });

                Echo.channel('notification').listen('Notification', (e) => {
                    reportData(false);
                });
            @else
                initializePieChart();

                initializeBarGraph();

                Echo.channel('evacuees').listen('Evacuees', (e) => {
                    $("#evacuated").text(e.evacuated);
                    $("#returnedHome").text(e.returnedHome);
                    initializePieChart(false);
                    initializeBarGraph(false);
                });

                Echo.channel('disaster').listen('Disaster', (e) => {
                    let ongoingDisaster = $("#ongoingDisaster");
                    ongoingDisaster.text("");

                    if (e.onGoingDisaster.length == 0)
                        ongoingDisaster.text("No disaster.");
                    else
                        e.onGoingDisaster.forEach((disaster, index) => {
                            ongoingDisaster.append(disaster.name);

                            if (index < e.onGoingDisaster.length - 1)
                                ongoingDisaster.append(' | ');
                        });
                });

                Echo.channel('evacuation-center').listen('EvacuationCenter', (e) => {
                    $("#activeEvacuation").text(e.activeEvacuation);
                    $("#inactiveEvacuation").text(e.inactiveEvacuation);
                    $("#fullEvacuation").text(e.fullEvacuation);
                });
            @endif
        });

        @if (auth()->user()->organization == 'CDRRMO')
            function reportData(loader = true) {
                $.get("{{ route('fetchReportData') }}").done(response => {
                    if (loader) $('#loader').remove();

                    if (response['data'].length > 0) {
                        const color = {
                            'Emergency': '#ef4444',
                            'Incident': '#ffcb2f',
                            'Flooded': '#2682fa',
                            'Roadblocked': '#000000'
                        };

                        $('.chart-container.report').prop('hidden', 0);
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
                    } else {
                        $('.chart-container.report').prop('hidden', 1);
                        Highcharts.series = [];
                    }
                });
            }
        @else
            function initializeBarGraph(loader = true) {
                $('.bar-chart').remove();
                $.get("{{ route('fetchDisasterData') }}")
                    .done(disasterData => {
                        let categories = ['SENIORCITIZEN', 'MINORS', 'INFANTS', 'PWD', 'PREGNANT',
                            'LACTATING'
                        ];

                        if (loader) $('#loader').remove();

                        disasterData.forEach((disaster, count) => {
                            $('.bar-figure').append(
                                `<div id="evacueeGraph${count + 1}" class="bar-graph"></div>`);
                            Highcharts.chart(`evacueeGraph${count + 1}`, {
                                chart: {
                                    type: 'bar'
                                },
                                title: {
                                    text: `${disaster.disasterName}`
                                },
                                xAxis: {
                                    categories: ['SENIOR CITIZEN', 'MINORS', 'INFANTS', 'PWD',
                                        'PREGNANT', 'LACTATING'
                                    ]
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
                                series: categories.map((category) => ({
                                    name: category,
                                    data: categories.map((cat) => (cat == category ?
                                        parseInt(disaster[cat.toLowerCase()]) : ''
                                    )),
                                    color: {
                                        'SENIOR CITIZEN': '#e74c3c',
                                        'MINORS': '#3498db',
                                        'INFANTS': '#2ecc71',
                                        'PWD': '#1abc9c',
                                        'PREGNANT': '#e67e22',
                                        'LACTATING': '#9b59b6'
                                    } [category]
                                })),
                                exporting: false,
                                credits: {
                                    enabled: false
                                },
                            })
                        })
                    })
                    .fail(() => showErrorMessage("Unable to fetch data."));
            }

            function initializePieChart(loader = true) {
                $.get("{{ route('fetchBarangayData') }}")
                    .done(barangayData => {
                        if (loader) $('#loader').remove();

                        if (barangayData.length > 0) {
                            $('.pie-chart').remove();
                            $('.pie-container').prop('hidden', 0);
                            $('.pie-figure').append(
                                `<div id="evacueePie" class="pie-chart"></div>`);
                            Highcharts.chart(`evacueePie`, {
                                chart: {
                                    type: 'pie'
                                },
                                title: false,
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
                                        },
                                        center: ['50%', '50%'],
                                        size: '80%'
                                    }
                                },
                                series: [{
                                    name: 'Evacuee',
                                    colorByPoint: true,
                                    data: barangayData.map((barangay, count) => ({
                                        name: barangay.barangay,
                                        y: parseInt(barangay.individuals),
                                        color: `hsl(${(count / barangayData.length) * 360}, 40%, 70%)`
                                    }))
                                }],
                                exporting: false,
                                credits: {
                                    enabled: false
                                }
                            })
                        } else {
                            $('.pie-container').prop('hidden', 1);
                        }
                    })
                    .fail(() => showErrorMessage("Unable to fetch data."));
            }
        @endif
    </script>
</body>

</html>
