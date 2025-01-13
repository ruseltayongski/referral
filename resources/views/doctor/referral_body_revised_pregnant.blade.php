<?php
$user = Session::get('auth');
?>

<style>

    .glasgow-table {
        border: 1px solid lightgrey;
        width: 100%;
    }

     .glasgow-dot {
        background-color: #494646;
        border-radius: 50%;
        display: inline-block;
    }
    #glasgow_table_1, tr td:nth-child(1) {width: 35%;}
    #glasgow_table_2 tr td:nth-child(2) {width: 35%;}  

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
</style>
@include('include.header_form')
<table class="table table-striped form-label">
    <tr>
        <th colspan="4">REFERRAL RECORD</th>
    </tr>
    <tr>
        <td>Who is Referring</td>
        <td>Record Number: <span class="record_no form-details">{{ $form['pregnant']->record_no }}</span></td>
        <td colspan="2">Referred Date: <span class="referred_date form-details">{{ $form['pregnant']->referred_date }}</span></td>
    </tr>
    <tr>
        <td colspan="2">Referring Name: <span class="md_referring form-details">{{ $form['pregnant']->md_referring }}</span></td>
        <td colspan="2">Arrival Date: </td>
    </tr>
    <tr>
        <td colspan="4">Contact # of referring MD/HCW: <span class="referring_md_contact form-details">{{ $form['pregnant']->referring_md_contact }}</span></td>
    </tr>
    <tr>
        <td colspan="4">Referring Facility: <span class="referring_facility form-details">{{ $form['pregnant']->referring_facility }}</span></td>
    </tr>
    <tr>
        <td colspan="4">Facility Contact #: <span class="referring_contact form-details">{{ $form['pregnant']->referring_contact }}</span></td>
    </tr>
    <tr>
        <td colspan="4">Accompanied by the Health Worker: <span class="health_worker form-details">{{ $form['pregnant']->health_worker }}</span></td>
    </tr>
    <tr>
        <td colspan="2">Referred To: <span class="referred_name form-details">{{ $form['pregnant']->referred_facility }}</span></td>
        <td colspan="2">Department: <span class="department_name form-details">{{ $form['pregnant']->department }}</span></td>
    </tr>
    <tr>
        <td colspan="4">Covid Number: <span class="covid_number form-details">{{ $form['pregnant']->covid_number }}</span></td>
    </tr>
    <tr>
        <td colspan="4">Clinical Status: <span class="clinical_status form-details" style="text-transform: capitalize;">{{ $form['pregnant']->refer_clinical_status }}</span></td>
    </tr>
    <tr>
        <td colspan="4">Surveillance Category: <span class="surveillance_category form-details" style="text-transform: capitalize;">{{ $form['pregnant']->refer_sur_category }}</span></td>
    </tr>
</table>

<div class="row">
    <div class="col-sm-6">
        <table class="table bg-warning">
            <tr class="bg-gray">
                <th colspan="4">WOMAN</th>
            </tr>
            <tr>
                <td colspan="3">Name: <span class="woman_name form-details">{{ $form['pregnant']->woman_name }}</span></td>
                <td>Age: <span class="patient_age form-details"> {{ $form['pregnant']->woman_age }}</span><br><small><i>(at time of referral)</i></small></td>
            </tr>
            <tr>
                <td colspan="4">Address: <span class="woman_address form-details">{{ $form['pregnant']->patient_address }}</span></td>
            </tr>
            <tr>
                <td colspan="4">
                    Main Reason for Referral: <span class="woman_reason form-details">{!! nl2br($form['pregnant']->woman_reason) !!}</span>
                </td>
            </tr>
            <tr>
                <td colspan="4">Major Findings (Clinica and BP,Temp,Lab)
                    <br />
                    <span class="woman_major_findings form-details">{!! nl2br($form['pregnant']->woman_major_findings) !!}</span>
                </td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr class="bg-gray">
                <td colspan="4">Treatments Give Time</td>
            </tr>
            <tr>
                <td colspan="4">Before Referral: <span class="woman_before_treatment form-details">{{ $form['pregnant']->woman_before_treatment }}</span> - <span class="woman_before_given_time form-details">{{ $form['pregnant']->woman_before_given_time }}</span></td>
            </tr>
            <tr>
                <td colspan="4">During Transport: <span class="woman_during_transport form-details">{{ $form['pregnant']->woman_during_transport }}</span> - <span class="woman_transport_given_time form-details">{{ $form['pregnant']->woman_transport_given_time }}</span></td>
            </tr>
            <tr>
                <td colspan="4">Information Given to the Woman and Companion About the Reason for Referral
                    <br />
                    <span class="woman_information_given form-details">{!! nl2br($form['pregnant']->woman_information_given) !!}</span>
                </td>
            </tr>
            @if(isset($icd[0]))
                <tr>
                    <td colspan="4">
                        ICD-10 Code and Description:
                        <br />
                        @foreach($icd as $i)
                            <span class="reason form-details">{{ $i->code }} - {{ $i->description }}</span><br>
                        @endforeach
                    </td>
                </tr>
            @endif
            @if(isset($form['pregnant']->notes_diagnoses))
                <tr>
                    <td colspan="4">
                        Diagnosis/Impression:
                        <br />
                        <span class="diagnosis form-details">{!! nl2br($form['pregnant']->notes_diagnoses) !!}</span>
                    </td>
                </tr>
            @endif
            @if(isset($form['pregnant']->other_diagnoses))
                <tr>
                    <td colspan="4">
                        Other Diagnoses:
                        <br />
                        <span class="reason form-details">{{ $form['pregnant']->other_diagnoses }}</span>
                    </td>
                </tr>
            @endif
            @if(isset($reason))
                <tr>
                    <td colspan="4">
                        Reason for referral:
                        <br />
                        <span class="reason form-details">{{ $reason->reason }}</span>
                    </td>
                </tr>
            @endif
            @if(isset($form['pregnant']->other_reason_referral))
                <tr>
                    <td colspan="4">
                        Reason for referral:
                        <br />
                        <span class="reason form-details">{{ $form['pregnant']->other_reason_referral }}</span>
                    </td>
                </tr>
            @endif

            @if(isset($file_path))
                <tr>
                    <td colspan="6">
                        @if(count($file_path) > 1) File Attachments: @else File Attachment: @endif <br>
                        @for($i = 0; $i < count($file_path); $i++)
                            <a href="{{ $file_path[$i] }}" id="file_download" class="reason" target="_blank" style="font-size: 12pt;" download>{{ $file_name[$i] }}</a>
                            @if($i + 1 != count($file_path))
                                ,&nbsp;
                            @endif
                        @endfor
                    </td>
                </tr>
            @endif
            {{--@if(isset($file_path))--}}
                {{--<tr>--}}
                    {{--<td colspan="4">--}}
                        {{--File Attachment:--}}
                        {{--<a href="{{ asset($file_path) }}" target="_blank" class="reason" style="font-size: 12pt;" download>{{ $file_name }}</a>--}}
                    {{--</td>--}}
                {{--</tr>--}}
            {{--@endif--}}

        </table>
    </div>

    <div class="col-sm-6">
        <table class="table bg-warning">
            <tr class="bg-gray">
                <th colspan="4">BABY</th>
            </tr>
            <tr>
                <td colspan="2">Name: <span class="baby_name form-details">{{ $form['baby']->baby_name }}</span></td>
                <td>Date of Birth: <span class="baby_dob form-details">{{ $form['baby']->baby_dob }}</span></td>
            </tr>
            <tr>
                <td colspan="2">Birth Weight: <span class="weight form-details">{{ $form['baby']->weight }}</span></td>
                <td>Gestational Age: <span class="gestational_age form-details">{{ $form['baby']->gestational_age }}</span></td>
            </tr>
            <tr>
                <td colspan="4">
                    Main Reason for Referral: <span class="baby_reason form-details">{{ $form['baby']->baby_reason }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="4">Major Findings (Clinical and BP,Temp,Lab)
                    <br />
                    <span class="baby_major_findings form-details">{!! nl2br($form['baby']->baby_major_findings) !!}</span>
                </td>
            </tr>
            <tr>
                <td colspan="4">Last (Breast) Feed (Time): <span class="baby_last_feed form-details">{{ $form['baby']->baby_last_feed }}</span></td>
            </tr>
            <tr class="bg-gray">
                <td colspan="4">Treatments Give Time</td>
            </tr>
            <tr>
                <td colspan="4">Before Referral: <span class="baby_before_treatment form-details">{{ $form['baby']->baby_before_treatment }}</span> - <span class="baby_before_given_time form-details">{{ $form['baby']->baby_before_given_time }}</span></td>
            </tr>
            <tr>
                <td colspan="4">During Transport: <span class="baby_during_transport form-details">{{ $form['baby']->baby_during_transport }}</span> - <span class="baby_transport_given_time form-details">{{ $form['baby']->baby_transport_given_time }}</span></td>
            </tr>
            <tr>
                <td colspan="4">Information Given to the Woman and Companion About the Reason for Referral
                    <br />
                    <span class="baby_information_given form-details">{!! $form['baby']->baby_information_given !!}</span>
                </td>
            </tr>
        </table>
    </div>
</div>

<hr style="border-top: 1px solid #ccc;">
<div class="row">
    <div class="col-sm-6">
    <div class="table-responsive">
        <table class="table bg-warning">
            <tr class="bg-gray">
                <td colspan="4">Past Medical History</td>
            </tr>
            <tr> 
                <td colspan="4">Commorbidities: <span class="woman_commorbidities_treatment form-details"></span> - <span class="woman_before_given_time form-details">{{ implode(",",$commordities_arr) }}</span></td>       
            </tr>
            <tr>    
                <td colspan="4">Allergies: <span class="woman_allergies_food form-details"></span> - <span class="woman_before_given_time form-details">{{ implode(",",$allergies_arr) }}</span></td>
            </tr>
        
            <tr>
                <td colspan="4">Heredofamilial: <span class="woman_allergies_treatment form-details"></span> - <span class="woman_before_given_time form-details">{{ implode(",",$heredofamilial_arr) }}</span></td>
            </tr>
            <tr>
                <td colspan="4">Previous Hospitalization: <span class="woman_allergies_treatment form-details"></span> - <span class="woman_before_given_time form-details">{{ $past_medical_history->previous_hospitalization }}</span></td>
            </tr>

            <tr class="bg-gray">
                <td colspan="4">Personal and Social History </td>
            </tr>
            <tr>
                <td colspan="2">Smoking:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->smoking}}</span></td> 
                <td colspan="4">Sticks per Day:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->smoking_sticks_per_day}}</span></td> 
            </tr>
            <tr>
                <td colspan="2">Year Quit:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->smoking_quit_year}}</span></td> 
                <td colspan="4">Smoking Remarks:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->smoking_remarks}}</span></td> 
            </tr>
            <tr>
                <td colspan="2">Alcohol:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->alcohol_drinking}}</span></td> 
                <td colspan="4">Liquor Type:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->alcohol_liquor_type}}</span></td> 
            </tr>
            <tr>
                <td colspan="2">Year Quit:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->alcohol_drinking_quit_year}}</span></td> 
                <td colspan="4">Alcohol bottles per day:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->alcohol_bottles_per_day}}</span></td> 
            </tr>
            <tr>
                <td colspan="2">Illicit drugs:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->Illicit_drugs}}</span></td> 
                <td colspan="4">Illicit drugs taken:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->illicit_drugs_taken}}</span></td> 
            </tr>
            <tr>
                <td colspan="4">Quit year:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->Illicit_drugs_quit_year}}</span></td> 
            </tr>
            <tr>
                <td colspan="4">Current Medication:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->current_medications}}</span></td> 
            </tr>
            <tr class="bg-gray">
                <td colspan="4">Pertinent Laboratory and Other Ancillary Procedures </td>
            </tr>
            <tr>   
                <td colspan="4">Laboratory:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(",",$pertinent_arr)}}</span></td> 
            </tr>

            
            <tr class="bg-gray">
                <td colspan="4">Nutritional Status</td>
            </tr>
            <tr>
            <td colspan="2">Diet:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$nutritional_status->diet}}</span></td>
            <td colspan="4">Specific Diet:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$nutritional_status->specify_diets}}</span></td>
            </tr>

            <tr class="bg-gray">
                <td colspan="4">Latest Vital Signs</td>
            </tr>
            <tr>
            <td colspan="2">Teamperature:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->temperature}}</span></td>
            <td colspan="4">Pulse Rate:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->pulse_rate}}</span></td>
            </tr>
            <tr>
            <td colspan="2">Respiratory Rate:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->respiratory_rate}}</span></td>
            <td colspan="4">Blood Pressure:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->blood_pressure}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Oxygen Saturation:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->oxygen_saturation}}</span></td>
            </tr>

            <tr class="bg-gray">
                <td colspan="4">Glasgow Coma Scale</td>
            </tr> 
            <td colspan="4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><b>1</b></th>
                            <th><b>2</b></th>
                            <th><b>3</b></th>
                            <th><b>4</b></th>
                            <th><b>5</b></th>
                            <th><b>6</b></th>
                            <th><b>7</b></th>
                            <th><b>8</b></th>
                            <th><b>9</b></th>
                            <th><b>10</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="glasgow-dot" style="height: 6px; width: 6px;"></span></td>
                            <td><span class="glasgow-dot" style="height: 10px; width: 10px;"></span></td>
                            <td><span class="glasgow-dot" style="height: 13px; width: 13px;"></span></td>
                            <td><span class="glasgow-dot" style="height: 16px; width: 16px;"></span></td>
                            <td><span class="glasgow-dot" style="height: 20px; width: 20px;"></span></td>
                            <td><span class="glasgow-dot" style="height: 24px; width: 24px;"></span></td>
                            <td><span class="glasgow-dot" style="height: 28px; width: 28px;"></span></td>
                            <td><span class="glasgow-dot" style="height: 32px; width: 32px;"></span></td>
                            <td><span class="glasgow-dot" style="height: 36px; width: 36px;"></span></td>
                            <td><span class="glasgow-dot" style="height: 40px; width: 40px;"></span></td>
                        </tr>
                    </tbody>
                </table>
            </td> 
            <tr>
            <td colspan="2"><b>Pupil Size Chart:</b><span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->pupil_size_chart}}</span></td><br><br>
            <td colspan="4">Motor Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->motor_response}}</span></td>
            </tr>
            <tr>
            <td colspan="2">Verbal Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->verbal_response}}</span></td>
            <td colspan="4">Eye Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->eye_response}}</span></td>
            </tr>
            <tr>
            <td colspan="4">GSC Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->gsc_score}}</span></td>
            </tr>
        </table>
    </div>
    </div>
    
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
                $contraceptives_arr = explodeToArray($obstetric_and_gynecologic_history->contraceptive_history);
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
    
    <div class="col-sm-6">
    <div class="table-responsive">
        <table class="table bg-warning">
            <tr class="bg-gray">
                <td colspan="4">Review of Systems </td>
            </tr>
            <tr>
            <td colspan="4">Skin:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_skin)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Head:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_head)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Eyes:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_eyes)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Ears:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_ears)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Nose/Sinuses:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_nose)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Mouth/Throat:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_mouth)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Neck:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_neck)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Breast:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_breast)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Respiratory/Cardiac:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_respiratory)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Gastrointestinal:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_gastrointestinal)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Urinary:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_urinary)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Peripheral Vascular:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_peripheral)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Musculoskeletal:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_musculoskeletal)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Neurologic:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_neurologic)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Hematologic:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_hematologic)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Endocrine:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_endocrine)}}</span></td>
            </tr>
            <tr>
            <td colspan="4">Psychiatric:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_psychiatric)}}</span></td>
            </tr>

        <tr class="bg-gray">
                <td colspan="4">Obstetric and Gynecologic History </td>
        </tr>
        <tr>
            <td colspan="2">Menarche: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->menarche}}</span></td>
            <td>Menopause: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->menopausal_age}}</span></td>
        </tr>
        <tr>
            <td colspan="2">Menstrual Cycle: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->menstrual_cycle}}</span></td>
            <td>Menstrual Duration: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->menstrual_cycle_duration}}</span></td>
        </tr>
        <tr>
            <td colspan="2">Menstrual Pads per Day: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->menstrual_cycle_padsperday}}</span></td>
            <td>Menstrual Medication: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->menstrual_cycle_medication}}</span></td>
        </tr>

        <tr>
            <td colspan="4">Contraceptives:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(",",$contraceptives_arr)}}</span></td>
            </tr>
        <tr>
                <td colspan="4"><i>Parity</i></td>
        </tr>
        <tr>
            <td colspan="2">G:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_g}}</span></td> 
            <td>P:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_p}}</span></td> 
        </tr>
        <tr>
            <td colspan="2">FT:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_ft}}</span></td> 
            <td>PT:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_pt}}</span></td> 
        </tr>
        <tr>
            <td colspan="2">A:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_a}}</span></td> 
            <td>L:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_l}}</span></td> 
        </tr>
        <tr>
            <td colspan="2">LNMP:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_lnmp}}</span></td> 
            <td>EDC:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_edc}}</span></td> 
        </tr>
        <tr>
            <td colspan="4"><i>AOG</i></td>
        </tr>
        <tr>
            <td colspan="2">LNMP:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->aog_lnmp}}</span></td> 
            <td>EUTZ:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->aog_eutz}}</span></td> 
        </tr>
        <tr>
            <td colspan="4">Prenatal History:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->prenatal_history}}</span></td> 
        </tr>

        </table>
    </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr style="font-size: 10pt;">
                        <th class="text-center" style="width:50%;">Pregnancy Order</th>
                        <th class="text-center" style="width:20%;">Year of Birth</th>
                        <th class="text-center">Gestation Completed</th>
                        <th class="text-center">Pregnancy Outcome</th>
                        <th class="text-center">Place of Birth</th>
                        <th class="text-center">Biological Sex</th>
                        <th class="text-center" style="width:50%;">Birth Weight</th>
                        <th class="text-center">Present Status</th>
                        <th class="text-center">Complication(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($pregnancy) && count($pregnancy) > 0)
                        @foreach($pregnancy as $record)
                            <tr>
                                <td>{{ $record['pregnancy_order'] }}</td>
                                <td>{{ $record['pregnancy_year'] }}</td>
                                <td>{{ $record['pregnancy_gestation_completed'] }}</td>
                                <td>{{ $record['pregnancy_outcome'] }}</td>
                                <td>{{ $record['pregnancy_place_of_birth'] }}</td>
                                <td>{{ $record['pregnancy_sex'] }}</td>
                                <td>{{ $record['pregnancy_birth_weight'] }}</td>
                                <td>{{ $record['pregnancy_present_status'] }}</td>
                                <td>{{ $record['pregnancy_complication'] }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center">No data available.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>




<table class="table table-striped col-sm-6"></table>
<div class="clearfix"></div>
<hr />
<button class="btn btn-default btn-flat" data-dismiss="modal" id="closeReferralForm{{$form['pregnant']->code}}"><i class="fa fa-times"></i> Close</button>
<div class="pull-right">
    @if(!($cur_status == 'referred' || $cur_status == 'redirected' || $cur_status == 'transferred' || $cur_status == 'rejected') && $form['pregnant']->department_id === 5 && $user->id == $form['pregnant']->md_referring_id)
        <button class="btn-sm bg-success btn-flat" id="telemedicine" onclick="openTelemedicine('{{ $form['pregnant']->tracking_id }}','{{ $form['pregnant']->code }}','{{ $form['pregnant']->action_md }}','{{ $form['pregnant']->referring_md }}');"><i class="fa fa-camera"></i> Telemedicine</button>
        <a href="{{ url('doctor/print/prescription').'/'.$id }}" target="_blank" type="button" style="color: black;" class="btn btn-sm bg-warning btn-flat" id="prescription"><i class="fa fa-file-zip-o"></i> Prescription</a>
    @endif
    @if(($cur_status == 'transferred' || $cur_status == 'referred' || $cur_status == 'redirected') && $user->id == $form['pregnant']->md_referring_id)
        <button class="btn btn-primary btn-flat button_option edit_form_revised_btn" data-toggle="modal" data-target="#editReferralForm" data-id="{{ $id }}" data-type="pregnant" data-referral_status="{{ $referral_status }}"><i class="fa fa-edit"></i> Edit Form</button>
    @endif
    @if($cur_status == 'cancelled' && $user->id == $form['pregnant']->md_referring_id)
    <button class="btn btn-danger btn-flat button_option undo_cancel_btn" data-toggle="modal" data-target="#undoCancelModal" data-id="{{ $id }}"><i class="fa fa-times"></i> Undo Cancel</button>
    @endif
    @if($referral_status == 'referred' || $referral_status == 'redirected')
        @if(!$form['pregnant']->getAttribute('telemedicine'))
            <button class="btn btn-primary btn-flat queuebtn" data-toggle="modal" data-target="#queueModal" data-id="{{ $id }}"><i class="fa fa-pencil"></i> Update Queue </button>
            <button class="btn btn-info btn_call_request btn-flat btn-call button_option" data-toggle="modal" data-target="#sendCallRequest"><i class="fa fa-phone"></i> Call Request</button>
            <button class="btn btn-danger btn-flat button_option" data-toggle="modal" data-target="#rejectModal"><i class="fa fa-line-chart"></i> Recommend to Redirect</button>
        @endif
    <button class="btn btn-success btn-flat button_option" data-toggle="modal" data-target="#acceptFormModal"><i class="fa fa-check"></i> Accept</button>
    @endif
    <a href="{{ url('generate-pdf').'/'.$patient_id .'/'.$id . '/' . 'pregnant' }}" target="_blank" class="btn-refer-normal btn btn-sm btn-warning btn-flat"><i class="fa fa-print"></i> Print Form</a>
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
        var url = "<?php echo asset('api/video/call'); ?>";
        var json = {
            "_token" : "<?php echo csrf_token(); ?>",
            "tracking_id" : tracking_id,
            "code" : code,
            "action_md" : action_md,
            "referring_md" : referring_md,
            "trigger_by" : "{{ $user->id }}",
            "form_type" : "pregnant"
        };
        $.post(url,json,function(){

        });
        var windowName = 'NewWindow'; // Name of the new window
        var windowFeatures = 'width=600,height=400'; // Features for the new window (size, position, etc.)
        var newWindow = window.open("{{ asset('doctor/telemedicine?id=') }}"+tracking_id+"&code="+code+"&form_type=pregnant&referring_md=yes", windowName, windowFeatures);
        if (newWindow && newWindow.outerWidth) {
            // If the window was successfully opened, attempt to maximize it
            newWindow.moveTo(0, 0);
            newWindow.resizeTo(screen.availWidth, screen.availHeight);
        }
    }
</script>