<?php

namespace App\Http\Controllers\Vaccine;

use App\Facility;
use App\Muncity;
use App\Province;
use App\Vaccines;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class VaccineController extends Controller
{
    public function index(){
        return view("vaccine.dashboard");
    }

    public function vaccineView(){
        $vaccine = Vaccines::orderBy('id','desc')->paginate(15);
        return view("vaccine.vaccineview",[
            "vaccine"=>$vaccine,
        ]);

    }

    public function vaccinatedContent(){
        $province = Province::get();
        $muncity = Muncity::get();
        $facility = Facility::get();


        return view("vaccine.vaccinated_content",[
            "province" => $province,
            "muncity" => $muncity,
            "facility"=> $facility,
        ]);
    }

    public function vaccineSaved(Request $request){
        $vaccine = new Vaccines();
        $vaccine->encoded_by = Session::get('auth')->id;
        $vaccine->facility_id = $request->facility_id;
        $vaccine->typeof_vaccine = $request->typeof_vaccine;
        $vaccine->priority = $request->priority;
        $vaccine->sub_priority = $request->sub_priority;
        $vaccine->province_id = $request->province_id;
        $vaccine->muncity_id = $request->muncity_id;
        $vaccine->no_eli_pop = $request->no_eli_pop;
        $vaccine->ownership = $request->ownership;
        $vaccine->nvac_allocated = $request->nvac_allocated;
        $vaccine->first_dose = date("Y-m-d H:m:i",strtotime($request->first_dose));
        $vaccine->second_dose = date("Y-m-d H:m:i",strtotime($request->second_dose));
        $vaccine->dateof_del = date("Y-m-d H:m:i",strtotime($request->dateof_del));
        $vaccine->tgtdoseper_day = $request->tgtdoseper_day;
        $vaccine->numof_vaccinated = $request->numof_vaccinated;
        $vaccine->numof_vaccinated = $request->numof_vaccinated;
        $vaccine->aef1 = $request->aef1;
        $vaccine->aef1_qty = $request->aef_qty;
        $vaccine->deferred = $request->deferred;
        $vaccine->refused = $request->refused;
        $vaccine->percent_coverage = $request->percent_coverage;
        $vaccine->save();

        Session::put('vaccine_saved',true);

        return Redirect::back();
    }

    public function vaccineUpdateView($id)
    {
        $province = Province::get();
        $muncity = Muncity::get();
        $facility = Facility::get();
        $vaccine = Vaccines::find($id);

        return view("vaccine.vaccinated_content", [
            "province" => $province,
            "muncity" => $muncity,
            "facility" => $facility,
            "vaccine" =>$vaccine,
        ]);
    }

}


