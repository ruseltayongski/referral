<?php
$user = Session::get('auth');

$dateDailyReferral = \Illuminate\Support\Facades\Session::get('dateDailyReferral');
if(!$dateDailyReferral){
    $dateDailyReferral = date('Y-m-d');
}

$start = \Illuminate\Support\Facades\Session::get('startDateDailyReferral');
$end = \Illuminate\Support\Facades\Session::get('endDateDailyReferral');
if(!$start)
    $start = date('Y-m-d');
if(!$end)
    $end = date('Y-m-d');

?>
@extends('layouts.app')

@section('content')
    <style>
        label {
            padding: 0px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .table td{
            vertical-align: middle;
        }
    </style>
    <div class="col-md-3">
        @include('admin.sidebar.filterDailyReferral')
        @include('admin.sidebar.quick')
    </div>

    <div class="col-md-9">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3>{{ $title }}<br />
                    <small class="text-success">
                        {{ date('F d, Y',strtotime($start))}} to {{ date('F d, Y',strtotime($end))}}
                    </small>
                </h3>
            </div>
            <div class="box-body">
                @if(count($data) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <tr class="bg-black">
                                <th rowspan="2" style="vertical-align: middle;">Name of Hospital</th>
                                <th class="text-center" colspan="5">Number of Referrals To</th>
                                <th class="text-center" colspan="4">Number of Referrals From</th>
                            </tr>
                            <tr class="bg-black">
                                <th class="text-center">Accepted</th>
                                <th class="text-center">Redirected</th>
                                <th class="text-center">Seen</th>
                                <th class="text-center">Unseen</th>
                                <th class="text-center">TOTAL</th>
                                <th class="text-center">Accepted</th>
                                <th class="text-center">Redirected</th>
                                <th class="text-center">Seen</th>
                                <th class="text-center">TOTAL</th>
                            </tr>
                            @foreach($data as $row)
                            <?php
                                $referral = \App\Http\Controllers\admin\DailyCtrl::countOutgoingReferral($row->id);
                                $incoming = \App\Http\Controllers\admin\DailyCtrl::countIncommingReferral($row->id);
                            ?>
                            <tr>
                                <th style="text-align: left;white-space: nowrap;" title="{{ $row->name }}">
                                    @if(strlen($row->name)>25)
                                        {{ substr($row->name,0,25) }}...
                                    @else
                                        {{ $row->name }}
                                    @endif
                                </th>
                                <td class="text-center">{{ $referral['accepted'] }}</td>
                                <td class="text-center">{{ $referral['redirected'] }}</td>
                                <td class="text-center">{{ $referral['seen'] }}</td>
                                <td class="text-center">{{ $referral['unseen'] }}</td>
                                <td class="text-center">{{ $referral['total'] }}</td>

                                <td class="text-center">{{ $incoming['accepted'] }}</td>
                                <td class="text-center">{{ $incoming['redirected'] }}</td>
                                <td class="text-center">{{ $incoming['seen'] }}</td>
                                <td class="text-center">{{ $incoming['total'] }}</td>
                            </tr>
                            @endforeach
                        </table>
                        <div class="text-center">
                            {{ $data->links() }}
                        </div>
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
    <link rel="stylesheet" href="{{ url('resources/plugin/daterange/daterangepicker.css') }}" />
@endsection

@section('js')
    <script src="{{ url('resources/plugin/daterange/moment.min.js') }}"></script>
    <script src="{{ url('resources/plugin/daterange/daterangepicker.js') }}"></script>

    <script>
        <?php
        $start = date('m/d/Y',strtotime($start));
        $end = date('m/d/Y',strtotime($end));
        ?>
        $('#daterange').daterangepicker({
            "startDate": "{{ $start }}",
            "endDate": "{{ $end }}"
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
    </script>
@endsection

