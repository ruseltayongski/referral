<?php

namespace App\Http\Controllers\doctor;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class TelemedicineCtrl extends Controller
{
    public function index()
    {
        return view('doctor.video-call');
    }
}
