<?php

namespace App\Http\Controllers\doctor;

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

            $startdate = date('Y-m-d',strtotime($current));
            $end = '01.'.($new+1).'.'.date('Y');
            if($new==12){
                $end = '01/01/'.date('Y',strtotime("+1 year"));
            }
            $enddate = date('Y-m-d',strtotime($end));
            $data['accepted'][] = 0;
        }
        return $data;
    }
}
