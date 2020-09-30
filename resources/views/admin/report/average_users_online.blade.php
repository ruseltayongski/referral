@extends('layouts.app')

@section('content')

    <style>
        label {
            padding: 0px !important;
        }
    </style>
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3>Average User's Login Per Month</h3>
            </div>
            <div class="box-body">
                <div id="chartContainer" style="height: 370px; width: 100%;"></div>
            </div>
        </div>
    </div>

@endsection
@section('css')
@endsection

@section('js')
    <script>
        window.onload = function () {

            var dataPoints = [];

            var options = {
                animationEnabled: true,
                title:{
                    text: ""
                },
                axisY :{
                    valueFormatString: "",
                    prefix: "",
                    suffix: " users",
                    title: "Sales"
                },
                toolTip: {
                    shared: true
                },
                data: [
                    {
                        type: "stackedArea",
                        name: "",
                        xValueFormatString: "MMM YYYY",
                        yValueFormatString: "",
                        dataPoints: [
                            { x: new Date("<?php echo "2020-01-01" ?>"), y: 12 },
                            { x: new Date("<?php echo "2020-02-01" ?>"), y: 34 },
                            { x: new Date("<?php echo "2020-03-01" ?>"), y: 45 },
                            { x: new Date("<?php echo "2020-04-01" ?>"), y: 12 },
                            { x: new Date("<?php echo "2020-05-01" ?>"), y: 23 },
                            { x: new Date("<?php echo "2020-06-01" ?>"), y: 66 },
                            { x: new Date("<?php echo "2020-07-01" ?>"), y: 12 },
                            { x: new Date("<?php echo "2020-08-01" ?>"), y: 45 },
                            { x: new Date("<?php echo "2020-09-01" ?>"), y: 42 },
                            { x: new Date("<?php echo "2020-10-01" ?>"), y: 12 },
                            { x: new Date("<?php echo "2020-11-01" ?>"), y: 22 },
                            { x: new Date("<?php echo "2020-12-01" ?>"), y: 22 }
                        ]
                    }
                ]
            };

            $.each(<?php echo json_encode($data)?>, function( index, value ) {
                console.log(value.date);
                dataPoints.push({
                    x: new Date(value.date),
                    y: value.value
                });
            });


            $("#chartContainer").CanvasJSChart(options);


        }
    </script>
@endsection
