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
        //for past 15 days
        $date_start = date('Y-m-d',strtotime(Carbon::now()->subDays(15))).' 00:00:00';
        $date_end = date('Y-m-d',strtotime(Carbon::now()->subDays(1))).' 23:59:59';
        $sinovac_past = \DB::connection('mysql')->select("call vaccine_past_vaccinated('$date_start','$date_end','Sinovac')");
        $astra_past = \DB::connection('mysql')->select("call vaccine_past_vaccinated('$date_start','$date_end','Astrazeneca')");
        $moderna_past = \DB::connection('mysql')->select("call vaccine_past_vaccinated('$date_start','$date_end','Moderna')");
        $pfizer_past = \DB::connection('mysql')->select("call vaccine_past_vaccinated('$date_start','$date_end','Pfizer')");
        ///
        for($i=1; $i<=12; $i++)
        {
            $date = date('Y').'/'.$i.'/01';
            $startdate = Carbon::parse($date)->startOfMonth();
            $enddate = Carbon::parse($date)->endOfMonth();

            $sinovac = VaccineAccomplished::select(DB::raw("sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)) as sinovac_count"))
                ->where("typeof_vaccine","Sinovac")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->first()
                ->sinovac_count;
            $data['sinovac'][] = $sinovac;

            $astrazeneca = VaccineAccomplished::select(DB::raw("sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)) as astra_count"))
                ->where("typeof_vaccine","Astrazeneca")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->first()
                ->astra_count;
            $data['astrazeneca'][] = $astrazeneca;

            $moderna = VaccineAccomplished::select(DB::raw("sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)) as moderna_count"))
                ->where("typeof_vaccine","Moderna")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->first()
                ->moderna_count;
            $data['moderna'][] = $moderna;

            $pfizer = VaccineAccomplished::select(DB::raw("sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)) as pfizer_count"))
                ->where("typeof_vaccine","Pfizer")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->first()
                ->pfizer_count;
            $data['pfizer'][] = $pfizer;
        }

        $sinovac_count = VaccineAccomplished::select(DB::raw("sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)) as sinovac_count"))->where("typeof_vaccine","Sinovac")->first()->sinovac_count;
        $astrazeneca_count = VaccineAccomplished::select(DB::raw("sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)) as astra_count"))->where("typeof_vaccine","Astrazeneca")->first()->astra_count;
        $moderna_count = Vaccines::where("typeof_vaccine","Moderna")->count();
        $pfizer_count = Vaccines::where("typeof_vaccine","Pfizer")->count();


        return view("vaccine.dashboard",[
            "data" => $data,
            "sinovac_count" => $sinovac_count,
            "astrazeneca_count" => $astrazeneca_count,
            "moderna_count" => $moderna_count,
            "pfizer_count" => $pfizer_count,
            "sinovac_past" => $sinovac_past,
            "astra_past" => $astra_past,
            "moderna_past" => $moderna_past,
            "pfizer_past" => $pfizer_past
        ]);
    }

    public function vaccineView(Request $request,$province_id)
    {
        if(isset($request->date_range)){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = '2021-03-01 00:00:00';
            $date_end = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
        }

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
            ->where("province_id",$province_id);
        if($request->muncity_filter)
            $data = $data->where("id",$request->muncity_filter);

        $data = $data->orderBy("description","asc");
        $muncity = $data->get();

        $data = $data->paginate(10);

        return view('vaccine.vaccineview',[
            'title' => 'List of Municipality',
            'province_name' => Province::find($province_id)->description,
            'province_id' => $province_id,
            'data' => $data,
            'muncity' => $muncity,
            'typeof_vaccine_filter' => $request->typeof_vaccine_filter,
            'muncity_filter' => $request->muncity_filter,
            'date_start' => $date_start,
            'date_end' => $date_end
        ]);
    }

    public function vaccineFacility($tri_city,Request $request)
    {
        if(isset($request->date_range)){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = '2021-03-01 00:00:00';
            $date_end = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
        }

        if($tri_city == 'cebu'){
            $data = Facility::where("province",2)
                ->where(function($q){
                    $q->where("id","501")
                    ->orWhere("id","502")
                    ->orWhere("id","503")
                    ->orWhere("id","504")
                    ->orWhere("id","505")
                    ->orWhere("id","506")
                    ->orWhere("id","229")
                    ->orWhere("id","508")
                    ->orWhere("id","239")
                    ->orWhere("id","257")
                    ->orWhere("id","252")
                    ->orWhere("id","236")
                    ->orWhere("id","235")
                    ->orWhere("id","6")
                    ->orWhere("id","19")
                    ->orWhere("id","242")
                    ->orWhere("id","231")
                    ->orWhere("id","24")
                    ->orWhere("id","230")
                    ->orWhere("id","12");
                });
        }
        elseif($tri_city == 'mandaue'){
            $data = Facility::where("province",2)
                ->where(function($q){
                    $q->where("id","501")
                        ->orWhere("id","502");
                });
        }

        $facility = $data->orderBy("name","asc")
            ->get();

        if($request->muncity_filter)
            $data = $data->where("id",$request->muncity_filter);


        $data = $data
            ->orderBy("name","asc")
            ->paginate(10);

        return view('vaccine.vaccine_facility',[
            'title' => 'List of Facility',
            'province_name' => "Cebu",
            'province_id' => 2,
            'data' => $data,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'tri_city' => $tri_city,
            "facility" => $facility,
            'muncity_filter' => $request->muncity_filter,
            "typeof_vaccine_filter" => $request->typeof_vaccine_filter
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

    public function vaccinatedContentMunicipality(Request $request)
    {
        $vaccine_accomplishment = VaccineAccomplished::where('muncity_id',$request->muncity_id)->whereBetween("date_first",[$request->date_start,$request->date_end])->orderBy('date_first','asc')->paginate(8);
        $data = [
            "province_id" => $request->province_id,
            "muncity_id" => $request->muncity_id,
            "date_start" => $request->date_start,
            "date_end" => $request->date_end,
            "vaccine_accomplishment" => $vaccine_accomplishment,
            "total_epop_svac_a1" => $request->total_epop_svac_a1,
            "total_epop_svac_a2" => $request->total_epop_svac_a2,
            "total_epop_svac_a3" => $request->total_epop_svac_a3,
            "total_epop_svac_a4" => $request->total_epop_svac_a4,
            "total_epop_svac" => $request->total_epop_svac,
            "total_vallocated_svac_frst" => $request->total_vallocated_svac_frst,
            "total_vallocated_svac_scnd" => $request->total_vallocated_svac_scnd,
            "total_vallocated_svac" => $request->total_vallocated_svac,
            "total_svac_a1_frst" => $request->total_svac_a1_frst,
            "total_svac_a2_frst" => $request->total_svac_a2_frst,
            "total_svac_a3_frst" => $request->total_svac_a3_frst,
            "total_svac_a4_frst" => $request->total_svac_a4_frst,
            "total_vcted_svac_frst" => $request->total_vcted_svac_frst,
            "total_mild_svac_frst" => $request->total_mild_svac_frst,
            "total_srs_svac_frst" => $request->total_srs_svac_frst,
            "total_dfrd_svac_frst" => $request->total_dfrd_svac_frst,
            "total_rfsd_svac_frst" => $request->total_rfsd_svac_frst,
            "total_wstge_svac_frst" => $request->total_wstge_svac_frst,
            "p_cvrge_svac_frst" => $request->p_cvrge_svac_frst,
            "total_c_rate_svac_frst" => $request->total_c_rate_svac_frst,
            "total_r_unvcted_frst_svac" => $request->total_r_unvcted_frst_svac,

            "total_svac_a1_scnd" => $request->total_svac_a1_scnd,
            "total_svac_a2_scnd" => $request->total_svac_a2_scnd,
            "total_svac_a3_scnd" => $request->total_svac_a3_scnd,
            "total_svac_a4_scnd" => $request->total_svac_a4_scnd,
            "total_vcted_svac_scnd" => $request->total_vcted_svac_scnd,
            "total_mild_svac_scnd" => $request->total_mild_svac_scnd,
            "total_srs_svac_scnd" => $request->total_srs_svac_scnd,
            "total_dfrd_svac_scnd" => $request->total_dfrd_svac_scnd,
            "total_rfsd_svac_scnd" => $request->total_rfsd_svac_scnd,
            "total_wstge_svac_scnd" => $request->total_wstge_svac_scnd,
            "p_cvrge_svac_scnd" => $request->p_cvrge_svac_scnd,
            "total_c_rate_svac_scnd" => $request->total_c_rate_svac_scnd,
            "total_r_unvcted_scnd_svac" => $request->total_r_unvcted_scnd_svac,

            "total_epop_astra_a1" => $request->total_epop_astra_a1,
            "total_epop_astra_a2" => $request->total_epop_astra_a2,
            "total_epop_astra_a3" => $request->total_epop_astra_a3,
            "total_epop_astra_a4" => $request->total_epop_astra_a4,
            "total_epop_astra" => $request->total_epop_astra,
            "total_vallocated_astra_frst" => $request->total_vallocated_astra_frst,
            "total_vallocated_astra_scnd" => $request->total_vallocated_astra_scnd,
            "total_vallocated_astra" => $request->total_vallocated_astra,
            "total_astra_a1_frst" => $request->total_astra_a1_frst,
            "total_astra_a2_frst" => $request->total_astra_a2_frst,
            "total_astra_a3_frst" => $request->total_astra_a3_frst,
            "total_astra_a4_frst" => $request->total_astra_a4_frst,
            "total_vcted_astra_frst" => $request->total_vcted_astra_frst,
            "total_mild_astra_frst" => $request->total_mild_astra_frst,
            "total_srs_astra_frst" => $request->total_srs_astra_frst,
            "total_dfrd_astra_frst" => $request->total_dfrd_astra_frst,
            "total_rfsd_astra_frst" => $request->total_rfsd_astra_frst,
            "total_wstge_astra_frst" => $request->total_wstge_astra_frst,
            "p_cvrge_astra_frst" => $request->p_cvrge_astra_frst,
            "total_c_rate_astra_frst" => $request->total_c_rate_astra_frst,
            "total_r_unvcted_frst_astra" => $request->total_r_unvcted_frst_astra,

            "total_astra_a1_scnd" => $request->total_astra_a1_scnd,
            "total_astra_a2_scnd" => $request->total_astra_a2_scnd,
            "total_astra_a3_scnd" => $request->total_astra_a3_scnd,
            "total_astra_a4_scnd" => $request->total_astra_a4_scnd,
            "total_vcted_astra_scnd" => $request->total_vcted_astra_scnd,
            "total_mild_astra_scnd" => $request->total_mild_astra_scnd,
            "total_srs_astra_scnd" => $request->total_srs_astra_scnd,
            "total_dfrd_astra_scnd" => $request->total_dfrd_astra_scnd,
            "total_rfsd_astra_scnd" => $request->total_rfsd_astra_scnd,
            "total_wstge_astra_scnd" => $request->total_wstge_astra_scnd,
            "p_cvrge_astra_scnd" => $request->p_cvrge_astra_scnd,
            "total_c_rate_astra_scnd" => $request->total_c_rate_astra_scnd,
            "total_r_unvcted_scnd_astra" => $request->total_r_unvcted_scnd_astra,

            "total_vallocated_frst" => $request->total_vallocated_frst,
            "total_vallocated_scnd" => $request->total_vallocated_scnd,
            "total_vallocated" => $request->total_vallocated,
            "total_a1" => $request->total_a1,
            "total_a2" => $request->total_a2,
            "total_a3" => $request->total_a3,
            "total_a4" => $request->total_a4,
            "total_vcted_frst" => $request->total_vcted_frst,
            "total_mild" => $request->total_mild,
            "total_serious" => $request->total_serious,
            "total_deferred" => $request->total_deferred,
            "total_refused" => $request->total_refused,
            "total_wastage" => $request->total_wastage,
            "total_p_cvrge_frst" => $request->total_p_cvrge_frst,
            "total_c_rate_frst" => $request->total_c_rate_frst,
            "total_r_unvcted_frst" => $request->total_r_unvcted_frst,

            "total_overall_a1" => $request->total_overall_a1,
            "total_overall_a2" => $request->total_overall_a2,
            "total_overall_a3" => $request->total_overall_a3,
            "total_overall_a4" => $request->total_overall_a4,
            "total_vcted_scnd" => $request->total_vcted_scnd,
            "total_overall_mild" => $request->total_overall_mild,
            "total_overall_serious" => $request->total_overall_serious,
            "total_overall_deferred" => $request->total_overall_deferred,
            "total_overall_refused" => $request->total_overall_refused,
            "total_overall_wastage" => $request->total_overall_wastage,
            "total_overall_p_coverage" => $request->total_overall_p_coverage,
            "total_overall_c_rate" => $request->total_overall_c_rate,
            "total_overall_r_unvcted" => $request->total_overall_r_unvcted,
        ];

        if($request->pagination_table == "true")
            return view("vaccine.vaccine_table", $data);

        return view("vaccine.vaccine_content_municipality",$data);
    }

    public function vaccinatedFacilityContent($facility_id){
        $vaccine_accomplishment = VaccineAccomplished::where('facility_id',$facility_id)->orderBy('id','asc')->get();
        return view("vaccine.vaccine_facility_content", [
            "facility_id" => $facility_id,
            "province_id" => Facility::find($facility_id)->province,
            "vaccine_accomplishment" => $vaccine_accomplishment
        ]);
    }

    public function vaccineSaved(Request $request)
    {
        $user_id = Session::get('auth')->id;
        $count = 0;
        foreach ($request->typeof_vaccine as $row){
            $request->vaccine_id[$count];
            VaccineAccomplished::where("id",$request->vaccine_id[$count])->where("province_id",$request->province_id)->where("muncity_id",$request->muncity_id)->delete();
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

    public function vaccineFacilitySaved(Request $request)
    {
        VaccineAccomplished::where("facility_id",$request->facility_id)->delete();
        $user_id = Session::get('auth')->id;
        $count = 0;

        foreach ($request->typeof_vaccine as $row){
            $vaccine = new VaccineAccomplished();
            $vaccine->encoded_by = $user_id;
            $vaccine->province_id = $request->province_id;
            $vaccine->facility_id = $request->facility_id;
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

    public function getEliPop($muncity_id,$priority){
        $no_eli_pop = Muncity::find($muncity_id);
        if($priority == 'a1')
            return $no_eli_pop->a1;
        elseif($priority == 'a2')
            return $no_eli_pop->a2;
        elseif($priority == 'a3')
            return $no_eli_pop->a3;
        elseif($priority == 'a4')
            return $no_eli_pop->a4;
    }

    public function getEliPopFacility($facility_id,$priority){
        $no_eli_pop = Facility::find($facility_id);
        if($priority == 'a1')
            return $no_eli_pop->a1;
        elseif($priority == 'a2')
            return $no_eli_pop->a2;
        elseif($priority == 'a3')
            return $no_eli_pop->a3;
        elseif($priority == 'a4')
            return $no_eli_pop->a4;
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

    public function getVaccineAllocatedFacility($facility_id,$typeof_vaccine){
        $vaccine_allocated = Facility::find($facility_id);
        if($typeof_vaccine == 'Sinovac'){
            $data[0] = $vaccine_allocated->sinovac_allocated_first;
            $data[1] = $vaccine_allocated->sinovac_allocated_second;
        }else{
            $data[0] = $vaccine_allocated->astrazeneca_allocated_first;
            $data[1] = $vaccine_allocated->astrazeneca_allocated_second;
        }
        return $data;
    }

    public function getVaccineallocatedModal(Request $request)
    {
        $muncity = Muncity::where("id", $request->muncity_id)->where("province_id", $request->province_id)->first();
        $province = Province::find($request->province_id);
        return view('vaccine.vaccine_allocated_modal', [
            "muncity" => $muncity,
            "province_name" => $province->description,
            "province_id" => $request->province_id
        ]);
    }

    public function vaccineFacilityAllocated(Request $request)
    {
        $facility = Facility::where("id", $request->facility_id)->where("province", $request->province_id)->first();
        $province = Province::find($request->province_id);
        return view('vaccine.vaccine_facility_allocated', [
            "facility" => $facility,
            "province_name" => $province->description,
            "province_id" => $request->province_id
        ]);
    }

    public function vaccineFacilityEligiblePop(Request $request){
        $facility = Facility::where("id",$request->facility_id)->where("province",$request->province_id)->first();
        $province = Province::find($request->province_id);
        return view('vaccine.vaccine_facility_eligible',[
            "facility" => $facility,
            "province_name" => $province->description,
            "province_id" => $request->province_id
        ]);
    }

}



