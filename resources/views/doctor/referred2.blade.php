@extends('layouts.app')

@section('content')
    <style>
        .txtTitle { font-size:1.4em; font-weight: bold; color: #ac2020 }
        .txtDoctor { font-weight: bold; color: #1a9cb7;}
        .txtPatient { font-weight: bold; font-size: 1em; color: #22a114 }
        .txtHospital { font-weight: bold; color: #9d7311; }
        .txtInfo { font-weight: bold; color: #437ea9; }
        /*.tracking { background : #efefef; padding:5px;}
        .tracking table td { font-size: 0.9em; padding:10px 15px; vertical-align: top; letter-spacing: 0.5px; }
        .tracking table td:first-child { white-space: nowrap; color: #959595; letter-spacing: normal; width: 100px;  }*/
        .remarks { display: block; color: #df9e38}
        .txtCode { color: #f39c12}
        .txtSub { font-size: 0.7em; font-weight: normal;color: #737373}
    </style>
    <style type="text/css">
        .bs-wizard {margin-top: 20px;}
        /*Form Wizard*/
        .bs-wizard {border-bottom: solid 1px #4caf50; padding: 0 0 10px 0;}
        .bs-wizard > .bs-wizard-step {padding: 0; position: relative;}
        .bs-wizard > .bs-wizard-step + .bs-wizard-step {}
        .bs-wizard > .bs-wizard-step .bs-wizard-stepnum {color: #595959; margin-bottom: 5px;}
        .bs-wizard > .bs-wizard-step .bs-wizard-info {color: #999; font-size: 14px;}
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot {position: absolute; width: 30px; height: 30px; display: block; background: #4caf50; top: 45px; left: 50%; margin-top: -15px; margin-left: -15px; border-radius: 50%;}
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot:after {content: ' '; width: 14px; height: 14px; background: #4caf50; border-radius: 50px; position: absolute; top: 8px; left: 8px; }
        .bs-wizard > .bs-wizard-step > .progress {position: relative; border-radius: 0px; height: 8px; box-shadow: none; margin: 20px 0;}
        .bs-wizard > .bs-wizard-step > .progress > .progress-bar {width:0px; box-shadow: none; background: #4caf50;}
        .bs-wizard > .bs-wizard-step.complete > .progress > .progress-bar {width:100%;}
        .bs-wizard > .bs-wizard-step.active > .progress > .progress-bar {width:50%;}
        .bs-wizard > .bs-wizard-step:first-child.active > .progress > .progress-bar {width:0%;}
        .bs-wizard > .bs-wizard-step:last-child.active > .progress > .progress-bar {width: 100%;}
        .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot {background-color: #f5f5f5;}
        .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot:after {opacity: 0;}
        .bs-wizard > .bs-wizard-step:first-child  > .progress {left: 50%; width: 50%;}
        .bs-wizard > .bs-wizard-step:last-child  > .progress {width: 50%;}
        .bs-wizard > .bs-wizard-step.disabled a.bs-wizard-dot{ pointer-events: none; }
        .bs-wizard-stepnum { color: #4caf50 !important;}
        /*END Form Wizard*/
        @media only screen and (max-width: 440px) {
            .bs-wizard-stepnum {
                font-size: 14px;
                text-transform: lowercase;
            }
            .step-name {
                font-size : 9px;
            }
        }

        .stepper-wrapper {
            font-family: Arial;
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .stepper-item {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .stepper-item::before {
            position: absolute;
            content: "";
            border-bottom: 2px solid #ccc;
            width: 100%;
            top: 20px;
            left: -50%;
            z-index: 2;
        }

        .stepper-item::after {
            position: absolute;
            content: "";
            border-bottom: 2px solid #ccc;
            width: 100%;
            top: 20px;
            left: 50%;
            z-index: 2;
        }

        .stepper-item .step-counter {
            position: relative;
            z-index: 5;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ccc;
            margin-bottom: 6px;
        }

        .stepper-item.active {
            font-weight: bold;
        }

        .stepper-item.completed .step-counter {
            background-color: #4bb543;
        }

        .stepper-item.completed::after {
            position: absolute;
            content: "";
            border-bottom: 2px solid #4bb543;
            width: 100%;
            top: 20px;
            left: 50%;
            z-index: 3;
        }

        .stepper-item:first-child::before {
            content: none;
        }
        .stepper-item:last-child::after {
            content: none;
        }
    </style>

    <div class="row">
        <div class="col-md-3">
            @if($referredCode)
                @include('sidebar.track_referred')
            @else
                @include('sidebar.search_referred')
            @endif
            @include('sidebar.quick')
        </div>
        <div class="col-md-9">
            @if(count($data) > 0)
                @foreach($data as $row)
                    <?php

                    $type = ($row->type=='normal') ? 'success':'danger';
                    $modal = ($row->type=='normal') ? '#normalFormModal' : '#pregnantFormModal';

                    $activities = \App\Activity::select(
                        'activity.*',
                        DB::raw('CONCAT(users.fname," ",users.mname," ",users.lname) as md_name'),
                        DB::raw('CONCAT(u.fname," ",u.mname," ",u.lname) as referring_md'),
                        'users.contact',
                        'fac_rejected.name as fac_rejected',
                        'referring_md as referring_md_id'
                    )
                        ->where('activity.code',$row->code)
                        ->where('activity.id','>=',function($q) use($row,$user){
                            $q->select('id')
                                ->from('activity')
                                ->where('code',$row->code)
                                ->where(function($q){
                                    $q->where('status','referred')
                                        ->orwhere('status','rejected')
                                        ->orwhere('status','transferred');
                                })
                                ->first()
                            ;
                        })
                        ->leftJoin('users','users.id','=','activity.action_md')
                        ->leftJoin('users as u','u.id','=','activity.referring_md')
                        ->leftJoin('facility as fac_rejected','fac_rejected.id','=','users.facility_id')
                        ->orderBy('id','desc')
                        ->get();

                    $department_name = 'N/A';
                    $dept = \App\Department::find($row->department_id);
                    if($dept) {
                        $department_name = $dept->description;
                    }
                    $patient = \App\Patients::find($row->patient_id);
                    if(isset($patient->brgy)) {
                        $patient_address = $patient->region.', '.\App\Province::find($patient->province)->description.', '.\App\Muncity::find($patient->muncity)->description.', '.\App\Barangay::find($patient->brgy)->description;
                    }
                    else {
                        $patient_address = $patient->region.', '.$patient->province_others.', '.$patient->muncity_others.', '.$patient->brgy_others;
                    }

                    $seen = \App\Seen::where('tracking_id',$row->id)->count();
                    $checkForCancellation = \App\Http\Controllers\doctor\ReferralCtrl::checkForCancellation($row->code);

                    $feedback = \App\Feedback::where('code',$row->code)->count();
                    $caller_md = \App\Activity::where('code',$row->code)->where("status","=","calling")->count();
                    $redirected = \App\Activity::where('code',$row->code)->where("status","=","redirected")->count();
                    ?>
                    <div style="border:2px solid #7e7e7e;" class="panel panel-{{ $type }}">
                        <div class="panel-heading">
                            <span class="txtTitle"><i class="fa fa-wheelchair"></i> {{ ucwords(strtolower($row->patient_name)) }} <small class="txtSub">[ {{ $row->sex }}, {{ $row->age }} ] from {{ $patient_address }}. </small></span>
                            <br />
                            @if($row->contact)
                                Patient Contact Number: <strong class="text-primary">{{ $row->contact }}</strong>
                                <br />
                            @endif
                            Referred by:
                            <span class="txtDoctor" href="#">
                            <?php
                                if($row->user_level == 'doctor'){
                                    $referring_md = "Dr. ".$row->referring_md;
                                } else{
                                    $referring_md = $row->referring_md;
                                }
                                ?>
                                {{ $referring_md }}
                        </span>
                            <br />
                            Patient Code: <span class="txtCode">{{ $row->code }}</span>
                        </div>
                        <div class="panel-body">
                            <?php
                                $position = ["1st","2nd","3rd","4th","5th","6th","7th","8th","9th","10th","11th","12th"];
                                $position_count = 0;
                                $referred_track = \App\Activity::where("code",$row->code)->where("status","referred")->first();
                                $referred_seen_track = \App\Seen::where("code",$referred_track->code)
                                    ->where("facility_id",$referred_track->referred_to)
                                    ->where("created_at",">=",$referred_track->created_at)
                                    ->exists();
                                $referred_accepted_track = \App\Activity::where("code",$referred_track->code)
                                    ->where("referred_to",$referred_track->referred_to)
                                    ->where("created_at",">=",$referred_track->created_at)
                                    ->where("status","accepted")
                                    ->exists();
                                $referred_rejected_track = \App\Activity::where("code",$referred_track->code)
                                    ->where("referred_to",$referred_track->referred_to)
                                    ->where("created_at",">=",$referred_track->created_at)
                                    ->where("status","rejected")
                                    ->exists();
                                $referred_cancelled_track = \App\Activity::where("code",$referred_track->code)
                                    ->where("referred_to",$referred_track->referred_to)
                                    ->where("created_at",">=",$referred_track->created_at)
                                    ->where("status","cancelled")
                                    ->exists();
                                $referred_travel_track = \App\Activity::where("code",$referred_track->code)
                                    ->where("referred_to",$referred_track->referred_from)
                                    ->where("created_at",">=",$referred_track->created_at)
                                    ->where("status","travel")
                                    ->exists();
                                $referred_arrived_track = \App\Activity::where("code",$referred_track->code)
                                    ->where("referred_from",$referred_track->referred_to)
                                    ->where("created_at",">=",$referred_track->created_at)
                                    ->where("status","arrived")
                                    ->exists();
                                $referred_admitted_track = \App\Activity::where("code",$referred_track->code)
                                    ->where("referred_from",$referred_track->referred_to)
                                    ->where("created_at",">=",$referred_track->created_at)
                                    ->where("status","admitted")
                                    ->exists();
                                $referred_discharged_track = \App\Activity::where("code",$referred_track->code)
                                    ->where("referred_from",$referred_track->referred_to)
                                    ->where("created_at",">=",$referred_track->created_at)
                                    ->where("status","discharged")
                                    ->exists();
                                $redirected_track = \App\Activity::where("code",$row->code)
                                            ->where(function($query) {
                                                $query->where("status","redirected")
                                                    ->orWhere("status","transferred");
                                            })
                                            ->get();
                            ?>
                            <small class="label bg-blue">{{ $position[$position_count].' position - '.\App\Facility::find($referred_track->referred_to)->name }}</small><br>
                            <div class="stepper-wrapper">
                                <div class="stepper-item completed">
                                    <div class="step-counter">1</div>
                                    <div class="step-name">Referred</div>
                                </div>
                                <div class="stepper-item @if($referred_seen_track || $referred_accepted_track || $referred_rejected_track) completed @endif" id="seen_progress{{ $referred_track->code.$referred_track->id }}">
                                    <div class="step-counter">2</div>
                                    <div class="step-name">Seen</div>
                                </div>
                                <div class="stepper-item @if($referred_accepted_track || $referred_rejected_track) completed @endif">
                                    <div class="step-counter
                                        <?php
                                        if($referred_rejected_track)
                                            echo "bg-red";
                                        elseif($referred_cancelled_track)
                                            echo "bg-yellow";
                                        ?>
                                    ">3</div>
                                    <div class="step-name ">
                                        <?php
                                        if($referred_rejected_track)
                                            echo 'Rejected';
                                        elseif($referred_cancelled_track)
                                            echo 'Cancelled';
                                        else
                                            echo 'Accepted' ;
                                        ?>
                                    </div>
                                </div>
                                <div class="stepper-item @if($referred_travel_track || $referred_arrived_track) completed @endif">
                                    <div class="step-counter">4</div>
                                    <div class="step-name">Departed</div>
                                </div>
                                <div class="stepper-item @if($referred_arrived_track && !$referred_rejected_track) completed @endif">
                                    <div class="step-counter">5</div>
                                    <div class="step-name">Arrived</div>
                                </div>
                                <div class="stepper-item @if(($referred_admitted_track || $referred_discharged_track) && !$referred_rejected_track) completed @endif">
                                    <div class="step-counter">6</div>
                                    <div class="step-name">Admitted</div>
                                </div>
                                <div class="stepper-item @if($referred_discharged_track && !$referred_rejected_track) completed @endif">
                                    <div class="step-counter">7</div>
                                    <div class="step-name">Discharged</div>
                                </div>
                            </div>
                            @if(count($redirected_track) > 0)
                                @foreach($redirected_track as $redirect_track)
                                    <?php
                                        $position_count++;
                                        $redirected_seen_track = \App\Seen::where("code",$redirect_track->code)
                                            ->where("facility_id",$redirect_track->referred_to)
                                            ->where("created_at",">=",$redirect_track->created_at)
                                            ->exists();
                                        $redirected_accepted_track = \App\Activity::where("code",$redirect_track->code)
                                            ->where("referred_to",$redirect_track->referred_to)
                                            ->where("created_at",">=",$redirect_track->created_at)
                                            ->where("status","accepted")
                                            ->exists();
                                        $redirected_rejected_track = \App\Activity::where("code",$redirect_track->code)
                                            ->where("referred_to",$redirect_track->referred_to)
                                            ->where("created_at",">=",$redirect_track->created_at)
                                            ->where("status","rejected")
                                            ->exists();
                                        $redirected_cancelled_track = \App\Activity::where("code",$redirect_track->code)
                                            ->where("referred_to",$redirect_track->referred_to)
                                            ->where("created_at",">=",$redirect_track->created_at)
                                            ->where("status","cancelled")
                                            ->exists();
                                        $redirected_travel_track = \App\Activity::where("code",$redirect_track->code)
                                            ->where("referred_to",$redirect_track->referred_from)
                                            ->where("created_at",">=",$redirect_track->created_at)
                                            ->where("status","travel")
                                            ->exists();
                                        $redirected_arrived_track = \App\Activity::where("code",$redirect_track->code)
                                            ->where("referred_from",$redirect_track->referred_to)
                                            ->where("created_at",">=",$redirect_track->created_at)
                                            ->where("status","arrived")
                                            ->exists();
                                        $redirected_admitted_track = \App\Activity::where("code",$redirect_track->code)
                                            ->where("referred_from",$redirect_track->referred_to)
                                            ->where("created_at",">=",$redirect_track->created_at)
                                            ->where("status","admitted")
                                            ->exists();
                                        $redirected_discharged_track = \App\Activity::where("code",$redirect_track->code)
                                            ->where("referred_from",$redirect_track->referred_to)
                                            ->where("created_at",">=",$redirect_track->created_at)
                                            ->where("status","discharged")
                                            ->exists();
                                    ?>
                                    <small class="label bg-blue">{{ $position[$position_count].' position - '.\App\Facility::find($redirect_track->referred_to)->name }}</small><br>
                                    <div class="stepper-wrapper">
                                        <div class="stepper-item completed">
                                            <div class="step-counter">1</div>
                                            <div class="step-name">{{ ucfirst($redirect_track->status) }}</div>
                                        </div>
                                        <div class="stepper-item @if($redirected_seen_track || $redirected_accepted_track || $redirected_rejected_track) completed @endif" id="seen_progress{{ $redirect_track->code.$redirect_track->id }}">
                                            <div class="step-counter">2</div>
                                            <div class="step-name">Seen</div>
                                        </div>
                                        <div class="stepper-item @if($redirected_accepted_track || $redirected_rejected_track || $redirected_cancelled_track) completed @endif">
                                            <div class="step-counter
                                                <?php
                                            if($redirected_rejected_track)
                                                echo "bg-red";
                                            elseif($redirected_cancelled_track)
                                                echo "bg-yellow";
                                            ?>
                                                    ">3</div>
                                            <div class="step-name "><?php
                                                if($redirected_rejected_track)
                                                    echo 'Rejected';
                                                elseif($redirected_cancelled_track)
                                                    echo 'Cancelled';
                                                else
                                                    echo 'Accepted' ;
                                                ?></div>
                                        </div>
                                        <div class="stepper-item @if($redirected_travel_track || $redirected_arrived_track) completed @endif">
                                            <div class="step-counter">4</div>
                                            <div class="step-name">Departed</div>
                                        </div>
                                        <div class="stepper-item @if($redirected_arrived_track) completed @endif">
                                            <div class="step-counter">5</div>
                                            <div class="step-name">Arrived</div>
                                        </div>
                                        <div class="stepper-item @if($redirected_admitted_track || $redirected_discharged_track) completed @endif">
                                            <div class="step-counter">6</div>
                                            <div class="step-name">Admitted</div>
                                        </div>
                                        <div class="stepper-item @if($redirected_discharged_track) completed @endif">
                                            <div class="step-counter">7</div>
                                            <div class="step-name">Discharged</div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            @if(count($activities) > 0)
                                <?php $first = 0;
                                $latest_act = \App\Activity::where('code',$row->code)->latest('updated_at')->first();
                                ?>
                                <div class="row">
                                    <div class="tracking col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped" style="font-size: 9pt;">
                                                <thead class="prepend_from_firebase{{ $row->code }}"></thead>
                                                @foreach($activities as $act)
                                                    <?php
                                                    $act_name = \App\Patients::find($act->patient_id);
                                                    $old_facility_data = \App\Facility::find($act->referred_from);
                                                    $old_facility = $old_facility_data->name;
                                                    $old_facility_id = $old_facility_data->id;
                                                    if($act->status=='transferred' || $act->status=='redirected'|| $act->status=='referred' || $act->status=='calling'){
                                                        $act_icon = 'fa-ambulance';
                                                    }

                                                    $new_facility = 'N/A';
                                                    $tmp_new = \App\Facility::find($act->referred_to);

                                                    if($act->referred_to==0)
                                                    {
                                                        $tmp_new = \App\Facility::find($act->referred_from);
                                                    }

                                                    if($tmp_new){
                                                        $new_facility = $tmp_new->name;
                                                    }

                                                    ?>
                                                    @if($act->status=='rejected')
                                                        <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                                            <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                                            <td>
                                                                <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $act->fac_rejected }}</span> recommended to redirect <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> to other facility.
                                                                <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                                                <br />
                                                                @if($user->facility_id==$act->referred_from && $latest_act->status=='rejected')
                                                                    <button class="btn btn-success btn-xs btn-redirected" data-toggle="modal" data-target="#redirectedFormModal" data-activity_code="{{ $act->code }}">
                                                                        <i class="fa fa-ambulance"></i> Redirect to other facility<br>
                                                                    </button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @elseif($act->status=='referred' || $act->status=='redirected')
                                                        <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                                            <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                                            <td>
                                                                @if($act->referring_md_id!=0)
                                                                    <?php
                                                                    if($old_facility_id == 63)
                                                                        $referred_md = $act->referring_md;
                                                                    else
                                                                        $referred_md = 'Dr. '.$act->referring_md;
                                                                    ?>
                                                                    <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span>  was {{ $act->status }} by <span class="txtDoctor">{{ $referred_md }}</span> of <span class="txtHospital">{{ $old_facility }}</span> to <span class="txtHospital">{{ $new_facility }}.</span>
                                                                @else
                                                                    <strong>Walk-In Patient:</strong> <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @elseif($act->status=='transferred')
                                                        <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                                            <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                                            <td>
                                                                <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span>  was {{ $act->status }} by <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $old_facility }}</span> to <span class="txtHospital">{{ $new_facility }}.</span>
                                                                <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                                            </td>
                                                        </tr>
                                                    @elseif($act->status=='redirected')
                                                        <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                                            <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                                            <td>
                                                                <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span>  was referred by <span class="txtDoctor">Dr. {{ $act->md_name }}</span> to <span class="txtHospital">{{ $new_facility }}.</span>
                                                            </td>
                                                        </tr>
                                                    @elseif($act->status=='calling')
                                                        <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                                            <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                                            <td>
                                                                <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $new_facility }}</span> is requesting a call from <span class="txtHospital">{{ $old_facility }}</span>.
                                                                @if($user->facility_id==$act->referred_from)
                                                                    Please contact this number <span class="txtInfo">({{ $act->contact }})</span> .
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @elseif($act->status=='accepted')
                                                        <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                                            <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                                            <td>
                                                                <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span>  was accepted by <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $new_facility }}</span>.
                                                                <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                                            </td>
                                                        </tr>
                                                    @elseif($act->status=='arrived')
                                                        <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                                            <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                                            <td>
                                                                <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> arrived at <span class="txtHospital">{{ $new_facility }}</span>.
                                                                <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                                            </td>
                                                        </tr>
                                                    @elseif($act->status=='admitted')
                                                        <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                                            <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                                            <td>
                                                                <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> was admitted at <span class="txtHospital">{{ $new_facility }}</span>.
                                                                <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                                            </td>
                                                        </tr>
                                                    @elseif($act->status=='discharged')
                                                        <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                                            <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                                            <td>
                                                                <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> was discharged from <span class="txtHospital">{{ $new_facility }}</span>.
                                                                <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                                                <?php
                                                                ($row->type=='normal') ? $covid_discharge = \App\PatientForm::where("code",$act->code)->first() : $covid_discharge = \App\PregnantForm::where("code",$act->code)->first();
                                                                ?>
                                                                @if($covid_discharge->dis_clinical_status or $covid_discharge->dis_sur_category)
                                                                    <span class="remarks">Clinical Status: <b>{{ ucfirst($covid_discharge->dis_clinical_status) }}</b></span>
                                                                    <span class="remarks">Surveillance Category: <b>{{ ucfirst($covid_discharge->dis_sur_category) }}</b></span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @elseif($act->status=='archived')
                                                        <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                                            <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                                            <td>
                                                                <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> didn't arrive to <span class="txtHospital">{{ $new_facility }}</span>.
                                                                <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                                            </td>
                                                        </tr>
                                                    @elseif($act->status=='cancelled')
                                                        <?php
                                                        $doctor = \App\User::find($act->action_md);
                                                        ?>
                                                        <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                                            <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                                            <td>
                                                                Referral was cancelled by
                                                                <span class="txtDoctor">
                                                                <?php
                                                                    if($doctor->facility_id == 63)
                                                                        $cancel_doctor = $doctor->fname.' '.$doctor->mname.' '.$doctor->lname;
                                                                    else
                                                                        $cancel_doctor = 'Dr. '.$doctor->fname.' '.$doctor->mname.' '.$doctor->lname;
                                                                    ?>
                                                                    {{ $cancel_doctor }}
                                                            </span>.
                                                                <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                                            </td>
                                                        </tr>
                                                    @elseif($act->status=='travel')
                                                        <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                                            <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                                            <td>
                                                                <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span>  has departed by <span class="txtDoctor">{{ $act->remarks == 5 ? explode('-',$act->remarks)[1] : \App\ModeTransportation::find($act->remarks)->transportation }}</span>.
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    <?php $first = 1; ?>
                                                @endforeach
                                            </table>
                                        </div>
                                        @if(count($activities)>1)
                                            <div style="border-top: 1px solid #ccc;">
                                                <div class="text-center">
                                                    <a href="#toggle" data-id="{{ $row->id }}">View More</a> <small class="text-muted">({{ count($activities) }})</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="panel-footer">
                            <a data-toggle="modal" href="#referralForm"
                               data-type="{{ $row->type }}"
                               data-id="{{ $row->id }}"
                               data-code="{{ $row->code }}"
                               data-referral_status="referring"
                               class="view_form btn btn-warning btn-xs">
                                <i class="fa fa-folder"></i> View Form
                            </a>
                            @if($seen>0)
                                <a href="#seenModal" data-toggle="modal"
                                   data-id="{{ $row->id }}"
                                   class="btn btn-seen btn-xs btn-success"><i class="fa fa-user-md"></i> Seen
                                    @if($seen>0)
                                        <small class="badge bg-green-active">{{ $seen }}</small>
                                    @endif
                                </a>
                            @endif
                            @if($caller_md > 0)
                                <a href="#callerModal" data-toggle="modal"
                                   data-id="{{ $row->id }}"
                                   class="btn btn-primary btn-xs btn-caller"><i class="fa fa-phone"></i> Caller
                                    @if($caller_md>0)
                                        <small class="badge bg-blue-active">{{ $caller_md }}</small>
                                    @endif
                                </a>
                            @endif
                            @if(($referred_accepted_track || $redirected_accepted_track) && !$referred_arrived_track && $row->referred_from == $user->facility_id)
                                <a href="#transferModal" data-toggle="modal"
                                   data-id="{{ $row->id }}" class="btn btn-xs btn-success btn-transfer"><i class="fa fa-ambulance"></i> Depart </a>
                            @endif
                            <button class="btn btn-xs btn-info btn-feedback" data-toggle="modal"
                                    data-target="#feedbackModal"
                                    data-code="{{ $row->code }}"
                                    onclick="viewReco($(this))"
                            >
                                <i class="fa fa-comments"></i> ReCo
                                <span class="badge bg-blue" id="reco_count{{ $row->code }}">{{ $feedback }}</span>
                            </button>
                            <?php $issue_and_concern = \App\Issue::where("tracking_id","=",$row->id)->count(); ?>
                            <button class="btn btn-xs btn-danger btn-issue-referred" data-toggle="modal"
                                    data-target="#IssueAndConcern"
                                    data-code="{{ $row->code }}"
                                    data-referred_from="{{ $row->referred_from }}"
                                    data-tracking_id="{{ $row->id }}">
                                <i class="fa fa fa-exclamation-triangle"></i> Issue and Concern
                                @if($issue_and_concern>0)
                                    <span class="badge bg-red">{{ $issue_and_concern }}</span>
                                @endif
                            </button>
                            <?php $doh_remarks = \App\Monitoring::where("code","=",$row->code)->count(); ?>
                            @if($doh_remarks>0)
                                <button class="btn btn-xs btn-doh" data-toggle="modal" style="background-color: #dd7556;color: white"
                                        data-target="#feedbackDOH"
                                        data-code="{{ $row->code }}">
                                    <i class="fa fa-phone-square"></i> 711 DOH CVCHD HealthLine
                                    <span class="badge bg-green">{{ $doh_remarks }}</span>
                                </button>
                            @endif
                            @if($redirected > 0)
                                <a href="#" data-toggle="modal"
                                   data-id="{{ $row->id }}"
                                   class="btn btn-danger btn-xs btn-caller"><i class="fa fa-chevron-circle-right"></i> Redirected
                                    @if($redirected>0)
                                        <small class="badge bg-red-active">{{ $redirected }}</small>
                                    @endif
                                </a>
                            @endif
                            @if(!$checkForCancellation && !isset($_GET['referredCode']))
                                <a href="#cancelModal" data-toggle="modal"
                                   data-id="{{ $row->id }}" class="btn btn-xs btn-default btn-cancel"><i class="fa fa-times"></i> Cancel</a>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="text-center">
                    {{ $data->links() }}
                </div>
            @else
                <div class="alert alert-warning">
                <span class="text-warning" style="font-weight: bold; font-size: 1.2em;">
                    <i class="fa fa-warning"></i> No Referred Patients!
                </span>
                </div>
            @endif
        </div>
    </div>

    @include('modal.accept')
    @include('modal.refer')
    @include('modal.accept_reject')
    @include('modal.seen')
    @include('modal.caller')
    @include('modal.cancel')
    @include('modal.transfer')
@endsection
@include('script.firebase')

@section('js')
    @include('script.referred')
    <script>
        @if(Session::get('redirected_patient'))
            Lobibox.notify('success', {
                title: "Success",
                msg: "Successfully Redirected Patient!"
            });
            <?php
                Session::put("redirected_patient",false);
            ?>
        @endif

        $('body').on('click','.btn-transfer',function(){
            $(".transportation_body").html(''); //clear data
            var id = $(this).data('id');
            var url = "{{ url('doctor/referred/transfer') }}/"+id;
            var transportation_all = <?php echo \App\ModeTransportation::get(); ?>;
            var select_transportation = "<select class='form-control' onchange='addOthers()' name='mode_transportation' id='mode_transportation' >";
            $.each(transportation_all,function($x,$y){
                select_transportation += "<option value='"+$y.id+"'>"+$y.transportation+"</option>";
            });
            select_transportation += "</select><br>";

            $(".transportation_body").append(select_transportation);
            $("#transferReferralForm").attr('action',url);
        });

        $(document).ready(function(){
            $('.toggle').toggle();

            $("a[href='#toggle']").on('click',function () {
                var id = $(this).data('id');
                $('.toggle'+id).toggle();
                var txt = ($(this).html() =='View More') ? 'View Less': 'View More';
                $(this).html(txt);
            });

            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>

    <script>
        $(".select2").select2({ width: '100%' });

        $('#daterange').daterangepicker({
            "singleDatePicker": false,
            "startDate": "{{ $start }}",
            "endDate": "{{ $end }}"
        });

        function clearFieldsSidebar(){
            <?php
            Session::put('referredKeyword',false);
            Session::put('referredSelect',false);
            Session::put('referred_date',false);
            Session::put('referred_facility',false);
            Session::put('referred_department',false);
            ?>
            refreshPage();
        }
    </script>
@endsection

@section('css')

@endsection

