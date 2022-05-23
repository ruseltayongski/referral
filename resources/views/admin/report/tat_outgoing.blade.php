@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div style="background-color: white;padding: 10px;">
                <form action="{{ asset('admin/report/tat/outgoing') }}" method="GET" class="form-inline">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <span style="font-size: 12pt;"><i>as of </i></span>
                        <?php $date_range = date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)); ?>
                        <input type="text" class="form-control" name="date_range" value="{{ $date_range }}" id="consolidate_date_range">
                        <button type="submit" class="btn btn-md btn-info"><i class="fa fa-search"></i> Filter</button>
                        <button type="button" class="btn btn-md btn-warning" onClick="window.location.href = '{{ asset('admin/report/tat/outgoing') }}'"><i class="fa fa-search"></i> View All</button>
                    </div>
                </form><br>
            </div>
            <div id="chartContainer" style="height: 600px; width: 100%;"></div>
            <div style="width: 20%;height:20px;background-color: white;position: absolute;margin-top: -12px;"></div>
        </div>
        <div class="col-md-12">
            <div class="jim-content">
                <div class="row" style="margin-top: 3%;">
                    <div class="col-sm-2 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header refer_to_seen">{{ $refer_to_seen }}</h5>
                            <span class="description-text">Refer to Seen</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-2 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{ $seen_to_accept }}</h5>
                            <span class="description-text">Seen to Accepted</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-2 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{ $seen_to_reject }}</h5>
                            <span class="description-text">Seen to Recommend to Redirect</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-2 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{ $accept_to_arrive }}</h5>
                            <span class="description-text">Accepted to Arrived</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-2 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{ $arrive_to_admit }}</h5>
                            <span class="description-text">Arrived to Admitted</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-2 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{ $admit_to_discharge }}</h5>
                            <span class="description-text">Admitted to Discharge</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>
                <div class="row text-danger">
                    <!-- /.col -->
                    <div class="col-sm-2 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{ $redirect_to_seen }}</h5>
                            <span class="description-text">REDIRECTED TO SEEN</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-2 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{ $seen_to_accept_redirect }}</h5>
                            <span class="description-text">SEEN TO ACCEPTED</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-2 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{ $seen_to_reject_redirect}}</h5>
                            <span class="description-text">Seen to Recommend to Redirect</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-2 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{ $accept_to_arrive_redirect }}</h5>
                            <span class="description-text">ACCEPTED TO ARRIVED</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-2 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{ $arrive_to_admit_redirect }}</h5>
                            <span class="description-text">ARRIVED TO ADMITTED</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-2 col-xs-6">
                        <div class="description-block border-right">
                            <h5 class="description-header">{{ $admit_to_discharge_redirect }}</h5>
                            <span class="description-text">ADMITTED TO DISCHARGE</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @include('script.chart')
    <script>
        $('#consolidate_date_range').daterangepicker({
            maxDate: new Date()
        });

        window.onload = function () {

            var referred = [];
            var redirected = [];
            var transferred = [];
            var refer_to_accept = [];
            var redirect_to_accept = [];
            var transfer_to_accept = [];
            $.each(<?php echo json_encode($data_points); ?>,function(index,data){
                referred.push({
                    "label" : data.date,
                    "y" : data.referred
                });
                refer_to_accept.push({
                    y : data.refer_to_accept ? data.refer_to_accept : ''
                });

                redirected.push({
                    "label" : data.date,
                    "y" : data.redirected
                });
                redirect_to_accept.push({
                    y : data.redirect_to_accept ? data.redirect_to_accept : ''
                });

                transferred.push({
                    "label" : data.date,
                    "y" : data.transferred
                });
                transfer_to_accept.push({
                    y : data.transfer_to_accept ? data.transfer_to_accept : ''
                });
            });

            var chart = new CanvasJS.Chart("chartContainer", {
                title:{
                    text: "Turn Around Time - Outgoing"
                },
                axisY: {
                    title: "Number of Referral",
                    lineColor: "#4F81BC",
                    tickColor: "#4F81BC",
                    labelFontColor: "#4F81BC",
                    gridThickness: 0
                },
                axisY2: {
                    title: "Minutes",
                    suffix: "",
                    gridThickness: 0,
                    lineColor: "#C0504E",
                    tickColor: "#C0504E",
                    labelFontColor: "#C0504E"
                },
                data: [
                    {
                        type: "column",
                        color: '#40ffc5',
                        dataPoints: referred
                    },
                    {
                        type: "column",
                        color: '#050ab9',
                        dataPoints: redirected
                    },
                    {
                        type: "column",
                        color: '#98b92c',
                        dataPoints: transferred
                    }
                ]

            });
            chart.render();
            createPareto();

            function createPareto() {
                chart.addTo("data", {
                    type:"line",
                    axisYType: "secondary",
                    yValueFormatString: "0.##\"\"",
                    indexLabel: "{y}",
                    indexLabelFontColor: "#C24642",
                    dataPoints: refer_to_accept
                });
                chart.addTo("data", {
                    type:"line",
                    axisYType: "secondary",
                    yValueFormatString: "0.##\"\"",
                    indexLabel: "{y}",
                    indexLabelFontColor: "#C24642",
                    dataPoints: redirect_to_accept
                });
                chart.addTo("data", {
                    type:"line",
                    axisYType: "secondary",
                    yValueFormatString: "0.##\"\"",
                    indexLabel: "{y}",
                    indexLabelFontColor: "#C24642",
                    dataPoints: transfer_to_accept
                });
            }

        }
    </script>
@endsection

