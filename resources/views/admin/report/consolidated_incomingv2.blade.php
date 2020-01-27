<?php
    use Illuminate\Support\Facades\Session;
?>
@extends('layouts.app')

@section('content')
    <form action="{{ asset('admin/report/consolidated/incomingv2') }}" method="POST">
        {{ csrf_field() }}
        <div class="row" style="margin-top: -0.5%;margin-bottom: 1%">
            <div class="col-md-4">
                <input type="text" class="form-control" name="date_range" value="{{ date("m/d/Y",strtotime($date_range_start)).' - '.date("m/d/Y",strtotime($date_range_end)) }}" placeholder="Filter your daterange here..." id="reservation">
            </div>
            <div class="col-md-6">
                <div class="input-group-btn">
                    <button type="submit" class="btn btn-info btn-flat">Filter</button>
                    <a href="{{ asset('excel/incoming') }}" type="button" name="from_the_start" class="btn btn-warning btn-flat"><i class="fa fa-file-excel-o"></i> Incoming (Excel)</a>
                    <a href="{{ asset('excel/outgoing') }}" type="button" name="from_the_start" class="btn btn-success btn-flat"><i class="fa fa-file-excel-o"></i> Outgoing (Excel)</a>
                    <a href="{{ asset('excel/all') }}" type="button" name="from_the_start" class="btn btn-primary btn-flat"><i class="fa fa-file-excel-o"></i> All (Excel)</a>
                </div>
            </div>
        </div>
    </form>
    <?php
        $date_start = $date_range_start;
        $date_end = $date_range_end;
        $accepted_incoming = [];
        $seenzoned_incoming = [];
        $common_source_incoming = [];
        $referring_doctor_incoming = [];
        $diagnosis_ref_incoming = [];
        $reason_ref_incoming = [];
        $transport_ref_incoming = [];
        $department_ref_incoming = [];
        $issue_ref_incoming = [];
        $turnaround_time_accept_incoming = [];
        $turnaround_time_arrived_incoming = [];

        $total_outgoing1 = [];
        $accepted_outgoing1 = [];
        $seenzoned_outgoing1 = [];
        $common_referred_facility_outgoing1 = [];
        $common_referred_doctor_outgoing1 = [];
        $diagnosis_ref_outgoing1 = [];
        $reason_ref_outgoing1 = [];
        $transport_ref_outgoing1 = [];
        $department_ref_outgoing1 = [];
        $issue_ref_outgoing1 = [];
        $turnaround_time_accept_outgoing1 = [];
        $turnaround_time_arrived_outgoing1 = [];
    ?>
    @if(count($data) > 0)
    @foreach($data as $row)
    <div class="box box-success">
        <div class="box-body no-padding">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#incoming{{ $row->id }}" data-toggle="tab">Incoming</a></li>
                    <li ><a href="#outgoing{{ $row->id }}" data-toggle="tab">Outgoing</a></li>
                    <li class="pull-right"><strong><div class="text-info">{{ date('F',strtotime($date_range_start))." (".date('Y',strtotime($date_range_start)).") ".' to '.date('F',strtotime($date_range_end))." (".date('Y',strtotime($date_range_end)).")" }}</div></strong></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="incoming{{ $row->id }}">
                        <?php
                            $incoming = $row->count_incoming;
                            $facility_id = $row->id;
                            /*$incoming = \DB::connection('mysql')->select("call 0927countIncoming('$facility_id','$date_start','$date_end')")[0]->count_incoming;
                            $accepted = \DB::connection('mysql')->select("call 0927acceptedIncoming('$facility_id','$date_start','$date_end')")[0]->accepted_incoming;
                            $seenzoned = \DB::connection('mysql')->select("call 0927viewedOnlyIncoming('$facility_id','$date_start','$date_end')")[0]->viewed_only;*/
                            $accepted = \App\Tracking::where("tracking.referred_to","=",$row->id)
                                        ->join("activity","activity.code","=","tracking.code")
                                        ->where("activity.status","=","accepted")
                                        ->where("tracking.date_referred",">=",$date_start)
                                        ->where("tracking.date_referred","<=",$date_end)
                                        ->count();

                            if($accepted > $incoming)
                                $accepted = $incoming;

                            $no_respond = $incoming - $accepted;
                            $seenzoned = \DB::connection('mysql')->select("call getViewedOnly('$facility_id','$date_start','$date_end')")[0]->viewed_only;
                            $facility = \App\Tracking::select("facility.name",\DB::raw("count(facility.id) as count"))
                                ->leftJoin("facility","facility.id","=","tracking.referred_from")
                                ->where("tracking.referred_to","=",$row->id)
                                ->where("tracking.date_referred",">=",$date_start)
                                ->where("tracking.date_referred","<=",$date_end)
                                ->groupBy('facility.id')
                                ->orderBy("count","desc")
                                ->get();
                            $referring_doctor = \App\Tracking::select(\DB::raw("concat('DR. ',users.fname,' ',users.mname,' ',users.lname) as fullname"),\DB::raw("count(tracking.referring_md) as count"))
                                ->leftJoin("users","users.id","=","tracking.referring_md")
                                ->where("tracking.referred_to","=",$row->id)
                                ->where("tracking.date_referred",">=",$date_start)
                                ->where("tracking.date_referred","<=",$date_end)
                                ->where(\DB::raw("concat('DR. ',users.fname,' ',users.mname,' ',users.lname)"),"!=","")
                                ->groupBy("tracking.referring_md")
                                ->orderBy("count","desc")
                                ->limit(10)
                                ->get();
                            $diagnosis_ref = \App\PatientForm::select("patient_form.diagnosis",\DB::raw("count(patient_form.diagnosis) as count"))
                                ->where("patient_form.referred_to","=",$row->id)
                                ->where("patient_form.diagnosis","!=","")
                                ->where("patient_form.time_referred",">=",$date_start)
                                ->where("patient_form.time_referred","<=",$date_end)
                                ->groupBy('patient_form.diagnosis')
                                ->orderBy("count","desc")
                                ->limit(10)
                                ->get();
                            $reason_ref = \App\PatientForm::select("patient_form.reason",\DB::raw("count(patient_form.reason) as count"))
                                ->where("patient_form.referred_to","=",$row->id)
                                ->where("patient_form.reason","!=","")
                                ->where("patient_form.time_referred",">=",$date_start)
                                ->where("patient_form.time_referred","<=",$date_end)
                                ->groupBy('patient_form.reason')
                                ->orderBy("count","desc")
                                ->limit(10)
                                ->get();
                            $transport_ref = \App\Tracking::select("mode_transportation.transportation",\DB::raw("count(mode_transportation.id) as count"))
                                ->leftJoin("mode_transportation","mode_transportation.id","=","tracking.mode_transportation")
                                ->where("tracking.referred_to","=",$row->id)
                                ->where("tracking.date_referred",">=",$date_start)
                                ->where("tracking.date_referred","<=",$date_end)
                                ->where("mode_transportation.transportation","!=","")
                                ->groupBy('mode_transportation.id')
                                ->orderBy("count","desc")
                                ->get();
                            $department_ref = \App\Tracking::select("department.description",\DB::raw("count(department.id) as count"))
                                ->leftJoin("department","department.id","=","tracking.department_id")
                                ->where("tracking.referred_to","=",$row->id)
                                ->where("tracking.date_referred",">=",$date_start)
                                ->where("tracking.date_referred","<=",$date_end)
                                ->where("tracking.department_id","!=","")
                                ->groupBy('department.id')
                                ->orderBy("count","desc")
                                ->get();
                            $issue_ref = \App\Tracking::select("issue.issue")
                                ->leftJoin("issue","issue.tracking_id","=","tracking.id")
                                ->where("tracking.referred_to","=",$row->id)
                                ->where("tracking.date_referred",">=",$date_start)
                                ->where("tracking.date_referred","<=",$date_end)
                                ->where("issue.issue","!=","")
                                ->get();

                            $incoming_horizontal = \DB::connection('mysql')->select("call hospitalLevelIncoming('$facility_id','$date_start','$date_end')")[0]->horizontal;
                            $incoming_vertical = $incoming - $incoming_horizontal;

                            $accepted_incoming[$facility_id] = $accepted;
                            $seenzoned_incoming[$facility_id] = $seenzoned;

                            /*$time_accept_incoming = \App\Tracking::select(\DB::raw('TIMESTAMPDIFF(MINUTE,tra.date_referred,tra.date_accepted) as mins'))
                                                    ->where("referred_to",$row->id)*/
                        ?>
                        <div class="row">
                            <div class="col-md-4 ">
                                <div class="box-body box-profile">
                                    @if($row->picture)
                                        <img class="profile-user-img img-responsive img-circle" src="{{ asset('resources/hospital_logo').'/'.$row->picture }}" alt="User profile picture">
                                    @else
                                        <img class="profile-user-img img-responsive img-circle" src="{{ asset('resources/img/doh.png') }}" alt="User profile picture">
                                    @endif

                                    <h3 class="profile-username text-center">{{ $row->name }}</h3>
                                    <strong><i class="fa fa-random margin-r-5"></i> Transaction</strong>
                                    <p >
                                        <?php
                                            echo '<span class="label label-warning">Incoming <span class="badge bg-red" >'.$incoming.'</span></span>';
                                            echo '<span class="label label-warning">Accepted <span class="badge bg-red" >'.$accepted.'</span></span>';
                                            //echo '<span class="label label-warning">Viewed Only <span class="badge bg-red" >'.$seenzoned.'</span></span>';
                                            //echo '<span class="label label-warning">No Respond <span class="badge bg-red" >'.$no_respond.'</span></span><br><br><br>';
                                            echo '<span class="label label-warning">Viewed Only <span class="badge bg-red" >'.$no_respond.'</span></span><br><br><br>';
                                        ?>
                                    </p>

                                    <strong><i class="fa fa-clock-o margin-r-5"></i> Turnaround time</strong>
                                    <p>
                                        <?php
                                            $turnaround_time_accept_incoming[$facility_id] = $row->turnaround_time_accept;
                                            $turnaround_time_arrived_incoming[$facility_id] = $row->turnaround_time_arrived;
                                        ?>
                                        @if($accepted)
                                            <?php $turnaround_time_accept_incoming[$facility_id] .= ' mins'; ?>
                                            <span class="label label-primary">Acceptance <?php echo '<span class="badge bg-red"><i class="fa fa-clock-o"></i> '.$row->turnaround_time_accept.' mins</span>'; ?></span>
                                        @endif
                                        @if($accepted)
                                            <?php $turnaround_time_arrived_incoming[$facility_id] .= ' mins'; ?>
                                            <span class="label label-primary">Arrival <?php echo '<span class="badge bg-red"><i class="fa fa-clock-o"></i> '.$row->turnaround_time_arrived.'mins</span>'; ?></span>
                                        @endif
                                    </p><br><br>

                                    <strong><i class="fa fa-book margin-r-5"></i> Hospital Level</strong>
                                    <p>
                                        <span class="label label-success">Horizontal <?php echo '<span class="badge bg-red">'.$incoming_horizontal.'</span>'; ?></span>
                                        <span class="label label-success">Vertical <?php echo '<span class="badge bg-red">'.$incoming_vertical.'</span>'; ?></span>
                                    </p>

                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#common_sources{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: green"></i> Referring Facility</a></li>
                                        <li><a href="#common_referring{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: blue"></i> Referring Doctors <small>(Top 10)</small></a></li>
                                        <li><a href="#diagnosis_reason{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: yellowgreen"></i> Diagnoses & Reasons <small>(Top 10)</small></a></li>
                                        <li><a href="#transportation{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: red"></i> Common Transportation</a></li>
                                        <li><a href="#department{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: lightskyblue"></i> Department</a></li>
                                        <li><a href="#issue{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: orange"></i> Remarks</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="common_sources{{ $row->id }}">
                                            <?php $common_source_incoming_concat = ''; ?>
                                            @foreach($facility as $fac)
                                                <?php $common_source_incoming_concat .= $fac->name.'-'.$fac->count."\n"; ?>
                                                <label><span class="badge bg-maroon">{{ $fac->count }}</span> {{ $fac->name }}</label><br>
                                            @endforeach
                                            <?php $common_source_incoming[$facility_id] = $common_source_incoming_concat; ?>
                                        </div>
                                        <div class="tab-pane fade" id="common_referring{{ $row->id }}">
                                            <?php $referring_doctor_incoming_concat = ''; ?>
                                            @foreach($referring_doctor as $referring)
                                                    <?php $referring_doctor_incoming_concat .= $referring->fullname.'-'.$referring->count."\n"; ?>
                                                <label><span class="badge bg-maroon">{{ $referring->count }}</span> {{ $referring->fullname }}</label><br>
                                            @endforeach
                                            <?php $referring_doctor_incoming[$facility_id] = $referring_doctor_incoming_concat; ?>
                                        </div>
                                        <div class="tab-pane fade" id="diagnosis_reason{{ $row->id }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h4>Diagnosis</h4>
                                                    <?php $diagnosis_ref_incoming_concat = ''; ?>
                                                    @if(count($diagnosis_ref) >= 1)
                                                        @foreach($diagnosis_ref as $diagnosis)
                                                            <?php $diagnosis_ref_incoming_concat .= $diagnosis->diagnosis.'-'.$diagnosis->count."\n"; ?>
                                                            <label><span class="badge bg-maroon">{{ $diagnosis->count }}</span> {{ $diagnosis->diagnosis }}</label><br>
                                                        @endforeach
                                                    @else
                                                        <h1>Empty Record!</h1>
                                                    @endif
                                                    <?php $diagnosis_ref_incoming[$facility_id] = $diagnosis_ref_incoming_concat; ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <h4>Reasons</h4>
                                                    <?php $reason_ref_incoming_concat = ''; ?>
                                                    @if(count($reason_ref) >= 1)
                                                        @foreach($reason_ref as $reason)
                                                            <?php $reason_ref_incoming_concat .= $reason->reason.'-'.$reason->count."\n"; ?>
                                                            <label><span class="badge bg-maroon">{{ $reason->count }}</span> {{ $reason->reason }}</label><br>
                                                        @endforeach
                                                    @else
                                                        <h1>Empty Record!</h1>
                                                    @endif
                                                    <?php $reason_ref_incoming[$facility_id] = $reason_ref_incoming_concat; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="transportation{{ $row->id }}">
                                            <?php $transport_ref_incoming_concat = ''; ?>
                                            @if(count($transport_ref) >= 1)
                                                @foreach($transport_ref as $transport)
                                                    <?php $transport_ref_incoming_concat .= $transport->transportation.'-'.$transport->count."\n"; ?>
                                                    <label><span class="badge bg-maroon">{{ $transport->count }}</span> {{ $transport->transportation }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                            <?php $transport_ref_incoming[$facility_id] = $transport_ref_incoming_concat; ?>
                                        </div>
                                        <div class="tab-pane fade" id="department{{ $row->id }}">
                                            <?php $department_ref_incoming_concat = ''; ?>
                                            @if(count($department_ref) >= 1)
                                                @foreach($department_ref as $department)
                                                    <?php $department_ref_incoming_concat .= $department->description.'-'.$department->count."\n"; ?>
                                                    <label><span class="badge bg-maroon">{{ $department->count }}</span> {{ $department->description }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                            <?php $department_ref_incoming[$facility_id] = $department_ref_incoming_concat; ?>
                                        </div>
                                        <div class="tab-pane fade" id="issue{{ $row->id }}">
                                            <?php $issue_ref_incoming_concat = ''; ?>
                                            @if(count($issue_ref) >= 1)
                                                @foreach($issue_ref as $issue)
                                                    <?php $issue_ref_incoming_concat .= $issue->issue."\n"; ?>
                                                    <label>{{ $issue->issue }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                            <?php $issue_ref_incoming[$facility_id] = $issue_ref_incoming_concat; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="outgoing{{ $row->id }}">
                        <?php
                            $outgoing = \DB::connection('mysql')->select("call consolidatedOutgoing('$facility_id','$date_start','$date_end')")[0];
                            $accepted_outgoing = \App\Activity::where("referred_from","=",$row->id)
                                ->where("date_referred",">=",$date_start)
                                ->where("date_referred","<=",$date_end)
                                ->where("status","=","accepted")
                                ->count();
                            $seenzoned_outgoing = \DB::connection('mysql')->select("call getViewedOnlyOutgoing('$facility_id','$date_start','$date_end')")[0]->seen_outgoing;
                            if($accepted_outgoing > $seenzoned_outgoing){
                                $accepted_outgoing = $seenzoned_outgoing;
                            }
                            $facility_outgoing = \App\Tracking::select("facility.name",\DB::raw("count(facility.id) as count"))
                                ->leftJoin("facility","facility.id","=","tracking.referred_to")
                                ->where("tracking.referred_from","=",$row->id)
                                ->where("tracking.date_referred",">=",$date_start)
                                ->where("tracking.date_referred","<=",$date_end)
                                ->groupBy('facility.id')
                                ->orderBy("count","desc")
                                ->get();
                            $referral_md_outgoing = \App\Tracking::select(\DB::raw("concat('DR. ',users.fname,' ',users.mname,' ',users.lname) as fullname"),\DB::raw("count(tracking.referring_md) as count"))
                                ->leftJoin("users","users.id","=","tracking.referring_md")
                                ->where("tracking.referred_from","=",$row->id)
                                ->where(\DB::raw("concat('DR. ',users.fname,' ',users.mname,' ',users.lname)"),"!=","")
                                ->where("tracking.date_referred",">=",$date_start)
                                ->where("tracking.date_referred","<=",$date_end)
                                ->groupBy("tracking.referring_md")
                                ->orderBy("count","desc")
                                ->limit(10)
                                ->get();
                            $diagnosis_ref_outgoing = \App\PatientForm::select("patient_form.diagnosis",\DB::raw("count(patient_form.diagnosis) as count"))
                                ->where("patient_form.referred_to","=",$row->id)
                                ->where("patient_form.diagnosis","!=","")
                                ->where("patient_form.time_referred",">=",$date_start)
                                ->where("patient_form.time_referred","<=",$date_end)
                                ->groupBy('patient_form.diagnosis')
                                ->orderBy("count","desc")
                                ->limit(10)
                                ->get();
                            $reason_ref_outgoing = \App\PatientForm::select("patient_form.reason",\DB::raw("count(patient_form.reason) as count"))
                                ->where("patient_form.referring_facility","=",$row->id)
                                ->where("patient_form.reason","!=","")
                                ->where("patient_form.time_referred",">=",$date_start)
                                ->where("patient_form.time_referred","<=",$date_end)
                                ->groupBy('patient_form.reason')
                                ->orderBy("count","desc")
                                ->limit(10)
                                ->get();
                            $transport_ref_outgoing = \App\Tracking::select("mode_transportation.transportation",\DB::raw("count(mode_transportation.id) as count"))
                                ->leftJoin("mode_transportation","mode_transportation.id","=","tracking.mode_transportation")
                                ->where("tracking.referred_from","=",$row->id)
                                ->where("mode_transportation.transportation","!=","")
                                ->where("tracking.date_referred",">=",$date_start)
                                ->where("tracking.date_referred","<=",$date_end)
                                ->groupBy('mode_transportation.id')
                                ->orderBy("count","desc")
                                ->get();
                            $department_ref_outgoing = \App\Tracking::select("department.description",\DB::raw("count(department.id) as count"))
                                ->leftJoin("department","department.id","=","tracking.department_id")
                                ->where("tracking.referred_from","=",$row->id)
                                ->where("tracking.date_referred",">=",$date_start)
                                ->where("tracking.date_referred","<=",$date_end)
                                ->where("tracking.department_id","!=","")
                                ->groupBy('department.id')
                                ->orderBy("count","desc")
                                ->get();
                            $issue_ref_outgoing = \App\Tracking::select("issue.issue")
                                ->leftJoin("issue","issue.tracking_id","=","tracking.id")
                                ->where("tracking.referred_from","=",$row->id)
                                ->where("tracking.date_referred",">=",$date_start)
                                ->where("tracking.date_referred","<=",$date_end)
                                ->where("issue.issue","!=","")
                                ->get();

                            $redirected_outgoing = \App\Activity::
                                where("status","=","rejected")
                                ->where("date_referred",">=",$date_start)
                                ->where("date_referred","<=",$date_end)
                                ->where('referred_from',$row->id)
                                ->count();

                            $achived_outgoing = \App\Tracking::where(function($q){
                                    $q->where('tracking.status','referred')
                                        ->orwhere('tracking.status','seen');
                                })
                                ->where(DB::raw("TIMESTAMPDIFF(MINUTE,tracking.date_referred,now())"),">",4320)
                                ->where('referred_from',$row->id)
                                ->count();

                            $total_outgoing1[$facility_id] = $outgoing->count_outgoing;
                            $accepted_outgoing1[$facility_id] = $accepted_outgoing;
                            $seenzoned_outgoing1[$facility_id] = $seenzoned_outgoing;
                        ?>
                        <div class="row">
                            <div class="col-md-4 ">
                                <div class="box-body box-profile">
                                    @if($row->picture)
                                        <img class="profile-user-img img-responsive img-circle" src="{{ asset('resources/hospital_logo').'/'.$row->picture }}" alt="User profile picture">
                                    @else
                                        <img class="profile-user-img img-responsive img-circle" src="{{ asset('resources/img/doh.png') }}" alt="User profile picture">
                                    @endif

                                    <h3 class="profile-username text-center">{{ $row->name }}</h3>
                                    <strong><i class="fa fa-random margin-r-5"></i> Transaction</strong>
                                    <p>
                                        <?php
                                            echo '<span class="label label-warning">Outgoing <span class="badge bg-red" >'.$outgoing->count_outgoing.'</span></span>';
                                            echo '<span class="label label-warning">Viewed Only <span class="badge bg-red" >'.$seenzoned_outgoing.'</span></span>';
                                            echo '<span class="label label-warning">Accepted <span class="badge bg-red" >'.$accepted_outgoing.'</span></span>';
                                            echo '<span class="label label-warning">Redirected <span class="badge bg-red" >'.$redirected_outgoing.'</span></span>';
                                            echo '<span class="label label-warning">Archived <span class="badge bg-red" >'.$achived_outgoing.'</span></span><br><br><br><br><br>';
                                        ?>
                                    </p>
                                    <strong><i class="fa fa-clock-o margin-r-5"></i> Turnaround time</strong>
                                    <p>
                                        <span class="label label-primary">Viewed Only Acceptance<?php echo '<span class="badge bg-red"><i class="fa fa-clock-o"></i> '.' Under Development</span>'; ?></span>
                                        <span class="label label-primary">Viewed Only Redirection<?php echo '<span class="badge bg-red"><i class="fa fa-clock-o"></i> '.' Under Development</span>'; ?></span>
                                        <?php
                                            $turnaround_time_accept_outgoing1[$facility_id] = $outgoing->turnaround_time_accept_outgoing;
                                            $turnaround_time_arrived_outgoing1[$facility_id] = $outgoing->turnaround_time_arrived_outgoing;
                                        ?>
                                        @if($outgoing->turnaround_time_accept_outgoing)
                                            <?php $turnaround_time_accept_outgoing1[$facility_id] .= ' mins'; ?>
                                            <span class="label label-primary">Acceptance <?php echo '<span class="badge bg-red"><i class="fa fa-clock-o"></i> '.$outgoing->turnaround_time_accept_outgoing.' mins</span>'; ?></span>
                                        @endif
                                        <span class="label label-primary">Redirection <?php echo '<span class="badge bg-red"><i class="fa fa-clock-o"></i> '.' Under Development</span>'; ?></span>
                                        @if($outgoing->turnaround_time_arrived_outgoing)
                                            <?php $turnaround_time_arrived_outgoing1[$facility_id] .= ' mins'; ?>
                                            <span class="label label-primary">To Transport <?php echo '<span class="badge bg-red"><i class="fa fa-clock-o"></i> '.$outgoing->turnaround_time_arrived_outgoing.' mins</span>'; ?></span>
                                        @endif
                                    </p><br><br><br><br><br><br><br><br>

                                    <strong><i class="fa fa-book margin-r-5"></i> Hospital Level</strong>
                                    <p>
                                        <span class="label label-success">Horizontal <?php echo '<span class="badge bg-red">'.'Under Development</span>'; ?></span>
                                        <span class="label label-success">Vertical <?php echo '<span class="badge bg-red">'.'Under Development</span>'; ?></span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#referral_hospitals_outgoing{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: green"></i> Referred Facility</a></li>
                                        <li><a href="#referral_md_outgoing{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: blue"></i> Referred Doctor <small>(Top 10)</small></a></li>
                                        <li><a href="#diagnosis_reason_outgoing{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: yellowgreen"></i> Diagnoses & Reasons <small>(Top 10)</small></a></li>
                                        <li><a href="#transportation_outgoing{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: red"></i> Common Transportation</a></li>
                                        <li><a href="#department_outgoing{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: lightskyblue"></i> Department</a></li>
                                        <li><a href="#issue_outgoing{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: orange"></i> Remarks</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="referral_hospitals_outgoing{{ $row->id }}">
                                            <?php $common_referred_facility_outgoing_concat = ''; ?>
                                            @foreach($facility_outgoing as $fac_outgoing)
                                                <?php $common_referred_facility_outgoing_concat .= $fac_outgoing->name.'-'.$fac_outgoing->count."\n"; ?>
                                                <label><span class="badge bg-maroon">{{ $fac_outgoing->count }}</span> {{ $fac_outgoing->name }}</label><br>
                                            @endforeach
                                            <?php $common_referred_facility_outgoing1[$facility_id] = $common_referred_facility_outgoing_concat; ?>
                                        </div>
                                        <div class="tab-pane fade" id="referral_md_outgoing{{ $row->id }}">
                                            <?php $common_referred_doctor_outgoing_concat = ''; ?>
                                            @foreach($referral_md_outgoing as $ref_md_outgoing)
                                                <?php $common_referred_doctor_outgoing_concat .= $ref_md_outgoing->fullname.'-'.$ref_md_outgoing->count."\n"; ?>
                                                <label><span class="badge bg-maroon">{{ $ref_md_outgoing->count }}</span> {{ $ref_md_outgoing->fullname }}</label><br>
                                            @endforeach
                                            <?php $common_referred_doctor_outgoing1[$facility_id] = $common_referred_doctor_outgoing_concat; ?>
                                        </div>
                                        <div class="tab-pane fade" id="diagnosis_reason_outgoing{{ $row->id }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h4>Diagnosis</h4>
                                                    <?php $diagnosis_ref_outgoing_concat = ''; ?>
                                                    @if(count($diagnosis_ref_outgoing) >= 1)
                                                        @foreach($diagnosis_ref_outgoing as $diagnosis_outgoing)
                                                            <?php $diagnosis_ref_outgoing_concat .= $diagnosis_outgoing->diagnosis.'-'.$diagnosis_outgoing->count."\n"; ?>
                                                            <label><span class="badge bg-maroon">{{ $diagnosis_outgoing->count }}</span> {{ $diagnosis_outgoing->diagnosis }}</label><br>
                                                        @endforeach
                                                    @else
                                                        <h1>Empty Record!</h1>
                                                    @endif
                                                    <?php $diagnosis_ref_outgoing1[$facility_id] = $diagnosis_ref_outgoing_concat; ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <h4>Reasons</h4>
                                                    <?php $reason_ref_outgoing_concat = ''; ?>
                                                    @if(count($reason_ref_outgoing) >= 1)
                                                        @foreach($reason_ref_outgoing as $reason_outgoing)
                                                            <?php $reason_ref_outgoing_concat .= $reason_outgoing->reason.'-'.$reason_outgoing->count."\n"; ?>
                                                            <label><span class="badge bg-maroon">{{ $reason_outgoing->count }}</span> {{ $reason_outgoing->reason }}</label><br>
                                                        @endforeach
                                                    @else
                                                        <h1>Empty Record!</h1>
                                                    @endif
                                                    <?php $reason_ref_outgoing1[$facility_id] = $reason_ref_outgoing_concat; ?>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="tab-pane fade" id="transportation_outgoing{{ $row->id }}">
                                            <?php $transport_ref_outgoing_concat = ''; ?>
                                            @if(count($transport_ref_outgoing) >= 1)
                                                @foreach($transport_ref_outgoing as $transport_outgoing)
                                                    <?php $transport_ref_outgoing_concat .= $transport_outgoing->transportation.'-'.$transport_outgoing->count."\n"; ?>
                                                    <label><span class="badge bg-maroon">{{ $transport_outgoing->count }}</span> {{ $transport_outgoing->transportation }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                            <?php $transport_ref_outgoing1[$facility_id] = $transport_ref_outgoing_concat; ?>
                                        </div>
                                        <div class="tab-pane fade" id="department_outgoing{{ $row->id }}">
                                            <?php $department_ref_outgoing_concat = ''; ?>
                                            @if(count($department_ref_outgoing) >= 1)
                                                @foreach($department_ref_outgoing as $department_outgoing)
                                                    <?php $department_ref_outgoing_concat .= $department_outgoing->description.'-'.$department_outgoing->count."\n"; ?>
                                                    <label><span class="badge bg-maroon">{{ $department_outgoing->count }}</span> {{ $department_outgoing->description }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                            <?php $department_ref_outgoing1[$facility_id] = $department_ref_outgoing_concat; ?>
                                        </div>
                                        <div class="tab-pane fade" id="issue_outgoing{{ $row->id }}">
                                            <?php $issue_ref_outgoing_concat = ''; ?>
                                            @if(count($issue_ref_outgoing) >= 1)
                                                @foreach($issue_ref_outgoing as $issue_outgoing)
                                                    <?php $issue_ref_outgoing_concat .= $issue_outgoing->issue."\n"; ?>
                                                    <label>{{ $issue_outgoing->issue }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                            <?php $issue_ref_outgoing1[$facility_id] = $issue_ref_outgoing_concat; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @else
        <div class="alert-section">
            <div class="alert alert-warning">
                <span class="text-warning">
                    <i class="fa fa-warning"></i> No referrals!
                    <ul>
                        <li>Filer List:</li>
                        <ul>
                            <li>Date range - {{ $date_range_start.' - '.$date_range_end }}</li>
                        </ul>
                    </ul>
                </span>
            </div>
        </div>
    @endif
    <?php
        //INCOMING
        Session::put('accepted_incoming',$accepted_incoming);
        Session::put('seenzoned_incoming',$seenzoned_incoming);
        Session::put('common_source_incoming',$common_source_incoming);
        Session::put('referring_doctor_incoming',$referring_doctor_incoming);
        Session::put('diagnosis_ref_incoming',$diagnosis_ref_incoming);
        Session::put('reason_ref_incoming',$reason_ref_incoming);
        Session::put('transport_ref_incoming',$transport_ref_incoming);
        Session::put('department_ref_incoming',$department_ref_incoming);
        Session::put('issue_ref_incoming',$issue_ref_incoming);
        Session::put('turnaround_time_accept_incoming',$turnaround_time_accept_incoming);
        Session::put('turnaround_time_arrived_incoming',$turnaround_time_arrived_incoming);
        //OUTGOING
        Session::put('total_outgoing1',$total_outgoing1);
        Session::put('accepted_outgoing1',$accepted_outgoing1);
        Session::put('seenzoned_outgoing1',$seenzoned_outgoing1);
        Session::put('common_referred_facility_outgoing1',$common_referred_facility_outgoing1);
        Session::put('common_referred_doctor_outgoing1',$common_referred_doctor_outgoing1);
        Session::put('diagnosis_ref_outgoing1',$diagnosis_ref_outgoing1);
        Session::put('reason_ref_outgoing1',$reason_ref_outgoing1);
        Session::put('transport_ref_outgoing1',$transport_ref_outgoing1);
        Session::put('department_ref_outgoing1',$department_ref_outgoing1);
        Session::put('issue_ref_outgoing1',$issue_ref_outgoing1);
        Session::put('turnaround_time_accept_outgoing1',$turnaround_time_accept_outgoing1);
        Session::put('turnaround_time_arrived_outgoing1',$turnaround_time_arrived_outgoing1);
    ?>

@endsection

@section('css')

@endsection

@section('js')
    <script>
        //Date range picker
        $('#reservation').daterangepicker();
    </script>

@endsection