<?php

namespace App\Http\Controllers\admin;

use App\Facility;
use App\Muncity;
use App\Province;
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

    public function ProvinceView(Request $request){
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

        $data = Province::where('description',"like","%$keyword%")
            ->paginate(20);

        return view('admin.province.province',[
            'title' => 'List of Province',
            'data' => $data
        ]);
    }

    public function ProvinceBody(Request $request){
        $data = Province::find($request->province_id);
        return view('admin.province.province_body',[
            "data" => $data
        ]);
    }

    public function ProvinceAdd(Request $request){
        $data = $request->all();
        unset($data['_token']);

        if(isset($request->id)){
            Province::find($request->id)->update($data);
            Session::put('province_message','Successfully updated province');
        } else {
            Province::create($data);
            Session::put('province_message','Successfully added province');
        }

        Session::put('province',true);
        return Redirect::back();
    }

    public function ProvinceDelete(Request $request){
        Province::find($request->province_id)->delete();
        Session::put('province_message','Deleted province');
        Session::put('province',true);
        return Redirect::back();
    }

    public function MunicipalityView(Request $request,$province_id){
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

        $data = Muncity::where('description',"like","%$keyword%")
            ->where("province_id",$province_id)
            ->paginate(20);

        return view('admin.municipality.municipality',[
            'title' => 'List of Municipality',
            'province_name' => Province::find($province_id)->description,
            'province_id' => $province_id,
            'data' => $data
        ]);
    }

    public function MunicipalityBody(Request $request){
        $muncity = Muncity::where("id",$request->muncity_id)->where("province_id",$request->province_id)->first();
        $province = Province::find($request->province_id);
        return view('admin.municipality.municipality_body',[
            "muncity" => $muncity,
            "province_name" => $province->description,
            "province_id" => $request->province_id
        ]);
    }

    public function MunicipalityAdd(Request $request){
        $data = $request->all();
        unset($data['_token']);

        if(isset($request->id)){
            Muncity::find($request->id)->update($data);
            Session::put('municipality_message','Successfully updated municipality');
        } else {
            Muncity::create($data);
            Session::put('municipality_message','Successfully added municipality');
        }

        Session::put('municipality',true);
        return Redirect::back();
    }

    public function MunicipalityDelete(Request $request){
        Muncity::find($request->muncity_id)->delete();
        Session::put('municipality_message','Deleted municipality');
        Session::put('municipality',true);
        return Redirect::back();
    }

}
