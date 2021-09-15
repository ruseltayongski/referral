@extends('layouts.app')

@section('content')
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h1>Statistic Reports</h1><br>
                <form action="{{ asset('admin/statistics/incoming') }}" method="POST" class="form-inline">
                    {{ csrf_field() }}
                    <div class="form-group-lg">
                        <select name="request_type" class="form-control" id="" required>
                            <option value="">Select request type</option>
                            <option value="outgoing" <?php if($request_type == "outgoing") ?>>Outgoing</option>
                            <option value="incoming" <?php if($request_type == "incoming") ?>>Incoming</option>
                        </select>
                        <input type="text" class="form-control" name="date_range" value="{{ date("m/d/Y",strtotime($date_range_start)).' - '.date("m/d/Y",strtotime($date_range_end)) }}" placeholder="Filter your daterange here..." id="consolidate_date_range">
                        <button type="submit" class="btn-sm btn-info btn-flat"><i class="fa fa-search"></i> Filter</button>
                    </div>
                </form>
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
                                <th>Cancelled</th>
                                <th>Seen Only</th>
                                <th>Not Seen</th>
                            </tr>
                            </thead>
                            <tr>
                                <td></td>
                                <td colspan="5">
                                    <strong class="text-green">{{ $data[0]['province'] }} Province</strong>
                                </td>
                            </tr>
                            @foreach($data as $row)
                                <tr class="">
                                    <td>{{ $count }}</td>
                                    <td >
                                        <span style="font-size: 12pt;">
                                            {{ $row['facility_name'] }}
                                        </span><br>
                                        <small class="@if($row['hospital_type'] == 'government'){{ 'text-yellow' }}@else{{ 'text-maroon' }}@endif">{{ ucfirst($row['hospital_type']) }}</small>
                                    </td>
                                    <td width="10%">
                                        <span class="text-blue" style="font-size: 15pt">{{ $row['data']['referred'] }}</span><br><br>
                                    </td>
                                    <td>
                                        <span class="text-blue" style="font-size: 15pt;">
                                            {{ $row['data']['redirected'] }}
                                        </span><br><br>
                                    </td>
                                    <td>
                                        <span class="text-blue" style="font-size: 15pt;">
                                            {{ $row['data']['transferred'] }}
                                        </span><br><br>
                                    </td>
                                    <td width="10%">
                                        <?php
                                        $accept_percent = $row['data']['accepted'] / ($row['data']['referred'] + $row['data']['redirected']['transferred'] ) * 100;
                                        ?>
                                        <span class="text-blue">{{ $row['data']['accepted'] }}</span><br>
                                        <b style="font-size: 15pt" class="<?php if($accept_percent >= 50) echo 'text-green'; else echo 'text-red'; ?>">({{ round($accept_percent)."%" }})</b>
                                    </td>
                                    <td width="10%">
                                        <span class="text-blue" style="font-size: 15pt;">{{ $row['data']['denied'] }}</span>
                                        <br><br>
                                    </td>
                                    <td>
                                        <span class="text-blue" style="font-size: 15pt;">
                                            {{ $row['data']['cancelled'] }}
                                        </span><br><br>
                                    </td>
                                    <td width="10%">
                                        <span class="text-blue" style="font-size: 15pt;">{{ $row['data']['seen_only'] }}</span>
                                        <br><br>
                                    </td>
                                    <td width="10%">
                                        <span class="text-blue" style="font-size: 15pt;">{{ $row['data']['not_seen'] }}</span>
                                        <br><br>
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

@endsection
@section('css')
@endsection

@section('js')
    <script>
        //Date range picker
        $('#consolidate_date_range').daterangepicker();
        $(document).ready(function(){
            $('.table-fixed-header').fixedHeader();
        });
    </script>
@endsection

