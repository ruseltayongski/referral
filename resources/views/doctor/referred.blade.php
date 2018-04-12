<?php
$user = Session::get('auth');
?>
@extends('layouts.app')

@section('content')
    <style>
        .timeline .facility {
            color: #ff8456;
        }
        div.timeline-header{
            padding: 10px;
            color: #333;
        }
        .text-remarks {
            padding: 3px 5px;
            background: #c0ff99;
            margin-top: 10px;
        }
    </style>
    <div class="col-md-9">
        <div class="jim-content">
            <h3 class="page-header">Referred Patients
            </h3>
            <div class="row">
                <div class="col-md-12">
                    <!-- The time line -->
                    @if(count($data) > 0)

                        @foreach($data as $row)
                            <?php
                            $type = ($row->type=='normal') ? 'normal-section':'pregnant-section';
                            $type = ($row->status=='referred') ? $type : 'read-section';
                            $icon = ($row->status=='referred') ? 'fa-ambulance' : 'fa-eye';
                            $modal = ($row->type=='normal') ? '#normalFormModal' : '#pregnantFormModal';
                            $date = ($row->status=='referred') ? date('M d, Y h:i A',strtotime($row->date_referred)) : date('M d, Y h:i A',strtotime($row->date_seen));

                            $activities = \App\Activity::select('activity.*',DB::raw('CONCAT(users.fname," ",users.mname," ",users.lname) as md_name'))
                                ->where('activity.code',$row->code)
                                ->where('activity.id','>',function($q) use($row,$user){
                                    $q->select('id')
                                        ->from('activity')
                                        ->where('code',$row->code)
                                        ->where('referred_from',$user->facility_id)
                                        ->where('referred_to','!=',0)
                                        ->first()
                                    ;
                                })
                                ->leftJoin('users','users.id','=','activity.action_md')
                                ->orderBy('id','asc')
                                ->get();
                            $starter = \App\PregnantForm::select('id');
                            if($row->type=='normal'){
                                $starter = \App\PatientForm::select('id');
                            }
                            $starter = $starter->where('code',$row->code)
                                ->where('referring_facility',$user->facility_id)
                                ->first();
                            if($starter){
                                $activities = App\Activity::select('activity.*',DB::raw('CONCAT(users.fname," ",users.mname," ",users.lname) as md_name'))
                                    ->where('activity.code',$row->code)
                                    ->leftJoin('users','users.id','=','activity.action_md')
                                    ->get();
                            }

                            $last_login = \App\Http\Controllers\ParamCtrl::getLastLogin($row->facility_id);
                            ?>
                            <ul class="timeline activity-{{ $row->id }} code-{{ $row->code }}">
                            <li>
                                <i class="fa fa-user bg-blue-active"></i>
                                <div class="timeline-item {{ $type }}" id="item-{{ $row->id }}">
                                    <span class="time"><i class="icon fa {{ $icon }}"></i> <span class="date_activity">{{ $date }}</span></span>
                                    <h3 class="timeline-header no-border">
                                        <a href="#" class="patient_name">{{ $row->patient_name }}</a> <small class="status">[ {{ $row->sex }}, {{ $row->age }} ]</small> was referred to <span class="facility">{{ $row->facility_name }}</span>
                                        @if($last_login && ($row->status=='referred' || $row->status=='seen'))
                                        <br />
                                        <span class="text-warning" style="font-weight: normal; font-size: 0.8em;">Contact : Dr. {{ $last_login->fname }} {{ $last_login->lname }} - {{ $last_login->contact }}</span>
                                        @endif
                                    </h3>
                                    <div class="timeline-footer hide">
                                        <a class="btn btn-warning btn-xs btn-refer" href="{{ $modal }}"
                                           data-toggle="modal"
                                           data-code="{{ $row->code }}"
                                           data-item="#item-{{ $row->id }}"
                                           data-status="{{ $row->status }}"
                                           data-type="{{ $row->type }}"
                                           data-id="{{ $row->id }}"
                                           data-backdrop="static">
                                            <i class="fa fa-folder"></i> View Form
                                        </a>
                                        <a class="btn btn-default btn-xs"><i class="fa fa-user"></i> Patient No.: {{ $row->code }}</a>
                                    </div>
                                </div>
                            </li>
                            @if(count($activities) > 0)
                                @foreach($activities as $act)
                                    <?php
                                        $act_icon = ($act->status=='rejected') ? 'fa-user-times': 'fa-user-plus';
                                        $act_name = \App\Patients::find($act->patient_id);
                                        $old_facility = \App\Facility::find($act->referred_from)->name;
                                        if($act->status=='rejected'){
                                            $new_facility = \App\Facility::find($act->referred_to)->name;
                                        }else if($act->status=='transferred'){
                                            $new_facility = \App\Facility::find($act->referred_to)->name;
                                            $act_icon = 'fa-ambulance';
                                        }
                                    ?>
                                    <li>

                                        @if($act->status=='rejected')
                                            <div class="timeline-item read-section">
                                                <span class="time"><i class="fa {{ $act_icon }}"></i> {{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</span>
                                                <a href="#">
                                                    <div class="timeline-header no-border">
                                                        {{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}  was rejected by <span class="text-danger">Dr. {{ $act->md_name }}</span> of <span class="facility">{{ $old_facility }}</span> and referred to <span class="facility">{{ $new_facility }}.</span>
                                                        <br />
                                                        <div class="text-remarks">Reason: {{ $act->remarks }}</div>
                                                    </div>
                                                </a>

                                            </div>
                                        @elseif($act->status=='transferred')
                                            <div class="timeline-item read-section">
                                                <span class="time"><i class="fa {{ $act_icon }}"></i> {{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</span>
                                                <a href="#">
                                                    <div class="timeline-header no-border">
                                                        {{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}  was referred by <span class="text-success">Dr. {{ $act->md_name }}</span> of <span class="facility">{{ $old_facility }}</span> to <span class="facility">{{ $new_facility }}.</span>
                                                        <br />
                                                        <div class="text-remarks">Remarks: {{ $act->remarks }}</div>
                                                    </div>
                                                </a>

                                            </div>
                                        @elseif($act->status=='called')
                                            <div class="timeline-item read-section">
                                                <span class="time"><i class="fa fa-phone"></i> {{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</span>
                                                <a href="#">
                                                    <div class="timeline-header no-border">
                                                        <span class="text-info">Dr. {{ $act->md_name }}</span> of <span class="facility">{{ $old_facility }}</span> called the referring facility.
                                                    </div>
                                                </a>
                                            </div>
                                        @elseif($act->status=='arrived')
                                            <div class="timeline-item read-section">
                                                <span class="time"><i class="fa fa-wheelchair"></i> {{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</span>
                                                <a href="#">
                                                    <div class="timeline-header no-border">
                                                        {{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }} arrived at <span class="facility">{{ $old_facility }}</span>.
                                                        <br />
                                                        <div class="text-remarks">Remarks: {{ $act->remarks }}</div>
                                                    </div>
                                                </a>
                                            </div>
                                        @elseif($act->status=='admitted')
                                            <div class="timeline-item read-section">
                                                <span class="time"><i class="fa fa-stethoscope"></i> {{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</span>
                                                <a href="#">
                                                    <div class="timeline-header no-border">
                                                        {{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }} admitted at <span class="facility">{{ $old_facility }}</span>.
                                                    </div>
                                                </a>
                                            </div>
                                        @elseif($act->status=='discharged')
                                            <div class="timeline-item read-section">
                                                <span class="time"><i class="fa fa-stethoscope"></i> {{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</span>
                                                <a href="#">
                                                    <div class="timeline-header no-border">
                                                        {{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }} discharged from <span class="facility">{{ $old_facility }}</span>.
                                                        <br />
                                                        <div class="text-remarks">Remarks: {{ $act->remarks }}</div>
                                                    </div>
                                                </a>
                                            </div>
                                        @else
                                            <div class="timeline-item read-section">
                                                <span class="time"><i class="fa {{ $act_icon }}"></i> {{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</span>
                                                <a href="#">
                                                    <div class="timeline-header no-border">
                                                        {{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}  was accepted by <span class="text-success">Dr. {{ $act->md_name }}</span> of <span class="facility">{{ $old_facility }}</span>.</span>
                                                        <br />
                                                        <div class="text-remarks">Remarks: {{ $act->remarks }}</div>
                                                    </div>
                                                </a>

                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            @endif
                            </ul>
                        @endforeach
                        {{--<li>--}}
                            {{--<i class="fa fa-user bg-blue-active"></i>--}}
                            {{--<div class="timeline-item read-section">--}}
                                {{--<span class="time"><i class="fa fa-eye"></i> March 7, 2018 9:34 AM</span>--}}
                                {{--<h3 class="timeline-header no-border"><a href="#">Anna Baclayon</a> <small class="status">[ Female, 26 ]</small> was referred to <span class="facility">Badian District Hospital</span></h3>--}}
                                {{--<div class="timeline-footer">--}}
                                    {{--<a class="btn btn-warning btn-xs" href="#pregnantFormModal" data-toggle="modal" data-backdrop="static"><i class="fa fa-folder"></i> View Form</a>--}}
                                    {{--<a class="btn btn-default btn-xs"><i class="fa fa-user"></i> Patient No.: 180401-001-160446</a>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</li>--}}
                        {{--<li>--}}
                            {{--<div class="timeline-item read-section">--}}
                                {{--<span class="time"><i class="fa fa-user-times"></i> March 7, 2018 9:34 AM</span>--}}
                                {{--<a href="#">--}}
                                {{--<div class="timeline-header no-border">--}}
                                    {{--Anna Baclayon  was rejected by <span class="facility">Badian District Hospital</span> and referred to <span class="facility">Toledo District Hospital.</span>--}}
                                {{--</div>--}}
                                {{--</a>--}}

                            {{--</div>--}}
                        {{--</li>--}}
                        {{--<li>--}}
                            {{--<div class="timeline-item read-section">--}}
                                {{--<span class="time"><i class="fa fa-user-plus"></i> March 7, 2018 9:34 AM</span>--}}
                                {{--<a href="#">--}}
                                {{--<div class="timeline-header no-border">--}}
                                    {{--Anna Baclayon was accepted to <span class="facility">Toledo District Hospital</span></div>--}}
                                {{--</a>--}}
                            {{--</div>--}}
                        {{--</li>--}}
                    @else
                    <div class="alert alert-warning">
                        <span class="text-warning">
                            <i class="fa fa-warning"></i> No Referred Patients!
                        </span>
                    </div>
                    @endif
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div>

    </div>
    <div class="col-md-3">
        @include('sidebar.quick')
    </div>
    @include('modal.accept_reject')
    @include('modal.reject')
    @include('modal.refer')
    @include('modal.accept')
@endsection
@include('script.firebase')
@section('js')
    @include('script.referred')
@endsection

