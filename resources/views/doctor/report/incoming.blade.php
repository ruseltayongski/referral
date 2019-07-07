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
    <div class="row">
        <div class="col-md-4">
            @include('doctor.sidebar.side_incoming')
        </div>
        <div class="col-md-4">
            @include('sidebar.quick')
        </div>
        <div class="col-md-4">
            @include('sidebar.quick2')
        </div>
    </div>

    <div class=" col-md-13">
        <div class="box box-success">
            <div class="box-header with-border">
                <h1>{{ $title }}
                    <small class="pull-right text-success">
                        {{ date('F d, Y',strtotime($start ))}} - {{ date('F d, Y',strtotime($end ))}}
                    </small>
                </h1>
            </div>
            <div class="box-body">
                @if(count($data)>0)
                    <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered" width="100px">
                                    <tr class="bg-black">
                                        <th class="text-center">Date and Time Referred</th>
                                        <th class="text-center">Date and Time Arrived</th>
                                        <th class="text-center">Name of Patient</th>
                                        <th class="text-center">Age / Sex</th>
                                        <th class="text-center">Complete Address</th>
                                        <th class="text-center">Referred From</th>
                                        <th class="text-center">Diagnosis and Impression</th>
                                        <th class="text-center">Referring MD / HCW (Contact No.)</th>
                                        <th class="text-center">Reason for Referral</th>
                                        <th class="text-center">Referred to MD / HCW (Contact No.)</th>
                                        <th class="text-center">Method of Transportation</th>
                                        <th class="text-center">ReCo<br>"Call Done"</th>
                                        <th class="text-center">Acknowledgements<br>Receipt<br>Returned</th>
                                        <th class="text-center">Remarks</th>
                                    </tr>
                                    {{--<tr class="bg-black">--}}
                                        {{--<th class="text-center">Arrived</th>--}}
                                        {{--<th class="text-center">Admitted</th>--}}
                                        {{--<th class="text-center">Discharged</th>--}}
                                        {{--<th class="text-center">Transferred</th>--}}
                                        {{--<th class="text-center">Cancelled</th>--}}
                                    {{--</tr>--}}
                                    @foreach($data as $row)
                            <?php
                                $arrived = \App\Http\Controllers\doctor\ReportCtrl::checkStatus($row->date_referred,'arrived',$row->code);
                                $admitted = \App\Http\Controllers\doctor\ReportCtrl::checkStatus($row->date_referred,'admitted',$row->code);
                                $discharged = \App\Http\Controllers\doctor\ReportCtrl::checkStatus($row->date_referred,'discharged',$row->code);
                                $transferred = \App\Http\Controllers\doctor\ReportCtrl::checkStatus($row->date_referred,'transferred',$row->code);
                                $cancelled = \App\Http\Controllers\doctor\ReportCtrl::checkStatus($row->date_referred,'cancelled',$row->code);
                            ?>
                            <tr>
                                <td class="text-warning text-center">{{ $row->date_referred }}</td>
                                <td class="text-center text-success">
                                    @if($arrived)
                                        <small>{{ date('m/d/y H:i',strtotime($arrived)) }}</small>
                                    @endif
                                </td>
                                <td class="text-success" title="{{ $row->facility }}">
                                    {{ substr($row->facility,0,22) }}
                                    @if(strlen($row->facility) > 21)...@endif
                                </td>
                                <td class="text-center text-success">
                                    <?php $age = \App\Http\Controllers\ParamCtrl::getAge($row->date_referred); ?>
                                    <small>{{ $age }}</small>
                                </td>
                                <td class="text-center text-success">
                                    @if($discharged)
                                        <small>{{ date('m/d/y H:i',strtotime($discharged)) }}</small>
                                    @endif
                                </td>
                                <td class="text-center text-success">
                                    @if($transferred)
                                        <small>{{ date('m/d/y H:i',strtotime($transferred)) }}</small>
                                    @endif
                                </td>
                                <td class="text-center text-success">
                                    @if($cancelled)
                                        <small>{{ date('m/d/y H:i',strtotime($cancelled)) }}</small>
                                    @endif
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <hr />
                    <div class="text-center">{{ $data->links() }}</div>
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

