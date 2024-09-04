<?php

namespace App\Http\Controllers\doctor;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;

use App\PastMedicalHistory;
use App\PediatricHistory;
use App\ObstetricAndGynecologicHistory;
use App\PersonalAndSocialHistory;
use App\ReviewOfSystems;
use App\NutritionalStatus;
use App\GlasgoComaScale;
use App\LatestVitalSigns;
use App\PertinentLaboratory;
use App\Pregnancy;
use DB;
use Anouar\Fpdf\Fpdf;;

class NewFormCtrl extends Controller
{
    public function index()
    {
        return view('modal/revised_normal_form');
    }

    public function view_info()
    {
        return view('revised_form\revised_referral_info');
    }

    public function redirect_referral_info($patient_id)
    {
        $data = DB::table('past_medical_history')
            ->join('pediatric_history', 'past_medical_history.patient_id', '=', 'pediatric_history.patient_id')
            ->join('nutritional_status', 'past_medical_history.patient_id', '=', 'nutritional_status.patient_id')
            ->join('glasgow_coma_scale', 'past_medical_history.patient_id', '=', 'glasgow_coma_scale.patient_id')
            ->join('review_of_system', 'past_medical_history.patient_id', '=', 'review_of_system.patient_id')
            ->join('obstetric_and_gynecologic_history', 'past_medical_history.patient_id', '=', 'obstetric_and_gynecologic_history.patient_id')
            ->join('latest_vital_signs', 'past_medical_history.patient_id', '=', 'latest_vital_signs.patient_id')
            ->join('personal_and_social_history', 'past_medical_history.patient_id', '=', 'personal_and_social_history.patient_id')
            ->join('pertinent_laboratory', 'past_medical_history.patient_id', '=', 'pertinent_laboratory.patient_id')
            ->select(
                'past_medical_history.*',
                'pediatric_history.*',
                'nutritional_status.*',
                'glasgow_coma_scale.*',
                'review_of_system.*',
                'obstetric_and_gynecologic_history.*',
                'latest_vital_signs.*',
                'personal_and_social_history.*',
                'pertinent_laboratory.*',
            )
            ->where('past_medical_history.patient_id', $patient_id)
            ->first();

        $pregnancy_data = DB::table('pregnancy')
            ->where('patient_id', $patient_id)
            ->get();

        // // Debugging
        // dd($data);

        return view('revised_form.revised_referral_info', ['data' => $data, 'pregnancy_data' => $pregnancy_data]);
    }


    public function saveReferral(Request $request)
    {
        // Initialize arrays for storing medical history information
        $comorbidities = [];
        $heredofamilial_diseases = [];
        $allergies = [];
        $contraceptive_methods = [];
        $pertinent_methods = [];
        $rs_skin_methods = [];
        $rs_head_methods = [];
        $rs_eyes_methods = [];
        $rs_ears_methods = [];
        $rs_nose_methods = [];
        $rs_mouth_methods = [];
        $rs_neck_methods = [];
        $rs_breast_methods = [];
        $rs_respiratory_methods = [];
        $rs_gastrointestinal_methods = [];
        $rs_urinary_methods = [];
        $rs_peripheral_methods = [];
        $rs_musculoskeletal_methods = [];
        $rs_neurologic_methods = [];
        $rs_hematologic_methods = [];
        $rs_endocrine_methods = [];
        $rs_psychiatric_methods = [];

        $patient_id = 7;

        // Define the fields for comorbidities with associated additional data (year or string)
        $comorbidity_fields = [
            'Select All' => [
                'cbox' => 'comor_all_cbox',
            ],
            'None' => [
                'cbox' => 'comor_none_cbox',
            ],
            'Hypertension' => [
                'cbox' => 'comor_hyper_cbox',
            ],
            'Diabetes' => [
                'cbox' => 'comor_diab_cbox',
            ],
            'Asthma' => [
                'cbox' => 'comor_asthma_cbox',
            ],
            'COPD' => [
                'cbox' => 'comor_copd_cbox',
            ],
            'Dyslipidemia' => [
                'cbox' => 'comor_dyslip_cbox',
            ],
            'Thyroid Disease' => [
                'cbox' => 'comor_thyroid_cbox',
            ],
            'Cancer' => [
                'cbox' => 'comor_cancer_cbox',
            ],
            'Others' => [
                'cbox' => 'comor_others_cbox',
            ]
        ];
        // Define the fields for heredofamilial diseases with associated additional data (side of the family)
        $heredofamilial_diseases_fields = [
            'Select All' => [
                'cbox' => 'heredo_all_cbox',
            ],
            'None' => [
                'cbox' => 'heredo_none_cbox',
            ],
            'Hypertension' => [
                'cbox' => 'heredo_hyper_cbox',
            ],
            'Diabetes' => [
                'cbox' => 'heredo_diab_cbox',
            ],
            'Asthma' => [
                'cbox' => 'heredo_asthma_cbox',
            ],
            'Cancer' => [
                'cbox' => 'heredo_cancer_cbox',
            ],
            'Kidney Disease' => [
                'cbox' => 'heredo_kidney_cbox',
            ],
            'Thyroid Disease' => [
                'cbox' => 'heredo_thyroid_cbox',
            ],
            'Others' => [
                'cbox' => 'heredo_others_cbox',
            ],
        ];
        // Define the fields for allergies with associated causes
        $allergies_fields = [
            'Select All' => [
                'cbox' => 'allergy_all_cbox',
            ],
            'None' => [
                'cbox' => 'allergy_none_cbox',
            ],
            'Food' => [
                'cbox' => 'allergy_food_cbox',
            ],
            'Drugs' => [
                'cbox' => 'allergy_drug_cbox',
            ],
            'Others' => [
                'cbox' => 'allergy_other_cbox',
            ]
        ];
        //  Define the fields for review system with associated causes
        $rs_skin_fields = [
            'Select All' => [
                'cbox' => 'rs_skin_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_skin_none_cbox',
            ],
            'Rashes' => [
                'cbox' => 'rs_skin_rashes_cbox',
            ],
            'Itching' => [
                'cbox' => 'rs_skin_itching_cbox',
            ],
            'Change in hair or nails' => [
                'cbox' => 'rs_skin_hairchange_cbox',
            ],
        ];
        $rs_head_fields = [
            'Select All' => [
                'cbox' => 'rs_head_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_head_none_cbox',
            ],
            'Headaches' => [
                'cbox' => 'rs_head_headache_cbox',
            ],
            'Head injury' => [
                'cbox' => 'rs_head_injury_cbox',
            ],
        ];
        $rs_eyes_fields = [
            'Select All' => [
                'cbox' => 'rs_eyes_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_eyes_none_cbox',
            ],
            'Glasses or Contacts' => [
                'cbox' => 'rs_eyes_glasses_cbox',
            ],
            'Change in vision' => [
                'cbox' => 'rs_eyes_vision_cbox',
            ],
            'Eye pain' => [
                'cbox' => 'rs_eyes_pain_cbox',
            ],
            'Double Vision' => [
                'cbox' => 'rs_eyes_doublevision_cbox',
            ],
            'Flashing lights' => [
                'cbox' => 'rs_eyes_flashing_cbox',
            ],
            'Glaucoma/Cataracts' => [
                'cbox' => 'rs_eyes_glaucoma_cbox',
            ],
            'Last eye exam' => [
                'cbox' => 'rs_eye_exam_cbox',
            ],
        ];
        $rs_ears_fields = [
            'Select All' => [
                'cbox' => 'rs_ears_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_ears_none_cbox',
            ],
            'Change in hearing' => [
                'cbox' => 'rs_ears_changehearing_cbox',
            ],
            'Ear pain' => [
                'cbox' => 'rs_ears_pain_cbox',
            ],
            'Ear discharge' => [
                'cbox' => 'rs_ears_discharge_cbox',
            ],
            'Ringing' => [
                'cbox' => 'rs_ears_ringing_cbox',
            ],
            'Dizziness' => [
                'cbox' => 'rs_ears_dizziness_cbox',
            ],
        ];
        $rs_nose_fields = [
            'Select All' => [
                'cbox' => 'rs_nose_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_nose_none_cbox',
            ],
            'Nose bleeds' => [
                'cbox' => 'rs_nose_bleeds_cbox',
            ],
            'Nasal stuffiness' => [
                'cbox' => 'rs_nose_stuff_cbox',
            ],
            'Frequent Colds' => [
                'cbox' => 'rs_nose_colds_cbox',
            ],
        ];
        $rs_mouth_fields = [
            'Select All' => [
                'cbox' => 'rs_mouth_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_mouth_none_cbox',
            ],
            'Bleeding gums' => [
                'cbox' => 'rs_mouth_bleed_cbox',
            ],
            'Sore tongue' => [
                'cbox' => 'rs_mouth_soretongue_cbox',
            ],
            'Sore throat' => [
                'cbox' => 'rs_mouth_sorethroat_cbox',
            ],
            'Hoarseness' => [
                'cbox' => 'rs_mouth_hoarse_cbox',
            ],
        ];
        $rs_neck_fields = [
            'Select All' => [
                'cbox' => 'rs_neck_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_neck_none_cbox',
            ],
            'Lumps' => [
                'cbox' => 'rs_neck_lumps_cbox',
            ],
            'Swollen glands' => [
                'cbox' => 'rs_neck_swollen_cbox',
            ],
            'Goiter' => [
                'cbox' => 'rs_neck_goiter_cbox',
            ],
            'Stiffness' => [
                'cbox' => 'rs_neck_stiff_cbox',
            ],
        ];

        $rs_breast_fields = [
            'Select All' => [
                'cbox' => 'rs_breast_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_breast_none_cbox',
            ],
            'Lumps' => [
                'cbox' => 'rs_breast_lumps_cbox',
            ],
            'Pain' => [
                'cbox' => 'rs_breast_pain_cbox',
            ],
            'Nipple discharge' => [
                'cbox' => 'rs_breast_discharge_cbox',
            ],
            'BSE' => [
                'cbox' => 'rs_breast_bse_cbox',
            ],
        ];
        $rs_respiratory_fields = [
            'Select All' => [
                'cbox' => 'rs_respi_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_respi_none_cbox',
            ],
            'Shortness of breath' => [
                'cbox' => 'rs_respi_shortness_cbox',
            ],
            'Cough' => [
                'cbox' => 'rs_respi_cough_cbox',
            ],
            'Production of phlegm color' => [
                'cbox' => 'rs_respi_phlegm_cbox',
            ],
            'Wheezing' => [
                'cbox' => 'rs_respi_wheezing_cbox',
            ],
            'Coughing up blood' => [
                'cbox' => 'rs_respi_coughblood_cbox',
            ],
            'Chest pain' => [
                'cbox' => 'rs_respi_chestpain_cbox',
            ],
            'Fever' => [
                'cbox' => 'rs_respi_fever_cbox',
            ],
            'Night sweats' => [
                'cbox' => 'rs_respi_sweats_cbox',
            ],
            'Swelling in hands/feet' => [
                'cbox' => 'rs_respi_swelling_cbox',
            ],
            'Blue fingers/toes' => [
                'cbox' => 'rs_respi_bluefingers_cbox',
            ],
            'High blood pressure' => [
                'cbox' => 'rs_respi_highbp_cbox',
            ],
            'Skipping heart beats' => [
                'cbox' => 'rs_respi_skipheartbeats_cbox',
            ],
            'Heart murmur' => [
                'cbox' => 'rs_respi_heartmurmur_cbox',
            ],
            'HX of heart medication' => [
                'cbox' => 'rs_respi_hxheart_cbox',
            ],
            'Bronchitis/emphysema' => [
                'cbox' => 'rs_respi_brochitis_cbox',
            ],
            'Rheumatic heart disease' => [
                'cbox' => 'rs_respi_rheumaticheart_cbox',
            ],

        ];
        $rs_gastrointestinal_fields = [
            'Select All' => [
                'cbox' => 'rs_gastro_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_gastro_none_cbox',
            ],
            'Change of appetite or weight' => [
                'cbox' => 'rs_gastro_appetite_cbox',
            ],
            'Problems swallowing' => [
                'cbox' => 'rs_gastro_swallow_cbox',
            ],
            'Nausea' => [
                'cbox' => 'rs_gastro_nausea_cbox',
            ],
            'Heartburn' => [
                'cbox' => 'rs_gastro_heartburn_cbox',
            ],
            'Vomiting' => [
                'cbox' => 'rs_gastro_vomit_cbox',
            ],
            'Vomiting Blood' => [
                'cbox' => 'rs_gastro_vomitblood_cbox',
            ],
            'Constipation' => [
                'cbox' => 'rs_gastro_constipation_cbox',
            ],
            'Diarrhea' => [
                'cbox' => 'rs_gastro_diarrhea_cbox',
            ],
            'Change in bowel habits' => [
                'cbox' => 'rs_gastro_bowel_cbox',
            ],
            'Abdominal pain' => [
                'cbox' => 'rs_gastro_abdominal_cbox',
            ],
            'Excessive belching' => [
                'cbox' => 'rs_gastro_belching_cbox',
            ],
            'Excessive flatus' => [
                'cbox' => 'rs_gastro_flatus_cbox',
            ],
            'Yellow color of skin (Jaundice/Hepatitis)' => [
                'cbox' => 'rs_gastro_jaundice_cbox',
            ],
            'Food intolerance' => [
                'cbox' => 'rs_gastro_intolerance_cbox',
            ],
            'Rectal bleeding/Hemorrhoids' => [
                'cbox' => 'rs_gastro_rectalbleed_cbox',
            ],
        ];
        $rs_urinary_fields = [
            'Select All' => [
                'cbox' => 'rs_urin_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_urin_none_cbox',
            ],
            'Difficulty in urination' => [
                'cbox' => 'rs_urin_difficult_cbox',
            ],
            'Pain or burning on urination' => [
                'cbox' => 'rs_urin_pain_cbox',
            ],
            'Frequent urination at night' => [
                'cbox' => 'rs_urin_frequent_cbox',
            ],
            'Urgent need to urinate' => [
                'cbox' => 'rs_urin_urgent_cbox',
            ],
            'Incontinence of urine' => [
                'cbox' => 'rs_urin_incontinence_cbox',
            ],
            'Dribbling' => [
                'cbox' => 'rs_urin_dribbling_cbox',
            ],
            'Decreased urine stream' => [
                'cbox' => 'rs_urin_decreased_cbox',
            ],
            'Blood in urine' => [
                'cbox' => 'rs_urin_blood_cbox',
            ],
            'UTI/stones/prostate infection' => [
                'cbox' => 'rs_urin_uti_cbox',
            ],
        ];
        $rs_peripheral_fields = [
            'Select All' => [
                'cbox' => 'rs_peri_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_peri_none_cbox',
            ],
            'Leg cramps' => [
                'cbox' => 'rs_peri_legcramp_cbox',
            ],
            'Varicose veins' => [
                'cbox' => 'rs_peri_varicose_cbox',
            ],
            'Clots in veins' => [
                'cbox' => 'rs_peri_veinclot_cbox',
            ],
        ];

        $rs_musculoskeletal_fields = [
            'Select All' => [
                'cbox' => 'rs_muscle_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_muscle_none_cbox',
            ],
            'Pain' => [
                'cbox' => 'rs_musclgit_cbox',
            ],
            'Swelling' => [
                'cbox' => 'rs_muscle_swell_cbox',
            ],
            'Stiffness' => [
                'cbox' => 'rs_muscle_stiff_cbox',
            ],
            'Decreased joint motion' => [
                'cbox' => 'rs_muscle_decmotion_cbox',
            ],
            'Broken bone' => [
                'cbox' => 'rs_muscle_brokenbone_cbox',
            ],
            'Serious sprains' => [
                'cbox' => 'rs_muscle_sprain_cbox',
            ],
            'Arthritis' => [
                'cbox' => 'rs_muscle_arthritis_cbox',
            ],
            'Gout' => [
                'cbox' => 'rs_muscle_gout_cbox',
            ],
        ];
        $rs_neurologic_fields = [
            'Select All' => [
                'cbox' => 'rs_neuro_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_neuro_none_cbox',
            ],
            'Headaches' => [
                'cbox' => 'rs_neuro_headache_cbox',
            ],
            'Seizures' => [
                'cbox' => 'rs_neuro_seizure_cbox',
            ],
            'Loss of Consciousness/Fainting' => [
                'cbox' => 'rs_neuro_faint_cbox',
            ],
            'Paralysis' => [
                'cbox' => 'rs_neuro_paralysis_cbox',
            ],
            'Weakness' => [
                'cbox' => 'rs_neuro_weakness_cbox',
            ],
            'Loss of muscle size' => [
                'cbox' => 'rs_neuro_sizeloss_cbox',
            ],
            'Muscle Spasm' => [
                'cbox' => 'rs_neuro_spasm_cbox',
            ],
            'Tremor' => [
                'cbox' => 'rs_neuro_tremor_cbox',
            ],
            'Involuntary movement' => [
                'cbox' => 'rs_neuro_involuntary_cbox',
            ],
            'Incoordination' => [
                'cbox' => 'rs_neuro_incoordination_cbox',
            ],
            'Numbness' => [
                'cbox' => 'rs_neuro_numbness_cbox',
            ],
            'Feeling of pins and needles/tingles' => [
                'cbox' => 'rs_neuro_tingles_cbox',
            ],
        ];
        $rs_hematologic_fields = [
            'Select All' => [
                'cbox' => 'rs_hema_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_hema_none_cbox',
            ],
            'Anemia' => [
                'cbox' => 'rs_hema_anemia_cbox',
            ],
            'Easy bruising/bleeding' => [
                'cbox' => 'rs_hema_bruising_cbox',
            ],
            'Past Transfusions' => [
                'cbox' => 'rss_hema_transfusion_cbox',
            ],
        ];
        $rs_endocrine_fields = [
            'Select All' => [
                'cbox' => 'rs_endo_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_endo_none_cbox',
            ],
            'Abnormal growth' => [
                'cbox' => 'rs_endo_abnormal_cbox',
            ],
            'Increased appetite' => [
                'cbox' => 'rs_endo_appetite_cbox',
            ],
            'Increased thirst' => [
                'cbox' => 'rs_endo_thirst_cbox',
            ],
            'Increased urine production' => [
                'cbox' => 'rs_endo_urine_cbox',
            ],
            'Thyroid troubles' => [
                'cbox' => 'rs_endo_thyroid_cbox',
            ],
            'Heat/cold intolerancee' => [
                'cbox' => 'rs_endo_heatcold_cbox',
            ],
            'Excessive sweating' => [
                'cbox' => 'rs_endo_sweat_cbox',
            ],
            'Diabetes' => [
                'cbox' => 'rs_endo_diabetes_cbox',
            ],
        ];
        $rs_psychiatric_fields = [
            'Select All' => [
                'cbox' => 'rs_psych_all_cbox',
            ],
            'None' => [
                'cbox' => 'rs_psych_none_cbox',
            ],
            'Tension/Anxiety' => [
                'cbox' => 'rs_psych_tension_cbox',
            ],
            'Depression/suicide ideation' => [
                'cbox' => 'rs_psych_depression_cbox',
            ],
            'Memory problems' => [
                'cbox' => 'rs_psych_memory_cbox',
            ],
            'Unusual problems' => [
                'cbox' => 'rs_psych_unusual_cbox',
            ],
            'Sleep problems' => [
                'cbox' => 'rs_psych_sleep_cbox',
            ],
            'Past treatment with psychiatrist' => [
                'cbox' => 'rs_psych_treatment_cbox',
            ],
            'Change in mood/change in attitude towards family/friends' => [
                'cbox' => 'rs_psych_moodchange_cbox',
            ],
        ];
        // Define the fields for contraceptive with associated causes
        $contraceptive_fields = [
            'Pills' => [
                'cbox' => 'contraceptive_pills_cbox',
            ],
            'IUD' => [
                'cbox' => 'contraceptive_iud_cbox',
            ],
            'Rhythm' => [
                'cbox' => 'contraceptive_rhythm_cbox',
            ],
            'Condom' => [
                'cbox' => 'contraceptive_condom_cbox',
            ],
            'Other' => [
                'cbox' => 'contraceptive_other_cbox',
            ],
        ];

        $pertinent_fields = [
            'Select All' => [
                'cbox' => 'lab_all_cbox',
            ],
            'UA' => [
                'cbox' => 'lab_ua_cbox',
            ],
            'CBC' => [
                'cbox' => 'lab_cbc_cbox',
            ],
            'X-RAY' => [
                'cbox' => 'lab_xray_cbox',
            ],
            'Others' => [
                'cbox' => 'lab_others_cbox',
            ],
        ];

        $this->dataArray($contraceptive_fields, $contraceptive_methods, $request);
        $this->dataArray($allergies_fields, $allergies, $request);
        $this->dataArray($heredofamilial_diseases_fields, $heredofamilial_diseases, $request);
        $this->dataArray($pertinent_fields, $pertinent_methods, $request);
        $this->dataArray($comorbidity_fields, $comorbidities, $request);


        $this->dataArray($rs_skin_fields, $rs_skin_methods, $request);
        $this->dataArray($rs_head_fields, $rs_head_methods, $request);
        $this->dataArray($rs_eyes_fields, $rs_eyes_methods, $request);
        $this->dataArray($rs_ears_fields, $rs_ears_methods, $request);
        $this->dataArray($rs_nose_fields, $rs_nose_methods, $request);
        $this->dataArray($rs_mouth_fields, $rs_mouth_methods, $request);
        $this->dataArray($rs_neck_fields, $rs_neck_methods, $request);
        $this->dataArray($rs_breast_fields, $rs_breast_methods, $request);
        $this->dataArray($rs_respiratory_fields, $rs_respiratory_methods, $request);
        $this->dataArray($rs_gastrointestinal_fields, $rs_gastrointestinal_methods, $request);
        $this->dataArray($rs_urinary_fields, $rs_urinary_methods, $request);
        $this->dataArray($rs_peripheral_fields, $rs_peripheral_methods, $request);
        $this->dataArray($rs_musculoskeletal_fields, $rs_musculoskeletal_methods, $request);
        $this->dataArray($rs_neurologic_fields, $rs_neurologic_methods, $request);
        $this->dataArray($rs_hematologic_fields, $rs_hematologic_methods, $request);
        $this->dataArray($rs_endocrine_fields, $rs_endocrine_methods, $request);
        $this->dataArray($rs_psychiatric_fields, $rs_psychiatric_methods, $request);

        // Convert arrays to strings for database storage
        $comorbidities = implode(',', $comorbidities);
        $heredofamilial_diseases = implode(',', $heredofamilial_diseases);
        $allergies = implode(',', $allergies);
        $contraceptive_methods = implode(',', $contraceptive_methods);
        $pertinent_methods = implode(',', $pertinent_methods);
        $rs_skin_methods = implode(',', $rs_skin_methods);
        $rs_head_methods = implode(',', $rs_head_methods);
        $rs_eyes_methods = implode(',', $rs_eyes_methods);
        $rs_ears_methods = implode(',', $rs_ears_methods);
        $rs_nose_methods = implode(',', $rs_nose_methods);
        $rs_mouth_methods = implode(',', $rs_mouth_methods);
        $rs_neck_methods = implode(',', $rs_neck_methods);
        $rs_breast_methods = implode(',', $rs_breast_methods);
        $rs_respiratory_methods = implode(',', $rs_respiratory_methods);
        $rs_gastrointestinal_methods = implode(',', $rs_gastrointestinal_methods);
        $rs_urinary_methods = implode(',', $rs_urinary_methods);
        $rs_peripheral_methods = implode(',', $rs_peripheral_methods);
        $rs_musculoskeletal_methods = implode(',', $rs_musculoskeletal_methods);
        $rs_neurologic_methods = implode(',', $rs_neurologic_methods);
        $rs_hematologic_methods = implode(',', $rs_hematologic_methods);
        $rs_endocrine_methods = implode(',', $rs_endocrine_methods);
        $rs_psychiatric_methods = implode(',', $rs_psychiatric_methods);

        // Prepare past medical history data for database
        $past_medical_history_data = [
            'patient_id' => $patient_id,
            'commordities' => $comorbidities,
            'commordities_hyper_year' => $request->hyper_year,
            'commordities_diabetes_year' => $request->diab_year,
            'commordities_asthma_year' => $request->asthma_year,
            'commordities_others' => $request->comor_others,
            'commordities_cancer' => $request->comor_cancer,
            'heredofamilial_diseases' => $heredofamilial_diseases,
            'heredo_hyper_side' => $request->heredo_hypertension_side,
            'heredo_diab_side' => $request->heredo_diabetes_side,
            'heredo_asthma_side' => $request->heredo_asthma_side,
            'heredo_cancer_side' => $request->heredo_cancer_side,
            'heredo_kidney_side' => $request->heredo_kidney_side,
            'heredo_thyroid_side' => $request->heredo_thyroid_side,
            'heredo_others' => $request->heredo_others_side,
            'allergies' => $allergies,
            'allergy_food_cause' => $request->allergy_food_cause,
            'allergy_drugs_cause' => $request->allergy_drug_cause,
            'allergy_others_cause' => $request->allergy_other_cause,
            'previous_hospitalization' => $request->previous_hospitalization,
        ];

        // Prepare pediatric history data for database
        $pediatric_history = [
            'patient_id' => $patient_id,
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
            'patient_id' => $patient_id,
            'menarche' => $request->menarche,
            'menopause' => $request->menopausal,
            'menopausal_age' => $request->menopausal_age,
            'menstrual_cycle' => $request->mens_cycle,
            'mens_irreg_xmos' => $request->mens_irreg_xmos,
            'menstrual_cycle_dysmenorrhea' => $request->dysme,
            'menstrual_cycle_duration' => $request->mens_duration,
            'menstrual_cycle_padsperday' => $request->mens_padsperday,
            'menstrual_cycle_medication' => $request->mens_medication,
            'contraceptive_history' => $contraceptive_methods,
            'contraceptive_others' => $request->contraceptive_other,
            'prenatal_history' => $request->prenatal_history,
            'parity_g' => $request->parity_g,
            'parity_p' => $request->parity_p,
            'parity_ft' => $request->parity_ft,
            'parity_pt' => $request->parity_pt,
            'parity_a' => $request->parity_a,
            'parity_l' => $request->parity_l,
            'parity_lnmp' => $request->parity_lnmp,
            'parity_edc' => $request->parity_edc_ifpregnant,
            'aog_lnmp' => $request->aog_bylnmp,
            'aog_eutz' => $request->aog_byEUTZ,
        ];
        $pertinent_lab = [
            'patient_id' => $patient_id,
            'pertinent_laboratory_and_procedures' => $pertinent_methods,
            'lab_procedure_other' => $request->lab_procedure_other,
        ];

        $personal_history = [
            'patient_id' => $patient_id,
            'smoking' => $request->smoking_radio,
            'smoking_sticks_per_day' => $request->smoking_sticks_per_day,
            'smoking_quit_year' => $request->smoking_year_quit,
            'smoking_remarks' => $request->smoking_other_remarks,
            'alcohol_drinking' => $request->alcohol_radio,
            'alcohol_liquor_type' => $request->alcohol_type,
            'alcohol_bottles_per_day' => $request->alcohol_bottles,
            'alcohol_drinking_quit_year' => $request->alcohol_year_quit,
            'illicit_drugs' => $request->illicit_drugs,
            'illicit_drugs_taken' => $request->illicit_drugs_token,
            'illicit_drugs_quit_year' => $request->drugs_year_quit,
            'current_medications' => $request->current_meds,
        ];
        $review_of_system = [
            'patient_id' => $patient_id,
            'skin' => $rs_skin_methods,
            'head' => $rs_head_methods,
            'eyes' => $rs_eyes_methods,
            'ears' => $rs_ears_methods,
            'nose_or_sinuses' => $rs_nose_methods,
            'mouth_or_throat' => $rs_mouth_methods,
            'neck' => $rs_neck_methods,
            'breast' => $rs_breast_methods,
            'respiratory_or_cardiac' => $rs_respiratory_methods,
            'gastrointestinal' => $rs_gastrointestinal_methods,
            'urinary' => $rs_urinary_methods,
            'peripheral_vascular' => $rs_peripheral_methods,
            'musculoskeletal' => $rs_musculoskeletal_methods,
            'neurologic' => $rs_neurologic_methods,
            'hematologic' => $rs_hematologic_methods,
            'endocrine' => $rs_endocrine_methods,
            'psychiatric' => $rs_psychiatric_methods,
        ];
        $nutritional_status = [
            'patient_id' => $patient_id,
            'diet' => $request->diet_radio,
            'specify_diets' => $request->diet,
        ];
        $glasgocoma_scale = [
            'patient_id' => $patient_id,
            'pupil_size_chart' => $request->glasgow_pupil_btn,
            'motor_response' => $request->motor_radio,
            'verbal_response' => $request->verbal_radio,
            'eye_response' => $request->eye_radio,
            'gsc_score' => $request->gcs_score,

        ];
        $vital_signs = [
            'patient_id' => $patient_id,
            'temperature' => $request->vital_temp,
            'pulse_rate' => $request->vital_pulse,
            'respiratory_rate' => $request->vital_respi_rate,
            'blood_pressure' => $request->vital_bp,
            'oxygen_saturation' => $request->vital_oxy_saturation,
        ];



        $orders = $request->input('pregnancy_history_order');
        $years = $request->input('pregnancy_history_year');
        $gestations = $request->input('pregnancy_history_gestation');
        $outcomes = $request->input('pregnancy_history_outcome');
        $placesOfBirth = $request->input('pregnancy_history_placeofbirth');
        $sexes = $request->input('prenatal_history_sex');
        $birthWeights = $request->input('pregnancy_history_birthweight');
        $presentStatuses = $request->input('pregnancy_history_presentstatus');
        $complications = $request->input('pregnancy_history_complications');

        foreach ($orders as $key => $order) {
            Pregnancy::create([
                'patient_id' => $patient_id,
                'pregnancy_order' => $order,
                'pregnancy_year' => $years[$key],
                'pregnancy_gestation_completed' => $gestations[$key],
                'pregnancy_outcome' => $outcomes[$key],
                'pregnancy_place_of_birth' => $placesOfBirth[$key],
                'pregnancy_sex' => $sexes[$key],
                'pregnancy_birth_weight' => $birthWeights[$key],
                'pregnancy_present_status' => $presentStatuses[$key],
                'pregnancy_complication' => $complications[$key],
            ]);
        }


        // dd($request->all(), $request->comor_others, $request->allergy_other_cause);
        // Save to database
        PastMedicalHistory::create($past_medical_history_data);
        PertinentLaboratory::create($pertinent_lab);
        PediatricHistory::create($pediatric_history);
        ObstetricAndGynecologicHistory::create($obstetric_history);
        PersonalAndSocialHistory::create($personal_history);
        ReviewOfSystems::create($review_of_system);
        NutritionalStatus::create($nutritional_status);
        GlasgoComaScale::create($glasgocoma_scale);
        LatestVitalSigns::create($vital_signs);

        return redirect("/revised/referral/info/{$patient_id}");
    }

    public function dataArray($fields, &$output, $request)
    {
        foreach ($fields as $key => $field) {
            if ($request->has($field['cbox'])) {
                $output[] = $key;
            }
        }
    }

    public function generatePdf($patient_id)
    {
        // Fetch the data needed for the PDF
        $data = DB::table('past_medical_history')
            ->join('pediatric_history', 'past_medical_history.patient_id', '=', 'pediatric_history.patient_id')
            ->join('nutritional_status', 'past_medical_history.patient_id', '=', 'nutritional_status.patient_id')
            ->join('glasgow_coma_scale', 'past_medical_history.patient_id', '=', 'glasgow_coma_scale.patient_id')
            ->join('review_of_system', 'past_medical_history.patient_id', '=', 'review_of_system.patient_id')
            ->join('obstetric_and_gynecologic_history', 'past_medical_history.patient_id', '=', 'obstetric_and_gynecologic_history.patient_id')
            ->join('latest_vital_signs', 'past_medical_history.patient_id', '=', 'latest_vital_signs.patient_id')
            ->join('personal_and_social_history', 'past_medical_history.patient_id', '=', 'personal_and_social_history.patient_id')
            ->join('pertinent_laboratory', 'past_medical_history.patient_id', '=', 'pertinent_laboratory.patient_id')
            ->join('pregnancy', 'past_medical_history.patient_id', '=', 'pregnancy.patient_id')
            ->select(
                'past_medical_history.*',
                'pediatric_history.*',
                'nutritional_status.*',
                'glasgow_coma_scale.*',
                'review_of_system.*',
                'obstetric_and_gynecologic_history.*',
                'latest_vital_signs.*',
                'personal_and_social_history.*',
                'pertinent_laboratory.*',
                'pregnancy.*'
            )
            ->where('past_medical_history.patient_id', $patient_id)
            ->first();

        $pdf = new Fpdf();
        $x = ($pdf->w) - 20;

        $pdf->setTopMargin(17);
        $pdf->setTitle($data->patient_id);
        $pdf->addPage();

        $pdf->SetFont('Arial', 'B', 12);

        $pdf->SetFillColor(200, 200, 200);
        $pdf->SetTextColor(30);
        $pdf->SetDrawColor(200);
        $pdf->SetLineWidth(.3);

        // PAST MEDICAL HISTORY
        $pdf->MultiCell(0, 7, "PAST MEDICAL HISTORY", 1, 'L', true);
        $pdf->SetFillColor(255, 250, 205);
        $pdf->SetTextColor(40);
        $pdf->SetDrawColor(230);
        $pdf->SetLineWidth(.3);

        $pdf->MultiCell(0, 7, "Comorbidities:" . self::green($pdf, $data->commordities, 'Comorbidities'), 1, 'L');
        $pdf->MultiCell(0, 7, "Allergies:" . self::green($pdf, $data->allergies, 'Allergies'), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "HEREDOFAMILIAL DISEASES: ") . "\n" . self::staticGreen($pdf, $data->heredofamilial_diseases), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "PREVIOUS HOSPITALIZATION(S) and OPERATION(S): ") . "\n" . self::staticGreen($pdf, $data->previous_hospitalization), 1, 'L');

        $pdf->ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->SetTextColor(30);
        $pdf->SetDrawColor(200);
        $pdf->SetLineWidth(.3);

        //PEDIATRIC HISTORY
        $pdf->MultiCell(0, 7, "PEDIATRIC HISTORY", 1, 'L', true);
        $pdf->SetFillColor(255, 250, 205);
        $pdf->SetTextColor(40);
        $pdf->SetDrawColor(230);
        $pdf->SetLineWidth(.3);
        $pdf->MultiCell(0, 7, "Prenatal A:" . self::green($pdf, $data->prenatal_a, 'Prenatal A'), 1, 'L');
        $pdf->MultiCell(0, 7, "Prenatal G:" . self::green($pdf, $data->prenatal_g, 'Prenatal G'), 1, 'L');
        $pdf->MultiCell(0, 7, "Prenatal P:" . self::green($pdf, $data->prenatal_p, 'Prenatal P'), 1, 'L');
        $pdf->MultiCell(0, 7, "Maternal Illness:" . self::green($pdf, $data->prenatal_with_maternal_illness, 'Maternal Illness'), 1, 'L');
        $pdf->ln(3);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->MultiCell(0, 7, "NATAL", 1, 'L', true);
        $pdf->MultiCell(0, 7, "Born At:" . self::green($pdf, $data->natal_born_at, 'Born At'), 1, 'L');
        $pdf->MultiCell(0, 7, "Born Address:" . self::green($pdf, $data->natal_born_address, 'Born Address'), 1, 'L');
        $pdf->MultiCell(0, 7, "By:" . self::green($pdf, $data->natal_by, 'By'), 1, 'L');
        $pdf->MultiCell(0, 7, "Via:" . self::green($pdf, $data->natal_via, 'Via'), 1, 'L');
        $pdf->MultiCell(0, 7, "Indication:" . self::green($pdf, $data->natal_indication, 'Indication'), 1, 'L');
        $pdf->MultiCell(0, 7, "Term:" . self::green($pdf, $data->natal_term, 'Term'), 1, 'L');
        $pdf->MultiCell(0, 7, "With Good Cry:" . self::green($pdf, $data->natal_with_good_cry, 'With Good Cry'), 1, 'L');
        $pdf->MultiCell(0, 7, "Other Complications:" . self::green($pdf, $data->natal_other_complications, 'Other Complications'), 1, 'L');
        $pdf->ln(3);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->MultiCell(0, 7, "POST NATAL", 1, 'L', true);
        $pdf->ln(3);
        $pdf->MultiCell(0, 7, "Feeding History", 1, 'L', true);
        $pdf->MultiCell(0, 7, "Breast Feed in mos:" . self::green($pdf, $data->post_natal_bfeedx_month, 'Breast Feed in mos'), 1, 'L');
        $pdf->MultiCell(0, 7, "Specific Formula Feed:" . self::green($pdf, $data->post_natal_ffeed_specify, 'Specific Formula Feed'), 1, 'L');
        $pdf->MultiCell(0, 7, "Started Semi Food in mos:" . self::green($pdf, $data->post_natal_ffeed_specify, 'Started Semi Food in mos'), 1, 'L');
        $pdf->ln(3);
        $pdf->MultiCell(0, 7, "Immunization History", 1, 'L', true);
        if ($data->post_natal_bcg == "Yes") {
            $pdf->MultiCell(0, 7, "BCG:" . self::green($pdf, "Immunized", 'BCG'), 1, 'L');
        }
        if ($data->post_natal_dpt_opv_x == "Yes") {
            $pdf->MultiCell(0, 7, "DPT/OPV:" . self::green($pdf, "Immunized", 'DPT/OPV'), 1, 'L');
            $pdf->MultiCell(0, 7, "DPT/OPV doses:" . self::green($pdf, $data->post_dpt_doses, 'DPT/OPV doses'), 1, 'L');
        }
        if ($data->post_natal_hepB_cbox == "Yes") {
            $pdf->MultiCell(0, 7, "Hep B:" . self::green($pdf, "Immunized", 'Hep B'), 1, 'L');
            $pdf->MultiCell(0, 7, "Hep B doses:" . self::green($pdf, $data->post_natal_hepB_x_doses, 'Hep B doses'), 1, 'L');
        }
        if ($data->post_natal_immu_measles_cbox == "Yes") {
            $pdf->MultiCell(0, 7, "Measles:" . self::green($pdf, "Immunized", 'Measles'), 1, 'L');
        }
        if ($data->post_natal_mmr_cbox == "Yes") {
            $pdf->MultiCell(0, 7, "MMR:" . self::green($pdf, "Immunized", 'MMR'), 1, 'L');
        }
        if ($data->post_natal_others_cbox == "Yes") {
            $pdf->MultiCell(0, 7, "Others:" . self::green($pdf, $data->post_natal_others, 'Others'), 1, 'L');
        }

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->SetTextColor(30);
        $pdf->SetDrawColor(200);
        $pdf->SetLineWidth(.3);
        $pdf->ln(10);

        //OBSTETRIC AND GYNECOLOGIC HISTORY 
        $pdf->MultiCell(0, 7, "OBSTETRIC AND GYNECOLOGIC HISTORY", 1, 'L', true);
        $pdf->SetFillColor(255, 250, 205);
        $pdf->SetTextColor(40);
        $pdf->SetDrawColor(230);
        $pdf->SetLineWidth(.3);

        $pdf->Output();
        exit();



        // dd($data);
    }

    public function orange($pdf, $val, $str)
    {
        $y = $pdf->getY() + 4.5;
        $x = $pdf->getX() + 2;
        $ln = $pdf->GetStringWidth($str) + 1;
        $pdf->SetTextColor(102, 56, 0);
        $pdf->SetFont('Arial', 'I', 10);
        $r = $pdf->Text($x + $ln, $y, $val);
        $pdf->SetFont('Arial', '', 10);
        return $r;
    }

    public function staticGreen($pdf, $val)
    {
        $pdf->SetTextColor(0, 50, 0);
        $pdf->SetFont('Arial', '', 10);
        return $val;
    }

    public function staticBlack($pdf, $val)
    {
        $y = $pdf->getY() + 4.5;
        $x = $pdf->getX() + 2;
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);
        return $pdf->Text($x, $y, $val);
    }

    public function green($pdf, $val, $str)
    {
        $y = $pdf->getY() + 4.5;
        $x = $pdf->getX() + 2;
        $ln = $pdf->GetStringWidth($str) + 1;
        $pdf->SetTextColor(0, 50, 0);
        $pdf->SetFont('Arial', 'I', 10);
        $r = $pdf->Text($x + $ln, $y, $val);
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        return $r;
    }

    public function gray($pdf, $val, $str)
    {
        $y = $pdf->getY() + 4.5;
        $x = $pdf->getX() + 2;
        $ln = $pdf->GetStringWidth($str) + 1;
        $pdf->SetTextColor(51, 51, 51);
        $pdf->SetFont('Arial', 'I', 10);
        $r = $pdf->Text($x + $ln, $y, $val);
        $pdf->SetFont('Arial', '', 10);
        return $r;
    }

    public function black($pdf, $val)
    {
        $y = $pdf->getY() + 4.5;
        $x = $pdf->getX() + 2;
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        return $pdf->Text($x, $y, $val);
    }
}
