<?php

namespace App\Http\Controllers\doctor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReferralCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('doctor');
    }

    public function index()
    {
        return view('doctor.referral',[
            'title' => 'Incoming Patients'
        ]);
    }

    public function referred()
    {
        return view('doctor.referred',[
            'title' => 'Referred Patients'
        ]);
    }
}
