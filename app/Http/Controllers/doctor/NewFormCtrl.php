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
use App\Pregnancy;
use DB;

class NewFormCtrl extends Controller
{
        public function index(){
            return view('modal/revised_normal_form');
        }

        public function view_info(){
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
                ->select(
                    'past_medical_history.*',           
                    'pediatric_history.*',
                    'nutritional_status.*',
                    'glasgow_coma_scale.*',
                    'review_of_system.*',
                    'obstetric_and_gynecologic_history.*',
                    'latest_vital_signs.*',
                    'personal_and_social_history.*',
                )
                ->where('past_medical_history.patient_id', $patient_id)
                ->first();

            $pregnancy_data = DB::table('pregnancy')
                ->where('patient_id', $patient_id)
                ->get(); // Retrieves all rows for the patient

            // Debugging
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
            $rs_skin_methods=[];
            $rs_head_methods=[];
            $rs_eyes_methods=[];
            $rs_ears_methods=[];
            $rs_nose_methods=[];
            $rs_mouth_methods=[];
            $rs_neck_methods=[];
            $rs_breast_methods=[];
            $rs_respiratory_methods=[];
            $rs_gastrointestinal_methods=[];
            $rs_urinary_methods=[];
            $rs_peripheral_methods=[];
            $rs_musculoskeletal_methods=[];
            $rs_neurologic_methods=[];
            $rs_hematologic_methods=[];
            $rs_endocrine_methods=[];
            $rs_psychiatric_methods=[];

            $patient_id=2;

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
            //  Define the fields for review system with associated causes
            $rs_skin_fields = [
            'None' => [
                'cbox' => 'rs_skin_none_cbox',
                'other' => null
            ],
            'Rashes' => [
                'cbox' => 'rs_skin_rashes_cbox',
                'other' => null
            ],
            'Itching' => [
                'cbox' => 'rs_skin_itching_cbox',
                'other' => null
            ],
            'Change in hair or nails' => [
                'cbox' => 'rs_skin_hairchange_cbox',
                'other' => null
            ],
            ];
            $rs_head_fields = [
            'None' => [
                'cbox' => 'rs_head_none_cbox',
                'other' => null
            ],
            'Headaches' => [
                'cbox' => 'rs_head_headache_cbox',
                'other' => null
            ],
            'Head injury' => [
                'cbox' => 'rs_head_injury_cbox',
                'other' => null
            ],
            ];
            $rs_eyes_fields = [
            'None' => [
                'cbox' => 'rs_eyes_none_cbox',
                'other' => null
            ],
            'Glasses or Contacts' => [
                'cbox' => 'rs_eyes_glasses_cbox',
                'other' => null
            ],
            'Change in vision' => [
                'cbox' => 'rs_eyes_vision_cbox',
                'other' => null
            ],
            'Eye pain' => [
                'cbox' => 'rs_eyes_pain_cbox',
                'other' => null
            ],
            'Double Vision' => [
                'cbox' => 'rs_eyes_doublevision_cbox',
                'other' => null
            ],
            'Flashing lights' => [
                'cbox' => 'rs_eyes_flashing_cbox',
                'other' => null
            ],
            'Glaucoma/Cataracts' => [
                'cbox' => 'rs_eyes_glaucoma_cbox',
                'other' => null
            ],
            'Last eye exam' => [
                'cbox' => 'rs_eye_exam_cbox',
                'other' => null
            ],
            ];
            $rs_ears_fields = [
            'None' => [
                'cbox' => 'rs_ears_none_cbox',
                'other' => null
            ],
            'Change in hearing' => [
                'cbox' => 'rs_ears_changehearing_cbox',
                'other' => null
            ],
            'Ear pain' => [
                'cbox' => 'rs_ears_pain_cbox',
                'other' => null
            ],
            'Ear discharge' => [
                'cbox' => 'rs_ears_discharge_cbox',
                'other' => null
            ],
            'Ringing' => [
                'cbox' => 'rs_ears_ringing_cbox',
                'other' => null
            ],
            'Dizziness' => [
                'cbox' => 'rs_ears_dizziness_cbox',
                'other' => null
            ],
            ];
            $rs_nose_fields = [
            'None' => [
                'cbox' => 'rs_nose_none_cbox',
                'other' => null
            ],
            'Nose bleeds' => [
                'cbox' => 'rs_nose_bleeds_cbox',
                'other' => null
            ],
            'Nasal stuffiness' => [
                'cbox' => 'rs_nose_stuff_cbox',
                'other' => null
            ],
            'Frequent Colds' => [
                'cbox' => 'rs_nose_colds_cbox',
                'other' => null
            ],
            ];
            $rs_mouth_fields = [
            'None' => [
                'cbox' => 'rs_mouth_none_cbox',
                'other' => null
            ],
            'Bleeding gums' => [
                'cbox' => 'rs_mouth_bleed_cbox',
                'other' => null
            ],
            'Sore tongue' => [
                'cbox' => 'rs_mouth_soretongue_cbox',
                'other' => null
            ],
            'Sore throat' => [
                'cbox' => 'rs_mouth_sorethroat_cbox',
                'other' => null
            ],
            'Hoarseness' => [
                'cbox' => 'rs_mouth_hoarse_cbox',
                'other' => null
            ],
            ];
            $rs_neck_fields = [
            'None' => [
                'cbox' => 'rs_neck_none_cbox',
                'other' => null
            ],
            'Lumps' => [
                'cbox' => 'rs_neck_lumps_cbox',
                'other' => null
            ],
            'Swollen glands' => [
                'cbox' => 'rs_neck_swollen_cbox',
                'other' => null
            ],
            'Goiter' => [
                'cbox' => 'rs_neck_goiter_cbox',
                'other' => null
            ],
            'Stiffness' => [
                'cbox' => 'rs_neck_stiff_cbox',
                'other' => null
            ],
            ];

            $rs_breast_fields = [
            'None' => [
                'cbox' => 'rs_breast_none_cbox',
                'other' => null
            ],
            'Lumps' => [
                'cbox' => 'rs_breast_lumps_cbox',
                'other' => null
            ],
            'Pain' => [
                'cbox' => 'rs_breast_pain_cbox',
                'other' => null
            ],
            'Nipple discharge' => [
                'cbox' => 'rs_breast_discharge_cbox',
                'other' => null
            ],
            'BSE' => [
                'cbox' => 'rs_breast_bse_cbox',
                'other' => null
            ],
            ];
            $rs_respiratory_fields = [
            'None' => [
                'cbox' => 'rs_respi_none_cbox',
                'other' => null
            ],
            'Shortness of breath' => [
                'cbox' => 'rs_respi_shortness_cbox',
                'other' => null
            ],
            'Cough' => [
                'cbox' => 'rs_respi_cough_cbox',
                'other' => null
            ],
            'Production of phlegm, color' => [
                'cbox' => 'rs_respi_phlegm_cbox',
                'other' => null
            ],
            'Wheezing' => [
                'cbox' => 'rs_respi_wheezing_cbox',
                'other' => null
            ],
            'Coughing up blood' => [
                'cbox' => 'rs_respi_coughblood_cbox',
                'other' => null
            ],
            'Chest pain' => [
                'cbox' => 'rs_respi_chestpain_cbox',
                'other' => null
            ],
            'Fever' => [
                'cbox' => 'rs_respi_fever_cbox',
                'other' => null
            ],
            'Night sweats' => [
                'cbox' => 'rs_respi_sweats_cbox',
                'other' => null
            ],
            'Swelling in hands/feet' => [
                'cbox' => 'rs_respi_swelling_cbox',
                'other' => null
            ],
            'Blue fingers/toes' => [
                'cbox' => 'rs_respi_bluefingers_cbox',
                'other' => null
            ],
            'High blood pressure' => [
                'cbox' => 'rs_respi_highbp_cbox',
                'other' => null
            ],
            'Skipping heart beats' => [
                'cbox' => 'rs_respi_skipheartbeats_cbox',
                'other' => null
            ],
            'Heart murmur' => [
                'cbox' => 'rs_respi_heartmurmur_cbox',
                'other' => null
            ],
            'HX of heart medication' => [
                'cbox' => 'rs_respi_hxheart_cbox',
                'other' => null
            ],
            'Bronchitis/emphysema' => [
                'cbox' => 'rs_respi_brochitis_cbox',
                'other' => null
            ],
            'Rheumatic heart disease' => [
                'cbox' => 'rs_respi_rheumaticheart_cbox',
                'other' => null
            ],
            
            ];
            $rs_gastrointestinal_fields = [
            'None' => [
                'cbox' => 'rs_gastro_none_cbox',
                'other' => null
            ],
            'Change of appetite or weight' => [
                'cbox' => 'rs_gastro_appetite_cbox',
                'other' => null
            ],
            'Problems swallowing' => [
                'cbox' => 'rs_gastro_swallow_cbox',
                'other' => null
            ],
            'Nausea' => [
                'cbox' => 'rs_gastro_nausea_cbox',
                'other' => null
            ],
            'Heartburn' => [
                'cbox' => 'rs_gastro_heartburn_cbox',
                'other' => null
            ],
            'Vomiting' => [
                'cbox' => 'rs_gastro_vomit_cbox',
                'other' => null
            ],
            'Vomiting Blood' => [
                'cbox' => 'rs_gastro_vomitblood_cbox',
                'other' => null
            ],
            'Constipation' => [
                'cbox' => 'rs_gastro_constipation_cbox',
                'other' => null
            ],
            'Diarrhea' => [
                'cbox' => 'rs_gastro_diarrhea_cbox',
                'other' => null
            ],
            'Change in bowel habits' => [
                'cbox' => 'rs_gastro_bowel_cbox',
                'other' => null
            ],
            'Abdominal pain' => [
                'cbox' => 'rs_gastro_abdominal_cbox',
                'other' => null
            ],
            'Excessive belching' => [
                'cbox' => 'rs_gastro_belching_cbox',
                'other' => null
            ],
            'Excessive flatus' => [
                'cbox' => 'rs_gastro_flatus_cbox',
                'other' => null
            ],
            'Yellow color of skin (Jaundice/Hepatitis)' => [
                'cbox' => 'rs_gastro_jaundice_cbox',
                'other' => null
            ],
            'Food intolerance' => [
                'cbox' => 'rs_gastro_intolerance_cbox',
                'other' => null
            ],
            'Rectal bleeding/Hemorrhoids' => [
                'cbox' => 'rs_gastro_rectalbleed_cbox',
                'other' => null
            ],
            ];
            $rs_urinary_fields = [
            'None' => [
                'cbox' => 'rs_urin_none_cbox',
                'other' => null
            ],
            'Difficulty in urination' => [
                'cbox' => 'rs_urin_difficult_cbox',
                'other' => null
            ],
            'Pain or burning on urination' => [
                'cbox' => 'rs_urin_pain_cbox',
                'other' => null
            ],
            'Frequent urination at night' => [
                'cbox' => 'rs_urin_frequent_cbox',
                'other' => null
            ],
            'Urgent need to urinate' => [
                'cbox' => 'rs_urin_urgent_cbox',
                'other' => null
            ],
            'Incontinence of urine' => [
                'cbox' => 'rs_urin_incontinence_cbox',
                'other' => null
            ],
            'Dribbling' => [
                'cbox' => 'rs_urin_dribbling_cbox',
                'other' => null
            ],
            'Decreased urine stream' => [
                'cbox' => 'rs_urin_decreased_cbox',
                'other' => null
            ],
            'Blood in urine' => [
                'cbox' => 'rs_urin_blood_cbox',
                'other' => null
            ],
            'UTI/stones/prostate infection' => [
                'cbox' => 'rs_urin_uti_cbox',
                'other' => null
            ],
            ];
            $rs_peripheral_fields = [
            'None' => [
                'cbox' => 'rs_peri_none_cbox',
                'other' => null
            ],
            'Leg cramps' => [
                'cbox' => 'rs_peri_legcramp_cbox',
                'other' => null
            ],
            'Varicose veins' => [
                'cbox' => 'rs_peri_varicose_cbox',
                'other' => null
            ],
            'Clots in veins' => [
                'cbox' => 'rs_peri_veinclot_cbox',
                'other' => null
            ],
            ];

            $rs_musculoskeletal_fields = [
            'None' => [
                'cbox' => 'rs_muscle_none_cbox',
                'other' => null
            ],
            'Pain' => [
                'cbox' => 'rs_musclgit e_pain_cbox',
                'other' => null
            ],
            'Swelling' => [
                'cbox' => 'rs_muscle_swell_cbox',
                'other' => null
            ],
            'Stiffness' => [
                'cbox' => 'rs_muscle_stiff_cbox',
                'other' => null
            ],
            'Decreased joint motion' => [
                'cbox' => 'rs_muscle_decmotion_cbox',
                'other' => null
            ],
            'Broken bone' => [
                'cbox' => 'rs_muscle_brokenbone_cbox',
                'other' => null
            ],
            'Serious sprains' => [
                'cbox' => 'rs_muscle_sprain_cbox',
                'other' => null
            ],
            'Arthritis' => [
                'cbox' => 'rs_muscle_arthritis_cbox',
                'other' => null
            ],
            'Gout' => [
                'cbox' => 'rs_muscle_gout_cbox',
                'other' => null
            ],
            ];
            $rs_neurologic_fields = [
            'None' => [
                'cbox' => 'rs_neuro_none_cbox',
                'other' => null
            ],
            'Headaches' => [
                'cbox' => 'rs_neuro_headache_cbox',
                'other' => null
            ],
            'Seizures' => [
                'cbox' => 'rs_neuro_seizure_cbox',
                'other' => null
            ],
            'Loss of Consciousness/Fainting' => [
                'cbox' => 'rs_neuro_faint_cbox',
                'other' => null
            ],
            'Paralysis' => [
                'cbox' => 'rs_neuro_paralysis_cbox',
                'other' => null
            ],
            'Weakness' => [
                'cbox' => 'rs_neuro_weakness_cbox',
                'other' => null
            ],
            'Loss of muscle size' => [
                'cbox' => 'rs_neuro_sizeloss_cbox',
                'other' => null
            ],
            'Muscle Spasm' => [
                'cbox' => 'rs_neuro_spasm_cbox',
                'other' => null
            ],
            'Tremor' => [
                'cbox' => 'rs_neuro_tremor_cbox',
                'other' => null
            ],
            'Involuntary movement' => [
                'cbox' => 'rs_neuro_involuntary_cbox',
                'other' => null
            ],
            'Incoordination' => [
                'cbox' => 'rs_neuro_incoordination_cbox',
                'other' => null
            ],
            'Numbness' => [
                'cbox' => 'rs_neuro_numbness_cbox',
                'other' => null
            ],
            'Feeling of "pins and needles/tingles' => [
                'cbox' => 'rs_neuro_tingles_cbox',
                'other' => null
            ],
            ];
            $rs_hematologic_fields = [
            'None' => [
                'cbox' => 'rs_hema_none_cbox',
                'other' => null
            ],
            'Anemia' => [
                'cbox' => 'rs_hema_anemia_cbox',
                'other' => null
            ],
            'Easy bruising/bleeding' => [
                'cbox' => 'rs_hema_bruising_cbox',
                'other' => null
            ],
            'Past Transfusions' => [
                'cbox' => 'rss_hema_transfusion_cbox',
                'other' => null
            ],
            ];
            $rs_endocrine_fields = [
            'None' => [
                'cbox' => 'rs_endo_none_cbox',
                'other' => null
            ],
            'Abnormal growth' => [
                'cbox' => 'rs_endo_abnormal_cbox',
                'other' => null
            ],
            'Increased appetite' => [
                'cbox' => 'rs_endo_appetite_cbox',
                'other' => null
            ],
            'Increased thirst' => [
                'cbox' => 'rs_endo_thirst_cbox',
                'other' => null
            ],
            'Increased urine production' => [
                'cbox' => 'rs_endo_urine_cbox',
                'other' => null
            ],
            'Thyroid troubles' => [
                'cbox' => 'rs_endo_thyroid_cbox',
                'other' => null
            ],
            'Heat/cold intolerancee' => [
                'cbox' => 'rs_endo_heatcold_cbox',
                'other' => null
            ],
            'Excessive sweating' => [
                'cbox' => 'rs_endo_sweat_cbox',
                'other' => null
            ],
            'Diabetes' => [
                'cbox' => 'rs_endo_diabetes_cbox',
                'other' => null
            ],];
            $rs_psychiatric_fields = [
            'None' => [
                'cbox' => 'rs_psych_none_cbox',
                'other' => null
            ],
            'Tension/Anxiety' => [
                'cbox' => 'rs_psych_tension_cbox',
                'other' => null
            ],
            'Depression/suicide ideation' => [
                'cbox' => 'rs_psych_depression_cbox',
                'other' => null
            ],
            'Memory problems' => [
                'cbox' => 'rs_psych_memory_cbox',
                'other' => null
            ],
            'Unusual problems' => [
                'cbox' => 'rs_psych_unusual_cbox',
                'other' => null
            ],
            'Sleep problems' => [
                'cbox' => 'rs_psych_sleep_cbox',
                'other' => null
            ],
            'Past treatment with psychiatrist' => [
                'cbox' => 'rs_psych_treatment_cbox',
                'other' => null
            ],
            'Change in mood/change in attitude towards family/friends' => [
                'cbox' => 'rs_psych_moodchange_cbox',
                'other' => null
            ],
            ];
            // Define the fields for contraceptive with associated causes
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

            $this->dataArray($contraceptive_fields, $contraceptive_methods, $request);

            
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
                'heredofamilial_diseases' => $heredofamilial_diseases,
                'allergies' => $allergies,
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
                'patient_id'=>$patient_id,
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

        

            // if ($request->smoking_radio=="No" && $request->smoking_sticks_per_day == null){
            //     $smoke_quit_year = "N/A";
            //     $smoke_sticks_per_day= "N/A";
            // }else {
            //     $smoke_quit_year = $request->smoking_year_quit;
            //     $smoke_sticks_per_day = $request->smoking_sticks_per_day;
            // }
            // if ($request->alcohol_radio=="No" && $request->alcohol_liquor_type==null){
            //     $drinking_quit_year = "N/A";
            //     $liquor_type = "N/A";
            // }else {
            //     $drinking_quit_year=$request->alcohol_year_quit;
            //     $liquor_type = $request->alcohol_liquor_type;
            // }
            // if ($request->illicit_drugs == "No" && $request->illicit_drugs_taken==null){
            //     $drugs_quit_year = "N/A";
            //     $drugs_taken="N/A";
            // }else {
            //     $drugs_quit_year = $request->drugs_year_quit;
            //     $drugs_taken = $request->illicit_drugs_taken;
            // }

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
                'patient_id'=>$patient_id,
                'skin'=>$rs_skin_methods,
                'head'=>$rs_head_methods,
                'eyes'=>$rs_eyes_methods,
                'ears'=>$rs_ears_methods,
                'nose_or_sinuses'=>$rs_nose_methods,
                'mouth_or_throat'=>$rs_mouth_methods,
                'neck'=>$rs_neck_methods,
                'breast'=>$rs_breast_methods,
                'respiratory_or_cardiac'=>$rs_respiratory_methods,
                'gastrointestinal'=>$rs_gastrointestinal_methods,
                'urinary'=>$rs_urinary_methods,
                'peripheral_vascular'=>$rs_peripheral_methods,
                'musculoskeletal'=>$rs_musculoskeletal_methods,
                'neurologic'=>$rs_neurologic_methods,
                'hematologic'=>$rs_hematologic_methods,
                'endocrine'=>$rs_endocrine_methods,
                'psychiatric'=>$rs_psychiatric_methods,
            ];
            $nutritional_status = [
                'patient_id'=>$patient_id,
                'diet'=>$request->diet_radio,
                'specify_diets'=>$request->diet,
            ];
            $glasgocoma_scale = [
                'patient_id'=>$patient_id,
                'pupil_size_chart'=>$request->glasgow_pupil_btn,
                'motor_response'=>$request->motor_radio,
                'verbal_response'=>$request->verbal_radio,
                'eye_response'=>$request->eye_radio,
                'gsc_score'=>$request->gcs_score,

            ];
            $vital_signs = [
                'patient_id'=>$patient_id,
                'temperature'=>$request->vital_temp,
                'pulse_rate'=>$request->vital_pulse,
                'respiratory_rate'=>$request->vital_respi_rate,
                'blood_pressure'=>$request->vital_bp,
                'oxygen_saturation'=>$request->vital_oxy_saturation,
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
                    'patient_id'=>$patient_id,
                    'pregnancy_order'=> $order,
                    'pregnancy_year'=> $years[$key],
                    'pregnancy_gestation_completed'=> $gestations[$key],
                    'pregnancy_outcome'=> $outcomes[$key],
                    'pregnancy_place_of_birth'=> $placesOfBirth[$key],
                    'pregnancy_sex'=> $sexes[$key],
                    'pregnancy_birth_weight'=> $birthWeights[$key],
                    'pregnancy_present_status'=> $presentStatuses[$key],
                    'pregnancy_complication'=> $complications[$key],
                ]);
            }
            


            // Save to database
            PastMedicalHistory::create($past_medical_history_data);
            PediatricHistory::create($pediatric_history);
            ObstetricAndGynecologicHistory::create($obstetric_history);
            PersonalAndSocialHistory::create($personal_history);
            ReviewOfSystems::create($review_of_system);
            NutritionalStatus::create($nutritional_status);
            GlasgoComaScale::create($glasgocoma_scale);
            LatestVitalSigns::create($vital_signs);
            // Pregnancy::create($pregnancy_data);

            // Debugging
            // dd($request->all());
            // $glasgocoma_scale);

            return redirect("/revised/referral/info/{$patient_id}");

    }

    public function dataArray($dataFields, &$dataMethods, $request)   {
        foreach ($dataFields as $default_method => $fields) {
            $cbox = $fields['cbox'];
            $other_data = $fields['other'];

            if ($request->$cbox == "Yes") {
                $dataMethod = $default_method;

                if ($other_data && $request->$other_data) {
                    $dataMethod .= ' => ' . $request->$other_data;
                }

                $dataMethods[] = $dataMethod;
            }
        }
    }
    
}
