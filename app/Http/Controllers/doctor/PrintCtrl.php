<?php

namespace App\Http\Controllers\doctor;

use Anouar\Fpdf\Fpdf;
use App\Activity;
use App\Tracking;
use App\Icd;
use App\LabRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\doctor\ReferralCtrl;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ParamCtrl;
use App\PrescribedPrescription;
use Illuminate\Support\Facades\Log;

class PDFPrescription extends FPDF
{
    public $underline = false;
    public $header = "";
    public $department = "";
    public $facility = "";
    public $facility_address = "";
    public $facility_contact = "";
    public $facility_email = "";
    public $signature_path = "";
    public $license = "";

    public function __construct($header = "", $department = "", $facility = "", $facility_address = "", $facility_contact = "", $facility_email = "", $signature_path = "", $license = "")
    {
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

    public function Header()
    {
        $this->SetTextColor(0);
        $headerPath = realpath(__DIR__.'/../../../../resources/img/video/wave_header.png');
        $this->Image($headerPath, 0, 0, 210, 35);
        $imagePath = realpath(__DIR__.'/../../../../resources/img/video/doh-logo.png');
        $this->Image($imagePath, 10, 10, 22);

        $this->Setx(10);
        $this->SetFont('Times', '', 9);
        $this->Cell(0, 4, 'Republic of the Philippines', 0, 1, 'C');
        $this->Setx(10);
        $this->SetFont('Times', '', 12);
        $this->Cell(0, 5, 'Department of Health', 0, 1, 'C');
        $this->Setx(10);
        $this->SetFont('Times', 'B', 14);
        $this->Cell(0, 6, 'CENTRAL VISAYAS CENTER for HEALTH DEVELOPMENT', 0, 1, 'C');
        $this->Ln(15);

        $this->Setx(10);
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 2, iconv('UTF-8', 'windows-1252', $this->header), 0, 1, 'C');
        $this->Setx(10);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', $this->department), 0, 1, 'C');
        $this->Ln(5);

        $this->Setx(10);
        $this->SetFont('Arial','B',10);
        $this->Cell(0,0, iconv('UTF-8', 'windows-1252', $this->facility), 0,"","C");
        $this->Ln();

        $this->Setx(10);
        $this->SetFont('Arial','',9);
        $this->Cell(0,12, iconv('UTF-8', 'windows-1252', $this->facility_address), 0,"","C");
        $this->Ln();
        $this->Setx(10);
        $this->Cell(0,0, iconv('UTF-8', 'windows-1252', $this->facility_email), 0,"","C");
        $this->Ln();
        $this->Setx(10);
        $this->Cell(0, 12, $this->facility_contact, 0, "", "C");

        $this->Ln(13);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(0.5);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(3);
    }

    public function Footer() {

        $headerPath = realpath(__DIR__.'/../../../../resources/img/video/wave_footer.png');
        $this->Image($headerPath, 0, 262, 210, 35);

        $this->SetY(-40);   
        $this->Setx(105);
        $this->SetTextColor(0,0,0);

        $this->SetFont('Arial', 'B', 11);
        $headerLength = strlen($this->header);
        if ($headerLength > 41) {
            $this->SetFont('Arial', 'B', 9);    
        }
        if ($headerLength > 49) {
            $this->SetFont('Arial', 'B', 8);    
        }
        $this->SetUnderline(true);
        $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', $this->header), 0, 1, '');

        $this->SetFont('Arial', '', 10);
        $this->Setx(105);
        $this->Cell(0, 0,"LICENSE NO.: ".$this->license, 0, 1, '');
        $this->Ln(4);
        $this->Setx(105);
        $this->Cell(0, 0,'PTR NO.:', 0, 1, '');
        if(is_file($this->signature_path)) {
            $this->Image($this->signature_path, 115, 245, 50, 0);
        }
        $this->SetY(-15);
        $this->Setx(10);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    public function SetUnderline($value)
    {
        $this->underline = $value;
    }

    public function _putdecoration($txt, $decor)
    {
        return $txt;
    }

    public function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '')
    {
        $txt = $this->_putdecoration($txt, 'T');
        parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }

    public function Write($h, $txt, $link = '')
    {
        $txt = $this->_putdecoration($txt, 'T');
        parent::Write($h, $txt, $link);
    }

    public function GetPageWidth()
    {
        return $this->w;
    }

    public function GetPageHeight()
    {
        return $this->h;
    }
}

class PrintCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
  

    public function printPrescription($tracking_id,$activity_id,Request $request) {

        if($request->prescription_new) {
            $code = Activity::find($activity_id)->code;
            $activity_id = Activity::where("code", $code)
                ->where("status", "prescription")
                ->where("id", ">", $activity_id)
                ->first()->id;
        }

        $prescription = Tracking::select(
            "tracking.code",
            \DB::raw("concat('Dr. ',referring_md.fname,' ',referring_md.lname) as referring_md"),
            "referring_md.signature as referring_md_signature",
            "referring_md.license",
            "department.description as department",
            "facility.name as facility",
            "facility.address as facility_address",
            "facility.contact as facility_contact",
            "facility.email as facility_email",
            \DB::raw("concat(patients.fname,' ',patients.lname) as patient_name"),
            /*\DB::raw("if(tracking.type='normal',pf.updated_at,preg_f.updated_at) as prescription_date"),
                \DB::raw("if(tracking.type='normal',pf.prescription,preg_f.prescription) as prescription"),*/
            "activity.created_at as prescription_date",
            "activity.remarks as prescription",

            "prescribed_prescriptions.prescribed_activity_id as prescribed_id",
            "prescribed_prescriptions.generic_name as generic_name",
            "prescribed_prescriptions.dosage as dosage",
            "prescribed_prescriptions.formulation as formulation",
            "prescribed_prescriptions.brandname as brandname",
            "prescribed_prescriptions.frequency as frequency",
            "prescribed_prescriptions.duration as duration",
            "prescribed_prescriptions.quantity as quantity",

            \DB::raw("if(tracking.type='normal',pf.other_diagnoses,preg_f.other_diagnoses) as other_diagnosis"),
            "patients.dob",
            "patients.sex",
            "muncity.description as muncity",
            \DB::raw("DATE_FORMAT(tracking.date_referred,'%m/%e/%Y') as date_referral")
        )
            ->where("tracking.id", $tracking_id)
            ->where("activity.id", $activity_id)
            ->leftJoin("users as referring_md", "referring_md.id", "=", "tracking.referring_md")
            ->leftJoin("department", "department.id", "=", "referring_md.department_id")
            ->leftJoin("facility", "facility.id", "=", "referring_md.facility_id")
            ->leftJoin("patient_form as pf", "pf.code", "=", "tracking.code")
            ->leftJoin("pregnant_form as preg_f", "preg_f.code", "=", "tracking.code")
            ->leftJoin("patients", "patients.id", "=", \DB::raw("if(tracking.type = 'normal',pf.patient_id,preg_f.patient_woman_id)"))
            ->leftJoin("muncity", "muncity.id", "=", "patients.muncity")
            ->leftJoin("activity", "activity.code", "=", "tracking.code")
            //->leftJoin("prescribed_prescriptions", "prescribed_prescriptions.code", "=", "tracking.code")
            ->leftJoin("prescribed_prescriptions", "prescribed_prescriptions.prescribed_activity_id", "=", "activity.id")
            ->first();
      
        $prescribedActivityId = $prescription->prescribed_id;
        $prescriptions = PrescribedPrescription::where('prescribed_activity_id', $prescribedActivityId)->get();

        $header = $prescription->referring_md;
        $department = $prescription->department;
        $facility = $prescription->facility;
        $facility_address = $prescription->facility_address;
        $facility_contact = $prescription->facility_contact;
        $facility_email = $prescription->facility_email;
        $signature_path = realpath(__DIR__ . '/../../../../' . $prescription->referring_md_signature);

        $pdf = new PDFPrescription($header, $department, $facility, $facility_address, $facility_contact, $facility_email, $signature_path, $prescription->license);
        $pdf->setTitle($prescription->facility);
        $pdf->AddPage();
        $pdf->AliasNbPages();

        Session::put('date_referral', $prescription->date_referred);
        $patient_age_year =  ParamCtrl::getAge($prescription->dob);
        $patient_age_month = ParamCtrl::getMonths($prescription->dob);

        $patient_age = "";
        if ($patient_age_year == 1)
            $patient_age .= $patient_age_year . " year ";
        else
            $patient_age .= $patient_age_year . " years ";

        if ($patient_age_month['month'] == 1)
            $patient_age .= $patient_age_month['month'] . " month ";
        else
            $patient_age .= $patient_age_month['month'] . " months ";

        if ($patient_age_month['days'] == 1)
            $patient_age .= $patient_age_month['days'] . " day old";
        else
            $patient_age .= $patient_age_month['days']." days old"; 

        //----------------------------------------------------------------------------------------------
        $formattedDate = date("m/d/Y", strtotime($prescription->prescription_date));
        $pdf->SetFillColor(222, 250, 238);  
        $pdf->SetFont('Arial', 'B', 10); 
        $pdf->Cell(13, 8, "Name: ", 0, 0, '', true); 

        $pdf->SetFont('Arial', 'I', 10); 
        $pdf->Cell(130, 8, "{$prescription->patient_name}", 0, 0, 'L', true);
        $pdf->Cell(2, 8, '', 0, 0);

        $pdf->SetFont('Arial', 'B', 10); 
        $pdf->Cell(13, 8, "Date: ", 0, 0, '', true);
        $pdf->SetFont('Arial', 'I', 10); 
        $pdf->Cell(0, 8, $formattedDate, 0, 1, '', true);
        $pdf->Ln(2); 

        $pdf->SetFont('Arial', 'B', 10); 
        $pdf->Cell(13, 8, "Age: ", 0, 0, '', true); 
        $pdf->SetFont('Arial', 'I', 10); 
        $pdf->Cell(130, 8, "{$patient_age}", 0, 0, 'L', true); 
        $pdf->Cell(2, 8, '', 0, 0);

        $pdf->SetFont('Arial', 'B', 10); 
        $pdf->Cell(13, 8, "Sex: ", 0, 0, '', true);   
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 8, "{$prescription->sex}", 0, 1, '', true); 
        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'B', 10); 
        $pdf->Cell(19, 8, "Address: ", 0, 0, '', true); 
        $pdf->SetFont('Arial', 'I', 10); 
        $pdf->Cell(0, 8, iconv('UTF-8', 'windows-1252', "{$prescription->muncity}"), 0, 1, '', true);
        $pdf->Ln(23);
        //-------------------------------------------------------------------------------------------------

        // $pdf->MultiCell($x/2, 7, self::black($pdf,"Name: ").self::orange($pdf,$prescription->patient_name,"Name: "), 0, 'L');
        // $y = $pdf->getY();
        // $pdf->SetXY($x/2+165, $y-7);
        // $pdf->MultiCell($x/2, 7, self::black($pdf,"Date: ").self::orange($pdf,date("m/d/Y",strtotime($prescription->prescription_date)),"Date: "), 0);
        // $pdf->MultiCell($x/2, 7, self::black($pdf,"Age: ").self::orange($pdf,$patient_age,"Age: "), 0, 'L');
        // $y = $pdf->getY();
        // $pdf->SetXY($x/2+165, $y-7); 
        // $pdf->MultiCell($x/2, 7, self::black($pdf,"Sex: ").self::orange($pdf,$prescription->sex,"Sex: "), 0);
        // $pdf->MultiCell(0, 7, self::black($pdf,"Address: ").self::orange($pdf,$prescription->muncity,"Address:"), 0, 'L');
        
        $prescriptionSetY = 117;
        $rxPath = realpath(__DIR__.'/../../../../resources/img/video/rx_3.png');
        $pdf->Image($rxPath, 12, $prescriptionSetY, 18, 0);
        $pdf->setY($prescriptionSetY);

        $prescriptionSetY = 138;
        //$pdf->SetTextColor(102,56,0);
        $pdf->SetFont('Arial','I',10);
        $leftMargin = 35;
        $pdf->SetLeftMargin($leftMargin);
        $pdf->setY($prescriptionSetY);

        // $prescriptionCounter = 0;
        // $totalPrescriptionCount = 0;

        // foreach ($prescriptions as $prescription) {
        //     $totalPrescriptionCount++;

        //     if ($prescriptionCounter == 7) {
        //         $pdf->AddPage();
        //         $prescriptionSetY = 85;
        //         $rxPath = realpath(__DIR__.'/../../../../resources/img/video/rx_3.png');
        //         $pdf->Image($rxPath, 12, $prescriptionSetY, 18, 0);
        //         $pdf->setY($prescriptionSetY);
        //         $prescriptionCounter = 0;

        //         $prescriptionSetY = 103;
        //         $pdf->setY($prescriptionSetY);
        //     }
        //     $prescriptionCounter++;

        //     $brandname = !empty($prescription->brandname) ? $prescription->brandname : 'N/A';
        //     $rowText = "{$totalPrescriptionCount}. {$prescription->generic_name}    ({$brandname})    {$prescription->dosage}     #{$prescription->quantity}";

        //     $availableWidth = $pdf->getPageWidth() - $pdf->GetX() - 20;

        //     if ($pdf->getStringWidth($rowText) + $pdf->getStringWidth($prescription->formulation) > $availableWidth) {
        //         $pdf->MultiCell(0, 5, $rowText, 0, 'L');
        //         $pdf->SetX(39);
        //         $pdf->MultiCell(0, 5, $prescription->formulation, 0, 'L');
        //     } else {
        //         $pdf->MultiCell(0, 5, $rowText . '     ' . $prescription->formulation, 0, 'L');
        //     }
        //     $rowText2 = "    Sig:   {$prescription->frequency}    {$prescription->duration}";
        //     $pdf->MultiCell(0, 5, $rowText2, 0, 'L');
        //     $pdf->Ln();
        // }

        // Convert HTML to plain text
        // Define tag patterns

       
        // Clean up HTML
        $html = str_replace(['&nbsp;', "\n"], ' ', $prescriptions[0]->prescription_v2);
        $html = preg_replace('/\s+/', ' ', $html);
        
        // Handle paragraphs and line breaks
        $html = str_replace(['<p>', '</p>', '<br>'], "\n", $html);
        
        $pdf->SetFont('Arial', '', 10);
        $fontSize = $pdf->FontSizePt; // Get current font size in points
        
        // List Tracking
        $listType = null;
        $listCount = 0;
        $indentation = 5;
        $leftMargin = 10; // Adjust this value to control the left margin
        
        foreach (explode("\n", $html) as $line) {
            $line = trim($line);
            if ($line === '') continue;
        
            $bold = false;
            $italic = false;
            $strike = false;
        
            preg_match_all('/(<\/?.*?>|[^<]+)/', $line, $matches);
            $pdf->SetX($leftMargin);  // Apply left margin
            $xStart = $pdf->GetX();
            $textWidth = 0;
        
            foreach ($matches[0] as $part) {
                // Handle List Tags
                if ($part === '<ul>' || $part === '<ol>') {
                    $listType = $part === '<ul>' ? 'ul' : 'ol';
                    $listCount = 0;
                    $pdf->Ln(0.5);
                    continue;
                }
                if ($part === '</ul>' || $part === '</ol>') {
                    $listType = null;
                    $pdf->Ln(0.5);
                    continue;
                }
                if ($part === '<li>') {
                    $pdf->SetX($leftMargin + $indentation);
                    if ($listType === 'ul') {
                        $pdf->Write(10, chr(149) . ' ');
                    } elseif ($listType === 'ol') {
                        $listCount++;
                        $pdf->Write(10, "{$listCount}. ");
                    }
                    $xStart = $pdf->GetX();
                    continue;
                }
                if ($part === '</li>') {
                    $pdf->Ln();  // New line after each item
                    continue;
                }
        
                // Handle Formatting Tags
                if ($part === '<strong>') {
                    $bold = true;
                    $pdf->SetFont('', ($italic ? 'BI' : 'B'));
                    continue;
                }
                if ($part === '</strong>') {
                    $bold = false;
                    $pdf->SetFont('', ($italic ? 'I' : ''));
                    continue;
                }
                if ($part === '<i>' || $part === '<em>') {
                    $italic = true;
                    $pdf->SetFont('', ($bold ? 'BI' : 'I'));
                    continue;
                }
                if ($part === '</i>' || $part === '</em>') {
                    $italic = false;
                    $pdf->SetFont('', ($bold ? 'B' : ''));
                    continue;
                }
                if ($part === '<s>') {
                    $strike = true;
                    continue;
                }
                if ($part === '</s>') {
                    $strike = false;
                    continue;
                }
        
                // Process Text
                $text = strip_tags($part);
                $textWidth = $pdf->GetStringWidth($text);
        
                if ($strike) {
                    $currentY = $pdf->GetY();
                    $yMid = $currentY + ($fontSize / 2);
                    $pdf->Line($xStart, $yMid, $xStart + $textWidth, $yMid);
                }
        
                $pdf->Write(10, $text);
                $xStart += $textWidth;
            }
            $pdf->Ln();
        }
        
        // Reset font at the end
        $pdf->SetFont('Arial', '', 10);
        




        // $rowText2 = "{$html}";
        // $pdf->MultiCell(0, 5, $rowText2, 0, 'L');
        
        $pdf->Output();
        exit;
    }

     public function labRequestData() {
        $data = array(
            'LABOR00195' => '24 Hour Albumin',
            'LABOR00196' => '24 Hour Creatinine',
            'LABOR00283' => '24 Hour Urine Albumin',
            'LABOR00284' => '24 Hour Urine Creatinine',
            'LABOR00079' => '24 Hr. Urine Protein Det.',
            'LABOR00156' => 'A/G Ratio',
            'LABOR00112' => 'ABO Typing',
            'LABOR00018' => 'ABO/Rh typing',
            'LABOR00203' => 'Acid Fast Staining',
            'LABOR00086' => 'Acid Phosphatase',
            'LABOR00120' => 'AFB culture',
            'LABOR00119' => 'AFB Stain',
            'LABOR00198' => 'ALAT (SGPT)',
            'LABOR00155' => 'Albumin',
            'LABOR00242' => 'Alfa Feto Protein (AFP)',
            'LABOR00050' => 'Alk. Phosphatase',
            'LABOR00163' => 'Alkaline Phosphatase',
            'LABOR00351' => 'ALT',
            'LABOR00167' => 'Amylase',
            'LABOR00220' => 'Anti DNA',
            'LABOR00250' => 'Anti HBs (Quantitative)',
            'LABOR00252' => 'Anti HCV (Hepa C Antibody)',
            'LABOR00186' => 'APT\'S Test',
            'LABOR00269' => 'APTT',
            'LABOR00094' => 'Arterial Blood Gas Analysis',
            'LABOR00199' => 'ASAT (SGOT)',
            'LABOR00053' => 'ASO',
            'LABOR00296' => 'ASO (qualitative)',
            'LABOR00213' => 'ASO Titre',
            'LABOR00074' => 'Autopsy',
            'LABOR00075' => 'Autopsy - Partial',
            'LABOR00300' => 'B-HCG (quantitative)',
            'LABOR00279' => 'B1B2 Total & Direct',
            'LABOR00206' => 'Bactec not Provided',
            'LABOR00205' => 'Bactec Provided',
            'LABOR00188' => 'Bence Jones Protein',
            'LABOR00253' => 'Beta HCG (Quantitative)',
            'LABOR00299' => 'Beta HCG (undiluted)',
            'LABOR00254' => 'Beta HCG with Titar',
            'LABOR00194' => 'Bilirubin, Total Direct',
            'LABOR00227' => 'Biopsy, large',
            'LABOR00226' => 'Biopsy, medium',
            'LABOR00225' => 'Biopsy, small',
            'LABOR00027' => 'Bleeding Time - Disposable',
            'LABOR00026' => 'Bleeding Time - Manual',
            'LABOR01002' => 'Blood Cholesterol',
            'LABOR00117' => 'Blood Culture / sensitivity test',
            'LABOR00301' => 'Blood Culture with Bactec',
            'LABOR00303' => 'Blood Culture without Bactec',
            'LABOR00197' => 'Blood Extraction',
            'LABOR00111' => 'Blood Indices (MCV, MCH, MCHC)',
            'LABOR00109' => 'Blood smear',
            'LABOR00173' => 'Blood Typing',
            'LABOR00286' => 'Blood Typing, ABO Only',
            'LABOR00285' => 'Blood Typing, ABO Rh',
            'LABOR00287' => 'Blood Typing, RH Only',
            'LABOR00001' => 'BLOOD UREA NITROGEN (BUN)',
            'LABOR00004' => 'BLOOD URIC ACID',
            'LABOR00030' => 'Bone marrow',
            'LABOR00067' => 'Bone Marrow Aspirate',
            'LABOR00268' => 'Bone Marrow Smears',
            'LABOR00180' => 'BSMP',
            'LABOR00147' => 'BUN',
            'LABOR00219' => 'C-Reactive Protein',
            'LABOR00060' => 'C3',
            'LABOR00247' => 'CA 125 (Ovary)',
            'LABOR00245' => 'CA 15-3 (Breast)',
            'LABOR00244' => 'CA 19-9 (Pancreas)',
            'LABOR00246' => 'CA 72-4 (GIT)',
            'LABOR00093' => 'Calcium',
            'LABOR00006' => 'CBC',
            'LABOR00243' => 'CEA (Colorectal)',
            'LABOR00282' => 'Cell and Differential Count, Sugar and Protein',
            'LABOR00230' => 'Cell Block',
            'LABOR00228' => 'Cell Cytology',
            'LABOR00291' => 'Cervical Biopsy',
            'LABOR00231' => 'Cervical Punch Biopsy',
            'LABOR00092' => 'Chloride',
            'LABOR00145' => 'Chloride and Sugar',
            'LABOR00045' => 'Cholesterol',
            'LABOR00166' => 'CKMB',
            'LABOR00107' => 'Clot Retraction Time (CRT, castor oil Method)',
            'LABOR00025' => 'Clotting Time',
            'LABOR00275' => 'Clotting Time (Lee White)',
            'LABOR00005' => 'CLOTTING TIME - BLEEDING TIME',
            'LABOR00105' => "Clotting Time, Bleeding Time (Slide, Duke's method)",
            'LABOR00098' => 'Complete blood count (includes WBC count differential count, hemoglobin, hematocrit)',
            'LABOR00115' => "Coomb's Test (Direct)",
            'LABOR00019' => "Coomb's test - direct",
            'LABOR00020' => "Coomb's test - indirect",
            'LABOR00264' => 'Cortisol',
            'LABOR00088' => 'CPK-MB',
            'LABOR00047' => 'Crea Nitrogen',
            'LABOR00148' => 'Creatinine Clearance Test ( CCT )',
            'LABOR00046' => 'Creatinine Test',
            'LABOR00207' => 'Cross Matching',
            'LABOR00114' => 'Cross Matching (3 Phase)',
            'LABOR00288' => 'Crossmatching per bag',
            'LABOR00054' => 'CRP',
            'LABOR00143' => 'CSF Analysis (cell count)',
            'LABOR00070' => 'CSF Analysis (complete)',
            'LABOR00071' => 'CSF Analysis - cell count / diff. count',
            'LABOR00033' => 'CSF Analysis - Protein',
            'LABOR00032' => 'CSF Analysis - Sugar',
            'LABOR00187' => 'CSF, Body Fluids Transudate, Exudate Cell & Differential Ct. Sugar, Protein Analysis',
            'LABOR00177' => 'CT, BT',
            'LABOR00116' => 'Culture & Sensitivity',
            'LABOR00248' => 'Cyfral 21-1 (Lungs)',
            'LABOR00036' => 'Cytology (fluids) with cell block',
            'LABOR00035' => 'Cytology (FNAB)',
            'LABOR00294' => 'Decalcification of Bone',
            'LABOR00059' => 'Dengue Antigen IgM',
            'LABOR00058' => 'Dengue dot',
            'LABOR00062' => 'DIC panel',
            'LABOR00024' => 'DIC panel - Activated PT',
            'LABOR00065' => 'DIC panel - Fibrin Degredation Product',
            'LABOR00064' => 'DIC panel - Fibrinogen Assay',
            'LABOR00023' => 'DIC panel - Prothrombin Time',
            'LABOR00063' => 'DIC panel - Thrombin Time',
            'LABOR00158' => 'Direct Bilirubin',
            'LABOR00210' => 'DU Variant (Anti Du)',
            'LABOR01004' => 'ECG',
            'LABOR00102' => 'Erythrocytes Sedimentation Rate (ESR)',
            'LABOR00061' => 'ESR',
            'LABOR00258' => 'Estradiol',
            'LABOR00281' => 'Extraction Fee',
            'LABOR01003' => 'Fasting / Random Blood Sugar (FBS / RBS)',
            'LABOR01000' => 'Fasting Blood Sugar',
            'LABOR00191' => 'FBS / RBS',
            'LABOR00040' => 'Fecalysis',
            'LABOR00136' => 'Fecalysis - Apts Test',
            'LABOR00134' => 'Fecalysis - Bile and Bilirubin',
            'LABOR00138' => 'Fecalysis - Concentration Technic',
            'LABOR00133' => 'Fecalysis - Occult Blood',
            'LABOR00132' => 'Fecalysis - Routine Stool Exam',
            'LABOR00140' => 'Fecalysis - Scock Tape for Enerobius',
            'LABOR00139' => 'Fecalysis - Smear for Amoeba',
            'LABOR00137' => 'Fecalysis - Sudan Test',
            'LABOR00135' => 'Fecalysis - Urobilinogen & Stercobilinogen',
            'LABOR00277' => 'Fecalysis w/ Kato Thick',
            'LABOR00263' => 'Ferritin',
            'LABOR00290' => 'FFP, Cryoppt Retyping per bag',
            'LABOR00270' => 'Filaria',
            'LABOR00229' => 'FNAB',
            'LABOR00073' => 'Frozen Section',
            'LABOR00256' => 'FSH',
            'LABOR00297' => 'FT3',
            'LABOR00238' => 'FT4',
            'LABOR00223' => 'FTI',
            'LABOR00298' => 'FTI (FT3/FT4 Index)',
            'LABOR00239' => 'FTI/FTT4 Index',
            'LABOR00202' => 'Geimsa Staining',
            'LABOR00043' => 'Glucose',
            'LABOR00149' => 'Glucose (FBS)',
            'LABOR00118' => 'Gram Staining',
            'LABOR00304' => 'Gram Staining (Direct)',
            'LABOR00293' => 'Gram Staining of Cervical Smears',
            'LABOR00292' => 'Gram Staining of Tissue',
            'LABOR00002' => "Gram's Stain",
            'LABOR00153' => 'GTT (Glucose Tolerance Test)',
            'LABOR00221' => 'H.pylori',
            'LABOR00295' => 'HbsAg',
            'LABOR00251' => 'HbsAg & Anti HBs (Package)',
            'LABOR00249' => 'HbsAg (Quantitative)',
            'LABOR00083' => 'HDL / LDL',
            'LABOR00176' => 'Hematocrit',
            'LABOR00100' => 'Hemoglobin',
            'LABOR00216' => 'Hepa B Antigen Quanti',
            'LABOR00218' => 'Hepa C Antibody',
            'LABOR00010' => 'Hepatitis - HBc',
            'LABOR00012' => 'Hepatitis - HBe / HBeAg',
            'LABOR00011' => 'Hepatitis - HBs',
            'LABOR00009' => 'Hepatitis - HBsAg (EIA)',
            'LABOR00008' => 'Hepatitis - HBsAg (RPHA)',
            'LABOR00051' => 'Hepatitis - HBsAg (strips)',
            'LABOR00208' => 'Hepatitis A Antigen',
            'LABOR00209' => 'Hepatitis B Antigen',
            'LABOR00013' => 'Hepatitis B profile',
            'LABOR00015' => 'Hepatitis C profile - HCV (EIA)',
            'LABOR00014' => 'Hepatitis C profile HCV (PA)',
            'LABOR00217' => 'Hepatitis Test',
            'LABOR00350' => 'HGT',
            'LABOR00039' => 'Histopathology - Large',
            'LABOR00038' => 'Histopathology - Medium or (2) small',
            'LABOR00037' => 'Histopathology - Small (1)',
            'LABOR00016' => 'HIV',
            'LABOR00215' => 'HIV Antibody',
            'LABOR00017' => 'HIV EIA',
            'LABOR00122' => 'India Ink',
            'LABOR00159' => 'Indirect Bilirubin',
            'LABOR00172' => 'Inorganic Phospharous',
            'LABOR00121' => 'KGH Mounting',
            'LABOR00003' => 'KOH',
            'LABOR00029' => "L.E.  Schilling's Hemogram",
            'LABOR00028' => 'L.E. Preparation (manual)',
            'LABOR00162' => 'LDH',
            'LABOR00110' => 'LE Cell Preparation',
            'LABOR00182' => 'Lee White, CT',
            'LABOR00255' => 'LH (Luteinizing Hormone)',
            'LABOR00201' => 'Lipid Profile',
            'LABOR00108' => 'Malarial Smear',
            'LABOR00068' => 'Malarial Smear - QBC',
            'LABOR00069' => 'Malarial Smear- Slide',
            'LABOR555' => 'MISCELLANEOUS TESTING',
            'LABOR00041' => 'Occult Blood',
            'LABOR00087' => 'OGTT',
            'LABOR00280' => 'OGTT (3 takes))',
            'LABOR00031' => 'Osmotic Fragility Test',
            'LABOR00034' => "PAP's Smear",
            'LABOR00099' => 'Partial Blood count ( WBC count, diff. count)',
            'LABOR00091' => 'Patassium',
            'LABOR00178' => 'Peripheral Smear',
            'LABOR00007' => 'PLATELET COUNT',
            'LABOR00103' => 'Platelet Count / Actual Platelet Count',
            'LABOR00289' => 'Platelet Crossmatching',
            'LABOR00169' => 'Potassium',
            'LABOR00146' => 'Pregnancy Test',
            'LABOR00081' => 'Pregnancy Test (Preg Test)',
            'LABOR00259' => 'Progesterone',
            'LABOR00257' => 'Prolactin',
            'LABOR00106' => 'Prothrombin Time (INR) DerivedFibrinogen, Protime %  Activity',
            'LABOR00181' => 'Protime',
            'LABOR00241' => 'PSA (Prostate)',
            'LABOR00189' => 'Quali Pregnancy Test',
            'LABOR00190' => 'Quanti Pregnancy Test',
            'LABOR00072' => 'Radical (Breast, Uterus, RND)',
            'LABOR00278' => 'Rapid Blood Sugar / RBS',
            'LABOR00174' => 'RBC Count',
            'LABOR00101' => 'Red Blood Cell Count',
            'LABOR00022' => 'Reticulocyte Count',
            'LABOR00104' => 'Reticulocytos count',
            'LABOR00113' => 'RH Blood Typing (Tube Method)',
            'LABOR00055' => 'Rheumatoid factor',
            'LABOR00214' => 'RPR (VDRL)',
            'LABOR1005' => 'RT PCR',
            'LABOR01005' => 'SAMPLE SAMPLE',
            'LABOR00066' => "Schilling's Hemogram",
            'LABOR00052' => 'Screening fee per blood unit',
            'LABOR00082' => 'Semen Analysis',
            'LABOR00142' => 'Seminal Fluid Analysis',
            'LABOR00273' => 'Serial HB, HCT',
            'LABOR00272' => 'Serial HB, HCT, Platelet',
            'LABOR00274' => 'Serial HCT, PLatelet Ct',
            'LABOR00049' => 'Serum Albumin',
            'LABOR00096' => 'SGOT',
            'LABOR00097' => 'SGPT',
            'LABOR00042' => 'Smear for Amoeba',
            'LABOR00271' => 'Smear for Malaria (SMP)',
            'LABOR00090' => 'Sodium',
            'LABOR00232' => 'Special Staining',
            'LABOR0099' => 'SPUTUM',
            'LABOR00204' => 'Stool / Rectal Swab Blood Culture:',
            'LABOR01001' => 'SYPHILIS TEST',
            'LABOR00240' => 'T Uptake',
            'LABOR00234' => 'T3',
            'LABOR00224' => 'T3 Uptake',
            'LABOR00237' => 'T3, T4, TSH',
            'LABOR00212' => 'T3T4',
            'LABOR00235' => 'T4',
            'LABOR00157' => 'Total Bilirubin',
            'LABOR00095' => 'Total Bilirubin B1B2',
            'LABOR00192' => 'Total Cholesterol',
            'LABOR00165' => 'Total CPR',
            'LABOR00048' => 'Total Protein',
            'LABOR00144' => 'Total Protein & Globulin',
            'LABOR00179' => 'Toxic Granules',
            'LABOR00085' => 'TP, Albumin, AG ratio',
            'LABOR00266' => 'TPAG',
            'LABOR00152' => 'Triglycerides',
            'LABOR00084' => 'Triglycerides / LDH',
            'LABOR00200' => 'Trop T',
            'LABOR00302' => 'Trop T (qualitative)',
            'LABOR00262' => 'Trop T (Quantitative)',
            'LABOR00222' => 'TSH',
            'LABOR00056' => 'Typhidot',
            'LABOR00193' => 'Urea (BUN)',
            'LABOR00150' => 'Uric Acid',
            'LABOR00044' => 'Uric Acid (Urates)',
            'LABOR00129' => 'Urinalysis - Bench Jones Protein',
            'LABOR00127' => 'Urinalysis - Bilirubin & Bile',
            'LABOR00128' => 'Urinalysis - Diabetic & Calcium',
            'LABOR00126' => 'Urinalysis - Qualitative & Quantitative Urobilinogen',
            'LABOR00125' => 'Urinalysis - Qualitative Albumin',
            'LABOR00124' => 'Urinalysis - Qualitative Sugar & Acetone',
            'LABOR00123' => 'Urinalysis - Routine (Qualitative & Microscopic)',
            'LABOR00130' => 'Urinalysis - Stone Analysis',
            'LABOR00076' => 'Urinalysis MARTEST',
            'LABOR00265' => 'Urinary Caculi (Stone Analysis)',
            'LABOR00077' => 'Urine Albumin',
            'LABOR00276' => 'Urine Bilirubin',
            'LABOR00078' => 'Urine Ketone/Acetone',
            'LABOR00184' => 'Urine PH',
            'LABOR00185' => 'Urine Specific Gravity',
            'LABOR00080' => 'Urine Sugar',
            'LABOR00131' => 'Urine Sugar (Clinitest)',
            'LABOR00175' => 'WBC Count',
            'LABOR00057' => 'Widal',
            'LABOR00141' => 'Widal Test'
        );

        return $data;
    }
    public function printLabResult($activity_id) {
        // $facility = Facility::select('id','name')->get();

        $activity = Activity::with([
            'patient.municipal',
            'referredFrom',
            'labRequest' => function ($query) {
                $query->select('activity_id', 'laboratory_code','others','requested_by','created_at')
                    ->with(['requestedBy' => function ($query) {
                        $query->select('id', 'fname', 'lname', 'signature', 'license');
                    }]);
            }
        ])
        ->find($activity_id);
        // return response()->json($activity);
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => asset('api/laboratories'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response, true);

        $activity->labRequest->each(function ($labRequest) use ($data) {
            $labRequest->laboratory_description = $data[$labRequest->laboratory_code] ?? null;
        });

        $requested_by = $activity->labRequest[0]->requestedBy;

        $header = 'Dr. ' . $requested_by->fname . ' ' . $requested_by->lname;
        $department = 'OPD';
        $facility = $activity->referredFrom;
        $facility_address = $facility->address;
        $facility_contact = $facility->contact;
        $facility_email = $facility->email;
        $signature_path = realpath(__DIR__ . '/../../../../' . $requested_by->signature);

        $pdf = new PDFPrescription($header, $department, $facility->name, $facility_address, $facility_contact, $facility_email, $signature_path, $facility->license);
        $pdf->setTitle($facility->name);
        $pdf->AddPage();

        $imageWidth = 20;
        $pageWidth = $pdf->GetPageWidth();
        $pdf->SetFont('Arial','B',15);
        $pdf->Cell(0,20,"LABORATORY REQUEST",0,"","C");
        $pdf->Ln();

        for ($x = 5; $x <= $pageWidth - 10; $x += $imageWidth + 40) { }

        $patient = $activity->patient;
        $patient_age_year =  ParamCtrl::getAge($patient->dob);
        $patient_age_month = ParamCtrl::getMonths($patient->dob);

        $patient_age = "";
        if ($patient_age_year == 1)
            $patient_age .= $patient_age_year . " year ";
        else
            $patient_age .= $patient_age_year . " years ";

        if ($patient_age_month['month'] == 1)
            $patient_age .= $patient_age_month['month'] . " month ";
        else
            $patient_age .= $patient_age_month['month'] . " months ";

        if ($patient_age_month['days'] == 1)
            $patient_age .= $patient_age_month['days'] . " day old";
        else
            $patient_age .= $patient_age_month['days']." days old"; 

        //===============================================================================
        $formattedDate = date("m/d/Y", strtotime($activity->labRequest[0]->created_at));
        $pdf->SetFillColor(222, 250, 238);  
        $pdf->SetFont('Arial', 'B', 10); 
        $pdf->Cell(13, 8, "Name: ", 0, 0, '', true); 

        $pdf->SetFont('Arial', 'I', 10); 
        $pdf->Cell(130, 8, "$patient->fname $patient->lname", 0, 0, 'L', true);
        $pdf->Cell(2, 8, '', 0, 0);

        $pdf->SetFont('Arial', 'B', 10); 
        $pdf->Cell(13, 8, "Date: ", 0, 0, '', true);
        $pdf->SetFont('Arial', 'I', 10); 
        $pdf->Cell(0, 8, $formattedDate, 0, 1, '', true);
        $pdf->Ln(2); 

        $pdf->SetFont('Arial', 'B', 10); 
        $pdf->Cell(13, 8, "Age: ", 0, 0, '', true); 
        $pdf->SetFont('Arial', 'I', 10); 
        $pdf->Cell(130, 8, "{$patient_age}", 0, 0, 'L', true); 
        $pdf->Cell(2, 8, '', 0, 0);

        $pdf->SetFont('Arial', 'B', 10); 
        $pdf->Cell(13, 8, "Sex: ", 0, 0, '', true);   
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 8, "{$patient->sex}", 0, 1, '', true); 
        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'B', 10); 
        $pdf->Cell(19, 8, "Address: ", 0, 0, '', true); 
        $pdf->SetFont('Arial', 'I', 10); 
        $pdf->Cell(0, 8, iconv('UTF-8', 'windows-1252', "{$patient->municipal->description}"), 0, 1, '', true);
        $pdf->Ln(10);

        //===============================================================================
        
        // $pdf->MultiCell($x/2, 7, self::black($pdf,"Name: ").self::orange($pdf,$patient->fname.' '.$patient->lname,"Name: "), 0, 'L');
        // $y = $pdf->getY();
        // $pdf->SetXY($x/2+40, $y-7);
        // $pdf->MultiCell($x/2, 7, self::black($pdf,"Date: ").self::orange($pdf,date("m/d/Y",strtotime($activity->created_at)),"Date: "), 0);
        // $pdf->MultiCell($x/2, 7, self::black($pdf,"Age: ").self::orange($pdf,$patient_age,"Age: "), 0, 'L');
        // $y = $pdf->getY();
        // $pdf->SetXY($x/2+40, $y-7);
        // $pdf->MultiCell($x/2, 7, self::black($pdf,"Sex: ").self::orange($pdf,$patient->sex,"Sex: "), 0);
        // $pdf->MultiCell(0, 7, self::black($pdf,"Address: ").self::orange($pdf,'Day-as, Cebu City, Cebu',"Address:"), 0, 'L');


        //$prescriptionSetY = 140;
        //$rxPath = realpath(__DIR__.'/../../../../resources/img/video/rx.png');
        //$pdf->Image($rxPath, 10, $prescriptionSetY, 30, 0);
        //$pdf->setY($prescriptionSetY);

        $pdf->SetTextColor(102,56,0);
        $pdf->SetFont('Arial','I',10);
        $leftMargin = 20;
        $pdf->SetLeftMargin($leftMargin);  

        $labRequestData = $this->labRequestData();

        // Initialize a counter to keep track of prescriptions
        $prescriptionCounter = 0;
        // dd($activity->labRequest);
       
        foreach ($activity->labRequest as $row) {
            if ($prescriptionCounter == 10) {
                $pdf->AddPage();
                $prescriptionCounter = 0;
            }
            $prescriptionCounter++;
           
            $rowText = "{$labRequestData[$row->laboratory_code]}";
            $pdf->MultiCell(0, 3, $rowText, 0, 'L');
     
            $pdf->MultiCell(0, 2, $row->other == null ? $row->others : '', 0, 'L');
            
            $pdf->Ln();
        }
        
        $pdf->Output();
        exit;
    }


    public function printReferral($track_id)
    {
        $user = Session::get('auth');
        $form_type = Tracking::where('id', $track_id)
            ->where(function ($q) use ($user) {
                $q->where('referred_from', $user->facility_id)
                    ->orwhere('referred_to', $user->facility_id);
            })
            ->first();
        $form_status = $form_type->status;
        if ($form_type) {
            $form_type = $form_type->type;
        } else {
            return redirect('doctor');
        }

        if ($form_type == 'normal') {
            $data = ReferralCtrl::normalForm($track_id, $form_status, $form_type);
            return self::printNormal($data->form, $data);
        } else if ($form_type == 'pregnant') {
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
        $x = ($pdf->w) - 20;

        $patient_address = '';
        $referred_address = '';

        $patient_address .= ($data->patient_brgy) ? $data->patient_brgy . ', ' : '';
        $patient_address .= ($data->patient_muncity) ? $data->patient_muncity . ', ' : '';
        $patient_address .= ($data->patient_province) ? $data->patient_province : '';

        $referred_address .= ($data->ff_brgy) ? $data->ff_brgy . ', ' : '';
        $referred_address .= ($data->ff_muncity) ? $data->ff_muncity . ', ' : '';
        $referred_address .= ($data->ff_province) ? $data->ff_province : '';

        $pdf->setTopMargin(17);
        $pdf->setTitle($data->woman_name);
        $pdf->addPage();

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 0, "BEmONC/ CEmONC REFERRAL FORM", 0, "", "C");
        $pdf->ln(10);
        $pdf->MultiCell(0, 7, self::black($pdf, "Patient Code: ") . self::orange($pdf, $data->code, "Patient Code :"), 0, 'L');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 7, self::black($pdf, "REFERRAL RECORD"), 0, 'L');
        $pdf->SetFont('Arial', '', 10);

        $pdf->MultiCell($x / 4, 7, self::black($pdf, "Who is Referring"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY(60, $y - 7);
        $pdf->MultiCell($x / 4, 7, self::black($pdf, "Record Number: ") . self::orange($pdf, $data->record_no, "Record Number:"), 0);
        $y = $pdf->getY();
        $pdf->SetXY(125, $y - 7);
        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Referred Date: ") . self::orange($pdf, $data->referred_date, "Referred Date:"), 0);

        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Name of referring MD/HCW: ") . self::orange($pdf, $data->md_referring, "Name of referring MD/HCW:"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY(125, $y - 7);
        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Arrival Date: ") . self::orange($pdf, $data->arrival_date, "Arrival Date:"), 0);

        $pdf->MultiCell(0, 7, self::black($pdf, "Contact # of referring MD/HCW: ") . self::orange($pdf, $data->referring_md_contact, "Contact # of referring MD/HCW:"), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Facility: ") . self::orange($pdf, $data->referring_facility, "Facility:"), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Facility Contact #: ") . self::orange($pdf, $data->referring_contact, "Facility Contact #:"), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Accompanied by the Health Worker: ") . self::orange($pdf, $data->health_worker, "Accompanied by the Health Worker:"), 0, 'L');

        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Referred To: ") . self::orange($pdf, $data->referred_facility, "Referred To:"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x / 2 + 40, $y - 7);
        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Department: ") . self::orange($pdf, $data->department, "Department:"), 0);

        $pdf->MultiCell(0, 7, self::black($pdf, "Address: ") . self::orange($pdf, $referred_address, "Address:"), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Covid Number: ") . self::orange($pdf, $data->covid_number, "Covid Number: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Clinical Status: ") . self::orange($pdf, $data->refer_clinical_status, "Clinical Status: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Surveillance Category: ") . self::orange($pdf, $data->refer_sur_category, "Surveillance Category: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Discharge Clinical Status: ") . self::orange($pdf, $data->dis_clinical_status, "Discharge Clinical Status: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Discharge Surveillance Category: ") . self::orange($pdf, $data->dis_sur_category, "Discharge Surveillance Category: "), 0, 'L');
        $pdf->Ln(3);

        $pdf->SetFillColor(200, 200, 200);
        $pdf->SetTextColor(30);
        $pdf->SetDrawColor(200);
        $pdf->SetLineWidth(.3);

        $pdf->MultiCell(0, 7, "WOMAN", 1, 'L', true);

        $pdf->SetFillColor(255, 250, 205);
        $pdf->SetTextColor(40);
        $pdf->SetDrawColor(230);
        $pdf->SetLineWidth(.3);

        $pdf->MultiCell(0, 7, "Name: " . self::green($pdf, $data->woman_name, 'name'), 1, 'L');
        $pdf->MultiCell(0, 7, "Age: " . self::green($pdf, $data->woman_age, 'Age'), 1, 'L');
        $pdf->MultiCell(0, 7, "Address: " . self::green($pdf, $data->patient_address, 'Address'), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Main Reason for Referral: ") . "\n" . self::staticGreen($pdf, $data->woman_reason), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Major Findings (Clinica and BP,Temp,Lab) : ") . "\n" . self::staticGreen($pdf, $data->woman_major_findings), 1, 'L');

        $pdf->SetFillColor(200, 200, 200);
        $pdf->SetTextColor(30);
        $pdf->SetDrawColor(200);
        $pdf->SetLineWidth(.3);

        $pdf->MultiCell(0, 7, "Treatments Give Time", 1, 'L', true);

        $pdf->SetFillColor(255, 250, 205);
        $pdf->SetTextColor(40);
        $pdf->SetDrawColor(230);
        $pdf->SetLineWidth(.3);

        $pdf->MultiCell(0, 7, "Before Referral: " . self::green($pdf, $data->woman_before_treatment . '-' . $data->woman_before_given_time, 'Before Referral'), 1, 'L');
        $pdf->MultiCell(0, 7, "During Transport: " . self::green($pdf, $data->woman_before_given_time . '-' . $data->woman_before_given_time, 'During Transport'), 1, 'L');
        $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Information Given to the Woman and Companion About the Reason for Referral : ") . "\n" . self::staticGreen($pdf, $data->woman_information_given), 1, 'L');

        if (isset($record->icd)) {
            $pdf->MultiCell(0, 7, self::black($pdf, "ICD-10: "), 1, 'L');
            foreach ($record->icd as $icd) {
                $pdf->MultiCell(0, 5, self::staticGreen($pdf, $icd->code . " - " . $icd->description), 0, 'L');
            }
        }

        if (isset($data->notes_diagnoses)) {
            $pdf->MultiCell(0, 7, self::black($pdf, "Diagnosis/Impression: "), 1, 'L');
            $pdf->MultiCell(0, 5, self::staticGreen($pdf, $data->notes_diagnoses), 1, 'L');
        }

        if (isset($data->other_diagnoses)) {
            $pdf->MultiCell(0, 7, self::black($pdf, "Other diagnosis: ") . "\n" . self::staticGreen($pdf, $data->other_diagnoses), 1, 'L');
        }

        if (isset($record->reason)) {
            $pdf->MultiCell(0, 7, self::black($pdf, "Reason for referral: ") . "\n" . self::staticGreen($pdf, $record->reason['reason']), 1, 'L');
        }

        if (isset($data->other_reason_referral)) {
            $pdf->MultiCell(0, 7, self::black($pdf, "Reason for referral: ") . "\n" . self::staticGreen($pdf, $data->other_reason_referral), 1, 'L');
        }

        if (count($baby) > 0) {
            $pdf->Ln(8);

            $pdf->SetFillColor(200, 200, 200);
            $pdf->SetTextColor(30);
            $pdf->SetDrawColor(200);
            $pdf->SetLineWidth(.3);

            $pdf->MultiCell(0, 7, "BABY", 1, 'L', true);

            $pdf->SetFillColor(255, 250, 205);
            $pdf->SetTextColor(40);
            $pdf->SetDrawColor(230);
            $pdf->SetLineWidth(.3);

            $pdf->MultiCell(0, 7, "Name: " . self::green($pdf, $baby->baby_name, 'name'), 1, 'L');
            $pdf->MultiCell(0, 7, "Date of Birth: " . self::green($pdf, $baby->baby_dob, "Date of Birth"), 1, 'L');
            $pdf->MultiCell(0, 7, "Body Weight: " . self::green($pdf, $baby->weight, 'body weight'), 1, 'L');
            $pdf->MultiCell(0, 7, "Gestational Age: " . self::green($pdf, $baby->gestational_age, 'Gestational Age'), 1, 'L');
            $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Main Reason for Referral: ") . "\n" . self::staticGreen($pdf, $baby->baby_reason), 1, 'L');
            $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Major Findings (Clinica and BP,Temp,Lab) : ") . "\n" . self::staticGreen($pdf, $baby->baby_major_findings), 1, 'L');
            $pdf->MultiCell(0, 7, "Last (Breast) Feed (Time): " . self::green($pdf, $baby->baby_last_feed, "Last (Breast) Feed (Time)"), 1, 'L');

            $pdf->SetFillColor(200, 200, 200);
            $pdf->SetTextColor(30);
            $pdf->SetDrawColor(200);
            $pdf->SetLineWidth(.3);

            $pdf->MultiCell(0, 7, "Treatments Give Time", 1, 'L', true);

            $pdf->SetFillColor(255, 250, 205);
            $pdf->SetTextColor(40);
            $pdf->SetDrawColor(230);
            $pdf->SetLineWidth(.3);

            $pdf->MultiCell(0, 7, "Before Referral: " . self::green($pdf, $baby->baby_before_treatment . '-' . $baby->baby_before_given_time, 'Before Referral'), 1, 'L');
            $pdf->MultiCell(0, 7, "During Transport: " . self::green($pdf, $baby->baby_during_transport . '-' . $baby->baby_transport_given_time, 'During Transport'), 1, 'L');
            $pdf->MultiCell(0, 7, self::staticBlack($pdf, "Information Given to the Woman and Companion About the Reason for Referral : ") . "\n" . self::staticGreen($pdf, $baby->baby_information_given), 1, 'L');
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
        $x = ($pdf->w) - 20;

        $pdf->setTopMargin(17);
        $pdf->setTitle($data->patient_name);
        $pdf->addPage();

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 0, "CENTRAL VISAYAS HEALTH REFERRAL SYSTEM", 0, "", "C");
        $pdf->ln();
        $pdf->Cell(0, 12, "Clinical Referral Form", 0, "", "C");
        $pdf->Ln(20);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 7, self::black($pdf, "Patient Code: ") . self::orange($pdf, $data->code, "Patient Code :"), 0, 'L');
        $pdf->Ln(5);
        $pdf->MultiCell(0, 7, self::black($pdf, "Name of Referring Facility: ") . self::orange($pdf, $data->referring_name, "Name of Referring Facility:"), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Facility Contact #: ") . self::orange($pdf, $data->referring_contact, "Facility Contact #:"), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Address: ") . self::orange($pdf, $referring_address, "Address:"), 0, 'L');


        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Referred to: ") . self::orange($pdf, $data->referred_name, "Referred to:"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x / 2 + 10, $y - 7);
        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Department: ") . self::orange($pdf, $data->department, "Department:"), 0);

        $pdf->MultiCell(0, 7, self::black($pdf, "Address: ") . self::orange($pdf, $referred_address, "Address:"), 0, 'L');

        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Date/Time Referred (ReCo): ") . self::orange($pdf, $data->time_referred, "Date/Time Referred (ReCo):"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x / 2 + 10, $y - 7);
        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Date/Time Transferred: ") . self::orange($pdf, $data->time_transferred, "Date/Time Transferred:"), 0);

        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Name of Patient: ") . self::orange($pdf, $data->patient_name, "Name of Patient:"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x / 2 + 10, $y - 7);

        $patient_age = "";
        if (strcmp($data2->age_type, "y") == 0)
            $patient_age = $data2->patient_age . (($data2->patient_age == 1) ? " year" : " years");
        elseif (strcmp($data2->age_type, "m") == 0)
            $patient_age = $data2->patient_age['month'] . (($data2->patient_age['month'] == 1) ? " mo, " : " mos, ") . $data2->patient_age['days'] . (($data2->patient_age['days'] == 1) ? " day" : " days");

        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Age: ") . self::orange($pdf, $patient_age, "age:"), 0);

        $y = $pdf->getY();
        $pdf->SetXY(($x / 2) + ($x / 4) - 5, $y - 7);
        $pdf->MultiCell($x / 4, 7, self::black($pdf, "Sex: ") . self::orange($pdf, $data->patient_sex, "sex:"), 0);
        $y = $pdf->getY();
        $pdf->SetXY(($x / 2) + ($x / 2) - 30, $y - 7);
        $pdf->MultiCell($x / 4, 7, self::black($pdf, "Status: ") . self::orange($pdf, $data->patient_status, "Status:"), 0);

        $pdf->MultiCell(0, 7, self::black($pdf, "Address: ") . self::orange($pdf, $patient_address, "address:"), 0, 'L');

        $pdf->MultiCell($x / 2, 7, self::black($pdf, "PhilHealth Status: ") . self::orange($pdf, $data->phic_status, "PhilHealth status:"), 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x / 2 + 10, $y - 7);
        $pdf->MultiCell($x / 2, 7, self::black($pdf, "PhilHealth # : ") . self::orange($pdf, $data->phic_id, "PhilHealth # :"), 0);


        $pdf->MultiCell(0, 7, self::black($pdf, "Covid Number: ") . self::orange($pdf, $data->covid_number, "Covid Number: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Clinical Status: ") . self::orange($pdf, $data->refer_clinical_status, "Clinical Status: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Surveillance Category: ") . self::orange($pdf, $data->refer_sur_category, "Surveillance Category: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Discharge Clinical Status: ") . self::orange($pdf, $data->dis_clinical_status, "Discharge Clinical Status: "), 0, 'L');
        $pdf->MultiCell(0, 7, self::black($pdf, "Discharge Surveillance Category: ") . self::orange($pdf, $data->dis_sur_category, "Discharge Surveillance Category: "), 0, 'L');

        $pdf->MultiCell(0, 7, self::black($pdf, "Case Summary (pertinent Hx/PE, including meds, labs, course etc.): "), 0, 'L');
        $pdf->SetTextColor(102, 56, 0);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->MultiCell(0, 5, $data->case_summary, 0, 'L');
        $pdf->Ln();

        $pdf->MultiCell(0, 7, self::black($pdf, "Summary of ReCo (pls. refer to ReCo Guide in Referring Patients Checklist): "), 0, 'L');
        $pdf->SetTextColor(102, 56, 0);
        $pdf->SetFont('Arial', 'I', 10);
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

        if (isset($data2->icd[0])) {
            $pdf->MultiCell(0, 7, self::black($pdf, "ICD-10: "), 0, 'L');
            $pdf->SetTextColor(102, 56, 0);
            $pdf->SetFont('Arial', 'I', 10);
            foreach ($data2->icd as $icd) {
                $pdf->MultiCell(0, 5, $icd->code . " - " . $icd->description, 0, 'L');
            }
            $pdf->Ln();
        }

        if (isset($data->diagnosis)) {
            $pdf->MultiCell(0, 7, self::black($pdf, "Diagnosis/Impression: "), 0, 'L');
            $pdf->SetTextColor(102, 56, 0);
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->MultiCell(0, 5, $data->diagnosis, 0, 'L');
            $pdf->Ln();
        }

        if (isset($data->other_diagnoses)) {
            $pdf->MultiCell(0, 7, self::black($pdf, "Other diagnosis: "), 0, 'L');
            $pdf->SetTextColor(102, 56, 0);
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->MultiCell(0, 5, $data->other_diagnoses, 0, 'L');
            $pdf->Ln();
        }

        if (isset($data2->reason)) {
            $pdf->MultiCell(0, 7, self::black($pdf, "Reason for referral: "), 0, 'L');
            $pdf->SetTextColor(102, 56, 0);
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->MultiCell(0, 5, $data2->reason['reason'], 0, 'L');
            $pdf->Ln();
        }

        if (isset($data->other_reason_referral)) {
            $pdf->MultiCell(0, 7, self::black($pdf, "Reason for referral: "), 0, 'L');
            $pdf->SetTextColor(102, 56, 0);
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->MultiCell(0, 5, $data->other_reason_referral, 0, 'L');
            $pdf->Ln();
        }

        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Name of referring MD/HCW: ") . self::orange($pdf, $data->md_referring, "Name of referring MD/HCW:"), 0, 'L');
        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Contact # of referring MD/HCW: ") . self::orange($pdf, $data->referring_md_contact, "Contact # of referring MD/HCW:"), 0, 'L');
        $pdf->MultiCell($x / 2, 7, self::black($pdf, "Name of referred MD/HCW- Mobile Contact # (ReCo): ") . self::orange($pdf, $data->md_referred, "Name of referred MD/HCW- Mobile Contact # (ReCo):"), 0, 'L');

        $pdf->Output();

        exit;
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
