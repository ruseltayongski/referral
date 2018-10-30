<?php

namespace App\Http\Controllers\support;

use App\Login;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
}
