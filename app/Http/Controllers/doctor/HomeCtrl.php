<?php

namespace App\Http\Controllers\doctor;

use App\Activity;
use App\Http\Controllers\ParamCtrl;
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
        ParamCtrl::lastLogin();
        return view('doctor.home');
    }


}
