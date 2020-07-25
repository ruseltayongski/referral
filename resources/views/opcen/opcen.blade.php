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
            <h3 class="page-header">Monthly Activity
            </h3>
            <div class="chart">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-jim">
            <div class="panel-heading">
                <h3 class="panel-title">Statistics</h3>
            </div>
            <div class="panel-body">
                <div class="list-group">
                    <a href="#" class="list-group-item clearfix">
                        Completed
                        <span class="pull-right">
                            <div class="badge">
                                <span class="count_referral">{{ $transaction_complete }}</span>
                            </div>
                        </span>
                    </a>
                    <a href="#" class="list-group-item clearfix">
                        In Complete
                        <span class="pull-right">
                            <div class="badge">
                                <span class="count_referral">{{ $transaction_incomplete }}</span>
                            </div>
                        </span>
                    </a>
                    <a href="#" class="list-group-item clearfix">
                        Inquiry
                        <span class="pull-right">
                            <div class="badge">
                                <span class="count_referral">{{ $inquiry }}</span>
                            </div>
                        </span>
                    </a>
                    <a href="#" class="list-group-item clearfix">
                        Referrals
                        <span class="pull-right">
                            <div class="badge">
                                <span class="count_referral">{{ $referral }}</span>
                            </div>
                        </span>
                    </a>
                    <a href="#" class="list-group-item clearfix">
                        Others
                        <span class="pull-right">
                            <div class="badge">
                                <span class="count_referral">{{ $others }}</span>
                            </div>
                        </span>
                    </a>
                </div>

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

