<?php

namespace App\Http\Controllers\Opcen;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inventory;
use Illuminate\Support\Facades\Session;

class OpcenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function Opcen(){
        return view('opcen.opcen');
    }

    public function newClient(){
        return view('opcen.new_client');
    }

    public function bedAvailable(){
        return view('opcen.bed_available');
    }

    public function newCall(){
        return view('opcen.new_call');
    }

    public function repeatCall(){
        return view('opcen.repeat_call');
    }

    public function reasonCalling($reason){
        return view('opcen.reason_calling',[
            "reason" => $reason
        ]);
    }

    public function availabilityAndService(){
        $inventory = Inventory::select("inventory.*","facility.name as facility","facility.province as province","facility.id as facility_id")
            ->leftJoin("facility","facility.id","=","inventory.facility_id")
            ->where("facility.name","not like","%RHU%")
            ->where("facility.name","not like","%department%")
            ->where("facility.name","not like","%referred%")
            ->orderBy("facility.province","asc")
            ->orderBy("facility.name","asc")
            ->orderBy("inventory.name","asc")
            ->get();

        Session::put("inventory",$inventory);

        return view('eoc.eoc_region',[
            "inventory" => $inventory
        ]);
    }

}
