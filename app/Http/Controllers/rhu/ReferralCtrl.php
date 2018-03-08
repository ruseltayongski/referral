<?php

namespace App\Http\Controllers\rhu;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReferralCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('rhu');
    }

    public function index()
    {

        return view('rhu.referral',[
            'title' => 'Referral'
        ]);
    }
}
