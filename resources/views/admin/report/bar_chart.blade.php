@extends('layouts.app')

@section('content')

    <div class="box box-success">
        <div class="box-body no-padding">
            <div class="box-body">
                <div id="chartContainer" style="height: 300px; width: 100%;"></div>
            </div>
        </div>

    </div>

@endsection
@section('css')


@endsection

@section('js')

    <script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>


    <script type="text/javascript">
        window.onload = function () {

            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                title:{
                    text: "E REFERRAL REPORT IN JANUARY to SEPTEMBER of 2019"
                },
                axisY: {
                    title: "Medals"
                },
                legend: {
                    cursor:"pointer",
                    itemclick : toggleDataSeries
                },
                toolTip: {
                    shared: true,
                    content: toolTipFormatter
                },
                data: [{
                    type: "bar",
                    showInLegend: true,
                    name: "Incoming",
                    color: "blue",
                    dataPoints: [


                        { y: 18, label: "Cebu Provincial Hospital (Danao City)" },
                        { y: 17, label: "Eversley Childs Sanitarium" },
                        { y: 9, label: "Cebu Provincial Hospital (Carcar City)" },
                        { y: 8, label: "Saint Anthony Mother And Child Hospital" },
                        { y: 4, label: "Cebu Provincial Hospital - Bogo City" },
                        { y: 3, label: "Cebu Provincial Hospital (Balamban)" },

                        { y: 69, label: "Talisay District Hospital" },
                        { y: 4389, label: "Vicente Sotto Memorial Medical Center" },
                    ]
                },
                    {
                        type: "bar",
                        showInLegend: true,
                        name: "Accepted",
                        color: "red",
                        dataPoints: [

                            { y: 4, label: "Cebu Provincial Hospital (Danao City)" },
                            { y: 11, label: "Eversley Childs Sanitarium" },
                            { y: 8, label: "Cebu Provincial Hospital (Carcar City)" },
                            { y: 0, label: "Saint Anthony Mother And Child Hospital" },
                            { y: 4, label: "Cebu Provincial Hospital - Bogo City" },
                            { y: 0, label: "Cebu Provincial Hospital (Balamban)" },

                            { y: 21, label: "Talisay District Hospital" },
                            { y: 3763, label: "Vicente Sotto Memorial Medical Center" },
                        ]
                    },
                    {
                        type: "bar",
                        showInLegend: true,
                        name: "Outgoing",
                        color: "green",
                        dataPoints: [

                            { y: 1033, label: "Cebu Provincial Hospital (Danao City)" },
                            { y: 489, label: "Eversley Childs Sanitarium" },
                            { y: 719, label: "Cebu Provincial Hospital (Carcar City)" },
                            { y: 128, label: "Saint Anthony Mother And Child Hospital" },
                            { y: 820, label: "Cebu Provincial Hospital - Bogo City" },
                            { y: 581, label: "Cebu Provincial Hospital (Balamban)" },

                            { y: 716, label: "Talisay District Hospital" },
                            { y: 31, label: "Vicente Sotto Memorial Medical Center" },
                        ]
                    }]
            });
            chart.render();

            function toolTipFormatter(e) {
                var str = "";
                var total = 0 ;
                var str3;
                var str2 ;
                for (var i = 0; i < e.entries.length; i++){
                    var str1 = "<span style= \"color:"+e.entries[i].dataSeries.color + "\">" + e.entries[i].dataSeries.name + "</span>: <strong>"+  e.entries[i].dataPoint.y + "</strong> <br/>" ;
                    total = e.entries[i].dataPoint.y + total;
                    str = str.concat(str1);
                }
                str2 = "<strong>" + e.entries[0].dataPoint.label + "</strong> <br/>";
                str3 = "<span style = \"color:Tomato\">Total: </span><strong>" + total + "</strong><br/>";
                return (str2.concat(str)).concat(str3);
            }

            function toggleDataSeries(e) {
                if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                }
                else {
                    e.dataSeries.visible = true;
                }
                chart.render();
            }

        }
    </script>

    <script>
        function ipLookUp () {
            $.ajax('http://ip-api.com/json')
                .then(
                    function success(response) {
                        console.log('User\'s Location Data is ', response);
                        console.log('User\'s Country', response.country);
                        getAddress(response.lat, response.lon)
                    },

                    function fail(data, status) {
                        console.log('Request failed.  Returned status of',
                            status);
                    }
                );
        }

        function getAddress (latitude, longitude) {
            $.ajax('https://maps.googleapis.com/maps/api/geocode/json?' +
                'latlng=' + latitude + ',' + longitude + '&key=' +
                GOOGLE_MAP_KEY)
                .then(
                    function success (response) {
                        console.log('User\'s Address Data is ', response)
                    },
                    function fail (status) {
                        console.log('Request failed.  Returned status of',
                            status)
                    }
                )
        }

        if ("geolocation" in navigator) {
            // check if geolocation is supported/enabled on current browser
            navigator.geolocation.getCurrentPosition(
                function success(position) {
                    // for when getting location is a success
                    console.log('latitude', position.coords.latitude,
                        'longitude', position.coords.longitude);
                    getAddress(position.coords.latitude,
                        position.coords.longitude)
                },
                function error(error_message) {
                    // for when getting location results in an error
                    console.error('An error has occured while retrieving' +
                        'location', error_message)
                    ipLookUp()
                });
        } else {
            // geolocation is not supported
            // get your location some other way
            console.log('geolocation is not enabled on this browser')
            ipLookUp()
        }
    </script>
@endsection

