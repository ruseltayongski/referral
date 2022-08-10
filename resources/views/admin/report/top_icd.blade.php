<?php
$error = \Illuminate\Support\Facades\Input::get('error');
?>
@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="jim-content">
                <form action="{{ asset('admin/report/top/icd') }}" method="GET" class="form-inline">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <span class="text-green" style="font-size: 17pt;">Top 10 ICD-10 Diagnosis </span>
                        <span style="font-size: 12pt;"><i>as of </i></span>
                        <?php $date_range = date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)); ?>
                        <input type="text" class="form-control" name="date_range" value="{{ $date_range }}" id="consolidate_date_range">
                        <select name="province_id" id="" class="form-control">
                            <option value="">Select Province</option>
                            @foreach(\App\Province::get() as $prov)
                                <option value="{{ $prov->id }}" <?php if($prov->id == $province_id) echo 'selected'; ?>>{{ $prov->description }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-md btn-info"><i class="fa fa-search"></i> Filter</button>
                        <button type="button" class="btn btn-md btn-warning" onClick="window.location.href = '{{ asset('admin/report/top/icd') }}'"><i class="fa fa-search"></i> View All</button>
                    </div>
                </form><br>
                <div class="box-body table-responsive no-padding">
                    <table class="table">
                        <tbody>
                        <tr class="bg-success">
                            <th>Code</th>
                            <th>Description</th>
                            <th>Count</th>
                        </tr>
                        @foreach($icd as $top)
                        <?php
                            $pie_chart_data[] = [
                                "label" => $top->code,
                                "y" => $top->count
                            ];
                        ?>
                        <tr>
                            <td>{{ $top->code }}</td>
                            <td>{{ $top->description }}</td>
                            <td>{{ $top->count }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="box box-success">
                <div id="top_icd" style="height: 300px; width: 100%;"></div>
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
                title: {
                    text: "Top 10 Icd-10 Diagnosis",
                    fontFamily: "Arial"
                },
                legend: {
                    horizontalAlign: "center", // "center" , "right"
                    verticalAlign: "top"  // "top" , "bottom"
                },
                animationEnabled: true,
                data: [{
                    type: "pie",
                    startAngle: 45,
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabel: "{label} ({y})",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: <?php echo json_encode($pie_chart_data); ?>
                }]
            };
            $("#top_icd").CanvasJSChart(options);

        }
    </script>
@endsection

