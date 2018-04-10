@extends('layouts.app')

@section('content')
    <div class="col-md-9">
        <div class="jim-content">
            <h3 class="page-header">Monthly Activity
            </h3>
            <div class="chart">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        @include('sidebar.quick')
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
                    label: 'Accepted',
                    backgroundColor: '#26B99A',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                },
                {
                    label: 'Rejected',
                    backgroundColor: '#03586A',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                }
            ]
        }
    }


    var ctx = document.getElementById('barChart').getContext('2d');
    new Chart(ctx, chartdata);
</script>
@endsection

