<?php

namespace App\Http\Controllers\mcc;

use App\Facility;
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
        $this->middleware('chief');
    }

    public function online()
    {
        $user = Session::get('auth');

        $start = date('m/d/Y');
        $end = date('m/d/Y');

        $date = Session::get('date_online');
        if($date){
            $range = explode('-',str_replace(' ', '', $date));
            $start = $range[0];
            $end = $range[1];
        }

        $data = User::select('department.description',
                    'department.id'
                )
                ->leftJoin('department','department.id','=','users.department_id')
                ->where('users.facility_id',$user->facility_id)
                ->where('users.level','doctor')
                ->groupBy('users.department_id')
                ->get();
        return view('mcc.online',[
            'title' => 'Online Users per Department',
            'data' => $data,
            'start' => $start,
            'end' => $end
        ]);
    }

    public function filterOnline(Request $req)
    {
        Session::put('date_online',$req->date);
        return redirect()->back();
    }

    public function incoming()
    {
        $user = Session::get('auth');

        $start = Carbon::now()->startOfMonth()->format('m/d/Y');
        $end = Carbon::now()->endOfMonth()->format('m/d/Y');

        $date = Session::get('date_incoming');
        if($date){
            $range = explode('-',str_replace(' ', '', $date));
            $start = $range[0];
            $end = $range[1];
        }

        $data = Facility::orderBy('name','asc')
            ->where('id','!=',$user->facility_id)
            ->where('status',1)
            ->get();

        return view('mcc.incoming',[
            'title' => 'Incoming Referrals',
            'data' => $data,
            'start' => $start,
            'end' => $end
        ]);
    }

    public function filterIncoming(Request $req)
    {
        Session::put('date_incoming',$req->date);
        return redirect()->back();
    }

    static function countIncoming($status,$facility_id)
    {
        $user = Session::get('auth');
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $date = Session::get('date_incoming');
        if($date){
            $range = explode('-',str_replace(' ', '', $date));
            $start = Carbon::parse($range[0])->startOfDay();
            $end = Carbon::parse($range[1])->endOfDay();
        }

        $ref = Tracking::where('tracking.referred_to',$user->facility_id)
                    ->where('tracking.referred_from',$facility_id)
                    ->whereBetween('tracking.date_referred',[$start,$end]);

        if($status){
            $ref = $ref->join('activity','activity.code','=','tracking.code')
                        ->where('activity.status',$status)
                        ->distinct('activity.code')
                        ->count('activity.code');
        }else{
            $ref = $ref->count();
        }

        return $ref;
    }

    static function countLogin($status,$department_id)
    {
        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();

        $u = Session::get('auth');
        $date = Session::get('date_online');
        if($date){
            $range = explode('-',str_replace(' ', '', $date));
            $start = Carbon::parse($range[0])->startOfDay();
            $end = Carbon::parse($range[1])->endOfDay();
        }

        $users = User::where("users.level",'doctor')
                    ->where('users.department_id',$department_id)
                    ->where('users.facility_id',$u->facility_id);

        if($status){
            $users = $users->join('login','login.userId','=','users.id')
                        ->where('login.status',$status)
                        ->whereBetween('login.login',[$start,$end])
                        ->distinct('login.userId')
                        ->count('login.userId');
        }else{
            $users = $users->count();
        }

        return $users;


    }
}
