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
        //for past 10 days
        $date_start = date('Y-m-d',strtotime(Carbon::now()->subDays(15))).' 00:00:00';
        $date_end = date('Y-m-d',strtotime(Carbon::now()->subDays(1))).' 23:59:59';
        //$past_days = \DB::connection('mysql')->select("call vaccine_past_report_call('$date_start','$date_end')");
        ///
        for($i=1; $i<=12; $i++)
        {
            $date = date('Y').'/'.$i.'/01';
            $startdate = Carbon::parse($date)->startOfMonth();
            $enddate = Carbon::parse($date)->endOfMonth();

            $sinovac = Vaccines::where("typeof_vaccine","Sinovac")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->count();
            $data['sinovac'][] = $sinovac;

            $astrazeneca = Vaccines::where("typeof_vaccine","Astrazeneca")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->count();
            $data['astrazeneca'][] = $astrazeneca;

            $moderna = Vaccines::where("typeof_vaccine","Moderna")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->count();
            $data['moderna'][] = $moderna;

            $pfizer = Vaccines::where("typeof_vaccine","Pfizer")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->count();
            $data['pfizer'][] = $pfizer;
        }

        $sinovac_count = Vaccines::where("typeof_vaccine","Sinovac")->count();
        $astrazeneca_count = Vaccines::where("typeof_vaccine","Astrazeneca")->count();
        $moderna_count = Vaccines::where("typeof_vaccine","Moderna")->count();
        $pfizer_count = Vaccines::where("typeof_vaccine","Pfizer")->count();

        return view("vaccine.dashboard",[
            "data" => $data,
            "sinovac_count" => $sinovac_count,
            "astrazeneca_count" => $astrazeneca_count,
            "moderna_count" => $moderna_count,
            "pfizer_count" => $pfizer_count
        ]);
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
                        $q->where('province_id','like',"%$search%");
                    })
                    ->whereBetween('dateof_del',[$date_start,$date_end])
                    ->orderBy('id', 'desc');

        Session::put("vaccine",$vaccine->get());
        $vaccine = $vaccine->paginate(15);
        $province = Province::get();


        return view("vaccine.vaccineview", [
            "vaccine" => $vaccine,
            "date_range_start" => $date_start,
            "date_range_end" => $date_end,
            "province" => $province
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
        if($request->first_dose)
             $vaccine->first_dose = date("Y-m-d H:m:i", strtotime($request->first_dose));
        if($request->second_dose)
             $vaccine->second_dose = date("Y-m-d H:m:i", strtotime($request->second_dose));
        if($request->dateof_del)
             $vaccine->dateof_del = date("Y-m-d H:m:i", strtotime($request->dateof_del));
        $vaccine->tgtdoseper_day = $request->tgtdoseper_day;
        $vaccine->numof_vaccinated = $request->numof_vaccinated;
        $vaccine->numof_vaccinated = $request->numof_vaccinated;
        $vaccine->aefi = $request->aefi;
        $vaccine->aefi_qty = $request->aefi_qty;
        $vaccine->deferred = $request->deferred;
        $vaccine->refused = $request->refused;
        $vaccine->wastage= $request->wastage;
        $vaccine->numof_vaccinated2 = $request->numof_vaccinated;
        if($request->dateof_del2)
            $vaccine->dateof_del2 = date("Y-m-d H:m:i", strtotime($request->dateof_del2));
        $vaccine->aefi2 = $request->aefi2;
        $vaccine->aefi_qty2 = $request->aefi_qty2;
        $vaccine->deferred2 = $request->deferred2;
        $vaccine->refused2 = $request->refused2;
        $vaccine->wastage2= $request->wastage2;
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
        if($request->first_dose)
            $vaccine->first_dose = date("Y-m-d H:m:i", strtotime($request->first_dose));
        else
            $vaccine->first_dose = null;
        if($request->second_dose)
            $vaccine->second_dose = date("Y-m-d H:m:i", strtotime($request->second_dose));
        else
            $vaccine->second_dose = null;
        $vaccine->dateof_del = date("Y-m-d H:m:i", strtotime($request->dateof_del));
        $vaccine->tgtdoseper_day = $request->tgtdoseper_day;
        $vaccine->numof_vaccinated = $request->numof_vaccinated;
        $vaccine->numof_vaccinated = $request->numof_vaccinated;
        $vaccine->aefi = $request->aefi;
        $vaccine->aefi_qty = $request->aefi_qty;
        $vaccine->deferred = $request->deferred;
        $vaccine->refused = $request->refused;
        $vaccine->wastage = $request->wastage;
        $vaccine->dateof_del2 = date("Y-m-d H:m:i", strtotime($request->dateof_del2));
        $vaccine->numof_vaccinated2 = $request->numof_vaccinated;
        $vaccine->aefi2 = $request->aefi2;
        $vaccine->aefi_qty2 = $request->aefi_qty2;
        $vaccine->deferred2 = $request->deferred2;
        $vaccine->refused2 = $request->refused2;
        $vaccine->wastage2= $request->wastage2;
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



