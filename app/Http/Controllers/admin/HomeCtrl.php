<?php

namespace App\Http\Controllers\admin;

use App\Facility;
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
        $this->middleware('admin');
    }

    public function index()
    {
        return view('admin.home',[
            'title' => 'Admin: Dashboard'
        ]);
    }

    public function count()
    {
        $user = Session::get('auth');
        $facility_id = $user->facility_id;
        $tmp = User::where('level','doctor');
        $data['countDoctors'] = $tmp->count();
        $data['countOnline'] = $tmp->where(function($q){
                $q->where('login_status','login')
                    ->orwhere('login_status','login_off');
            })
            ->count();
        $data['countReferral'] = Tracking::count();
        $countFacility = Facility::where('status',1)->count();
        return array(
            'countDoctors' => number_format($data['countDoctors']),
            'countReferral' => number_format($data['countReferral']),
            'countOnline' => number_format($data['countOnline']),
            'countFacility' => number_format($countFacility)
        );
    }
}
