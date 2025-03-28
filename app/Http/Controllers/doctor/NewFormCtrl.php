<?php

namespace App\Http\Controllers\doctor;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Events\NewReferral;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ParamCtrl;
use App\Http\Controllers\doctor\ReferralCtrl;
use Illuminate\Support\Facades\Response;
use App\Events\SocketReferralSeen;
use App\Events\SocketReferralUpdate;

use App\Feedback;
use App\Seen;
use App\User;
use App\Baby;
use App\Icd;
use App\Tracking;
use App\Department;
use App\Activity;
use App\Facility;
use App\TelemedAssignDoctor;
use App\ReasonForReferral;
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
use DateTime;
use Anouar\Fpdf\Fpdf;
use App\Barangay;
use App\Muncity;
use App\Province;


class NewFormCtrl extends Controller
{
    // Views
    public function index()
    {
        return view('modal.revised_normal_form');
    }
    
    public function view_choose_versionModal(){
        return view('modal.choose_version');
    }


    // Get data from Database
    public static function fetchDataFromDB($patient_id,$tracking_id)
    {
        $data = DB::table('patients')
            ->join('past_medical_history', 'patients.id', '=', 'past_medical_history.patient_id')
            ->join('pediatric_history', 'patients.id', '=', 'pediatric_history.patient_id')
            ->join('nutritional_status', 'patients.id', '=', 'nutritional_status.patient_id')
            ->join('glasgow_coma_scale', 'patients.id', '=', 'glasgow_coma_scale.patient_id')
            ->join('review_of_system', 'patients.id', '=', 'review_of_system.patient_id')
            ->join('obstetric_and_gynecologic_history', 'patients.id', '=', 'obstetric_and_gynecologic_history.patient_id')
            ->join('latest_vital_signs', 'patients.id', '=', 'latest_vital_signs.patient_id')
            ->join('personal_and_social_history', 'patients.id', '=', 'personal_and_social_history.patient_id')
            ->join('pertinent_laboratory', 'patients.id', '=', 'pertinent_laboratory.patient_id')
            ->join('pregnant_form', 'patients.id', '=', 'pregnant_form.patient_woman_id')
            ->select(
                'patients.*',
                'past_medical_history.*',
                'pediatric_history.*',
                'nutritional_status.*',
                'glasgow_coma_scale.*',
                'review_of_system.*',
                'obstetric_and_gynecologic_history.*',
                'latest_vital_signs.*',
                'personal_and_social_history.*',
                'pertinent_laboratory.*',
                'pregnant_form.*'
            )
            ->where('patients.id', $patient_id)
            ->first();

            if ($data) {
                $icd = Icd::select('icd10.code', 'icd10.description')
                    ->join('icd10', 'icd10.id', '=', 'icd.icd_id')
                    ->where('icd.code', $data->code)
                    ->get();
                $tracking_date = Tracking::select('date_referred',
                DB::raw("DATE_FORMAT(date_transferred, '%M %d, %Y %h:%i %p') as date_transferred"),
                'date_accepted',
                DB::raw("DATE_FORMAT(date_arrived, '%M %d, %Y %h:%i %p') as date_arrived"),
                'date_seen')->where('tracking.code',$data->code)->first();

                $reason = ReasonForReferral::select("reason_referral.reason","reason_referral.id")
                    ->join('pregnant_form', 'pregnant_form.reason_referral', 'reason_referral.id')
                    ->where('pregnant_form.code', $data->code)->first();
                $pregnant_data = self::pregnantFormData($tracking_id);
                // Attach the ICD data to $data
                $data->baby_data = $pregnant_data['baby'];
                $data->tracking_date = $tracking_date;
                $data->pregnant_form = $pregnant_data['pregnant'];
                $data->icd = $icd;
                $data->reason = $reason;
            }
            
        return $data;
    }

    public static function fetchDataNormal($patient_id,$tracking_id){

        $data = DB::table('patients')
        ->join('past_medical_history', 'patients.id', '=', 'past_medical_history.patient_id')
        ->join('nutritional_status', 'patients.id', '=', 'nutritional_status.patient_id')
        ->join('glasgow_coma_scale', 'patients.id', '=', 'glasgow_coma_scale.patient_id')
        ->join('review_of_system', 'patients.id', '=', 'review_of_system.patient_id')
        ->join('obstetric_and_gynecologic_history', 'patients.id', '=', 'obstetric_and_gynecologic_history.patient_id')
        ->join('latest_vital_signs', 'patients.id', '=', 'latest_vital_signs.patient_id')
        ->join('personal_and_social_history', 'patients.id', '=', 'personal_and_social_history.patient_id')
        ->join('pertinent_laboratory', 'patients.id', '=', 'pertinent_laboratory.patient_id')
        ->join('pediatric_history', 'patients.id', '=', 'pediatric_history.patient_id')
        ->join('patient_form', 'patients.id', '=', 'patient_form.patient_id')
        ->select(
            'patients.*',
            'past_medical_history.*',
            'pediatric_history.*',
            'nutritional_status.*',
            'glasgow_coma_scale.*',
            'review_of_system.*',
            'obstetric_and_gynecologic_history.*',
            'latest_vital_signs.*',
            'personal_and_social_history.*',
            'pertinent_laboratory.*',
            'patient_form.*'
        )
        ->where('patients.id', $patient_id)
        ->first();

    if ($data) {
        $icd = Icd::select('icd10.code', 'icd10.description')
            ->join('icd10', 'icd10.id', '=', 'icd.icd_id')
            ->where('icd.code', $data->code)
            ->get();
        $reason = ReasonForReferral::select("reason_referral.reason","reason_referral.id")
            ->join('patient_form', 'patient_form.reason_referral', 'reason_referral.id')
            ->where('patient_form.code', $data->code)->first();
        $form = ReferralCtrl::normalFormData($tracking_id);
       
        // Attach the ICD data to $data
        $data->icd = $icd;
        $data->reason = $reason;
        $data->form= $form;
    }
        
        return $data;
    }
    

    public function fetchPatientsData($id){
        $data = DB::table('patients')
        ->where('id', $id)
        ->first();

        return $data;
    }
    public static function fetchPregnant($patient_id)
    {
        $pregnancy_data = DB::table('pregnancy')
            ->where('patient_id', $patient_id)
            ->get();

        return $pregnancy_data;
    }


    // Forms CRUD 

    public function redirect_referral_info($patient_id)
    {

        $data = $this->fetchDataFromDB($patient_id,null);
        $pregnancy_data = $this->fetchPregnant($patient_id);

        // // Debugging
    

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
            'Edema' => [
                'cbox' => 'rs_peri_edema_cbox',
            ]
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
            'Loss of muscle size' => [
                'cbox' => 'rs_muscle_sizeloss_cbox',
            ],
            'Fracture' => [
                'cbox' => 'rs_muscle_fracture_cbox',
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
            'Muscle Spasm' => [
                'cbox' => 'rs_muscle_spasm_cbox',
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
            'Tremor' => [
                'cbox' => 'rs_neuro_tremor_cbox',
            ],
            'Involuntary movement' => [
                'cbox' => 'rs_neuro_involuntary_cbox',
            ],
            'Unsteady Gait' => [
                'cbox' => 'rs_neuro_unsteadygait_cbox',
            ],
            'Numbness' => [
                'cbox' => 'rs_neuro_numbness_cbox',
            ],
            'Feeling of pins and needles/tingles' => [
                'cbox' => 'rs_neuro_tingles_cbox',
            ],
            'Disorientation' => [
                'cbox' => 'rs_neuro_disorientation_cbox',
            ],
            'Slurring Speech' => [
                'cbox' => 'rs_neuro_slurringspeech_cbox'
            ]
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
            'Heat/cold intolerancee' => [
                'cbox' => 'rs_endo_heatcold_cbox',
            ],
            'Excessive sweating' => [
                'cbox' => 'rs_endo_sweat_cbox',
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
            'Depression' => [
                'cbox' => 'rs_psych_depression_cbox',
            ],
            'Suicide Ideation' => 
            [
                'cbox' => 'rs_psych_suicideideation_cbox',
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
            'Rhythm / Calendar' => [
                'cbox' => 'contraceptive_rhythm_cbox',
            ],
            'Condom' => [
                'cbox' => 'contraceptive_condom_cbox',
            ],
            'Withdrawal' => [
                'cbox' => 'contraceptive_withdrawal_cbox',
            ],  
            'Injections' => [
                'cbox' => 'contraceptive_injections_cbox',
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
            'ULTRA SOUND' => [
                'cbox' => 'lab_ultrasound_cbox',
            ],
            'CT SCAN' => [
                'cbox' => 'lab_ctscan_cbox',
            ],
            'CTG MONITORING' => [
                'cbox' => 'lab_ctgmonitoring_cbox',
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

            $form = PatientForm::updateOrCreate( ['unique_id' => $unique_id],$data);
        
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
            $baby = array(
                'fname' => ($request->baby_fname) ? $request->baby_fname: '',
                'mname' => ($request->baby_mname) ? $request->baby_mname: '',
                'lname' => ($request->baby_lname) ? $request->baby_lname: '',
                'dob' => ($request->baby_dob) ? $request->baby_dob: '',
                'civil_status' => 'Single'
            );
            $baby_id = self::storeBabyAsPatient($baby,$patient_id);

            $baby2 = Baby::updateOrCreate([
                'baby_id' => $baby_id,
                'mother_id' => $patient_id
            ],[
                'weight' => ($request->baby_weight) ? $request->baby_weight:'',
                'gestational_age' => ($request->baby_gestational_age) ? $request->baby_gestational_age: ''
            ]);

            $baby2->birth_date = ($request->baby_dob) ? $request->baby_dob : '';
            $baby2->save();
           

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
                'patient_baby_id' => $baby_id,
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

            $file_paths = "";

            if ($_FILES["file_upload"]["name"]) {
                ApiController::fileUpload($request);
                for ($i = 0; $i < count($_FILES["file_upload"]["name"]); $i++) {
                    $file = $_FILES['file_upload']['name'][$i];
                    if (isset($file) && !empty($file)) {
                        $username = $user->username;
                        $file_paths .= ApiController::fileUploadUrl() . $username . "/" . $file;
                        if ($i + 1 != count($_FILES["file_upload"]["name"])) {
                            $file_paths .= "|";
                        }
                    }
                }
            }
            $form->file_path = $file_paths;
            $form->save();

            foreach ($request->icd_ids as $i) {
                $icd = new Icd();
                $icd->code = $form->code;
                $icd->icd_id = $i;
                $icd->save();
            }
           

            if($request->referred_facility == 790 || $request->referred_facility == 23) {
                $patient = Patients::find($patient_id);
              //  $patient_name = isset($patient->mname[0]) ? ucfirst($patient->fname).' '.strtoupper($patient->mname[0]).'. '.ucfirst($patient->lname) : ucfirst($patient->fname).' '.ucfirst($patient->lname);
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
        if($request->referred_facility == 790 || $request->referred_facility == 23) {
            return $this->referred_patient_data;
        } else {
            Session::put("refer_patient",true);
        }
    }

    public function getViewForm_pregnant($id,$referral_status){
        $track = Tracking::select('code', 'status', 'referred_from as referring_fac_id')->where('id', $id)->first();
        $icd = Icd::select('icd10.code', 'icd10.description', 'icd.icd_id as id')
            ->join('icd10', 'icd10.id', '=', 'icd.icd_id')
            ->where('icd.code',$track->code)->get();

        $file_link = (PregnantForm::select('file_path')->where('code', $track->code)->first())->file_path;

        $path = [];
        $file_name = [];

        if($file_link != null && $file_link != "") {
            $explode = explode("|",$file_link);
            foreach($explode as $link) {
                $path_tmp = ReferralCtrl::securedFile($link);
                if($path_tmp != '') {
                    array_push($path, $path_tmp);
                    array_push($file_name, basename($path_tmp));
                }
            }
        }

        $reason = ReasonForReferral::select("reason_referral.id", "reason_referral.reason")
            ->join('pregnant_form', 'pregnant_form.reason_referral', 'reason_referral.id')
            ->where('pregnant_form.code', $track->code)->first();
        $patient_id = Tracking::select('patient_id')->where('id', $id)->first()->patient_id;
        $facility_id = Tracking::select('referred_to')->where('id', $id)->first()->referred_to;
        $referred_to_address = Facility::select('address')->where('id', $facility_id)->first()->address;
        $civil_status = Patients::select('civil_status')->where('id', $patient_id)->first()->civil_status;
        $past_medical_history = PastMedicalHistory::where('patient_id', $patient_id)->first();
        $personal_and_social_history = PersonalAndSocialHistory::where('patient_id', $patient_id)->first();
        $pertinent_laboratory = PertinentLaboratory::where('patient_id', $patient_id)->first();
        $review_of_system = ReviewOfSystems::where('patient_id', $patient_id)->first();
        $nutritional_status = NutritionalStatus::where('patient_id', $patient_id)->first();
        $latest_vital_signs = LatestVitalSigns::where('patient_id', $patient_id)->first();
        $glasgocoma_scale = GlasgoComaScale::where('patient_id', $patient_id)->first();
        $pediatric_history = PediatricHistory::where('patient_id', $patient_id)->first();
        $obstetric_and_gynecologic_history = ObstetricAndGynecologicHistory::where('patient_id', $patient_id)->first();
        $pregnancy_data = Pregnancy::where('patient_id', $patient_id)->get(); 
        $status = Tracking::select('status')->where('id', $id)->first()->status;
        $obstetetric_parity_date = ObstetricAndGynecologicHistory::select( DB::raw("DATE_FORMAT(parity_lnmp, '%M %d, %Y %h:%i %p') as parity_lnmp"),
        DB::raw("DATE_FORMAT(parity_edc, '%M %d, %Y %h:%i %p') as parity_edc"),
        )->where('patient_id', $patient_id)->first();


        $arr = [
            "form" => self::pregnantFormData($id),
            "id" => $id,
            "reason" => $reason,
            "icd" => $icd,
            "file_path" => $path,
            "file_name" => $file_name,
            "referral_status" => $referral_status,
            "form_type" => 'pregnant',
            "username" => Session::get('auth')->username,
            "cur_status" => $track->status,
            "referring_fac_id" => $track->referring_fac_id,
            "form_type" => "pregnant",
            "patient_id" => $patient_id,
            "civil_status" =>$civil_status,
            "referred_to_address" => $referred_to_address,
            "past_medical_history" => $past_medical_history,
            "personal_and_social_history" => $personal_and_social_history,
            "pertinent_laboratory" => $pertinent_laboratory,
            "review_of_system" => $review_of_system,
            "nutritional_status" => $nutritional_status,
            "latest_vital_signs" => $latest_vital_signs,
            "glasgocoma_scale" => $glasgocoma_scale,
            "pediatric_history"=>$pediatric_history,
            "obstetetric_parity_date"=>$obstetetric_parity_date,
            "obstetric_and_gynecologic_history"=>$obstetric_and_gynecologic_history,
            "pregnancy"=>$pregnancy_data,
            "status"=>$status
        ];
        
        if(Session::get('telemed')) {
            Session::put('telemed',false);
            return $arr;
        } else {
            return view("doctor.referral_body_revised_pregnant",$arr);
        }
    }

    public static function pregnantFormData($id) {
        $form = PregnantForm::select(
            DB::raw("'$id' as tracking_id"),
            'tracking.action_md',
            'tracking.telemedicine', // I add this a piece of code for accessing telemedicine
            'tracking.referring_md',
            'pregnant_form.patient_baby_id',
            'pregnant_form.code',
            'pregnant_form.record_no',
            'pregnant_form.other_reason_referral',
            'pregnant_form.notes_diagnoses',
            'pregnant_form.other_diagnoses',
            DB::raw("DATE_FORMAT(pregnant_form.referred_date,'%M %d, %Y %h:%i %p') as referred_date"),
            DB::raw("DATE_FORMAT(pregnant_form.arrival_date,'%M %d, %Y %h:%i %p') as arrival_date"),
            DB::raw('CONCAT(
                if(users.level="doctor","Dr. ","")
            ,users.fname," ",users.mname," ",users.lname) as md_referring'),
            'users.id as md_referring_id',
            'facility.name as referring_facility',
            'b.description as facility_brgy',
            'm.description as facility_muncity',
            'p.description as facility_province',
            'ff.name as referred_facility',
            'ff.id as referred_facility_id',
            'bb.description as ff_brgy',
            'mm.description as ff_muncity',
            'pp.description as ff_province',
            'pregnant_form.health_worker',
            DB::raw('CONCAT(patients.fname," ",patients.mname," ",patients.lname) as woman_name'),
            DB::raw("TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) AS woman_age"),
            'patients.sex',
            DB::raw("if(
                patients.brgy,
                concat(patients.region,', ',province.description,', ',muncity.description,', ',barangay.description),
                concat(patients.region,', ',patients.province_others,', ',patients.muncity_others,', ',patients.brgy_others)
            ) as patient_address"),
            'pregnant_form.woman_reason',
            'pregnant_form.woman_major_findings',
            'pregnant_form.woman_before_treatment',
            DB::raw("DATE_FORMAT(pregnant_form.woman_before_given_time,'%M %d, %Y %h:%i %p') as woman_before_given_time"),
            'pregnant_form.woman_during_transport',
            DB::raw("DATE_FORMAT(pregnant_form.woman_transport_given_time,'%M %d, %Y %h:%i %p') as woman_transport_given_time"),
            'pregnant_form.woman_information_given',
            'facility.contact as referring_contact',
            'ff.contact as referred_contact',
            'users.contact as referring_md_contact',
            'department.description as department',
            'department.id as department_id',
            'pregnant_form.covid_number',
            'pregnant_form.refer_clinical_status',
            'pregnant_form.refer_sur_category',
            'pregnant_form.dis_clinical_status',
            'pregnant_form.dis_sur_category',
            'patients.id as mother_id',
            'patients.dob',
            DB::raw("DATE_FORMAT(tracking.date_referred,'%m/%e/%Y') as date_referral")
        )
            ->leftJoin('patients','patients.id','=','pregnant_form.patient_woman_id')
            ->leftJoin('tracking','tracking.form_id','=','pregnant_form.id')
            ->leftJoin('facility','facility.id','=','tracking.referred_from')
            ->leftJoin('facility as ff','ff.id','=','tracking.referred_to')
            ->leftJoin('users','users.id','=','pregnant_form.referred_by')
            ->leftJoin('barangay','barangay.id','=','patients.brgy')
            ->leftJoin('muncity','muncity.id','=','patients.muncity')
            ->leftJoin('province','province.id','=','patients.province')
            ->leftJoin('barangay as b','b.id','=','facility.brgy')
            ->leftJoin('muncity as m','m.id','=','facility.muncity')
            ->leftJoin('province as p','p.id','=','facility.province')
            ->leftJoin('barangay as bb','bb.id','=','ff.brgy')
            ->leftJoin('muncity as mm','mm.id','=','ff.muncity')
            ->leftJoin('province as pp','pp.id','=','ff.province')
            ->leftJoin('department','department.id','=','pregnant_form.department_id')
            ->where('tracking.id',$id)
            ->first();

        $baby = array();
        if(isset($form->patient_baby_id) && $form->patient_baby_id > 0)
        {
            $baby = PregnantForm::select(
                DB::raw('CONCAT(baby.fname," ",baby.mname," ",baby.lname) as baby_name'),
                'pregnant_form.patient_baby_id as baby_id',
                'baby.fname as baby_fname',
                'baby.mname as baby_mname',
                'baby.lname as baby_lname',
                DB::raw("DATE_FORMAT(bb.birth_date,'%M %d, %Y %h:%i %p') as baby_dob"),
                'bb.weight',
                'bb.gestational_age',
                'pregnant_form.baby_reason',
                'pregnant_form.baby_major_findings',
                DB::raw("DATE_FORMAT(pregnant_form.baby_last_feed,'%M %d, %Y %h:%i %p') as baby_last_feed"),
                'pregnant_form.baby_before_treatment',
                DB::raw("DATE_FORMAT(pregnant_form.baby_before_given_time,'%M %d, %Y %h:%i %p') as baby_before_given_time"),
                'pregnant_form.baby_during_transport',
                DB::raw("DATE_FORMAT(pregnant_form.baby_transport_given_time,'%M %d, %Y %h:%i %p') as baby_transport_given_time"),
                'pregnant_form.baby_information_given'
            )
                ->join('baby as bb','bb.baby_id','=','pregnant_form.patient_baby_id')
                ->join('patients as baby','baby.id','=','pregnant_form.patient_baby_id')
                ->join('tracking','tracking.form_id','=','pregnant_form.id')
                ->where('tracking.id',$id)
                ->first();

            if($baby['baby_dob'] === null) {
                $dob_ = Patients::select(DB::raw("DATE_FORMAT(patients.dob,'%M %d, %Y %h:%i %p') as baby_dob"))
                    ->join('pregnant_form as pregnant','pregnant.patient_baby_id','=','patients.id')
                    ->join('tracking','tracking.code','=','pregnant.code')
                    ->where('tracking.id',$id)
                    ->first();
                $baby['baby_dob'] = $dob_->baby_dob;
            }
        }

        Session::put('date_referral', $form['date_referral']);
        $form['woman_age'] = ParamCtrl::getAge($form['dob']);

        return array(
            'pregnant' => $form,
            'baby' => $baby
        );
    }

    public function getViewForm_normal($id,$referral_status,$form_type){
        $track = Tracking::select('code', 'status', 'referred_from as referring_fac_id')->where('id', $id)->first();
        $icd = Icd::select('icd10.code', 'icd10.description', 'icd.icd_id as id')
            ->join('icd10', 'icd10.id', '=', 'icd.icd_id')
            ->where('icd.code',$track->code)->get();
      
        $file_link = (PatientForm::select('file_path')->where('code', $track->code)->first())->file_path;

        //        $path = self::securedFile($file_link);
        //        $file_name = basename($path);

        $path = [];
        $file_name = [];

        if($file_link != null && $file_link != "") {
            $explode = explode("|",$file_link);
            foreach($explode as $link) {
                $path_tmp = ReferralCtrl::securedFile($link);
                if($path_tmp != '') {
                    array_push($path, $path_tmp);
                    array_push($file_name, basename($path_tmp));
                }
            }
        }

        $reason = ReasonForReferral::select("reason_referral.reason","reason_referral.id")
            ->join('patient_form', 'patient_form.reason_referral', 'reason_referral.id')
            ->where('patient_form.code', $track->code)->first();

        $form = ReferralCtrl::normalFormData($id);
        $patient_id = Tracking::select('patient_id')->where('id', $id)->first()->patient_id;
        $type =  Tracking::select('type')->where('id', $id)->first()->type;
        $status = Tracking::select('status')->where('id', $id)->first()->status;
        $past_medical_history = PastMedicalHistory::where('patient_id', $patient_id)->first();
        $personal_and_social_history = PersonalAndSocialHistory::where('patient_id', $patient_id)->first();
        $pertinent_laboratory = PertinentLaboratory::where('patient_id', $patient_id)->first();
        $review_of_system = ReviewOfSystems::where('patient_id', $patient_id)->first();
        $nutritional_status = NutritionalStatus::where('patient_id', $patient_id)->first();
        $latest_vital_signs = LatestVitalSigns::where('patient_id', $patient_id)->first();
        $glasgocoma_scale = GlasgoComaScale::where('patient_id', $patient_id)->first();
        $obstetric_and_gynecologic_history = ObstetricAndGynecologicHistory::where('patient_id', $patient_id)->first();
        $pediatric_history = PediatricHistory::where('patient_id', $patient_id)->first();
        $pregnancy_data = Pregnancy::where('patient_id', $patient_id)->get(); 
        $obstetetric_parity_date = ObstetricAndGynecologicHistory::select( DB::raw("DATE_FORMAT(parity_lnmp, '%M %d, %Y %h:%i %p') as parity_lnmp"),
        DB::raw("DATE_FORMAT(parity_edc, '%M %d, %Y %h:%i %p') as parity_edc"),
        )->where('patient_id', $patient_id)->first();

        $arr = [
            "form" => $form['form'],
            "id" => $id,
            "patient_age" => $form['age'],
            "age_type" => $form['ageType'],
            "reason" => $reason,
            "icd" => $icd,
            "file_path" => $path,
            "file_name" => $file_name,
            "referral_status" => $referral_status,
            "cur_status" => $track->status,
            "referring_fac_id" => $track->referring_fac_id,
            "form_type" => $form_type,
            "patient_id" => $patient_id,
            "past_medical_history" => $past_medical_history,
            "personal_and_social_history" => $personal_and_social_history,
            "obstetric_and_gynecologic_history"=>$obstetric_and_gynecologic_history,
            "pregnancy"=>$pregnancy_data,
            "pertinent_laboratory" => $pertinent_laboratory,
            "review_of_system" => $review_of_system,
            "nutritional_status" => $nutritional_status,
            "latest_vital_signs" => $latest_vital_signs,
            "glasgocoma_scale" => $glasgocoma_scale,
            "pediatric_history"=>$pediatric_history,
            "obstetetric_parity_date"=>$obstetetric_parity_date,
            "type"=>$type,
            "status" => $status
        ];
        if(Session::get('telemed')) {
            Session::put('telemed',false);
            return $arr;
        } else {
                return view("doctor.referral_body_revised_normal",$arr);
        }
    }

    public static function editInfo($id,$form_type,$referral_status)
    {
        $track = Tracking::select('code')->where('id', $id)->first();
        $icd = Icd::select('icd10.code', 'icd10.description', 'icd.icd_id as id')
            ->join('icd10', 'icd10.id', '=', 'icd.icd_id')
            ->where('icd.code',$track->code)->get();

        if($form_type == 'normal') {
            $file_link = (PatientForm::select('file_path')->where('code', $track->code)->first())->file_path;
        //    $path = self::securedFile($file_link);
        //    $file_name = basename($path);

            $path = array();
            $file_name = array();

            if($file_link != null) {
                $explode = explode("|",$file_link);
                foreach($explode as $link) {
                    $path_tmp = ReferralCtrl::securedFile($link);
                    if($path_tmp != '') {
                        array_push($path, $path_tmp);
                        array_push($file_name, basename($path_tmp));
                    }
                }
            }

            $reason = ReasonForReferral::select('patient_form.reason_referral as id', 'reason_referral.reason as reason')
                ->join('patient_form', 'patient_form.reason_referral', 'reason_referral.id')
                ->where('patient_form.code', $track->code)->first();
            $form = ReferralCtrl::normalFormData($id);

            $patient_id = Tracking::select('patient_id')->where('id', $id)->first()->patient_id;
            $type =  Tracking::select('type')->where('id', $id)->first()->type;
            $status = Tracking::select('status')->where('id', $id)->first()->status;
            $past_medical_history = PastMedicalHistory::where('patient_id', $patient_id)->first();
            $personal_and_social_history = PersonalAndSocialHistory::where('patient_id', $patient_id)->first();
            $pertinent_laboratory = PertinentLaboratory::where('patient_id', $patient_id)->first();
            $review_of_system = ReviewOfSystems::where('patient_id', $patient_id)->first();
            $nutritional_status = NutritionalStatus::where('patient_id', $patient_id)->first();
            $latest_vital_signs = LatestVitalSigns::where('patient_id', $patient_id)->first();
            $glasgocoma_scale = GlasgoComaScale::where('patient_id', $patient_id)->first();
            $obstetric_and_gynecologic_history = ObstetricAndGynecologicHistory::where('patient_id', $patient_id)->first();
            $pregnancy_data = Pregnancy::where('patient_id', $patient_id)->get();
            $pediatric_history = PediatricHistory::where('patient_id', $patient_id)->first();
    
           
            return view("modal.revised_normal_form_info", [
                "form" => $form['form'],
                "id" => $id,
                "patient_age" => $form['age'],
                "age_type" => $form['ageType'],
                "reason" => $reason,
                "icd" => $icd,
                "file_path" => $path,
                "file_name" => $file_name,
                "form_type" => $form_type,
                "referral_status" => $referral_status,
                "username" => Session::get('auth')->username,

                "patient_id" => $patient_id,
                "past_medical_history" => $past_medical_history,
                "personal_and_social_history" => $personal_and_social_history,
                "pertinent_laboratory" => $pertinent_laboratory,
                "review_of_system" => $review_of_system,
                "nutritional_status" => $nutritional_status,
                "latest_vital_signs" => $latest_vital_signs,
                "glasgocoma_scale" => $glasgocoma_scale,
                "pediatric_history"=>$pediatric_history,
                "type"=>$type,
                "status" => $status,
                "obstetric_and_gynecologic_history"=>$obstetric_and_gynecologic_history,
                "pregnancy"=>$pregnancy_data,
            ]);
        } else if($form_type == 'pregnant') {
            $file_link = (PregnantForm::select('file_path')->where('code', $track->code)->first())->file_path;

            $path = array();
            $file_name = array();

            if($file_link != null) {
                $explode = explode("|",$file_link);
                foreach($explode as $link) {
                    $path_tmp = ReferralCtrl::securedFile($link);
                    if($path_tmp != '') {
                        array_push($path, $path_tmp);
                        array_push($file_name, basename($path_tmp));
                    }
                }
            }

            $reason = ReasonForReferral::select("reason_referral.id", "reason_referral.reason")
            ->join('pregnant_form', 'pregnant_form.reason_referral', 'reason_referral.id')
            ->where('pregnant_form.code', $track->code)->first();
            $patient_id = Tracking::select('patient_id')->where('id', $id)->first()->patient_id;
            $facility_id = Tracking::select('referred_to')->where('id', $id)->first()->referred_to;
            $referred_to_address = Facility::select('address')->where('id', $facility_id)->first()->address;
            $civil_status = Patients::select('civil_status')->where('id', $patient_id)->first()->civil_status;
            $past_medical_history = PastMedicalHistory::where('patient_id', $patient_id)->first();
            $personal_and_social_history = PersonalAndSocialHistory::where('patient_id', $patient_id)->first();
            $pertinent_laboratory = PertinentLaboratory::where('patient_id', $patient_id)->first();
            $review_of_system = ReviewOfSystems::where('patient_id', $patient_id)->first();
            $nutritional_status = NutritionalStatus::where('patient_id', $patient_id)->first();
            $latest_vital_signs = LatestVitalSigns::where('patient_id', $patient_id)->first();
            $glasgocoma_scale = GlasgoComaScale::where('patient_id', $patient_id)->first();
            $pediatric_history = PediatricHistory::where('patient_id', $patient_id)->first();
            $obstetric_and_gynecologic_history = ObstetricAndGynecologicHistory::where('patient_id', $patient_id)->first();
            $pregnancy_data = Pregnancy::where('patient_id', $patient_id)->get(); 
            $status = Tracking::select('status')->where('id', $id)->first()->status;

            $reason = ReasonForReferral::select('pregnant_form.reason_referral as id', 'reason_referral.reason as reason')
                ->join('pregnant_form', 'pregnant_form.reason_referral', 'reason_referral.id')
                ->where('pregnant_form.code', $track->code)->first();

            
            
            return view("modal.revised_pregnant_info", [
                "form" => self::pregnantFormData($id),
                "id" => $id,
                "reason" => $reason,
                "icd" => $icd,
                "file_path" => $path,
                "file_name" => $file_name,
                "referral_status" => $referral_status,
                "form_type" => $form_type,
                "username" => Session::get('auth')->username,

                "patient_id" => $patient_id,
                "civil_status" =>$civil_status,
                "referred_to_address" => $referred_to_address,
                "past_medical_history" => $past_medical_history,
                "personal_and_social_history" => $personal_and_social_history,
                "pertinent_laboratory" => $pertinent_laboratory,
                "review_of_system" => $review_of_system,
                "nutritional_status" => $nutritional_status,
                "latest_vital_signs" => $latest_vital_signs,
                "glasgocoma_scale" => $glasgocoma_scale,
                "pediatric_history"=>$pediatric_history,
                "obstetric_and_gynecologic_history"=>$obstetric_and_gynecologic_history,
                "pregnancy"=>$pregnancy_data,
                "status"=>$status,
            ]);
        }
    }

    public function getFormType($form_id){
        try {
            // Check if form_id exists and is correct
            $form_type = Tracking::select('form_type')->where('id', $form_id)->first();
    
            if ($form_type) {
                return Response::json($form_type);
            } else {
                return Response::json(['error' => 'Form not found'], 404);
            }
        } catch (Exception $e) {
            // Log the exception for debugging
            Log::error($e->getMessage());
            return Response::json(['error' => 'An error occurred'], 500);
        }
    }

    public function storeBabyAsPatient($data,$mother_id)
    {
        if($data['fname']){
            if($data['mname'] == "")
                $data['mname'] = " ";

            $mother = Patients::find($mother_id);
            $data['brgy'] = $mother->brgy;
            $data['muncity'] = $mother->muncity;
            $data['province'] = $mother->province;
            $dob = date('ymd',strtotime($data['dob']));

            $tmp = array(
                $data['fname'],
                $data['mname'],
                $data['lname'],
                $data['brgy'],
                $dob
            );
            $unique = implode($tmp);
            $match = array(
                'unique_id' => $unique
            );

            $patient = Patients::updateOrCreate($match,$data);
            return $patient->id;
        }else{
            return '0';
        }
    }

    public function newFormPregnant($request){
        $patient_id = $request->patient_id;

        $data = $this->prepareData($request, $patient_id);
        ObstetricAndGynecologicHistory::create($data['obstetric_history']);
        // PediatricHistory::create($data['pediatric_history']);

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
            // Check if a record with the same details already exists
            $existingRecord = Pregnancy::where('patient_id', $patient_id)
                ->where('pregnancy_order', $order)
                ->where('pregnancy_year', $years[$key])
                ->where('pregnancy_gestation_completed', $gestations[$key])
                ->where('pregnancy_outcome', $outcomes[$key])
                ->where('pregnancy_place_of_birth', $placesOfBirth[$key])
                ->where('pregnancy_sex', $sexes[$key])
                ->where('pregnancy_birth_weight', $birthWeights[$key])
                ->where('pregnancy_present_status', $presentStatuses[$key])
                ->where('pregnancy_complication', $complications[$key])
                ->first();
        
            // Create a new record only if it doesn't already exist
            if (!$existingRecord) {
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
        
    }

    public function newFormSave($request){
        // new save form
        $patient_id = $request->patient_id;

        $data = $this->prepareData($request, $patient_id);

        PastMedicalHistory::create($data['past_medical_history_data']);
        PediatricHistory::create($data['pediatric_history']);
        PertinentLaboratory::create($data['pertinent_lab']);
        PersonalAndSocialHistory::create($data['personal_history']);
        ReviewOfSystems::create($data['review_of_system']);
        NutritionalStatus::create($data['nutritional_status']);
        GlasgoComaScale::create($data['glasgocoma_scale']);
        LatestVitalSigns::create($data['vital_signs']);
        ObstetricAndGynecologicHistory::create($data['obstetric_history']);

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
            // Check if a record with the same details already exists
            $existingRecord = Pregnancy::where('patient_id', $patient_id)
                ->where('pregnancy_order', $order)
                ->where('pregnancy_year', $years[$key])
                ->where('pregnancy_gestation_completed', $gestations[$key])
                ->where('pregnancy_outcome', $outcomes[$key])
                ->where('pregnancy_place_of_birth', $placesOfBirth[$key])
                ->where('pregnancy_sex', $sexes[$key])
                ->where('pregnancy_birth_weight', $birthWeights[$key])
                ->where('pregnancy_present_status', $presentStatuses[$key])
                ->where('pregnancy_complication', $complications[$key])
                ->first();
        
            // Create a new record only if it doesn't already exist
            if (!$existingRecord) {
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
            'form_type' => 'version2',
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
        return redirect()->back();
        //end websocket
    }
    

    public function updateReferral(Request $request, $patient_id,$id,$type,$status)
    { 
        $data = $this->prepareData($request, $patient_id);
  
        if ($type === "normal"){
         
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

            self::editForm($request,$status,$type,$patient_id);
            return redirect()->back();
        }else if ($type === "pregnant"){
           

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

        self::editForm($request,$status,$type,$patient_id);
        return redirect()->back();
        }
    }

    public static function editForm($req,$status,$type,$patient_id)
    {   
        // dd($req->icd_ids); 
        $user = Session::get('auth');
        $id = $req->id;
        $old_facility = (int) $req->old_facility;

        $tracking = Tracking::where('id', $id)->first();

        if($tracking->status == 'rejected') {
            Session::put('ignore_edit',true);
            return redirect()->back();
        }

        $track = $tracking->code;

        $updated = '';
        $date = date('Y-m-d H:i:s');

        $form_type = $req->form_type;
        $dob = date('Y-m-d h:i:s', strtotime($req->baby_dob));
        $data = '';

       

        $request_arr = [
            "id" => $id,
            "referral_status" => $status,
            "form_type" => $type,
            "baby_id" => $req->baby_id,
            "mother_id" => $patient_id,
            "username" => Session::get('auth')->username,
            "old_facility" => $req->old_facility,
            "referred_to" => $req->referred_to,
            "department_id" => $req->department_id,
            "covid_number" => $req->covid_number,
            "refer_clinical_status" => $req->clinical_status,
            "refer_sur_category" => $req->sur_category,
            "woman_reason" => $req->woman_reason,
            "woman_major_findings" => $req->woman_major_findings,
            "woman_before_treatment" => $req->woman_before_treatment,
            "woman_before_given_time" => $req->woman_before_given_time,
            "woman_during_transport" => $req->woman_during_transport,
            "woman_transport_given_time" => $req->woman_transport_given_time,
            "woman_information_given" => $req->woman_information_given,
            "icd_cleared" => $req->icd_cleared ?? '',
            "icd_ids" => $req->icd_ids,
            "notes_diag_cleared" => $req->notes_diag_cleared ?? '',
            "notes_diagnoses" => $req->notes_diagnoses,
            "other_diag_cleared" => $req->other_diag_cleared ?? '',
            "other_diagnoses" => $req->other_diagnosis ?? '',
            "reason_referral" => $req->reason_referral,
            "baby_fname" => $req->baby_fname,
            "baby_mname" => $req->baby_mname,
            "baby_lname" => $req->baby_lname,
            "baby_dob" => $req->baby_dob,
            "baby_weight" => $req->baby_weight,
            "baby_gestational_age" => $req->baby_gestational_age,
            "baby_reason" => $req->baby_reason,
            "baby_major_findings" => $req->baby_major_findings,
            "baby_last_feed" => $req->baby_last_feed,
            "baby_before_treatment" => $req->baby_before_treatment,
            "baby_before_given_time" => $req->baby_before_given_time,
            "baby_during_transport" => $req->baby_during_transport,
            "baby_transport_given_time" => $req->baby_transport_given_time,
            "baby_information_given" => $req->baby_information_given,
            "file_cleared" => null,
        ];

        $request_arr2 = [
            "id" => $id,
            "referral_status" => $status,
            "form_type" => $type,
            "username" => Session::get('auth')->username,
            "old_facility" => $req->old_facility,
            "referred_to" =>  $req->referred_to,
            "department_id" => $req->department_id,
            "covid_number" => $req->covid_number,
            "refer_clinical_status" => $req->clinical_status,
            "refer_sur_category" => $req->sur_category,
            "case_summary" => $req->case_summary,
            "reco_summary" => $req->reco_summary,
            "icd_cleared" =>  $req->icd_cleared ?? '',
            "icd_ids" => $req->icd_ids,
            "notes_diag_cleared" => $req->notes_dig_cleared ?? '',
            "diagnosis" => $req->diagnosis,
            "other_diag_cleared" => $req->other_diag_cleared ?? '',
            "other_diagnoses" => $req->other_diagnosis ?? '',
            "reason_referral" => $req->reason_referral,
            "other_reason_referral" => $req->reason_referral,
            "file_cleared" => null,
            "referred_md" => $req->referred_md,
        ];

        /* FACILITY AND DEPARTMENT */
        if($old_facility != $req->referred_facility)
            $updated .= "Referred facility, Department";

        if($form_type === 'normal') {
            $data_update = $request_arr2;
            $data = PatientForm::where('code', $track)->first();

            /* DIAGNOSIS NOTES */
            if(($data->diagnosis !== $req->diagnosis) || $req->notes_diag_cleared)
                $updated .= ", Diagnosis Notes";

            if($req->notes_diag_cleared)
                $data->update(['diagnosis' => NULL]);
            unset($data_update['notes_diag_cleared']);

            /* TIME OF REFERRAL WILL CHANGE IF FACILITY IS UPDATED/CHANGED */
            if($old_facility != $req->referred_facility)
                $data->update(['time_referred' => date('Y-m-d H:i:s')]);
        }
        else if($form_type === 'pregnant') {
            $data_update = $request_arr;
            $data = PregnantForm::where('code', $track)->first();
            $baby_id = $req->baby_id;
            $match = Patients::where('id', $baby_id)->first();

            if($old_facility != $req->referred_facility)
                $data->update(['referred_date' => date('Y-m-d H:i:s')]);

            $baby = array(
                "fname" => $req->baby_fname,
                "mname" => $req->baby_mname,
                "lname" => $req->baby_lname,
                "dob" => $dob
            );
            if(isset($match)) {
                $match->update($baby);
            } else {
                $baby_id = PatientCtrl::storeBabyAsPatient($baby,$req->mother_id);
            }

            $match = Baby::where("baby_id", $baby_id)->first();

            if(isset($match)) {
                if((isset($match->weight) && $match->weight != $req->baby_weight) || (isset($match->gestational_age) && $match->gestational_age != $req->baby_gestational_age) || (isset($match->birth_date) && $match->birth_date != $dob))
                    $updated .= ", Baby's Information";
                $match->weight = ($req->baby_weight) ? $req->baby_weight : '';
                $match->gestational_age = ($req->baby_gestational_age) ? $req->baby_gestational_age : '';
                $match->birth_date = $dob;
                $match->save();
            } else {
                $updated .= ", Baby's Information";
                $b = new Baby();
                $b->baby_id = $baby_id;
                $b->mother_id = $req->mother_id;
                $b->weight = ($req->baby_weight) ? $req->baby_weight : '';
                $b->gestational_age = ($req->baby_gestational_age) ? $req->baby_gestational_age : '';
                $b->birth_date = $dob;
                $b->save();
            }

            unset($data_update['baby_fname']);
            unset($data_update['baby_mname']);
            unset($data_update['baby_lname']);
            unset($data_update['baby_dob']);
            unset($data_update['baby_weight']);
            unset($data_update['baby_gestational_age']);
            unset($data_update['baby_id']);
            unset($data_update['mother_id']);

            if($req->notes_diag_cleared == "true")
                $data->update(['notes_diagnoses' => NULL]);

            unset($data_update['notes_diag_cleared']);

            $woman_before_given_time = date('Y-m-d h:i:s', strtotime($req->woman_before_given_time));
            $woman_transport_given_time = date('Y-m-d h:i:s', strtotime($req->woman_transport_given_time));
            $baby_last_feed = date('Y-m-d h:i:s', strtotime($req->baby_last_feed));
            $baby_before_given_time = date('Y-m-d h:i:s', strtotime($req->baby_before_given_time));
            $baby_transport_given_time = date('Y-m-d h:i:s', strtotime($req->baby_transport_given_time));
            $baby_reason = ($req->baby_reason == null) ? 'None' : $req->baby_reason;
            $baby_information_given = ($req->baby_information_given == null) ? '' : $req->baby_information_given;

            if(isset($data->woman_reason) && $data->woman_reason != $req->woman_reason)
                $updated .= ", Mother's main reason for referral";
            if(isset($data->woman_major_findings) && $data->woman_major_findings != $req->woman_major_findings)
                $updated .= ", Mother's major findings";
            if(isset($data->woman_before_treatment) && $data->woman_before_treatment != $req->woman_before_treatment)
                $updated .= ", Mother's treatment given before referral";
            if($data->woman_before_given_time != '0000-00-00 00:00:00' && $data->woman_before_given_time != $woman_before_given_time)
                $updated .= ", Mother's treament time before referral";
            if(isset($data->woman_during_transport) && $data->woman_during_transport != $req->woman_during_transport)
                $updated .= ", Mother's treatment given during transport";
            if($data->woman_transport_given_time != '0000-00-00 00:00:00' && $data->woman_transport_given_time != $woman_transport_given_time)
                $updated .= ", Mother's treatment time during transport";
            if(($data->notes_diagnoses != $req->notes_diagnoses) || $req->notes_diag_cleared)
                $updated .= ", Diagnosis Notes";
            if(isset($data->baby_reason) && $data->baby_reason != $baby_reason)
                $updated .= ", Baby's main reason for referral";
            if(isset($data->baby_major_findings) && $data->baby_major_findings != $req->baby_major_findings)
                $updated .= ", Baby's major findings";
            if($data->baby_last_feed != '0000-00-00 00:00:00' && $data->baby_last_feed != $baby_last_feed)
                $updated .= ", Baby's last feeding time";
            if(isset($data->baby_before_treatment) && $data->baby_before_treatment != $req->baby_before_treatment)
                $updated .= ", Baby's treatment given before referral";
            if($data->baby_before_given_time != '0000-00-00 00:00:00' && $data->baby_before_given_time != $baby_before_given_time)
                $updated .= ", Baby's treatment time before referral";
            if(isset($data->baby_during_transport) && $data->baby_during_transport != $req->baby_during_transport)
                $updated .= ", Baby's treatment given during transport";
            if($data->baby_transport_given_time != '0000-00-00 00:00:00' && $data->baby_transport_given_time != $baby_transport_given_time)
                $updated .= ", Baby's treatment time during transport";
            if((isset($data->woman_information_given) && $data->woman_information_given != $req->woman_information_given) || (isset($data->baby_information_given) && $data->baby_information_given !== $baby_information_given))
                $updated .= ", Information given about the reason of referral";

            $data_update['patient_baby_id'] = $baby_id;
            $data_update['woman_before_given_time'] = ($req->woman_before_given_time) ? $woman_before_given_time : '';
            $data_update['woman_transport_given_time'] = ($req->woman_transport_given_time) ? $woman_transport_given_time : '';
            $data_update['baby_last_feed'] = ($req->baby_last_feed) ? $baby_last_feed : '';
            $data_update['baby_before_given_time'] = ($req->baby_before_given_time) ? $baby_before_given_time : '';
            $data_update['baby_transport_given_time'] = ($req->baby_transport_given_time) ? $baby_transport_given_time : '';
        }

        /* COVID NUMBER, CLINICAL STATUS AND SURVEILLANCE CATEGORY */
        if($data->covid_number !== $req->covid_number)
            $updated .= ", Covid Number";

        if($data->refer_clinical_status !== $req->refer_clinical_status)
            $updated .= ", Covid Clinical Status";

        if($data->refer_sur_category !== $req->refer_sur_category)
            $updated .= ", Covid Surveillance Category";

        /* CASE SUMMARY AND SUMMARY OF RECO */
        if($data->case_summary !== $req->case_summary)
            $updated .= ", Case summary";

        if($data->reco_summary !== $req->reco_summary)
            $updated .= ", Summary of ReCo";


        /* DIAGNOSIS THAT IS NOT AN ICD CODE */
        if(($data->other_diagnoses !== $req->other_diagnosis) || $req->other_diag_cleared)
            $updated .= ", Diagnosis";

        if($req->other_diag_cleared == "true") {
            $data->update(['other_diagnoses' => NULL]);
        }
        unset($data_update['other_diag_cleared']);


        /* ICD DIAGNOSIS */
        if($req->icd_cleared === 'true')
            Icd::where('code', $track)->delete();
        unset($data_update['icd_cleared']);

        $updated_icd = false;
        foreach($req->icd_ids as $i) {
            $value = Icd::where('code', $track)->where('icd_id', $i)->first();
            if(!isset($value)) {
                $icd = new Icd();
                $icd->code = $track;
                $icd->icd_id = $i;
                $icd->save();
                $updated_icd = true;
            }
        }
        unset($data_update['icd_ids']);
        if($updated_icd) {
            $updated .= ", ICD-10 Diagnosis";
        }

        if ($req->file_cleared == "true") {
            $data->update([
                'file_path' => ""
            ]);
        }
        
        // Get existing file paths
        $file_paths = $data->file_path;
        $old_file = $file_paths;
        
        // Handle file uploads
        if (!empty($_FILES["file_upload"]["name"][0])) { // Ensure at least one file is uploaded
            ApiController::fileUpload($req);
        
            // Initialize an array to store new file paths
            $new_file_paths = [];
            
            for ($i = 0; $i < count($_FILES['file_upload']['name']); $i++) {
                $file = $_FILES['file_upload']['name'][$i];
                if (!empty($file)) {
                    $username = $user->username ?? 'default_user'; // Ensure username is defined
                    $new_file_paths[] = ApiController::fileUploadUrl() . $username . "/" . $file;
                }
            }
        
            // Append new file paths to existing paths (if any)
            if (!empty($file_paths)) {
                $file_paths .= "|"; // Add a delimiter before appending new paths
            }
            $file_paths .= implode("|", $new_file_paths);
        }
        
        // Update the file_path in the database
        $data->update([
            'file_path' => $file_paths
        ]);
        
        // Track if there were changes to file attachments
        if ($req->file_cleared == "true" || $old_file !== $file_paths) {
            $updated .= ", File Attachment";
        }
        
        // Unset unnecessary fields
        unset($data_update['file_cleared']);
        unset($data_update['file_upload']);
        unset($data_update['username']);
        unset($data_update['id']);
        unset($data_update['referral_status']);
        unset($data_update['form_type']);
        


        /* REASON FOR REFERRAL */
        $reason_referral = (int) $req->reason_referral;
        if(($data->reason_referral !== $reason_referral) || ($data->other_reason_referral !== $req->other_reason_referral))
            $updated .= ", Reason for Referral";
        $data_update['other_reason_referral'] = isset($req->other_reason_referral) ? $req->other_reason_referral : null;

        if($updated[0] === ',')
            $updated = substr($updated, 2, strlen($updated));

        $updated_remarks = "";
        if($updated !== "") {
            $updated_remarks .= "Updated fields: " . $updated;
        }

        $latest_activity = Activity::select('status')->where('code',$tracking->code)->orderBy('id','desc')->first()->status;
        if($latest_activity == 'accepted') {
            Session::put('already_accepted',true);
            return redirect()->back();
        }

        if($old_facility != $req->referred_to) {
        // Seen::where('tracking_id',$tracking->id)->delete();
            $data2 = array(
                'code' => $track,
                'patient_id' => $tracking->patient_id,
                'date_referred' => $date,
                'referred_from' => $tracking->referred_from,
                'referred_to' => $old_facility,
                'department_id' => $req->department_id,
                'referring_md' => $user->id,
                'action_md' => $user->id,
                'remarks' => "Patient's referral form was updated and was redirected to another facility. ".$updated_remarks,
                'status' => "rejected"
            );
            Activity::create($data2);

            $data2 = array(
                'code' => $track,
                'patient_id' => $tracking->patient_id,
                'date_referred' => $date,
                'referred_from' => $tracking->referred_from,
                'referred_to' => $req->referred_to,
                'department_id' => $req->department_id,
                'referring_md' => $user->id,
                'action_md' => $user->id,
                'remarks' => "Patient's referral form was updated and has been redirected to this facility. ".$updated_remarks,
                'status' => "redirected"
            );
            Activity::create($data2);

            $new_data = array(
                'date_referred' => $date,
                'date_arrived' => '',
                'date_seen' => '',
                'action_md' => $user->id,
                'department_id' => $req->department_id,
                'referred_to' => $req->referred_to,
                'referring_md' => $user->id,
                'status' => 'redirected'
            );
            $tracking->update($new_data);
        } else {
            $data2 = array(
                'code' => $track,
                'patient_id' => $tracking->patient_id,
                'date_referred' => $date,
                'referred_from' => $tracking->referred_from,
                'referred_to' => $req->referred_to,
                'department_id' => $req->department_id,
                'referring_md' => $user->id,
                'action_md' => $user->id,
                'remarks' => $updated_remarks,
                'status' => "form_updated"
            );
            Activity::create($data2);

            $new_data = array(
                'department_id' => $req->department_id,
            );
            $tracking->update($new_data);
        }
        unset($data_update['old_facility']);

        $data_update["department_id"] = $req->department_id;
        $data->update($data_update);
        Session::put('referral_update_save',true);
        Session::put('update_message','Successfully updated referral form!');

        $patient = Patients::find($tracking->patient_id);
        $date = date('Y-m-d H:i:s');
        $count_seen = Seen::where('tracking_id',$tracking->id)->count();
        $count_reco = Feedback::where("code",$track)->count();
        $count_activity = Activity::where("code",$track)
            ->where(function($query){
                $query->where("status","redirected");
            })
            ->groupBy("code")
            ->count();
        $latest_activity = Activity::where("code",$track)->orderBy("id","desc")->first();
        $tracking = Tracking::where('id', $id)->first();
        $redirect_track = asset("doctor/referred?referredCode=").$track;

        $update = [
            "patient_code" => $track,
            "patient_name" => ucfirst($patient->fname).' '.$patient->mname.' '.ucfirst($patient->lname),
            "activity_id" => $latest_activity->id,
            "referring_md" => ucfirst($user->fname).' '.ucfirst($user->lname),
            "referring_name" => Facility::find($user->facility_id)->name,
            "update_date" => date('M d, Y h:i A',strtotime($date)),
            "referred_to" => $latest_activity->referred_to,
            "referred_name" => Facility::find($latest_activity->referred_to)->name,
            "referred_department" => Department::where('id',$tracking->department_id)->first()->description,
            "referred_from" => $user->facility_id,
            "form_type" => $tracking->type,
            "tracking_id" => $tracking->id,
            "patient_sex" => $patient->sex,
            "age" => ParamCtrl::getAge($patient->dob),
            "status" => $tracking->status,
            "count_activity" => $count_activity,
            "count_seen" => $count_seen,
            "count_reco" => $count_reco,
            "old_facility" => $old_facility,
            "faci_changed" => ($old_facility == $latest_activity->referred_to) ? false : true,
            "redirect_track" => $redirect_track,
            "notif_type" => "update form"
        ];

        broadcast(new SocketReferralUpdate($update));
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

    public static function mapExplodedDataToArray($explodedData, $stringData)
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

    public static function explodeString($string)
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

    public static function calculateAge($dateOfBirth)
    {
        $dob = new DateTime($dateOfBirth); // Convert string to DateTime
        $now = new DateTime();             // Current date
        $age = $dob->diff($now)->y;        // Get the difference in years
        return $age;
    }
}