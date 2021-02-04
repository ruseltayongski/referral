<?php

namespace App\Http\Controllers\admin;

use App\Facility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class FacilityCtrl extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('admin');
        //$this->middleware('doctor');
    }


    public function index(Request $request)
    {
        if($request->view_all == 'view_all')
            $keyword = '';
        else{
            if(Session::get("keyword")){
                if(!empty($request->keyword) && Session::get("keyword") != $request->keyword)
                    $keyword = $request->keyword;
                else
                    $keyword = Session::get("keyword");
            } else {
                $keyword = $request->keyword;
            }
        }

        Session::put('keyword',$keyword);

        $data = Facility::select(
            "facility.id",
            'facility.facility_code',
            "facility.name",
            "facility.address",
            "prov.description as province",
            "mun.description as muncity",
            "bar.description as barangay",
            "facility.contact",
            "facility.email",
            "facility.chief_hospital",
            "facility.level",
            "facility.hospital_type",
            "facility.status"
        ) ->leftJoin("province as prov","prov.id","=","facility.province")
         ->leftJoin("muncity as mun","mun.id","=","facility.muncity")
         ->leftJoin("barangay as bar","bar.id","=","facility.brgy");

        $data = $data->where('facility.name',"like","%$keyword%");

        $data = $data->orderBy('name','asc')
            ->paginate(20);

        return view('admin.facility',[
            'title' => 'List of Facility',
            'data' => $data
        ]);
    }

    public function FacilityAdd(Request $request){
        $data = $request->all();
        unset($data['_token']);

        if(isset($request->id)){
            Facility::find($request->id)->update($data);
            Session::put('facility_message','Successfully updated facility');
        } else {
            Facility::create($data);
            Session::put('facility_message','Successfully added facility');
        }

        Session::put('facility',true);
        return Redirect::back();
    }

    public function FacilityBody(Request $request){
        $data = Facility::find($request->facility_id);
        return view('admin.facility_body',[
            "data" => $data
        ]);
    }

    public function FacilityDelete(Request $request){
        Facility::find($request->facility_id)->delete();
        Session::put('facility_message','Deleted facility');
        Session::put('facility',true);
        return Redirect::back();
    }

}
