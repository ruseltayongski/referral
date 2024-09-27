<?php

namespace App\Http\Controllers\doctor;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Events\NewReferral;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ParamCtrl;


use App\Tracking;
use App\Department;
use App\Activity;
use App\Facility;
use App\TelemedAssignDoctor;
use App\PatientForm;
use App\PregnantForm;
use App\Patients;
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
use Anouar\Fpdf\Fpdf;

use function PHPSTORM_META\type;

;

class NewFormCtrl extends Controller
{
    // Views
    public function index()
    {
        return view('modal.revised_normal_form');
    }

    public function view_info(Request $request)
    {
        dd($request->all());
        return view('revised_form.revised_referral_info');
    }
    public function view_choose_versionModal(){
        return view('modal.choose_version');
    }


    // Get data from Database
    public function fetchDataFromDB($patient_id)
    {
        $data = Patients::table('patients')
            ->joint('past_medical_history', 'patients.id', '=', 'past_medical_history.patient_id')
            ->join('pediatric_history', 'patients.id', '=', 'pediatric_history.patient_id')
            ->join('nutritional_status', 'patients.id', '=', 'nutritional_status.patient_id')
            ->join('glasgow_coma_scale', 'patients.id', '=', 'glasgow_coma_scale.patient_id')
            ->join('review_of_system', 'patients.id', '=', 'review_of_system.patient_id')
            ->join('obstetric_and_gynecologic_history', 'patients.id', '=', 'obstetric_and_gynecologic_history.patient_id')
            ->join('latest_vital_signs', 'patients.id', '=', 'latest_vital_signs.patient_id')
            ->join('personal_and_social_history', 'patients.id', '=', 'personal_and_social_history.patient_id')
            ->join('pertinent_laboratory', 'patients.id', '=', 'pertinent_laboratory.patient_id')
            ->select(
                'patients.id.*',
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
            ->where('patients.id', $patient_id)
            ->first();

        return $data;
    }
    public function fetchPregnant($patient_id)
    {
        $pregnancy_data = DB::table('pregnancy')
            ->where('patient_id', $patient_id)
            ->get();

        return $pregnancy_data;
    }


    // Forms CRUD 

    public function redirect_referral_info($patient_id)
    {

        $data = $this->fetchDataFromDB($patient_id);
        $pregnancy_data = $this->fetchPregnant($patient_id);

        // // Debugging
        // dd($data);

        return view('revised_form.revised_referral_info', ['data' => $data, 'pregnancy_data' => $pregnancy_data]);
    }

    private function prepareData($request, $patient_id)
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

        // $patient_id = 15;

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
            'contraceptive_others' => $request->contraceptive_others,
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

        return [
            'past_medical_history_data' => $past_medical_history_data,
            'pertinent_lab' => $pertinent_lab,
            'pediatric_history' => $pediatric_history,
            'obstetric_history' => $obstetric_history,
            'personal_history' => $personal_history,
            'review_of_system' => $review_of_system,
            'nutritional_status' => $nutritional_status,
            'glasgocoma_scale' => $glasgocoma_scale,
            'vital_signs' => $vital_signs,
        ];
    }

    // public function revisedreferPatient(Request $req,$type){
    //     if ($type == "normal"){
    //         dd($req->all());
    //     }
    // }


    public function saveReferral(Request $request, $type)
    {
        $user = Session::get('auth');
        if($request->telemedicine) {
            $telemedAssignDoctor = TelemedAssignDoctor::where('appointment_id',$request->appointmentId)->where('doctor_id',$request->doctorId)->first();
            if($telemedAssignDoctor->appointment_by) {
                return 'consultation_rejected';
            }
            $telemedAssignDoctor->appointment_by = $user->id;
            $telemedAssignDoctor->save();
        }

        $patient_id = $request->patient_id;
        $user_code = str_pad($user->facility_id,3,0,STR_PAD_LEFT);
        $code = date('ymd').'-'.$user_code.'-'.date('His')."$user->facility_id"."$user->id";
        $unique_id = "$patient_id-$user->facility_id-".date('ymdHis');

        if ($type == "normal"){
         
            // from PatientCtrl

            Patients::where('id',$patient_id)
            ->update([
                'sex' => $request->patient_sex,
                'civil_status' => $request->civil_status,
                'phic_status' => $request->phic_status,
                'phic_id' => $request->phic_id
            ]);

            $data = array(
                'unique_id' => $unique_id,
                'code' => $code,
                'referring_facility' => $user->facility_id,
                'referred_to' => $request->referred_facility,
                'department_id' => $request->referred_department,
                'covid_number' => $request->covid_number,
                'refer_clinical_status' => $request->clinical_status,
                'refer_sur_category' => $request->sur_category,
                'time_referred' => date('Y-m-d H:i:s'),
                'time_transferred' => '',
                'patient_id' => $patient_id,
                'case_summary' => $request->case_summary,
                'reco_summary' => $request->reco_summary,
                'diagnosis' => $request->diagnosis,
                'referring_md' => $user->id,
                'referred_md' => ($request->reffered_md) ? $request->reffered_md: 0,
                'reason_referral' => $request->reason_referral1,
                'other_reason_referral' => $request->other_reason_referral,
                'other_diagnoses' => $request->other_diagnosis,
            );

            $form = PatientForm::create($data);
            
            $file_paths = "";
            if($_FILES["file_upload"]["name"]) {
                ApiController::fileUpload($request);
                for($i = 0; $i < count($_FILES['file_upload']['name']); $i++) {
                    $file = $_FILES['file_upload']['name'][$i];
                    if(isset($file) && !empty($file)) {
                        $username = $user->username;
                        $file_paths .= ApiController::fileUploadUrl().$username."/".$file;
                        if($i + 1 != count($_FILES["file_upload"]["name"])) {
                            $file_paths .= "|";
                        }
                    }
                }
            }
            $form->file_path = $file_paths;
            $form->save();

            foreach($request->icd_ids as $i) {
                $icd = new Icd();
                $icd->code = $form->code;
                $icd->icd_id = $i;
                $icd->save();
            }

            //if($req->referred_facility == 790 && $user->id == 1687) {
            if($request->referred_facility == 790 || $request->referred_facility == 23) {
                $patient = Patients::find($patient_id);
                $patient_name = isset($patient->mname[0]) ? ucfirst($patient->fname).' '.strtoupper($patient->mname[0]).'. '.ucfirst($patient->lname) : ucfirst($patient->fname).' '.ucfirst($patient->lname);
                $this->referred_patient_data = array(
                    "age" => (int)ParamCtrl::getAge($patient->dob),
                    "chiefComplaint" => $request->case_summary,
                    "department" => Department::find($request->referred_department)->description,
                    "patient" => $patient_name,
                    "sex" => $patient->sex,
                    "referring_hospital" => Facility::find($user->facility_id)->name,
                    "referred_to" => $request->referred_facility,
                    "date_referred" => $form->created_at,
                    "userid" => $user->id,
                    "patient_code" => $form->code
                );
                ApiController::pushNotificationCCMC($this->referred_patient_data);
            }//push notification for cebu south medical center
            self::newFormSave($request);
            self::addTracking($code,$patient_id,$user,$request,$type,$form->id,'refer');
         
        }else if ($type == "pregnant") {

            $data = array(
                'unique_id' => $unique_id,
                'code' => $code,
                'referring_facility' => ($user->facility_id) ? $user->facility_id: '',
                'referred_by' => ($user->id) ? $user->id: '',
                'record_no' => ($request->record_no) ? $request->record_no: '',
                'referred_date' => date('Y-m-d H:i:s'),
                'referred_to' => ($request->referred_facility) ? $request->referred_facility: '',
                'department_id' => ($request->referred_department) ? $request->referred_department:'',
                'covid_number' => $request->covid_number,
                'refer_clinical_status' => $request->clinical_status,
                'refer_sur_category' => $request->sur_category,
                'health_worker' => ($request->health_worker) ? $request->health_worker: '',
                'patient_woman_id' => $patient_id,
                'woman_reason' => ($request->woman_reason) ? $request->woman_reason: '',
                'woman_major_findings' => ($request->woman_major_findings) ? $request->woman_major_findings: '',
                'woman_before_treatment' => ($request->woman_before_treatment) ? $request->woman_before_treatment: '',
                'woman_before_given_time' => ($request->woman_before_given_time) ? $request->woman_before_given_time: '',
                'woman_during_transport' => ($request->woman_during_treatment) ? $request->woman_during_treatment: '',
                'woman_transport_given_time' => ($request->woman_during_given_time) ? $request->woman_during_given_time: '',
                'woman_information_given' => ($request->woman_information_given) ? $request->woman_information_given: '',
                'patient_baby_id' => '',
                'baby_reason' => ($request->baby_reason) ? $request->baby_reason: '',
                'baby_major_findings' => ($request->baby_major_findings) ? $request->baby_major_findings: '',
                'baby_last_feed' => ($request->baby_last_feed) ? $request->baby_last_feed: '',
                'baby_before_treatment' => ($request->baby_before_treatment) ? $request->baby_before_treatment: '',
                'baby_before_given_time' => ($request->baby_before_given_time) ? $request->baby_before_given_time: '',
                'baby_during_transport' => ($request->baby_during_treatment) ? $request->baby_during_treatment: '',
                'baby_transport_given_time' => ($request->baby_during_given_time) ? $request->baby_during_given_time: '',
                'baby_information_given' => ($request->baby_information_given) ? $request->baby_information_given: '',
                'notes_diagnoses' => $request->notes_diagnosis,
                'reason_referral' => $request->reason_referral1,
                'other_reason_referral' => $request->other_reason_referral,
                'other_diagnoses' => $request->other_diagnosis,
            );
            $form = PregnantForm::create($data);

            if($request->referred_facility == 790 || $request->referred_facility == 23) {
                $patient = Patients::find($patient_id);
                $patient_name = isset($patient->mname[0]) ? ucfirst($patient->fname).' '.strtoupper($patient->mname[0]).'. '.ucfirst($patient->lname) : ucfirst($patient->fname).' '.ucfirst($patient->lname);
                $this->referred_patient_data = array(
                    "age" => (int)ParamCtrl::getAge($patient->dob),
                    "chiefComplaint" => $request->case_summary,
                    "department" => Department::find($request->referred_department)->description,
                    "patient" => $patient_name,
                    "sex" => $patient->sex,
                    "referring_hospital" => Facility::find($user->facility_id)->name,
                    "referred_to" => $request->referred_facility,
                    "date_referred" => $form->created_at,
                    "userid" => $user->id,
                    "patient_code" => $form->code
                );
                ApiController::pushNotificationCCMC($this->referred_patient_data);
            }
            
            self::newFormSave($request);
            self::newFormPregnant($request);
            self::addTracking($code,$patient_id,$user,$request,$type,$form->id);
        }
        
    }
    public function newFormPregnant($request){
        $patient_id = $request->patient_id;

        $data = $this->prepareData($request, $patient_id);
        ObstetricAndGynecologicHistory::create($data['obstetric_history']);
        PediatricHistory::create($data['pediatric_history']);

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
    }

    public function newFormSave($request){
        // new save form
        $patient_id = $request->patient_id;

        $data = $this->prepareData($request, $patient_id);

        PastMedicalHistory::create($data['past_medical_history_data']);
        PertinentLaboratory::create($data['pertinent_lab']);
        PersonalAndSocialHistory::create($data['personal_history']);
        ReviewOfSystems::create($data['review_of_system']);
        NutritionalStatus::create($data['nutritional_status']);
        GlasgoComaScale::create($data['glasgocoma_scale']);
        LatestVitalSigns::create($data['vital_signs']);

    }

    public function addTracking($code, $patient_id, $user, $req, $type, $form_id, $status='')
    {
        $match = array(
            'code' => $code
        );
        $track = array(
            'patient_id' => $patient_id,
            'date_referred' => date('Y-m-d H:i:s'),
            'referred_from' => ($status == 'walkin') ? $req->referring_facility_walkin : $user->facility_id,
            'referred_to' => ($status == 'walkin') ? $user->facility_id : $req->referred_facility,
            'department_id' => $req->referred_department,
            'referring_md' => ($status == 'walkin') ? 0 : $user->id,
            'action_md' => '',
            'type' => $type,
            'form_id' => $form_id,
            'remarks' => ($req->reason) ? $req->reason : '',
            'status' => ($status == 'walkin') ? 'accepted' : 'referred',
            'walkin' => 'no',
            'telemedicine' => ($req->telemedicine) ? $req->telemedicine : 'no', // Add default value for telemedicine
        );
    
        if ($status == 'walkin') {
            $track['date_seen'] = date('Y-m-d H:i:s');
            $track['date_accepted'] = date('Y-m-d H:i:s');
            $track['action_md'] = $user->id;
            $track['walkin'] = 'yes';
        }
    
        $tracking = Tracking::updateOrCreate($match, $track);
    
        $activity = array(
            'code' => $code,
            'patient_id' => $patient_id,
            'date_referred' => date('Y-m-d H:i:s'),
            'date_seen' => ($status == 'walkin') ? date('Y-m-d H:i:s') : '',
            'referred_from' => ($status == 'walkin') ? $req->referring_facility_walkin : $user->facility_id,
            'referred_to' => ($status == 'walkin') ? $user->facility_id : $req->referred_facility,
            'department_id' => $req->referred_department,
            'referring_md' => ($status == 'walkin') ? 0 : $user->id,
            'action_md' => '',
            'remarks' => ($req->reason) ? $req->reason : '',
            'status' => 'referred'
        );
        Activity::create($activity);
    
        if ($status == 'walkin') {
            $activity['date_seen'] = date('Y-m-d H:i:s');
            $activity['status'] = 'accepted';
            $activity['remarks'] = 'Walk-In Patient';
            $activity['action_md'] = $user->id;
            Activity::create($activity);
        }
    
        //start websocket
        $patient = Patients::find($patient_id);
        $redirect_track = asset("doctor/referred?referredCode=") . $code;
        $new_referral = [
            "patient_name" => ucfirst($patient->fname) . ' ' . ucfirst($patient->lname),
            "referring_md" => ucfirst($user->fname) . ' ' . ucfirst($user->lname),
            "referring_name" => Facility::find($user->facility_id)->name,
            "referred_name" => Facility::find($req->referred_facility)->name,
            "referred_to" => (int)$req->referred_facility,
            "referred_department" => Department::find($req->referred_department)->description,
            "referred_from" => $user->facility_id,
            "form_type" => $type,
            "tracking_id" => $tracking->id,
            "referred_date" => date('M d, Y h:i A'),
            "patient_sex" => $patient->sex,
            "age" => ParamCtrl::getAge($patient->dob),
            "patient_code" => $code,
            "status" => "referred",
            "count_reco" => 0,
            "redirect_track" => $redirect_track,
            "position" => 0 //default for first referred
        ];
    
        broadcast(new NewReferral($new_referral));
        //end websocket
    }
    

    public function updateReferral(Request $request, $patient_id)
    {

        $data = $this->prepareData($request, $patient_id);

        PastMedicalHistory::where('patient_id', $patient_id)->update($data['past_medical_history_data']);
        PertinentLaboratory::where('patient_id', $patient_id)->update($data['pertinent_lab']);
        PediatricHistory::where('patient_id', $patient_id)->update($data['pediatric_history']);
        ObstetricAndGynecologicHistory::where('patient_id', $patient_id)->update($data['obstetric_history']);
        PersonalAndSocialHistory::where('patient_id', $patient_id)->update($data['personal_history']);
        ReviewOfSystems::where('patient_id', $patient_id)->update($data['review_of_system']);
        NutritionalStatus::where('patient_id', $patient_id)->update($data['nutritional_status']);
        GlasgoComaScale::where('patient_id', $patient_id)->update($data['glasgocoma_scale']);
        LatestVitalSigns::where('patient_id', $patient_id)->update($data['vital_signs']);


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
            Pregnancy::updateOrCreate(
                [
                    'patient_id' => $patient_id,
                    'pregnancy_order' => $order // assuming order is unique per patient
                ],
                [
                    'pregnancy_year' => $years[$key],
                    'pregnancy_gestation_completed' => $gestations[$key],
                    'pregnancy_outcome' => $outcomes[$key],
                    'pregnancy_place_of_birth' => $placesOfBirth[$key],
                    'pregnancy_sex' => $sexes[$key],
                    'pregnancy_birth_weight' => $birthWeights[$key],
                    'pregnancy_present_status' => $presentStatuses[$key],
                    'pregnancy_complication' => $complications[$key],
                ]
            );
        }


        // return redirect("/revised/referral/info/{$patient_id}");
    }


    // Other functions needed for validations.

    public function dataArray($fields, &$output, $request)
    {
        foreach ($fields as $key => $field) {
            if ($request->has($field['cbox'])) {
                $output[] = $key;
            }
        }
    }

    function mapExplodedDataToArray($explodedData, $stringData)
    {
        $dataArray = [];

        foreach ($stringData as $key) {
            if (in_array($key, $explodedData)) {
                $dataArray[$key] = "Yes";
            } else {
                $dataArray[$key] = '';
            }
        }

        return $dataArray;
    }

    function explodeString($string)
    {

        if (strpos($string, 'Select All,None,') !== false) {
            $exploded_string = explode('Select All,None,', $string);
        } else if (strpos($string, 'Select All,') !== false) {
            $exploded_string = explode('Select All,', $string);
        } else {
            return $string;
        }


        $implodeString = implode(',', $exploded_string);
        $implodeString = ltrim($implodeString, ', ');
        return $implodeString;
    }


    // generate pdf functions.

    public function generatePdf($patient_id)
    {


        $data = $this->fetchDataFromDB($patient_id);
        $comor_string = $data->commordities;
        $comor_explodedData = explode(',', $comor_string);
        $comorbidities = ['Hypertension', 'Diabetes', 'Asthma', 'Cancer', 'Others'];

        $allergies_string = $data->allergies;
        $allergies_explodedData = explode(',', $allergies_string);
        $allergies = ['Food', 'Drugs', 'Others'];

        $heredo_string = $data->heredofamilial_diseases;
        $heredo_exploadedData = explode(',', $heredo_string);
        $heredofamilial = ['Hypertension', 'Diabetes', 'Asthma', 'Cancer', 'Kidney Disease', 'Thyroid Disease', 'Others'];

        $comor_dataArray = $this->mapExplodedDataToArray($comor_explodedData, $comorbidities);
        $allergies_dataArray = $this->mapExplodedDataToArray($allergies_explodedData, $allergies);
        $heredo_dataArray = $this->mapExplodedDataToArray($heredo_exploadedData, $heredofamilial);

        $pdf = new Fpdf();
        $x = ($pdf->w) - 20;

        $pdf->setTopMargin(10);
        $pdf->setTitle($data->patient_id);
        $pdf->addPage();

        $this->titleHeader($pdf, "PAST MEDICAL HISTORY");

        $pdf->MultiCell(0, 7, "Comorbidities:" . self::green($pdf, $this->explodeString($data->commordities), 'Comorbidities'), 1, 'L');

        if (!empty($comor_dataArray['Hypertension'])) {
            $pdf->MultiCell(0, 7, "Hypertension Year:" . self::green($pdf, $this->explodeString($data->commordities_hyper_year), 'Hypertension Year'), 1, 'L');
        }
        if (!empty($comor_dataArray['Diabetes'])) {
            $pdf->MultiCell(0, 7, "Diabetes Year:" . self::green($pdf, $this->explodeString($data->commordities_diabetes_year), 'Diabetes Year'), 1, 'L');
        }
        if (!empty($comor_dataArray['Asthma'])) {
            $pdf->MultiCell(0, 7, "Asthma Year:" . self::green($pdf, $this->explodeString($data->commordities_asthma_year), 'Asthma Year'), 1, 'L');
        }
        if (!empty($comor_dataArray['Cancer'])) {
            $pdf->MultiCell(0, 7, "Cancer:" . self::green($pdf, $this->explodeString($data->commordities_cancer), 'Cancer'), 1, 'L');
        }
        if (!empty($comor_dataArray['Others'])) {
            $pdf->MultiCell(0, 7, "Others:" . self::green($pdf, $this->explodeString($data->commordities_others), 'Others'), 1, 'L');
        }
        $pdf->MultiCell(0, 7, "Allergies:" . self::green($pdf, $this->explodeString($data->allergies), 'Allergies'), 1, 'L');
        if (!empty($allergies_dataArray['Food'])) {
            $pdf->MultiCell(0, 7, "Food(s): (ex. crustaceans, eggs):" . self::green($pdf, $this->explodeString($data->allergy_food_cause), 'Food(s): (ex. crustaceans, eggs)'), 1, 'L');
        }
        if (!empty($allergies_dataArray['Drugs'])) {
            $pdf->MultiCell(0, 7, "Drugs allergy:" . self::green($pdf, $this->explodeString($data->allergy_drugs_cause), 'Drugs allergy'), 1, 'L');
        }
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "HEREDOFAMILIAL DISEASES: ") . "\n" . self::staticGreen($pdf, $this->explodeString($data->heredofamilial_diseases)), 1, 'L');
        if (!empty($heredo_dataArray['Hypertension'])) {
            $pdf->MultiCell(0, 7, "Hypertension side:" . self::green($pdf, $this->explodeString($data->heredo_hyper_side), 'Hypertension side'), 1, 'L');
        }
        if (!empty($heredo_dataArray['Diabetes'])) {
            $pdf->MultiCell(0, 7, "Diabetes side:" . self::green($pdf, $this->explodeString($data->heredo_diab_side), 'Diabetes side'), 1, 'L');
        }
        if (!empty($heredo_dataArray['Asthma'])) {
            $pdf->MultiCell(0, 7, "Asthma side:" . self::green($pdf, $this->explodeString($data->heredo_asthma_side), 'Asthma side'), 1, 'L');
        }
        if (!empty($heredo_dataArray['Cancer'])) {
            $pdf->MultiCell(0, 7, "Cancer side:" . self::green($pdf, $this->explodeString($data->heredo_cancer_side), 'Cancer side'), 1, 'L');
        }
        if (!empty($heredo_dataArray['Kidney Disease'])) {
            $pdf->MultiCell(0, 7, "Kidney Disease side:" . self::green($pdf, $this->explodeString($data->heredo_kidney_side), 'Kidney Disease side'), 1, 'L');
        }
        if (!empty($heredo_dataArray['Thyroid Disease'])) {
            $pdf->MultiCell(0, 7, "Thyroid Disease:" . self::green($pdf, $this->explodeString($data->heredo_thyroid_side), 'Thyroid Disease'), 1, 'L');
        }
        if (!empty($heredo_dataArray['Others'])) {
            $pdf->MultiCell(0, 7, "Others:" . self::green($pdf, $this->explodeString($data->heredo_others), 'Others'), 1, 'L');
        }
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "PREVIOUS HOSPITALIZATION(S) and OPERATION(S): ") . "\n" . self::staticGreen($pdf, $data->previous_hospitalization), 1, 'L');



        $this->titleHeader($pdf, "PEDIATRIC HISTORY");
        $pdf->MultiCell(0, 7, "Prenatal A:" . self::green($pdf, $data->prenatal_a, 'Prenatal A'), 1, 'L');
        $pdf->MultiCell(0, 7, "Prenatal G:" . self::green($pdf, $data->prenatal_g, 'Prenatal G'), 1, 'L');
        $pdf->MultiCell(0, 7, "Prenatal P:" . self::green($pdf, $data->prenatal_p, 'Prenatal P'), 1, 'L');
        if ($data->prenatal_radiowith_or_without == "with") {
            $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Maternal Illness: ") . "\n" . self::staticGreen($pdf, $data->prenatal_with_maternal_illness), 1, 'L');
        } else if ($data->prenatal_radiowith_or_without == "without") {
            $pdf->MultiCell(0, 7, "Maternal Illness:" . self::green($pdf, $data->prenatal_radiowith_or_without . " illness", 'Maternal Illness'), 1, 'L');
        }
        $pdf->ln(1);
        $pdf->SetFillColor(235, 235, 235);
        $pdf->MultiCell(0, 7, "Born At:" . self::green($pdf, $data->natal_born_at, 'Born At'), 1, 'L');
        $pdf->MultiCell(0, 7, "Born Address:" . self::green($pdf, $data->natal_born_address, 'Born Address'), 1, 'L');
        $pdf->MultiCell(0, 7, "By:" . self::green($pdf, $data->natal_by, 'By'), 1, 'L');
        $pdf->MultiCell(0, 7, "Via:" . self::green($pdf, $data->natal_via, 'Via'), 1, 'L');
        $pdf->MultiCell(0, 7, "Indication:" . self::green($pdf, $data->natal_indication, 'Indication'), 1, 'L');
        $pdf->MultiCell(0, 7, "Term:" . self::green($pdf, $data->natal_term, 'Term'), 1, 'L');
        $pdf->MultiCell(0, 7, "With Good Cry:" . self::green($pdf, $data->natal_with_good_cry, 'With Good Cry'), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Other Complications: ") . "\n" . self::staticGreen($pdf, $data->natal_other_complications), 1, 'L');
        $pdf->ln(1);
        $pdf->SetFillColor(235, 235, 235);
        $pdf->SetTextColor(40);
        $pdf->ln(1);
        $pdf->MultiCell(0, 7, "Feeding History", 1, 'L', true);
        if ($data->post_natal_bfeed == "Yes") {
            $pdf->MultiCell(0, 7, "Breast Feed:" . self::green($pdf, $data->post_natal_bfeed, 'Breast Feed'), 1, 'L');
            $pdf->MultiCell(0, 7, "Breast Feed in mos:" . self::green($pdf, $data->post_natal_bfeedx_month, 'Breast Feed in mos'), 1, 'L');
        } else if ($data->post_natal_bfeed == "No") {
            $pdf->MultiCell(0, 7, "Breast Feed:" . self::green($pdf, $data->post_natal_bfeed, 'Breast Feed'), 1, 'L');
        }
        if ($data->post_natal_formula_feed == "Yes") {
            $pdf->MultiCell(0, 7, "Formula Feed:" . self::green($pdf, $data->post_natal_formula_feed, 'Formula Feed'), 1, 'L');
            $pdf->MultiCell(0, 7, "Specific Formula Feed:" . self::green($pdf, $data->post_natal_ffeed_specify, 'Specific Formula Feed'), 1, 'L');
        } else if ($data->post_formula_feed == "No") {
            $pdf->MultiCell(0, 7, "Formula Feed:" . self::green($pdf, $data->post_natal_formula_feed, 'Formula Feed'), 1, 'L');
        }

        $pdf->MultiCell(0, 7, "Started Semi Food in mos:" . self::green($pdf, $data->post_natal_ffeed_specify, 'Started Semi Food in mos'), 1, 'L');
        $pdf->ln(1);
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
        $pdf->MultiCell(0, 7, "Developmental Milestones:" . self::green($pdf, $data->post_natal_development_milestones, 'Developmental Milestones'), 1, 'L');

        $pdf->ln(3);
        // this is for the prenancy table
        $pdf->SetFont('Arial', '', 12);
        // Define the table header based on your migration fields
        $header = array(
            'Preg. Order', 'Year', 'Gestation Completed',
            'Preg. Outcome', 'Birthplace', 'Sex',
            'BW', 'Present Status', 'Complications'
        );

        $data_pregnancy = $this->fetchPregnant($patient_id);

        // Convert data to an array for FPDF
        $dataArray = [];
        foreach ($data_pregnancy as $row) {
            $dataArray[] = (array) $row;
        }

        // Create the styled table in the PDF
        $this->obstetricPage($pdf, $header, $dataArray, $data);




        $pdf->addPage();
        $this->titleHeader($pdf, "PERSONAL AND SOCIAL HISTORY");
        if ($data->smoking == "Yes") {
            $pdf->MultiCell(0, 7, "Smoking:" . self::green($pdf, $data->smoking, 'Smoking'), 1, 'L');
            $pdf->MultiCell(0, 7, "Sticks per Day:" . self::green($pdf, $data->smoking_sticks_per_day, 'Sticks per Day'), 1, 'L');
        } else if ($data->smoking == "No") {
            $pdf->MultiCell(0, 7, "Smoking:" . self::green($pdf, $data->smoking, 'Smoking'), 1, 'L');
        } else if ($data->smoking == "Quit") {
            $pdf->MultiCell(0, 7, "Smoking:" . self::green($pdf, $data->smoking, 'Smoking'), 1, 'L');
            $pdf->MultiCell(0, 7, "Smoking Quit Year:" . self::green($pdf, $data->smoking_quit_year, 'Smoking Quit Year'), 1, 'L');
        }
        $pdf->MultiCell(0, 7, "Smoking Remarks:" . self::green($pdf, $data->smoking_remarks, 'Smoking Remarks'), 1, 'L');

        if ($data->alcohol_drinking == "Yes") {
            $pdf->MultiCell(0, 7, "Drinking:" . self::green($pdf, $data->alcohol_drinking, 'Drinking'), 1, 'L');
            $pdf->MultiCell(0, 7, "Liquor Type:" . self::green($pdf, $data->alcohol_liquor_type, 'Liquor Type'), 1, 'L');
            $pdf->MultiCell(0, 7, "Bottles per day:" . self::green($pdf, $data->alcohol_bottles_per_day, 'Bottles per day'), 1, 'L');
        } else if ($data->alcohol_drinking == "No") {
            $pdf->MultiCell(0, 7, "Drinking:" . self::green($pdf, $data->alcohol_drinking, 'Drinking'), 1, 'L');
        } else if ($data->alcohol_drinking == "Quit") {
            $pdf->MultiCell(0, 7, "Drinking:" . self::green($pdf, $data->alcohol_drinking, 'Drinking'), 1, 'L');
            $pdf->MultiCell(0, 7, "Drinking quit year:" . self::green($pdf, $data->alcohol_drinking_quit_year, 'Drinking quit year'), 1, 'L');
        }

        if ($data->illicit_drugs == "Yes") {
            $pdf->MultiCell(0, 7, "Drugs:" . self::green($pdf, $data->illicit_drugs, 'Drugs'), 1, 'L');
            $pdf->MultiCell(0, 7, "Drugs taken:" . self::green($pdf, $data->illicit_drugs_taken, 'Drugs taken'), 1, 'L');
        } else if ($data->illicit_drugs == "No") {
            $pdf->MultiCell(0, 7, "Drugs:" . self::green($pdf, $data->illicit_drugs, 'Drugs'), 1, 'L');
        } else if ($data->illicit_drugs == "Quit") {
            $pdf->MultiCell(0, 7, "Drugs:" . self::green($pdf, $data->illicit_drugs, 'Drugs'), 1, 'L');
            $pdf->MultiCell(0, 7, "Drugs quit year:" . self::green($pdf, $data->illicit_drugs_quit_year, 'Drugs quit year'), 1, 'L');
        }
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Current Medications: ") . "\n" . self::staticGreen($pdf, $this->explodeString($data->current_medications)), 1, 'L');
        $this->titleHeader($pdf, "PERTINENT LABORATORY AND OTHER ANCILLARY PROCEDURES");
        $pdf->MultiCell(0, 7, "Pertinent Laboratory:" . self::green($pdf, $data->pertinent_laboratory_and_procedures, 'Pertinent Laboratory'), 1, 'L');
        $pdf->MultiCell(0, 7, "Other Procedures:" . self::green($pdf, $data->lab_procedure_other, 'Other Procedures'), 1, 'L');

        $this->titleHeader($pdf, "NUTRITIONAL STATUS");
        $pdf->MultiCell(0, 7, "Diet:" . self::green($pdf, $data->diet, 'Diet'), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Specific Diet: ") . "\n" . self::staticGreen($pdf, $this->explodeString($data->specify_diets)), 1, 'L');
        $this->titleHeader($pdf, "LATEST VITAL SIGNS");
        $pdf->MultiCell(0, 7, "Temperature:" . self::green($pdf, $data->temperature, 'Temperature'), 1, 'L');
        $pdf->MultiCell(0, 7, "Pulse Rate:" . self::green($pdf, $data->pulse_rate, 'Pulse Rate'), 1, 'L');
        $pdf->MultiCell(0, 7, "Respiratory Rate:" . self::green($pdf, $data->respiratory_rate, 'Respiratory Rate'), 1, 'L');
        $pdf->MultiCell(0, 7, "Blood Pressure:" . self::green($pdf, $data->blood_pressure, 'Blood Pressure'), 1, 'L');
        $pdf->MultiCell(0, 7, "Oxgen Saturation:" . self::green($pdf, $data->diet, 'Oxygen Saturation'), 1, 'L');

        $this->titleHeader($pdf, "GLASGOW COMA SCALE");
        $pdf->MultiCell(0, 7, "Pupil Size Chart:" . self::green($pdf, $data->pupil_size_chart, 'Pupil Size Chart'), 1, 'L');
        $pdf->MultiCell(0, 7, "Motor Response:" . self::green($pdf, $data->motor_response, 'Motor Response'), 1, 'L');
        $pdf->MultiCell(0, 7, "Verbal Response:" . self::green($pdf, $data->verbal_response, 'Verbal Response'), 1, 'L');
        $pdf->MultiCell(0, 7, "Eye Response:" . self::green($pdf, $data->eye_response, 'Eye Response'), 1, 'L');
        $pdf->MultiCell(0, 7, "GSC Score:" . self::green($pdf, $data->gsc_score, 'GSC Score'), 1, 'L');

        $pdf->addPage();
        $this->titleHeader($pdf, "REVIEW OF SYSTEMS");
        $pdf->MultiCell(0, 7, "Skin:" . self::green($pdf, $this->explodeString($data->skin), 'Skin'), 1, 'L');
        $pdf->MultiCell(0, 7, "Head:" . self::green($pdf, $this->explodeString($data->head), 'Head'), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Eyes: ") . "\n" . self::staticGreen($pdf, $this->explodeString($data->eyes)), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Ears: ") . "\n" . self::staticGreen($pdf, $this->explodeString($data->ears)), 1, 'L');
        $pdf->MultiCell(0, 7, "Nose/Sinuses:" . self::green($pdf, $this->explodeString($data->nose_or_sinuses), 'Nose/Sinuses'), 1, 'L');
        $pdf->MultiCell(0, 7, "Mouth/Throat:" . self::green($pdf, $this->explodeString($data->mouth_or_throat), 'Mouth/Throat'), 1, 'L');
        $pdf->MultiCell(0, 7, "Neck:" . self::green($pdf, $this->explodeString($data->neck), 'Neck'), 1, 'L');
        $pdf->MultiCell(0, 7, "Breast:" . self::green($pdf, $data->breast, 'Breast'), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Respiratory/Cardiac: ") . "\n" . self::staticGreen($pdf, $this->explodeString($data->respiratory_or_cardiac)), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Gastrointestinal: ") . "\n" . self::staticGreen($pdf, $this->explodeString($data->gastrointestinal)), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Urinary: ") . "\n" . self::staticGreen($pdf, $this->explodeString($data->urinary)), 1, 'L');
        $pdf->MultiCell(0, 7, "Peripheral Vascular:" . self::green($pdf, $this->explodeString($data->peripheral_vascular), 'Peripheral Vascular'), 1, 'L');
        $pdf->MultiCell(0, 7, "Musculoskeletal:" . self::green($pdf, $this->explodeString($data->musculoskeletal), 'Musculoskeletal'), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Neurologic: ") . "\n" . self::staticGreen($pdf, $this->explodeString($data->neurologic)), 1, 'L');
        $pdf->MultiCell(0, 7, "Hematologic:" . self::green($pdf, $this->explodeString($data->hematologic), 'Hematologic'), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Endocrine: ") . "\n" . self::staticGreen($pdf, $this->explodeString($data->endocrine)), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Psychiatric: ") . "\n" . self::staticGreen($pdf, $this->explodeString($data->psychiatric)), 1, 'L');
        $pdf->addPage();




        $pdf->Output();
        exit();
        // dd($data);
    }

    public function titleHeader($pdf, $title)
    {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(210, 210, 210);
        $pdf->SetTextColor(30);
        $pdf->SetDrawColor(200);
        $pdf->SetLineWidth(.3);
        $pdf->ln(5);

        $pdf->MultiCell(0, 7, $title, 1, 'L', true);
        $pdf->SetFillColor(255, 250, 205);
        $pdf->SetTextColor(40);
        $pdf->SetDrawColor(230);
        $pdf->SetLineWidth(.3);
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
        // $pdf->SetTextColor(0, 50, 0);
        $pdf->SetTextColor(102, 56, 0);
        $pdf->SetFont('Arial', '', 10);
        return $val;
    }

    public function staticBlack($pdf, $val)
    {
        $y = $pdf->getY() + 4.5;
        $x = $pdf->getX() + 2;
        $pdf->SetTextColor(0, 0, 0);
        // $pdf->SetTextColor(102, 56, 0);
        $pdf->SetFont('Arial', '', 10);
        return $pdf->Text($x, $y, $val);
    }

    public function green($pdf, $val, $str)
    {
        $y = $pdf->getY() + 4.5;
        $x = $pdf->getX() + 2;
        $ln = $pdf->GetStringWidth($str) + 1;
        // $pdf->SetTextColor(0, 50, 0);
        $pdf->SetTextColor(102, 56, 0);
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

    private function obstetricPage($pdf, $header, $data, $data_)
    {


        $pdf->SetMargins(6.35, 6.35, 6.35);
        $pdf->AddPage('L');

        $contraceptive_other = $data->contraceptive_history;
        $other_explodedData = explode(',', $contraceptive_other);
        $others = ['Other'];

        $other_dataArray = $this->mapExplodedDataToArray($other_explodedData, $others);

        $this->titleHeader($pdf, "OBSTETRIC AND GYNECOLOGIC HISTORY");
        $pdf->MultiCell(0, 7, "Menarche:" . self::green($pdf, $data_->menarche, 'Menarche'), 1, 'L');
        if ($data_->menopause) {
            $pdf->MultiCell(0, 7, "Menopausal Age:" . self::green($pdf, $data_->menopausal_age, 'Menopausal Age'), 1, 'L');
        }
        if ($data_->menstrual_cycle == "irregular") {
            $pdf->MultiCell(0, 7, "Menstrual Cycle:" . self::green($pdf, $data_->menstrual_cycle, 'Menstrual Cycle'), 1, 'L');
            $pdf->MultiCell(0, 7, "Irregular x mos:" . self::green($pdf, $data_->mens_irreg_xmos, 'Irregular x mos'), 1, 'L');
        } else if ($data_->menstrual_cycle == "regular") {
            $pdf->MultiCell(0, 7, "Menstrual Cycle:" . self::green($pdf, $data_->menstrual_cycle, 'Menstrual Cycle'), 1, 'L');
        }
        $pdf->MultiCell(0, 7, "Dysmenorrhea:" . self::green($pdf, $data_->menstrual_cycle_dysmenorrhea, 'Dysmenorrhea'), 1, 'L');
        $pdf->MultiCell(0, 7, "Duration:" . self::green($pdf, $data_->menstrual_cycle_duration, 'Duration'), 1, 'L');
        $pdf->MultiCell(0, 7, "Pads per day:" . self::green($pdf, $data_->menstrual_cycle_padsperday, 'Pads per day'), 1, 'L');
        $pdf->MultiCell(0, 7, "Medication:" . self::green($pdf, $data_->menstrual_cycle_medication, 'Medication'), 1, 'L');
        $pdf->MultiCell(0, 7, "Contraceptive History:" . self::green($pdf, $data_->contraceptive_history, 'contraceptive History'), 1, 'L');
        if (!empty($other_dataArray['Other'])) {
            $pdf->MultiCell(0, 7, "Contraceptive Others:" . self::green($pdf, $data_->contraceptive_others, 'contraceptive Others'), 1, 'L');
        }
        $pdf->MultiCell(0, 7, "Parity G:" . self::green($pdf, $data_->parity_g, 'Parity G'), 1, 'L');
        $pdf->MultiCell(0, 7, "Parity P:" . self::green($pdf, $data_->parity_p, 'Parity P'), 1, 'L');
        $pdf->MultiCell(0, 7, "Parity FT:" . self::green($pdf, $data_->parity_ft, 'Parity FT'), 1, 'L');
        $pdf->MultiCell(0, 7, "Parity PT:" . self::green($pdf, $data_->parity_pt, 'Parity PT'), 1, 'L');
        $pdf->MultiCell(0, 7, "Parity A:" . self::green($pdf, $data_->parity_a, 'Parity A'), 1, 'L');
        $pdf->MultiCell(0, 7, "Parity l:" . self::green($pdf, $data_->parity_l, 'Parity l'), 1, 'L');
        $pdf->MultiCell(0, 7, "Parity LNMP:" . self::green($pdf, $data_->parity_lnmp, 'Parity LNMP'), 1, 'L');
        $pdf->MultiCell(0, 7, "Parity EDC:" . self::green($pdf, $data_->parity_edc, 'Parity EDC'), 1, 'L');
        $pdf->MultiCell(0, 7, "EUTZ:" . self::green($pdf, $data_->aog_eutz, 'EUTZ'), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Prenatal History: ") . "\n" . self::staticGreen($pdf, $this->explodeString($data_->prenatal_history)), 1, 'L');

        $pdf->SetMargins(6.35, 6.35, 6.35);
        $pdf->AddPage('L');

        $pdf->ln(5);

        $pdf->SetFillColor(200, 200, 200);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Arial', 'B', 10);


        $colWidth = array(20, 16, 38, 34, 36, 10, 16, 34, 80);


        foreach ($header as $i => $colHeader) {
            $pdf->Cell($colWidth[$i], 7, $colHeader, 1, 0, 'C', true);
        }
        $pdf->Ln();

        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', 10);

        $fill = false;
        foreach ($data as $row) {
            $pdf->Cell($colWidth[0], 6, $this->wrapText($pdf, $row['pregnancy_order'], $colWidth[0]), 'LR', 0, 'L', $fill);
            $pdf->Cell($colWidth[1], 6, $this->wrapText($pdf, $row['pregnancy_year'], $colWidth[1]), 'LR', 0, 'L', $fill);
            $pdf->Cell($colWidth[2], 6, $this->wrapText($pdf, $row['pregnancy_gestation_completed'], $colWidth[2]), 'LR', 0, 'L', $fill);
            $pdf->Cell($colWidth[3], 6, $this->wrapText($pdf, $row['pregnancy_outcome'], $colWidth[3]), 'LR', 0, 'L', $fill);
            $pdf->Cell($colWidth[4], 6, $this->wrapText($pdf, $row['pregnancy_place_of_birth'], $colWidth[4]), 'LR', 0, 'L', $fill);
            $pdf->Cell($colWidth[5], 6, $this->wrapText($pdf, $row['pregnancy_sex'], $colWidth[5]), 'LR', 0, 'L', $fill);
            $pdf->Cell($colWidth[6], 6, $this->wrapText($pdf, $row['pregnancy_birth_weight'], $colWidth[6]), 'LR', 0, 'R', $fill);
            $pdf->Cell($colWidth[7], 6, $this->wrapText($pdf, $row['pregnancy_present_status'], $colWidth[7]), 'LR', 0, 'L', $fill);
            $pdf->Cell($colWidth[8], 6, $this->wrapText($pdf, $row['pregnancy_complication'], $colWidth[7]), 'LR', 0, 'L', $fill);
            $pdf->Ln();
            $fill = !$fill;
        }

        $pdf->Cell(array_sum($colWidth), 0, '', 'T');
    }

    private function wrapText($pdf, $text, $colWidth)
    {
        $maxLineWidth = $colWidth / 2.5;

        $words = explode(' ', $text);
        $wrappedText = '';
        $line = '';

        foreach ($words as $word) {

            if ($pdf->GetStringWidth($line . ' ' . $word) <= $maxLineWidth) {
                $line .= ($line == '') ? $word : ' ' . $word;
            } else {
                $wrappedText .= $line . "\n";
                $line = $word;
            }
        }
        $wrappedText .= $line;
        return $wrappedText;
    }
}
