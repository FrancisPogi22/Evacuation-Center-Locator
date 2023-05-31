<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.content.headPackage')
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <title>{{ config('app.name') }}</title>
</head>

<body class="bg-gray-400">
    <div class="wrapper">

        @include('partials.content.header')
        @include('partials.content.sidebar')

        <div class="locator-container pt-8 pr-8 pl-28">

            <div class="dashboard-logo pb-4">
                <i class="bi bi-house text-2xl p-2 bg-slate-900 text-white rounded"></i>
                <span class="text-2xl font-bold tracking-wider mx-2">EVACUATION CENTER</span>
                <hr class="mt-4">
            </div>

            <div class="locator-content my-8 drop-shadow-2xl">
                <div class="locator-header text-center text-white h-22 bg-red-900">
                    <div class="text-2xl p-2">
                        <span>{{ config('app.name') }}</span><br>
                        <span>"E-LIGTAS"</span>
                    </div>
                </div>
                <div class="map-section">
                    <div class="w-full" id="map" style="height:600px;"></div>
                </div>
            </div>
            <div class="map-btn">
                @guest
                    <button type="button"
                        class="bg-slate-700 text-white p-2 rounded shadow-lg hover:shadow-xl">Locate
                        Nearest Evacuation</button>
                    <button type="button"
                        class="bg-red-700 text-white p-2 rounded shadow-lg hover:shadow-xl">Locate
                        Current Location</button>
                @endguest
            </div>
            <div class="evacuation-table mt-5">
                <table class="table bg-slate-50">
                    <thead>
                        <tr>
                            <th scope="col">Evacuation Center Id</th>
                            <th scope="col">Evacuation Center Name</th>
                            <th scope="col">Evacuation Center Contact</th>
                            <th scope="col">Evacuation Center Address</th>
                            <th scope="col">Latitude</th>
                            <th scope="col" colspan="2">Longitude</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($evacuationCenter as $evacuationCenterList)
                            <tr>
                                <th>{{ $evacuationCenterList->evacuation_center_id }}</th>
                                <td>{{ $evacuationCenterList->evacuation_center_name }}</td>
                                <td>{{ $evacuationCenterList->evacuation_center_contact }}</td>
                                <td>{{ $evacuationCenterList->evacuation_center_address }}</td>
                                <td>{{ $evacuationCenterList->latitude }}</td>
                                <td>{{ $evacuationCenterList->longitude }}</td>
                                @guest
                                    <td>
                                        <a href="#"
                                            class="bg-red-700 text-white p-2 rounded shadow-lg hover:shadow-xl">Locate</a>
                                    </td>
                                @endguest
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-center" colspan="4">
                                    No Evacuation Center Record Found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script async src="https://maps.googleapis.com/maps/api/js?key=...&callback=initMap"></script>
    <script>
        let map, activeInfoWindow, markers = [];

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: {
                    lat: 14.242311,
                    lng: 121.12772
                },
                zoom: 15
            });

            map.addListener("click", function(event) {
                mapClicked(event);
            });

            initMarkers();
        }

        function initMarkers() {
            const initialMarkers = <?php echo json_encode($initialMarkers); ?>;

            for (let index = 0; index < initialMarkers.length; index++) {

                const markerData = initialMarkers[index];
                const marker = new google.maps.Marker({
                    position: markerData.position,
                    label: markerData.label,
                    draggable: markerData.draggable,
                    map
                });
                markers.push(marker);

                const infowindow = new google.maps.InfoWindow({
                    content: `<b>${markerData.position.lat}, ${markerData.position.lng}</b>`,
                });
                marker.addListener("click", (event) => {
                    if (activeInfoWindow) {
                        activeInfoWindow.close();
                    }
                    infowindow.open({
                        anchor: marker,
                        shouldFocus: false,
                        map
                    });
                    activeInfoWindow = infowindow;
                    markerClicked(marker, index);
                });

                marker.addListener("dragend", (event) => {
                    markerDragEnd(event, index);
                });
            }
        }

        function mapClicked(event) {
            console.log(map);
            console.log(event.latLng.lat(), event.latLng.lng());
        }

        function markerClicked(marker, index) {
            console.log(map);
            console.log(marker.position.lat());
            console.log(marker.position.lng());
        }

        function markerDragEnd(event, index) {
            console.log(map);
            console.log(event.latLng.lat());
            console.log(event.latLng.lng());
        }
    </script>

    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>

</body>

</html>
