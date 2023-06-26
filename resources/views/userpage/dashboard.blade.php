<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="shortcut icon" href="{{ asset('assets/img/e-ligtas-logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    {{-- @vite(['resources/js/app.js']) --}}
    <title>{{ config('app.name') }}</title>
</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <x-messages />

        <div class="main-content">
            <div class="dashboard-logo relative mb-14">
                <i class="bi bi-speedometer2 text-2xl p-2 bg-slate-600 text-white rounded"></i>
                <span class="text-2xl font-bold tracking-wider mx-2">DASHBOARD</span>
                <hr class="horizontal-line">
                <button
                    class="float-right bg-green-700 hover:bg-green-800 p-2 mt-2 rounded font-medium text-white drop-shadow-xl transition ease-in-out delay-150 hover:scale-105 duration-100">
                    <i class="bi bi-printer pr-2"></i>
                    Generate Report Data
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="widget bg-green-400 drop-shadow-lg rounded max-w-full">
                    {{-- <div class="widget-logo flex justify-center items-center">
                        <img class="pt-52" src="{{ asset('assets/img/cdrrmo-logo.png') }}">
                    </div> --}}
                    <div class="content-logo flex justify-between my-3 p-1">
                        <div class="content-description px-2">
                            <h5 class="font-semibold tracking-wide">Evacuation Center (Active)</h5>
                            <span class="text-4xl font-bold">{{ $activeEvacuation }}</span>
                        </div>
                        <div class="content-header rounded">
                            <img src="{{ asset('assets/img/evacuation.png') }}" style="width:3rem;">
                        </div>
                    </div>
                </div>
                <div class="widget bg-red-400 drop-shadow-lg rounded max-w-full">
                    {{-- <div class="widget-logo flex justify-center items-center">
                        <img class="pt-52" src="{{ asset('assets/img/cdrrmo-logo.png') }}">
                    </div> --}}
                    <div class="content-logo flex justify-between my-3 p-1">    
                        <div class="content-description px-2">
                            <h5 class="font-semibold tracking-wide">Evacuation Center (Inactive)</h5>
                            <span class="text-4xl font-bold">{{ $inActiveEvacuation }} </span>
                        </div>
                        <div class="content-header rounded">
                            <img src="{{ asset('assets/img/evacuation.png') }}" style="width:3rem;">
                        </div>
                    </div>
                </div>
                <div class="widget bg-yellow-300 drop-shadow-lg rounded max-w-full">
                    {{-- <div class="widget-logo flex justify-center items-center">
                        <img class="pt-52" src="{{ asset('assets/img/cdrrmo-logo.png') }}">
                    </div> --}}
                    <div class="content-logo flex justify-between my-3 p-1">
                        <div class="content-description px-2">
                            <h5 class="font-semibold tracking-wide">Evacuee (On Evacuation Center)</h5>
                            <span class="text-4xl font-bold" id="onEvacuationCenter">{{ $inEvacuationCenter }}</span>
                        </div>
                        <div class="content-header rounded">
                            <img src="{{ asset('assets/img/family.png') }}" style="width:3rem;">
                        </div>
                    </div>
                </div>
                <div class="widget bg-blue-300 drop-shadow-lg rounded max-w-full">
                    {{-- <div class="widget-logo flex justify-center items-center">
                        <img class="pt-52" src="{{ asset('assets/img/cdrrmo-logo.png') }}">
                    </div> --}}
                    <div class="content-logo flex justify-between my-3 p-1">
                        <div class="content-description px-2">
                            <h5 class="font-semibold tracking-wide">Evacuee(Returned)</h5>
                            <span class="text-4xl font-bold">{{ $isReturned }}</span>
                        </div>
                        <div class="content-header rounded">
                            <img src="{{ asset('assets/img/family.png') }}" style="width:3rem;">
                        </div>
                    </div>
                </div>
            </div>

            <figure class="pie-chart-container mb-3 mt-5">
                <div id="Typhoon" class="bg-slate-50 rounded shadow-lg mr-3"></div>
                <div id="TyphoonBarGraph" class="bg-slate-200 rounded shadow-lg flex-1"></div>
            </figure>
            <figure class="pie-chart-container mb-3">
                <div id="Flooding" class="bg-slate-50 rounded shadow-lg mr-3"></div>
                <div id="FloodingBarGraph" class="bg-slate-200 rounded shadow-lg flex-1"></div>
            </figure>
        </div>
    </div>

    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            Highcharts.chart('Typhoon', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Typhoon'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y}</b>'
                },
                series: [{
                    name: 'Evacuee',
                    colorByPoint: true,
                    data: [{
                        name: 'Male',
                        y: {{ Js::from($typhoonMaleData) }},
                        color: '#dc2626'
                    }, {
                        name: 'Female',
                        y: {{ Js::from($typhoonFemaleData) }},
                        color: '#2c3e50'
                    }]
                }],
            });

            Highcharts.chart('TyphoonBarGraph', {
                chart: {
                    type: 'bar'
                },
                title: {
                    text: 'Typhoon Evacuee Statistics'
                },
                xAxis: {
                    categories: ['4Ps', 'PWD', 'Pregnant', 'Lactating', 'Student', 'Working'],
                },
                yAxis: {
                    allowDecimals: false,
                    title: {
                        text: 'Estimated Numbers'
                    },
                },
                legend: {
                    reversed: true
                },
                plotOptions: {
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
                    name: '4Ps',
                    data: [{{ Js::from($typhoon_4Ps) }}, '', '', '', '', ''],
                    color: '#e74c3c'
                }, {
                    name: 'PWD',
                    data: ['', {{ Js::from($typhoon_PWD) }}, '', '', '', ''],
                    color: '#3498db'
                }, {
                    name: 'Pregnant',
                    data: ['', '', {{ Js::from($typhoon_pregnant) }}, '', '', ''],
                    color: '#2ecc71'
                }, {
                    name: 'Lactating',
                    data: ['', '', '', {{ Js::from($typhoon_lactating) }}, '', ''],
                    color: '#1abc9c'
                }, {
                    name: 'Student',
                    data: ['', '', '', '', {{ Js::from($typhoon_student) }}, ''],
                    color: '#e67e22'
                }, {
                    name: 'Working',
                    data: ['', '', '', '', '', {{ Js::from($typhoon_working) }}],
                    color: '#9b59b6'
                }]
            });

            Highcharts.chart('Flooding', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Flashflood'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y}</b>'
                },
                series: [{
                    name: 'Evacuee',
                    colorByPoint: true,
                    data: [{
                        name: 'Male',
                        y: {{ Js::from($floodingMaleData) }},
                        color: '#dc2626'
                    }, {
                        name: 'Female',
                        y: {{ Js::from($floodingFemaleData) }},
                        color: '#2c3e50'
                    }]
                }]
            });

            Highcharts.chart('FloodingBarGraph', {
                chart: {
                    type: 'bar'
                },
                title: {
                    text: 'Flashflood Evacuee Statistics'
                },
                xAxis: {
                    categories: ['4Ps', 'PWD', 'Pregnant', 'Lactating', 'Student', 'Working']
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
                    name: '4Ps',
                    data: [{{ Js::from($flooding_4Ps) }}, '', '', '', '', ''],
                    color: '#e74c3c'
                }, {
                    name: 'PWD',
                    data: ['', {{ Js::from($flooding_PWD) }}, '', '', '', ''],
                    color: '#3498db'
                }, {
                    name: 'Pregnant',
                    data: ['', '', {{ Js::from($flooding_pregnant) }}, '', '', ''],
                    color: '#2ecc71'
                }, {
                    name: 'Lactating',
                    data: ['', '', '', {{ Js::from($flooding_lactating) }}, '', ''],
                    color: '#1abc9c'
                }, {
                    name: 'Student',
                    data: ['', '', '', '', {{ Js::from($flooding_student) }}, ''],
                    color: '#e67e22'
                }, {
                    name: 'Working',
                    data: ['', '', '', '', '', {{ Js::from($flooding_working) }}],
                    color: '#9b59b6'
                }]
            });

            Echo.channel('active-evacuees').listen('ActiveEvacuees', (e) => {
                document.getElementById('onEvacuationCenter').innerHTML = e.activeEvacuees;
            })
        });
    </script>
</body>

</html>
