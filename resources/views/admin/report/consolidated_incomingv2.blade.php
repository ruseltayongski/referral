@extends('layouts.app')

@section('content')
    <div class="row" style="margin-top: -0.5%;margin-bottom: 1%">
        <div class="col-md-5">
            <div class="input-group">
                <input type="text" class="form-control">
                <span class="input-group-btn">
                    <button type="button" class="btn btn-info btn-flat">Filter</button>
                </span>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-primary btn-flat">From the start</button>
                </span>
            </div>
        </div>
    </div>
    @foreach($data as $row)
    <div class="box box-success">
        <div class="box-body no-padding">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li ><a href="#incoming{{ $row->id }}" data-toggle="tab">Incoming</a></li>
                    <li class="active"><a href="#outgoing{{ $row->id }}" data-toggle="tab">Outgoing</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="incoming{{ $row->id }}">
                        <?php
                            $incoming = $row->count_incoming;
                            $accepted = \App\Activity::where("referred_to","=",$row->id)->where("status","=","accepted")->count();
                            $facility_id = $row->id;
                            $seenzoned = \DB::connection('mysql')->select("call getViewedOnly('$facility_id')")[0]->viewed_only;
                            $facility = \App\Tracking::select("facility.name",\DB::raw("count(facility.id) as count"))
                                ->leftJoin("facility","facility.id","=","tracking.referred_from")
                                ->where("tracking.referred_to","=",$row->id)
                                ->groupBy('facility.id')
                                ->orderBy("count","desc")
                                ->get();
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
                                            echo '<span class="label label-success">Accepted <span class="badge bg-red" >'.$accepted.'</span></span>';
                                            echo '<span class="label label-warning">Viewed Only <span class="badge bg-red" >'.$seenzoned.'</span></span><br><br><br>';
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
                    <div class="active tab-pane" id="outgoing{{ $row->id }}">
                        <?php
                            $outgoing = \App\Tracking::where("referred_from","=",$row->id)->count();
                            $accepted_outgoing = \App\Activity::where("referred_from","=",$row->id)->where("status","=","accepted")->count();
                            $seenzoned_outgoing = "Under Develop";
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
                                        echo '<span class="label label-primary">Outgoing <span class="badge bg-red" >'.$outgoing.'</span></span>';
                                        echo '<span class="label label-success">Accepted <span class="badge bg-red" >'.$accepted_outgoing.'</span></span>';
                                        echo '<span class="label label-warning">Viewed Only <span class="badge bg-red" >'.$seenzoned_outgoing.'</span></span><br><br><br>';
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

@endsection

@section('css')
    <link rel="stylesheet" href="{{ url('resources/plugin/daterange/daterangepicker.css') }}" />
@endsection

@section('js')
    <script src="{{ url('resources/plugin/daterange/moment.min.js') }}"></script>
    <script src="{{ url('resources/plugin/daterange/daterangepicker.js') }}"></script>

    <script>

    </script>

@endsection