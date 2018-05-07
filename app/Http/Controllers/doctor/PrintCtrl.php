<?php

namespace App\Http\Controllers\doctor;

use Anouar\Fpdf\Fpdf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PrintCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doctor');
    }

    public function printReferral($track_id)
    {

        $pdf = new Fpdf();
        $x = ($pdf->w)-20;

        $pdf->setTopMargin(17);
        $pdf->setTitle('Print Referral Form');
        $pdf->addPage();

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,0,"CENTRAL VISAYAS HEALTH REFERRAL SYSTEM",0,"","C");
        $pdf->ln();
        $pdf->Cell(0,12,"Clinical Referral Form",0,"","C");
        $pdf->Ln(20);
        $pdf->SetFont('Arial','',10);

        $pdf->MultiCell(0, 7, "Name of Referring Facility:", 0, 'L');
        $pdf->MultiCell(0, 7, "Facility Contact #: ", 0, 'L');
        $pdf->MultiCell(0, 7, "Address: ", 0, 'L');

        $pdf->MultiCell($x/2, 7, "Referred to:", 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x/2+10, $y-7);
        $pdf->MultiCell($x/2, 7, "Department: ", 0);

        $pdf->MultiCell(0, 7, "Address: ", 0, 'L');

        $pdf->MultiCell($x/2, 7, "Date/Time Referred (ReCo):", 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x/2+10, $y-7);
        $pdf->MultiCell($x/2, 7, "Date/Time Transferred:", 0);

        $pdf->MultiCell($x/2, 7, "Name of Patient:", 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x/2+10, $y-7);
        $pdf->MultiCell($x/4, 7, "Age:", 0);
        $y = $pdf->getY();
        $pdf->SetXY(($x/2)+($x/4) - 15, $y-7);
        $pdf->MultiCell($x/4, 7, "Sex:", 0);
        $y = $pdf->getY();
        $pdf->SetXY(($x/2)+($x/2) - 30, $y-7);
        $pdf->MultiCell($x/4, 7, "Status:", 0);

        $pdf->MultiCell(0, 7, "Address: ", 0, 'L');

        $pdf->MultiCell($x/2, 7, "PhilHealth status:", 0, 'L');
        $y = $pdf->getY();
        $pdf->SetXY($x/2+10, $y-7);
        $pdf->MultiCell($x/2, 7, "PhilHealth #:", 0);

        $pdf->MultiCell(0, 7, "Case Summary (pertinent Hx/PE, including meds, labs, course etc.):", 0, 'L');
        $pdf->MultiCell(0, 7, "", 1, 'L');
        $pdf->Ln();

        $pdf->MultiCell(0, 7, "Summary of ReCo (pls. refer to ReCo Guide in Referring Patients Checklist): ", 0, 'L');
        $pdf->MultiCell(0, 7, "", 1, 'L');
        $pdf->Ln();

        $pdf->MultiCell(0, 7, "Diagnosis/Impression: ", 0, 'L');
        $pdf->MultiCell(0, 7, "", 1, 'L');
        $pdf->Ln();

        $pdf->MultiCell(0, 7, "Reason for referral: ", 0, 'L');
        $pdf->MultiCell(0, 7, "", 1, 'L');
        $pdf->Ln();

        $pdf->MultiCell(0, 7, "Name of referring MD/HCW: ", 0, 'L');
        $pdf->MultiCell(0, 7, "NContact # of referring MD/HCW:  ", 0, 'L');
        $pdf->MultiCell(0, 7, "Name of referred MD/HCW- Mobile Contact # (ReCo): ", 0, 'L');

        $pdf->Output();

        exit;
    }
}
