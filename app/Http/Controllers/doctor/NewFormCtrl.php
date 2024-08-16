<?php

namespace App\Http\Controllers\doctor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PastMedicalHistory;
use App\PediatricHistory;
use App\ObstetricAndGynecologicHistory;
use App\PersonalAndSocialHistory;

class NewFormCtrl extends Controller
{
        public function index(){
            return view('modal/revised_normal_form');
        }
        
        public function saveReferral(Request $request)
        {
    // Initialize arrays for storing medical history information
    $comorbidities = [];
    $heredofamilial_diseases = [];
    $allergies = [];
    $contraceptive_methods = [];

    // Define the fields for comorbidities with associated additional data (year or string)
    $comorbidity_fields = [
        'Hypertension' => [
            'cbox' => 'comor_hyper_cbox',
            'year' => 'hyper_year'
        ],
        'Diabetes' => [
            'cbox' => 'comor_diab_cbox',
            'year' => 'diab_year'
        ],
        'Asthma' => [
            'cbox' => 'comor_asthma_cbox',
            'year' => 'asthma_year'
        ],
        'COPD' => [
            'cbox' => 'comor_copd_cbox',
            'year' => null
        ],
        'Dyslipidemia' => [
            'cbox' => 'comor_dyslip_cbox',
            'year' => null
        ],
        'Thyroid Disease' => [
            'cbox' => 'comor_thyroid_cbox',
            'year' => null
        ],
        'Cancer' => [
            'cbox' => 'comor_cancer_cbox',
            'year' => 'comor_cancer' 
        ],
        'Others' => [
            'cbox' => 'comor_others_cbox',
            'year' => 'comor_others' 
        ]
    ];



    // Process comorbidities
    foreach ($comorbidity_fields as $default_field => $fields) {
        $cbox = $fields['cbox'];
        $additional_data = $fields['year'];

        if ($request->$cbox == "Yes") {
            $comorbidity = $default_field;

            if ($additional_data && $request->$additional_data) {
                if ($default_field == "Cancer" || $default_field == "Others") {
                    $comorbidity .= ' => ' . $request->$additional_data;
                } else {
                    $comorbidity .= ' Year => ' . $request->$additional_data;
                }
            }

            $comorbidities[] = $comorbidity;
        }
    }


    // Define the fields for heredofamilial diseases with associated additional data (side of the family)
    $heredofamilial_diseases_fields = [
        'Hypertension' => [
            'cbox' => 'heredo_hyper_cbox',
            'side' => 'heredo_hypertension_side'
        ],
        'Diabetes' => [
            'cbox' => 'heredo_diab_cbox',
            'side' => 'heredo_diabetes_side'
        ],
        'Asthma' => [
            'cbox' => 'heredo_asthma_cbox',
            'side' => 'heredo_asthma_side'
        ],
        'Cancer' => [
            'cbox' => 'heredo_cancer_cbox',
            'side' => 'heredo_cancer_side'
        ],
        'Kidney Disease' => [
            'cbox' => 'heredo_kidney_cbox',
            'side' => 'heredo_kidney_side'
        ],
        'Thyroid Disease' => [
            'cbox' => 'heredo_thyroid_cbox',
            'side' => 'heredo_thyroid_side'
        ],
        'Others' => [
            'cbox' => 'heredo_others_cbox',
            'side' => 'heredo_others_side'
        ],
    ];

    // Process heredofamilial diseases
    foreach ($heredofamilial_diseases_fields as $default_field => $fields) {
        $cbox = $fields['cbox'];
        $side = $fields['side'];

        if ($request->$cbox == "Yes") {
            $heredofamilial = $default_field;

            if ($side && $request->$side) {
                $heredofamilial .= ' side => ' . $request->$side;
            }

            $heredofamilial_diseases[] = $heredofamilial;
        }
    }


    // Define the fields for allergies with associated causes
    $allergies_fields = [
        'Food Allergy' => [
            'cbox' => 'allergy_food_cbox',
            'cause' => 'allergy_food_cause'
        ],
        'Drug Allergy' => [
            'cbox' => 'allergy_drug_cbox',
            'cause' => 'allergy_drug_cause'
        ],
        'Other Allergy' => [
            'cbox' => 'allergy_other_cbox',
            'cause' => 'allergy_other_cause'
        ]
    ];

    // Process allergies
    foreach ($allergies_fields as $default_field => $fields) {
        $cbox = $fields['cbox'];
        $cause = $fields['cause'];

        if ($request->$cbox == "Yes") {
            $allergy = $default_field;

            if ($cause && $request->$cause) {
                $allergy .= ', Cause => ' . $request->$cause;
            }

            $allergies[] = $allergy;
        }
    }

    // Define the fields for allergies with associated causes
    $contraceptive_fields = [
        'Pills' => [
            'cbox' => 'contraceptive_pills_cbox',
            'other' => null
        ],
        'IUD' => [
            'cbox' => 'contraceptive_iud_cbox',
            'other' => null
        ],
        'Rhythm' => [
            'cbox' => 'contraceptive_rhythm_cbox',
            'other' => null
        ],
        'Condom' => [
            'cbox' => 'contraceptive_condom_cbox',
            'other' => null
        ],
        'Other' => [
            'cbox' => 'contraceptive_other_cbox',
            'other' => 'contraceptive_other'
        ],
    ];


    foreach ($contraceptive_fields as $default_method => $fields) {
        $cbox = $fields['cbox'];
        $other_data = $fields['other'];

        if ($request->$cbox == "Yes") {
            $contraceptive_method = $default_method;

            if ($other_data && $request->$other_data) {
                $contraceptive_method .= ' => ' . $request->$other_data;
            }

            $contraceptive_methods[] = $contraceptive_method;
        }
    }


    // Convert arrays to strings for database storage
    $comorbidities = implode(',', $comorbidities);
    $heredofamilial_diseases = implode(',', $heredofamilial_diseases);
    $allergies = implode(',', $allergies);
    $contraceptive_methods = implode(',', $contraceptive_methods);

    // Prepare past medical history data for database
    $past_medical_history_data = [
        'patient_id' => $request->patient_id,
        'commordities' => $comorbidities,
        'heredofamilial_diseases' => $heredofamilial_diseases,
        'allergies' => $allergies,
        'previous_hospitalization' => $request->previous_hospitalization,
    ];

    // Prepare pediatric history data for database
    $pediatric_history = [
        'patient_id' => $request->patient_id,
        'prenatal_a' => $request->prenatal_age,
        'prenatal_g' => $request->prenatal_g,
        'prenatal_p' => $request->prenatal_p,
        'prenatal_radiowith_or_without' => $request->prenatal_radiowith_or_without,
        'prenatal_with_maternal_illness' => $request->prenatal_maternal_illness,
        'natal_born_at' => $request->natal_bornat,
        'natal_born_address' => $request->natal_born_address,
        'natal_by' => $request->natal_by,
        'natal_via' => $request->natal_via,
        'natal_indication' => $request->cs_indication,
        'natal_term' => $request->natal_term,
        'natal_weight' => $request->natal_weight,
        'natal_br' => $request->natal_br,
        'natal_with_good_cry' => $request->natal_withGoodCry,
        'natal_other_complications' => $request->natal_complications,
        'post_natal_bfeed' => $request->postnatal_bfeed,
        'post_natal_bfeedx_month' => $request->postnatal_bfeed_xmos,
        'post_natal_formula_feed' => $request->postnatal_ffeed,
        'post_natal_ffeed_specify' => $request->postnatal_ffeed_specify,
        'post_natal_started_semifoods' => $request->postnatal_started_semisolidfood_at,
        'post_natal_bcg' => $request->immu_bcg_cbox,
        'post_natal_dpt_opv_x' => $request->immu_dpt_cbox,
        'post_dpt_doses' => $request->immu_dpt_doses,
        'post_natal_hepB_cbox' => $request->immu_hepb_cbox,
        'post_natal_hepB_x_doses' => $request->immu_hepb_doses,
        'post_natal_immu_measles_cbox' => $request->immu_measles_cbox,
        'post_natal_mmr_cbox' => $request->immu_mmr_cbox,
        'post_natal_others_cbox' => $request->immu_others_cbox,
        'post_natal_others' => $request->immu_others,
        'post_natal_development_milestones' => $request->prenatal_milestone,
    ];

    $obstetric_history = [
        'patient_id'=>$request->patient_id,
        'menarche'=>$request->menarche,
        'menopause'=>$request->menopausal,
        'menopausal_age'=>$request->menopausal_age,
        'menstrual_cycle'=>$request->mens_cycle,
        'mens_irreg_xmos'=>$request->mens_irreg_xmos,
        'menstrual_cycle_dysmenorrhea'=>$request->dysme,
        'menstrual_cycle_duration'=>$request->mens_duration,
        'menstrual_cycle_padsperday'=>$request->mens_padsperday,
        'menstrual_cycle_medication'=>$request->mens_medication,
        'contraceptive_history'=>$contraceptive_methods,
        'parity_g'=>$request->parity_g,
        'parity_p'=>$request->parity_p,
        'parity_ft'=>$request->parity_ft,
        'parity_pt'=>$request->parity_pt,
        'parity_a'=>$request->parity_a,
        'parity_l'=>$request->parity_l,
        'parity_lnmp'=>$request->parity_lnmp,
        'parity_edc'=>$request->parity_edc_ifpregnant,
        'aog_lnmp'=>$request->aog_bylnmp,
        'aog_eutz'=>$request->aog_byEUTZ,     
    ];

    $personal_history = [
        'patient_id'=>$request->patient_id,
        'smoking'=>$request->smoking_radio,
        'smoking_sticks_per_day'=>$request->smoking_sticks_per_day,
        'smoking_quit_year'=>$request->smoking_year_quit,
        'smoking_remarks'=>$request->smoking_other_remarks,
        'alcohol_drinking'=>$request->alcohol_radio,
        'alcohol_liquor_type'=>$request->alcohol_type,
        'alcohol_drinking_quit_year'=>$request->alcohol_year_quit,
        'illicit_drugs'=>$request->illicit_drugs,
        'illicit_drugs_taken'=>$request->illicit_drugs_token,
        'illicit_drugs_quit_year'=>$request->drugs_year_quit,
        'current_medications'=>$request->current_meds,
    ];



    // Save to database
    PastMedicalHistory::create($past_medical_history_data);
    PediatricHistory::create($pediatric_history);
    ObstetricAndGynecologicHistory::create($obstetric_history);
    PersonalAndSocialHistory::create($personal_history);

    // Debugging
    dd($request->all(),
    $past_medical_history_data,
    $comorbidities, 
    $pediatric_history,
    $obstetric_history,
    $contraceptive_methods,
    $personal_history);

    return redirect()->back()->with('success', 'Past medical history saved successfully!');

        }
    
}
