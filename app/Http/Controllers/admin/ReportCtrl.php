<?php

namespace App\Http\Controllers\admin;

use App\Facility;
use App\Login;
use App\Tracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class ReportCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
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

        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();

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

    public function onboardFacility(){
        $data = Facility::
                    select("facility.name","facility.chief_hospital","facility.contact","facility.hospital_type","province.description as province")
                    ->leftJoin("province","province.id","=","facility.province")
                    ->orderBy("facility.province","desc")
                    ->orderBy("facility.name","asc")
                    ->get();

        $data = \DB::connection('mysql')->select("call onboard_facility()");

        return view('admin.report.onboard_facility',[
            'title' => 'ONBOARD FACILITY',
            "data" => $data
        ]);
    }

}
