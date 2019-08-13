@extends('layouts.app')

@section('content')
    <div class="col-md-12 hide">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3>{{ $title }}
                    <small class="pull-right text-success">
                        <?php //echo date('F d, Y').' - '.date('F d, Y'); ?>
                    </small>
                </h3>
            </div>
            <div class="box-body">
                @if(count($data)>0)
                <div class="table-responsive">
                    <div class="alert alert-info">
                        <p style="color: #3c8dbc !important">
                            <i class="fa fa-info"></i> Legend:
                        </p>
                        <span class="badge bg-red">Incoming</span> <span class="badge bg-green">Accepted</span> <span class="badge bg-yellow">Viewed Only</span>
                    </div>
                    <table class="table table-striped table-hover table-bordered" style="font-size: 8pt">
                        <tr class="bg-black">
                            <td>Facility Name</td>
                            <td>Common Sources(Facility)</td>
                            <td>Common Referring(Doctor)</td>

                            <td>Reasons by<br>(ranked)</td>
                            <td>Top diagnoses</td>
                            <td>Common Transportation</td>
                            <td>Department</td>
                            <td>Issues &<br>Concerns</td>
                        </tr>
                        @foreach($data as $row)
                        <?php
                        $incoming = \App\Tracking::where("referred_to","=",$row->id)->count();
                        $accepted = \App\Activity::where("referred_to","=",$row->id)->where("status","=","accepted")->count();
                        $facility_id = $row->id;
                        $seenzoned = \DB::connection('mysql')->select("call getViewedOnly('$facility_id')")[0]->viewed_only;
                        ?>
                        <tr>
                            <td >
                                <?php
                                echo '<span class="label label-primary">'.$row->name.'</span>';
                                echo '<br><br>';
                                echo '<span class="badge bg-red">'.$incoming.'</span><span class="badge bg-green">'.$accepted.'</span><span class="badge bg-yellow">'.$seenzoned.'</span>';
                                ?>
                            </td>
                            <td >
                                <?php
                                $facility = \App\Tracking::select("facility.name",\DB::raw("count(facility.id) as count"))
                                    ->leftJoin("facility","facility.id","=","tracking.referred_from")
                                    ->where("tracking.referred_to","=",$row->id)
                                    ->groupBy('facility.id')
                                    ->orderBy("count","desc")
                                    ->get();
                                foreach($facility as $fac){
                                    echo '<span class="label label-primary">'.$fac->name.'<span class="badge bg-maroon">'.$fac->count.'</span></span>';
                                }
                                ?>
                            </td>
                            <td >
                                <?php
                                $referring = \App\Tracking::select(\DB::raw("concat('DR. ',users.fname,' ',users.mname,' ',users.lname) as fullname"),\DB::raw("count(tracking.referring_md) as count"))
                                    ->leftJoin("users","users.id","=","tracking.referring_md")
                                    ->where("tracking.referred_to","=",$row->id)
                                    ->where(\DB::raw("concat('DR. ',users.fname,' ',users.mname,' ',users.lname)"),"!=","")
                                    ->groupBy("tracking.referring_md")
                                    ->orderBy("count","desc")
                                    ->limit(3)
                                    ->get();
                                foreach($referring as $ref){
                                    echo '<span class="label label-primary">'.$ref->fullname.'<span class="badge bg-maroon">'.$ref->count.'</span></span>';
                                }
                                ?>
                            </td>

                            <td >
                                <?php
                                $reason = \App\PatientForm::select("patient_form.reason",\DB::raw("count(patient_form.reason) as count"))
                                    ->where("patient_form.referred_to","=",$row->id)
                                    ->where("patient_form.reason","!=","")
                                    ->groupBy('patient_form.reason')
                                    ->orderBy("count","desc")
                                    ->limit(3)
                                    ->get();
                                foreach($reason as $rea){
                                    echo '<span class="label label-primary">'.$rea->reason.'<span class="badge bg-maroon">'.$rea->count.'</span></span>';
                                    echo '<br><br>';
                                }
                                ?>
                            </td>
                            <td >
                                <?php
                                $diagnosis = \App\PatientForm::select("patient_form.diagnosis",\DB::raw("count(patient_form.diagnosis) as count"))
                                    ->where("patient_form.referred_to","=",$row->id)
                                    ->where("patient_form.diagnosis","!=","")
                                    ->groupBy('patient_form.diagnosis')
                                    ->orderBy("count","desc")
                                    ->limit(3)
                                    ->get();
                                foreach($diagnosis as $dia){
                                    echo '<span class="label label-primary">'.$dia->diagnosis.'<span class="badge bg-maroon">'.$dia->count.'</span></span>';
                                    echo '<br><br>';
                                }
                                ?>
                            </td>
                            <td >
                                <?php
                                $transportation = \App\Tracking::select("mode_transportation.transportation",\DB::raw("count(mode_transportation.id) as count"))
                                    ->leftJoin("mode_transportation","mode_transportation.id","=","tracking.mode_transportation")
                                    ->where("tracking.referred_to","=",$row->id)
                                    ->where("mode_transportation.transportation","!=","")
                                    ->groupBy('mode_transportation.id')
                                    ->orderBy("count","desc")
                                    ->get();
                                foreach($transportation as $trans){
                                    echo '<span class="label label-primary">'.$trans->transportation.'<span class="badge bg-maroon">'.$trans->count.'</span></span>';
                                }
                                ?>
                            </td>
                            <td >
                                <?php
                                $department = \App\Tracking::select("department.description",\DB::raw("count(department.id) as count"))
                                    ->leftJoin("department","department.id","=","tracking.department_id")
                                    ->where("tracking.referred_to","=",$row->id)
                                    ->where("tracking.department_id","!=","")
                                    ->groupBy('department.id')
                                    ->orderBy("count","desc")
                                    ->get();
                                foreach($department as $dep){
                                    echo '<span class="label label-primary">'.$dep->description.'<span class="badge bg-maroon">'.$dep->count.'</span></span>';
                                    echo '<br><br>';
                                }
                                ?>
                            </td>
                            <td >
                                <?php
                                $issue = \App\Tracking::select("issue.issue")
                                    ->leftJoin("issue","issue.tracking_id","=","tracking.id")
                                    ->where("tracking.referred_to","=",$row->id)
                                    ->where("issue.issue","!=","")
                                    ->get();
                                foreach($issue as $iss){
                                    echo '<span class="label label-primary">'.$iss->issue.'</span>';
                                    echo '<br><br>';
                                }
                                ?>
                            </td>
                        </tr>
                        @endforeach
                    </table>

                </div>
                <hr />
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
    <div class="row">
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
    </div><br>
    @foreach($data as $row)
    <?php
        $incoming = \App\Tracking::where("referred_to","=",$row->id)->count();
        $accepted = \App\Activity::where("referred_to","=",$row->id)->where("status","=","accepted")->count();
        $facility_id = $row->id;
        $seenzoned = \DB::connection('mysql')->select("call getViewedOnly('$facility_id')")[0]->viewed_only;
    ?>
    <div class="box box-success">
        <div class="box-body no-padding">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#incoming" data-toggle="tab">Incoming</a></li>
                    <li><a href="#outgoing" data-toggle="tab">Outgoing</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="incoming">
                        <div class="row">
                            <div class="col-md-4 ">
                                <div class="box-body box-profile">
                                    <img class="profile-user-img img-responsive img-circle" src="{{ asset('resources/img/doh.png') }}" alt="User profile picture">

                                    <h3 class="profile-username text-center">{{ $row->name }}</h3>

                                    <strong><i class="fa fa-random margin-r-5"></i> Transaction</strong>
                                    <p >
                                        <?php
                                            echo '<span class="label label-primary">Incoming<span class="badge bg-red" >'.$incoming.'</span></span>';
                                            echo '<span class="label label-success">Accepted<span class="badge bg-red" >'.$accepted.'</span></span>';
                                            echo '<span class="label label-warning">Viewed Only<span class="badge bg-red" >'.$seenzoned.'</span></span><br><br><br>';
                                        ?>
                                    </p>

                                    <strong><i class="fa fa-book margin-r-5"></i> Turnaround time</strong>

                                    <p>
                                        <span class="label label-info">Acceptance <?php echo '<span class="badge bg-red"><i class="fa fa-clock-o"></i> 100 mins</span>'; ?></span><br><br>
                                        <span class="label label-primary">Arrival <?php echo '<span class="badge bg-red"><i class="fa fa-clock-o"></i> 100 mins</span>'; ?></span>
                                    </p><br>

                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#activity" data-toggle="tab"><i class="fa fa-ambulance green" style="color: green"></i> Facility Name</a></li>
                                        <li><a href="#timeline" data-toggle="tab"><i class="fa fa-ambulance" style="color: orange"></i> Common Sources(Facility)</a></li>
                                        <li><a href="#settings" data-toggle="tab"><i class="fa fa-ambulance" style="color: blue"></i> Common Referring(Doctor)</a></li>
                                        <li><a href="#settings" data-toggle="tab"><i class="fa fa-ambulance" style="color: yellowgreen"></i> Top diagnoses</a></li>
                                        <li><a href="#settings" data-toggle="tab"><i class="fa fa-ambulance" style="color: red"></i> Common Transportation</a></li>
                                        <li><a href="#settings" data-toggle="tab"><i class="fa fa-ambulance" style="color: lightskyblue"></i> Department</a></li>
                                        <li><a href="#settings" data-toggle="tab"><i class="fa fa-ambulance" style="color: #ffbcc3"></i> Issues & Concerns</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="activity">
                                            <span class="label label-primary">Cebu Provincial Hospital (Danao City)<span class="badge bg-maroon">1212</span></span><br><br>
                                            <span class="label label-primary">Talisay District Hospital<span class="badge bg-maroon">1141</span></span><br><br>
                                            <span class="label label-primary">Eversley Childs Sanitarium<span class="badge bg-maroon">813</span></span><br><br>
                                            <span class="label label-primary">Cebu Provincial Hospital - Bogo City<span class="badge bg-maroon">783</span></span><br><br>
                                        </div>
                                        <div class="tab-pane fade" id="timeline">
                                            <span class="label label-primary">Cebu Provincial Hospital (Balamban)<span class="badge bg-maroon">735</span></span><br><br>
                                            <span class="label label-primary">Cebu Provincial Hospital (Carcar City)<span class="badge bg-maroon">482</span></span><br><br>
                                            <span class="label label-primary">Saint Anthony Mother And Child Hospital<span class="badge bg-maroon">177</span></span><br><br>
                                        </div>
                                        <div class="tab-pane fade" id="settings">
                                            <h1>Hello!</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="outgoing">
                        Ongoing Development!
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

@endsection