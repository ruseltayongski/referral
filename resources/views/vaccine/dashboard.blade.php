<?php
$error = \Illuminate\Support\Facades\Input::get('error');
?>
@extends('layouts.app')

@section('content')
    <div class="col-md-9">
        <div class="jim-content">
            @if($error)
                <div class="alert alert-danger">
                <span class="text-danger">
                    <i class="fa fa-times"></i> Error switching account! Please try again.
                </span>
                </div>
            @endif
            <h3 class="page-header">Dashboard</h3>
            <div class="row" style="padding-left: 1%;padding-right: 1%; ">
                <div class="col-lg-3">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3 style="font-size: 20pt;">Sinovac</h3>
                            <p style="font-size:13pt" >{{ $sinovac_count }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-erlenmeyer-flask-bubbles"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 style="font-size: 20pt;">Astrazeneca</h3>
                            <p style="font-size:13pt" >{{ $astrazeneca_count }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-erlenmeyer-flask-bubbles"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3 style="font-size: 20pt;">Moderna</h3>
                            <p style="font-size:13pt" >{{ $moderna_count }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-erlenmeyer-flask-bubbles"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3 style="font-size: 20pt;">Pfizer</h3>
                            <p style="font-size:13pt" >{{ $pfizer_count }}</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-erlenmeyer-flask-bubbles"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <h3 class="page-header">Monthly Activity</h3>
            <div class="chart">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>


    <div class="col-md-3">
        <div class="panel panel-jim">
            <div id="typeof_vaccine" style="height: 340px; width: 100%;"></div>
        </div>
        <div class="panel panel-jim">
            <div id="transaction_status" style="height: 340px; width: 100%;"></div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="jim-content">
            <h3 class="page-header">Last 15 days call</h3>
            <div id="past_days" style="height: 370px; width: 100%;"></div>
        </div>
    </div>

@endsection

@section('js')

    <script type="text/javascript">
        window.onload = function() {

            var options1 = {
                title: {
                    text: "Type of Vaccine",
                    fontFamily: "Arial"
                },
                legend: {
                    horizontalAlign: "center", // "center" , "right"
                    verticalAlign: "top",  // "top" , "bottom"
                },
                animationEnabled: true,
                data: [{
                    type: "pie",
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabel: "{label} ({y})",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: [
                        { label: "Sinovac", y: "{{ $inquiry }}" },
                        { label: "Astrazeneca", y: "{{ $referral }}" },
                        { label: "Others", y: "{{ $others }}" }
                    ]
                }]
            };
            $("#typeof_vaccine").CanvasJSChart(options1);


            var options = {
                title: {
                    text: "Priority",
                    fontFamily: "Arial"
                },
                legend: {
                    horizontalAlign: "center", // "center" , "right"
                    verticalAlign: "top"  // "top" , "bottom"
                },
                animationEnabled: true,
                data: [{
                    type: "pie",
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabel: "{label} ({y})",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: [
                        { label: "Complete", y: "{{ $transaction_complete }}" },
                        { label: "In Complete", y: "{{ $transaction_incomplete }}" }
                    ]
                }]
            };
            $("#transaction_status").CanvasJSChart(options);

        };

        var chartdata = {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                // labels: month,
                datasets: [
                    {
                        label: 'Sinovac',
                        backgroundColor: '#dd4b39',
                        data: <?php echo json_encode($data["sinovac"]); ?>
                    },
                    {
                        label: 'Astrazeneca',
                        backgroundColor: '#f39c12',
                        data: <?php echo json_encode($data["astrazeneca"]); ?>
                    },
                    {
                        label: 'Moderna',
                        backgroundColor: '#00a65a',
                        data: <?php echo json_encode($data["moderna"]); ?>
                    },
                    {
                        label: 'Pfizer',
                        backgroundColor: '#00c0ef',
                        data: <?php echo json_encode($data["pfizer"]); ?>
                    }
                ]
            }
        };
        var ctx = document.getElementById('barChart').getContext('2d');
        new Chart(ctx, chartdata);


        //line chart
        var dataPoints = [];
        var options_days = {
            animationEnabled: true,
            theme: "light2",
            title:{
                text: ""
            },
            axisX:{
                valueFormatString: "DD MMM"
            },
            axisY: {
                title: "",
                suffix: "",
                minimum: 0
            },
            toolTip:{
                shared:true
            },
            legend:{
                display: false
            },
            data: [{
                type: "line",
                showInLegend: true,
                name: "",
                markerType: "square",
                xValueFormatString: "DD MMM, YYYY",
                color: "#00a65a",
                yValueFormatString: "call: #,##0",
                dataPoints: dataPoints
            }
            ]
        };

        $.each(<?php echo json_encode($past_days)?>, function( index, value ) {
            dataPoints.push({
                x: new Date(value.date),
                y: value.value
            });
        });

        $("#past_days").CanvasJSChart(options_days);


    </script>
@endsection

