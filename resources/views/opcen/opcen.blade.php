<?php
$error = \Illuminate\Support\Facades\Input::get('error');
?>
@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <div class="jim-content">
            @if($error)
                <div class="alert alert-danger">
                <span class="text-danger">
                    <i class="fa fa-times"></i> Error swtiching account! Please try again.
                </span>
                </div>
            @endif
            <h3 class="page-header">Monthly Activity
            </h3>
            <div class="chart">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

@endsection

@section('js')
    @include('script.chart')
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

