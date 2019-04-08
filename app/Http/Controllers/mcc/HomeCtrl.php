<?php

namespace App\Http\Controllers\mcc;

use App\Login;
use App\Tracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class HomeCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('chief');
    }

    public function index()
    {
        return view('mcc.home',[
            'title' => 'Medical Center Chief'
        ]);
    }

    public function count()
    {
        $user = Session::get('auth');
        $facility_id = $user->facility_id;
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $tmp = User::where('facility_id',$facility_id)
            ->where('level','doctor');

        $data['countDoctors'] = $tmp->count();

        $data['countReferral'] = Tracking::where('referred_from',$facility_id)
                                    ->whereBetween('date_referred',[$start,$end])
                                    ->count();
        return array(
            'countDoctors' => number_format($data['countDoctors']),
            'countReferral' => number_format($data['countReferral']),
            'countOnline' => number_format(self::countOnline($facility_id))
        );
    }

    static function countOnline($facility_id)
    {
        $start = Carbon::now()->startOfDay();
        $end = Carbon::now()->endOfDay();

        $data = Login::where(function($q) {
                $q->where('login.status','login')
                    ->orwhere('login.status','login_off');
            })
            ->join('users','users.id','=','login.userId')
            ->where('users.level','doctor')
            ->whereBetween('login.login',[$start,$end])
            ->where('login.logout','0000-00-00 00:00:00')
            ->where('users.facility_id',$facility_id)
            ->count();
        return $data;
    }
}
