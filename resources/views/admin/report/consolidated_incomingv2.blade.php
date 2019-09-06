<?php
    use Illuminate\Support\Facades\Session;
?>
@extends('layouts.app')
<link href="{{ asset('resources/plugin/daterangepicker_old/daterangepicker-bs3.css') }}" rel="stylesheet">
@section('content')
    <form action="{{ asset('admin/report/consolidated/incomingv2') }}" method="POST">
        {{ csrf_field() }}
        <div class="row" style="margin-top: -0.5%;margin-bottom: 1%">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" name="date_range" id="reservation">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-info btn-flat">Filter</button>
                    </span>
                    <span class="input-group-btn">
                        <a href="{{ asset('excel/incoming') }}" type="button" name="from_the_start" class="btn btn-warning btn-flat">Incoming (Excel)</a>
                        <a href="#" type="button" name="from_the_start" class="btn btn-success btn-flat">Outgoing (Excel)</a>
                    </span>
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
    ?>
    @foreach($data as $row)
    <div class="box box-success">
        <div class="box-body no-padding">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#incoming{{ $row->id }}" data-toggle="tab">Incoming</a></li>
                    <li ><a href="#outgoing{{ $row->id }}" data-toggle="tab">Outgoing</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="incoming{{ $row->id }}">
                        <?php
                            $incoming = $row->count_incoming;
                            $accepted = \App\Activity::where("referred_to","=",$row->id)->where("status","=","accepted")->count();
                            $facility_id = $row->id;
                            $accepted_incoming[$facility_id] = $accepted;
                            $seenzoned = \DB::connection('mysql')->select("call getViewedOnly('$facility_id')")[0]->viewed_only;
                            $seenzoned_incoming[$facility_id] = $seenzoned;
                            $facility = \App\Tracking::select("facility.name",\DB::raw("count(facility.id) as count"))
                                ->leftJoin("facility","facility.id","=","tracking.referred_from")
                                ->where("tracking.referred_to","=",$row->id)
                                ->groupBy('facility.id')
                                ->orderBy("count","desc")
                                ->get();
                            $common_source_incoming[$facility_id] = $facility;

                            $referring_doctor = \App\Tracking::select(\DB::raw("concat('DR. ',users.fname,' ',users.mname,' ',users.lname) as fullname"),\DB::raw("count(tracking.referring_md) as count"))
                                ->leftJoin("users","users.id","=","tracking.referring_md")
                                ->where("tracking.referred_to","=",$row->id)
                                ->where(\DB::raw("concat('DR. ',users.fname,' ',users.mname,' ',users.lname)"),"!=","")
                                ->groupBy("tracking.referring_md")
                                ->orderBy("count","desc")
                                ->limit(10)
                                ->get();
                            $diagnosis_ref = \App\PatientForm::select("patient_form.diagnosis",\DB::raw("count(patient_form.diagnosis) as count"))
                                ->where("patient_form.referred_to","=",$row->id)
                                ->where("patient_form.diagnosis","!=","")
                                ->groupBy('patient_form.diagnosis')
                                ->orderBy("count","desc")
                                ->limit(10)
                                ->get();
                            $reason_ref = \App\PatientForm::select("patient_form.reason",\DB::raw("count(patient_form.reason) as count"))
                                ->where("patient_form.referred_to","=",$row->id)
                                ->where("patient_form.reason","!=","")
                                ->groupBy('patient_form.reason')
                                ->orderBy("count","desc")
                                ->limit(10)
                                ->get();
                            $transport_ref = \App\Tracking::select("mode_transportation.transportation",\DB::raw("count(mode_transportation.id) as count"))
                                ->leftJoin("mode_transportation","mode_transportation.id","=","tracking.mode_transportation")
                                ->where("tracking.referred_to","=",$row->id)
                                ->where("mode_transportation.transportation","!=","")
                                ->groupBy('mode_transportation.id')
                                ->orderBy("count","desc")
                                ->get();
                            $department_ref = \App\Tracking::select("department.description",\DB::raw("count(department.id) as count"))
                                ->leftJoin("department","department.id","=","tracking.department_id")
                                ->where("tracking.referred_to","=",$row->id)
                                ->where("tracking.department_id","!=","")
                                ->groupBy('department.id')
                                ->orderBy("count","desc")
                                ->get();
                            $issue_ref = \App\Tracking::select("issue.issue")
                                ->leftJoin("issue","issue.tracking_id","=","tracking.id")
                                ->where("tracking.referred_to","=",$row->id)
                                ->where("issue.issue","!=","")
                                ->get();
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
                                            echo '<span class="label label-primary">Incoming <span class="badge bg-red" >'.$incoming.'</span></span>';
                                            echo '<span class="label label-warning">Viewed Only <span class="badge bg-red" >'.$seenzoned.'</span></span>';
                                        echo '<span class="label label-success">Accepted <span class="badge bg-red" >'.$accepted.'</span></span><br><br><br>';
                                        ?>
                                    </p>

                                    <strong><i class="fa fa-book margin-r-5"></i> Turnaround time</strong>

                                    <p>
                                        @if($row->turnaround_time_accept)
                                            <span class="label label-info">Acceptance <?php echo '<span class="badge bg-red"><i class="fa fa-clock-o"></i> '.$row->turnaround_time_accept.' mins</span>'; ?></span><br><br>
                                        @endif
                                        @if($row->turnaround_time_arrived)
                                            <span class="label label-primary">Arrival <?php echo '<span class="badge bg-red"><i class="fa fa-clock-o"></i> '.$row->turnaround_time_arrived.'mins</span>'; ?></span>
                                        @endif
                                    </p><br>

                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#common_sources{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: green"></i> Common Sources(Facility)</a></li>
                                        <li><a href="#common_referring{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: blue"></i> Common Referring Doctor <small>(Top 10)</small></a></li>
                                        <li><a href="#diagnosis{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: yellowgreen"></i> Diagnoses <small>(Top 10)</small></a></li>
                                        <li><a href="#reasons{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: #6e93cd"></i> Reasons <small>(Top 10)</small></a></li>
                                        <li><a href="#transportation{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: red"></i> Common Transportation</a></li>
                                        <li><a href="#department{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: lightskyblue"></i> Department</a></li>
                                        <li><a href="#issue{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: orange"></i> Remarks</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="common_sources{{ $row->id }}">
                                            @foreach($facility as $fac)
                                            <label><span class="badge bg-maroon">{{ $fac->count }}</span> {{ $fac->name }}</label><br>
                                            @endforeach
                                        </div>
                                        <div class="tab-pane fade" id="common_referring{{ $row->id }}">
                                            @foreach($referring_doctor as $referring)
                                                <label><span class="badge bg-maroon">{{ $referring->count }}</span> {{ $referring->fullname }}</label><br>
                                            @endforeach
                                        </div>
                                        <div class="tab-pane fade" id="diagnosis{{ $row->id }}">
                                            @if(count($diagnosis_ref) >= 1)
                                                @foreach($diagnosis_ref as $diagnosis)
                                                    <label><span class="badge bg-maroon">{{ $diagnosis->count }}</span> {{ $diagnosis->diagnosis }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                        </div>
                                        <div class="tab-pane fade" id="reasons{{ $row->id }}">
                                            @if(count($reason_ref) >= 1)
                                                @foreach($reason_ref as $reason)
                                                    <label><span class="badge bg-maroon">{{ $reason->count }}</span> {{ $reason->reason }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                        </div>
                                        <div class="tab-pane fade" id="transportation{{ $row->id }}">
                                            @if(count($transport_ref) >= 1)
                                                @foreach($transport_ref as $transport)
                                                    <label><span class="badge bg-maroon">{{ $transport->count }}</span> {{ $transport->transportation }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                        </div>
                                        <div class="tab-pane fade" id="department{{ $row->id }}">
                                            @if(count($department_ref) >= 1)
                                                @foreach($department_ref as $department)
                                                    <label><span class="badge bg-maroon">{{ $department->count }}</span> {{ $department->description }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                        </div>
                                        <div class="tab-pane fade" id="issue{{ $row->id }}">
                                            @if(count($issue_ref) >= 1)
                                                @foreach($issue_ref as $issue)
                                                    <label>{{ $issue->issue }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
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
                                ->groupBy('mode_transportation.id')
                                ->orderBy("count","desc")
                                ->get();
                            $department_ref_outgoing = \App\Tracking::select("department.description",\DB::raw("count(department.id) as count"))
                                ->leftJoin("department","department.id","=","tracking.department_id")
                                ->where("tracking.referred_from","=",$row->id)
                                ->where("tracking.department_id","!=","")
                                ->groupBy('department.id')
                                ->orderBy("count","desc")
                                ->get();
                            $issue_ref_outgoing = \App\Tracking::select("issue.issue")
                                ->leftJoin("issue","issue.tracking_id","=","tracking.id")
                                ->where("tracking.referred_from","=",$row->id)
                                ->where("issue.issue","!=","")
                                ->get();
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
                                            echo '<span class="label label-primary">Outgoing <span class="badge bg-red" >'.$outgoing->count_outgoing.'</span></span>';
                                            echo '<span class="label label-warning">Viewed Only <span class="badge bg-red" >'.$seenzoned_outgoing.'</span></span>';
                                            echo '<span class="label label-success">Accepted <span class="badge bg-red" >'.$accepted_outgoing.'</span></span><br><br><br>';
                                        ?>
                                    </p>
                                    <strong><i class="fa fa-book margin-r-5"></i> Turnaround time</strong>
                                    <p>
                                        @if($outgoing->turnaround_time_accept_outgoing)
                                            <span class="label label-info">Acceptance <?php echo '<span class="badge bg-red"><i class="fa fa-clock-o"></i> '.$outgoing->turnaround_time_accept_outgoing.' mins</span>'; ?></span><br><br>
                                        @endif
                                        @if($outgoing->turnaround_time_arrived_outgoing)
                                            <span class="label label-primary">Arrival <?php echo '<span class="badge bg-red"><i class="fa fa-clock-o"></i> '.$outgoing->turnaround_time_arrived_outgoing.'mins</span>'; ?></span>
                                        @endif
                                    </p><br>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#referral_hospitals_outgoing{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: green"></i> Referral Hospitals</a></li>
                                        <li><a href="#referral_md_outgoing{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: blue"></i> Common Referring Doctor <small>(Top 10)</small></a></li>
                                        <li><a href="#diagnosis_outgoing{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: yellowgreen"></i> Diagnoses <small>(Top 10)</small></a></li>
                                        <li><a href="#reasons_outgoing{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: #6e93cd"></i> Reasons <small>(Top 10)</small></a></li>
                                        <li><a href="#transportation_outgoing{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: red"></i> Common Transportation</a></li>
                                        <li><a href="#department_outgoing{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: lightskyblue"></i> Department</a></li>
                                        <li><a href="#issue_outgoing{{ $row->id }}" data-toggle="tab"><i class="fa fa-ambulance" style="color: orange"></i> Remarks</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="referral_hospitals_outgoing{{ $row->id }}">
                                            @foreach($facility_outgoing as $fac_outgoing)
                                                <label><span class="badge bg-maroon">{{ $fac_outgoing->count }}</span> {{ $fac_outgoing->name }}</label><br>
                                            @endforeach
                                        </div>
                                        <div class="tab-pane fade" id="referral_md_outgoing{{ $row->id }}">
                                            @foreach($referral_md_outgoing as $ref_md_outgoing)
                                                <label><span class="badge bg-maroon">{{ $ref_md_outgoing->count }}</span> {{ $ref_md_outgoing->fullname }}</label><br>
                                            @endforeach
                                        </div>
                                        <div class="tab-pane fade" id="diagnosis_outgoing{{ $row->id }}">
                                            @if(count($diagnosis_ref_outgoing) >= 1)
                                                @foreach($diagnosis_ref_outgoing as $diagnosis_outgoing)
                                                    <label><span class="badge bg-maroon">{{ $diagnosis_outgoing->count }}</span> {{ $diagnosis_outgoing->diagnosis }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                        </div>
                                        <div class="tab-pane fade" id="reasons_outgoing{{ $row->id }}">
                                            @if(count($reason_ref_outgoing) >= 1)
                                                @foreach($reason_ref_outgoing as $reason_outgoing)
                                                    <label><span class="badge bg-maroon">{{ $reason_outgoing->count }}</span> {{ $reason_outgoing->reason }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                        </div>
                                        <div class="tab-pane fade" id="transportation_outgoing{{ $row->id }}">
                                            @if(count($transport_ref_outgoing) >= 1)
                                                @foreach($transport_ref_outgoing as $transport_outgoing)
                                                    <label><span class="badge bg-maroon">{{ $transport_outgoing->count }}</span> {{ $transport_outgoing->transportation }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                        </div>
                                        <div class="tab-pane fade" id="department_outgoing{{ $row->id }}">
                                            @if(count($department_ref_outgoing) >= 1)
                                                @foreach($department_ref_outgoing as $department_outgoing)
                                                    <label><span class="badge bg-maroon">{{ $department_outgoing->count }}</span> {{ $department_outgoing->description }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
                                        </div>
                                        <div class="tab-pane fade" id="issue_outgoing{{ $row->id }}">
                                            @if(count($issue_ref_outgoing) >= 1)
                                                @foreach($issue_ref_outgoing as $issue_outgoing)
                                                    <label>{{ $issue_outgoing->issue }}</label><br>
                                                @endforeach
                                            @else
                                                <h1>Empty Record!</h1>
                                            @endif
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
    <?php
        Session::put('accepted_incoming',$accepted_incoming);
        Session::put('seenzoned_incoming',$seenzoned_incoming);
        Session::put('common_source_incoming',$common_source_incoming);
    ?>

@endsection

@section('css')

@endsection

@section('js')
    <script src="{{ url('resources/plugin/daterangepicker_old/moment.min.js') }}"></script>
    <script src="{{ url('resources/plugin/daterangepicker_old/daterangepicker.js') }}"></script>

    <script>
        //Date range picker
        $('#reservation').daterangepicker();
    </script>

@endsection