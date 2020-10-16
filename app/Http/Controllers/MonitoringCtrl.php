<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facility;

class MonitoringCtrl extends Controller
{
    public function monitoring(){
        $pending_activity = \DB::connection('mysql')->select("call monitoring()");

        return view('admin.monitoring',[
            "pending_activity" => $pending_activity
        ]);
    }
}
