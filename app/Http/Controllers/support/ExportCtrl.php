<?php

namespace App\Http\Controllers\support;

use App\Facility;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class ExportCtrl extends Controller
{
    public function exportUsers()
    {
        $data = User::get()->toArray();
        return Excel::create('itsolutionstuff_example', function($excel) use ($data) {
            $excel->sheet('mySheet', function($sheet) use ($data)
            {
                $sheet->fromArray($data);
            });
        })->download('xlsx');
//
//        Excel::create('User Data', function($excel) use ($data_array){
//
//            $excel->sheet('User Data', function ($sheet) use($data_array){
//               $sheet->fromArray($data_array,null,'A4',false,false);
//            });
//        })->download('xlsx');
    }

    static function dailyUsers()
    {
        $user = Session::get('auth');

        $date = Session::get('dateReportUsers');
        if(!$date){
            $date = date('Y-m-d');
        }

        $users = User::where('facility_id',$user->facility_id)
            ->where('level','doctor')
            ->orderBy('lname','asc')
            ->get();
        $data[] = array('Monitoring Tool for Central Visayas Electronic Health Referral System');
        $data[] = array('Form 1');
        $data[] = array('DAILY REPORT FOR AVAILABLE USERS');
        $data[] = array('');
        $data[] = array('Name of Hospital: ' . Facility::find($user->facility_id)->name);
        $data[] = array('Date: '. date('F d, Y',strtotime($date)));
        $data[] = array('');
        $data[] = array('Name of User','On Duty','Off Duty','Login','Logout','Remarks');

        foreach($users as $row)
        {
            $log = ReportCtrl::getLoginLog($row->id); //&#10004;
            $loginStatus = '';
            $logoutStatus = '';
            $login = '';
            $logout = '';

            if($log->status=='login')
            {
                $loginStatus = '✓';
            }

            if($log->status=='login_off'){
                $logoutStatus = '✓';
            }

            if($log->login){
                $login = date('h:i A',strtotime($log->login));
            }

            if($log->logout){
                $logout = date('h:i A',strtotime($log->logout));
            }

            $data[] = array(
                'users' => $row->lname.', '.$row->fname,
                'loginStatus' => $loginStatus,
                'logoutStatus' => $logoutStatus,
                'login' => $login,
                'logout' => $logout
            );
        }



        return Excel::create("DailyUsers-$date",function ($excel) use ($data) {
            $excel->sheet('Users',function($sheet) use ($data) {
                $totalCell = count($data) + 1;

                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                $sheet->mergeCells('A3:F3');
                $sheet->mergeCells('A4:F4');
                $sheet->mergeCells('A5:F5');
                $sheet->mergeCells('A6:F6');
                $sheet->mergeCells('A7:F7');
                $sheet->mergeCells('A8:F8');

                $sheet->setBorder("A1:F$totalCell", 'thin');

                $sheet->cell("A1:F4", function($cell){
                    $cell->setAlignment('center');
                });

                $sheet->cell("B9:F$totalCell", function($cell){
                    $cell->setAlignment('center');
                });

                $sheet->getStyle('A1:A7')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);

                $sheet->getStyle('A9:F9')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);


                $sheet->fromArray($data);

            });
        })->download('xlsx');
    }
}
