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
        $date_from = date("Y-m-d",strtotime(explode('-', $request->date_range)[0]));
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

    public function consolidatedIncoming()
    {
        $incomingData = \DB::connection('mysql')->select("call consolidatedIncoming()");
        return view('admin.report.consolidated_incoming',
            [
                'title' => 'INCOMING REFERRAL CONSOLIDATION TABLE  (Within Province Wide Health System)',
                'data' => $incomingData
            ]);
    }

    public function consolidatedOutgoing()
    {
        $outgoingData = \DB::connection('mysql')->select("call incomingMonitorPatient()");
        return view('admin.report.consolidated_outgoing',
            [
                'title' => 'Outgoing Patients',
                'data' => $outgoingData
            ]);
    }

    public function consolidatedIncomingv2(Request $request)
    {
        if($request->isMethod('post') && isset($request->date_range)){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = Carbon::now()->startOfYear()->format('Y-m-d').' 00:00:00';
            $date_end = Carbon::now()->endOfYear()->format('Y-m-d').' 23:59:59';
        }

        $facility_id = Session::get("auth")->facility_id;
        Session::get("auth")->level == "mcc" ? $stored_name = "consolidatedIncomingMcc('$date_start','$date_end','$facility_id')" : $stored_name = "consolidatedIncoming('$date_start','$date_end')";
        $incomingData = \DB::connection('mysql')->select("call $stored_name");

        Session::put('data',$incomingData);
        return view('admin.report.consolidated_incomingv2',
        [
            'title' => 'INCOMING REFERRAL CONSOLIDATION TABLE  (Within Province Wide Health System)',
            'data' => $incomingData,
            'date_range_start' => $date_start,
            'date_range_end' => $date_end
        ]);
    }

}