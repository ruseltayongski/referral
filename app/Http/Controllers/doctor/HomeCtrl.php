<?php

namespace App\Http\Controllers\doctor;

use App\Activity;
use App\Department;
use App\Facility;
use App\Http\Controllers\ParamCtrl;
use App\Login;
use App\Patients;
use App\Tracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Events\NewReferral;

class HomeCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('doctor');
    }

    public function index()
    {
        ParamCtrl::lastLogin();

        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();

        $user = Session::get("auth");
        $group_by_department = $user->level == 'admin' ?
            Login::
            select(DB::raw("count(users.id) as y"),DB::raw("coalesce(department.description,'NO DEPARTMENT') as label"))
                ->leftJoin("users","users.id","=","login.userId")
                ->leftJoin("department","department.id","=","users.department_id")
                ->whereBetween('login.login',[$start,$end])
                ->where('login.logout','0000-00-00 00:00:00')
                ->where("users.level","doctor")
                ->groupBy("users.department_id")
                ->get()
        :
            Login::
            select(DB::raw("count(users.id) as y"),DB::raw("coalesce(department.description,'NO DEPARTMENT') as label"))
                ->leftJoin("users","users.id","=","login.userId")
                ->leftJoin("department","department.id","=","users.department_id")
                ->whereBetween('login.login',[$start,$end])
                ->where('login.logout','0000-00-00 00:00:00')
                ->where("users.facility_id",$user->facility_id)
                ->where("users.level","doctor")
                ->groupBy("users.department_id")
                ->get()
        ;

        $date_start = Carbon::now()->startOfYear()->format('Y-m-d').' 00:00:00';
        $date_end = Carbon::now()->endOfYear()->format('Y-m-d').' 23:59:59';

        $incoming_statistics = \DB::connection('mysql')->select("call statistics_report_facility('$date_start','$date_end','$user->facility_id','$user->level')")[0];
        //return json_encode($incoming_statistics);

        $referred_query = DB::connection('mysql')->select("call doctor_monthly_report('$date_start','$date_end','$user->facility_id','referred','$user->level')");
        $accepted_query = DB::connection('mysql')->select("call doctor_monthly_report('$date_start','$date_end','$user->facility_id','accepted','$user->level')");
        $redirected_query = DB::connection('mysql')->select("call doctor_monthly_report('$date_start','$date_end','$user->facility_id','redirected','$user->level')");


        //for past 15 days
        $date_start_past = date('Y-m-d',strtotime(Carbon::now()->subDays(15))).' 00:00:00';
        $date_end_past = date('Y-m-d',strtotime(Carbon::now()->subDays(1))).' 23:59:59';
        $referred_past = DB::connection('mysql')->select("call doctor_past_transaction('$date_start_past','$date_end_past','$user->facility_id','referred','$user->level')");
        $accepted_past = DB::connection('mysql')->select("call doctor_past_transaction('$date_start_past','$date_end_past','$user->facility_id','accepted','$user->level')");
        $redirected_past = DB::connection('mysql')->select("call doctor_past_transaction('$date_start_past','$date_end_past','$user->facility_id','redirected','$user->level')");

        $index = 0;
        foreach($referred_query as $row){
            $data['referred'][] = $row->value;
            $data['accepted'][] = $accepted_query[$index]->value;
            $data['redirected'][] = $redirected_query[$index]->value;
            $index++;
        }

        return view('doctor.home',[
            "group_by_department" => $group_by_department,
            "doctor_monthly_report" => $data,
            "referred_past" => $referred_past,
            "accepted_past" => $accepted_past,
            "redirected_past" => $redirected_past,
            "incoming_statistics" => $incoming_statistics,
            "date_start" => $date_start,
            "date_end" => Carbon::now()->format('Y-m-d')
        ]);
    }

    public function index1()
    {
        ParamCtrl::lastLogin();
        $date_start = Carbon::now()->startOfYear()->format('Y-m-d').' 00:00:00';
        return view('doctor.home1',[
            "date_start" => date('F d,Y',strtotime($date_start)),
            "date_end" => date('F d,Y',strtotime(Carbon::now()->format('Y-m-d')))
        ]);
    }

    public function doctorMonthlyReport() {
        $user = Session::get("auth");
        $date_start = Carbon::now()->startOfYear()->format('Y-m-d').' 00:00:00';
        $date_end = Carbon::now()->endOfYear()->format('Y-m-d').' 23:59:59';

        $referred_query = DB::connection('mysql')->select("call doctor_monthly_report('$date_start','$date_end','$user->facility_id','referred','$user->level')");
        $accepted_query = DB::connection('mysql')->select("call doctor_monthly_report('$date_start','$date_end','$user->facility_id','accepted','$user->level')");
        $redirected_query = DB::connection('mysql')->select("call doctor_monthly_report('$date_start','$date_end','$user->facility_id','redirected','$user->level')");

        $index = 0;
        $data = [];
        foreach($referred_query as $row){
            $data['referred'][] = $row->value;
            $data['accepted'][] = $accepted_query[$index]->value;
            $data['redirected'][] = $redirected_query[$index]->value;
            $index++;
        }

        return $data;
    }

    public function optionPerDepartment() {
        $user = Session::get("auth");
        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();

        $group_by_department = $user->level == 'admin' ?
            Login::
            select(DB::raw("count(users.id) as y"),DB::raw("coalesce(department.description,'NO DEPARTMENT') as label"))
                ->leftJoin("users","users.id","=","login.userId")
                ->leftJoin("department","department.id","=","users.department_id")
                ->whereBetween('login.login',[$start,$end])
                ->where('login.logout','0000-00-00 00:00:00')
                ->where("users.level","doctor")
                ->groupBy("users.department_id")
                ->get()
            :
            Login::
            select(DB::raw("count(users.id) as y"),DB::raw("coalesce(department.description,'NO DEPARTMENT') as label"))
                ->leftJoin("users","users.id","=","login.userId")
                ->leftJoin("department","department.id","=","users.department_id")
                ->whereBetween('login.login',[$start,$end])
                ->where('login.logout','0000-00-00 00:00:00')
                ->where("users.facility_id",$user->facility_id)
                ->where("users.level","doctor")
                ->groupBy("users.department_id")
                ->get()
        ;

        return $group_by_department;
    }

    public function optionPerActivity() {
        $user = Session::get("auth");
        $date_start = Carbon::now()->startOfYear()->format('Y-m-d').' 00:00:00';
        $date_end = Carbon::now()->endOfYear()->format('Y-m-d').' 23:59:59';

        $incoming_statistics = \DB::connection('mysql')->select("call statistics_report_facility('$date_start','$date_end','$user->facility_id','$user->level')")[0];

        return json_encode($incoming_statistics);
    }

    public function optionLastTransaction() {
        $user = Session::get("auth");
        //for past 15 days
        $date_start_past = date('Y-m-d',strtotime(Carbon::now()->subDays(15))).' 00:00:00';
        $date_end_past = date('Y-m-d',strtotime(Carbon::now()->subDays(1))).' 23:59:59';
        $referred_past = DB::connection('mysql')->select("call doctor_past_transaction('$date_start_past','$date_end_past','$user->facility_id','referred','$user->level')");
        $accepted_past = DB::connection('mysql')->select("call doctor_past_transaction('$date_start_past','$date_end_past','$user->facility_id','accepted','$user->level')");
        $redirected_past = DB::connection('mysql')->select("call doctor_past_transaction('$date_start_past','$date_end_past','$user->facility_id','redirected','$user->level')");

        return [
            "referred_past" => $referred_past,
            "accepted_past" => $accepted_past,
            "redirected_past" => $redirected_past
        ];
    }

    public function viewMap(){
        $facility = Facility::whereNotNull("latitude")->get();
        return view('map',[
            "facility" => $facility
        ]);
    }
}
