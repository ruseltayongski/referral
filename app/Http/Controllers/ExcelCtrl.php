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
        Excel::create($title, function($excel) {
            $excel->sheet('ALL', function($sheet) {
                $data = Session::get('data');
                $data = json_decode( json_encode($data), true);
                $headerColumn = [
                    "Name of Facility",
                    "Total Incoming  Referrals",
                    "Number of Referrals Seenzoned",
                    "Total Accepted Referrals",
                    "Name of Facility",
                    "Common Sources of Referrals",
                    "Common Referring HCW/MD",
                    "Average Referral Acceptance  Turnaround time ",
                    "Average Transport to Arrival Turnaround Time",
                    "Reasons for referral (ranked))",
                    "Top diagnoses for incoming referral",
                    "Number of Horizontal referrals",
                    "Number of Vertical Referrals",
                    "Common Methods of Transportation",
                    "Number of ER Referrals",
                    "Department",
                    "Issues and Concerns"
                ];

                $sheet->appendRow($headerColumn);
                $sheet->row($sheet->getHighestRow(), function ($row) {
                    $row->setFontFamily('Comic Sans MS');
                    $row->setFontSize(10);
                    $row->setFontWeight('bold');
                    $row->setBackground('#FFFF00');
                });

                foreach($data as $row){
                    $common_source = '';
                    foreach(Session::get('common_source_incoming')[$row['id']] as $common){
                        $common_source .= $common['name'].'-'.$common['count']."
                        \n\n\n\n\n\n\n
                        ";
                    }
                    $data = [
                        $row['name'],
                        Session::get('accepted_incoming')[$row['id']],
                        $row['count_incoming'],
                        Session::get('seenzoned_incoming')[$row['id']],
                        $common_source
                    ];
                    $sheet->appendRow($data);
                }
                $sheet->getStyle('A1')->getAlignment()->setWrapText(true);

            })->download('xlsx');
        });

    }

    public function ExportExcelIncoming1()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $title = 'Incoming Report';
        $type = 'xlsx';
        $data = Session::get('data');
        $data = json_decode( json_encode($data), true);

        return Excel::create($title, function($excel) use ($data,$title) {
            $excel->sheet($title, function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download($type);
    }
}
