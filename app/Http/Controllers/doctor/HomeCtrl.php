<?php

namespace App\Http\Controllers\doctor;

use App\Activity;
use App\Http\Controllers\ParamCtrl;
use App\Tracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HomeCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doctor');
    }

    public function index()
    {
        /*for($i=1; $i<=12; $i++)
        {
            $date = date('Y').'/'.$i.'/01';
            $startdate = Carbon::parse($date)->startOfMonth();
            $enddate = Carbon::parse($date)->endOfMonth();

            $referred = Tracking::
            whereBetween('date_referred',[$startdate,$enddate])
                ->where('referred_from',$user->facility_id)
                ->groupBy('code')
                ->get();
            $data['referred'][] = count($referred);

            $accepted = Activity::where(function($q){
                $q->where('status','accepted')
                    ->orwhere('status','admitted')
                    ->orwhere('status','arrived');
            })
                ->whereBetween('date_referred',[$startdate,$enddate])
                ->where('referred_to',$user->facility_id)
                ->groupBy('code')
                ->get();
            $data['accepted'][] = count($accepted);

            $redirected = Activity::where(function($q){
                $q->where('status','redirected')
                    ->orwhere('status','rejected');
            })
                ->whereBetween('date_referred',[$startdate,$enddate])
                ->where('referred_to',$user->facility_id)
                ->groupBy('code')
                ->get();
            $data['rejected'][] = count($redirected);
        }

        $bar_chart = $data;*/


        ParamCtrl::lastLogin();

        $user = Session::get("auth");
        $group_by_department = User::
        select(DB::raw("count(users.id) as y"),DB::raw("coalesce(department.description,'NO DEPARTMENT') as label"))
            ->leftJoin("department","department.id","=","users.department_id")
            ->where("users.facility_id",$user->facility_id)
            ->where("users.level","doctor")
            ->groupBy("users.department_id")
            ->get();

        $date_start = Carbon::now()->startOfYear()->format('Y-m-d').' 00:00:00';
        $date_end = Carbon::now()->endOfYear()->format('Y-m-d').' 23:59:59';

        $incoming_statistics = \DB::connection('mysql')->select("call statistics_report_facility('$date_start','$date_end','$user->facility_id')")[0];
        //return json_encode($incoming_statistics);

        $referred_query = DB::connection('mysql')->select("call doctor_monthly_report('$date_start','$date_end','$user->facility_id','referred')");
        $accepted_query = DB::connection('mysql')->select("call doctor_monthly_report('$date_start','$date_end','$user->facility_id','accepted')");
        $redirected_query = DB::connection('mysql')->select("call doctor_monthly_report('$date_start','$date_end','$user->facility_id','redirected')");


        //for past 10 days
        $date_start_past = date('Y-m-d',strtotime(Carbon::now()->subDays(15))).' 00:00:00';
        $date_end_past = date('Y-m-d',strtotime(Carbon::now()->subDays(1))).' 23:59:59';
        $referred_past = DB::connection('mysql')->select("call doctor_past_transaction('$date_start_past','$date_end_past','$user->facility_id','referred')");
        $accepted_past = DB::connection('mysql')->select("call doctor_past_transaction('$date_start_past','$date_end_past','$user->facility_id','accepted')");
        $redirected_past = DB::connection('mysql')->select("call doctor_past_transaction('$date_start_past','$date_end_past','$user->facility_id','redirected')");

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


}
