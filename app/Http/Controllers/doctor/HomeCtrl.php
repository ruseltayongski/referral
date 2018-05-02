<?php

namespace App\Http\Controllers\doctor;

use App\Activity;
use App\Tracking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        return view('doctor.home');
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
}
