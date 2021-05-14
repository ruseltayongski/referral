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
                            <p style="font-size:13pt"  class="sinovac_dashboard">{{ $sinovac_count }}</p>
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
                            <p style="font-size:13pt" class="astra_dashboard">{{ $astrazeneca_count }}</p>
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
            <div id="chartPercentCoverage" style="height: 335px; width: 100%;"></div>
        </div>
        <div class="panel panel-jim">
            <div id="chartConsumptionRate" style="height: 335px; width: 100%;"></div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="jim-content">
            <h3 class="page-header">Last 15 days vaccinated</h3>
            <div id="past_days" style="height: 370px; width: 100%;"></div>
        </div>
    </div>

@endsection

@section('js')
    @include('script.chart')

    <script type="text/javascript">

        window.onload = function() {

            var percent_coverage_first = <?php echo $percent_coverage_first; ?>;
            var percent_coverage_second = <?php echo $percent_coverage_second ; ?>;
            var options3 = {
                title: {
                    text: "Percentage Coverage",
                    fontSize: 23,
                },
                data: [{
                    type: "doughnut",
                    startAngle: 45,
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabel: "{label} ({y}%)",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: [
                        { label: "First Dose", y: percent_coverage_first, color:"#00a65a" },
                        { label: "Second Dose", y: percent_coverage_second, color:"#f39c12" },

                    ]
                }]
            };
            $("#chartPercentCoverage").CanvasJSChart(options3);

            var consumption_rate_first = <?php echo $consumption_rate_first; ?>;
            var consumption_rate_second = <?php echo $consumption_rate_second ; ?>;
            var options4 = {
                title: {
                    text: "Consumption Rate",
                    fontSize: 23,
                },
                data: [{
                    type: "doughnut",
                    startAngle: 45,
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabel: "{label} ({y}%)",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: [
                        { label: "First Dose", y: consumption_rate_first, color:"#00a65a" },
                        { label: "Second Dose", y: consumption_rate_second, color:"#f39c12" },

                    ]
                }]
            };
            $("#chartConsumptionRate").CanvasJSChart(options4);

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
        var dataPoints1 = [];
        var dataPoints2 = [];
        $.each(<?php echo json_encode($first_dose_past)?>, function( index, value ) {
            dataPoints1.push({
                x: new Date(value.date),
                y: value.value
            });
        });

        $.each(<?php echo json_encode($second_dose_past)?>, function( index, value ) {
            dataPoints2.push({
                x: new Date(value.date),
                y: value.value,
            });
        });

        var chart = new CanvasJS.Chart("past_days", {
            axisX: {
                valueFormatString: "DD MMM"
            },
            toolTip: {
                shared: true
            },
            legend: {
                cursor: "pointer",
                itemclick: toogleDataSeries
            },
            animationEnabled: true,
            data: [
                {
                    type:"line",
                    name: "First Dose",
                    showInLegend: true,
                    lineColor: "#00a65a",
                    color: '#00a65a',
                    yValueFormatString: "#,###",
                    dataPoints: dataPoints1
                },
                {
                    type:"line",
                    name: "Second Dose",
                    showInLegend: true,
                    lineColor: "#f39c12",
                    color: '#f39c12',
                    yValueFormatString: "#,###",
                    dataPoints: dataPoints2
                }
            ]
        });
        chart.render();

        function toogleDataSeries(e){
            if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                e.dataSeries.visible = false;
            } else{
                e.dataSeries.visible = true;
            }
            chart.render();
        }



    </script>
@endsection

