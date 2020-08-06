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
                    <i class="fa fa-times"></i> Error swtiching account! Please try again.
                </span>
                </div>
            @endif
            <h3 class="page-header">Monthly Activity</h3>
            <div class="chart">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-jim">
            <div id="reason_calling" style="height: 300px; width: 100%;"></div>
        </div>
        <div class="panel panel-jim">
            <div id="transaction_status" style="height: 300px; width: 100%;"></div>
        </div>
    </div>

@endsection

@section('js')
    @include('script.chart')

    <script type="text/javascript">
        window.onload = function() {

            var options1 = {
                title: {
                    text: "Reason for calling",
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
                        { label: "Inquiry", y: "{{ $inquiry }}" },
                        { label: "Referral", y: "{{ $referral }}" },
                        { label: "Others", y: "{{ $others }}" }
                    ]
                }]
            };
            $("#reason_calling").CanvasJSChart(options1);


            var options = {
                title: {
                    text: "Status of Transaction",
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

        }
    </script>

    <script>
        var chartdata = {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                // labels: month,
                datasets: [
                    {
                        label: 'New Call',
                        backgroundColor: '#06bdff',
                        data: <?php echo json_encode($data["new_call"]); ?>
                    },
                    {
                        label: 'Repeat Call',
                        backgroundColor: '#ff7c57',
                        data: <?php echo json_encode($data["repeat_call"]); ?>
                    }
                ]
            }
        };


        var ctx = document.getElementById('barChart').getContext('2d');
        new Chart(ctx, chartdata);
    </script>
@endsection

