<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\Session;

class ExcelCtrl extends Controller
{
    public function ExportExcelIncoming()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $title = 'Incoming Report';
        Excel::create($title, function($excel) use ($title) {
            $this->IncomingProcess($excel,$title);
        })->download('xlsx');
    }

    public function ExportExcelOutgoing(){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $title = 'Outgoing Report';
        Excel::create($title, function($excel) use ($title) {
            $this->OutgoingProcess($excel,$title);
        })->download('xlsx');
    }

    public function ExportExcelAll()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        Excel::create('Consolidate Report', function($excel) {
            $this->IncomingProcess($excel,"Incoming Report");
            $this->OutgoingProcess($excel,"Outgoing Report");
        })->download('xlsx');
    }

    public function IncomingProcess($excel,$title){
        $excel->sheet($title, function($sheet) {
            $data = Session::get('data');
            $data = json_decode( json_encode($data), true);
            $headerColumn = [
                "Name of Facility",
                "Total Incoming  Referrals",
                "Total Viewed Only Referrals",
                "Total Accepted Referrals",
                "Common Sources(Facility)",
                "Common Referring Doctor HCW/MD (Top 10)",
                "Average Referral Acceptance Turnaround time ",
                "Average Referral Arrival Turnaround Time",
                "Diagnoses (Top 10)",
                "Reasons (Top 10)",
                "Number of Horizontal referrals",
                "Number of Vertical Referrals",
                "Common Methods of Transportation",
                "Department",
                "Remarks"
            ];

            $sheet->appendRow($headerColumn);
            $sheet->row($sheet->getHighestRow(), function ($row) {
                $row->setFontFamily('Comic Sans MS');
                $row->setFontSize(10);
                $row->setFontWeight('bold');
                $row->setBackground('#FFFF00');
            });

            foreach($data as $row){
                $data = [
                    $row['name'],
                    $row['count_incoming'],
                    Session::get('seenzoned_incoming')[$row['id']],
                    Session::get('accepted_incoming')[$row['id']],
                    Session::get('common_source_incoming')[$row['id']],
                    Session::get('referring_doctor_incoming')[$row['id']],
                    Session::get('turnaround_time_accept_incoming')[$row['id']],
                    Session::get('turnaround_time_arrived_incoming')[$row['id']],
                    Session::get('diagnosis_ref_incoming')[$row['id']],
                    Session::get('reason_ref_incoming')[$row['id']],
                    "Under development this column",
                    "Under development this column",
                    Session::get('transport_ref_incoming')[$row['id']],
                    Session::get('department_ref_incoming')[$row['id']],
                    Session::get('issue_ref_incoming')[$row['id']],
                ];
                $sheet->appendRow($data);
            }
            //$sheet->getStyle('A1')->getAlignment()->setWrapText(true);

        });
    }

    public function OutgoingProcess($excel,$title){
        $excel->sheet($title, function($sheet) {
            $data = Session::get('data');
            $data = json_decode( json_encode($data), true);

            $headerColumn = [
                "Name of Facility",
                "Total Outgoing Referrals",
                "Total Viewed Only Referrals",
                "Total Accepted Referrals",
                "Total Archived Referrals",
                "Total Redirected Referrals",
                "Common Sources(Facility)",
                "Common Referring Doctor HCW/MD (Top 10)",
                "Average Referral Viewed Only Acceptance Turnaround time ",
                "Average Referral Viewed Only Redirection Turnaround time ",
                "Average Referral Acceptance Turnaround time ",
                "Average Referral Redirection Turnaround Time",
                "Average Referral to Transport Turnaround Time",
                "Diagnoses for Outgoing Referral(Top 10)",
                "Reasons for Referral(Top 10)",
                "Reasons for Redirection(Top 10)",
                "Number of Horizontal referrals",
                "Number of Vertical Referrals",
                "Common Methods of Transportation",
                "Department",
                "Remarks"
            ];

            $sheet->appendRow($headerColumn);
            $sheet->row($sheet->getHighestRow(), function ($row) {
                $row->setFontFamily('Comic Sans MS');
                $row->setFontSize(10);
                $row->setFontWeight('bold');
                $row->setBackground('#FFFF00');
            });

            foreach($data as $row){
                $data = [
                    $row['name'],
                    $row['count_incoming'],
                    Session::get('seenzoned_outgoing1')[$row['id']],
                    Session::get('accepted_outgoing1')[$row['id']],
                    "Under development this column",
                    "Under development this column",
                    Session::get('common_referred_facility_outgoing1')[$row['id']],
                    Session::get('common_referred_doctor_outgoing1')[$row['id']],
                    "Under development this column",
                    Session::get('turnaround_time_accept_outgoing1')[$row['id']],
                    "Under development this column",
                    "Under development this column",
                    Session::get('diagnosis_ref_outgoing1')[$row['id']],
                    Session::get('reason_ref_outgoing1')[$row['id']],
                    "Under development this column",
                    "Under development this column",
                    "Under development this column",
                    "Under development this column",
                    Session::get('transport_ref_outgoing1')[$row['id']],
                    Session::get('department_ref_outgoing1')[$row['id']],
                    Session::get('issue_ref_outgoing1')[$row['id']]
                ];
                $sheet->appendRow($data);
            }
        });

    }

}
