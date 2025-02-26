<?php
$user = Session::get('auth');
?>

<style>

    .glasgow-table {
        border: 1px solid lightgrey;
        width: 100%;
    }
    .glasgow-table th.highlight, 
    .glasgow-table td.highlight {
        border: 2px solid orange;
        background-color: #FFFBDA; /* Light red background */
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
                    <span class="woman_major_findings form-details">{!! nl2br($form['pregnant']->woman_major_findings) !!}</span>
                </td>
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
                $pertinent_others = $pertinent_laboratory->lab_procedure_other;
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
                $review_psychiatric = explodeToArray($review_of_system->psychiatric);

                 // PAST MEDICAL HISTORY - NULL CHECKER VALIDATOR
                $commorbidities_null_checker = (!empty($past_medical_history->commordities));
                $allergies_null_checker = (!empty($past_medical_history->allergies));
                $heredofamilial_null_checker = (!empty($past_medical_history->heredofamilial_diseases));
                $validation_checker_past_medical_history = ($commorbidities_null_checker || $allergies_null_checker || $heredofamilial_null_checker);
                // REVIEW OF SYSTEMS - NULL CHECKER VALIDATOR
                $review_skin_null_checker = (!empty($review_of_system->skin));
                $review_head_null_checker = (!empty($review_of_system->head));
                $review_eyes_null_checker = (!empty($review_of_system->eyes));
                $review_ears_null_checker = (!empty($review_of_system->ears));
                $review_nose_null_checker = (!empty($review_of_system->nose_or_sinuses));
                $review_mouth_null_checker = (!empty($review_of_system->mouth_or_throat));
                $review_neck_null_checker = (!empty($review_of_system->neck));
                $review_breast_null_checker = (!empty($review_of_system->breast));
                $review_respiratory_null_checker = (!empty($review_of_system->respiratory_or_cardiac));
                $review_gastrointestinal_null_checker = (!empty($review_of_system->gastrointestinal));
                $review_urinary_null_checker = (!empty($review_of_system->urinary));
                $review_peripheral_null_checker = (!empty($review_of_system->peripheral_vascular));
                $review_musculoskeletal_null_checker = (!empty($review_of_system->musculoskeletal));
                $review_neurologic_null_checker = (!empty($review_of_system->neurologic));
                $review_hematologic_null_checker = (!empty($review_of_system->hematologic));
                $review_endocrine_null_checker = (!empty($review_of_system->endocrine));
                $review_psychiatric_null_checker = (!empty($review_of_system->psychiatric));
                $validation_checker_review_of_systems = ($review_skin_null_checker || $review_head_null_checker || $review_eyes_null_checker || $review_ears_null_checker
                || $review_nose_null_checker || $review_mouth_null_checker || $review_neck_null_checker || $review_breast_null_checker || $review_respiratory_null_checker
                || $review_gastrointestinal_null_checker || $review_urinary_null_checker || $review_peripheral_null_checker || $review_musculoskeletal_null_checker
                || $review_neurologic_null_checker || $review_hematologic_null_checker || $review_endocrine_null_checker || $review_psychiatric_null_checker);
                // PERSONAL AND SOCIAL HISTORY
                $smoking_null_checker = (!empty($personal_and_social_history->smoking));
                $smoking_quit_year_null_checker =(!empty($personal_and_social_history->smoking_quit_year));
                $smoking_sticks_per_day_null_checker=(!empty($personal_and_social_history->smoking_sticks_per_day));
                $smoking_remarks_null_checker = (!empty($personal_and_social_history->smoking_remarks));
                $alcohol_dringking_null_checker =(!empty($personal_and_social_history->alcohol_drinking));
                $alcohol_liquor_type_null_checker =(!empty($personal_and_social_history->alcohol_liquor_type));
                $alcohol_dringking_quit_year_null_checker =(!empty($personal_and_social_history->alcohol_drinking_quit_year));
                $alcohol_bottles_per_day_null_checker=(!empty($personal_and_social_history->alcohol_bottles_per_day));
                $illicit_drugs_null_checker=(!empty($personal_and_social_history->illicit_drugs));
                $illicit_drugs_taken_null_checker =(!empty($personal_and_social_history->illicit_drugs_taken));
                $illicit_drugs_quit_year_null_checker =(!empty($personal_and_social_history->Illicit_drugs_quit_year));
                $personal_history_validation_checker = ($smoking_null_checker || $smoking_quit_year_null_checker || $smoking_sticks_per_day_null_checker
                || $smoking_remarks_null_checker || $alcohol_dringking_null_checker || $alcohol_liquor_type_null_checker || $alcohol_dringking_quit_year_null_checker
                || $alcohol_bottles_per_day_null_checker || $illicit_drugs_null_checker || $illicit_drugs_taken_null_checker || $illicit_drugs_quit_year_null_checker);
                // GLASGOCOMA SCALE
                $glasgo_pupil_size_null_checker = (!empty($glasgocoma_scale->pupil_size_chart));
                $glasgo_motor_response_null_checker =(!empty($glasgocoma_scale->motor_response));
                $glasgo_verbal_response_null_checker = (!empty($glasgocoma_scale->verbal_response));
                $glasgo_eye_response_null_checker = (!empty($glasgocoma_scale->eye_response));
                $glasgo_gsc_score_null_checker = (!empty($glasgocoma_scale->gsc_score));
                $glasgocoma_scale_validation_checker = $glasgo_pupil_size_null_checker || $glasgo_motor_response_null_checker || $glasgo_verbal_response_null_checker || $glasgo_eye_response_null_checker || $glasgo_gsc_score_null_checker;
                // OBSTETRIC AND GYNECOLOGIC HISTORY
                $obstetric_and_gynecologic_menarche_null_checker=(!empty($obstetric_and_gynecologic_history->menarche));
                $obstetric_and_gynecologic_menopausal_null_checker = (!empty($obstetric_and_gynecologic_history->menopausal_age));
                $obstetric_and_gynecologic_menstrual_cycle=(!empty($obstetric_and_gynecologic_history->menstrual_cycle));
                $obstetric_and_gynecologic_menstrual_duration=(!empty($obstetric_and_gynecologic_history->menstrual_cycle_duration));
                $obstetric_and_gynecologic_menstrual_padsperday=(!empty($obstetric_and_gynecologic_history->menstrual_cycle_padsperday));
                $obstetric_and_gynecologic_menstrual_medication=(!empty($obstetric_and_gynecologic_history->menstrual_cycle_medication));
                $obstetric_and_gynecologic_constraceptives=(!empty(implode(",",$contraceptives_arr)));
                $obstetric_and_gynecologic_parity_g=(!empty($obstetric_and_gynecologic_history->parity_g));
                $obstetric_and_gynecologic_parity_p = (!empty($obstetric_and_gynecologic_history->parity_p));
                $obstetric_and_gynecologic_parity_ft=(!empty($obstetric_and_gynecologic_history->parity_ft));
                $obstetric_and_gynecologic_parity_pt=(!empty($obstetric_and_gynecologic_history->parity_pt));
                $obstetric_and_gynecologic_parity_a=(!empty($obstetric_and_gynecologic_history->parity_a));
                $obstetric_and_gynecologic_parity_l=(!empty($obstetric_and_gynecologic_history->parity_l));
                $obstetric_and_gynecologic_parity_lnmp=(!empty($obstetric_and_gynecologic_history->parity_lnmp));
                $obstetric_and_gynecologic_parity_edc=(!empty($obstetric_and_gynecologic_history->parity_edc));
                $obstetric_and_gynecologic_aog_lnmp=(!empty($obstetric_and_gynecologic_history->aog_lnmp));
                $obstetric_and_gynecologic_aog_eutz=(!empty($obstetric_and_gynecologic_history->aog_eutz));
                $obstetric_and_gynecologic_prenatal_history=(!empty($obstetric_and_gynecologic_history->prenatal_history));
                $obstetric_and_gynecologic_validation_checker=($obstetric_and_gynecologic_menarche_null_checker || $obstetric_and_gynecologic_menopausal_null_checker || $obstetric_and_gynecologic_menstrual_cycle
                || $obstetric_and_gynecologic_menstrual_duration || $obstetric_and_gynecologic_menstrual_padsperday || $obstetric_and_gynecologic_menstrual_medication || $obstetric_and_gynecologic_constraceptives
                || $obstetric_and_gynecologic_parity_g || $obstetric_and_gynecologic_parity_p || $obstetric_and_gynecologic_parity_ft || $obstetric_and_gynecologic_parity_pt || $obstetric_and_gynecologic_parity_a
                || $obstetric_and_gynecologic_parity_l || $obstetric_and_gynecologic_parity_lnmp || $obstetric_and_gynecologic_parity_edc || $obstetric_and_gynecologic_aog_lnmp || $obstetric_and_gynecologic_aog_eutz
                || $obstetric_and_gynecologic_prenatal_history);
            ?>

<hr style="border-top: 1px solid #ccc;">
<div class="row">
    <div class="col-sm-6">
    <div class="table-responsive">
        <table class="table bg-warning">

            @if(isset($icd[0]))
            <tr class="bg-gray">
                <td colspan="6">Diagnosis</td>
            </tr>
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
            @if(isset($form['pregnant']->other_diagnoses))
            <tr class="bg-gray">
                <td colspan="6">Diagnosis</td>
            </tr>
                <tr>
                    <td colspan="4">
                        Other Diagnosis:
                        <br />
                        <span class="reason form-details">{{ $form['pregnant']->other_diagnoses }}</span>
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
           
            @if ($validation_checker_past_medical_history)
            <tr class="bg-gray">
                <td colspan="4">Past Medical History</td>
            </tr>
            @endif
            @if ($commorbidities_null_checker)
            @php $commorbidities_data = []; @endphp

            @foreach ($commordities_arr as $commorbidities)
                @if($commorbidities === 'Hypertension')
                    @php $commorbidities_data[] = "Hypertension-" . $past_medical_history->commordities_hyper_year; @endphp
                @endif
                @if($commorbidities === 'Diabetes')
                    @php $commorbidities_data[] = "Diabetes-" . $past_medical_history->commordities_diabetes_year; @endphp
                @endif
                @if($commorbidities === 'Asthma')
                    @php $commorbidities_data[] = "Asthma-" . $past_medical_history->commordities_asthma_year; @endphp
                @endif
                @if($commorbidities === 'COPD')
                    @php $commorbidities_data[] = "COPD"; @endphp
                @endif
                @if($commorbidities === 'Dyslipidemia')
                    @php $commorbidities_data[] = "Dyslipidemia"; @endphp
                @endif
                @if($commorbidities === 'Others')
                    @php $commorbidities_data[] = "Others-" . $past_medical_history->commordities_others; @endphp
                @endif
                @if($commorbidities === 'Cancer')
                    @php $commorbidities_data[] = "Cancer-" . $past_medical_history->commordities_cancer; @endphp
                @endif
            
            @endforeach

            {{-- Display the formatted data --}}
            <tr>
                <td colspan="4">Comorbidities: 
                    <span class="woman_commorbidities_treatment form-details"></span> - 
                    <span class="woman_before_given_time form-details">{{ implode(", ", $commorbidities_data) }}</span>
                </td>
            </tr>
            @endif
            @if ($allergies_null_checker)
            @php $allergies_data = []; @endphp
                    @foreach ($allergies_arr as $allergies)
                    @if($allergies === 'Food')  @php $allergies_data[] = "Food-" . $past_medical_history->allergy_food_cause; @endphp@endif
                    @if($allergies === 'Drugs')  @php $allergies_data[] = "Drugs-" . $past_medical_history->allergy_drugs_cause; @endphp@endif
                    @if($allergies === 'Others')  @php $allergies_data[] = "Others-" . $past_medical_history->allergy_others_cause; @endphp@endif
                    @endforeach        
                <tr>    
                    <td colspan="6">Allergies: <span class="woman_allergies_food form-details"></span> - <span class="woman_before_given_time form-details">{{ implode(",", $allergies_data) }}</span></td>
                </tr>
            @endif
            @if ($heredofamilial_null_checker)
            @php $heredofamilial_data = []; @endphp

                @foreach ($heredofamilial_arr as $heredofamilial)
                    @if($heredofamilial === 'Hypertension')
                        @php $heredofamilial_data[] = "Hypertension-" . $past_medical_history->heredo_hyper_side; @endphp
                    @endif
                    @if($heredofamilial === 'Diabetes')
                        @php $heredofamilial_data[] = "Diabetes-" . $past_medical_history->heredo_diab_side; @endphp
                    @endif
                    @if($heredofamilial === 'Asthma')
                        @php $heredofamilial_data[] = "Asthma-" . $past_medical_history->heredo_asthma_side; @endphp
                    @endif
                    @if($heredofamilial === 'Cancer')
                        @php $heredofamilial_data[] = "Cancer-" . $past_medical_history->heredo_cancer_side; @endphp
                    @endif
                    @if($heredofamilial === 'Kidney Disease')
                        @php $heredofamilial_data[] = "Kidney Disease-" . $past_medical_history->heredo_kidney_side; @endphp
                    @endif
                    @if($heredofamilial === 'Thyroid Disease')
                        @php $heredofamilial_data[] = "Thyroid Disease-" . $past_medical_history->heredo_thyroid_side; @endphp
                    @endif
                    @if($heredofamilial === 'Others')
                        @php $heredofamilial_data[] = "Others-" . $past_medical_history->heredo_others; @endphp
                    @endif
                @endforeach

                {{-- Display the formatted data --}}
                <tr>
                    <td colspan="4">Heredofamilial: 
                        <span class="woman_allergies_treatment form-details"></span> - <span class="woman_before_given_time form-details">{{ implode(", ", $heredofamilial_data) }}</span>
                    </td>
                </tr>
            @endif
            @if (!empty($past_medical_history->previous_hospitalization))
            <tr>
                <td colspan="4">Previous Hospitalization: <span class="woman_allergies_treatment form-details"></span> - <span class="woman_before_given_time form-details">{{ $past_medical_history->previous_hospitalization }}</span></td>
            </tr>
            @endif
            
            @if ($personal_history_validation_checker)
            <tr class="bg-gray">
                <td colspan="4">Personal and Social History </td>
            </tr>
            @endif

            <tr>
            @if($smoking_null_checker) <td colspan="4">Smoking:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->smoking}}</span></td> @endif
            @if($smoking_sticks_per_day_null_checker)<td colspan="4">Sticks per Day:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->smoking_sticks_per_day}}</span></td> @endif
            </tr>
            
            <tr>
            @if($smoking_quit_year_null_checker)<td colspan="4">Year Quit:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->smoking_quit_year}}</span></td> @endif
            @if($smoking_remarks_null_checker)<td colspan="4">Smoking Remarks:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->smoking_remarks}}</span></td> @endif
            </tr>
            
            <tr>
            @if($alcohol_dringking_null_checker)<td colspan="4">Alcohol:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->alcohol_drinking}}</span></td>@endif
            @if($alcohol_liquor_type_null_checker)<td colspan="4">Liquor Type:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->alcohol_liquor_type}}</span></td>@endif
            </tr>
            <tr>
            @if($alcohol_dringking_quit_year_null_checker)<td colspan="4">Year Quit:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->alcohol_drinking_quit_year}}</span></td> @endif
            @if($alcohol_bottles_per_day_null_checker)<td colspan="4">Alcohol bottles per day:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->alcohol_bottles_per_day}}</span></td> @endif
            </tr>
  
            <tr>
            @if($illicit_drugs_null_checker)<td colspan="4">Illicit drugs:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->illicit_drugs}}</span></td>@endif
            @if($illicit_drugs_taken_null_checker)<td colspan="4">Illicit drugs taken:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->illicit_drugs_taken}}</span></td> @endif
            </tr>
            <tr>
            @if($illicit_drugs_quit_year_null_checker)<td colspan="4">Quit year:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->Illicit_drugs_quit_year}}</span></td> @endif
            </tr>
     
            @if (!empty($personal_and_social_history->current_medications))
            <tr class="bg-gray">
                <td colspan="4">Current Medications </td>
            </tr>
            <tr>
                <td colspan="4">Current Medication:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->current_medications}}</span></td> 
            </tr>
            @endif
            
            @if (!empty(implode(implode(",",$pertinent_arr))) || !empty(implode(",",$file_path)))
            <tr class="bg-gray">
                <td colspan="4">Pertinent Laboratory and Other Ancillary Procedures </td>
            </tr>
            @endif
           
            @if (!empty(implode(",",$pertinent_arr)))
            <tr>   
                <td colspan="4">Laboratory:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(",",$pertinent_arr)}}</span></td>
            </tr>
            @if(!empty($pertinent_others))<tr><td colspan="4">Others:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$pertinent_others}}</span></td></tr>@endif
            @endif

            @if (!empty(implode(",",$file_path)))
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
            @endif
            {{--@if(isset($file_path))--}}
                {{--<tr>--}}
                    {{--<td colspan="4">--}}
                        {{--File Attachment:--}}
                        {{--<a href="{{ asset($file_path) }}" target="_blank" class="reason" style="font-size: 12pt;" download>{{ $file_name }}</a>--}}
                    {{--</td>--}}
                {{--</tr>--}}
            {{--@endif--}}
            
            @if (!empty($nutritional_status->diet) || !empty($nutritional_status->specify_diets))
            <tr class="bg-gray">
                <td colspan="4">Nutritional Status</td>
            </tr>
            @endif
            @if (!empty($nutritional_status->diet) || !empty($nutritional_status->specify_diets))
            <tr>
            <td colspan="2">Diet:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$nutritional_status->diet}}</span></td>
            <td colspan="4">Specific Diet:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$nutritional_status->specify_diets}}</span></td>
            </tr>
            @endif
            
            @if (!empty($latest_vital_signs->temperature) || !empty($latest_vital_signs->pulse_rate) ||
            !empty($latest_vital_signs->respiratory_rate) || !empty($latest_vital_signs->blood_pressure) ||
            !empty($latest_vital_signs->oxygen_saturation))
            <tr class="bg-gray">
                <td colspan="4">Latest Vital Signs</td>
            </tr>
            @endif
            @if (!empty($latest_vital_signs->temperature) || !empty($latest_vital_signs->pulse_rate))
            <tr>
            <td colspan="2">Teamperature:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->temperature}}</span></td>
            <td colspan="4">Pulse Rate:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->pulse_rate}}</span></td>
            </tr>
            @endif
            @if (!empty($latest_vital_signs->respiratory_rate) || !empty($latest_vital_signs->blood_pressure))
            <tr>
            <td colspan="2">Respiratory Rate:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->respiratory_rate}}</span></td>
            <td colspan="4">Blood Pressure:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->blood_pressure}}</span></td>
            </tr>
            @endif
            @if (!empty($latest_vital_signs->oxygen_saturation))
            <tr>
            <td colspan="4">Oxygen Saturation:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->oxygen_saturation}}</span></td>
            </tr>
            @endif

            @if ($glasgocoma_scale_validation_checker)
            <tr class="bg-gray">
                <td colspan="4">Glasgow Coma Scale</td>
            </tr>
            @endif 
            @if ($glasgo_pupil_size_null_checker)
            <td colspan="4">
            <table class="table table-bordered glasgow-table">
            <thead>
                    <tr>
                        @for ($i = 1; $i <= 10; $i++)
                            <th class="{{ $glasgocoma_scale->pupil_size_chart == $i ? 'highlight' : '' }}">
                                <b>{{ $i }}</b>
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @for ($i = 1; $i <= 10; $i++)
                            <td 
                                class="{{ $glasgocoma_scale->pupil_size_chart == $i ? 'highlight' : '' }}">
                                <span 
                                    class="glasgow-dot" 
                                    style="height: {{ $i * 4 + 2 }}px; width: {{ $i * 4 + 2 }}px;">
                                </span>
                            </td>
                        @endfor
                    </tr>
                </tbody>
            </table>
            </td>
            @endif
           
            <tr>
            @if ($glasgo_pupil_size_null_checker)<td colspan="2"><b>Pupil Size Chart:</b><span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->pupil_size_chart}}</span></td><br><br>@endif
            @if ($glasgo_motor_response_null_checker)<td colspan="4">Motor Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->motor_response}}</span></td>@endif
            </tr>
           
            <tr>
            @if ($glasgo_verbal_response_null_checker)<td colspan="2">Verbal Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->verbal_response}}</span></td>@endif
            @if ($glasgo_eye_response_null_checker)<td colspan="4">Eye Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->eye_response}}</span></td>@endif
            </tr>
            
            <tr>
            @if ($glasgo_gsc_score_null_checker)<td colspan="4">GCS Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->gsc_score}}</span></td>@endif
            </tr>
       
        </table>
    </div>
    </div>
    
  
    
    <div class="col-sm-6">
    <div class="table-responsive">
        <table class="table bg-warning">
            @if ($validation_checker_review_of_systems)
            <tr class="bg-gray">
                <td colspan="4">Review of Systems </td>
            </tr>
            @endif
            @if ($review_skin_null_checker)
            <tr>
            <td colspan="4">Skin:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_skin)}}</span></td>
            </tr>
            @endif
            @if ($review_head_null_checker)
            <tr>
            <td colspan="4">Head:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_head)}}</span></td>
            </tr>
            @endif
            @if ($review_eyes_null_checker)
            <tr>
            <td colspan="4">Eyes:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_eyes)}}</span></td>
            </tr>
            @endif
            @if ($review_ears_null_checker)
            <tr>
            <td colspan="4">Ears:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_ears)}}</span></td>
            </tr>
            @endif
            @if ($review_nose_null_checker)
            <tr>
            <td colspan="4">Nose/Sinuses:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_nose)}}</span></td>
            </tr>
            @endif
            @if ($review_mouth_null_checker)
            <tr>
            <td colspan="4">Mouth/Throat:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_mouth)}}</span></td>
            </tr>
            @endif
            @if ($review_neck_null_checker)
            <tr>
            <td colspan="4">Neck:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_neck)}}</span></td>
            </tr>
            @endif
            @if ($review_breast_null_checker)
            <tr>
            <td colspan="4">Breast:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_breast)}}</span></td>
            </tr>
            @endif
            @if ($review_respiratory_null_checker)
            <tr>
            <td colspan="4">Respiratory/Cardiac:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_respiratory)}}</span></td>
            </tr>
            @endif
            @if ($review_gastrointestinal_null_checker)
            <tr>
            <td colspan="4">Gastrointestinal:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_gastrointestinal)}}</span></td>
            </tr>
            @endif
            @if ($review_urinary_null_checker)
            <tr>
            <td colspan="4">Urinary:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_urinary)}}</span></td>
            </tr>
            @endif
            @if ($review_peripheral_null_checker)
            <tr>
            <td colspan="4">Peripheral Vascular:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_peripheral)}}</span></td>
            </tr>
            @endif
            @if ($review_musculoskeletal_null_checker)
            <tr>
            <td colspan="4">Musculoskeletal:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_musculoskeletal)}}</span></td>
            </tr>
            @endif
            @if ($review_neurologic_null_checker)
            <tr>
            <td colspan="4">Neurologic:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_neurologic)}}</span></td>
            </tr>
            @endif
            @if ($review_hematologic_null_checker)
            <tr>
            <td colspan="4">Hematologic:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_hematologic)}}</span></td>
            </tr>
            @endif
            @if ($review_endocrine_null_checker)
            <tr>
            <td colspan="4">Endocrine:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_endocrine)}}</span></td>
            </tr>
            @endif
            @if ($review_psychiatric_null_checker)
            <tr>
            <td colspan="4">Psychiatric:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_psychiatric)}}</span></td>
            </tr>
            @endif
        
            @if(isset($reason))
            <tr class="bg-gray">
                <td colspan="6">Reason for Referral</td>
            </tr>
                <tr>
                    <td colspan="4">
                        Reason for referral:
                        <br />
                        <span class="reason form-details">{{ $reason->reason }}</span>
                    </td>
                </tr>
            @endif
            @if(isset($form['pregnant']->other_reason_referral))
            <tr class="bg-gray">
                <td colspan="6">Reason for Referral</td>
            </tr>
                <tr>
                    <td colspan="4">
                        Reason for referral:
                        <br />
                        <span class="reason form-details">{{ $form['pregnant']->other_reason_referral }}</span>
                    </td>
                </tr>
            @endif
        
        @if($obstetric_and_gynecologic_validation_checker)
        <tr class="bg-gray">
                <td colspan="4">Obstetric and Gynecologic History </td>
        </tr>
        @endif

        <tr>
        @if($obstetric_and_gynecologic_menarche_null_checker)<td colspan="4">Menarche: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->menarche}}</span></td>@endif
        @if($obstetric_and_gynecologic_menopausal_null_checker)<td colspan="4">Menopause: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->menopausal_age}}</span></td>@endif
        </tr>
        
        <tr>
        @if($obstetric_and_gynecologic_menstrual_cycle)<td colspan="2">Menstrual Cycle: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->menstrual_cycle}}</span></td>@endif
        @if($obstetric_and_gynecologic_menstrual_duration)<td colspan="4">Menstrual Duration: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->menstrual_cycle_duration}}</span></td>@endif
        </tr>
        
        <tr>
        @if($obstetric_and_gynecologic_menstrual_padsperday)<td colspan="2">Menstrual Pads per Day: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->menstrual_cycle_padsperday}}</span></td>@endif
        @if($obstetric_and_gynecologic_menstrual_medication)<td colspan="4">Menstrual Medication: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->menstrual_cycle_medication}}</span></td>@endif
        </tr>

        @if ($obstetric_and_gynecologic_constraceptives)
        <tr>
            <td colspan="4">Contraceptives:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(",",$contraceptives_arr)}}</span></td>
        </tr>
        @endif
        @if (!empty($obstetric_and_gynecologic_history->parity_g) || !empty($obstetric_and_gynecologic_history->parity_p) || 
        !empty($obstetric_and_gynecologic_history->parity_ft) || !empty($obstetric_and_gynecologic_history->parity_pt) ||
        !empty($obstetric_and_gynecologic_history->parity_a) || !empty($obstetric_and_gynecologic_history->parity_l) ||
        !empty($obstetric_and_gynecologic_history->parity_lnmp) || !empty($obstetric_and_gynecologic_history->parity_edc) ||
        !empty($obstetric_and_gynecologic_history->aog_lnmp) || !empty($obstetric_and_gynecologic_history->aog_eutz))
        <tr>
                <td colspan="4"><i>Parity</i></td>
        </tr>
        @endif

        <tr>
        @if($obstetric_and_gynecologic_parity_g)<td colspan="2">G:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_g}}</span></td>@endif
        @if($obstetric_and_gynecologic_parity_p)<td colspan="4">P:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_p}}</span></td>@endif
        </tr>
        
        <tr>
        @if($obstetric_and_gynecologic_parity_ft)<td colspan="2">FT:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_ft}}</span></td>@endif
        @if($obstetric_and_gynecologic_parity_pt)<td colspan="4">PT:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_pt}}</span></td>@endif
        </tr>

        <tr>
        @if($obstetric_and_gynecologic_parity_a)<td colspan="2">A:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_a}}</span></td>@endif
        @if($obstetric_and_gynecologic_parity_l)<td colspan="4">L:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_l}}</span></td>@endif
        </tr>
        
        <tr>
        @if($obstetric_and_gynecologic_parity_lnmp)<td colspan="2">LMP:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_lnmp}}</span></td>@endif
        @if($obstetric_and_gynecologic_parity_edc)<td colspan="4">EDC:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->parity_edc}}</span></td> @endif
        </tr>

        @if($obstetric_and_gynecologic_aog_lnmp || $obstetric_and_gynecologic_aog_eutz)
        <tr>
            <td colspan="4"><i>AOG</i></td>
        </tr>
        @endif
        <tr>
        @if($obstetric_and_gynecologic_aog_lnmp)<td colspan="2">LMP:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->aog_lnmp}}</span></td>@endif
        @if($obstetric_and_gynecologic_aog_eutz)<td colspan="4">UTZ:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->aog_eutz}}</span></td>@endif
        </tr>
       
        <tr>
        @if($obstetric_and_gynecologic_prenatal_history)<td colspan="4">Prenatal History:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$obstetric_and_gynecologic_history->prenatal_history}}</span></td>@endif 
        </tr>
  
        </table>
    </div>
    </div>
</div>
@if($obstetric_and_gynecologic_validation_checker)
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
                @php
                    // Filter out records that don't have any meaningful data
                    $filteredPregnancy = collect($pregnancy)->filter(function ($record) {
                        return $record['pregnancy_order'] !== null ||
                            $record['pregnancy_year'] !== null ||
                            $record['pregnancy_gestation_completed'] !== null ||
                            $record['pregnancy_outcome'] !== null ||
                            $record['pregnancy_place_of_birth'] !== null ||
                            $record['pregnancy_sex'] !== null ||
                            $record['pregnancy_birth_weight'] !== null ||
                            $record['pregnancy_present_status'] !== null ||
                            $record['pregnancy_complication'] !== null;
                    });
                @endphp
                    @if($filteredPregnancy->isNotEmpty())
                        @foreach($filteredPregnancy as $record)
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
@endif




<table class="table table-striped col-sm-6"></table>
<div class="clearfix"></div>
<hr />
<button class="btn btn-default btn-flat" data-dismiss="modal" id="closeReferralForm{{$form['pregnant']->code}}"><i class="fa fa-times"></i> Close</button>
<div class="pull-right">
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


