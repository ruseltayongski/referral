<?php

namespace App\Http\Controllers\admin;

use App\Activity;
use App\Barangay;
use App\Facility;
use App\Login;
use App\Province;
use App\Seen;
use App\Tracking;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PatientCtrl extends Controller
{
    public function incoming()
    {

        $incomingData = \DB::connection('mysql')->select("call mergeTable()");

        return view('admin.report.incoming',
        [
            'title' => 'Incoming Patients',
            'data' => $incomingData
        ]);
    }

    public function outgoing()
    {
        $outgoingData = \DB::connection('mysql')->select("call mergeTable_outgoing()");
        return view('admin.report.outgoing',
        [
            'title' => 'Outgoing Patients',
            'data' => $outgoingData
        ]);
    }


}