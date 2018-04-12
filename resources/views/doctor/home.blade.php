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
    var accepted = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    var rejected = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    var link = "{{ url('doctor/chart') }}";
    $.ajax({
        url: link,
        type: "GET",
        success: function(data){
            console.log(data);
        }
    });
    var chartdata = {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            // labels: month,
            datasets: [
                {
                    label: 'Accepted',
                    backgroundColor: '#26B99A',
                    data: accepted
                },
                {
                    label: 'Rejected',
                    backgroundColor: '#03586A',
                    data: rejected
                }
            ]
        }
    }


    var ctx = document.getElementById('barChart').getContext('2d');
    new Chart(ctx, chartdata);
</script>
@endsection

