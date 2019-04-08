@extends('layouts.app')

@section('content')
    <style>
        label {
            padding: 0px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .table td, .table th{
            vertical-align: middle !important;
        }
    </style>
    <div class="col-md-3">
        @include('mcc.sidebar.incomingfilter')
        @include('mcc.sidebar.links')
    </div>

    <div class="col-md-9">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3>{{ $title }}
                    <small class="pull-right text-success">
                        {{ date('F d, Y',strtotime($start ))}} - {{ date('F d, Y',strtotime($end ))}}
                    </small>
                </h3>
            </div>
            <div class="box-body">
                @if(count($data)>0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <tr class="bg-black">
                                <th class="text-center" rowspan="2">Hospital</th>
                                <th class="text-center" colspan="3">Seen</th>
                                <th class="text-center" rowspan="2">No Action</th>
                                <th class="text-center" rowspan="2">TOTAL</th>
                            </tr>
                            <tr class="bg-black">
                                <th class="text-center">Accepted</th>
                                <th class="text-center">Redirected</th>
                                <th class="text-center">IDLE</th>
                            </tr>
                            @foreach($data as $row)
                            <tr>
                                <td class="text-warning">{{ $row->name }}</td>
                                <td class="text-center text-success">0</td>
                                <td class="text-center text-danger">0</td>
                                <td class="text-center text-muted">0</td>
                                <td class="text-center text-muted">0</td>
                                <td class="text-center text-primary text-bold">{{ \App\Http\Controllers\mcc\ReportCtrl::countIncoming('',$row->id) }}</td>
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
    <link rel="stylesheet" href="{{ url('resources/plugin/daterange/daterangepicker.css') }}" />
@endsection

@section('js')
    <script src="{{ url('resources/plugin/daterange/moment.min.js') }}"></script>
    <script src="{{ url('resources/plugin/daterange/daterangepicker.js') }}"></script>
    <script>
        $('#daterange').daterangepicker({
            "singleDatePicker": false,
            "startDate": "{{ $start }}",
            "endDate": "{{ $end }}"
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
    </script>
@endsection

