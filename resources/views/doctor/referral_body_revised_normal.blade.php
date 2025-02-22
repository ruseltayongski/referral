<?php $user = Session::get('auth'); ?>
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
                Other Diagnosis:
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
        // function explodeToArray($string){
        //     $array = explode(',',$string);

        //     $filteredOptions = array_filter($array, function ($value) {
        //         return $value !== "Select All";
        //     });

        //         return $filteredOptions;
        // }
        $pertinent_arr = explodeToArray($pertinent_laboratory->pertinent_laboratory_and_procedures);
      
    ?>


    
    <?php 
    function explodeToArray($string){
        $array = explode(',', $string);
        return array_filter($array, function ($value) {
            return $value !== "Select All";
        });
    }

    // PAST MEDICAL HISTORY
    $commordities_arr = explodeToArray($past_medical_history->commordities);
    $allergies_arr = explodeToArray($past_medical_history->allergies);
    $heredofamilial_arr = explodeToArray($past_medical_history->heredofamilial_diseases);
    // REVIEW OF SYSTEMS
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
    // dd($past_medical_history);

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

    ?>

    @if ($validation_checker_past_medical_history)
        <tr class="bg-gray">
            <td colspan="6">Past Medical History</td>
        </tr>
    @endif
    
    @if($commorbidities_null_checker)
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
            <td colspan="6">Comorbidities: 
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

   @if($heredofamilial_null_checker)
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
            <td colspan="6">Heredofamilial: 
                <span class="woman_allergies_treatment form-details"></span> - <span class="woman_before_given_time form-details">{{ implode(", ", $heredofamilial_data) }}</span>
            </td>
        </tr>
    @endif

    @if (!empty($past_medical_history->previous_hospitalization))
    <tr>
        <td colspan="6">Previous Hospitalization: <span class="woman_allergies_treatment form-details"></span> - <span class="woman_before_given_time form-details">{{ $past_medical_history->previous_hospitalization }}</span></td>
    </tr>
    @endif



    @php
    // Create an array with all the relevant fields
    $prenatalFields = [
        $pediatric_history->prenatal_a,
        $pediatric_history->prenatal_p,
        $pediatric_history->prenatal_g,
        $pediatric_history->prenatal_radiowith_or_without,
        $pediatric_history->prenatal_with_maternal_illness
    ];

    $natalFields = [
        $pediatric_history->natal_born_at,
        $pediatric_history->natal_born_address,
        $pediatric_history->natal_by,
        $pediatric_history->natal_via,
        $pediatric_history->natal_indication,
        $pediatric_history->natal_term,
        $pediatric_history->natal_weight,
        $pediatric_history->natal_br,
        $pediatric_history->natal_with_good_cry,
        $pediatric_history->natal_other_complications
    ];

    $postNatalFields = [
        $pediatric_history->post_natal_bfeedx_month,
        $pediatric_history->post_natal_ffeed_specify,
        $pediatric_history->post_natal_started_semifoods,
        $pediatric_history->post_natal_bcg,
        $pediatric_history->post_natal_dpt_opv_x,
        $pediatric_history->post_dpt_doses,
        $pediatric_history->post_natal_hepB_cbox,
        $pediatric_history->post_natal_hepB_x_doses,
        $pediatric_history->post_natal_immu_measles_cbox,
        $pediatric_history->post_natal_mmr_cbox,
        $pediatric_history->post_natal_others,
        $pediatric_history->post_natal_development_milestones
    ];

    // Filter out empty fields
    $filteredPrenatalFields = array_filter($prenatalFields);
    $filteredNatalFields = array_filter($natalFields);
    $filteredPostNatalFields = array_filter($postNatalFields);
    @endphp

    @if ($patient_age <= "18")
        @if (count($filteredPrenatalFields) > 0 || count($filteredNatalFields) > 0 || count($filteredPostNatalFields) > 0)
            <tr class="bg-gray">
                <td colspan="6">Pediatric History</td>
            </tr>
        @endif

        @if (count($filteredPrenatalFields) > 0)
            <tr>
                <td colspan="6">PRENATAL</td>
            </tr>
            @if (!empty($pediatric_history->prenatal_a) || !empty($pediatric_history->prenatal_p))
            <tr>
                <td colspan="3">Prenatal A: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$pediatric_history->prenatal_a}}</span></td>
                <td colspan="3">Prenatal P: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$pediatric_history->prenatal_p}}</span></td>
            </tr>
            @endif
            @if (!empty($pediatric_history->prenatal_g))
            <tr>
                <td colspan="6">Prenatal G: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$pediatric_history->prenatal_g}}</span></td>
            </tr>
            @endif
            @if (!empty($pediatric_history->prenatal_radiowith_or_without))
            <tr>
                <td colspan="6">{{$pediatric_history->prenatal_radiowith_or_without}} maternal illness:
                    <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">
                        @if ($pediatric_history->prenatal_radiowith_or_without === "with")
                            {{$pediatric_history->prenatal_with_maternal_illness}}
                        @else
                            N/A
                        @endif
                    </span>
                </td>
            </tr>
            @endif
        @endif

        @if (count($filteredNatalFields) > 0)
            <tr>
                <td colspan="6">NATAL</td>
            </tr>
            @if (!empty($pediatric_history->natal_born_at) || !empty($pediatric_history->natal_born_address))
            <tr>
                <td colspan="3">Born At: <span class="woman_natal form-details"></span> - <span class="woman_natal form-details">{{$pediatric_history->natal_born_at}}</span></td>
                <td colspan="3">Born Address: <span class="woman_natal form-details"></span> - <span class="woman_natal form-details">{{$pediatric_history->natal_born_address}}</span></td>
            </tr>
            @endif
            @if (!empty($pediatric_history->natal_by) || !empty($pediatric_history->natal_via))
            <tr>
                <td colspan="3">Born By: <span class="woman_natal form-details"></span> - <span class="woman_natal form-details">{{$pediatric_history->natal_by}}</span></td>
                <td colspan="3">Born Via: <span class="woman_natal form-details"></span> - <span class="woman_natal form-details">{{$pediatric_history->natal_via}}</span></td>
            </tr>
            @endif
            @if (!empty($pediatric_history->natal_indication) || !empty($pediatric_history->natal_term))
            <tr>
                <td colspan="3">Indication: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$pediatric_history->natal_indication}}</span></td>
                <td colspan="3">Term: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$pediatric_history->natal_term}}</span></td>
            </tr>
            @endif
        @endif

        @if (count($filteredPostNatalFields) > 0)
            <tr>
                <td colspan="6">POSTNATAL</td>
            </tr>
            <tr>
                <td colspan="6"><i>Feeding History</i></td>
            </tr>
            @if (!empty($pediatric_history->post_natal_bfeedx_month) || !empty($pediatric_history->post_natal_ffeed_specify))
            <tr> 
                <td colspan="3">Breastfed x: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$pediatric_history->post_natal_bfeedx_month}}</span></td>
                <td colspan="3">Formula Fed: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$pediatric_history->post_natal_ffeed_specify}}</span></td>
            </tr>
            @endif
            @if (!empty($pediatric_history->post_natal_started_semifoods))
            <tr>
                <td colspan="6">Started Semi Food: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$pediatric_history->post_natal_started_semifoods}}</span></td>
            </tr>
            @endif
            <tr>
                <td colspan="6"><i>Immunization History</i></td>
            </tr>
            @if (!empty($pediatric_history->post_natal_bcg))
            <tr>
                <td colspan="6">BCG: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$pediatric_history->post_natal_bcg}}</span></td>
            </tr>
            @endif
        @endif
    @endif

    

   @php
    $psh = $personal_and_social_history;
    $displaySections = array_filter([
        $psh->smoking ?? null, 
        $psh->alcohol_drinking ?? null,
        $psh->illicit_drugs ?? null, 
        $psh->illicit_drugs_taken ?? null
    ]);
    @endphp

    @if ($displaySections)
    <tr class="bg-gray">
        <td colspan="6">Personal and Social History</td>
    </tr>
    @endif

    @if (!empty($psh->smoking))
    <tr>
        <td colspan="3">Smoking: - <span class="woman_prenatal form-details">{{$psh->smoking}}</span></td>
        @if ($psh->smoking === "Yes")
            <td colspan="3">Sticks per Day: - <span class="woman_prenatal form-details">{{$psh->smoking_sticks_per_day ?? 'N/A'}}</span></td>
        @elseif ($psh->smoking === "Quit")
            <td colspan="6">Quit Year: - <span class="woman_prenatal form-details">{{$psh->smoking_quit_year ?? 'N/A'}}</span></td>
        @elseif ($psh->smoking === "No")
    </tr>
    <tr>
        <td colspan="6">Smoking Remarks: - <span class="woman_prenatal form-details">{{$psh->smoking_remarks ?? 'N/A'}}</span></td>
    </tr>
    @endif
    @endif

    @if (!empty($psh->alcohol_drinking))
    <tr>
        <td colspan="6" class="woman_prenatal form-details">Alcohol: - <span>{{$psh->alcohol_drinking}}</span></td>
    </tr>
    @if ($psh->alcohol_drinking === "Yes")
    <tr>
        <td colspan="3">Liquor Type: - <span class="woman_prenatal form-details">{{$psh->alcohol_liquor_type ?? 'N/A'}}</span></td>
        <td colspan="3">Bottles per day: - <span class="woman_prenatal form-details">{{$psh->alcohol_bottles_per_day ?? 'N/A'}}</span></td>
    </tr>
    @elseif ($psh->alcohol_drinking === "Quit")
    <tr>
        <td colspan="6">Year Quit: - <span class="woman_prenatal form-details">{{$psh->alcohol_drinking_quit_year ?? 'N/A'}}</span></td>
    </tr>
    @endif
    @endif

    @if (!empty($psh->illicit_drugs))
    <tr>
        <td colspan="6">Illicit drugs: - <span class="woman_prenatal form-details">{{$psh->illicit_drugs}}</span></td>
    </tr>
    @if ($psh->illicit_drugs === "Yes")
    <tr>
        <td colspan="6">Illicit drugs taken: - <span class="woman_prenatal form-details">{{$psh->illicit_drugs_taken ?? 'N/A'}}</span></td>
    </tr>
    @elseif ($psh->illicit_drugs === "Quit")
    <tr>
        <td colspan="6">Quit Year: - <span class="woman_prenatal form-details">{{$psh->illicit_drugs_quit_year ?? 'N/A'}}</span></td>
    </tr>
    @endif
    @endif

    @if (!empty($personal_and_social_history->current_medications))
    <tr class="bg-gray">
        <td colspan="6">Current Medications </td>
    </tr>
    <tr>
        <td colspan="6">Current Medication:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$personal_and_social_history->current_medications}}</span></td> 
    </tr>
    @endif
    
    @if (!empty(implode(",",$pertinent_arr)))
    <tr class="bg-gray">
        <td colspan="6">Pertinent Laboratory and Other Ancillary Procedures </td>
    </tr>
    @endif
    @if (!empty(implode(",",$pertinent_arr)))
    <tr>   
        <td colspan="6">Laboratory:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(",",$pertinent_arr)}}</span></td> 
    </tr>
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
    @endif
    @if ($patient_age >= 9 && $form->patient_sex === "Female")
    @php
        $history = [
            'Menarche' => $obstetric_and_gynecologic_history->menarche ?? '',
            'Menopause' => $obstetric_and_gynecologic_history->menopausal_age ?? '',
            'Menstrual Cycle' => $obstetric_and_gynecologic_history->menstrual_cycle ?? '',
            'Menstrual Duration' => $obstetric_and_gynecologic_history->menstrual_cycle_duration ?? '',
            'Menstrual Pads per Day' => $obstetric_and_gynecologic_history->menstrual_cycle_padsperday ?? '',
            'Menstrual Medication' => $obstetric_and_gynecologic_history->menstrual_cycle_medication ?? '',
            'Contraceptives' => implode(",", $contraceptives_arr) ?? '',
            'G' => $obstetric_and_gynecologic_history->parity_g ?? '',
            'P' => $obstetric_and_gynecologic_history->parity_p ?? '',
            'FT' => $obstetric_and_gynecologic_history->parity_ft ?? '',
            'PT' => $obstetric_and_gynecologic_history->parity_pt ?? '',
            'A' => $obstetric_and_gynecologic_history->parity_a ?? '',
            'L' => $obstetric_and_gynecologic_history->parity_l ?? '',
            'LMP' => $obstetric_and_gynecologic_history->parity_lnmp ?? '',
            'EDC' => $obstetric_and_gynecologic_history->parity_edc ?? '',
            'AOG by LMP' => $obstetric_and_gynecologic_history->aog_lnmp ?? '',
            'AOG by UTZ' => $obstetric_and_gynecologic_history->aog_eutz ?? '',
            'Prenatal History' => $obstetric_and_gynecologic_history->prenatal_history ?? '',
        ];

            // Remove empty values
            $filteredHistory = array_filter($history);
        @endphp

        @if (!empty($filteredHistory))
            <tr class="bg-gray">
                <td colspan="6">Obstetric and Gynecologic History</td>
            </tr>

            @foreach ($filteredHistory as $label => $value)
                <tr>
                    <td colspan="6">{{ $label }}: <span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{ $value }}</span></td>
                </tr>
            @endforeach
        @endif
    @endif

            @if ($validation_checker_review_of_systems)
            <tr class="bg-gray">
                <td colspan="6">Review of Systems </td>
            </tr>
            @endif
            @if ($review_skin_null_checker)
            <tr>
            <td colspan="6">Skin:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_skin)}}</span></td>
            </tr>
            @endif
            @if ($review_head_null_checker)
            <tr>
            <td colspan="6">Head:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_head)}}</span></td>
            </tr>
            @endif
            @if ($review_eyes_null_checker)
            <tr>
            <td colspan="6">Eyes:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_eyes)}}</span></td>
            </tr>
            @endif
            @if ($review_ears_null_checker)
            <tr>
            <td colspan="6">Ears:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_ears)}}</span></td>
            </tr>
            @endif
            @if ($review_nose_null_checker)
            <tr>
            <td colspan="6">Nose/Sinuses:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_nose)}}</span></td>
            </tr>
            @endif
            @if ($review_mouth_null_checker)
            <tr>
            <td colspan="6">Mouth/Throat:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_mouth)}}</span></td>
            </tr>
            @endif
            @if ($review_neck_null_checker)
            <tr>
            <td colspan="6">Neck:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_neck)}}</span></td>
            </tr>
            @endif
            @if ($review_breast_null_checker)
            <tr>
            <td colspan="6">Breast:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_breast)}}</span></td>
            </tr>
            @endif
            @if ($review_respiratory_null_checker)
            <tr>
            <td colspan="6">Respiratory/Cardiac:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_respiratory)}}</span></td>
            </tr>
            @endif
            @if ($review_gastrointestinal_null_checker)
            <tr>
            <td colspan="6">Gastrointestinal:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_gastrointestinal)}}</span></td>
            </tr>
            @endif
            @if ($review_urinary_null_checker)
            <tr>
            <td colspan="6">Urinary:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_urinary)}}</span></td>
            </tr>
            @endif
            @if (!empty(implode(',',$review_peripheral)))
            <tr>
            <td colspan="6">Peripheral Vascular:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_peripheral)}}</span></td>
            </tr>
            @endif
            @if ($review_musculoskeletal_null_checker)
            <tr>
            <td colspan="6">Musculoskeletal:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_musculoskeletal)}}</span></td>
            </tr>
            @endif
            @if ($review_neurologic_null_checker )
            <tr>
            <td colspan="6">Neurologic:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_neurologic)}}</span></td>
            </tr>
            @endif
            @if ($review_hematologic_null_checker)
            <tr>
            <td colspan="6">Hematologic:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_hematologic)}}</span></td>
            </tr>
            @endif
            @if ($review_endocrine_null_checker)
            <tr>
            <td colspan="6">Endocrine:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_endocrine)}}</span></td>
            </tr>
            @endif
            @if ($review_psychiatric_null_checker)
            <tr>
            <td colspan="6">Psychiatric:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{implode(',',$review_psychiatric)}}</span></td>
            </tr>
            @endif

    @php
    $nutritionalFields = array_filter([
        $nutritional_status->diet ?? null,
        $nutritional_status->specify_diets ?? null
    ]);

    $vitalSignsFields = array_filter([
        $latest_vital_signs->temperature ?? null,
        $latest_vital_signs->pulse_rate ?? null,
        $latest_vital_signs->respiratory_rate ?? null,
        $latest_vital_signs->blood_pressure ?? null,
        $latest_vital_signs->oxygen_saturation ?? null
    ]);
    @endphp

    @if (!empty($nutritionalFields) || !empty($vitalSignsFields))
    <tr class="bg-gray">
        <td colspan="6">Nutritional Status</td>
    </tr>
    @endif

    @if (!empty($nutritionalFields))
    <tr>
        <td colspan="3">Diet:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$nutritional_status->diet}}</span></td>
        <td colspan="3">Specific Diet:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$nutritional_status->specify_diets}}</span></td>
    </tr>
    @endif

    @if (!empty($vitalSignsFields))
    <tr class="bg-gray">
        <td colspan="6">Latest Vital Signs</td>
    </tr>
    @endif

    @if (!empty($latest_vital_signs->temperature) || !empty($latest_vital_signs->pulse_rate))
    <tr>
        <td colspan="3">Temperature:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->temperature}}</span></td>
        <td colspan="3">Pulse Rate:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->pulse_rate}}</span></td>
    </tr>
    @endif

    @if (!empty($latest_vital_signs->respiratory_rate) || !empty($latest_vital_signs->blood_pressure))
    <tr>
        <td colspan="3">Respiratory Rate:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->respiratory_rate}}</span></td>
        <td colspan="3">Blood Pressure:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->blood_pressure}}</span></td>
    </tr>
    @endif

    @if (!empty($latest_vital_signs->oxygen_saturation))
    <tr>
        <td colspan="6">Oxygen Saturation:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$latest_vital_signs->oxygen_saturation}}</span></td>
    </tr>
    @endif

    
    @if (!empty($glasgocoma_scale->pupil_size_chart) || !empty($glasgocoma_scale->motor_response) ||
    !empty($glasgocoma_scale->verbal_response) || !empty($glasgocoma_scale->eye_response) ||
    !empty($glasgocoma_scale->gsc_score))
    <tr class="bg-gray">
        <td colspan="6">Glasgow Coma Scale</td>
    </tr>
    @endif
    @if (!empty($glasgocoma_scale->pupil_size_chart))
    <td colspan="6">
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
    @if (!empty($glasgocoma_scale->pupil_size_chart) || !empty($glasgocoma_scale->motor_response))
    <tr>
        <td colspan="3"><b>Pupil Size Chart:</b><span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->pupil_size_chart}}</span></td><br><br>
        <td colspan="3">Motor Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->motor_response}}</span></td>
    </tr>
    @endif
    @if (!empty($glasgocoma_scale->verbal_response) || !empty($glasgocoma_scale->eye_response))
    <tr>
        <td colspan="3">Verbal Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->verbal_response}}</span></td>
        <td colspan="3">Eye Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->eye_response}}</span></td>
    </tr>
    @endif
    @if (!empty($glasgocoma_scale->gsc_score))
    <tr>
        <td colspan="6">GCS Response:<span class="woman_prenatal form-details"></span> - <span class="woman_prenatal form-details">{{$glasgocoma_scale->gsc_score}}</span></td>
    </tr>
    @endif
</table>
<hr/>

@if($form->patient_sex === "Female" && !empty($filteredHistory))
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

