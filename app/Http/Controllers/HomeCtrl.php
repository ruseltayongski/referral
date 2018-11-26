<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Tracking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Session::get('auth');
        if($user->level=='doctor'){
            return redirect('/doctor');
        }else if($user->level=='support'){
            return redirect('/support');
        }else if($user->level=='chief'){

        }else{
            Session::flush();
            return redirect('/');
        }
    }

    public function chart()
    {
        $user = Session::get('auth');
        $facility_id = '';
        $data = array();
        for($i=1; $i<=12; $i++){
            $new = str_pad($i, 2, '0', STR_PAD_LEFT);
            $current = '01.'.$new.'.'.date('Y');

            $startdate = date('Y-m-d',strtotime($current)).' 00:00:00';
            $end = '01.'.($new+1).'.'.date('Y');
            if($new==12){
                $end = '01/01/'.date('Y',strtotime("+1 year"));
            }
            $enddate = date('Y-m-d',strtotime($end)).' 23:59:59';;
            $data['accepted'][] = Tracking::where('status','accepted')
                ->where('referred_to',$user->facility_id)
                ->where('date_accepted','>=',$startdate)
                ->where('date_accepted','<=',$enddate)
                ->count();

            $data['rejected'][] = Activity::where(function($q){
                $q->where('status','redirected')
                    ->orwhere('status','rejected');
            })
                ->where('referred_to',$user->facility_id)
                ->where('date_referred','>=',$startdate)
                ->where('date_referred','<=',$enddate)
                ->count();
        }
        return $data;
    }

    public function adminChart()
    {
        for($i=1; $i<=12; $i++)
        {
            $date = date('Y').'/'.$i.'/01';
            $startdate = Carbon::parse($date)->startOfMonth();
            $enddate = Carbon::parse($date)->endOfMonth();

            $data['accepted'][] = Activity::where(function($q){
                $q->where('status','accepted')
                    ->orwhere('status','admitted')
                    ->orwhere('status','arrived');
            })
                ->whereBetween('date_referred',[$startdate,$enddate])
                ->count();

            $data['rejected'][] = Activity::where(function($q){
                $q->where('status','redirected')
                    ->orwhere('status','rejected');
            })
                ->whereBetween('date_referred',[$startdate,$enddate])
                ->count();
        }
        return $data;
    }

    public function sample()
    {

    }
}
