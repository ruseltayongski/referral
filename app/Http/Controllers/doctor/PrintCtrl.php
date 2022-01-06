<?php

namespace App\Http\Controllers\doctor;

use Anouar\Fpdf\Fpdf;
use App\Tracking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\doctor\ReferralCtrl;
use Illuminate\Support\Facades\Session;

class PrintCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //this->middleware('doctor');
    }

    public function printReferral($track_id)
    {
        $user = Session::get('auth');
        $form_type = Tracking::where('id',$track_id)
            ->where(function($q) use($user) {
                $q->where('referred_from',$user->facility_id)
                    ->orwhere('referred_to',$user->facility_id);
            })
            ->first();
        $form_status = $form_type->status;
        if($form_type){
            $form_type = $form_type->type;
        }else{
            return redirect('doctor');
        }

        if($form_type=='normal')
        {
            $data = ReferralCtrl::normalForm($track_id, $form_status, $form_type);
            return self::printNormal($data->form, $data);
        }else if($form_type=='pregnant'){
            $data = ReferralCtrl::pregnantForm($track_id, $form_status);
            return self::printPregnant($data);
        }
    }

    public function printPregnant($record)
    {

        $data = $record->form["pregnant"];
        $baby = $record->form["baby"];
        //print_r($baby);
        $pdf = new Fpdf();
        $x = ($pdf->w)-20;

        $patient_address='';
        $referred_address= '';

        $patient_address .= ($data->patient_brgy) ? $data->patient_brgy.', ': '';
        $patient_address .= ($data->patient_muncity) ? $data->patient_muncity.', ': '';
        $patient_address .= ($data->patient_province) ? $data->patient_province: '';

        $referred_address .= ($data->ff_brgy) ? $data->ff_brgy.', ': '';
        $referred_address .= ($data->ff_muncity) ? $data->ff_muncity.', ': '';
        $referred_address .= ($data->ff_province) ? $data->ff_province: '';

        $pdf->setTopMargin(17);
        $pdf->setTitle($data->woman_name);
        $pdf->addPage();

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,0,"BEmONC/ CEmONC REFERRAL FORM",0,"","C");
        $pdf->ln(10);
        $pdf->MultiCell(0, 7, self::black($pdf,"Patient Code: ").self::orange($pdf,$data->code,"Patient Code :"), 0, 'L');
        $pdf->Ln(5);
        $pdf->SetFont('Arial','',10);
        $pdf->MultiCell(0, 7, self::black($pdf,"REFERRAL RECORD"), 0, 'L');
        $pdf->SetFont('Arial','',10);

        $pdf->MultiCell($x/4, 7, self::black($pdf,"Who is Referring"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY(60, $y-7);
        $pdf->MultiCell($x/4, 7, self::black($pdf,"Record Number: ").self::orange($pdf,$data->record_no,"Record Number:"), 0);
        $y = $pdf->getY();
        $pdf->SetXY(125, $y-7);
        $pdf->MultiCell($x/2, 7, self::black($pdf,"Referred Date: ").self::orange($pdf,$data->referred_date,"Referred Date:"), 0);

        $pdf->MultiCell($x/2, 7, self::black($pdf,"Name of referring MD/HCW: ").self::orange($pdf,$data->md_referring,"Name of referring MD/HCW:"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY(125, $y-7);
        $pdf->MultiCell($x/2, 7, self::black($pdf,"Arrival Date: ").self::orange($pdf,$data->arrival_date,"Arrival Date:"), 0);

        $pdf->MultiCell(0, 7, self::black($pdf,"Contact # of referring MD/HCW: ").self::orange($pdf,$data->referring_md_contact,"Contact # of referring MD/HCW:"), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Facility: ").self::orange($pdf,$data->referring_facility,"Facility:"), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Facility Contact #: ").self::orange($pdf,$data->referring_contact,"Facility Contact #:"), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Accompanied by the Health Worker: ").self::orange($pdf,$data->health_worker,"Accompanied by the Health Worker:"), 0, 'L');

        $pdf->MultiCell($x/2, 7, self::black($pdf,"Referred To: ").self::orange($pdf,$data->referred_facility,"Referred To:"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x/2+40, $y-7);
        $pdf->MultiCell($x/2, 7, self::black($pdf,"Department: ").self::orange($pdf,$data->department,"Department:"), 0);

        $pdf->MultiCell(0, 7, self::black($pdf,"Address: ").self::orange($pdf,$referred_address,"Address:"), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Covid Number: ").self::orange($pdf,$data->covid_number,"Covid Number: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Clinical Status: ").self::orange($pdf,$data->refer_clinical_status,"Clinical Status: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Surveillance Category: ").self::orange($pdf,$data->refer_sur_category,"Surveillance Category: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Discharge Clinical Status: ").self::orange($pdf,$data->dis_clinical_status,"Discharge Clinical Status: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Discharge Surveillance Category: ").self::orange($pdf,$data->dis_sur_category,"Discharge Surveillance Category: "), 0, 'L');
        $pdf->Ln(3);

        $pdf->SetFillColor(200,200,200);
        $pdf->SetTextColor(30);
        $pdf->SetDrawColor(200);
        $pdf->SetLineWidth(.3);

        $pdf->MultiCell(0, 7, "WOMAN", 1, 'L',true);

        $pdf->SetFillColor(255,250,205);
        $pdf->SetTextColor(40);
        $pdf->SetDrawColor(230);
        $pdf->SetLineWidth(.3);

        $pdf->MultiCell(0, 7, "Name: " .self::green($pdf,$data->woman_name,'name'), 1, 'L');
        $pdf->MultiCell(0, 7, "Age: " .self::green($pdf,$data->woman_age,'Age'), 1, 'L');
        $pdf->MultiCell(0, 7, "Address: " .self::green($pdf,$data->patient_address,'Address'), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf,"Main Reason for Referral: ")."\n".self::staticGreen($pdf,$data->woman_reason), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf,"Major Findings (Clinica and BP,Temp,Lab) : ")."\n".self::staticGreen($pdf,$data->woman_major_findings), 1, 'L');

        $pdf->SetFillColor(200,200,200);
        $pdf->SetTextColor(30);
        $pdf->SetDrawColor(200);
        $pdf->SetLineWidth(.3);

        $pdf->MultiCell(0, 7, "Treatments Give Time", 1, 'L',true);

        $pdf->SetFillColor(255,250,205);
        $pdf->SetTextColor(40);
        $pdf->SetDrawColor(230);
        $pdf->SetLineWidth(.3);

        $pdf->MultiCell(0, 7, "Before Referral: " .self::green($pdf,$data->woman_before_treatment.'-'.$data->woman_before_given_time,'Before Referral'), 1, 'L');
        $pdf->MultiCell(0, 7, "During Transport: " .self::green($pdf,$data->woman_before_given_time.'-'.$data->woman_before_given_time,'During Transport'), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf,"Information Given to the Woman and Companion About the Reason for Referral : ")."\n".self::staticGreen($pdf,$data->woman_information_given), 1, 'L');

        if(isset($record->icd)) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Diagnosis/Impression: "), 1, 'L');
            foreach($record->icd as $icd) {
                $pdf->MultiCell(0, 5, self::staticGreen($pdf, $icd->code." - ".$icd->description), 0, 'L');
            }
        }

        if(isset($data->notes_diagnoses)) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Additional notes in diagnosis: "), 1, 'L');
            $pdf->MultiCell(0, 5, self::staticGreen($pdf, $data->notes_diagnoses), 1, 'L');
        }

        if(isset($data->other_diagnoses)) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Other diagnosis: ")."\n".self::staticGreen($pdf, $data->other_diagnoses), 1, 'L');
        }

        if(isset($record->reason)) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Reason for referral: ")."\n".self::staticGreen($pdf, $record->reason), 1, 'L');
        }

        if(isset($data->other_reason_referral)) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Reason for referral: ")."\n".self::staticGreen($pdf, $data->other_reason_referral), 1, 'L');
        }

        if(count($baby)>0)
        {
            $pdf->Ln(8);

            $pdf->SetFillColor(200,200,200);
            $pdf->SetTextColor(30);
            $pdf->SetDrawColor(200);
            $pdf->SetLineWidth(.3);

            $pdf->MultiCell(0, 7, "BABY", 1, 'L',true);

            $pdf->SetFillColor(255,250,205);
            $pdf->SetTextColor(40);
            $pdf->SetDrawColor(230);
            $pdf->SetLineWidth(.3);

            $pdf->MultiCell(0, 7, "Name: " .self::green($pdf,$baby->baby_name,'name'), 1, 'L');
            $pdf->MultiCell(0, 7, "Date of Birth: " .self::green($pdf,$baby->baby_dob,"Date of Birth"), 1, 'L');
            $pdf->MultiCell(0, 7, "Body Weight: " .self::green($pdf,$baby->weight,'body weight'), 1, 'L');
            $pdf->MultiCell(0, 7, "Gestational Age: " .self::green($pdf,$baby->gestational_age,'Gestational Age'), 1, 'L');
            $pdf->MultiCell(0, 7, self::staticBlack($pdf,"Main Reason for Referral: ")."\n".self::staticGreen($pdf,$baby->baby_reason), 1, 'L');
            $pdf->MultiCell(0, 7, self::staticBlack($pdf,"Major Findings (Clinica and BP,Temp,Lab) : ")."\n".self::staticGreen($pdf,$baby->baby_major_findings), 1, 'L');
            $pdf->MultiCell(0, 7, "Last (Breast) Feed (Time): " .self::green($pdf,$baby->baby_last_feed,"Last (Breast) Feed (Time)"), 1, 'L');

            $pdf->SetFillColor(200,200,200);
            $pdf->SetTextColor(30);
            $pdf->SetDrawColor(200);
            $pdf->SetLineWidth(.3);

            $pdf->MultiCell(0, 7, "Treatments Give Time", 1, 'L',true);

            $pdf->SetFillColor(255,250,205);
            $pdf->SetTextColor(40);
            $pdf->SetDrawColor(230);
            $pdf->SetLineWidth(.3);

            $pdf->MultiCell(0, 7, "Before Referral: " .self::green($pdf,$baby->baby_before_treatment.'-'.$baby->baby_before_given_time,'Before Referral'), 1, 'L');
            $pdf->MultiCell(0, 7, "During Transport: " .self::green($pdf,$baby->baby_during_transport.'-'.$baby->baby_transport_given_time,'During Transport'), 1, 'L');
            $pdf->MultiCell(0, 7, self::staticBlack($pdf,"Information Given to the Woman and Companion About the Reason for Referral : ")."\n".self::staticGreen($pdf,$baby->baby_information_given), 1, 'L');
        }
        $pdf->Output();
        exit();
    }

    public function printNormal($data, $data2)
    {
       //print_r($data);
        $address='';
        $patient_address='';
        $referred_address = '';

        $address .= ($data->facility_brgy) ? $data->facility_brgy .', ': '';
        $address .= ($data->facility_muncity) ? $data->facility_muncity .', ': '';
        $address .= ($data->facility_province) ? $data->facility_province: '';

        $referred_address .= ($data->ff_brgy) ? $data->ff_brgy .', ': '';
        $referred_address .= ($data->ff_muncity) ? $data->ff_muncity .', ': '';
        $referred_address .= ($data->ff_province) ? $data->ff_province: '';

        $patient_address .= ($data->patient_brgy) ? $data->patient_brgy.', ': '';
        $patient_address .= ($data->patient_muncity) ? $data->patient_muncity.', ': '';
        $patient_address .= ($data->patient_province) ? $data->patient_province: '';


        $pdf = new Fpdf();
        $x = ($pdf->w)-20;

        $pdf->setTopMargin(17);
        $pdf->setTitle($data->patient_name);
        $pdf->addPage();

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,0,"CENTRAL VISAYAS HEALTH REFERRAL SYSTEM",0,"","C");
        $pdf->ln();
        $pdf->Cell(0,12,"Clinical Referral Form",0,"","C");
        $pdf->Ln(20);
        $pdf->SetFont('Arial','',10);
        $pdf->MultiCell(0, 7, self::black($pdf,"Patient Code: ").self::orange($pdf,$data->code,"Patient Code :"), 0, 'L');
        $pdf->Ln(5);
        $pdf->MultiCell(0, 7, self::black($pdf,"Name of Referring Facility: ").self::orange($pdf,$data->referring_name,"Name of Referring Facility:"), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Facility Contact #: ").self::orange($pdf,$data->referring_contact,"Facility Contact #:"), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Address: ").self::orange($pdf,$address,"Address:"), 0, 'L');


        $pdf->MultiCell($x/2, 7, self::black($pdf,"Referred to: ").self::orange($pdf,$data->referred_name,"Referred to:"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x/2+10, $y-7);
        $pdf->MultiCell($x/2, 7, self::black($pdf,"Department: ").self::orange($pdf,$data->department,"Department:"), 0);

        $pdf->MultiCell(0, 7, self::black($pdf,"Address: ").self::orange($pdf,$referred_address,"Address:"), 0, 'L');

        $pdf->MultiCell($x/2, 7, self::black($pdf,"Date/Time Referred (ReCo): ").self::orange($pdf,$data->time_referred,"Date/Time Referred (ReCo):"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x/2+10, $y-7);
        $pdf->MultiCell($x/2, 7, self::black($pdf,"Date/Time Transferred: ").self::orange($pdf,$data->time_transferred,"Date/Time Transferred:"), 0);

        $pdf->MultiCell($x/2, 7, self::black($pdf,"Name of Patient: ").self::orange($pdf,$data->patient_name,"Name of Patient:"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x/2+10, $y-7);
        $pdf->MultiCell($x/4, 7, self::black($pdf,"Age: ").self::orange($pdf,$data->patient_age,"age:"), 0);
        $y = $pdf->getY();
        $pdf->SetXY(($x/2)+($x/4) - 15, $y-7);
        $pdf->MultiCell($x/4, 7, self::black($pdf,"Sex: ").self::orange($pdf,$data->patient_sex,"sex:"), 0);
        $y = $pdf->getY();
        $pdf->SetXY(($x/2)+($x/2) - 30, $y-7);
        $pdf->MultiCell($x/4, 7, self::black($pdf,"Status: ").self::orange($pdf,$data->patient_status,"Status:"), 0);

        $pdf->MultiCell(0, 7, self::black($pdf,"Address: ").self::orange($pdf,$patient_address,"address:"), 0, 'L');

        $pdf->MultiCell($x/2, 7, self::black($pdf,"PhilHealth Status: ").self::orange($pdf,$data->phic_status,"PhilHealth status:"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x/2+10, $y-7);
        $pdf->MultiCell($x/2, 7, self::black($pdf,"PhilHealth # : ").self::orange($pdf,$data->phic_id,"PhilHealth # :"), 0);


        $pdf->MultiCell(0, 7, self::black($pdf,"Covid Number: ").self::orange($pdf,$data->covid_number,"Covid Number: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Clinical Status: ").self::orange($pdf,$data->refer_clinical_status,"Clinical Status: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Surveillance Category: ").self::orange($pdf,$data->refer_sur_category,"Surveillance Category: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Discharge Clinical Status: ").self::orange($pdf,$data->dis_clinical_status,"Discharge Clinical Status: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf,"Discharge Surveillance Category: ").self::orange($pdf,$data->dis_sur_category,"Discharge Surveillance Category: "), 0, 'L');

        $pdf->MultiCell(0, 7, self::black($pdf,"Case Summary (pertinent Hx/PE, including meds, labs, course etc.): "), 0, 'L');
        $pdf->SetTextColor(102,56,0);
        $pdf->SetFont('Arial','I',10);
        $pdf->MultiCell(0, 5, $data->case_summary, 0, 'L');
        $pdf->Ln();

        $pdf->MultiCell(0, 7, self::black($pdf,"Summary of ReCo (pls. refer to ReCo Guide in Referring Patients Checklist): "), 0, 'L');
        $pdf->SetTextColor(102,56,0);
        $pdf->SetFont('Arial','I',10);
        $pdf->MultiCell(0, 5, $data->reco_summary, 0, 'L');
        $pdf->Ln();

        // $pdf->MultiCell(0, 7, self::black($pdf,"Diagnosis/Impression: "), 0, 'L');
        // $pdf->SetTextColor(102,56,0);
        // $pdf->SetFont('Arial','I',10);
        // $pdf->MultiCell(0, 5, $data->diagnosis, 0, 'L');
        // $pdf->Ln();

        // $pdf->MultiCell(0, 7, self::black($pdf,"Reason for referral: "), 0, 'L');
        // $pdf->SetTextColor(102,56,0);
        // $pdf->SetFont('Arial','I',10);
        // $pdf->MultiCell(0, 5, $data->reason, 0, 'L');
        // $pdf->Ln();

        if(isset($data2->icd[0])) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Diagnosis/Impression: "), 0, 'L');
            $pdf->SetTextColor(102,56,0);
            $pdf->SetFont('Arial','I',10);
            foreach($data2->icd as $icd) {
                $pdf->MultiCell(0, 5, $icd->code." - ".$icd->description, 0, 'L');
            }
            $pdf->Ln();
        }

        if(isset($data->diagnosis)) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Additional notes in diagnosis: "), 0, 'L');
            $pdf->SetTextColor(102,56,0);
            $pdf->SetFont('Arial','I',10);
            $pdf->MultiCell(0, 5, $data->diagnosis, 0, 'L');
            $pdf->Ln();
        }

        if(isset($data->other_diagnoses)) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Other diagnosis: "), 0, 'L');
            $pdf->SetTextColor(102,56,0);
            $pdf->SetFont('Arial','I',10);
            $pdf->MultiCell(0, 5, $data->other_diagnoses, 0, 'L');
            $pdf->Ln();   
        }

        if(isset($data2->reason)) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Reason for referral: "), 0, 'L');
            $pdf->SetTextColor(102,56,0);
            $pdf->SetFont('Arial','I',10);
            $pdf->MultiCell(0, 5, $data2->reason, 0, 'L');
            $pdf->Ln();
        }

        if(isset($data->other_reason_referral)) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Reason for referral: "), 0, 'L');
            $pdf->SetTextColor(102,56,0);
            $pdf->SetFont('Arial','I',10);
            $pdf->MultiCell(0, 5, $data->other_reason_referral, 0, 'L');
            $pdf->Ln();   
        }

        $pdf->MultiCell($x/2, 7, self::black($pdf,"Name of referring MD/HCW: ").self::orange($pdf,$data->md_referring,"Name of referring MD/HCW:"), 0, 'L');
        $pdf->MultiCell($x/2, 7, self::black($pdf,"Contact # of referring MD/HCW: ").self::orange($pdf,$data->referring_md_contact,"Contact # of referring MD/HCW:"), 0, 'L');
        $pdf->MultiCell($x/2, 7, self::black($pdf,"Name of referred MD/HCW- Mobile Contact # (ReCo): ").self::orange($pdf,$data->md_referred,"Name of referred MD/HCW- Mobile Contact # (ReCo):"), 0, 'L');

        $pdf->Output();

        exit;
    }

    public function orange($pdf,$val,$str)
    {
        $y = $pdf->getY()+4.5;
        $x = $pdf->getX()+2;
        $ln = $pdf->GetStringWidth($str)+1;
        $pdf->SetTextColor(102,56,0);
        $pdf->SetFont('Arial','I',10);
        $r = $pdf->Text($x+$ln,$y,$val);
        $pdf->SetFont('Arial','',10);
        return $r;
    }

    public function staticGreen($pdf,$val)
    {
        $pdf->SetTextColor(0,50,0);
        $pdf->SetFont('Arial','',10);
        return $val;
    }

    public function staticBlack($pdf,$val)
    {
        $y = $pdf->getY()+4.5;
        $x = $pdf->getX()+2;
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','',10);
        return $pdf->Text($x,$y,$val);
    }

    public function green($pdf,$val,$str)
    {
        $y = $pdf->getY()+4.5;
        $x = $pdf->getX()+2;
        $ln = $pdf->GetStringWidth($str)+1;
        $pdf->SetTextColor(0,50,0);
        $pdf->SetFont('Arial','I',10);
        $r = $pdf->Text($x+$ln,$y,$val);
        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(0,0,0);
        return $r;
    }

    public function gray($pdf,$val,$str)
    {
        $y = $pdf->getY()+4.5;
        $x = $pdf->getX()+2;
        $ln = $pdf->GetStringWidth($str)+1;
        $pdf->SetTextColor(51,51,51);
        $pdf->SetFont('Arial','I',10);
        $r = $pdf->Text($x+$ln,$y,$val);
        $pdf->SetFont('Arial','',10);
        return $r;
    }

    public function black($pdf,$val)
    {
        $y = $pdf->getY()+4.5;
        $x = $pdf->getX()+2;
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','B',10);
        return $pdf->Text($x,$y,$val);
    }
}
