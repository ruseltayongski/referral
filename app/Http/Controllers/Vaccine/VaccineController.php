<?php

namespace App\Http\Controllers\Vaccine;

use App\Facility;
use App\Muncity;
use App\Province;
use App\VaccineAccomplished;
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

    public function vaccineView(Request $request,$province_id)
    {
        if($request->view_all == 'view_all')
            $keyword = '';
        else{
            if(Session::get("keyword_muncity")){
                if(!empty($request->keyword_muncity) && Session::get("keyword_muncity") != $request->keyword_muncity)
                    $keyword = $request->keyword_muncity;
                else
                    $keyword = Session::get("keyword_muncity");
            } else {
                $keyword = $request->keyword_muncity;
            }
        }

        Session::put('keyword_muncity',$keyword);

        $data = Muncity::where('description',"like","%$keyword%")
            ->where("province_id",$province_id)
            ->orderBy("description","asc")
            ->paginate(10);

        return view('vaccine.vaccineview',[
            'title' => 'List of Municipality',
            'province_name' => Province::find($province_id)->description,
            'province_id' => $province_id,
            'data' => $data
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

    public function vaccinatedContentMunicipality($province_id,$muncity_id)
    {    $vaccine_accomplishment = VaccineAccomplished::where('muncity_id',$muncity_id)->orderBy('id','asc')->get();
        return view("vaccine.vaccine_content_municipality", [
            "province_id" => $province_id,
            "muncity_id" => $muncity_id,
            "vaccine_accomplishment" => $vaccine_accomplishment
        ]);
    }

    public function vaccineSaved(Request $request)
    {
        VaccineAccomplished::where("province_id",$request->province_id)->where("muncity_id",$request->muncity_id)->delete();
        $user_id = Session::get('auth')->id;
        $count = 0;
        foreach ($request->typeof_vaccine as $row){
            $vaccine = new VaccineAccomplished();
            $vaccine->encoded_by = $user_id;
            $vaccine->province_id = $request->province_id;
            $vaccine->muncity_id = $request->muncity_id;
            $vaccine->typeof_vaccine = $request->typeof_vaccine[$count];
            $vaccine->priority = $request->priority[$count];
            if($request->date_first[$count])
                $vaccine->date_first = date("Y-m-d H:m:i", strtotime($request->date_first[$count]));
            if($request->date_second[$count])
                $vaccine->date_second = date("Y-m-d H:m:i", strtotime($request->date_second[$count]));
            $vaccine->vaccinated_first = $request->vaccinated_first[$count];
            $vaccine->vaccinated_second = $request->vaccinated_second[$count];
            $vaccine->mild_first = $request->mild_first[$count];
            $vaccine->mild_second = $request->mild_second[$count];
            $vaccine->serious_first = $request->serious_first[$count];
            $vaccine->serious_second = $request->serious_second[$count];
            $vaccine->refused_first = $request->refused_first[$count];
            $vaccine->refused_second = $request->refused_second[$count];
            $vaccine->deferred_first = $request->deferred_first[$count];
            $vaccine->deferred_second = $request->deferred_second[$count];
            $vaccine->wastage_first = $request->wastage_first[$count];
            $vaccine->wastage_second = $request->wastage_second[$count];
            $vaccine->no_eli_pop = $request->no_eli_pop[$count];
            $vaccine->vaccine_allocated_first = $request->vaccine_allocated_first[$count];
            $vaccine->vaccine_allocated_second = $request->vaccine_allocated_second[$count];
            $vaccine->save();
            $count++;
        }


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

    public function getEliPop($muncity_id,$priority){
        $no_eli_pop = Muncity::find($muncity_id);
        if($priority == 'frontline_health_workers')
            return $no_eli_pop->frontline_health_workers;

        return $no_eli_pop->senior_citizens;
    }

    public function getVaccineAllocated($muncity_id,$typeof_vaccine){
        $vaccine_allocated = Muncity::find($muncity_id);
        if($typeof_vaccine == 'Sinovac'){
            $data[0] = $vaccine_allocated->sinovac_allocated_first;
            $data[1] = $vaccine_allocated->sinovac_allocated_second;
        }else{
            $data[0] = $vaccine_allocated->astrazeneca_allocated_first;
            $data[1] = $vaccine_allocated->astrazeneca_allocated_second;
        }
        return $data;
    }

    public function getVallocatedModal(Request $request)
    {
        $muncity = Muncity::where("id", $request->muncity_id)->where("province_id", $request->province_id)->first();
        $province = Province::find($request->province_id);
        return view('vaccine.vaccine_allocated_modal', [
            "muncity" => $muncity,
            "province_name" => $province->description,
            "province_id" => $request->province_id
        ]);
    }
}



