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
                    <div class="pie-container" hidden>
                        <div class="pie-label"><span>Barangay Data Chart</span></div>
                        <div class="pie-content">
                            <div class="pie-figure"></div>
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
                        success(response, xhr) {
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
                    $("#totalReport").text(e.totalReport);
                });

                Echo.channel('notification').listen('Notification', (e) => {
                    reportData(false);
                });
            @else
                initializePieChart();

                initializeBarGraph();

                Echo.channel('active-evacuees').listen('ActiveEvacuees', (e) => {
                    $("#totalEvacuee").text(e.activeEvacuees);
                    initializePieChart(false);
                    initializeBarGraph(false);
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
                                        'LACTATING': '#9b59b6',
                                    } [category],
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
                            $('.pie-container').prop('hidden', 0);
                            $('.pie-chart').remove();
                            barangayData.forEach((barangay, count) => {
                                $('.pie-figure').append(
                                    `<div id="evacueePie${count + 1}" class="pie-chart"></div>`);
                                Highcharts.chart(`evacueePie${count + 1}`, {
                                    chart: {
                                        type: 'pie'
                                    },
                                    title: false,
                                    subtitle: {
                                        text: barangay.barangay,
                                        align: 'center',
                                        verticalAlign: 'bottom'
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
                                            },
                                            center: ['50%', '50%'],
                                            size: '80%'
                                        }
                                    },
                                    series: [{
                                        name: 'Evacuee',
                                        colorByPoint: true,
                                        data: [{
                                            name: 'Male',
                                            y: parseInt(barangay.male),
                                            color: '#0284c7'
                                        }, {
                                            name: 'Female',
                                            y: parseInt(barangay.female),
                                            color: '#f43f5e'
                                        }]
                                    }],
                                    exporting: false,
                                    credits: {
                                        enabled: false
                                    }
                                })
                            });
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
