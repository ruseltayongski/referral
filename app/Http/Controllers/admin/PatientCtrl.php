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
    public function incomingDateRange(Request $request)
    {
        return $date_from = date("Y-m-d",strtotime(explode('-', $request->date_range)[0]));
        $date_end = explode('-', $request->date_range)[1];

        return \DB::connection('mysql')->select("call mergeTable($date_from,$date_end)");

    }
    public function incoming()
    {
        $incomingData = \DB::connection('mysql')->select("call incomingMonitorPatient()");
        return view('admin.report.incoming',
            [
                'title' => 'Incoming Patients',
                'data' => $incomingData
            ]);
    }

    public function outgoing()
    {
        $outgoingData = \DB::connection('mysql')->select("call incomingMonitorPatient()");
        return view('admin.report.outgoing',
        [
            'title' => 'Outgoing Patients',
            'data' => $outgoingData
        ]);
    }


}