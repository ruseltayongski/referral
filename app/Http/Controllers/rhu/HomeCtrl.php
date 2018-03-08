<?php

namespace App\Http\Controllers\rhu;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('rhu');
    }

    public function index()
    {

        return view('rhu.home');
    }
}
