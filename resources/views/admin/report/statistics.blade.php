@extends('layouts.app')
<style>
    span{
        cursor: pointer;
    }
</style>
@section('content')
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="row">
                <div class="col-md-4">
                    <div class="box-header with-border">
                        <h1>Statistic Reports</h1><br>
                        <form action="{{ asset('admin/statistics').'/'.$province }}" method="GET" class="form-inline">
                            {{ csrf_field() }}
                            <div class="form-group-lg">
                                <select name="request_type" class="form-control" id="" required>
                                    <option value="">Select request type</option>
                                    <option value="outgoing" <?php if($request_type == "outgoing") echo 'selected'; ?>>Outgoing</option>
                                    <option value="incoming" <?php if($request_type == "incoming") echo 'selected'; ?>>Incoming</option>
                                </select>
                                <?php $date_range = date("m/d/Y",strtotime($date_range_start)).' - '.date("m/d/Y",strtotime($date_range_end)); ?>
                                <input type="text" class="form-control" name="date_range" value="{{ $date_range }}" placeholder="Filter your daterange here..." id="consolidate_date_range">
                                <button type="submit" class="btn-sm btn-info btn-flat"><i class="fa fa-search"></i> Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <section class="content" style="height: auto !important; min-height: 0px !important;margin-top: 10px;">
                        <div class="row">
                            <div class="col-lg-4">
                                <!-- small box -->
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <h3 id="statistics_referred">0</h3>
                                        <p>Referred</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-bag"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-4">
                                <!-- small box -->
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <h3 id="statistics_redirected">0</h3>
                                        <p>Redirected</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-stats-bars"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-4">
                                <!-- small box -->
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3 id="statistics_transferred">0</h3>
                                        <p>Transferred</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-person-add"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <!-- ./col -->
                        </div>
                    </section>
                </div>
            </div>
            <div class="box-body">
                @if($request_type)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-fixed-header">
                            <thead class='header'>
                            <tr>
                                <th></th>
                                <th>Facility Name</th>
                                <th>Referred</th>
                                <th>Redirected</th>
                                <th>Transferred</th>
                                <th>Accepted</th>
                                <th>Recommend to Redirect</th>
                                <th>Seen Only</th>
                                <th>Not Seen</th>
                                <!--
                                <th>Requesting a Call</th>
                                <th>Redirected Spam</th>
                                <th class="bg-red">Cancelled</th>
                                -->
                            </tr>
                            </thead>
                            <tr>
                                <td></td>
                                <td colspan="5">
                                    <strong class="text-green">{{ $data[0]['province'] }} Province</strong>
                                </td>
                            </tr>
                            <?php
                                $statistics_referred = 0;
                                $statistics_redirected = 0;
                                $statistics_transferred = 0;
                            ?>
                            @foreach($data as $row)
                                <?php
                                    $left_sum = 0;
                                    $right_sum = 0;
                                ?>
                                <tr class="">
                                    <td>{{ $count }}</td>
                                    <td width="30%;">
                                        <span style="font-size: 12pt;">
                                            {{ $row['facility_name'] }}
                                        </span><br>
                                        <small class="@if($row['hospital_type'] == 'government'){{ 'text-yellow' }}@else{{ 'text-maroon' }}@endif">{{ ucfirst($row['hospital_type']) }}</small>
                                    </td>
                                    <td width="10%">
                                        <span class="text-blue" style="font-size: 15pt" onclick="statisticsData($(this),'{{ $request_type }}','{{ $row['facility_id'] }}','referred','{{ $date_range }}')">
                                            <?php $statistics_referred += $row['data']['referred']; ?>
                                            {{ $row['data']['referred'] }}
                                        </span><br><br>
                                    </td>
                                    <td>
                                        <span class="text-blue" style="font-size: 15pt;" onclick="statisticsData($(this),'{{ $request_type }}','{{ $row['facility_id'] }}','redirected','{{ $date_range }}')">
                                            <?php $statistics_redirected += $row['data']['redirected']; ?>
                                            {{ $row['data']['redirected'] }}
                                        </span><br><br>
                                    </td>
                                    <td>
                                        <span class="text-blue" style="font-size: 15pt;" onclick="statisticsData($(this),'{{ $request_type }}','{{ $row['facility_id'] }}','transferred','{{ $date_range }}')">
                                            <?php $statistics_transferred += $row['data']['transferred']; ?>
                                            {{ $row['data']['transferred'] }}
                                        </span><br><br>
                                    </td>
                                    <td width="10%">
                                        <?php
                                        $accept_percent = $row['data']['accepted'] / ($row['data']['referred'] + $row['data']['redirected'] +$row['data']['transferred'] ) * 100;
                                        ?>
                                        <span class="text-blue" onclick="statisticsData($(this),'{{ $request_type }}','{{ $row['facility_id'] }}','accepted','{{ $date_range }}')">
                                            {{ $row['data']['accepted'] }}
                                        </span><br>
                                        <b style="font-size: 15pt" class="<?php if($accept_percent >= 50) echo 'text-green'; else echo 'text-red'; ?>">({{ round($accept_percent)."%" }})</b>
                                    </td>
                                    <td width="10%">
                                        <span class="text-blue" style="font-size: 15pt;" onclick="statisticsData($(this),'{{ $request_type }}','{{ $row['facility_id'] }}','denied','{{ $date_range }}')">{{ $row['data']['denied'] }}</span>
                                        <br><br>
                                    </td>
                                    <td width="10%">
                                        <span class="text-blue" style="font-size: 15pt;" onclick="statisticsData($(this),'{{ $request_type }}','{{ $row['facility_id'] }}','seen_only','{{ $date_range }}')">{{ $row['data']['seen_only'] }}</span>
                                        <br><br>
                                    </td>
                                    <td width="10%">
                                        <span class="text-blue" style="font-size: 15pt;" onclick="statisticsData($(this),'{{ $request_type }}','{{ $row['facility_id'] }}','not_seen','{{ $date_range }}')">{{ $row['data']['not_seen'] }}</span>
                                        <br><br>
                                    </td>
                                    <!--
                                    <td width="10%">
                                        <span class="text-blue" style="font-size: 15pt;" onclick="statisticsData($(this),'{{ $request_type }}','{{ $row['facility_id'] }}','request_call','{{ $date_range }}')">{{ $row['data']['request_call'] }}</span>
                                        <br><br>
                                    </td>
                                    <td width="10%">
                                        <span class="text-blue" style="font-size: 15pt;" onclick="statisticsData($(this),'{{ $request_type }}','{{ $row['facility_id'] }}','redirected_spam','{{ $date_range }}')">{{ $row['data']['redirected_spam'] }}</span>
                                        <br><br>
                                    </td>
                                    <td width="10%">
                                        <span class="text-red" style="font-size: 15pt;" onclick="statisticsData($(this),'{{ $request_type }}','{{ $row['facility_id'] }}','cancelled','{{ $date_range }}')">{{ $row['data']['cancelled'] }}</span>
                                        <br><br>
                                    </td>
                                    -->
                                </tr>
                                <tr>
                                    <?php
                                        $left_sum += $row['data']['referred'] + $row['data']['redirected'] + $row['data']['transferred'];
                                        $right_sum += $row['data']['accepted'] + $row['data']['denied'] + $row['data']['seen_only'] + $row['data']['not_seen'];
                                    ?>
                                    <td colspan="2">

                                    </td>
                                    <td colspan="3" class="{{ $left_sum > $right_sum ? 'bg-yellow' : '' }}">
                                        <center style="font-size: 20pt;">{{ $left_sum }}</center>
                                    </td>
                                    <td colspan="5" class="{{ $left_sum < $right_sum ? 'bg-yellow' : '' }}">
                                        <center style="font-size: 20pt;">{{ $right_sum }}</center>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <span class="text-warning" style="font-size: 20pt;">
                            <i class="fa fa-warning"></i> Please select a request type in filter
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="statistics-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row">
                        <div class="col-xs-6">
                            <h3 class="modal-title statistics-title"></h3>
                        </div>
                        <div class="col-xs-6">
                            <button type="button" class="close" style="float: right" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-body statistics-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('css')
@endsection

@section('js')
    <script>
        $("#statistics_referred").html("{{ $statistics_referred }}");
        $("#statistics_redirected").html("{{ $statistics_redirected }}");
        $("#statistics_transferred").html("{{ $statistics_transferred }}");

        //Date range picker
        $('#consolidate_date_range').daterangepicker();
        $(document).ready(function(){
            $('.table-fixed-header').fixedHeader();
        });

        function statisticsData(data,request_type,facility_id,status,date_range){
            date_range = date_range.replace(/\//ig, "%2F");
            date_range = date_range.replace(/ /g, "+");
            $(".statistics-title").html(request_type.charAt(0).toUpperCase() + request_type.slice(1)+" Statistics ");
            $("#statistics-modal").modal('show');
            $(".statistics-body").html(loading);
            $("span").css("background-color","");
            data.css("background-color","yellow");
            var url = "<?php echo asset('api/individual'); ?>"+"?request_type="+request_type+"&facility_id="+facility_id+"&status="+status+"&date_range="+date_range;
            //console.log(url);
            $.get(url,function(result){
                setTimeout(function(){
                    $(".statistics-title").append('<span class="badge bg-yellow data_count">'+result.length+'</span>');
                    $(".statistics-body").html(
                        "<table id=\"table\" class='table table-hover table-bordered' style='font-size: 9pt;'>\n" +
                        "    <tr class='bg-success'><th></th><th class='text-green'>Code</th><th class='text-green'>Patient Name</th><th class='text-green'>Age</th><th class='text-green'>Referring Facility</th><th class='text-green'>Referred Facility</th><th class='text-green'>Status</th></tr>\n" +
                        "</table>"
                    );
                    jQuery.each(result, function(index, value) {
                        var track_url = "<?php echo asset('doctor/referred?referredCode='); ?>"+value["code"];
                        var tr = $('<tr />');
                        tr.append("<a href='"+track_url+"' class=\"btn btn-xs btn-success\" target=\"_blank\">\n" +
                            "<i class=\"fa fa-stethoscope\"></i> Track\n" +
                            "</a>");
                        tr.append( $('<td />', { text : value["code"] } ));
                        tr.append( $('<td />', { text : value["patient_name"] } ));
                        tr.append( $('<td />', { text : value["age"] } ));
                        tr.append( $('<td />', { text : value["referring_facility"] } ));
                        tr.append( $('<td />', { text : value["referred_facility"] } ));
                        tr.append( $('<td />', { text : status } ));
                        $("#table").append(tr);
                    });

                },500);
            });
        }
    </script>
@endsection

