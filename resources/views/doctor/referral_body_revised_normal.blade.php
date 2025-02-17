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
    
    @if (!empty(implode(",",$commordities_arr)) || !empty(implode(",",$allergies_arr))
    || !empty(implode(",",$heredofamilial_arr)) || !empty($past_medical_history->previous_hospitalization))
    <tr class="bg-gray">
        <td colspan="6">Past Medical History</td>
    </tr>
    @endif
    @if (!empty(implode(",",$commordities_arr)))
    <tr> 
        <td colspan="6">Commorbidities: <span class="woman_commorbidities_treatment form-details"></span> - <span class="woman_before_given_time form-details">{{ implode(",",$commordities_arr) }}</span></td>       
    </tr>
    @endif
    @if (!empty(implode(",",$allergies_arr)))
    <tr>    
        <td colspan="6">Allergies: <span class="woman_allergies_food form-details"></span> - <span class="woman_before_given_time form-details">{{ implode(",",$allergies_arr) }}</span></td>
    </tr>
    @endif
    @if (!empty(implode(",",$heredofamilial_arr)))
    <tr>
        <td colspan="6">Heredofamilial: <span class="woman_allergies_treatment form-details"></span> - <span class="woman_before_given_time form-details">{{ implode(",",$heredofamilial_arr) }}</span></td>
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
        $psh->illicit_drugs_taken ?? null, 
        $psh->current_medications ?? null
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

    
    @php
    $review_sections = [
        'Skin' => $review_skin,
        'Head' => $review_head,
        'Eyes' => $review_eyes,
        'Ears' => $review_ears,
        'Nose/Sinuses' => $review_nose,
        'Mouth/Throat' => $review_mouth,
        'Neck' => $review_neck,
        'Breast' => $review_breast,
        'Respiratory/Cardiac' => $review_respiratory,
        'Gastrointestinal' => $review_gastrointestinal,
        'Urinary' => $review_urinary,
        'Peripheral Vascular' => $review_peripheral,
        'Musculoskeletal' => $review_musculoskeletal,
        'Neurologic' => $review_neurologic,
        'Hematologic' => $review_hematologic,
        'Endocrine' => $review_endocrine,
        'Psychiatric' => $review_psychiatric,
    ];

    $filtered_sections = array_filter($review_sections, fn($section) => !empty(array_filter($section)));
    @endphp

    @if (!empty($filtered_sections))
        <tr class="bg-gray">
            <td colspan="6">Review of Systems</td>
        </tr>

        @foreach ($filtered_sections as $section_name => $values)
            <tr>
                <td colspan="6">{{ $section_name }}:<span class="woman_prenatal form-details"></span> - 
                    <span class="woman_prenatal form-details">{{ implode(', ', $values) }}</span>
                </td>
            </tr>
        @endforeach
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

@if($form->patient_sex === "Female")
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