<style>
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
<div class="panel-body">
    <?php
    $position = ["1st","2nd","3rd","4th","5th","6th","7th","8th","9th","10th","11th","12th"];
    $position_count = 0;
    $referred_track = \App\Activity::where("code",$row->code)->where("status","referred")->first();
    $queue_referred = \App\Activity::where('code',$row->code)->where('status','queued')->orderBy('id','desc')->first()->remarks;
    $referred_seen_track = \App\Seen::where("code",$referred_track->code)
        ->where("facility_id",$referred_track->referred_to)
        ->where("created_at",">=",$referred_track->created_at)
        ->exists();
    $referred_queued_track = \App\Activity::where("code",$referred_track->code)
        ->where("referred_to",$referred_track->referred_to)
        ->where("created_at",">=",$referred_track->created_at)
        ->where("status","queued")
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
    $referred_notarrived_track = \App\Activity::where("code",$referred_track->code)
        ->where("referred_from",$referred_track->referred_to)
        ->where("created_at",">=",$referred_track->created_at)
        ->where("status","archived")
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

    //reset the variable in redirected if redirected not exist
    $redirected_queued_track = 0;
    $redirected_accepted_track = 0;
    $redirected_rejected_track = 0;
    $redirected_cancelled_track = 0;
    $redirected_travel_track = 0;
    $redirected_arrived_track = 0;
    $redirected_admitted_track = 0;
    $redirected_discharged_track = 0;
    //end reset
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
        <div class="text-center stepper-item @if($referred_accepted_track || $referred_rejected_track) completed @endif" id="accepted_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter
                                        <?php
            if($referred_cancelled_track)
                echo "bg-yellow";
            elseif($referred_rejected_track)
                echo "bg-red";
            elseif($referred_queued_track && !$referred_accepted_track)
                echo "bg-orange";
            ?>
                    " id="rejected_progress{{ $referred_track->code.$referred_track->id }}"><span id="queue_number{{ $referred_track->code }}">3</span></div>
            <div class="text-center step-name" id="rejected_name{{ $referred_track->code.$referred_track->id }}">
                <?php
                if($referred_cancelled_track)
                    echo 'Cancelled';
                elseif($referred_rejected_track)
                    echo 'Declined';
                elseif($referred_queued_track && !$referred_accepted_track)
                    echo 'Queued at <br> <b>'. $queue_referred.'</b>';
                else
                    echo 'Accepted'
                ?>
            </div>
        </div>
        <div class="stepper-item @if( ($referred_travel_track || $referred_arrived_track || $referred_notarrived_track) && !$referred_rejected_track && !$referred_cancelled_track) completed @endif" id="departed_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter">4</div>
            <div class="step-name">Departed</div>
        </div>
        <div class="stepper-item @if( ($referred_arrived_track || $referred_notarrived_track) && !$referred_rejected_track && !$referred_cancelled_track) completed @endif" id="arrived_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter {{ $referred_notarrived_track && !$referred_rejected_track ? 'bg-red' : '' }}" id="notarrived_progress{{ $referred_track->code.$referred_track->id }}">5</div>
            @if($referred_notarrived_track)
                <div class="step-name not_arrived">Not Arrived</div>
            @else
                <div class="step-name" id="arrived_name{{ $referred_track->code.$referred_track->id }}">Arrived</div>
            @endif
        </div>
        <div class="stepper-item @if(($referred_admitted_track || $referred_discharged_track) && !$referred_rejected_track && !$referred_cancelled_track) completed @endif" id="admitted_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter">6</div>
            <div class="step-name">Admitted</div>
        </div>
        <div class="stepper-item @if($referred_discharged_track && !$referred_rejected_track && !$referred_cancelled_track) completed @endif" id="discharged_progress{{ $referred_track->code.$referred_track->id }}">
            <div class="step-counter">7</div>
            <div class="step-name">Discharged</div>
        </div>
    </div>
    @if(count($redirected_track) > 0)
        @foreach($redirected_track as $redirect_track)
            <?php
            $queue_redirected = \App\Activity::where('code',$redirect_track->code)->where('status','queued')->orderBy('id','desc')->first()->remarks;
            $position_count++;
            $redirected_seen_track = \App\Seen::where("code",$redirect_track->code)
                ->where("facility_id",$redirect_track->referred_to)
                ->where("created_at",">=",$redirect_track->created_at)
                ->exists();
            $redirected_queued_track = \App\Activity::where("code",$redirect_track->code)
                ->where("referred_to",$redirect_track->referred_to)
                ->where("created_at",">=",$redirect_track->created_at)
                ->where("status","queued")
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
            $redirected_notarrived_track = \App\Activity::where("code",$redirect_track->code)
                ->where("referred_from",$redirect_track->referred_to)
                ->where("created_at",">=",$redirect_track->created_at)
                ->where("status","archived")
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
                <div class="stepper-item @if($redirected_accepted_track || $redirected_rejected_track || $redirected_cancelled_track) completed @endif" id="accepted_progress{{ $redirect_track->code.$redirect_track->id }}">
                    <div class="step-counter
                                                <?php
                    if($redirected_rejected_track)
                        echo "bg-red";
                    elseif($redirected_cancelled_track)
                        echo "bg-yellow";
                    elseif($redirected_queued_track && !$redirected_accepted_track)
                        echo "bg-orange";
                    ?>
                            " id="rejected_progress{{ $redirect_track->code.$redirect_track->id }}">3</div>
                    <div class="step-name text-center" id="rejected_name{{ $redirect_track->code.$redirect_track->id }}"><?php
                        if($redirected_rejected_track)
                            echo 'Declined';
                        elseif($redirected_cancelled_track)
                            echo 'Cancelled';
                        elseif($redirected_queued_track && !$redirected_accepted_track)
                            echo "Queued at <br><b>".$queue_redirected."</b>";
                        else
                            echo "Accepted"
                        ?></div>
                </div>
                <div class="stepper-item @if( ($redirected_travel_track || $redirected_arrived_track || $redirected_notarrived_track) && !$redirected_rejected_track && !$redirected_cancelled_track ) completed @endif" id="departed_progress{{ $redirect_track->code.$redirect_track->id }}">
                    <div class="step-counter">4</div>
                    <div class="step-name">Departed</div>
                </div>
                <div class="stepper-item @if( ($redirected_arrived_track || $redirected_notarrived_track) && !$redirected_rejected_track && !$redirected_cancelled_track ) completed @endif" id="arrived_progress{{ $redirect_track->code.$redirect_track->id }}">
                    <div class="step-counter {{ $redirected_notarrived_track && !$redirected_rejected_track ? "bg-red" : "" }}" id="notarrived_progress{{ $redirect_track->code.$redirect_track->id }}">5</div>
                    @if($redirected_notarrived_track)
                        <div class="step-name not_arrived">Not Arrived</div>
                    @else
                        <div class="step-name" id="arrived_name{{ $redirect_track->code.$redirect_track->id }}">Arrived</div>
                    @endif
                </div>
                <div class="stepper-item @if(($redirected_admitted_track || $redirected_discharged_track) && !$redirected_cancelled_track ) completed @endif" id="admitted_progress{{ $redirect_track->code.$redirect_track->id }}">
                    <div class="step-counter">6</div>
                    <div class="step-name">Admitted</div>
                </div>
                <div class="stepper-item @if($redirected_discharged_track && !$redirected_cancelled_track ) completed @endif" id="discharged_progress{{ $redirect_track->code.$redirect_track->id }}">
                    <div class="step-counter">7</div>
                    <div class="step-name">Discharged</div>
                </div>
            </div>
        @endforeach
    @endif
    @if(count($activities) > 0)
        <?php $first = 0;
        $latest_act = \App\Activity::where('code',$row->code)->latest('created_at')->first();
        ?>
        <div class="row">
            <div class="tracking col-sm-12">
                <div class="table-responsive">
                    <table class="table table-striped" style="font-size: 9pt;">
                        <thead class="prepend_from_firebase{{ $row->code }}" id="prepend_from_websocket{{ $row->code }}"></thead>
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
                                            @if($act->remarks)
                                                <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                            @endif
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
                                    <td>{{ date('M d, Y h:i A',strtotime($act->created_at)) }}</td>
                                    <td>
                                        <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> arrived at <span class="txtHospital">{{ $new_facility }}</span>.
                                        <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                    </td>
                                </tr>
                            @elseif($act->status=='admitted')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->created_at)) }}</td>
                                    <td>
                                        <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}</span> was admitted at <span class="txtHospital">{{ $new_facility }}</span>.
                                        <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                    </td>
                                </tr>
                            @elseif($act->status=='discharged')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->created_at)) }}</td>
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
                                    <td>{{ date('M d, Y h:i A',strtotime($act->created_at)) }}</td>
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
                                            {{ $cancel_doctor }}.
                                                            </span>
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
                            @elseif($act->status=='form_updated')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                    <td>
                                        <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}'s</span> form was updated by <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $old_facility }}</span>
                                        <span class="remarks">Remarks: {{ $act->remarks }}</span>
                                    </td>
                                </tr>
                            @elseif($act->status=='queued')
                                <tr @if($first==1) class="toggle toggle{{ $row->id }}" @endif>
                                    <td>{{ date('M d, Y h:i A',strtotime($act->date_referred)) }}</td>
                                    <td>
                                        <span class="txtPatient">{{ $act_name->fname }} {{ $act_name->mname }} {{ $act_name->lname }}'s</span> was queued by <span class="txtDoctor">Dr. {{ $act->md_name }}</span> of <span class="txtHospital">{{ $new_facility }}</span>
                                        <span class="remarks">Remarks: Queued at <b>{{ $act->remarks }}</b></span>
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