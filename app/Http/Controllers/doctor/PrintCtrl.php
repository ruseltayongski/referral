<?php

namespace App\Http\Controllers\doctor;

use Anouar\Fpdf\Fpdf;
use App\Tracking;
use App\Icd;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\doctor\ReferralCtrl;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ParamCtrl;

class PDFPrescription extends FPDF {
    public $underline = false;
    public $header = "";
    public $department = "";
    public $facility = "";
    public $facility_address = "";
    public $facility_contact = "";
    public $facility_email = "";
    public $signature_path = "";
    public $license = "";
    
    public function __construct($header = "",$department = "",$facility="",$facility_address="",$facility_contact="",$facility_email="",$signature_path="",$license="") {
        $this->header = $header;
        $this->department = $department;
        $this->facility = $facility;
        $this->facility_address = $facility_address;
        $this->facility_contact = $facility_contact;
        $this->facility_email = $facility_email;
        $this->signature_path = $signature_path;
        $this->license = $license;
        parent::__construct();
    }

    public function Header() {
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 5, $this->header, 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10,$this->department, 0, 1, 'C');

        $this->Ln(10);

        $this->SetFont('Arial','B',13);
        $this->Cell(0,0,$this->facility,0,"","C");
        $this->Ln();
        $this->SetFont('Arial','',12);
        $this->Cell(0,12,$this->facility_address,0,"","C");
        $this->Ln();
        $this->Cell(0,0,$this->facility_email,0,"","C");
        $this->Ln();
        $this->Cell(0,12,$this->facility_contact,0,"","C");

        $this->Ln(17);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(0.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
    }
    
    public function Footer() {
        $this->SetY(-30);
        $this->Setx(120);
        $this->SetTextColor(0,0,0);
        $this->SetFont('Arial', 'B', 14);
        $this->SetUnderline(true);
        $this->Cell(0, 10, $this->header."   ", 0, 1, '');
        $this->SetFont('Arial', '', 10);
        $this->Setx(120);
        $this->Cell(0, 0,"LICENSE NO.: ".$this->license, 0, 1, '');
        $this->Ln(4);
        $this->Setx(120);
        $this->Cell(0, 0,'PTR NO.:', 0, 1, '');
        if(is_file($this->signature_path)) {
            $this->Image($this->signature_path, 120, 250, 50, 0);
        }
    }

    public function SetUnderline($value) {
        $this->underline = $value;
    }
    
    public function _putdecoration($txt, $decor) {
        return $txt;
    }
    
    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {
        $txt = $this->_putdecoration($txt, 'T');
        parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }
    
    public function Write($h, $txt, $link = '') {
        $txt = $this->_putdecoration($txt, 'T');
        parent::Write($h, $txt, $link);
    }

    public function GetPageWidth() {
        return $this->w;
    }

    public function GetPageHeight() {
        return $this->h;
    }
}

class PrintCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function printPrescription($tracking_id) {
        $prescription = Tracking::
            select(
                "tracking.code",
                \DB::raw("concat('DR. ',action_md.fname,' ',action_md.lname) as action_md"),
                "action_md.signature as action_md_signature",
                "action_md.license",
                "department.description as department",
                "facility.name as facility",
                "facility.address as facility_address",
                "facility.contact as facility_contact",
                "facility.email as facility_email",
                \DB::raw("concat(patients.fname,' ',patients.lname) as patient_name"),
                \DB::raw("if(tracking.type='normal',pf.updated_at,preg_f.updated_at) as prescription_date"),
                \DB::raw("if(tracking.type='normal',pf.prescription,preg_f.prescription) as prescription"),
                \DB::raw("if(tracking.type='normal',pf.other_diagnoses,preg_f.other_diagnoses) as other_diagnosis"),
                "patients.dob",
                "patients.sex",
                "muncity.description as muncity",
                \DB::raw("DATE_FORMAT(tracking.date_referred,'%m/%e/%Y') as date_referral")
            )
            ->where("tracking.id",$tracking_id)
            ->leftJoin("users as action_md","action_md.id","=","tracking.action_md")
            ->leftJoin("department","department.id","=","action_md.department_id")
            ->leftJoin("facility","facility.id","=","action_md.facility_id")  
            ->leftJoin("patient_form as pf","pf.code","=","tracking.code")  
            ->leftJoin("pregnant_form as preg_f","preg_f.code","=","tracking.code") 
            ->leftJoin("patients","patients.id","=",\DB::raw("if(tracking.type = 'normal',pf.patient_id,preg_f.patient_woman_id)"))
            ->leftJoin("muncity","muncity.id","=","patients.muncity")
            ->first();

        $header = $prescription->action_md;
        $department = $prescription->department;
        $facility = $prescription->facility;
        $facility_address = $prescription->facility_address;
        $facility_contact = $prescription->facility_contact;
        $facility_email = $prescription->facility_email;
        $signature_path = realpath(__DIR__.'/../../../../'.$prescription->action_md_signature);
        $pdf = new PDFPrescription($header,$department,$facility,$facility_address,$facility_contact,$facility_email,$signature_path,$prescription->license);
        $pdf->setTitle($prescription->facility);
        $pdf->AddPage();

        $imagePath = realpath(__DIR__.'/../../../../resources/img/video/doh-logo-opacity.png');
        $imageWidth = 20; 
        $pageWidth = $pdf->GetPageWidth();
        $pageHeight = $pdf->GetPageHeight();
        for ($x = 5; $x <= $pageWidth-10; $x += $imageWidth+40) {
            $flag = true;
            for($y=0; $y<=$pageHeight; $y += 20) {
                $paddingX = 0;
                if($flag) {
                    $paddingX = 30;
                    $flag = false;
                } else {
                    $flag = true;
                }
                $pdf->Image($imagePath, $x+$paddingX, $y, $imageWidth);
            }
        }

        Session::put('date_referral', $prescription->date_referred);
        $patient_age_year =  ParamCtrl::getAge($prescription->dob);
        $patient_age_month = ParamCtrl::getMonths($prescription->dob);

        $patient_age = "";
        if($patient_age_year == 1)
            $patient_age .= $patient_age_year." year ";
        else
            $patient_age .= $patient_age_year." years ";
 
        if($patient_age_month['month'] == 1)
            $patient_age .= $patient_age_month['month']." month ";
        else
            $patient_age .= $patient_age_month['month']." months ";

        if($patient_age_month['days'] == 1)
            $patient_age .= $patient_age_month['days']." day old";
        else
            $patient_age .= $patient_age_month['days']." days old"; 
        
        $pdf->MultiCell($x/2, 7, self::black($pdf,"Name: ").self::orange($pdf,$prescription->patient_name,"Name: "), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x/2+40, $y-7);
        $pdf->MultiCell($x/2, 7, self::black($pdf,"Date: ").self::orange($pdf,date("m/d/Y",strtotime($prescription->prescription_date)),"Date: "), 0);

        $pdf->MultiCell($x/2, 7, self::black($pdf,"Age: ").self::orange($pdf,$patient_age,"Age: "), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x/2+40, $y-7);
        $pdf->MultiCell($x/2, 7, self::black($pdf,"Sex: ").self::orange($pdf,$prescription->sex,"Sex: "), 0);

        $pdf->MultiCell(0, 7, self::black($pdf,"Address: ").self::orange($pdf,$prescription->muncity,"Address:"), 0, 'L');

        $icd = Icd::select('icd10.code', 'icd10.description')
            ->join('icd10', 'icd10.id', '=', 'icd.icd_id')
            ->where('icd.code',$prescription->code)->get();

        $prescriptionSetY = 145;    
        if(isset($icd[0])) {
            $pdf->MultiCell(0, 7,self::black($pdf,"Diagnoses: "), 0, 'L');
            foreach($icd as $i) {
                $pdf->SetX(25); // Set the left margin position
                $pdf->SetTextColor(102,56,0);
                $pdf->SetFont('Arial','I',10);
                $icd_description = str_replace(array("\r", "\n"), '', $i->code.' - '.$i->description);
                $pdf->MultiCell(0, 7,$icd_description, 0, 'L');
            }
        } else {
            $pdf->MultiCell(0, 7, self::black($pdf,"Diagnoses: ").self::orange($pdf,$prescription->other_diagnosis,"Diagnoses:"), 0, 'L');
            $prescriptionSetY = 120;
        }

        $rxPath = realpath(__DIR__.'/../../../../resources/img/video/rx.png');
        $pdf->Image($rxPath, 10, $prescriptionSetY, 30, 0);

        $pdf->setY($prescriptionSetY);
        $leftMargin = 45;
        $pdf->SetLeftMargin($leftMargin); 
        $pdf->MultiCell(0, 7, self::black($pdf,"Prescription: "), 0, 'L');
        $pdf->SetTextColor(102,56,0);
        $pdf->SetFont('Arial','I',10);
        $leftMargin = 48;
        $pdf->SetLeftMargin($leftMargin);  
        $pdf->MultiCell(0, 5, $prescription->prescription , 0, 'L');

        $pdf->Output();
        exit;
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
        }
        else if($form_type=='pregnant'){
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
            $pdf->MultiCell(0, 7, self::black($pdf,"ICD-10: "), 1, 'L');
            foreach($record->icd as $icd) {
                $pdf->MultiCell(0, 5, self::staticGreen($pdf, $icd->code." - ".$icd->description), 0, 'L');
            }
        }

        if(isset($data->notes_diagnoses)) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Diagnosis/Impression: "), 1, 'L');
            $pdf->MultiCell(0, 5, self::staticGreen($pdf, $data->notes_diagnoses), 1, 'L');
        }

        if(isset($data->other_diagnoses)) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Other diagnosis: ")."\n".self::staticGreen($pdf, $data->other_diagnoses), 1, 'L');
        }

        if(isset($record->reason)) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Reason for referral: ")."\n".self::staticGreen($pdf, $record->reason['reason']), 1, 'L');
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
        $patient_address = $data->patient_address;
        $referring_address = $data->referring_address;
        $referred_address = $data->referred_address;

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
        $pdf->MultiCell(0, 7, self::black($pdf,"Address: ").self::orange($pdf,$referring_address,"Address:"), 0, 'L');


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

        $patient_age = "";
        if(strcmp($data2->age_type, "y") == 0)
            $patient_age = $data2->patient_age.(($data2->patient_age == 1) ? " year" : " years");
        elseif(strcmp($data2->age_type, "m") == 0)
            $patient_age = $data2->patient_age['month'].(($data2->patient_age['month'] == 1) ? " mo, " : " mos, ").$data2->patient_age['days'].(($data2->patient_age['days'] == 1) ? " day" : " days");

        $pdf->MultiCell($x/2, 7, self::black($pdf,"Age: ").self::orange($pdf,$patient_age,"age:"), 0);

        $y = $pdf->getY();
        $pdf->SetXY(($x/2)+($x/4) - 5, $y-7);
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
            $pdf->MultiCell(0, 7, self::black($pdf,"ICD-10: "), 0, 'L');
            $pdf->SetTextColor(102,56,0);
            $pdf->SetFont('Arial','I',10);
            foreach($data2->icd as $icd) {
                $pdf->MultiCell(0, 5, $icd->code." - ".$icd->description, 0, 'L');
            }
            $pdf->Ln();
        }

        if(isset($data->diagnosis)) {
            $pdf->MultiCell(0, 7, self::black($pdf,"Diagnosis/Impression: "), 0, 'L');
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
            $pdf->MultiCell(0, 5, $data2->reason['reason'], 0, 'L');
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
