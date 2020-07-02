<?php

namespace App\Http\Controllers\Eoc;

use App\Bed;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Facility;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('eoc_region');
    }

    public function index()
    {
        $facility = Facility::select("facility.*","province.description as province")
                            ->leftJoin("province","province.id","=","facility.province")
                            ->get();
        return view('eoc.home',[
            "data" => $facility
        ]);
    }

    public function bed($facility_id){
        $bed = Bed::where("facility_id",$facility_id)->get();
        return view('eoc.bed',[
            "facility_id" => $facility_id,
            "bed" => $bed
        ]);
    }

    public function bedAdd(Request $request){
        $bed = new Bed();
        $bed->encoded_by = Session::get("auth")->id;
        $bed->facility_id = $request->facility_id;
        $bed->name = $request->name;
        $bed->temporary = $request->temporary;
        $bed->allowable_no = $request->allowable;
        $bed->actual_no = $request->actual;
        $bed->status = 'Active';
        $bed->save();

        Session::put('bed',true);
        return Redirect::back();
    }

}
