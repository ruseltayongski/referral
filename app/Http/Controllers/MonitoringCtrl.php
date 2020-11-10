<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facility;

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

        return view('admin.monitoring',[
            "pending_activity" => $pending_activity,
            "date_start" => $date_start,
            "date_end" => $date_end
        ]);
    }
}
