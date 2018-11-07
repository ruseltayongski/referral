<?php

namespace App\Http\Controllers\support;

use App\Activity;
use App\Login;
use App\Seen;
use App\Tracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ReportCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('support');
    }

    public function users()
    {
        $user = Session::get('auth');
        $data = User::where('facility_id',$user->facility_id)
                ->where('level','doctor')
                ->orderBy('lname','asc')
                ->paginate(20);

        return view('support.report.users',[
            'title' => "Daily Users",
            'data' => $data
        ]);
    }

    public function referral()
    {
        $user = Session::get('auth');
        $data = User::where('facility_id',$user->facility_id)
            ->where('level','doctor')
            ->orderBy('lname','asc')
            ->paginate(20);
        return view('support.report.referral',[
            'title' => 'Daily Referral',
            'data' => $data
        ]);
    }

    public function usersFilter(Request $req)
    {
        Session::put('dateReportUsers',$req->date);

        return self::users();
    }

    static function getLoginLog($id)
    {
        $date = Session::get('dateReportUsers');
        if(!$date){
            $date = date('Y-m-d');
        }

        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();

        $data = array(
            'login' => '',
            'logout' => '',
            'status' => ''
        );

        $tmp = Login::where('userId',$id)
            ->whereBetween('login',[$start,$end])
            ->first();
        if($tmp){
            $data['login'] = $tmp->login;
        }

        $tmp = Login::where('userId',$id)
            ->whereBetween('logout',[$start,$end])
            ->orderBy('id','desc')
            ->first();
        if($tmp){
            $data['logout'] = $tmp->logout;
        }

//        $data['logout'] = Login::where('userId',$id)
//            ->whereBetween('logout',[$start,$end])
//            ->orderBy('id','desc')
//            ->first();
//
        $tmp = Login::where('userId',$id)
            ->whereBetween('logout',[$start,$end])
            ->orderBy('id','desc')
            ->first();

        if(!$tmp){
            $tmp = Login::where('userId',$id)
                ->whereBetween('login',[$start,$end])
                ->orderBy('id','desc')
                ->first();

            if(!$tmp){
                $data['status'] = 'offline';
            }else{
                $data['status'] = $tmp->status;
            }

        }else{
            $data['status'] = $tmp->status;
        }

        $data = (object)$data;
        return $data;
    }

    static function getLoginStatus($id)
    {
        $date = Session::get('dateReportUsers');
        if(!$date){
            $date = date('Y-m-d');
        }

        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();

        $data = Login::where('userId',$id)
            ->whereBetween('logout',[$start,$end])
            ->orderBy('id','desc')
            ->first();
        if(!$data){
            $data = Login::where('userId',$id)
                ->whereBetween('login',[$start,$end])
                ->orderBy('id','desc')
                ->first();
        }
        if(!$data){
            $data['status'] = 'offline';
            $data = (object)$data;
        }

        return $data->status;
    }

    static function countOutgoingReferral($id)
    {
        $date = Session::get('dateReportReferral');
        if(!$date){
            $date = date('Y-m-d');
        }

        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();


        $accepted = Activity::where('referring_md',$id)
            ->whereBetween('date_referred',[$start,$end])
            ->where('status','accepted')
            ->count();

        $redirected = Tracking::join('activity','activity.code','=','tracking.code')
            ->where('tracking.referring_md',$id)
            ->where('activity.status','rejected')
            ->whereBetween('tracking.date_referred',[$start,$end])
            ->count();

        $seen = Seen::select('tracking_id',DB::raw('count(*) as total'))
                    ->join('tracking','tracking.id','=','seen.tracking_id')
                    ->where('tracking.referring_md',$id)
                    ->whereBetween('tracking.date_referred',[$start,$end])
                    ->groupBy('tracking_id')
                    ->get();

        $total = Tracking::where('tracking.referring_md',$id)
                ->whereBetween('tracking.date_referred',[$start,$end])
                ->count();

        $cseen = (count($seen)- ($accepted+$redirected));
        $unseen = $total - count($seen);

        return array(
            'accepted' => $accepted,
            'redirected' => $redirected,
            'seen' => $cseen,
            'unseen' => $unseen,
            'total' => $total
        );
    }

    static function countIncommingReferral($id)
    {
        $date = Session::get('dateReportReferral');
        if (!$date) {
            $date = date('Y-m-d');
        }

        $start = Carbon::parse($date)->startOfDay();
        $end = Carbon::parse($date)->endOfDay();

        $accepted = Activity::where('action_md',$id)
            ->whereBetween('date_referred',[$start,$end])
            ->where('status','accepted')
            ->count();

        $redirected = Activity::where('action_md',$id)
            ->whereBetween('date_referred',[$start,$end])
            ->where('status','rejected')
            ->count();

        $seen = Seen::select('tracking_id',DB::raw('count(*) as total'))
            ->join('tracking','tracking.id','=','seen.tracking_id')
            ->where('seen.user_md',$id)
            ->whereBetween('tracking.date_referred',[$start,$end])
            ->groupBy('tracking_id')
            ->get();

        $cseen = (count($seen)- ($accepted+$redirected));
        $total = $cseen + $accepted + $redirected;


        return array(
            'accepted' => $accepted,
            'redirected' => $redirected,
            'seen' => $cseen,
            'total' => $total
        );
    }
}
