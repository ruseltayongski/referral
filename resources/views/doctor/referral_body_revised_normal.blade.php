<?php $user = Session::get('auth'); ?>
<style>
    .mobile-view {
        display: none;
        visibility: hidden;
    }

    @media only screen and (max-width: 720px) {
        .file-upload {
            background-color: #ffffff;
            width: 300px;
            margin: 0 auto;
            padding: 20px;
        }

        .web-view {
            display: none;
            visibility: hidden;
        }

        .mobile-view {
            display: block;
            visibility: visible;
        }
    }

    #telemedicine {
        border-color:#00a65a;
        border: none;
        padding: 7px;
    }
    #telemedicine:hover {
        background-color: lightgreen;
    }
</style>

@include('include.header_form', ['optionHeader' => 'referred'])<br>

<table class="table table-striped form-label referral-table">
    <tr>
        <td colspan="6" class="form-label">Name of Referring Facility: <span class="referring_name form-details">{{ $form->referring_name }}</span></td>
    </tr>
    <tr>
        <td colspan="6">Facility Contact #: <span class="referring_contact form-details">{{ $form->referring_contact }}</span></td>
    </tr>
    <tr>
        <td colspan="6">Address: <span class="referring_address form-details">{{ $form->referring_address }}</span></td>
    </tr>
    <tr>
        <td colspan="3">Referred to: <span class="referred_name form-details">{{ $form->referred_name }}</span></td>
        <td colspan="3">Department: <span class="department_name form-details">{{ $form->department }}</span></td>
    </tr>
    <tr>
        <td colspan="6">Address: <span class="referred_address form-details">{{ $form->referred_address }}</span></td>
    </tr>
    <tr>
        <td colspan="3">Date/Time Referred (ReCo): <span class="date_referred form-details">{{ $form->date_referred }}</span></td>
        <td colspan="3">Date/Time Transferred: <span class="date_transferred form-details"></span></td>
    </tr>
    <tr>
        <td colspan="3">Name of Patient: <span class="patient_name form-details">{{ $form->patient_name }}</span></td>
        <td>Age: <span class="patient_age form-details">
            @if($age_type == "y")
                @if($patient_age == 1)
                    {{ $patient_age }} year old
                @else
                    {{ $patient_age }} years old
                @endif
            @elseif($age_type == "m")
                @if($patient_age['month'])
                    {{ $patient_age['month'] }} mo,
                @else
                    {{ $patient_age['month'] }} mos,
                @endif
                @if($patient_age['days'] == 1)
                    {{ $patient_age['days'] }} day old
                @else
                    {{ $patient_age['days'] }} days old
                @endif
            @endif
            </span><br>
            <small><i>(at time of referral)</i></small>
        </td>
        <td>Sex: <span class="patient_sex form-details">{{ $form->patient_sex }}</span></td>
        <td>Status: <span class="patient_status form-details">{{ $form->patient_status }}</span></td>
    </tr>
    <tr>
        <td colspan="6">Address: <span class="patient_address form-details">{{ $form->patient_address }}</span></td>
    </tr>
    <tr>
        <td colspan="3">PhilHealth status: <span class="phic_status form-details">{{ $form->phic_status }}</span></td>
        <td colspan="3">PhilHealth #: <span class="phic_id form-details">{{ $form->phic_id }}</span></td>
    </tr>
    <tr>
        <td colspan="6">Covid Number: <span class="covid_number form-details">{{ $form->covid_number }}</span></td>
    </tr>
    <tr>
        <td colspan="6">Clinical Status: <span class="clinical_status form-details" style="text-transform: capitalize;">{{ $form->refer_clinical_status }}</span></td>
    </tr>
    <tr>
        <td colspan="6">Surveillance Category: <span class="surveillance_category form-details" style="text-transform: capitalize;">{{ $form->refer_sur_category }}</span></td>
    </tr>
    <tr>
        <td colspan="6">
            Case Summary (pertinent Hx/PE, including meds, labs, course etc.):
            <br />
            <span class="case_summary form-details">{!! nl2br($form->case_summary) !!}</span>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            Summary of ReCo (pls. refer to ReCo Guide in Referring Patients Checklist):
            <br />
            <span class="reco_summary form-details">{!! nl2br($form->reco_summary) !!}</span>
        </td>
    </tr>
    @if(isset($icd[0]))
        <tr>
            <td colspan="6">
                ICD-10 Code and Description:
                <br />
                @foreach($icd as $i)
                    <span class="reason form-details">{{ $i->code }} - {{ $i->description }}</span><br>
                @endforeach
            </td>
        </tr>
    @endif
    @if(isset($form->diagnosis))
        <tr>
            <td colspan="6">
                Diagnosis/Impression:
                <br />
                <span class="diagnosis form-details">{!! nl2br($form->diagnosis) !!}</span>
            </td>
        </tr>
    @endif
    @if(isset($form->other_diagnoses))
        <tr>
            <td colspan="6">
                Other Diagnoses:
                <br />
                <span class="reason form-details">{{ $form->other_diagnoses }}</span>
            </td>
        </tr>
    @endif
    @if(isset($reason))
        <tr>
            <td colspan="6">
                Reason for referral:
                <br />
                <span class="reason form-details">{{ $reason->reason }}</span>
            </td>
        </tr>
    @endif
    @if(isset($form->other_reason_referral))
        <tr>
            <td colspan="6">
                Reason for referral:
                <br />
                <span class="reason form-details">{{ $form->other_reason_referral }}</span>
            </td>
        </tr>
    @endif
    @if(isset($file_path))
        <tr>
            <td colspan="6">
                @if(count($file_path) > 1) File Attachments: @else File Attachment: @endif
                @for($i = 0; $i < count($file_path); $i++)
                    <a href="{{ $file_path[$i] }}" id="file_download" class="reason" target="_blank" style="font-size: 12pt;" download>{{ $file_name[$i] }}</a>
                    @if($i + 1 != count($file_path))
                        ,&nbsp
                    @endif
                @endfor
                {{--<a href="{{ asset($file_path) }}" id="file_download" class="reason" target="_blank" style="font-size: 12pt;" download>{{ $file_name }}</a>--}}
            </td>
        </tr>
    @endif
    <tr>
        <td colspan="6">
            Name of referring MD/HCW: <span class="referring_md form-details">{{ $form->md_referring }}</span>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            Contact # of referring MD/HCW: <span class="referring_md_contact form-details">{{ $form->referring_md_contact }}</span>
        </td>
    </tr>
    <tr>
        <td colspan="6">Name of referred MD/HCW- Mobile Contact # (ReCo): <span class="referred_md form-details">{{ $form->md_referred }}</span></td>
    </tr>
    <?php 
        function explodeToArray($string){
            $array = explode(',',$string);

            $filteredOptions = array_filter($array, function ($value) {
                return $value !== "Select All";
            });

                return $filteredOptions;
        }
        $commordities_arr = explodeToArray($past_medical_history->commordities);
        $allergies_arr = explodeToArray($past_medical_history->allergies);
        $heredofamilial_arr = explodeToArray($past_medical_history->heredofamilial_diseases);
        $pertinent_arr = explodeToArray($pertinent_laboratory->pertinent_laboratory_and_procedures);
        $review_skin = explodeToArray($review_of_system->skin);
        $review_head = explodeToArray($review_of_system->head);
        $review_eyes = explodeToArray($review_of_system->eyes);
        $review_ears = explodeToArray($review_of_system->ears);
        $review_nose = explodeToArray($review_of_system->nose_or_sinuses);
        $review_mouth = explodeToArray($review_of_system->mouth_or_throat);
        $review_neck = explodeToArray($review_of_system->neck);
        $review_breast = explodeToArray($review_of_system->breast);
        $review_respiratory = explodeToArray($review_of_system->respiratory_or_cardiac);
        $review_gastrointestinal = explodeToArray($review_of_system->gastrointestinal);
        $review_urinary = explodeToArray($review_of_system->urinary);
        $review_peripheral = explodeToArray($review_of_system->peripheral_vascular);
        $review_musculoskeletal = explodeToArray($review_of_system->musculoskeletal);
        $review_neurologic = explodeToArray($review_of_system->neurologic);
        $review_hematologic = explodeToArray($review_of_system->hematologic);
        $review_endocrine = explodeToArray($review_of_system->endocrine);
        $review_psychiatric = explodeToArray($review_of_system->psychiatric)
    ?>
    <tr class="bg-gray">
        <td colspan="6">Past Medical History</td>
    </tr>
    <tr> 
        <td colspan="6">Commorbidities: <span class="woman_commorbidities_treatment form-details"></span> - <span class="woman_before_given_time form-details">{{ implode(",",$commordities_arr) }}</span></td>       
    </tr>
    <tr>    
        <td colspan="6">Allergies: <span class="woman_allergies_food form-details"></span> - <span class="woman_before_given_time form-details">{{ implode(",",$allergies_arr) }}</span></td>
    </tr>
        
    <tr>
        <td colspan="6">Heredofamilial: <span class="woman_allergies_treatment form-details"></span> - <span class="woman_before_given_time form-details">{{ implode(",",$heredofamilial_arr) }}</span></td>
    </tr>
    <tr>
        <td colspan="6">Previous Hospitalization: <span class="woman_allergies_treatment form-details"></span> - <span class="woman_before_given_time form-details">{{ $past_medical_history->previous_hospitalization }}</span></td>
    </tr>
    
    <tr class="bg-gray">
        <td colspan="6">Personal and Social History </td>
    </tr>
    <tr>
        <td colspan="3">Smoking:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->smoking}}</span></td> 
        <td colspan="3">Sticks per Day:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->smoking_sticks_per_day}}</span></td> 
    </tr>
    <tr>
        <td colspan="3">Year Quit:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->smoking_quit_year}}</span></td> 
        <td colspan="3">Smoking Remarks:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->smoking_remarks}}</span></td> 
    </tr>
    <tr>
        <td colspan="3">Alcohol:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->alcohol_drinking}}</span></td> 
        <td colspan="3">Liquor Type:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->alcohol_liquor_type}}</span></td> 
    </tr>
        <tr>
        <td colspan="3">Year Quit:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->alcohol_drinking_quit_year}}</span></td> 
        <td colspan="3">Alcohol bottles per day:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->alcohol_bottles_per_day}}</span></td> 
    </tr>
        <tr>
        <td colspan="3">Illicit drugs:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->Illicit_drugs}}</span></td> 
        <td colspan="3">Illicit drugs taken:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->illicit_drugs_taken}}</span></td> 
    </tr>
        <tr>
        <td colspan="6">Quit year:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->Illicit_drugs_quit_year}}</span></td> 
    </tr>
    <tr>
        <td colspan="6">Current Medication:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->current_medications}}</span></td> 
    </tr>

    <tr class="bg-gray">
        <td colspan="6">Pertinent Laboratory and Other Ancillary Procedures </td>
    </tr>
    <tr>   
        <td colspan="6">Laboratory:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(",",$pertinent_arr)}}</span></td> 
    </tr>

    <tr class="bg-gray">
        <td colspan="6">Review of Systems </td>
    </tr>
    <tr>
        <td colspan="6">Skin:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_skin)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Head:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_head)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Eyes:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_eyes)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Ears:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_ears)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Nose/Sinuses:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_nose)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Mouth/Throat:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_mouth)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Neck:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_neck)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Breast:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_breast)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Respiratory/Cardiac:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_respiratory)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Gastrointestinal:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_gastrointestinal)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Urinary:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_urinary)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Peripheral Vascular:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_peripheral)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Musculoskeletal:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_musculoskeletal)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Neurologic:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_neurologic)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Hematologic:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_hematologic)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Endocrine:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_endocrine)}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Psychiatric:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_psychiatric)}}</span></td>
    </tr>

    <tr class="bg-gray">
        <td colspan="6">Nutritional Status</td>
    </tr>
    <tr>
        <td colspan="3">Diet:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$nutritional_status->diet}}</span></td>
        <td colspan="3">Specific Diet:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$nutritional_status->specify_diets}}</span></td>
    </tr>

    <tr class="bg-gray">
        <td colspan="6">Latest Vital Signs</td>
    </tr>
    <tr>
        <td colspan="3">Teamperature:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->temperature}}</span></td>
        <td colspan="3">Pulse Rate:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->pulse_rate}}</span></td>
    </tr>
    <tr>
        <td colspan="3">Respiratory Rate:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->respiratory_rate}}</span></td>
        <td colspan="3">Blood Pressure:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->blood_pressure}}</span></td>
    </tr>
    <tr>
        <td colspan="6">Oxygen Saturation:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->oxygen_saturation}}</span></td>
    </tr>

    <tr class="bg-gray">
        <td colspan="6">Glasgow Coma Scale</td>
    </tr>
    <tr>
        <td colspan="3">Pupil Size Chart:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->pupil_size_chart}}</span></td>
        <td colspan="3">Motor Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->motor_response}}</span></td>
    </tr>
    <tr>
        <td colspan="3">Verbal Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->verbal_response}}</span></td>
        <td colspan="3">Eye Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->eye_response}}</span></td>
    </tr>
    <tr>
        <td colspan="6">GSC Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->gsc_score}}</span></td>
    </tr>
</table>
<hr/>

<button class="btn-sm btn-default btn-flat" data-dismiss="modal" id="closeReferralForm{{$form->code}}"><i class="fa fa-times"></i> Close</button>
<div class="form-fotter pull-right">
    {{--@if(!($cur_status == 'referred' || $cur_status == 'redirected' || $cur_status == 'transferred' || $cur_status == 'rejected') && $form->department_id === 5 && $user->id == $form->md_referring_id)
        <button class="btn-sm bg-success btn-flat" id="telemedicine" onclick="openTelemedicine('{{ $form->tracking_id }}','{{ $form->code }}','{{ $form->action_md }}','{{ $form->referring_md }}');"><i class="fa fa-camera"></i> Telemedicine</button>
        <a href="{{ url('doctor/print/prescription').'/'.$id }}" target="_blank" type="button" style="color: black;" class="btn btn-sm bg-warning btn-flat" id="prescription"><i class="fa fa-file-zip-o"></i> Prescription</a>
    @endif--}}
    @if(($cur_status == 'transferred' || $cur_status == 'referred' || $cur_status == 'redirected') && $user->id == $form->md_referring_id)
        <button class="btn-sm btn-primary btn-flat button_option edit_form_revised_btn" data-toggle="modal" data-target="#editReferralForm" data-id="{{ $id }}" data-type="normal" data-referral_status="{{ $referral_status }}"><i class="fa fa-edit"></i> Edit Form</button>
    @endif
    @if($cur_status == 'cancelled' && $user->id == $form->md_referring_id)
        <button class="btn-sm btn-danger btn-flat button_option undo_cancel_btn" data-toggle="modal" data-target="#undoCancelModal" data-id="{{ $id }}"><i class="fa fa-times"></i> Undo Cancel</button>
    @endif
    @if($referral_status == 'referred' || $referral_status == 'redirected')
        @if(!$form->telemedicine)
            <button class="btn-sm btn-primary btn-flat queuebtn" data-toggle="modal" data-target="#queueModal" data-id="{{ $id }}"><i class="fa fa-pencil"></i> Update Queue</button>
            <button class="btn-sm btn-info btn_call_request btn-flat btn-cal button_option" data-toggle="modal" data-target="#sendCallRequest"><i class="fa fa-phone"></i> Call Request <span class="badge bg-red-active call_count" data-toggle="tooltip" title=""></span> </button>
            <button class="btn-sm btn-danger btn-flat button_option" data-toggle="modal" data-target="#rejectModal"><i class="fa fa-line-chart"></i> Recommend to Redirect</button>
        @endif
        <button class="btn-sm btn-success btn-flat button_option" data-toggle="modal" data-target="#acceptFormModal"><i class="fa fa-check"></i> Accept</button>
    @endif
    <a href="{{ url('generate-pdf').'/'.$patient_id .'/'.$id . '/' . 'normal' }}" target="_blank" class="btn-refer-normal btn btn-sm btn-warning btn-flat"><i class="fa fa-print"></i> Print Form</a>
</div>
<div class="clearfix"></div>

<script>
    function getParameterByName(name) {
        url = window.location.href;
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }
    if(getParameterByName('referredCode')) {
        $("#telemedicine").addClass('hide');
        $(".edit_form_revised_btn").addClass('hide');
    }

    function openTelemedicine(tracking_id, code, action_md, referring_md) {
        console.log("mao");
        var url = "<?php echo asset('api/video/call'); ?>";
        var json = {
            "_token" : "<?php echo csrf_token(); ?>",
            "tracking_id" : tracking_id,
            "code" : code,
            "action_md" : action_md,
            "referring_md" : referring_md,
            "trigger_by" : "{{ $user->id }}",
            "form_type" : "normal"
        };
        $.post(url,json,function(){

        });
        var windowName = 'NewWindow'; // Name of the new window
        var windowFeatures = 'width=600,height=400'; // Features for the new window (size, position, etc.)
        var newWindow = window.open("{{ asset('doctor/telemedicine?id=') }}"+tracking_id+"&code="+code+"&form_type=normal&referring_md=yes", windowName, windowFeatures);
        if (newWindow && newWindow.outerWidth) {
            // If the window was successfully opened, attempt to maximize it
            newWindow.moveTo(0, 0);
            newWindow.resizeTo(screen.availWidth, screen.availHeight);
        }
    }
</script>