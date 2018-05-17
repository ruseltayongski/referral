<?php

namespace App\Http\Controllers\support;

use App\PatientForm;
use App\Tracking;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class HomeCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('support');
    }

    public function index()
    {
        return view('support.home',[
            'title' => 'Support: Dashboard'
        ]);
    }

    public function count()
    {
        $user = Session::get('auth');
        $facility_id = $user->facility_id;
        $tmp = User::where('facility_id',$facility_id)
                ->where('level','doctor');
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        $data['countDoctors'] = $tmp->count();
        $data['countOnline'] = $tmp->where(function($q){
                $q->where('login_status','login')
                    ->orwhere('login_status','login_off');
            })
            ->whereBetween('last_login',[$start,$end])
            ->count();
        $data['countReferral'] = Tracking::where('referred_from',$facility_id)->count();
        return array(
            'countDoctors' => number_format($data['countDoctors']),
            'countReferral' => number_format($data['countReferral']),
            'countOnline' => number_format($data['countOnline'])
        );
    }
}
