<?php
$user = Session::get('auth');
?>

@extends('layouts.app')

@section('content')
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <form action="{{ asset('eoc_city/graph') }}" method="POST" class="form-inline">
                    {{ csrf_field() }}
                    <select name="facility[]" id="" class="select2" multiple="multiple" style="width: 50%">
                        @foreach($facility as $row)
                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" class="form-control" id="date_range" name="date_range" placeholder="Enter date range" required>
                    <button type="submit" class="btn btn-success btn-sm btn-flat">
                        <i class="fa fa-search"></i> Filter
                    </button>
                </form>
            </div>
            <div class="box-body">
                @if(Session::get('graph'))
                <?php Session::put("graph",false); ?>
                <br><div id="chartContainer" style="height: 300px; width: 100%;"></div>
                @endif
            </div>
        </div>
    </div>


    <form method="POST" action="{{ asset('inventory/update/save') }}">
        <div class="modal fade" role="dialog" id="bed_modal">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content inventory_update">
                    <center>
                        <img src="{{ asset('resources/img/loading.gif') }}" alt="">
                    </center>
                </div>
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </form>


@endsection

@section('js')
    <script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>

    <script>
        $('#date_range').daterangepicker();
        $('.select2').select2({
            placeholder: "Select a facility"
        });
    </script>

    <script>
        window.onload = function () {

            var options = {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light2",
                title:{
                    text: "Levels of Care Inventory Graph"
                },
                axisX:{
                    valueFormatString: "DD MMM"
                },
                axisY: {
                    title: "line chart past 7 days",
                    suffix: "",
                    minimum: 10
                },
                toolTip:{
                    shared:true
                },
                legend:{
                    cursor:"pointer",
                    verticalAlign: "bottom",
                    horizontalAlign: "center",
                    dockInsidePlotArea: false,
                    itemclick: toogleDataSeries
                },
                data: [
                    {
                        type: "line",
                        showInLegend: true,
                        name: "Dialysis Machines",
                        markerType: "square",
                        xValueFormatString: "DD MMM, YYYY",
                        color: "#F08080",
                        yValueFormatString: "#,##",
                        dataPoints: [
                            { x: new Date(2020, 5, 1), y: 63 },
                            { x: new Date(2020, 5, 2), y: 69 },
                            { x: new Date(2020, 5, 3), y: 65 },
                            { x: new Date(2020, 5, 4), y: 70 },
                            { x: new Date(2020, 5, 5), y: 71 },
                            { x: new Date(2020, 5, 6), y: 65 },
                            { x: new Date(2020, 5, 7), y: 73 }
                        ]
                    },
                    {
                        type: "line",
                        showInLegend: true,
                        name: "ICU Beds",
                        markerType: "square",
                        color: "#1cb34c",
                        yValueFormatString: "#,##",
                        dataPoints: [
                            { x: new Date(2020, 5, 1), y: 60 },
                            { x: new Date(2020, 5, 2), y: 57 },
                            { x: new Date(2020, 5, 3), y: 51 },
                            { x: new Date(2020, 5, 4), y: 56 },
                            { x: new Date(2020, 5, 5), y: 54 },
                            { x: new Date(2020, 5, 6), y: 55 },
                            { x: new Date(2020, 5, 7), y: 54 }
                        ]
                    },
                    {
                        type: "line",
                        showInLegend: true,
                        name: "Mechanical Ventilators",
                        markerType: "square",
                        color: "#2c10ff",
                        yValueFormatString: "#,##",
                        dataPoints: [
                            { x: new Date(2020, 5, 1), y: 32 },
                            { x: new Date(2020, 5, 2), y: 33 },
                            { x: new Date(2020, 5, 3), y: 25 },
                            { x: new Date(2020, 5, 4), y: 40 },
                            { x: new Date(2020, 5, 5), y: 30 },
                            { x: new Date(2020, 5, 6), y: 28 },
                            { x: new Date(2020, 5, 7), y: 27 }
                        ]
                    },
                    {
                        type: "line",
                        showInLegend: true,
                        name: "Regular Covid Beds",
                        markerType: "square",
                        color: "#f34a0f",
                        yValueFormatString: "#,##",
                        dataPoints: [
                            { x: new Date(2020, 5, 1), y: 25 },
                            { x: new Date(2020, 5, 2), y: 30 },
                            { x: new Date(2020, 5, 3), y: 32 },
                            { x: new Date(2020, 5, 4), y: 36 },
                            { x: new Date(2020, 5, 5), y: 34 },
                            { x: new Date(2020, 5, 6), y: 40 },
                            { x: new Date(2020, 5, 7), y: 38 }
                        ]
                    }
                ]
            };
            $("#chartContainer").CanvasJSChart(options);

            function toogleDataSeries(e){
                if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;
                } else{
                    e.dataSeries.visible = true;
                }
                e.chart.render();
            }

        }
    </script>

@endsection

