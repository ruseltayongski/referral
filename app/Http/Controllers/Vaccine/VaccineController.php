<?php

namespace App\Http\Controllers\Vaccine;

use App\Facility;
use App\Muncity;
use App\Province;
use App\Vaccines;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class VaccineController extends Controller
{
    public function index()
    {
        return view("vaccine.dashboard");
    }

    public function vaccineView(Request $request)
    {
        if (isset($request->date_range)) {
            $date_start = date('Y-m-d', strtotime(explode(' - ', $request->date_range)[0])) . ' 00:00:00';
            $date_end = date('Y-m-d', strtotime(explode(' - ', $request->date_range)[1])) . ' 23:59:59';
        } else {
            $date_start = Carbon::now()->startOfYear()->format('Y-m-d') . ' 00:00:00';
            $date_end = Carbon::now()->endOfMonth()->format('Y-m-d') . ' 23:59:59';
        }
        $search = $request->search;

        $vaccine = Vaccines::
                    where(function($q) use ($search){
                        $q->where('ownership','like',"%$search%");
                    })
                    ->whereBetween('first_dose',[$date_start,$date_end])
                    ->orderBy('id', 'desc');

        Session::put("vaccine",$vaccine->get());
        $vaccine = $vaccine->paginate(15);

        return view("vaccine.vaccineview", [
            "vaccine" => $vaccine,
            "date_range_start"=>$date_start,
            "date_range_end"=>$date_end,
        ]);

    }

    public function vaccinatedContent()
    {
        $province = Province::get();
        $muncity = Muncity::get();
        $facility = Facility::get();

        return view("vaccine.vaccinated_content", [
            "province" => $province,
            "muncity" => $muncity,
            "facility" => $facility,
        ]);
    }

    public function vaccineSaved(Request $request)
    {
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
        $vaccine->first_dose = date("Y-m-d H:m:i", strtotime($request->first_dose));
        $vaccine->second_dose = date("Y-m-d H:m:i", strtotime($request->second_dose));
        $vaccine->dateof_del = date("Y-m-d H:m:i", strtotime($request->dateof_del));
        $vaccine->tgtdoseper_day = $request->tgtdoseper_day;
        $vaccine->numof_vaccinated = $request->numof_vaccinated;
        $vaccine->numof_vaccinated = $request->numof_vaccinated;
        $vaccine->aef1 = $request->aef1;
        $vaccine->aef1_qty = $request->aef_qty;
        $vaccine->deferred = $request->deferred;
        $vaccine->refused = $request->refused;
        $vaccine->percent_coverage = $request->percent_coverage;
        $vaccine->save();

        Session::put('vaccine_saved', true);

        return Redirect::back();
    }

    public function vaccineUpdateView($id)
    {
        $province = Province::get();
        $vaccine = Vaccines::find($id);
        $muncity = Muncity::where('province_id', $vaccine->province_id)->get();
        $facility = Facility::where('province', $vaccine->province_id)->get();

        return view("vaccine.vaccinated_content", [
            "province" => $province,
            "muncity" => $muncity,
            "facility" => $facility,
            "vaccine" => $vaccine,
        ]);
    }

    public function vaccineUpdate(Request $request)
    {
        $vaccine = Vaccines::find($request->vaccine_id);
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
        $vaccine->first_dose = date("Y-m-d H:m:i", strtotime($request->first_dose));
        $vaccine->second_dose = date("Y-m-d H:m:i", strtotime($request->second_dose));
        $vaccine->dateof_del = date("Y-m-d H:m:i", strtotime($request->dateof_del));
        $vaccine->tgtdoseper_day = $request->tgtdoseper_day;
        $vaccine->numof_vaccinated = $request->numof_vaccinated;
        $vaccine->numof_vaccinated = $request->numof_vaccinated;
        $vaccine->aef1 = $request->aef1;
        $vaccine->aef1_qty = $request->aef_qty;
        $vaccine->deferred = $request->deferred;
        $vaccine->refused = $request->refused;
        $vaccine->percent_coverage = $request->percent_coverage;
        $vaccine->save();

        Session::put('vaccine_update', true);

        return Redirect::back();
    }

    public function getFacility($province_id)
    {
        return Facility::where('province', $province_id)->get();
    }

    public function exportExcel()
    {
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=vaccine.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        $vaccine = Session::get("vaccine");
        return view("vaccine.vaccine_excel", [
            "vaccine" => $vaccine,
        ]);
    }


}



