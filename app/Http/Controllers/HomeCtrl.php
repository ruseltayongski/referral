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
        for($i=1; $i<=12; $i++)
        {
            $date = date('Y').'/'.$i.'/01';
            $startdate = Carbon::parse($date)->startOfMonth();
            $enddate = Carbon::parse($date)->endOfMonth();

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
        return $data;
    }

    public function adminChart()
    {
        for($i=1; $i<=12; $i++)
        {
            $date = date('Y').'/'.$i.'/01';
            $startdate = Carbon::parse($date)->startOfMonth();
            $enddate = Carbon::parse($date)->endOfMonth();

            $accepted = Activity::where(function($q){
                    $q->where('status','accepted')
                        ->orwhere('status','admitted')
                        ->orwhere('status','arrived');
                })
                ->whereBetween('date_referred',[$startdate,$enddate])
                ->groupBy('code')
                ->get();
            $data['accepted'][] = count($accepted);

            $redirected = Activity::where(function($q){
                $q->where('status','redirected')
                    ->orwhere('status','rejected');
            })
                ->whereBetween('date_referred',[$startdate,$enddate])
                ->groupBy('code')
                ->get();
            $data['rejected'][] = count($redirected);
        }
        return $data;
    }

    public function sample()
    {

    }
}
