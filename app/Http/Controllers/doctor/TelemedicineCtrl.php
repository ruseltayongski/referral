<?php

namespace App\Http\Controllers\doctor;


use App\Tracking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class TelemedicineCtrl extends Controller
{
    public function index(Request $req)
    {
        $type = Tracking::select('type')->where('id', $req->id)->first()->type;
        return view('doctor.video-call', ['referral_type'=>$type]);
    }
}
