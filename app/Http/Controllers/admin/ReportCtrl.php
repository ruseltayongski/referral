<?php

namespace App\Http\Controllers\admin;

use App\Facility;
use App\Http\Controllers\ApiController;
use App\Login;
use App\Province;
use App\Tracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ReportCtrl extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
        //$this->middleware('admin');
        //$this->middleware('doctor');
    }

    public function online()
    {
        $date = Session::get('dateReportOnline');
        if(!$date){
            $date = date('Y-m-d');
        }

        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();

        $data = Login::select(
                    'login.*',
                        'users.*',
                        'login.status as login_status'
                )
                ->join('users','users.id','=','login.userId')
                ->whereBetween('login.login',[$start,$end])
                ->where('login.logout','0000-00-00 00:00:00')
                ->where('users.level','!=','admin')
                ->where('users.username','!=','rtayong_doctor')
                ->orderBy('users.facility_id','asc')
                ->orderBy('login.id','desc')
                ->orderBy('users.lname','asc')
                ->paginate(20);

        return view('admin.online',[
            'title' => 'Online Users',
            'data' => $data
        ]);
    }

    public function filterOnline(Request $req)
    {
        Session::put('dateReportOnline',$req->date);
        return self::online();
    }

    public function online1() //12/23/2019 created
    {
        $date = Session::get('dateReportOnline');
        if(!$date){
            $date = date('Y-m-d');
        }

        $start = $date.' 00:00:00';
        $end = $date.' 23:59:59';

        $data = \DB::connection('mysql')->select("call AttendanceFunc('$start','$end')");

        return view('admin.online',[
            'title' => 'Online Users',
            'data' => $data
        ]);
    }

    public function filterOnline1(Request $req)
    {
        Session::put('dateReportOnline',$req->date);
        return self::online1();
    }

    public function referral()
    {
        $start = Session::get('startDateReportReferral');
        $end = Session::get('endDateReportReferral');
        if(!$start)
            $start = date('Y-m-d');
        if(!$end)
            $end = date('Y-m-d');

        $start = Carbon::parse($start)->startOfDay();
        $end = Carbon::parse($end)->endOfDay();

        $data = Tracking::whereBetween('updated_at',[$start,$end])
            ->orderBy('updated_at','desc')
            ->paginate(20);
        return view('admin.referral',[
            'title' => 'Referral Status',
            'data' => $data
        ]);
    }

    public function filterReferral(Request $req)
    {
        $range = explode('-',str_replace(' ', '', $req->date));
        $tmp1 = explode('/',$range[0]);
        $tmp2 = explode('/',$range[1]);

        $start = $tmp1[2].'-'.$tmp1[0].'-'.$tmp1[1];
        $end = $tmp2[2].'-'.$tmp2[0].'-'.$tmp2[1];

        Session::put('startDateReportReferral',$start);
        Session::put('endDateReportReferral',$end);
        return self::referral();
    }

    public function graph(){
        return view('admin.report.graph');
    }

    public function bar_chart(){
        return view('admin.report.bar_chart');
    }

    public function onlineFacility(Request $request){
        if($request->isMethod('post') && isset($request->day_date)){
            $day_date = date('Y-m-d',strtotime($request->day_date));
        } else {
            $day_date = date('Y-m-d');
        }
        $stored_name = "online_facility('$day_date')";
        $data = \DB::connection('mysql')->select("call $stored_name");

        return view('admin.report.online_facility',[
            'title' => 'ONLINE FACILITY',
            "data" => $data,
            'day_date' => $day_date
        ]);
    }

    public function offlineFacility(Request $request,$province_id){
        if($request->isMethod('post') && isset($request->day_date)){
            $day_date = date('Y-m-d',strtotime($request->day_date));
        } else {
            $day_date = date('Y-m-d');
        }

        $data = \DB::connection('mysql')->select("call offline_facility('$day_date','$province_id')");

        return view('admin.report.offline_facility',[
            'title' => 'Offline Facility',
            "data" => $data,
            'day_date' => $day_date
        ]);
    }

    public function weeklyReport($province_id,Request $request){
        if($request->isMethod('post') && isset($request->date_range)){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0]));
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1]));
        } else {
            $date_start = date('Y-m-d',strtotime(Carbon::now()->subDays(31)));
            $date_end = date('Y-m-d');
        }

        $facility = Facility::select("id as facility_id","hospital_type","name")->where("referral_used","yes")
            ->where("province",$province_id)
            ->orderBy(DB::raw("
                CASE
                    WHEN hospital_type = 'government' THEN 'a'
                    WHEN hospital_type = 'private' THEN 'b'
                    ELSE hospital_type
                END 
            "),"asc")
            ->orderBy("name","desc")
            ->get();
        $generate_weeks = \DB::connection('mysql')->select("call generate_weeks('$date_start','$date_end')");

        return view('admin.report.offline_facility_weekly',[
            'title' => 'Login Status',
            'facility' => $facility,
            'generate_weeks' => $generate_weeks,
            'date_start' => $date_start,
            'date_end' => $date_end,
            "province" => Province::find($province_id)->description,
            "province_id" => $province_id
        ]);
    }

    public function onboardFacility($province_id){
        $data = \DB::connection('mysql')->select("call onboard_facility('$province_id')");

        return view('admin.report.onboard_facility',[
            'title' => 'ONBOARD FACILITY',
            "data" => $data
        ]);
    }

    public function onboardUsers(){
        $onboard_users = \DB::connection('mysql')->select("call onboard_users()");
        return view('admin.report.onboard_users',[
            "onboard_users" => $onboard_users
        ]);
    }

    public function statisticsReport(Request $request,$province){
        if(isset($request->date_range)){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = Carbon::now()->startOfMonth()->format('Y-m-d').' 00:00:00';
            $date_end = Carbon::now()->endOfDay()->format('Y-m-d').' 23:59:59';
            $request->date_range = $date_start." - ".$date_end;
        }
        $apiCtrl = new ApiController();
        $request->province = $province;
        $data = $apiCtrl->api($request);
        return view('admin.report.statistics',[
            'title' => 'STATISTICS REPORT INCOMING',
            "data" => $data,
            'date_range_start' => $date_start,
            'date_range_end' => $date_end,
            'request_type' => $request->request_type,
            'province' => $province
        ]);
    }

    public function erobReport(Request $request){
        if($request->isMethod('post') && isset($request->date_range)){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = Carbon::now()->startOfYear()->format('Y-m-d').' 00:00:00';
            $date_end = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
        }

        $stored_name = "er_ob_report('$date_start','$date_end')";
        $data = \DB::connection('mysql')->select("call $stored_name");

        return view('admin.report.er_ob',[
            'title' => 'ER OB REPORT',
            "data" => $data,
            'date_range_start' => $date_start,
            'date_range_end' => $date_end
        ]);
    }

    public function averageUsersOnline(){
        $date_start = Carbon::now()->startOfYear()->format('Y-m-d').' 00:00:00';
        $date_end = Carbon::now()->endOfYear()->format('Y-m-d').' 23:59:59';

        $data = \DB::connection('mysql')->select("call average_login_month('$date_start','$date_end')");
        return view('admin.report.average_users_online',[
            "data" => $data
        ]);
    }

    public function sottoReports(){
        return view("admin.report.sotto_reports");
    }

}
