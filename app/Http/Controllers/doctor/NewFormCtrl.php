<?php

namespace App\Http\Controllers\doctor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PastMedicalHistory;
use App\PediatricHistory;

class NewFormCtrl extends Controller
{
    public function index(){
        return view('modal/revised_normal_form');
    }
    
    public function saveReferral(Request $request)
    {
       // Initialize arrays for storing medical history information
$commordities = [];
$heredofamilial_diseases = [];
$allergies = [];

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
        $commordity = $default_field . ' => ' . $request->$cbox;

            if ($additional_data && $request->$additional_data) {
                if ($default_field == "Cancer" || $default_field == "Others") {
                 $commordity .= ', ' . $default_field . ' =>' . $request->$additional_data;
                } else {
                 $commordity .= ', ' . $default_field . ' Year => ' . $request->$additional_data;
                }
                
            }

        $commordities[] = $commordity;
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
        $heredofamilial = $default_field . ' => ' . $request->$cbox;

        if ($side && $request->$side) {
            $heredofamilial .= ', ' . $default_field . ' side => ' . $request->$side;
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
        $allergy = $default_field . ' => ' . $request->$cbox;

        if ($cause && $request->$cause) {
            $allergy .= ', Cause => ' . $request->$cause;
        }

        $allergies[] = $allergy;
    }
}

// Prepare past medical history data for database
$past_medical_history_data = [
    'patient_id' => $request->patient_id,
    'commordities' => $commordities,
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


// Convert arrays to strings for database storage
$commordities = implode(',', $commordities);
$heredofamilial_diseases = implode(',', $heredofamilial_diseases);
$allergies = implode(',', $allergies);

// Save to database
PastMedicalHistory::create($past_medical_history_data);
PediatricHistory::create($pediatric_history);


dd($request->all());

return redirect()->back()->with('success', 'Past medical history saved successfully!');

    }
    
}
