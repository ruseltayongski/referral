<?php
    $user = Session::get('auth');
?>
@extends('layouts.app')

@section('content')
    <style>
        .timeline .facility {
            color: #ff8456;
        }
    </style>
    <div class="col-md-3">
        @include('sidebar.filter_referral')
        @include('sidebar.quick')
    </div>
    <div class="col-md-9">
        <div class="jim-content">
            <h3 class="page-header">Incoming Patients </h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- The time line -->

                    @if(count($data) > 0)
                    <ul class="timeline">
                        <!-- timeline time label -->

                        <!-- /.timeline-label -->
                        <!-- timeline item -->
                        @foreach($data as $row)
                        <?php
                            $type = ($row->type=='normal') ? 'normal-section':'pregnant-section';
                            $type = ($row->status=='referred' || $row->status=='redirected') ? $type : 'read-section';
                            $icon = ($row->status=='referred' || $row->status=='redirected') ? 'fa-ambulance' : 'fa-eye';
                            $modal = ($row->type=='normal') ? '#normalFormModal' : '#pregnantFormModal';
                            //$date = ($row->status=='referred') ? date('M d, Y h:i A',strtotime($row->date_referred)) : date('M d, Y h:i A',strtotime($row->date_seen));
                            $date = date('M d, Y h:i A',strtotime($row->date_referred));
                            $step = \App\Http\Controllers\doctor\ReferralCtrl::step($row->code);
                            $feedback = \App\Feedback::where('code',$row->code)->count();
                        ?>

                        <?php
                            $department = '"Not specified department"';
                            $check_dept = \App\Department::find($row->department_id);
                            if($check_dept)
                            {
                                $department = $check_dept->description;
                            }
                            $seen = \App\Seen::where('tracking_id',$row->id)->count();
                            $caller_md = \App\Activity::where('code',$row->code)->where("status","=","calling")->count();
                        ?>

                        @if($row->status == 'referred' || $row->status == 'seen' || $row->status == 'redirected')

                        <li>
                            <i class="fa fa-ambulance bg-blue-active"></i>
                            <div class="timeline-item {{ $type }}" id="item-{{ $row->id }}">
                                <span class="time"><i class="icon fa {{ $icon }}"></i> <span class="date_activity">{{ $date }}</span></span>
                                <h3 class="timeline-header no-border">
                                    <small class="text-bold">{{ $row->code }}</small> <a href="#" class="patient_name">{{ $row->patient_name }}</a> <small class="status">[ {{ $row->sex }}, {{ $row->age }} ]</small> was referred to <span class="text-danger">{{ $department }}</span> by <span class="text-warning">Dr. {{ $row->referring_md }}</span> of <span class="facility">{{ $row->facility_name }}</span>
                                </h3>
                                <div class="timeline-footer">
                                    <div class="form-inline">
                                        @if($user->department_id==$row->department_id || $row->department_id==0)
                                        <div class="form-group">
                                            <a class="btn btn-warning btn-xs btn-refer" href="{{ $modal }}"
                                               data-toggle="modal"
                                               data-code="{{ $row->code }}"
                                               data-item="#item-{{ $row->id }}"
                                               data-status="{{ $row->status }}"
                                               data-type="{{ $row->type }}"
                                               data-id="{{ $row->id }}"
                                               data-referred_from="{{ $row->referred_from }}"
                                               data-backdrop="static">
                                                <i class="fa fa-folder"></i> View Form
                                            </a>
                                        </div>
                                        @endif

                                        @if($seen > 0)
                                            <div class="form-group">
                                                <a href="#seenModal" data-toggle="modal"
                                                   data-id="{{ $row->id }}"
                                                   class="btn btn-success btn-xs btn-seen col-xs-12"><i class="fa fa-user-md"></i> Seen
                                                    @if($seen>0)
                                                        <small class="badge bg-green-active">{{ $seen }}</small>
                                                    @endif
                                                </a>
                                            </div>
                                        @endif
                                        @if($caller_md > 0)
                                            <div class="form-group">
                                                <a href="#callerModal" data-toggle="modal"
                                                   data-id="{{ $row->id }}"
                                                   class="btn btn-primary btn-xs btn-caller col-xs-12"><i class="fa fa-phone"></i> Caller
                                                    @if($caller_md>0)
                                                        <small class="badge bg-blue-active">{{ $caller_md }}</small>
                                                    @endif
                                                </a>
                                            </div>
                                        @endif
                                        @if($step>=2 && $step<=4)
                                            <button class="btn btn-xs btn-info btn-feedback" data-toggle="modal"
                                                    data-target="#feedbackModal"
                                                    data-code="{{ $row->code }}">
                                                <i class="fa fa-comments"></i> ReCo
                                                @if($feedback>0)
                                                    <span class="badge bg-blue">{{ $feedback }}</span>
                                                @endif
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>
                        @elseif($row->status!='rejected' || $row->status!='redirected')
                        <li>
                            <i class="fa fa-user-plus bg-olive"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-calendar"></i> {{ $date }}</span>
                                <h3 class="timeline-header no-border"><small class="text-bold">{{ $row->code }}</small> <a href="#">{{ $row->patient_name }}</a> was {{ $row->status == 'rejected' ? 'redirected' : $row->status }} by <span class="text-success">Dr. {{ $row->action_md }}</span>
                                    @if($step<=4)
                                        <div class="form-inline">
                                            @if($seen > 0)
                                                <div class="form-group">
                                                    <a href="#seenModal" data-toggle="modal"
                                                       data-id="{{ $row->id }}"
                                                       class="btn btn-success btn-xs btn-seen"><i class="fa fa-user-md"></i> Seen
                                                        @if($seen>0)
                                                            <small class="badge bg-green-active">{{ $seen }}</small>
                                                        @endif
                                                    </a>
                                                </div>
                                            @endif
                                            @if($caller_md > 0)
                                                <div class="form-group">
                                                    <a href="#callerModal" data-toggle="modal"
                                                       data-id="{{ $row->id }}"
                                                       class="btn btn-primary btn-xs btn-caller col-xs-12"><i class="fa fa-phone"></i> Caller
                                                        @if($caller_md>0)
                                                            <small class="badge bg-blue-active">{{ $caller_md }}</small>
                                                        @endif
                                                    </a>
                                                </div>
                                            @endif
                                            <button class="btn btn-xs btn-info btn-feedback" data-toggle="modal"
                                                    data-target="#feedbackModal"
                                                    data-code="{{ $row->code }}">
                                                <i class="fa fa-comments"></i>
                                                ReCo
                                                @if($feedback>0)
                                                    <span class="badge bg-blue">{{ $feedback }}</span>
                                                @endif
                                            </button>

                                        </div>
                                    @endif
                                </h3>

                            </div>

                        </li>
                        @elseif($row->status=='rejected')
                        <li>
                            <i class="fa fa-user-times bg-maroon"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-calendar"></i> {{ $date }}</span>
                                <h3 class="timeline-header no-border"><small class="text-bold">{{ $row->code }}</small> <a href="#">{{ $row->patient_name }}</a> RECOMMENDED TO REDIRECT to other facility by <span class="text-danger">Dr. {{ $row->action_md }}</span></h3>

                            </div>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                    <div class="text-center">
                        {{ $data->links() }}
                    </div>
                    @else
                        <div class="alert-section">
                            <div class="alert alert-warning">
                                <span class="text-warning">
                                    <i class="fa fa-warning"></i> No referrals!
                                </span>
                            </div>
                        </div>

                        <ul class="timeline">
                        </ul>
                    @endif
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>

    </div>
    @include('modal.accept_reject')
    @include('modal.reject')
    @include('modal.refer')
    @include('modal.accept')
    @include('modal.contact')
    @include('modal.seen')
    @include('modal.caller')
    @include('modal.feedback')
@endsection
@section('js')
@include('script.referral')
@include('script.feedback')
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

@section('css')
    <link rel="stylesheet" href="{{ url('resources/plugin/daterange/daterangepicker.css') }}" />
@endsection

