<?php

namespace App\Http\Controllers;

use App\MonitoringNotAccepted;
use Illuminate\Http\Request;
use App\Facility;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class MonitoringCtrl extends Controller
{
    public function monitoring(Request $request){
        if($request->isMethod('post') && isset($request->date_range)){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = date('Y-m-d').' 00:00:00';
            $date_end = date('Y-m-d').' 23:59:59';
        }

        $pending_activity = \DB::connection('mysql')->select("call monitoring('$date_start','$date_end')");
        return view('monitoring.monitoring',[
            "pending_activity" => $pending_activity,
            "date_start" => $date_start,
            "date_end" => $date_end
        ]);
    }

    public function bodyRemark(Request $request){
        return view('monitoring.monitoring_remark',[
            "activity_id" => $request->activity_id,
            "code" => $request->code,
            "remark_by" => $request->remark_by,
            "referring_facility" => $request->referring_facility,
            "referred_to" => $request->referred_to
        ]);
    }

    public function addRemark(Request $request){
        $monitoring_not_accepted = new MonitoringNotAccepted();
        $monitoring_not_accepted->code = $request->code;
        $monitoring_not_accepted->remark_by = Session::get('auth')->id;
        $monitoring_not_accepted->activity_id = $request->activity_id;
        $monitoring_not_accepted->referring_facility = $request->referring_facility;
        $monitoring_not_accepted->referred_to = $request->referred_to;
        $monitoring_not_accepted->remarks = $request->remarks;
        $monitoring_not_accepted->save();

        Session::put("add_remark",true);
        return Redirect::back();
    }

}
