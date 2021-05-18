<?php
$error = \Illuminate\Support\Facades\Input::get('error');
header('Access-Control-Allow-Origin: *');
?>
@extends('layouts.app')

@section('content')
    <div class="jim-content">
        <iframe style="width: 100%;height: 700px;" src="https://dohph.maps.arcgis.com/apps/webappviewer/index.html?id=cf43a3468a4f4ee99ef128d89ea8a150&fbclid=IwAR0MHBpMuSjr0-ToS6oDHYI0c_gdhftfK3MTwioeIoLJPV5HPZaCexyqmw4"></iframe>
    </div>
    <div class="row">
        <div class="jim-content">
            <iframe style="width: 100%;height: 500px;" src="https://dohph.maps.arcgis.com/apps/webappviewer/index.html?id=cf43a3468a4f4ee99ef128d89ea8a150&fbclid=IwAR0MHBpMuSjr0-ToS6oDHYI0c_gdhftfK3MTwioeIoLJPV5HPZaCexyqmw4"></iframe>
            <form action="{{ asset('vaccine/vaccineview').'/'.$province_id }}" method="GET">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-3">
                        <select name="typeof_vaccine_filter" id="typeof_vaccine_filter" class="select2">
                            <option value="">Select Type of Vaccine</option>
                            <option value="Sinovac" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'Sinovac')echo 'selected';} ?>>Sinovac</option>
                            <option value="Astrazeneca" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'Astrazeneca')echo 'selected';} ?>>Astrazeneca</option>
                            <option value="Moderna" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'Moderna')echo 'selected';} ?> disabled>Moderna</option>
                            <option value="Pfizer" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'Pfizer')echo 'selected';} ?> disabled>Pfizer</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="muncity_filter" id="muncity_filter" class="select2">
                            <option value="">Select Municipality</option>
                            @foreach($muncity as $row)
                                <option value="{{ $row->id }}" <?php if(isset($muncity_filter)){if($muncity_filter == $row->id)echo 'selected';} ?> >{{ $row->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="date_range" placeholder="Enter date range.." name="date_range" value="{{ date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)) }}">
                    </div>
                    <div class="col-md-3">
                         <span class="input-group-btn">
                            <button type="submit" class="btn btn-success" onclick="loadPage()"><i class="fa fa-filter"></i> Filter</button>
                             <a href="{{ asset('vaccine/export/excel') }}" type="button" class="btn btn-danger"><i class="fa fa-file-excel-o"></i> Export Excel</a>
                            <a href="{{ asset('vaccine/vaccineview').'/'.$province_id }}" type="button" class="btn btn-warning" onclick="loadPage()"><i class="fa fa-eye"></i> View All</a>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-9 jim-content">
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
                            <p style="font-size:13pt" >0</p>
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
                            <p style="font-size:13pt" >0</p>
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

        <div class="col-md-3">
            <div class="row" style="padding-left:6%">
                <div class="jim-content">
                    <div id="chartPercentCoverage" style="height: 335px; width: 100%;"></div>
                    <div style="width: 20%;height:20px;background-color: white;position: absolute;margin-top: -12px;"></div>
                </div>
            </div>

            <div class="row" style="padding-left:6%">
                <div class="jim-content">
                    <div id="chartConsumptionRate" style="height: 335px; width: 100%;"></div>
                    <div style="width: 20%;height:20px;background-color: white;position: absolute;margin-top: -12px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div style="width: 100%">
        <div class="row">
            <div class="col-md-12 jim-content">
                <h3 class="page-header">Last 30 days vaccinated</h3>
                <div id="past_days_1" style="height: 370px; width: 100%;"></div>
                <div style="width: 20%;height:20px;background-color: white;position: absolute;margin-top: -12px;"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 jim-content">
                <div id="past_days_2" style="height: 370px; width: 100%;"></div>
                <div style="width: 20%;height:20px;background-color: white;position: absolute;margin-top: -12px;"></div>
            </div>
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
                legend:{
                    cursor:"pointer",
                    verticalAlign: "top",
                    horizontalAlign: "center",
                    dockInsidePlotArea: false,
                    fontSize: 12
                },
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
                legend:{
                    cursor:"pointer",
                    verticalAlign: "top",
                    horizontalAlign: "center",
                    dockInsidePlotArea: false,
                    fontSize: 12
                },
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


        //line chart 1
        var dataPoints1 = [];
        var dataPoints2 = [];
        $.each(<?php echo json_encode($first_dose_past_1)?>, function( index, value ) {
            dataPoints1.push({
                x: new Date(value.date),
                y: value.value
            });
        });

        $.each(<?php echo json_encode($second_dose_past_1)?>, function( index, value ) {
            dataPoints2.push({
                x: new Date(value.date),
                y: value.value,
            });
        });

        var chart = new CanvasJS.Chart("past_days_1", {
            axisX: {
                valueFormatString: "DD MMM"
            },
            toolTip: {
                shared: true
            },
            legend: {
                cursor:"pointer",
                verticalAlign: "bottom",
                horizontalAlign: "center",
                dockInsidePlotArea: false,
                fontSize: 15,
                itemclick: toogleDataSeries1
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

        function toogleDataSeries1(e){
            if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                e.dataSeries.visible = false;
            } else{
                e.dataSeries.visible = true;
            }
            chart.render();
        }
        //line chart 1

        //line chart 2
        var dataPoints1_2 = [];
        var dataPoints2_2 = [];
        $.each(<?php echo json_encode($first_dose_past_2)?>, function( index, value ) {
            dataPoints1_2.push({
                x: new Date(value.date),
                y: value.value
            });
        });

        $.each(<?php echo json_encode($second_dose_past_2)?>, function( index, value ) {
            dataPoints2_2.push({
                x: new Date(value.date),
                y: value.value,
            });
        });

        var chart_2 = new CanvasJS.Chart("past_days_2", {
            axisX: {
                valueFormatString: "DD MMM"
            },
            toolTip: {
                shared: true
            },
            legend: {
                cursor:"pointer",
                verticalAlign: "bottom",
                horizontalAlign: "center",
                dockInsidePlotArea: false,
                fontSize: 15,
                itemclick: toogleDataSeries2
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
                    dataPoints: dataPoints1_2
                },
                {
                    type:"line",
                    name: "Second Dose",
                    showInLegend: true,
                    lineColor: "#f39c12",
                    color: '#f39c12',
                    yValueFormatString: "#,###",
                    dataPoints: dataPoints2_2
                }
            ]
        });
        chart_2.render();

        function toogleDataSeries2(e){
            if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                e.dataSeries.visible = false;
            } else{
                e.dataSeries.visible = true;
            }
            chart_2.render();
        }



    </script>
@endsection

