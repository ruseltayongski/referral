<?php

namespace App\Http\Controllers\Vaccine;

use App\Facility;
use App\Muncity;
use App\Province;
use App\Vaccines;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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
        $no_eli_pop = Vaccines::select(DB::raw("sum(no_eli_pop) as no_eli_pop"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->no_eli_pop;
        $numof_vaccine_allocated = Vaccines::select(DB::raw("sum(nvac_allocated) as nvac_allocated"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->nvac_allocated;

        $numof_vaccinated_first = Vaccines::select(DB::raw("sum(numof_vaccinated) as numof_vaccinated"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->numof_vaccinated;
        $numof_vaccinated_second = Vaccines::select(DB::raw("sum(numof_vaccinated2) as numof_vaccinated2"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->numof_vaccinated2;
        $aefi_qty_first = Vaccines::select(DB::raw("sum(aefi_qty) as aefi_qty"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->aefi_qty;
        $aefi_qty_second = Vaccines::select(DB::raw("sum(aefi_qty2) as aefi_qty2"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->aefi_qty2;
        $targetdose_perday = Vaccines::select(DB::raw("sum(tgtdoseper_day) as tgtdoseper_day"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->tgtdoseper_day;
        $total_deferred_first = Vaccines::select(DB::raw("sum(deferred) as deferred"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->deferred;
        $total_deferred_second = Vaccines::select(DB::raw("sum(deferred2) as deferred2"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->deferred2;
        $total_refused_first = Vaccines::select(DB::raw("sum(refused) as refused"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->refused;
        $total_refused_second = Vaccines::select(DB::raw("sum(refused2) as refused2"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->refused2;
        $total_wastage_first = Vaccines::select(DB::raw("sum(wastage) as wastage"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->wastage;
        $total_wastage_second = Vaccines::select(DB::raw("sum(wastage2) as wastage2"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->wastage2;
        /*$totalpercent_coverage_first = Vaccines::select(DB::raw("(sum((numof_vaccinated+no_eli_pop) * 100 )) as percent_coverage"))
                                ->whereBetween('dateof_del',[$date_start,$date_end])
                                ->first()
                                ->percent_coverage;*/

        return view("vaccine.vaccineview", [
            "vaccine" => $vaccine,
            "date_range_start" => $date_start,
            "date_range_end" => $date_end,
            "province" => $province,
            "number_eligible_pop_total" => $no_eli_pop,
            "numof_vaccinated_total_first" => $numof_vaccinated_first,
            "numof_vaccinated_total_second" => $numof_vaccinated_second,
            "numof_vaccine_allocated" => $numof_vaccine_allocated,
            "aefi_qty_first" => $aefi_qty_first,
            "aefi_qty_second" => $aefi_qty_second,
            "targetdose_perday" => $targetdose_perday,
            "total_deferred_first" => $total_deferred_first,
            "total_deferred_second" => $total_deferred_second,
            "total_refused_first" => $total_refused_first,
            "total_refused_second" => $total_refused_second,
            "total_wastage_first" => $total_wastage_first,
            "total_wastage_second" => $total_wastage_second

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

    public function vaccinatedContentMunicipality($id)
    {
        $province = Province::get();
        $vaccine = Vaccines::find($id);
        $muncity = Muncity::where('province_id', $vaccine->province_id)->get();
        $facility = Facility::where('province', $vaccine->province_id)->get();

        return view("vaccine.vaccine_content_municipality", [
            "province" => $province,
            "muncity" => $muncity,
            "facility" => $facility,
            "vaccine" => $vaccine,
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
        $vaccine->numof_vaccinated2 = $request->numof_vaccinated2;
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
        $vaccine->numof_vaccinated2 = $request->numof_vaccinated2;
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

    public function vaccineTbodyContent($count=null){
        return view('vaccine.tbody_content',[
            "count" => $count
        ]);
    }

    public function vaccineNewDelivery($id)
    {
        $province = Province::get();
        $vaccine = Vaccines::find($id);
        $muncity = Muncity::where('province_id', $vaccine->province_id)->get();
        $facility = Facility::where('province', $vaccine->province_id)->get();

        return view("vaccine.new_delivery", [
            "province" => $province,
            "muncity" => $muncity,
            "facility" => $facility,
            "vaccine" => $vaccine,
        ]);
    }

    public function vaccineNewDeliverySaved(Request $request){

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
        $vaccine->numof_vaccinated2 = $request->numof_vaccinated2;
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
}



