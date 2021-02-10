<?php
    function convertToHoursMins($time, $format = '%02d:%02d') {
        if ($time < 1) {
            return;
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

    function calculateFromDay($date_start,$minutes){
        $to_time =  explode(' ',$date_start)[0].date(' H:i:s',strtotime($minutes));
        $to_time = strtotime($to_time);
        $from_time = strtotime(explode(' ',$date_start)[0].' 23:59:59');
        $minutes = round(abs($to_time - $from_time) / 60,2);

        return $minutes;
    }

    function offlineTime($date_start,$date_end)
    {
        if($date_end == '0000-00-00 00:00:00')
            $date_end = explode(' ',$date_start)[0].' 23:59:59';

        $to_time = strtotime($date_start);
        $from_time = strtotime($date_end);
        $minutes = round(abs($to_time - $from_time) / 60,2);
        $minutes = date('H:i', mktime(0,$minutes));


        return convertToHoursMins(calculateFromDay($date_start,$minutes));
    }
?>
@extends('layouts.app')

@section('content')
    <style>
        label {
            padding: 0px !important;
        }
    </style>
    <div class="row col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="pull-right">
                    <form action="{{ asset('weekly/report') }}" method="POST" class="form-inline">
                        {{ csrf_field() }}
                        <div class="form-group-sm">
                            <input type="text" class="form-control" name="date_range" value="{{ date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)) }}" placeholder="Filter your date here..." id="consolidate_date_range">
                            <button type="submit" class="btn-sm btn-info btn-flat"><i class="fa fa-search"></i> Filter</button>
                        </div>
                    </form>
                </div>
                <h1>{{ $title }}</h1>
                <strong>Legend:</strong>
                <table>
                    <tr>
                        <td style="font-size: 15pt;" >
                            <i class="fa fa-check text-green"></i>
                        </td>
                        <td>&nbsp;&nbsp;Whole day online</td>
                    </tr>
                    <tr>
                        <td style="font-size: 15pt;">
                            <i class="fa fa-times text-red"></i>
                        </td>
                        <td>&nbsp;&nbsp;Whole day offline</td>
                    </tr>
                </table>
            </div>
            <div class="box-body">
                @if(count($facility) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-fixed-header">
                            <thead class='header'>
                                <tr>
                                    <th style="background-color: rgb(31, 73, 125);color: rgb(255, 192, 50);vertical-align: middle;">Facility Name</th>
                                    @foreach($generate_weeks as $per_day)
                                        <td style="background-color: rgb(31, 73, 125);color: rgb(255, 192, 50);">
                                            {{ date('l', strtotime($per_day->per_day)) }}<br>
                                            <small style="font-size: 7pt;">(<i>{{ date('F d,Y',strtotime($per_day->per_day)) }}</i>)</small>
                                        </td>
                                    @endforeach
                                    <th style="background-color: rgb(31, 73, 125);color: rgb(255, 192, 50);vertical-align: middle;">Online</th>
                                    <th style="background-color: rgb(31, 73, 125);color: rgb(255, 192, 50);vertical-align: middle;">Offline</th>
                                </tr>
                            </thead>
                            <?php
                                $province = [];
                            ?>
                            @foreach($facility as $row)
                                <?php
                                $online_count = 0;
                                $offline_count = 0;
                                ?>
                                @if(!isset($province[$row->province]))
                                    <?php $province[$row->province] = true; ?>
                                    <tr>
                                        <td colspan="8">
                                            <strong class="text-info" style="font-size: 20pt;">{{ $row->province }}</strong>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>
                                        <strong style="font-size: 12pt;">{{ $row->name }}</strong><br>
                                        @if($row->hospital_type == 'birthing_home')
                                            <strong style="color: darkgoldenrod;font-size: 8pt;">Birthing Home</strong>
                                        @elseif($row->hospital_type == 'government')
                                            <strong class="text-green" style="font-size: 8pt;">Government</strong>
                                        @elseif($row->hospital_type == 'private')
                                            <strong class="text-blue" style="font-size: 8pt;">Private</strong>
                                        @elseif($row->hospital_type == 'RHU')
                                            <strong class="text-red" style="font-size: 8pt;">RHU</strong>
                                        @endif
                                    </td>
                                    @foreach($generate_weeks as $per_day)
                                        <?php
                                            $check_online = \DB::connection('mysql')->select("call check_online_facility('$row->facility_id','$per_day->per_day')")[0];
                                            $offline_time = offlineTime($check_online->login,$check_online->logout);
                                        ?>
                                        @if($check_online->check_online)
                                            <?php $online_count++ ?>
                                            <td style="font-size: 17pt;">
                                                <span style="content: '\2713'" class="text-green">&#10003;</span>
                                                <b style="font-size: 7pt" class="text-red">{{ $offline_time }}</b>
                                            </td>
                                        @else
                                            <?php $offline_count++; ?>
                                            <td style="font-size: 17pt;">
                                                <span style="content: '\00d7'" class="text-red">&#215;</span>
                                            </td>
                                        @endif
                                    @endforeach
                                    <td><span style="font-size: 15pt;" class="text-green">{{ $online_count }}</span></td>
                                    <td><span style="font-size: 15pt;" class="text-red">{{ $offline_count }}</span></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <span class="text-warning">
                            <i class="fa fa-warning"></i> No data found!
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


@section('css')

@endsection

@section('js')
    <script>
        $(document).ready(function(){
            $('.table-fixed-header').fixedHeader();
        });
        //Date range picker
        $('#consolidate_date_range').daterangepicker();
    </script>
@endsection

