<?php
namespace App\Http\Controllers\doctor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\doctor\NewFormCtrl;

use App\User;
use App\Tracking;
use App\Department;
use App\Facility;
use App\PatientForm;
use App\PregnantForm;
use App\Patients;

use DB;
use Anouar\Fpdf\Fpdf;
use App\Barangay;
use App\Muncity;
use App\Province;

class PrintNewFormCtrl extends Controller
{
    public function generatePdf($patient_id,$track_id,$form_type)
    {
        $data = [];
        
        $tracking_data = Tracking::select('code',
        DB::raw("DATE_FORMAT(date_transferred, '%M %d, %Y %h:%i %p') as date_transferred"))->where('id', $track_id)->first();
        $patients_form = PatientForm::select('referred_to',
        'department_id',
        'referring_facility',
        DB::raw("DATE_FORMAT(time_referred, '%M %d, %Y %h:%i %p') as time_referred"),
        'covid_number',
        'refer_clinical_status',
        'refer_sur_category',
        'dis_clinical_status',
        'dis_sur_category')->where('code',$tracking_data->code)->first();
        $pregnant_form = PregnantForm::select('referred_to','department_id','referred_by')->where('code', $tracking_data->code)->first();
        $referred_to = Facility::select('name','address')->where('id',$pregnant_form->referred_to)->first();
        $referred_to_normal = Facility::select('name','address')->where('id',$patients_form->referred_to)->first();
        $department = Department::select('description')->where('id',$pregnant_form->department_id)->first();
        $department_normal = Department::select('description')->where('id',$patients_form->department_id)->first();
        $referring_facility_normal = Facility::select('name','contact','address')->where('id',$patients_form->referring_facility)->first();
        $referring_md = User::select('fname','mname','lname')->where('id',$pregnant_form->referred_by)->first();
        $patients_name = Patients::select('fname','mname','lname','dob', 'brgy', 'province', 'muncity','region','sex','civil_status','phic_id')->where('id',$patient_id)->first();
        $referring_md_name = 'Dr. '. $referring_md->fname . ', ' . $referring_md->mname . ', ' . $referring_md->lname; 
        $woman_name = $patients_name->fname . ' '.$patients_name->mname.' '.$patients_name->lname;
        $barangay = Barangay::select('description')->where('id', $patients_name->brgy)->first();
        $province = Province::select('description')->where('id', $patients_name->province)->first();
        $muncity = Muncity::select('description')->where('id', $patients_name->muncity)->first();
        $patient_address = $patients_name->region.', '.$province->description.', '.$muncity->description.', '.$barangay->description;


        $comor_string = $data->commordities;
        $comor_explodedData = explode(',', $comor_string);
        $comorbidities = ['Hypertension', 'Diabetes', 'Asthma', 'Cancer', 'Others'];

        $allergies_string = $data->allergies;
        $allergies_explodedData = explode(',', $allergies_string);
        $allergies = ['Food', 'Drugs', 'Others'];

        $heredo_string = $data->heredofamilial_diseases;
        $heredo_exploadedData = explode(',', $heredo_string);
        $heredofamilial = ['Hypertension', 'Diabetes', 'Asthma', 'Cancer', 'Kidney Disease', 'Thyroid Disease', 'Others'];

        $comor_dataArray = NewFormCtrl::mapExplodedDataToArray($comor_explodedData, $comorbidities);
        $allergies_dataArray = NewFormCtrl::mapExplodedDataToArray($allergies_explodedData, $allergies);
        $heredo_dataArray = NewFormCtrl::mapExplodedDataToArray($heredo_exploadedData, $heredofamilial);

        $pdf = new Fpdf();
        $x = ($pdf->w) - 20;

        $pdf->setTopMargin(10);
        $pdf->setTitle($woman_name .': '.$form_type.' '.$patient_id.'-'.$track_id);
        $pdf->addPage();
   
        if ($form_type === 'normal'){ 
        $data = NewFormCtrl::fetchDataNormal($patient_id,$track_id);
        self::printNormal($pdf,
        $x, 
        $patient_address, 
        $woman_name, 
        $tracking_data, 
        $referring_facility_normal, 
        $referred_to_normal, $department_normal, 
        $patients_form, 
        $patients_name, 
        $data);

        }else if ($form_type === 'pregnant'){
        $data = NewFormCtrl::fetchDataFromDB($patient_id,$track_id); 
         self::printPregnant(
            $x,
            $data,
            $pdf,
            $data->referring_facility,
            $referred_to,
            $department,
            $tracking_data,
            $form_type,
            $patients_name,
            $patient_address,
            $woman_name,
            $referring_md_name
         );   
        }
        
       self::printNewFormPDF($pdf,$patient_id,$data,$comor_dataArray, $allergies_dataArray, $heredo_dataArray,$patients_name,$form_type);
        $pdf->Output();
        exit();
       
    }

    public function printNewFormPDF($pdf,$patient_id,$data,$comor_dataArray, $allergies_dataArray, $heredo_dataArray,$patients_name,$form_type){
        // DIAGNOSIS
          if (!empty($data->other_diagnoses) || !empty($data->other_reason_referral)) {
            $this->titleHeader($pdf, "DIAGNOSIS");
            if (isset($data->icd[0])) {
                $pdf->MultiCell(0, 7, self::black($pdf, "ICD-10: "), 0, 'L');
                $pdf->SetTextColor(102, 56, 0);
                $pdf->SetFont('Arial', 'I', 10);
                foreach ($data->icd as $icd) {
                   if(!empty($icd->code)&&!empty($icd->description)){
                    $pdf->MultiCell(0, 7, self::black($pdf, "ICD-10: ") . "\n" . self::staticGreen($pdf, utf8_decode($icd->description)), 1, 'L');}
                }
                $pdf->Ln();
            }
            if (isset($data->diagnosis)) {
                $pdf->SetTextColor(102, 56, 0);
                $pdf->SetFont('Arial', 'I', 10);
                if(!empty($data->diagnosis)){
                    $pdf->MultiCell(0, 7, self::black($pdf, "Diagnosis/Impression: ") . "\n" . self::staticGreen($pdf, utf8_decode($data->diagnosis)), 1, 'L');}
                $pdf->Ln();
            }
    
            if (isset($data->other_diagnoses)) {
                $pdf->SetTextColor(102, 56, 0);
                $pdf->SetFont('Arial', 'I', 10);
                if(!empty($data->other_diagnoses)){
                    $pdf->MultiCell(0, 7, self::black($pdf, "Other diagnosis: ") . "\n" . self::staticGreen($pdf, utf8_decode($data->other_diagnoses)), 1, 'L');}
                $pdf->Ln();
            }
        } 

        // PAST MEDICAL HISTORY
        if (!empty($data->commordities) || !empty($data->commordities_hyper_year) || !empty($data->commordities_diabetes_year) || !empty($data->commordities_asthma_year) || !empty($data->commordities_cancer)
        || !empty($data->commordities_others) || !empty($data->allergies) || !empty($data->allergy_food_cause) || !empty($data->allergy_drugs_cause) || !empty($data->heredofamilial_diseases) || !empty($data->heredo_hyper_side) 
        || !empty($data->heredo_diab_side) || !empty($data->heredo_asthma_side) || !empty($data->heredo_cancer_side) || !empty($data->heredo_kidney_side) || !empty($data->heredo_thyroid_side) 
        || !empty($data->heredo_others) || !empty($data->previous_hospitalization))
        {
            $this->titleHeader($pdf, "PAST MEDICAL HISTORY");

            $pdf->MultiCell(0, 7, "Comorbidities:" . self::green($pdf, NewFormCtrl::explodeString($data->commordities), 'Comorbidities'), 1, 'L');

            if (!empty($comor_dataArray['Hypertension'])) {
                $pdf->MultiCell(0, 7, "Hypertension Year:" . self::green($pdf, NewFormCtrl::explodeString($data->commordities_hyper_year), 'Hypertension Year'), 1, 'L');
            }
            if (!empty($comor_dataArray['Diabetes'])) {
                $pdf->MultiCell(0, 7, "Diabetes Year:" . self::green($pdf, NewFormCtrl::explodeString($data->commordities_diabetes_year), 'Diabetes Year'), 1, 'L');
            }
            if (!empty($comor_dataArray['Asthma'])) {
                $pdf->MultiCell(0, 7, "Asthma Year:" . self::green($pdf, NewFormCtrl::explodeString($data->commordities_asthma_year), 'Asthma Year'), 1, 'L');
            }
            if (!empty($comor_dataArray['Cancer'])) {
                $pdf->MultiCell(0, 7, "Cancer:" . self::green($pdf, NewFormCtrl::explodeString($data->commordities_cancer), 'Cancer'), 1, 'L');
            }
            if (!empty($comor_dataArray['Others'])) {
                $pdf->MultiCell(0, 7, "Others:" . self::green($pdf, NewFormCtrl::explodeString($data->commordities_others), 'Others'), 1, 'L');
            }
            if (!empty($data->allergies)){$pdf->MultiCell(0, 7, "Allergies:" . self::green($pdf,NewFormCtrl::explodeString($data->allergies), 'Allergies'), 1, 'L');}
            if (!empty($allergies_dataArray['Food'])) {
                $pdf->MultiCell(0, 7, "Food(s): (ex. crustaceans, eggs):" . self::green($pdf, NewFormCtrl::explodeString($data->allergy_food_cause), 'Food(s): (ex. crustaceans, eggs)'), 1, 'L');
            }
            if (!empty($allergies_dataArray['Drugs'])) {
                $pdf->MultiCell(0, 7, "Drugs allergy:" . self::green($pdf, NewFormCtrl::explodeString($data->allergy_drugs_cause), 'Drugs allergy'), 1, 'L');
            }
            if(!empty($data->heredofamilial_diseases)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "HEREDOFAMILIAL DISEASES: ") . "\n" . self::staticGreen($pdf, NewFormCtrl::explodeString($data->heredofamilial_diseases)), 1, 'L');}
            if (!empty($heredo_dataArray['Hypertension'])) {
                $pdf->MultiCell(0, 7, "Hypertension side:" . self::green($pdf, NewFormCtrl::explodeString($data->heredo_hyper_side), 'Hypertension side'), 1, 'L');
            }
            if (!empty($heredo_dataArray['Diabetes'])) {
                $pdf->MultiCell(0, 7, "Diabetes side:" . self::green($pdf, NewFormCtrl::explodeString($data->heredo_diab_side), 'Diabetes side'), 1, 'L');
            }
            if (!empty($heredo_dataArray['Asthma'])) {
                $pdf->MultiCell(0, 7, "Asthma side:" . self::green($pdf, NewFormCtrl::explodeString($data->heredo_asthma_side), 'Asthma side'), 1, 'L');
            }
            if (!empty($heredo_dataArray['Cancer'])) {
                $pdf->MultiCell(0, 7, "Cancer side:" . self::green($pdf, NewFormCtrl::explodeString($data->heredo_cancer_side), 'Cancer side'), 1, 'L');
            }
            if (!empty($heredo_dataArray['Kidney Disease'])) {
                $pdf->MultiCell(0, 7, "Kidney Disease side:" . self::green($pdf, NewFormCtrl::explodeString($data->heredo_kidney_side), 'Kidney Disease side'), 1, 'L');
            }
            if (!empty($heredo_dataArray['Thyroid Disease'])) {
                $pdf->MultiCell(0, 7, "Thyroid Disease:" . self::green($pdf, NewFormCtrl::explodeString($data->heredo_thyroid_side), 'Thyroid Disease'), 1, 'L');
            }
            if (!empty($heredo_dataArray['Others'])) {
                $pdf->MultiCell(0, 7, "Others:" . self::green($pdf, utf8_decode(NewFormCtrl::explodeString($data->heredo_others)), 'Others'), 1, 'L');
            }
            if(!empty($data->previous_hospitalization)){
                $pdf->MultiCell(0, 7, self::staticBlack($pdf, "PREVIOUS HOSPITALIZATION(S) and OPERATION(S): ") . "\n" . self::staticGreen($pdf, utf8_decode($data->previous_hospitalization)), 1, 'L');
            }
        }

        // PEDIATRIC HISTORY
        if ($form_type === 'normal' && NewFormCtrl::calculateAge($patients_name->dob)<=18){
            $this->titleHeader($pdf, "PEDIATRIC HISTORY");
              if (!empty($data->prenatal_a)){
                  $pdf->MultiCell(0, 7, "Prenatal A:" . self::green($pdf, $data->prenatal_a, 'Prenatal A'), 1, 'L');
              }
              if (!empty($data->prenatal_g)){
                  $pdf->MultiCell(0, 7, "Prenatal G:" . self::green($pdf, $data->prenatal_g, 'Prenatal G'), 1, 'L');
              }
              if (!empty($data->prenatal_p)){
                  $pdf->MultiCell(0, 7, "Prenatal P:" . self::green($pdf, $data->prenatal_p, 'Prenatal P'), 1, 'L');
              }
              if (!empty($data->prenatal_radiowith_or_without) || !empty($data->prenatal_with_maternal_illness)){
                  if ($data->prenatal_radiowith_or_without == "with") {
                      $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Maternal Illness: ") . "\n" . self::staticGreen($pdf, $data->prenatal_with_maternal_illness), 1, 'L');
                  } else if ($data->prenatal_radiowith_or_without == "without") {
                      $pdf->MultiCell(0, 7, "Maternal Illness:" . self::green($pdf, $data->prenatal_radiowith_or_without . " illness", 'Maternal Illness'), 1, 'L');
                  }
              }

              $pdf->ln(1);
              $pdf->SetFillColor(235, 235, 235);

              if (!empty($data->natal_born_at)){
                  $pdf->MultiCell(0, 7, "Born At:" . self::green($pdf, $data->natal_born_at, 'Born At'), 1, 'L');
              }
              if (!empty($data->natal_born_address)){
                  $pdf->MultiCell(0, 7, "Born Address:" . self::green($pdf, $data->natal_born_address, 'Born Address'), 1, 'L');
              }
              if (!empty($data->natal_by)){
                  $pdf->MultiCell(0, 7, "By:" . self::green($pdf, $data->natal_by, 'By'), 1, 'L');
              }
              if (!empty($data->natal_via)){
                  $pdf->MultiCell(0, 7, "Via:" . self::green($pdf, $data->natal_via, 'Via'), 1, 'L');
              }
              if (!empty($data->natal_indication)){
                  $pdf->MultiCell(0, 7, "Indication:" . self::green($pdf, $data->natal_indication, 'Indication'), 1, 'L');
              }
              if (!empty($data->natal_term)) {
                  $pdf->MultiCell(0, 7, "Term:" . self::green($pdf, $data->natal_term, 'Term'), 1, 'L');
              }
              if (!empty($data->natal_with_good_cry)){
                  $pdf->MultiCell(0, 7, "With Good Cry:" . self::green($pdf, $data->natal_with_good_cry, 'With Good Cry'), 1, 'L');
              }
              if (!empty($data->natal_other_complications)){
                  $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Other Complications: ") . "\n" . self::staticGreen($pdf, $data->natal_other_complications), 1, 'L');
              }

              $pdf->ln(1);
              $pdf->SetFillColor(235, 235, 235);
              $pdf->SetTextColor(40);
              $pdf->ln(1);
              
      
              
              if (!empty($data->post_natal_bfeedx_month)){
                  $pdf->MultiCell(0, 7, "Feeding History", 1, 'L', true);
                  if ($data->post_natal_bfeed == "Yes") {
                      if (!empty($data->post_natal_bfeed)){ $pdf->MultiCell(0, 7, "Breast Feed:" . self::green($pdf, $data->post_natal_bfeed, 'Breast Feed'), 1, 'L'); }
                      if (!empty($data->post_natal_bfeedx_month)){ $pdf->MultiCell(0, 7, "Breast Feed in mos:" . self::green($pdf, $data->post_natal_bfeedx_month, 'Breast Feed in mos'), 1, 'L');}  
                  } else if ($data->post_natal_bfeed == "No") {
                      if (!empty($data->post_natal_bfeed)){$pdf->MultiCell(0, 7, "Breast Feed:" . self::green($pdf, $data->post_natal_bfeed, 'Breast Feed'), 1, 'L');}
                  }
              }
              if (!empty($data->post_natal_formula_feed)){
                  if ($data->post_natal_formula_feed == "Yes") {
                      $pdf->MultiCell(0, 7, "Formula Feed:" . self::green($pdf, $data->post_natal_formula_feed, 'Formula Feed'), 1, 'L');
                      if (!empty($data->post_natal_ffeed_specify)){
                       $pdf->MultiCell(0, 7, "Specific Formula Feed:" . self::green($pdf, $data->post_natal_ffeed_specify, 'Specific Formula Feed'), 1, 'L');
                       $pdf->MultiCell(0, 7, "Started Semi Food in mos:" . self::green($pdf, $data->post_natal_ffeed_specify, 'Started Semi Food in mos'), 1, 'L');
                      }
                     
                  } else if ($data->post_formula_feed == "No") {
                      $pdf->MultiCell(0, 7, "Formula Feed:" . self::green($pdf, $data->post_natal_formula_feed, 'Formula Feed'), 1, 'L');
                  }
                  
              }
              
              $pdf->ln(1);
             
              if (!empty($data->post_natal_bcg)){
                  $pdf->MultiCell(0, 7, "Immunization History", 1, 'L', true);
                  if ($data->post_natal_bcg == "Yes") {
                      $pdf->MultiCell(0, 7, "BCG:" . self::green($pdf, "Immunized", 'BCG'), 1, 'L');
                  }
              }
              if (!empty($data->post_natal_dpt_opv_x)){
                  if ($data->post_natal_dpt_opv_x == "Yes") {
                      $pdf->MultiCell(0, 7, "DPT/OPV:" . self::green($pdf, "Immunized", 'DPT/OPV'), 1, 'L');
                     if(!empty($data->post_dpt_doses)){$pdf->MultiCell(0, 7, "DPT/OPV doses:" . self::green($pdf, $data->post_dpt_doses, 'DPT/OPV doses'), 1, 'L');}
                  }
              }
              
              if (!empty($data->post_natal_hepB_cbox)){
                  if ($data->post_natal_hepB_cbox == "Yes") {
                      $pdf->MultiCell(0, 7, "Hep B:" . self::green($pdf, "Immunized", 'Hep B'), 1, 'L');
                      if (!empty($data->post_natal_hepB_x_doses)) {$pdf->MultiCell(0, 7, "Hep B doses:" . self::green($pdf, $data->post_natal_hepB_x_doses, 'Hep B doses'), 1, 'L');}
                  }
              }
              if (!empty($data->post_natal_immu_measles_cbox)){
                  if ($data->post_natal_immu_measles_cbox == "Yes") {
                      $pdf->MultiCell(0, 7, "Measles:" . self::green($pdf, "Immunized", 'Measles'), 1, 'L');
                  }
              }
              if (!empty($data->post_natal_mmr_cbox)){
                  if ($data->post_natal_mmr_cbox == "Yes") {
                      $pdf->MultiCell(0, 7, "MMR:" . self::green($pdf, "Immunized", 'MMR'), 1, 'L');
                  }
              }
              if (!empty($data->post_natal_others_cbox)){
                  if ($data->post_natal_others_cbox == "Yes") {
                      if (!empty($data->post_natal_others)){$pdf->MultiCell(0, 7, "Others:" . self::green($pdf, $data->post_natal_others, 'Others'), 1, 'L');}
                  }
              }
              if (!empty($data->post_natal_development_milestones)){
                  $pdf->MultiCell(0, 7, "Developmental Milestones:" . self::green($pdf, $data->post_natal_development_milestones, 'Developmental Milestones'), 1, 'L');
              }      
        }

        // PERSONAL AND SOCIAL HISTORY
        if (!empty($data->smoking) || !empty($data->smoking_sticks_per_day) || !empty($data->smoking_quit_year) || !empty($data->smoking_remarks)
        || !empty($data->alcohol_drinking) || !empty($data->alcohol_liquor_type) || !empty($data->alcohol_bottles_per_day) || !empty($data->alcohol_drinking_quit_year)
        || !empty($data->illicit_drugs) || !empty($data->illicit_drugs_taken) || !empty($data->illicit_drugs_quit_year)){
            
            $this->titleHeader($pdf, "PERSONAL AND SOCIAL HISTORY");
            if (!empty($data->smoking)){
                if ($data->smoking == "Yes") {
                    $pdf->MultiCell(0, 7, "Smoking:" . self::green($pdf, $data->smoking, 'Smoking'), 1, 'L');
                   if(!empty($data->smoking_sticks_per_day)){$pdf->MultiCell(0, 7, "Sticks per Day:" . self::green($pdf, $data->smoking_sticks_per_day, 'Sticks per Day'), 1, 'L');}
                } else if ($data->smoking == "No") {
                    $pdf->MultiCell(0, 7, "Smoking:" . self::green($pdf, $data->smoking, 'Smoking'), 1, 'L');
                } else if ($data->smoking == "Quit") {
                    $pdf->MultiCell(0, 7, "Smoking:" . self::green($pdf, $data->smoking, 'Smoking'), 1, 'L');
                   if (!empty($data->smoking_quit_year)){$pdf->MultiCell(0, 7, "Smoking Quit Year:" . self::green($pdf, $data->smoking_quit_year, 'Smoking Quit Year'), 1, 'L');}
                }
            }
            if (!empty($data->smoking_remarks)){
                $pdf->MultiCell(0, 7, "Smoking Remarks:" . self::green($pdf, $data->smoking_remarks, 'Smoking Remarks'), 1, 'L');
            }
            if (!empty($data->alcohol_drinking)){
                if ($data->alcohol_drinking == "Yes") {
                    $pdf->MultiCell(0, 7, "Drinking:" . self::green($pdf, $data->alcohol_drinking, 'Drinking'), 1, 'L');
                    if(!empty($data->alcohol_liquor_type)){$pdf->MultiCell(0, 7, "Liquor Type:" . self::green($pdf, $data->alcohol_liquor_type, 'Liquor Type'), 1, 'L');}
                    if(!empty($data->alcohol_bottles_per_day)){$pdf->MultiCell(0, 7, "Bottles per day:" . self::green($pdf, $data->alcohol_bottles_per_day, 'Bottles per day'), 1, 'L');}
                } else if ($data->alcohol_drinking == "No") {
                    $pdf->MultiCell(0, 7, "Drinking:" . self::green($pdf, $data->alcohol_drinking, 'Drinking'), 1, 'L');
                } else if ($data->alcohol_drinking == "Quit") {
                    $pdf->MultiCell(0, 7, "Drinking:" . self::green($pdf, $data->alcohol_drinking, 'Drinking'), 1, 'L');
                    if(!empty($data->alcohol_drinking_quit_year)){$pdf->MultiCell(0, 7, "Drinking quit year:" . self::green($pdf, $data->alcohol_drinking_quit_year, 'Drinking quit year'), 1, 'L');}
                }
            }
          
            if(!empty($data->illicit_drugs)){
                if ($data->illicit_drugs == "Yes") {
                    $pdf->MultiCell(0, 7, "Drugs:" . self::green($pdf, $data->illicit_drugs, 'Drugs'), 1, 'L');
                   if(!empty($data->illicit_drugs_taken)){$pdf->MultiCell(0, 7, "Drugs taken:" . self::green($pdf, $data->illicit_drugs_taken, 'Drugs taken'), 1, 'L');}
                } else if ($data->illicit_drugs == "No") {
                    $pdf->MultiCell(0, 7, "Drugs:" . self::green($pdf, $data->illicit_drugs, 'Drugs'), 1, 'L');
                } else if ($data->illicit_drugs == "Quit") {
                    $pdf->MultiCell(0, 7, "Drugs:" . self::green($pdf, $data->illicit_drugs, 'Drugs'), 1, 'L');
                    if(!empty($data->illicit_drugs_quit_year)){$pdf->MultiCell(0, 7, "Drugs quit year:" . self::green($pdf, $data->illicit_drugs_quit_year, 'Drugs quit year'), 1, 'L');}
                }
            }  
        }

        // CURRENT MEDICATION
        if (!empty($data->current_medications)){
            $this->titleHeader($pdf, "CURRENT MEDICATION");
            $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Current Medications: ") . "\n" . self::staticGreen($pdf, NewFormCtrl::explodeString($data->current_medications)), 1, 'L');
        }

        // PERTINENT LABORATORY AND OTHER ANCILLARY PROCEDURES
        if (!empty($data->pertinent_laboratory_and_procedures)){
            $this->titleHeader($pdf, "PERTINENT LABORATORY AND OTHER ANCILLARY PROCEDURES");
            $pdf->MultiCell(0, 7, "Pertinent Laboratory:" . self::green($pdf, $data->pertinent_laboratory_and_procedures, 'Pertinent Laboratory'), 1, 'L');
            if(!empty($data->lab_procedure_other)){$pdf->MultiCell(0, 7, "Other Procedures:" . self::green($pdf, $data->lab_procedure_other, 'Other Procedures'), 1, 'L');}
        }

        // REVIEW OF SYSTEM
        if (!empty($data->skin) || !empty($data->head) || !empty($data->eyes) || !empty($data->ears) || !empty($data->nose_or_sinuses) || !empty($data->mouth_or_throat)
        || !empty($data->neck) || !empty($data->breast) || !empty($data->respiratory_or_cardiac) || !empty($data->gastrointestinal) || !empty($data->urinary)
        || !empty($data->peripheral_vascular) || !empty($data->musculoskeletal) || !empty($data->neurologic) || !empty($data->hematologic)
        || !empty($data->endocrine) || !empty($data->psychiatric)){
            $this->titleHeader($pdf, "REVIEW OF SYSTEMS");
           if(!empty($data->skin)){$pdf->MultiCell(0, 7, "Skin:" . self::green($pdf, NewFormCtrl::explodeString($data->skin), 'Skin'), 1, 'L');}
           if(!empty($data->head) ){$pdf->MultiCell(0, 7, "Head:" . self::green($pdf, NewFormCtrl::explodeString($data->head), 'Head'), 1, 'L');}
           if(!empty($data->eyes)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Eyes: ") . "\n" . self::staticGreen($pdf, NewFormCtrl::explodeString($data->eyes)), 1, 'L');}
           if(!empty($data->ears)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Ears: ") . "\n" . self::staticGreen($pdf, NewFormCtrl::explodeString($data->ears)), 1, 'L');}
           if(!empty($data->nose_or_sinuses)){$pdf->MultiCell(0, 7, "Nose/Sinuses:" . self::green($pdf, NewFormCtrl::explodeString($data->nose_or_sinuses), 'Nose/Sinuses'), 1, 'L');}
           if(!empty($data->mouth_or_throat)){$pdf->MultiCell(0, 7, "Mouth/Throat:" . self::green($pdf, NewFormCtrl::explodeString($data->mouth_or_throat), 'Mouth/Throat'), 1, 'L');}
           if(!empty($data->neck)){$pdf->MultiCell(0, 7, "Neck:" . self::green($pdf, NewFormCtrl::explodeString($data->neck), 'Neck'), 1, 'L');}
           if(!empty($data->breast)){$pdf->MultiCell(0, 7, "Breast:" . self::green($pdf, $data->breast, 'Breast'), 1, 'L');}
           if(!empty($data->respiratory_or_cardiac)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Respiratory/Cardiac: ") . "\n" . self::staticGreen($pdf, NewFormCtrl::explodeString($data->respiratory_or_cardiac)), 1, 'L');}
           if(!empty($data->gastrointestinal)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Gastrointestinal: ") . "\n" . self::staticGreen($pdf, NewFormCtrl::explodeString($data->gastrointestinal)), 1, 'L');}
           if(!empty($data->urinary)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Urinary: ") . "\n" . self::staticGreen($pdf, NewFormCtrl::explodeString($data->urinary)), 1, 'L');}
           if(!empty($data->peripheral_vascular)){$pdf->MultiCell(0, 7, "Peripheral Vascular:" . self::green($pdf, NewFormCtrl::explodeString($data->peripheral_vascular), 'Peripheral Vascular'), 1, 'L');}
           if(!empty($data->musculoskeletal)){$pdf->MultiCell(0, 7, "Musculoskeletal:" . self::green($pdf, NewFormCtrl::explodeString($data->musculoskeletal), 'Musculoskeletal'), 1, 'L');}
           if(!empty($data->neurologic)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Neurologic: ") . "\n" . self::staticGreen($pdf, NewFormCtrl::explodeString($data->neurologic)), 1, 'L');}
           if(!empty($data->hematologic)){$pdf->MultiCell(0, 7, "Hematologic:" . self::green($pdf, NewFormCtrl::explodeString($data->hematologic), 'Hematologic'), 1, 'L');}
           if(!empty($data->endocrine)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Endocrine: ") . "\n" . self::staticGreen($pdf, NewFormCtrl::explodeString($data->endocrine)), 1, 'L');}
           if(!empty($data->psychiatric)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Psychiatric: ") . "\n" . self::staticGreen($pdf,NewFormCtrl::explodeString($data->psychiatric)), 1, 'L');}
            // $pdf->addPage();
        }

        // NUTRITIONAL STATUS
        if (!empty($data->diet)){
            $this->titleHeader($pdf, "NUTRITIONAL STATUS");
            $pdf->MultiCell(0, 7, "Diet:" . self::green($pdf, $data->diet, 'Diet'), 1, 'L');
            if(!empty($data->specify_diets)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Specific Diet: ") . "\n" . self::staticGreen($pdf, NewFormCtrl::explodeString($data->specify_diets)), 1, 'L');}
        }
        
        // LATEST VITAL SIGNS
        if (!empty($data->pulse_rate)|| !empty($data->temperature) || !empty($data->blood_pressure) || !empty($data->oxygen_saturation) || !empty($data->respiratory_rate)){
            $this->titleHeader($pdf, "LATEST VITAL SIGNS");
            if(!empty($data->temperature)){$pdf->MultiCell(0, 7, "Temperature:" . self::green($pdf, $data->temperature, 'Temperature'), 1, 'L');}
            if(!empty($data->pulse_rate)){$pdf->MultiCell(0, 7, "Pulse Rate:" . self::green($pdf, $data->pulse_rate, 'Pulse Rate'), 1, 'L');}
            if(!empty($data->respiratory_rate)){$pdf->MultiCell(0, 7, "Respiratory Rate:" . self::green($pdf, $data->respiratory_rate, 'Respiratory Rate'), 1, 'L');}
            if(!empty($data->blood_pressure)){$pdf->MultiCell(0, 7, "Blood Pressure:" . self::green($pdf, $data->blood_pressure, 'Blood Pressure'), 1, 'L');}
            if(!empty($data->oxygen_saturation)){$pdf->MultiCell(0, 7, "Oxgen Saturation:" . self::green($pdf, $data->oxygen_saturation, 'Oxygen Saturation'), 1, 'L');}
        }

        // GLASGOW COMA SCALE
        if (!empty($data->pupil_size_chart) || !empty($data->motor_response) || !empty($data->verbal_response) || !empty($data->eye_response) || !empty($data->gsc_score)) {
            $this->titleHeader($pdf, "GLASGOW COMA SCALE");
        
            if (!empty($data->pupil_size_chart)) {
                $pdf->MultiCell(0, 7, "Pupil Size Chart:" . self::green($pdf, $data->pupil_size_chart, 'Pupil Size Chart'), 1, 'L');
            }
            if (!empty($data->motor_response)) {
                $pdf->MultiCell(0, 7, "Motor Response:" . self::green($pdf, $data->motor_response, 'Motor Response'), 1, 'L');
            }
            if (!empty($data->verbal_response)) {
                $pdf->MultiCell(0, 7, "Verbal Response:" . self::green($pdf, $data->verbal_response, 'Verbal Response'), 1, 'L');
            }
            if (!empty($data->eye_response)) {
                $pdf->MultiCell(0, 7, "Eye Response:" . self::green($pdf, $data->eye_response, 'Eye Response'), 1, 'L');
            }
            if (!empty($data->gsc_score)) {
                $pdf->MultiCell(0, 7, "GSC Score:" . self::green($pdf, $data->gsc_score, 'GSC Score'), 1, 'L');
            }
        }
        // REASON FOR REFERRAL
        if (!empty($data->reason['reason']) || !empty($data->other_reason_referral)) {
            $this->titleHeader($pdf, "REASON FOR REFERRAL");
            if(!empty($data->reason['reason'])){$pdf->MultiCell(0, 7, self::black($pdf, "Reason for referral: ") . "\n" . self::staticGreen($pdf, utf8_decode($data->reason['reason'])), 1, 'L');}
            else if(!empty($data->other_reason_referral)){$pdf->MultiCell(0, 7, self::black($pdf, "Other reason for referral: ") . "\n" . self::staticGreen($pdf, utf8_decode($data->other_reason_referral)), 1, 'L');}
        } 

        // OBSTETRIC AND GYNECOLOGIC     
            $pdf->ln(3);
            $pdf->SetFont('Arial', '', 12);
            $header = array(
                'Preg. Order', 'Year', 'Gestation Completed',
                'Preg. Outcome', 'Birthplace', 'Sex',
                'BW', 'Present Status', 'Complications'
            );

            $data_pregnancy = NewFormCtrl::fetchPregnant($patient_id);   
            
            $dataArray = [];
            foreach ($data_pregnancy as $row) {
                $dataArray[] = (array) $row;
            }
           
            $this->obstetricPage($pdf, $header, $dataArray, $data, $form_type);

    }

    public function printPregnant(
        $x,
        $data,
        $pdf,
        $referring_facility,
        $referred_to,
        $department,
        $tracking_data,
        $form_type,
        $patients_name,
        $patient_address,
        $woman_name,
        $referring_md_name
    ){
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 0, "BEmONC/ CEmONC REFERRAL FORM", 0, "", "C");
        $pdf->ln(10);
        if(!empty($tracking_data->code)){$pdf->MultiCell(0, 7, self::black($pdf, "Patient Code: ") . self::orange($pdf, $tracking_data->code, "Patient Code :"), 0, 'L');}
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 7, self::black($pdf, "REFERRAL RECORD"), 0, 'L');
        $pdf->SetFont('Arial', '', 10);

        $pdf->MultiCell($x / 4, 7, self::black($pdf, "Who is Referring"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY(60, $y - 7);

        if(!empty($data->pregnant_form->record_no)){
            $pdf->MultiCell($x / 4, 7, self::black($pdf, "Record Number: ") . self::orange($pdf, $data->pregnant_form->record_no, "Record Number:"), 0);
        }else {
            $pdf->MultiCell($x / 4, 7, self::black($pdf, "") . self::orange($pdf, null, ""), 0);
        }
        $y = $pdf->getY();
        $pdf->SetXY(125, $y - 7);
        if(!empty($data->pregnant_form->referred_date)){$pdf->MultiCell($x / 2, 7, self::black($pdf, "Referred Date: ") . self::orange($pdf, $data->pregnant_form->referred_date, "Referred Date:"), 0);}

        if(!empty($data->pregnant_form->md_referring)){$pdf->MultiCell($x / 2, 7, self::black($pdf, "Name of referring MD/HCW: ") . self::orange($pdf, utf8_decode($data->pregnant_form->md_referring), "Name of referring MD/HCW:"), 0, 'L');}
        $y = $pdf->getY();
        $pdf->SetXY(125, $y - 7);
        if(!empty($data->tracking_date->date_transferred)){
            if ($data->tracking_date->date_transferred !== '0000-00-00 00:00:00'){
              $pdf->MultiCell($x / 2, 7, self::black($pdf, "Arrival Date: ") . self::orange($pdf, $data->tracking_date->date_transferred, "Arrival Date:"), 0);
            }  
        }else {
            $pdf->MultiCell($x / 2, 7, self::black($pdf, "") . self::orange($pdf, null, ""), 0);
        }


        // dd($data->pregnant_form);
        if(!empty($data->pregnant_form->referring_contact)){$pdf->MultiCell(0, 7, self::black($pdf, "Contact # of referring MD/HCW: ") . "\n" . self::orange($pdf, $data->pregnant_form->referring_contact, "Contact # of referring MD/HCW:"), 0, 'L');}
        //-------------------------------------------------------------------------------------------------------
            
        if(!empty($data->pregnant_form->referring_facility)){$pdf->MultiCell(0, 7, self::black($pdf, "Facility: ") . self::orange($pdf, $data->pregnant_form->referring_facility, "Facility:"), 0, 'L');}
        if(!empty($data->pregnant_form->referring_contact)){$pdf->MultiCell(0, 7, self::black($pdf, "Facility Contact #: ") . self::orange($pdf, $data->pregnant_form->referring_contact, "Facility Contact #:"), 0, 'L');}
        if(!empty($data->pregnant_form->health_worker)){$pdf->MultiCell(0, 7, self::black($pdf, "Accompanied by the Health Worker: ") . self::orange($pdf, utf8_decode($data->pregnant_form->health_worker), "Accompanied by the Health Worker:"), 0, 'L');}

        if(!empty($data->pregnant_form->referred_facility)){$pdf->MultiCell ($x / 2, 7, self::black($pdf, "Referred To: ") . self::orange($pdf, $data->pregnant_form->referred_facility, "Referred To:"), 0, 'L');}
        if(!empty($data->pregnant_form->referred_contact)){$pdf->MultiCell ($x / 2, 7, self::black($pdf, "Referred Contact #: ") . self::orange($pdf, $data->pregnant_form->referred_contact, "Referred Contact #:"), 0, 'L');}
        $y = $pdf->getY();
        $pdf->SetXY($x / 2 + 40, $y - 7);
        if(!empty($department->description)){$pdf->MultiCell($x / 2, 7, self::black($pdf, "Department: ") . self::orange($pdf, $department->description, "Department:"), 0);}
     
        if(!empty($referred_to->address)){$pdf->MultiCell(0, 7, self::black($pdf, "Address: ") . self::orange($pdf, utf8_decode($referred_to->address), "Address:"), 0, 'L');}
        if(!empty($data->pregnant_form->covid_number)){$pdf->MultiCell(0, 7, self::black($pdf, "Covid Number: ") . self::orange($pdf, $data->pregnant_form->covid_number, "Covid Number: "), 0, 'L');}
        if(!empty($data->pregnant_form->refer_clinical_status)){$pdf->MultiCell(0, 7, self::black($pdf, "Clinical Status: ") . self::orange($pdf, $data->pregnant_form->refer_clinical_status, "Clinical Status: "), 0, 'L');}
        if(!empty($data->pregnant_form->refer_sur_category)){$pdf->MultiCell(0, 7, self::black($pdf, "Surveillance Category: ") . self::orange($pdf, $data->pregnant_form->refer_sur_category, "Surveillance Category: "), 0, 'L');}
        if(!empty($data->pregnant_form->dis_clinical_status)){$pdf->MultiCell(0, 7, self::black($pdf, "Discharge Clinical Status: ") . self::orange($pdf, $data->pregnant_form->dis_clinical_status, "Discharge Clinical Status: "), 0, 'L');}
        if(!empty($data->pregnant_form->dis_sur_category)){$pdf->MultiCell(0, 7, self::black($pdf, "Discharge Surveillance Category: ") . self::orange($pdf, $data->pregnant_form->dis_sur_category, "Discharge Surveillance Category: "), 0, 'L');}
        $pdf->Ln(3);

        

        $pdf->SetFillColor(200, 200, 200);
        $pdf->SetTextColor(30);
        $pdf->SetDrawColor(200);
        $pdf->SetLineWidth(.3);

        if(!empty($woman_name) || !empty($patients_name->dob) || !empty($patient_address) || !empty($data->pregnant_form->woman_reason) || !empty($data->pregnant_form->woman_major_findings)){
            $this->titleHeader($pdf, "WOMAN");

            $pdf->SetFillColor(255, 250, 205);
            $pdf->SetTextColor(40);
            $pdf->SetDrawColor(230);
            $pdf->SetLineWidth(.3);
            
            if(!empty($woman_name)){$pdf->MultiCell(0, 7, "Name: " . self::green($pdf, utf8_decode($woman_name), 'name'), 1, 'L');}
            if(!empty($patients_name->dob)){$pdf->MultiCell(0, 7, "Age: " . self::green($pdf, NewFormCtrl::calculateAge($patients_name->dob), 'Age'), 1, 'L');}
            if(!empty($patient_address)){$pdf->MultiCell(0, 7, "Address: " . self::green($pdf, utf8_decode($patient_address), 'Address'), 1, 'L');}
            if(!empty($data->pregnant_form->woman_reason)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Main Reason for Referral: ") . "\n" . self::staticGreen($pdf, $data->pregnant_form->woman_reason), 1, 'L');}
            if(!empty($data->pregnant_form->woman_major_findings)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Major Findings (Clinica and BP,Temp,Lab) : ") . "\n" . self::staticGreen($pdf, utf8_decode($data->pregnant_form->woman_major_findings)), 1, 'L');}
        }
        
        $pdf->SetFillColor(200, 200, 200);
        $pdf->SetTextColor(30);
        $pdf->SetDrawColor(200);
        $pdf->SetLineWidth(.3);
        
      
        if(!empty($data->pregnant_form->woman_before_treatment) || !empty($data->pregnant_form->woman_before_given_time) || !empty($data->pregnant_form->woman_information_given)){
            $this->titleHeader($pdf, "Treatments Give Time");

            $pdf->SetFillColor(255, 250, 205);
            $pdf->SetTextColor(40);
            $pdf->SetDrawColor(230);
            $pdf->SetLineWidth(.3);
            // dd($data);
            if(!empty($data->pregnant_form->woman_before_treatment)){$pdf->MultiCell(0, 7, "Before Referral: " . self::green($pdf, $data->pregnant_form->woman_before_treatment . '-' . $data->pregnant_form->woman_before_given_time, 'Before Referral'), 1, 'L');}
            if(!empty($data->pregnant_form->woman_before_given_time)){$pdf->MultiCell(0, 7, "During Transport: " . self::green($pdf, $data->pregnant_form->woman_during_transport . '-' . $data->pregnant_form->woman_before_given_time, 'During Transport'), 1, 'L');}
            if(!empty($data->pregnant_form->woman_information_given)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Information Given to the Woman and Companion About the Reason for Referral : ") . "\n" . self::staticGreen($pdf, utf8_decode($data->pregnant_form->woman_information_given)), 1, 'L');}
        }
       
        
            $pdf->Ln(3);

            $pdf->SetFillColor(200, 200, 200);
            $pdf->SetTextColor(30);
            $pdf->SetDrawColor(200);
            $pdf->SetLineWidth(.3);

            // dd($data->baby_data);
            // $pdf->addPage();
           if ($form_type === 'pregnant') {
            if(!empty($data->baby_data->baby_name) || !empty($data->baby_data->baby_dob) || !empty($data->baby_data->weight) || !empty($data->baby_data->gestational_age) || !empty($data->baby_data->baby_reason) 
            || !empty($data->baby_data->baby_major_findings) || !empty($data->baby_data->baby_last_feed)){
                $this->titleHeader($pdf, "BABY");

                $pdf->SetFillColor(255, 250, 205);
                $pdf->SetTextColor(40);
                $pdf->SetDrawColor(230);
                $pdf->SetLineWidth(.3);
    
                if(!empty($data->baby_data->baby_name)){$pdf->MultiCell(0, 7, "Name: " . self::green($pdf, utf8_decode($data->baby_data->baby_name), 'name'), 1, 'L');}
                if(!empty($data->baby_data->baby_dob)){$pdf->MultiCell(0, 7, "Date of Birth: " . self::green($pdf, $data->baby_data->baby_dob, "Date of Birth"), 1, 'L');}
                if(!empty($data->baby_data->weight)){$pdf->MultiCell(0, 7, "Body Weight: " . self::green($pdf, $data->baby_data->weight, 'body weight'), 1, 'L');}
                if(!empty($data->baby_data->gestational_age)){$pdf->MultiCell(0, 7, "Gestational Age: " . self::green($pdf, $data->baby_data->gestational_age, 'Gestational Age'), 1, 'L');}
                if(!empty($data->baby_data->baby_reason)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Main Reason for Referral: ") . "\n" . self::staticGreen($pdf, $data->baby_data->baby_reason), 1, 'L');}
                if(!empty($data->baby_data->baby_major_findings)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Major Findings (Clinica and BP,Temp,Lab) : ") . "\n" . self::staticGreen($pdf, utf8_decode($data->baby_data->baby_major_findings)), 1, 'L');}
                if(!empty($data->baby_data->baby_last_feed)){$pdf->MultiCell(0, 7, "Last (Breast) Feed (Time): " . self::green($pdf, $data->baby_data->baby_last_feed, "Last (Breast) Feed (Time)"), 1, 'L');}
            }
           }
           
            
            $pdf->SetFillColor(200, 200, 200);
            $pdf->SetTextColor(30);
            $pdf->SetDrawColor(200);
            $pdf->SetLineWidth(.3);

            if(!empty($data->baby_data->baby_before_treatment) || !empty($data->baby_data->baby_during_transport) || !empty($data->baby_data->baby_information_given) || !empty($data->baby_data->baby_before_given_time)){
                $pdf->MultiCell(0, 7, "Treatments Give Time", 1, 'L', true);

                $pdf->SetFillColor(255, 250, 205);
                $pdf->SetTextColor(40);
                $pdf->SetDrawColor(230);
                $pdf->SetLineWidth(.3);
    
                if(!empty($data->baby_data->baby_before_treatment)){$pdf->MultiCell(0, 7, "Before Referral: " . self::green($pdf, $data->baby_data->baby_before_treatment . '-' . $data->baby_data->baby_before_given_time, 'Before Referral'), 1, 'L');}
                if(!empty($data->baby_data->baby_during_transport)){$pdf->MultiCell(0, 7, "During Transport: " . self::green($pdf, $data->baby_data->baby_during_transport . '-' . $data->baby_data->baby_transport_given_time, 'During Transport'), 1, 'L');}
                if(!empty($data->baby_data->baby_information_given)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Information Given to the Woman and Companion About the Reason for Referral : ") . "\n" . self::staticGreen($pdf, utf8_decode($data->baby_data->baby_information_given)), 1, 'L');}
                                
            }  
    }

    public function printNormal($pdf, 
    $x, 
    $patient_address, 
    $woman_name, 
    $tracking_data, 
    $referring_facility_normal, 
    $referred_to_normal, 
    $department_normal, 
    $patients_form, 
    $patients_name, 
    $data){
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 0, "CENTRAL VISAYAS HEALTH REFERRAL SYSTEM", 0, "", "C");
            $pdf->ln();
            $pdf->Cell(0, 12, "Clinical Referral Form", 0, "", "C");
            $pdf->Ln(20);
            $pdf->SetFont('Arial', '', 10);
           if(!empty($tracking_data->code)){$pdf->MultiCell(0, 7, self::black($pdf, "Patient Code: ") . self::orange($pdf,  $tracking_data->code, "Patient Code :"), 0, 'L');}
            $pdf->Ln(5);
           if(!empty($referring_facility_normal->name)){$pdf->MultiCell(0, 7, self::black($pdf, "Name of Referring Facility: ") . self::orange($pdf, utf8_decode($referring_facility_normal->name), "Name of Referring Facility:"), 0, 'L');}
           if(!empty($referring_facility_normal->contact)){$pdf->MultiCell(0, 7, self::black($pdf, "Facility Contact #: ") . self::orange($pdf, $referring_facility_normal->contact, "Facility Contact #:"), 0, 'L');}
           if(!empty($referring_facility_normal->address)){$pdf->MultiCell(0, 7, self::black($pdf, "Address: ") . self::orange($pdf, utf8_decode($referring_facility_normal->address), "Address:"), 0, 'L');}
    
           if(!empty($referred_to_normal->name)){$pdf->MultiCell($x / 2, 7, self::black($pdf, "Referred to: ") . self::orange($pdf, utf8_decode($referred_to_normal->name), "Referred to:"), 0, 'L');}
            $y = $pdf->getY();
            $pdf->SetXY($x / 2 + 10, $y - 7);
           if(!empty($department_normal->description)){$pdf->MultiCell($x / 2, 7, self::black($pdf, "Department: ") . self::orange($pdf, utf8_decode($department_normal->description), "Department:"), 0);}
    
            if(!empty($referred_to_normal->address)){$pdf->MultiCell(0, 7, self::black($pdf, "Address: ") . self::orange($pdf, $referred_to_normal->address, "Address:"), 0, 'L');}
        
            if(!empty($patients_form->time_referred)){$pdf->MultiCell($x / 2, 7, self::black($pdf, "Date/Time Referred (ReCo): ") . self::orange($pdf, utf8_decode($patients_form->time_referred), "Date/Time Referred (ReCo):"), 0, 'L');}
            $y = $pdf->getY();
            $pdf->SetXY($x / 2 + 10, $y - 7);
            
            if(!empty($tracking_data->date_transferred)){
                if ($tracking_data->date_transferred !== '0000-00-00 00:00:00'){$pdf->MultiCell($x / 2, 7, self::black($pdf, "Date/Time Transferred: ") . self::orange($pdf, $tracking_data->date_transferred, "Date/Time Transferred:"), 0);} 
            }else{
                $pdf->MultiCell($x / 2, 7, self::black($pdf, "Date/Time Transferred: ") . self::orange($pdf, null, "Date/Time Transferred:"), 0);
            }
    
            // $pdf->MultiCell($x / 2, 7, self::black($pdf, "Name of Patient: ") . "\n" . self::orange($pdf, $woman_name, "Name of Patient:"), 0, 'L');
            if(!empty($woman_name)){$pdf->MultiCell(0, 7, self::black($pdf, "Name of Patient: ") . "\n" . self::staticGreen($pdf, utf8_decode($woman_name)), 0, 'L');}
            $y = $pdf->getY();
            $pdf->SetXY($x / 2 + 10, $y - 7);
    
            if(!empty($patients_name->dob)){$pdf->MultiCell($x / 2, 7, self::black($pdf, "Age: ") . self::orange($pdf, NewFormCtrl::calculateAge($patients_name->dob), "age:"), 0);}
    
            $y = $pdf->getY();
            $pdf->SetXY(($x / 2) + ($x / 4) - 5, $y - 7);
            if(!empty($patients_name->sex)){$pdf->MultiCell($x / 4, 7, self::black($pdf, "Sex: ") . self::orange($pdf, $patients_name->sex, "sex:"), 0);}
            $y = $pdf->getY();
            $pdf->SetXY(($x / 2) + ($x / 2) - 30, $y - 7);
            if(!empty($patients_name->civil_status)){$pdf->MultiCell($x / 4, 7, self::black($pdf, "Status: ") . self::orange($pdf, $patients_name->civil_status, "Status:"), 0);}
    
            if(!empty($patient_address)){$pdf->MultiCell(0, 7, self::black($pdf, "Address: ") . self::orange($pdf, utf8_decode($patient_address), "address:"), 0, 'L');}

            if(!empty( $patients_name->phic_status)){$pdf->MultiCell($x / 2, 7, self::black($pdf, "PhilHealth Status: ") . self::orange($pdf, $patients_name->phic_status, "PhilHealth status:"), 0, 'L');}
            $y = $pdf->getY();
            $pdf->SetXY($x / 2 + 10, $y - 7);
            if(!empty($patients_name->phic_id)){$pdf->MultiCell($x / 2, 7, self::black($pdf, "PhilHealth # : ") . self::orange($pdf, $patients_name->phic_id, "PhilHealth # :"), 0);}
    
    
            if(!empty($patients_form->covid_number)){$pdf->MultiCell(0, 7, self::black($pdf, "Covid Number: ") . self::orange($pdf, $patients_form->covid_number, "Covid Number: "), 0, 'L');}
            if(!empty($patients_form->refer_clinical_status)){$pdf->MultiCell(0, 7, self::black($pdf, "Clinical Status: ") . self::orange($pdf, $patients_form->refer_clinical_status, "Clinical Status: "), 0, 'L');}
            if(!empty($patients_form->refer_sur_category)){$pdf->MultiCell(0, 7, self::black($pdf, "Surveillance Category: ") . self::orange($pdf, $patients_form->refer_sur_category, "Surveillance Category: "), 0, 'L');}
            if(!empty($patients_form->dis_clinical_status)){$pdf->MultiCell(0, 7, self::black($pdf, "Discharge Clinical Status: ") . self::orange($pdf, $patients_form->dis_clinical_status, "Discharge Clinical Status: "), 0, 'L');}
            if(!empty($patients_form->dis_sur_category)){$pdf->MultiCell(0, 7, self::black($pdf, "Discharge Surveillance Category: ") . self::orange($pdf, $patients_form->dis_sur_category, "Discharge Surveillance Category: "), 0, 'L');}
            $pdf->Ln();
            $pdf->MultiCell(0, 7, self::black($pdf, "Case Summary (pertinent Hx/PE, including meds, labs, course etc.): "), 0, 'L');
            $pdf->SetTextColor(102, 56, 0);
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->MultiCell(0, 5, utf8_decode($data->case_summary), 0, 'L');
            $pdf->Ln();
    
    
            $pdf->MultiCell(0, 7, self::black($pdf, "Summary of ReCo (pls. refer to ReCo Guide in Referring Patients Checklist): "), 0, 'L');
            $pdf->SetTextColor(102, 56, 0);
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->MultiCell(0, 5, utf8_decode($data->reco_summary), 0, 'L');
            $pdf->Ln();
 
            $data_md_referring = []; 
            $data_md_contact = [];
            $data_md_referred_contact=[];
            // Loop through the array and collect data
            foreach ($data->form as $data2) {
                $data_md_referring[] = $data2->md_referring; // Store each md_referring in the array
                $data_md_contact[] = $data2->referring_contact;
                $data_md_referred_contact[] = $data2->referred_contact;
            }
           
            $pdf->MultiCell($x / 2, 7, self::black($pdf, "Name of referring MD/HCW: ") . self::orange($pdf, utf8_decode($data_md_referring[0]), "Name of referring MD/HCW:"), 0, 'L');
            $pdf->MultiCell($x / 2, 7, self::black($pdf, "Contact # of referring MD/HCW: ") . self::orange($pdf, $data_md_contact[0], "Contact # of referring MD/HCW:"), 0, 'L');
            $pdf->MultiCell($x / 2, 7, self::black($pdf, "Name of referred MD/HCW- Mobile Contact # (ReCo): ") . self::orange($pdf, $data_md_referred_contact[0], "Name of referred MD/HCW- Mobile Contact # (ReCo):"), 0, 'L');
            
            
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

    private function obstetricPage($pdf, $header, $data, $data_, $form_type)
    {
        $pdf->SetMargins(6.35, 6.35, 6.35);
        $contraceptive_other = $data->contraceptive_history;
        $other_explodedData = explode(',', $contraceptive_other);
        $others = ['Other'];

        $other_dataArray = NewFormCtrl::mapExplodedDataToArray($other_explodedData, $others);

        if (!empty($data_->menarche) || !empty($data_->menopause) || !empty($data_->menopausal_age) || !empty($data_->menstrual_cycle) || !empty( $data_->mens_irreg_xmos)
        || !empty($data_->menstrual_cycle_dysmenorrhea) || !empty($data_->menstrual_cycle_duration) || !empty($data_->menstrual_cycle_padsperday) || !empty($data_->menstrual_cycle_medication)
        || !empty($data_->contraceptive_history) || !empty($data_->contraceptive_others) || !empty($data_->parity_g) || !empty($data_->parity_p) || !empty($data_->parity_ft) || !empty($data_->parity_pt)
        || !empty($data_->parity_a) || !empty($data_->parity_l) || !empty($data_->parity_lnmp) || !empty($data_->parity_edc) || !empty($data_->aog_eutz) || !empty($data_->prenatal_history)){
            
            $this->titleHeader($pdf, "OBSTETRIC AND GYNECOLOGIC HISTORY");
            if (!empty($data_->menarche)){$pdf->MultiCell(0, 7, "Menarche:" . self::green($pdf, $data_->menarche, 'Menarche'), 1, 'L');}
            if ($data_->menopause) {
                if (!empty($data_->menopausal_age)){$pdf->MultiCell(0, 7, "Menopausal Age:" . self::green($pdf, $data_->menopausal_age, 'Menopausal Age'), 1, 'L');}
            }
            if ($data_->menstrual_cycle == "irregular") {
               if(!empty($data_->menstrual_cycle)){$pdf->MultiCell(0, 7, "Menstrual Cycle:" . self::green($pdf, $data_->menstrual_cycle, 'Menstrual Cycle'), 1, 'L');}
               if(!empty($data_->mens_irreg_xmos)){$pdf->MultiCell(0, 7, "Irregular x mos:" . self::green($pdf, $data_->mens_irreg_xmos, 'Irregular x mos'), 1, 'L');}
            } else if ($data_->menstrual_cycle == "regular") {
               if(!empty($data_->menstrual_cycle)){$pdf->MultiCell(0, 7, "Menstrual Cycle:" . self::green($pdf, $data_->menstrual_cycle, 'Menstrual Cycle'), 1, 'L');}
            }
            if(!empty($data_->menstrual_cycle_dysmenorrhea)){$pdf->MultiCell(0, 7, "Dysmenorrhea:" . self::green($pdf, $data_->menstrual_cycle_dysmenorrhea, 'Dysmenorrhea'), 1, 'L');}
            if(!empty($data_->menstrual_cycle_duration)){$pdf->MultiCell(0, 7, "Duration:" . self::green($pdf, $data_->menstrual_cycle_duration, 'Duration'), 1, 'L');}
            if(!empty($data_->menstrual_cycle_padsperday)){$pdf->MultiCell(0, 7, "Pads per day:" . self::green($pdf, $data_->menstrual_cycle_padsperday, 'Pads per day'), 1, 'L');}
            if(!empty($data_->menstrual_cycle_medication)){$pdf->MultiCell(0, 7, "Medication:" . self::green($pdf, $data_->menstrual_cycle_medication, 'Medication'), 1, 'L');}
            if(!empty($data_->contraceptive_history)){$pdf->MultiCell(0, 7, "Contraceptive History:" . self::green($pdf, $data_->contraceptive_history, 'contraceptive History'), 1, 'L');}
            if(!empty($other_dataArray['Other'])) {$pdf->MultiCell(0, 7, "Contraceptive Others:" . self::green($pdf, $data_->contraceptive_others, 'contraceptive Others'), 1, 'L');}
            if(!empty($data_->parity_g)){$pdf->MultiCell(0, 7, "Parity G:" . self::green($pdf, $data_->parity_g, 'Parity G'), 1, 'L');}
            if(!empty($data_->parity_p)){$pdf->MultiCell(0, 7, "Parity P:" . self::green($pdf, $data_->parity_p, 'Parity P'), 1, 'L');}
            if(!empty($data_->parity_ft)){$pdf->MultiCell(0, 7, "Parity FT:" . self::green($pdf, $data_->parity_ft, 'Parity FT'), 1, 'L');}
            if(!empty($data_->parity_pt)){$pdf->MultiCell(0, 7, "Parity PT:" . self::green($pdf, $data_->parity_pt, 'Parity PT'), 1, 'L');}
            if(!empty($data_->parity_a)){$pdf->MultiCell(0, 7, "Parity A:" . self::green($pdf, $data_->parity_a, 'Parity A'), 1, 'L');}
            if(!empty($data_->parity_l)){$pdf->MultiCell(0, 7, "Parity l:" . self::green($pdf, $data_->parity_l, 'Parity l'), 1, 'L');}
            $decoded_LMP = utf8_decode($data_->parity_lnmp);
            $decoded_EDC = utf8_decode($data_->parity_edc);
            $formatted_LMP = date('F d, Y h:i A', strtotime($decoded_LMP));
            $formatted_EDC = date('F d, Y h:i A', strtotime($decoded_EDC));
            if(!empty($data_->parity_lnmp)){$pdf->MultiCell(0, 7, "Parity LMP:" . self::green($pdf, $formatted_LMP, 'Parity LMP'), 1, 'L');}
            if(!empty($data_->parity_edc)){$pdf->MultiCell(0, 7, "Parity EDC:" . self::green($pdf, $formatted_EDC, 'Parity EDC'), 1, 'L');}
            if(!empty($data_->aog_eutz)){$pdf->MultiCell(0, 7, "UTZ:" . self::green($pdf, $data_->aog_eutz, 'UTZ'), 1, 'L');}
            if(!empty($data_->prenatal_history)){$pdf->MultiCell(0, 7, self::staticBlack($pdf, "Prenatal History: ") . "\n" . self::staticGreen($pdf, NewFormCtrl::explodeString($data_->prenatal_history)), 1, 'L');}

            $pdf->SetMargins(6.35, 6.35, 6.35);
            $pdf->ln(5);
            // dd($data_);
            
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