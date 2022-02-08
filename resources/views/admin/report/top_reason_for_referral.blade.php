<?php
$error = \Illuminate\Support\Facades\Input::get('error');
?>
@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="box box-success">
                <form action="{{ asset('admin/report/top/reason_for_referral') }}" method="GET" class="form-inline">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <span class="text-green" style="font-size: 17pt;">Top Reason for Referral </span>
                        <span style="font-size: 12pt;"><i>as of </i></span>
                        <?php $date_range = date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)); ?>
                        <input type="text" class="form-control" name="date_range" value="{{ $date_range }}" id="consolidate_date_range">
                        <button type="submit" class="btn btn-md btn-info"><i class="fa fa-search"></i> Filter</button>
                        <button type="button" class="btn btn-md btn-warning" onClick="window.location.href = '{{ asset('admin/report/top/reason_for_referral') }}'"><i class="fa fa-search"></i> View All</button>
                    </div>
                </form><br>
                <div id="top_reason_for_referral" style="height: 500px; width: 100%;"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="jim-content">
                <div class="box-body table-responsive no-padding">
                    <table class="table">
                        <tbody>
                        <tr class="bg-success">
                            <th>Reason</th>
                            <th>Count</th>
                        </tr>
                        @foreach($reason_for_referral as $top)
                            <?php
                            $pie_chart_data[] = [
                                "label" => $top->reason,
                                "y" => $top->count
                            ];
                            ?>
                            <tr>
                                <td>{{ $top->reason }}</td>
                                <td>{{ $top->count }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    @include('script.chart')
    <script>
        $('#consolidate_date_range').daterangepicker({
            minDate:new Date("2022-01-13")
        });

        window.onload = function() {

            var options = {
                animationEnabled: true,
                data: [{
                    type: "pie",
                    startAngle: 45,
                    legendText: "{label}",
                    indexLabel: "{label} ({y})",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: <?php echo json_encode($pie_chart_data); ?>
                }]
            };
            $("#top_reason_for_referral").CanvasJSChart(options);

        }
    </script>
@endsection

