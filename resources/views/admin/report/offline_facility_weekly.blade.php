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
                                </tr>
                            </thead>
                            <?php
                            $province = [];
                            ?>
                            @foreach($facility as $row)
                                @if(!isset($province[$row->province]))
                                    <?php $province[$row->province] = true; ?>
                                    <tr>
                                        <td colspan="8"><strong class="text-info" style="font-size: 20pt;">{{ $row->province }}</strong></td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>{{ $row->name }}</strong></td>
                                    @foreach($generate_weeks as $per_day)
                                        <?php
                                            $check_online = \DB::connection('mysql')->select("call check_online_facility('$row->facility_id','$per_day->per_day')")[0]->check_online
                                        ?>
                                        @if($check_online)
                                            <td style="font-size: 15pt;"><i class="fa fa-check text-blue"></i></td>
                                        @else
                                            <td style="font-size: 15pt;"><i class="fa fa-times text-red"></i></td>
                                        @endif
                                    @endforeach
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

