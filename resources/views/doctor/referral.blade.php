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
            <h3 class="page-header">Incoming Patients</h3>
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
                            $date = ($row->status=='referred') ? date('M d, Y h:i A',strtotime($row->date_referred)) : date('M d, Y h:i A',strtotime($row->date_seen));
                        ?>

                        @if($row->status == 'referred' || $row->status == 'seen' || $row->status == 'redirected')
                        <?php
                            $department = '"Not specified department"';
                            $check_dept = \App\Department::find($row->department_id);
                            if($check_dept)
                            {
                                $department = $check_dept->description;
                            }
                            $seen = \App\Seen::where('tracking_id',$row->id)->count();
                        ?>
                        <li>
                            <i class="fa fa-ambulance bg-blue-active"></i>
                            <div class="timeline-item {{ $type }}" id="item-{{ $row->id }}">
                                <span class="time"><i class="icon fa {{ $icon }}"></i> <span class="date_activity">{{ $date }}</span></span>
                                <h3 class="timeline-header no-border"><a href="#" class="patient_name">{{ $row->patient_name }}</a> <small class="status">[ {{ $row->sex }}, {{ $row->age }} ]</small> was referred to <span class="text-danger">{{ $department }}</span> by <span class="text-warning">Dr. {{ $row->referring_md }}</span> of <span class="facility">{{ $row->facility_name }}</span></h3>
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
                                        <div class="form-group">
                                            <a class="btn btn-default btn-xs col-xs-12"><i class="fa fa-user"></i> Patient No.: {{ $row->code }}</a>
                                        </div>

                                        @if($seen > 0)
                                            <div class="form-group">
                                                <a href="#seenModal" data-toggle="modal"
                                                   data-id="{{ $row->id }}"
                                                   class="btn btn-success btn-xs btn-seen col-xs-12"><i class="fa fa-user-md"></i> Seen by {{ $seen }} User{{ ($seen>1) ? 's':'' }}</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>
                        @elseif($row->status=='accepted')
                        <li>
                            <i class="fa fa-user-plus bg-olive"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-calendar"></i> {{ $date }}</span>
                                <h3 class="timeline-header no-border"><a href="#">{{ $row->patient_name }}</a> was ACCEPTED by <span class="text-success">Dr. {{ $row->action_md }}</span></h3>

                            </div>

                        </li>
                        @elseif($row->status=='rejected')
                        <li>
                            <i class="fa fa-user-times bg-maroon"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-calendar"></i> {{ $date }}</span>
                                <h3 class="timeline-header no-border"><a href="#">{{ $row->patient_name }}</a> RECOMMENDED TO REDIRECT to other facility by <span class="text-danger">Dr. {{ $row->action_md }}</span></h3>

                            </div>

                        </li>
                        @endif
                        @endforeach

                        {{--<li>--}}
                            {{--<i class="fa fa-ambulance bg-blue-active"></i>--}}
                            {{--<div class="timeline-item pregnant-section">--}}
                                {{--<span class="time"><i class="fa fa-eye"></i> March 7, 2018 9:34 AM</span>--}}
                                {{--<h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a> <small class="status">[ Female, 26 ]</small> is referred to your hospital from <span class="facility">Cebu Health Unit</span></h3>--}}
                                {{--<div class="timeline-footer">--}}
                                    {{--<a class="btn btn-warning btn-xs" href="#pregnantFormModal" data-toggle="modal" data-backdrop="static"><i class="fa fa-folder"></i> View Form</a>--}}
                                    {{--<a class="btn btn-default btn-xs"><i class="fa fa-user"></i> Patient No.: 180401-001-160446</a>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</li>--}}
                        {{--<li>--}}
                            {{--<i class="fa fa-ambulance bg-blue-active"></i>--}}
                            {{--<div class="timeline-item read-section">--}}
                                {{--<span class="time"><i class="fa fa-eye"></i> March 7, 2018 9:34 AM</span>--}}
                                {{--<h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a> <small class="status">[ Female, 26 ]</small> is referred to your hospital from <span class="facility">Cebu Health Unit</span></h3>--}}
                                {{--<div class="timeline-footer">--}}
                                    {{--<a class="btn btn-warning btn-xs" href="#pregnantFormModal" data-toggle="modal" data-backdrop="static"><i class="fa fa-folder"></i> View Form</a>--}}
                                    {{--<a class="btn btn-default btn-xs"><i class="fa fa-user"></i> Patient No.: 180401-001-160446</a>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</li>--}}


                        <!-- END timeline item -->
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
@endsection
@section('js')
@include('script.referral')
@endsection

