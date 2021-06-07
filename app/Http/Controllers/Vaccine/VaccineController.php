<?php

namespace App\Http\Controllers\Vaccine;

use App\Facility;
use App\Muncity;
use App\Province;
use App\VaccineAccomplished;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class VaccineController extends Controller
{

    public function index(Request $request)
    {
        if(isset($request->date_range)){
            $date_start = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[0])).' 00:00:00';
            $date_end = date('Y-m-d',strtotime(explode(' - ',$request->date_range)[1])).' 23:59:59';
        } else {
            $date_start = '2021-03-01 00:00:00';
            $date_end = Carbon::now()->endOfMonth()->format('Y-m-d').' 23:59:59';
        }

        $a1_target = Muncity::select(DB::raw("sum(coalesce(a1,0)) as a1_target"))->first()->a1_target;
        $a2_target = Muncity::select(DB::raw("sum(coalesce(a2,0)) as a2_target"))->first()->a2_target;
        $a3_target = Muncity::select(DB::raw("sum(coalesce(a3,0)) as a3_target"))->first()->a3_target;
        $a4_target = Muncity::select(DB::raw("sum(coalesce(a4,0)) as a4_target"))->first()->a4_target;

        $a1_completion = VaccineAccomplished::select(DB::raw("sum(coalesce(vaccinated_second,0)) as a1_completion"))->where("priority","a1")->first()->a1_completion;
        $a2_completion = VaccineAccomplished::select(DB::raw("sum(coalesce(vaccinated_second,0)) as a2_completion"))->where("priority","a2")->first()->a2_completion;
        $a3_completion = VaccineAccomplished::select(DB::raw("sum(coalesce(vaccinated_second,0)) as a3_completion"))->where("priority","a3")->first()->a3_completion;
        $a4_completion = VaccineAccomplished::select(DB::raw("sum(coalesce(vaccinated_second,0)) as a4_completion"))->where("priority","a4")->first()->a4_completion;
        $a1_completion = number_format($a1_completion / $a1_target * 100,2);
        $a2_completion = number_format($a2_completion / $a2_target * 100,2);
        $a3_completion = number_format($a3_completion / $a3_target * 100,2);
        $a4_completion = number_format($a4_completion / $a4_target * 100,2);

        for($i=1; $i<=12; $i++)
        {
            $date = date('Y').'/'.$i.'/01';
            $startdate = Carbon::parse($date)->startOfMonth();
            $enddate = Carbon::parse($date)->endOfMonth();

            $sinovac = VaccineAccomplished::select(DB::raw("COALESCE(sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)),0) as sinovac_count"))
                ->where("typeof_vaccine","Sinovac")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->first()
                ->sinovac_count;
            $data['sinovac'][] = $sinovac;

            $astrazeneca = VaccineAccomplished::select(DB::raw("COALESCE(sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)),0) as astra_count"))
                ->where("typeof_vaccine","Astrazeneca")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->first()
                ->astra_count;
            $data['astrazeneca'][] = $astrazeneca;

            $sputnikv = VaccineAccomplished::select(DB::raw("COALESCE(sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)),0) as sputnikv_count"))
                ->where("typeof_vaccine","SputnikV")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->first()
                ->sputnikv_count;
            $data['sputnikv'][] = $sputnikv;

            $pfizer = VaccineAccomplished::select(DB::raw("COALESCE(sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)),0) as pfizer_count"))
                ->where("typeof_vaccine","Pfizer")
                ->whereBetween('created_at',[$startdate,$enddate])
                ->first()
                ->pfizer_count;
            $data['pfizer'][] = $pfizer;
        }

        $sinovac_count = VaccineAccomplished::select(DB::raw("sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)) as sinovac_count"))->where("typeof_vaccine","Sinovac")->first()->sinovac_count;
        $astrazeneca_count = VaccineAccomplished::select(DB::raw("sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)) as astra_count"))->where("typeof_vaccine","Astrazeneca")->first()->astra_count;
        $sputnikv_count = VaccineAccomplished::select(DB::raw("sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)) as sputnikv_count"))->where("typeof_vaccine","SputnikV")->first()->sputnikv_count;
        $pfizer_count = VaccineAccomplished::select(DB::raw("sum(COALESCE(vaccinated_first,0)+COALESCE(vaccinated_second,0)) as pfizer_count"))->where("typeof_vaccine","Pfizer")->first()->pfizer_count;

        $total_vaccinated_first = VaccineAccomplished::select(DB::raw("sum(vaccinated_first) as total_vaccinated_first"))->first()->total_vaccinated_first;
        $total_vaccinated_second = VaccineAccomplished::select(DB::raw("sum(vaccinated_second) as total_vaccinated_second"))->first()->total_vaccinated_second;

        $elipop_muncity = Muncity::select(DB::raw("sum(COALESCE(a1,0)+COALESCE(a2,0)+COALESCE(a3,0)+COALESCE(a4,0)) as elipop_muncity"))->first()->elipop_muncity;

        $percent_coverage_first = number_format($total_vaccinated_first / $elipop_muncity * 100,2);
        $percent_coverage_second = number_format($total_vaccinated_second / $elipop_muncity * 100,2);

        $total_vaccine_allocated_first = Muncity::select(DB::raw("sum(COALESCE(sinovac_allocated_first,0)+COALESCE(astrazeneca_allocated_first,0)) as total_vaccine_allocated_first"))->first()->total_vaccine_allocated_first;
        $total_vaccine_allocated_second = Muncity::select(DB::raw("sum(COALESCE(sinovac_allocated_second,0)+COALESCE(astrazeneca_allocated_second,0)) as total_vaccine_allocated_second"))->first()->total_vaccine_allocated_second;

        $consumption_rate_first =  number_format($total_vaccinated_first / $total_vaccine_allocated_first * 100,2);
        $consumption_rate_second =  number_format($total_vaccinated_second / $total_vaccine_allocated_second * 100,2);


        return view("vaccine.dashboard",[
            "data" => $data,
            "sinovac_count" => $sinovac_count,
            "astrazeneca_count" => $astrazeneca_count,
            "sputnikv_count" => $sputnikv_count,
            "pfizer_count" => $pfizer_count,
            "percent_coverage_first" =>$percent_coverage_first,
            "percent_coverage_second"=>$percent_coverage_second,
            "consumption_rate_first" =>$consumption_rate_first,
            "consumption_rate_second" =>$consumption_rate_second,
            "a1_target" => $a1_target,
            "a2_target" => $a2_target,
            "a3_target" => $a3_target,
            "a4_target" => $a4_target,
            "a1_completion" => $a1_completion,
            "a2_completion" => $a2_completion,
            "a3_completion" => $a3_completion,
            "a4_completion" => $a4_completion,
            "date_start" => $date_start,
            "date_end" => $date_end
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
                ->where("vaccine_used","yes")
                ->where("tricity_id",63);
        }
        elseif($tri_city == 'mandaue'){
            $data = Facility::where("province",2)
                ->where("vaccine_used","yes")
                ->where("tricity_id",80)
                ->where('referral_used','yes');
        }
        elseif($tri_city == 'lapu'){
            $data = Facility::where("province",2)
                ->where("vaccine_used","yes")
                ->where("tricity_id",76)
                ->where('referral_used','yes');
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

            //sinovac
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
            //end sinovac

            //astra
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
            //end astra

            //sputnikv
            "total_epop_sputnikv_a1" => $request->total_epop_sputnikv_a1,
            "total_epop_sputnikv_a2" => $request->total_epop_sputnikv_a2,
            "total_epop_sputnikv_a3" => $request->total_epop_sputnikv_a3,
            "total_epop_sputnikv_a4" => $request->total_epop_sputnikv_a4,
            "total_epop_sputnikv" => $request->total_epop_sputnikv,
            "total_vallocated_sputnikv_frst" => $request->total_vallocated_sputnikv_frst,
            "total_vallocated_sputnikv_scnd" => $request->total_vallocated_sputnikv_scnd,
            "total_vallocated_sputnikv" => $request->total_vallocated_sputnikv,
            "total_sputnikv_a1_frst" => $request->total_sputnikv_a1_frst,
            "total_sputnikv_a2_frst" => $request->total_sputnikv_a2_frst,
            "total_sputnikv_a3_frst" => $request->total_sputnikv_a3_frst,
            "total_sputnikv_a4_frst" => $request->total_sputnikv_a4_frst,
            "total_vcted_sputnikv_frst" => $request->total_vcted_sputnikv_frst,
            "total_mild_sputnikv_frst" => $request->total_mild_sputnikv_frst,
            "total_srs_sputnikv_frst" => $request->total_srs_sputnikv_frst,
            "total_dfrd_sputnikv_frst" => $request->total_dfrd_sputnikv_frst,
            "total_rfsd_sputnikv_frst" => $request->total_rfsd_sputnikv_frst,
            "total_wstge_sputnikv_frst" => $request->total_wstge_sputnikv_frst,
            "p_cvrge_sputnikv_frst" => $request->p_cvrge_sputnikv_frst,
            "total_c_rate_sputnikv_frst" => $request->total_c_rate_sputnikv_frst,
            "total_r_unvcted_frst_sputnikv" => $request->total_r_unvcted_frst_sputnikv,

            "total_sputnikv_a1_scnd" => $request->total_sputnikv_a1_scnd,
            "total_sputnikv_a2_scnd" => $request->total_sputnikv_a2_scnd,
            "total_sputnikv_a3_scnd" => $request->total_sputnikv_a3_scnd,
            "total_sputnikv_a4_scnd" => $request->total_sputnikv_a4_scnd,
            "total_vcted_sputnikv_scnd" => $request->total_vcted_sputnikv_scnd,
            "total_mild_sputnikv_scnd" => $request->total_mild_sputnikv_scnd,
            "total_srs_sputnikv_scnd" => $request->total_srs_sputnikv_scnd,
            "total_dfrd_sputnikv_scnd" => $request->total_dfrd_sputnikv_scnd,
            "total_rfsd_sputnikv_scnd" => $request->total_rfsd_sputnikv_scnd,
            "total_wstge_sputnikv_scnd" => $request->total_wstge_sputnikv_scnd,
            "p_cvrge_sputnikv_scnd" => $request->p_cvrge_sputnikv_scnd,
            "total_c_rate_sputnikv_scnd" => $request->total_c_rate_sputnikv_scnd,
            "total_r_unvcted_scnd_sputnikv" => $request->total_r_unvcted_scnd_sputnikv,
            //end sputnikv

            //pfizer
            "total_epop_pfizer_a1" => $request->total_epop_pfizer_a1,
            "total_epop_pfizer_a2" => $request->total_epop_pfizer_a2,
            "total_epop_pfizer_a3" => $request->total_epop_pfizer_a3,
            "total_epop_pfizer_a4" => $request->total_epop_pfizer_a4,
            "total_epop_pfizer" => $request->total_epop_pfizer,
            "total_vallocated_pfizer_frst" => $request->total_vallocated_pfizer_frst,
            "total_vallocated_pfizer_scnd" => $request->total_vallocated_pfizer_scnd,
            "total_vallocated_pfizer" => $request->total_vallocated_pfizer,
            "total_pfizer_a1_frst" => $request->total_pfizer_a1_frst,
            "total_pfizer_a2_frst" => $request->total_pfizer_a2_frst,
            "total_pfizer_a3_frst" => $request->total_pfizer_a3_frst,
            "total_pfizer_a4_frst" => $request->total_pfizer_a4_frst,
            "total_vcted_pfizer_frst" => $request->total_vcted_pfizer_frst,
            "total_mild_pfizer_frst" => $request->total_mild_pfizer_frst,
            "total_srs_pfizer_frst" => $request->total_srs_pfizer_frst,
            "total_dfrd_pfizer_frst" => $request->total_dfrd_pfizer_frst,
            "total_rfsd_pfizer_frst" => $request->total_rfsd_pfizer_frst,
            "total_wstge_pfizer_frst" => $request->total_wstge_pfizer_frst,
            "p_cvrge_pfizer_frst" => $request->p_cvrge_pfizer_frst,
            "total_c_rate_pfizer_frst" => $request->total_c_rate_pfizer_frst,
            "total_r_unvcted_frst_pfizer" => $request->total_r_unvcted_frst_pfizer,

            "total_pfizer_a1_scnd" => $request->total_pfizer_a1_scnd,
            "total_pfizer_a2_scnd" => $request->total_pfizer_a2_scnd,
            "total_pfizer_a3_scnd" => $request->total_pfizer_a3_scnd,
            "total_pfizer_a4_scnd" => $request->total_pfizer_a4_scnd,
            "total_vcted_pfizer_scnd" => $request->total_vcted_pfizer_scnd,
            "total_mild_pfizer_scnd" => $request->total_mild_pfizer_scnd,
            "total_srs_pfizer_scnd" => $request->total_srs_pfizer_scnd,
            "total_dfrd_pfizer_scnd" => $request->total_dfrd_pfizer_scnd,
            "total_rfsd_pfizer_scnd" => $request->total_rfsd_pfizer_scnd,
            "total_wstge_pfizer_scnd" => $request->total_wstge_pfizer_scnd,
            "p_cvrge_pfizer_scnd" => $request->p_cvrge_pfizer_scnd,
            "total_c_rate_pfizer_scnd" => $request->total_c_rate_pfizer_scnd,
            "total_r_unvcted_scnd_pfizer" => $request->total_r_unvcted_scnd_pfizer,
            // pfizer end

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

    public function vaccinatedFacilityContent(Request $request){
        $vaccine_accomplishment = VaccineAccomplished::where('facility_id',$request->facility_id)
                            ->whereBetween("date_first",[$request->date_start,$request->date_end])
                            ->orderBy('id','asc')
                            ->paginate(8);

        $data = [
            "vaccine_accomplishment" => $vaccine_accomplishment,
            "facility_id" => $request->facility_id,
            "province_id" => Facility::find($request->facility_id)->province,
            "date_start" => $request->date_start,
            "date_end" => $request->date_end,
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

            //astra
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

            //sputnikv
            "total_epop_sputnikv_a1" => $request->total_epop_sputnikv_a1,
            "total_epop_sputnikv_a2" => $request->total_epop_sputnikv_a2,
            "total_epop_sputnikv_a3" => $request->total_epop_sputnikv_a3,
            "total_epop_sputnikv_a4" => $request->total_epop_sputnikv_a4,
            "total_epop_sputnikv" => $request->total_epop_sputnikv,
            "total_vallocated_sputnikv_frst" => $request->total_vallocated_sputnikv_frst,
            "total_vallocated_sputnikv_scnd" => $request->total_vallocated_sputnikv_scnd,
            "total_vallocated_sputnikv" => $request->total_vallocated_sputnikv,
            "total_sputnikv_a1_frst" => $request->total_sputnikv_a1_frst,
            "total_sputnikv_a2_frst" => $request->total_sputnikv_a2_frst,
            "total_sputnikv_a3_frst" => $request->total_sputnikv_a3_frst,
            "total_sputnikv_a4_frst" => $request->total_sputnikv_a4_frst,
            "total_vcted_sputnikv_frst" => $request->total_vcted_sputnikv_frst,
            "total_mild_sputnikv_frst" => $request->total_mild_sputnikv_frst,
            "total_srs_sputnikv_frst" => $request->total_srs_sputnikv_frst,
            "total_dfrd_sputnikv_frst" => $request->total_dfrd_sputnikv_frst,
            "total_rfsd_sputnikv_frst" => $request->total_rfsd_sputnikv_frst,
            "total_wstge_sputnikv_frst" => $request->total_wstge_sputnikv_frst,
            "p_cvrge_sputnikv_frst" => $request->p_cvrge_sputnikv_frst,
            "total_c_rate_sputnikv_frst" => $request->total_c_rate_sputnikv_frst,
            "total_r_unvcted_frst_sputnikv" => $request->total_r_unvcted_frst_sputnikv,

            "total_sputnikv_a1_scnd" => $request->total_sputnikv_a1_scnd,
            "total_sputnikv_a2_scnd" => $request->total_sputnikv_a2_scnd,
            "total_sputnikv_a3_scnd" => $request->total_sputnikv_a3_scnd,
            "total_sputnikv_a4_scnd" => $request->total_sputnikv_a4_scnd,
            "total_vcted_sputnikv_scnd" => $request->total_vcted_sputnikv_scnd,
            "total_mild_sputnikv_scnd" => $request->total_mild_sputnikv_scnd,
            "total_srs_sputnikv_scnd" => $request->total_srs_sputnikv_scnd,
            "total_dfrd_sputnikv_scnd" => $request->total_dfrd_sputnikv_scnd,
            "total_rfsd_sputnikv_scnd" => $request->total_rfsd_sputnikv_scnd,
            "total_wstge_sputnikv_scnd" => $request->total_wstge_sputnikv_scnd,
            "p_cvrge_sputnikv_scnd" => $request->p_cvrge_sputnikv_scnd,
            "total_c_rate_sputnikv_scnd" => $request->total_c_rate_sputnikv_scnd,
            "total_r_unvcted_scnd_sputnikv" => $request->total_r_unvcted_scnd_sputnikv,
            //end sputnikv

            //pfizer
            "total_epop_pfizer_a1" => $request->total_epop_pfizer_a1,
            "total_epop_pfizer_a2" => $request->total_epop_pfizer_a2,
            "total_epop_pfizer_a3" => $request->total_epop_pfizer_a3,
            "total_epop_pfizer_a4" => $request->total_epop_pfizer_a4,
            "total_epop_pfizer" => $request->total_epop_pfizer,
            "total_vallocated_pfizer_frst" => $request->total_vallocated_pfizer_frst,
            "total_vallocated_pfizer_scnd" => $request->total_vallocated_pfizer_scnd,
            "total_vallocated_pfizer" => $request->total_vallocated_pfizer,
            "total_pfizer_a1_frst" => $request->total_pfizer_a1_frst,
            "total_pfizer_a2_frst" => $request->total_pfizer_a2_frst,
            "total_pfizer_a3_frst" => $request->total_pfizer_a3_frst,
            "total_pfizer_a4_frst" => $request->total_pfizer_a4_frst,
            "total_vcted_pfizer_frst" => $request->total_vcted_pfizer_frst,
            "total_mild_pfizer_frst" => $request->total_mild_pfizer_frst,
            "total_srs_pfizer_frst" => $request->total_srs_pfizer_frst,
            "total_dfrd_pfizer_frst" => $request->total_dfrd_pfizer_frst,
            "total_rfsd_pfizer_frst" => $request->total_rfsd_pfizer_frst,
            "total_wstge_pfizer_frst" => $request->total_wstge_pfizer_frst,
            "p_cvrge_pfizer_frst" => $request->p_cvrge_pfizer_frst,
            "total_c_rate_pfizer_frst" => $request->total_c_rate_pfizer_frst,
            "total_r_unvcted_frst_pfizer" => $request->total_r_unvcted_frst_pfizer,

            "total_pfizer_a1_scnd" => $request->total_pfizer_a1_scnd,
            "total_pfizer_a2_scnd" => $request->total_pfizer_a2_scnd,
            "total_pfizer_a3_scnd" => $request->total_pfizer_a3_scnd,
            "total_pfizer_a4_scnd" => $request->total_pfizer_a4_scnd,
            "total_vcted_pfizer_scnd" => $request->total_vcted_pfizer_scnd,
            "total_mild_pfizer_scnd" => $request->total_mild_pfizer_scnd,
            "total_srs_pfizer_scnd" => $request->total_srs_pfizer_scnd,
            "total_dfrd_pfizer_scnd" => $request->total_dfrd_pfizer_scnd,
            "total_rfsd_pfizer_scnd" => $request->total_rfsd_pfizer_scnd,
            "total_wstge_pfizer_scnd" => $request->total_wstge_pfizer_scnd,
            "p_cvrge_pfizer_scnd" => $request->p_cvrge_pfizer_scnd,
            "total_c_rate_pfizer_scnd" => $request->total_c_rate_pfizer_scnd,
            "total_r_unvcted_scnd_pfizer" => $request->total_r_unvcted_scnd_pfizer,
            // pfizer end

            "total_vallocated_frst" => $request->total_vallocated_frst,
            "total_vallocated_scnd" => $request->total_vallocated_scnd,
            "total_vallocated" => $request->total_vallocated,
            "total_overall_a1_first" => $request->total_overall_a1_first,
            "total_overall_a2_first" => $request->total_overall_a2_first,
            "total_overall_a3_first" => $request->total_overall_a3_first,
            "total_overall_a4_first" => $request->total_overall_a4_first,
            "total_vcted_frst" => $request->total_vcted_frst,
            "total_overall_mild_first" => $request->total_overall_mild_first,
            "total_overall_serious_first" => $request->total_overall_serious_first,
            "total_overall_deferred_first" => $request->total_overall_deferred_first,
            "total_rfsd_frst" => $request->total_rfsd_frst,
            "total_overall_wastage_first" => $request->total_overall_wastage_first,
            "total_p_cvrge_frst" => $request->total_p_cvrge_frst,
            "total_c_rate_frst" => $request->total_c_rate_frst,
            "total_r_unvcted_frst" => $request->total_r_unvcted_frst,

            "total_overall_a1_second" => $request->total_overall_a1_second,
            "total_overall_a2_second" => $request->total_overall_a2_second,
            "total_overall_a3_second" => $request->total_overall_a3_second,
            "total_overall_a4_second" => $request->total_overall_a4_second,
            "total_vcted_scnd" => $request->total_vcted_scnd,
            "total_overall_mild_second" => $request->total_overall_mild_second,
            "total_overall_serious_second" => $request->total_overall_serious_second,
            "total_overall_deferred_second" => $request->total_overall_deferred_second,
            "total_rfsd_scnd" => $request->total_rfsd_scnd,
            "total_overall_wastage_second" => $request->total_overall_wastage_second,
            "total_p_cvrge_scnd" => $request->total_p_cvrge_scnd,
            "total_c_rate_scnd" => $request->total_c_rate_scnd,
            "total_r_unvcted_scnd" => $request->total_r_unvcted_scnd,



        ];

        if($request->pagination_table == "true"){
            return view("vaccine.vaccine_facility_table",$data);
        }
        return view("vaccine.vaccine_facility_content",$data);
    }

    public function vaccineSaved(Request $request)
    {
        $user_id = Session::get('auth')->id;
        $count = 0;
        foreach ($request->typeof_vaccine as $row){
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
        $user_id = Session::get('auth')->id;
        $count = 0;

        foreach ($request->typeof_vaccine as $row){
            $vaccine = new VaccineAccomplished();
            VaccineAccomplished::where("id",$request->vaccine_id[$count])->delete();
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
        }
        elseif($typeof_vaccine == 'Astrazeneca'){
            $data[0] = $vaccine_allocated->astrazeneca_allocated_first;
            $data[1] = $vaccine_allocated->astrazeneca_allocated_second;
        }
        elseif($typeof_vaccine == 'SputnikV'){
            $data[0] = $vaccine_allocated->sputnikv_allocated_first;
            $data[1] = $vaccine_allocated->sputnikv_allocated_second;
        }
        elseif($typeof_vaccine == 'Pfizer'){
            $data[0] = $vaccine_allocated->pfizer_allocated_first;
            $data[1] = $vaccine_allocated->pfizer_allocated_second;
        }
        return $data;
    }
    public function getVaccineAllocatedFacility($facility_id,$typeof_vaccine){
        $vaccine_allocated = Facility::find($facility_id);
        if($typeof_vaccine == 'Sinovac'){
            $data[0] = $vaccine_allocated->sinovac_allocated_first;
            $data[1] = $vaccine_allocated->sinovac_allocated_second;
        }
        elseif($typeof_vaccine == 'Astrazeneca'){
            $data[0] = $vaccine_allocated->astrazeneca_allocated_first;
            $data[1] = $vaccine_allocated->astrazeneca_allocated_second;
        }
        elseif($typeof_vaccine == 'SputnikV'){
            $data[0] = $vaccine_allocated->sputnikv_allocated_first;
            $data[1] = $vaccine_allocated->sputnikv_allocated_second;
        }
        elseif($typeof_vaccine == 'Pfizer'){
            $data[0] = $vaccine_allocated->pfizer_allocated_first;
            $data[1] = $vaccine_allocated->pfizer_allocated_second;
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

    public function vaccineEligiblePop(Request $request){
        $muncity = Muncity::where("id",$request->muncity_id)->where("province_id",$request->province_id)->first();
        $province = Province::find($request->province_id);
        return view('vaccine.vaccine_eligible_pop',[
            "muncity" => $muncity,
            "province_name" => $province->description,
            "province_id" => $request->province_id
        ]);
    }

    public function vaccineMap(){
        return view('vaccine.vaccine_map');
    }

    public function vaccineLineChart(){
        //for past 15 days
        $date_start_1 = date('Y-m-d',strtotime(Carbon::now()->subDays(31))).' 00:00:00';
        $date_end_1 = date('Y-m-d',strtotime(Carbon::now()->subDays(16))).' 23:59:59';
        $first_dose_past_1 = \DB::connection('mysql')->select("call vaccine_past_vaccinated('$date_start_1','$date_end_1','first')");
        $second_dose_past_1 = \DB::connection('mysql')->select("call vaccine_past_vaccinated('$date_start_1','$date_end_1','second')");

        $date_start_2 = date('Y-m-d',strtotime(Carbon::now()->subDays(16))).' 00:00:00';
        $date_end_2 = date('Y-m-d',strtotime(Carbon::now()->subDays(1))).' 23:59:59';
        $first_dose_past_2 = \DB::connection('mysql')->select("call vaccine_past_vaccinated('$date_start_2','$date_end_2','first')");
        $second_dose_past_2 = \DB::connection('mysql')->select("call vaccine_past_vaccinated('$date_start_2','$date_end_2','second')");
        ///
        return view('vaccine.vaccine_line_chart',[
            "first_dose_past_1" =>$first_dose_past_1,
            "second_dose_past_1" =>$second_dose_past_1,
            "first_dose_past_2" =>$first_dose_past_2,
            "second_dose_past_2" =>$second_dose_past_2,
        ]);
    }

    public function vaccineSummaryReport(){
        $sinovac_bohol = Muncity::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_bohol"))->where("province_id",1)->first()->sinovac_bohol;
        $sinovac_cebu = Muncity::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_cebu"))->where("province_id",2)->first()->sinovac_cebu;
        $sinovac_negros = Muncity::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_negros"))->where("province_id",3)->first()->sinovac_negros;
        $sinovac_siquijor = Muncity::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_siquijor"))->where("province_id",4)->first()->sinovac_siquijor;
        $sinovac_cebu_facility = Facility::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_cebu_facility"))->where("tricity_id",63)->first()->sinovac_cebu_facility;
        $sinovac_mandaue_facility = Facility::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_mandaue_facility"))->where("tricity_id",80)->first()->sinovac_mandaue_facility;
        $sinovac_lapu_facility = Facility::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_lapu_facility"))->where("tricity_id",76)->first()->sinovac_lapu_facility;

        $astra_bohol = Muncity::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_bohol"))->where("province_id",1)->first()->astra_bohol;
        $astra_cebu = Muncity::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_cebu"))->where("province_id",2)->first()->astra_cebu;
        $astra_negros = Muncity::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_negros"))->where("province_id",3)->first()->astra_negros;
        $astra_siquijor = Muncity::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_siquijor"))->where("province_id",4)->first()->astra_siquijor;
        $astra_cebu_facility = Facility::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_cebu_facility"))->where("tricity_id",63)->first()->astra_cebu_facility;
        $astra_mandaue_facility = Facility::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_mandaue_facility"))->where("tricity_id",80)->first()->astra_mandaue_facility;
        $astra_lapu_facility = Facility::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_lapu_facility"))->where("tricity_id",76)->first()->astra_lapu_facility;

        $sputnikv_bohol = Muncity::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_bohol"))->where("province_id",1)->first()->sputnikv_bohol;
        $sputnikv_cebu = Muncity::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_cebu"))->where("province_id",2)->first()->sputnikv_cebu;
        $sputnikv_negros = Muncity::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_negros"))->where("province_id",3)->first()->sputnikv_negros;
        $sputnikv_siquijor = Muncity::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_siquijor"))->where("province_id",4)->first()->sputnikv_siquijor;
        $sputnikv_cebu_facility = Facility::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_cebu_facility"))->where("tricity_id",63)->first()->sputnikv_cebu_facility;
        $sputnikv_mandaue_facility = Facility::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_mandaue_facility"))->where("tricity_id",80)->first()->sputnikv_mandaue_facility;
        $sputnikv_lapu_facility = Facility::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_lapu_facility"))->where("tricity_id",76)->first()->sputnikv_lapu_facility;

        $pfizer_bohol = Muncity::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_bohol"))->where("province_id",1)->first()->pfizer_bohol;
        $pfizer_cebu = Muncity::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_cebu"))->where("province_id",2)->first()->pfizer_cebu;
        $pfizer_negros = Muncity::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_negros"))->where("province_id",3)->first()->pfizer_negros;
        $pfizer_siquijor = Muncity::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_siquijor"))->where("province_id",4)->first()->pfizer_siquijor;
        $pfizer_cebu_facility = Facility::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_cebu_facility"))->where("tricity_id",63)->first()->pfizer_cebu_facility;
        $pfizer_mandaue_facility = Facility::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_mandaue_facility"))->where("tricity_id",80)->first()->pfizer_mandaue_facility;
        $pfizer_lapu_facility = Facility::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_lapu_facility"))->where("tricity_id",76)->first()->pfizer_lapu_facility;


        //ELIGIBLE POP
        $eli_pop_bohol = Muncity::select(DB::raw("SUM(coalesce(a1,0)) as eli_pop_bohol"))->where("province_id",1)->first()->eli_pop_bohol;
        $eli_pop_cebu = Muncity::select(DB::raw("SUM(coalesce(a1,0)) as eli_pop_cebu"))->where("province_id",2)->first()->eli_pop_cebu;
        $eli_pop_negros = Muncity::select(DB::raw("SUM(coalesce(a1,0)) as eli_pop_negros"))->where("province_id",3)->first()->eli_pop_negros;
        $eli_pop_siquijor = Muncity::select(DB::raw("SUM(coalesce(a1,0)) as eli_pop_siquijor"))->where("province_id",4)->first()->eli_pop_siquijor;

        $eli_pop_cebu_facility = Muncity::select(DB::raw("SUM(coalesce(facility.a1,0)) as eli_pop_cebu_facility"))
                                ->leftJoin("facility","facility.tricity_id","=","muncity.id")
                                ->where("muncity.id","=",63)
                                ->first()->eli_pop_cebu_facility;
        $eli_pop_mandaue_facility = Muncity::select(DB::raw("SUM(coalesce(facility.a1,0)) as eli_pop_mandaue_facility"))
                                ->leftJoin("facility","facility.tricity_id","=","muncity.id")
                                ->where("muncity.id","=",80)
                                ->first()->eli_pop_mandaue_facility;
        $eli_pop_lapu_facility = Muncity::select(DB::raw("SUM(coalesce(facility.a1,0)) as eli_pop_lapu_facility"))
                                ->leftJoin("facility","facility.tricity_id","=","muncity.id")
                                ->where("muncity.id","=",76)
                                ->first()->eli_pop_lapu_facility;



        $sinovac_region = $sinovac_bohol + $sinovac_cebu + $sinovac_negros + $sinovac_siquijor + $sinovac_cebu_facility + $sinovac_mandaue_facility + $sinovac_lapu_facility;
        $astra_region = $astra_bohol + $astra_cebu + $astra_negros + $astra_siquijor + $astra_cebu_facility + $astra_mandaue_facility + $astra_lapu_facility;
        $sputnikv_region = $sputnikv_bohol + $sputnikv_cebu + $sputnikv_negros + $sputnikv_siquijor + $sputnikv_cebu_facility + $sputnikv_mandaue_facility + $sputnikv_lapu_facility;
        $pfizer_region = $pfizer_bohol + $pfizer_cebu + $pfizer_negros + $pfizer_siquijor + $pfizer_cebu_facility + $pfizer_mandaue_facility + $pfizer_lapu_facility;


        //VACCINATED SINOVAC FIRST
        $vcted_sinovac_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sinovac_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->vcted_sinovac_bohol_first;
        $vcted_sinovac_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sinovac_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->vcted_sinovac_cebu_first;
        $vcted_sinovac_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sinovac_negros_first"))->where("province_id",3)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->vcted_sinovac_negros_first;
        $vcted_sinovac_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sinovac_siquijor_first"))->where("province_id",4)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->vcted_sinovac_siquijor_first;


        $vcted_sinovac_cebu_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as vcted_sinovac_cebu_facility_first"))
                                        ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
                                        ->where("facility.tricity_id","=",63)
                                        ->where("vaccine_accomplish.typeof_vaccine",'Sinovac')
                                        ->where("vaccine_accomplish.priority",'a1')
                                        ->first()
                                        ->vcted_sinovac_cebu_facility_first;



        //VACCINATED SINOVAC SECOND
        $vcted_sinovac_bohol_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sinovac_bohol_second"))->where("province_id",1)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->vcted_sinovac_bohol_second;
        $vcted_sinovac_cebu_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sinovac_cebu_second"))->where("province_id",2)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->vcted_sinovac_cebu_second;
        $vcted_sinovac_negros_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sinovac_negros_second"))->where("province_id",3)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->vcted_sinovac_negros_second;
        $vcted_sinovac_siquijor_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sinovac_siquijor_second"))->where("province_id",4)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->vcted_sinovac_siquijor_second;


        //VACCINATED ASTRAZENECA FIRST
        $vcted_astra_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_astra_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'Astrazeneca')->where("priority",'a1')->first()->vcted_astra_bohol_first;
        $vcted_astra_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_astra_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'Astrazeneca')->where("priority",'a1')->first()->vcted_astra_cebu_first;
        $vcted_astra_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_astra_negros_first"))->where("province_id",3)->where("typeof_vaccine",'Astrazeneca')->where("priority",'a1')->first()->vcted_astra_negros_first;
        $vcted_astra_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_astra_siquijor_first"))->where("province_id",4)->where("typeof_vaccine",'Astrazeneca')->where("priority",'a1')->first()->vcted_astra_siquijor_first;

        //VACCINATED ASTRAZENECA SECOND
        $vcted_astra_bohol_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_astra_bohol_second"))->where("province_id",1)->where("typeof_vaccine",'Astrazeneca')->where("priority",'a1')->first()->vcted_astra_bohol_second;
        $vcted_astra_cebu_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_astra_cebu_second"))->where("province_id",2)->where("typeof_vaccine",'Astrazeneca')->where("priority",'a1')->first()->vcted_astra_cebu_second;
        $vcted_astra_negros_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_astra_negros_second"))->where("province_id",3)->where("typeof_vaccine",'Astrazeneca')->where("priority",'a1')->first()->vcted_astra_negros_second;
        $vcted_astra_siquijor_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_astra_siquijor_second"))->where("province_id",4)->where("typeof_vaccine",'Astrazeneca')->where("priority",'a1')->first()->vcted_astra_siquijor_second;

        //VACCINATED SPUTNIK V FIRST
        $vcted_sputnikv_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sputnikv_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'SputnikV')->where("priority",'a1')->first()->vcted_sputnikv_bohol_first;
        $vcted_sputnikv_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sputnikv_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'SputnikV')->where("priority",'a1')->first()->vcted_sputnikv_cebu_first;
        $vcted_sputnikv_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sputnikv_negros_first"))->where("province_id",3)->where("typeof_vaccine",'SputnikV')->where("priority",'a1')->first()->vcted_sputnikv_negros_first;
        $vcted_sputnikv_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sputnikv_siquijor_first"))->where("province_id",4)->where("typeof_vaccine",'SputnikV')->where("priority",'a1')->first()->vcted_sputnikv_siquijor_first;

        //VACCINATED SPUTNIK V SECOND
        $vcted_sputnikv_bohol_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sputnikv_bohol_second"))->where("province_id",1)->where("typeof_vaccine",'SputnikV')->where("priority",'a1')->first()->vcted_sputnikv_bohol_second;
        $vcted_sputnikv_cebu_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sputnikv_cebu_second"))->where("province_id",2)->where("typeof_vaccine",'SputnikV')->where("priority",'a1')->first()->vcted_sputnikv_cebu_second;
        $vcted_sputnikv_negros_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sputnikv_negros_second"))->where("province_id",3)->where("typeof_vaccine",'SputnikV')->where("priority",'a1')->first()->vcted_sputnikv_negros_second;
        $vcted_sputnikv_siquijor_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sputnikv_siquijor_second"))->where("province_id",4)->where("typeof_vaccine",'SputnikV')->where("priority",'a1')->first()->vcted_sputnikv_siquijor_second;

        //VACCINATED PFIZER FIRST
        $vcted_pfizer_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_pfizer_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'Pfizer')->where("priority",'a1')->first()->vcted_pfizer_bohol_first;
        $vcted_pfizer_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_pfizer_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'Pfizer')->where("priority",'a1')->first()->vcted_pfizer_cebu_first;
        $vcted_pfizer_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_pfizer_negros_first"))->where("province_id",3)->where("typeof_vaccine",'Pfizer')->where("priority",'a1')->first()->vcted_pfizer_negros_first;
        $vcted_pfizer_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_pfizer_siquijor_first"))->where("province_id",4)->where("typeof_vaccine",'Pfizer')->where("priority",'a1')->first()->vcted_pfizer_siquijor_first;

        //VACCINATED PFIZER V SECOND
        $vcted_pfizer_bohol_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_pfizer_bohol_second"))->where("province_id",1)->where("typeof_vaccine",'Pfizer')->where("priority",'a1')->first()->vcted_pfizer_bohol_second;
        $vcted_pfizer_cebu_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_pfizer_cebu_second"))->where("province_id",2)->where("typeof_vaccine",'Pfizer')->where("priority",'a1')->first()->vcted_pfizer_cebu_second;
        $vcted_pfizer_negros_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_pfizer_negros_second"))->where("province_id",3)->where("typeof_vaccine",'Pfizer')->where("priority",'a1')->first()->vcted_pfizer_negros_second;
        $vcted_pfizer_siquijor_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_pfizer_siquijor_second"))->where("province_id",4)->where("typeof_vaccine",'Pfizer')->where("priority",'a1')->first()->vcted_pfizer_siquijor_second;

        //$vcted_sinovac_cebu_faclity_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sinovac_cebu_faclity_first"))->where("facility_id",523)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->vcted_sinovac_cebu_faclity_first;
        //$vcted_sinovac_mandaue_faclity_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sinovac_mandaue_faclity_first"))->where("muncity_id",1)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->vcted_sinovac_mandaue_faclity_first;
        //$vcted_sinovac_lapu_faclity_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sinovac_lapu_faclity_first"))->where("muncity_id",1)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->vcted_sinovac_lapu_faclity_first;

        //TOTAL_REFUSED_FIRST
        $refused_first_bohol = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_first,0)) as refused_first_bohol"))->where("province_id",1)->where("priority",'a1')->first()->refused_first_bohol;
        $refused_first_cebu = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_first,0)) as refused_first_cebu"))->where("province_id",2)->where("priority",'a1')->first()->refused_first_cebu;
        $refused_first_negros = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_first,0)) as refused_first_negros"))->where("province_id",3)->where("priority",'a1')->first()->refused_first_negros;
        $refused_first_siquijor = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_first,0)) as refused_first_siquijor"))->where("province_id",4)->where("priority",'a1')->first()->refused_first_siquijor;

        //TOTAL_REFUSED_SECOND
        $refused_second_bohol = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_second,0)) as refused_second_bohol"))->where("province_id",1)->where("priority",'a1')->first()->refused_second_bohol;
        $refused_second_cebu = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_second,0)) as refused_second_cebu"))->where("province_id",2)->where("priority",'a1')->first()->refused_second_cebu;
        $refused_second_negros = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_second,0)) as refused_second_negros"))->where("province_id",3)->where("priority",'a1')->first()->refused_second_negros;
        $refused_second_siquijor = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_second,0)) as refused_second_siquijor"))->where("province_id",4)->where("priority",'a1')->first()->refused_second_siquijor;


        //TOTAL_DEFERRED_FIRST
        $deferred_first_bohol = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_first,0)) as deferred_first_bohol"))->where("province_id",1)->where("priority",'a1')->first()->deferred_first_bohol;
        $deferred_first_cebu = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_first,0)) as deferred_first_cebu"))->where("province_id",2)->where("priority",'a1')->first()->deferred_first_cebu;
        $deferred_first_negros = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_first,0)) as deferred_first_negros"))->where("province_id",3)->where("priority",'a1')->first()->deferred_first_negros;
        $deferred_first_siquijor = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_first,0)) as deferred_first_siquijor"))->where("province_id",4)->where("priority",'a1')->first()->deferred_first_siquijor;

        //TOTAL_DEFERRED_SECOND
        $deferred_second_bohol = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_second,0)) as deferred_second_bohol"))->where("province_id",1)->where("priority",'a1')->first()->deferred_second_bohol;
        $deferred_second_cebu = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_second,0)) as deferred_second_cebu"))->where("province_id",2)->where("priority",'a1')->first()->deferred_second_cebu;
        $deferred_second_negros = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_second,0)) as deferred_second_negros"))->where("province_id",3)->where("priority",'a1')->first()->deferred_second_negros;
        $deferred_second_siquijor = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_second,0)) as deferred_second_siquijor"))->where("province_id",4)->where("priority",'a1')->first()->deferred_second_siquijor;


        //WASTAGE_SINOVAC_FIRST
        $wastage_sinovac_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sinovac_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->wastage_sinovac_bohol_first;
        $wastage_sinovac_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sinovac_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->wastage_sinovac_cebu_first;
        $wastage_sinovac_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sinovac_negros_first"))->where("province_id",3)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->wastage_sinovac_negros_first;
        $wastage_sinovac_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sinovac_siquior_first"))->where("province_id",4)->where("typeof_vaccine",'Sinovac')->where("priority",'a1')->first()->wastage_sinovac_siquior_first;

        //WASTAGE_ASTRA_FIRST
        $wastage_astra_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_astra_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'Astrazeneca')->where("priority",'a1')->first()->wastage_astra_bohol_first;
        $wastage_astra_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_astra_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'Astrazeneca')->where("priority",'a1')->first()->wastage_astra_cebu_first;
        $wastage_astra_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_astra_negros_first"))->where("province_id",3)->where("typeof_vaccine",'Astrazeneca')->where("priority",'a1')->first()->wastage_astra_negros_first;
        $wastage_astra_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_astra_siquior_first"))->where("province_id",4)->where("typeof_vaccine",'Astrazeneca')->where("priority",'a1')->first()->wastage_astra_siquior_first;

        //WASTAGE_SPUTNIKV_FIRST
        $wastage_sputnikv_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sputnikv_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'SputnikV')->where("priority",'a1')->first()->wastage_sputnikv_bohol_first;
        $wastage_sputnikv_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sputnikv_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'SputnikV')->where("priority",'a1')->first()->wastage_sputnikv_cebu_first;
        $wastage_sputnikv_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sputnikv_negros_first"))->where("province_id",3)->where("typeof_vaccine",'SputnikV')->where("priority",'a1')->first()->wastage_sputnikv_negros_first;
        $wastage_sputnikv_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sputnikv_siquior_first"))->where("province_id",4)->where("typeof_vaccine",'SputnikV')->where("priority",'a1')->first()->wastage_sputnikv_siquior_first;

        //WASTAGE_PFIZER_FIRST
        $wastage_pfizer_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_pfizer_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'Pfizer')->where("priority",'a1')->first()->wastage_pfizer_bohol_first;
        $wastage_pfizer_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_pfizer_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'Pfizer')->where("priority",'a1')->first()->wastage_pfizer_cebu_first;
        $wastage_pfizer_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_pfizer_negros_first"))->where("province_id",3)->where("typeof_vaccine",'Pfizer')->where("priority",'a1')->first()->wastage_pfizer_negros_first;
        $wastage_pfizer_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_pfizer_siquior_first"))->where("province_id",4)->where("typeof_vaccine",'Pfizer')->where("priority",'a1')->first()->wastage_pfizer_siquior_first;



        //REGION VACCINATED
        $region_sinovac_first_dose = $vcted_sinovac_bohol_first + $vcted_sinovac_cebu_first + $vcted_sinovac_negros_first + $vcted_sinovac_siquijor_first;
        $region_sinovac_second_dose = $vcted_sinovac_bohol_second + $vcted_sinovac_cebu_second + $vcted_sinovac_negros_second + $vcted_sinovac_siquijor_second;
        $region_astra_first_dose = $vcted_astra_bohol_first + $vcted_astra_cebu_first + $vcted_astra_negros_first + $vcted_astra_siquijor_first;
        $region_astra_second_dose = $vcted_astra_bohol_second + $vcted_astra_cebu_second + $vcted_astra_negros_second + $vcted_astra_siquijor_second;
        $region_sputnikv_first_dose = $vcted_sputnikv_bohol_first + $vcted_sputnikv_cebu_first + $vcted_sputnikv_negros_first + $vcted_sputnikv_siquijor_first;
        $region_sputnikv_second_dose = $vcted_sputnikv_bohol_second + $vcted_sputnikv_cebu_second + $vcted_sputnikv_negros_second + $vcted_sputnikv_siquijor_second;
        $region_pfizer_first_dose = $vcted_pfizer_bohol_first + $vcted_pfizer_cebu_first + $vcted_pfizer_negros_first + $vcted_pfizer_siquijor_first;
        $region_pfizer_second_dose = $vcted_pfizer_bohol_second + $vcted_pfizer_cebu_second + $vcted_pfizer_negros_second + $vcted_pfizer_siquijor_second;

        //PERCENT_COVERAGE_SINOVAC_FIRST
        $p_cvrge_sinovac_bohol_first = number_format($vcted_sinovac_bohol_first / $eli_pop_bohol * 100,2);
        $p_cvrge_sinovac_cebu_first = number_format($vcted_sinovac_cebu_first / $eli_pop_cebu * 100,2);
        $p_cvrge_sinovac_negros_first = number_format($vcted_sinovac_negros_first / $eli_pop_negros * 100,2);
        $p_cvrge_sinovac_siquijor_first = number_format($vcted_sinovac_siquijor_first / $eli_pop_siquijor * 100,2);

        //PERCENT_COVERAGE_ASTRA_FIRST
        $p_cvrge_astra_bohol_first = number_format($vcted_astra_bohol_first / $eli_pop_bohol * 100,2);
        $p_cvrge_astra_cebu_first = number_format($vcted_astra_cebu_first / $eli_pop_cebu * 100,2);
        $p_cvrge_astra_negros_first = number_format($vcted_astra_negros_first / $eli_pop_negros * 100,2);
        $p_cvrge_astra_siquijor_first = number_format($vcted_astra_siquijor_first / $eli_pop_siquijor * 100,2);

        //PERCENT_COVERAGE_SPUTNIKV_FIRST
        $p_cvrge_sputnikv_bohol_first = number_format($vcted_sputnikv_bohol_first / $eli_pop_bohol * 100,2);
        $p_cvrge_sputnikv_cebu_first = number_format($vcted_sputnikv_cebu_first / $eli_pop_cebu * 100,2);
        $p_cvrge_sputnikv_negros_first = number_format($vcted_sputnikv_negros_first / $eli_pop_negros * 100,2);
        $p_cvrge_sputnikv_siquijor_first = number_format($vcted_sputnikv_siquijor_first / $eli_pop_siquijor * 100,2);

        //PERCENT_COVERAGE_PFIZER_FIRST
        $p_cvrge_pfizer_bohol_first = number_format($vcted_pfizer_bohol_first / $eli_pop_bohol * 100,2);
        $p_cvrge_pfizer_cebu_first = number_format($vcted_pfizer_cebu_first / $eli_pop_cebu * 100,2);
        $p_cvrge_pfizer_negros_first = number_format($vcted_pfizer_negros_first / $eli_pop_negros * 100,2);
        $p_cvrge_pfizer_siquijor_first = number_format($vcted_pfizer_siquijor_first / $eli_pop_siquijor * 100,2);

        //TOTAL_PERCENT_COVERAGE_FIRST
        $total_p_cvrge_bohol_first = number_format($p_cvrge_sinovac_bohol_first + $p_cvrge_astra_bohol_first + $p_cvrge_sputnikv_bohol_first + $p_cvrge_pfizer_bohol_first,2);
        $total_p_cvrge_cebu_first = number_format($p_cvrge_sinovac_cebu_first + $p_cvrge_astra_cebu_first + $p_cvrge_sputnikv_cebu_first + $p_cvrge_pfizer_cebu_first,2);
        $total_p_cvrge_negros_first = number_format($p_cvrge_sinovac_negros_first + $p_cvrge_astra_negros_first + $p_cvrge_sputnikv_negros_first + $p_cvrge_pfizer_negros_first,2);
        $total_p_cvrge_siquijor_first = number_format($p_cvrge_sinovac_siquijor_first + $p_cvrge_astra_siquijor_first + $p_cvrge_sputnikv_siquijor_first + $p_cvrge_pfizer_siquijor_first,2);

        //PERCENT_COVERAGE_SINOVAC_SECOND
        $p_cvrge_sinovac_bohol_second = number_format($vcted_sinovac_bohol_second / $eli_pop_bohol * 100,2);
        $p_cvrge_sinovac_cebu_second = number_format($vcted_sinovac_cebu_second / $eli_pop_cebu * 100,2);
        $p_cvrge_sinovac_negros_second = number_format($vcted_sinovac_negros_second / $eli_pop_negros * 100,2);
        $p_cvrge_sinovac_siquijor_second = number_format($vcted_sinovac_siquijor_second / $eli_pop_siquijor * 100,2);

        //PERCENT_COVERAGE_ASTRA_SECOND
        $p_cvrge_astra_bohol_second = number_format($vcted_astra_bohol_second / $eli_pop_bohol * 100,2);
        $p_cvrge_astra_cebu_second = number_format($vcted_astra_cebu_second / $eli_pop_cebu * 100,2);
        $p_cvrge_astra_negros_second = number_format($vcted_astra_negros_second / $eli_pop_negros * 100,2);
        $p_cvrge_astra_siquijor_second = number_format($vcted_astra_siquijor_second / $eli_pop_siquijor * 100,2);

        //PERCENT_COVERAGE_SPUTNIKV_SECOND
        $p_cvrge_sputnikv_bohol_second = number_format($vcted_sputnikv_bohol_second / $eli_pop_bohol * 100,2);
        $p_cvrge_sputnikv_cebu_second = number_format($vcted_sputnikv_cebu_second / $eli_pop_cebu * 100,2);
        $p_cvrge_sputnikv_negros_second = number_format($vcted_sputnikv_negros_second / $eli_pop_negros * 100,2);
        $p_cvrge_sputnikv_siquijor_second = number_format($vcted_sputnikv_siquijor_second / $eli_pop_siquijor * 100,2);

        //PERCENT_COVERAGE_PFIZER_SECOND
        $p_cvrge_pfizer_bohol_second = number_format($vcted_pfizer_bohol_second / $eli_pop_bohol * 100,2);
        $p_cvrge_pfizer_cebu_second = number_format($vcted_pfizer_cebu_second / $eli_pop_cebu * 100,2);
        $p_cvrge_pfizer_negros_second = number_format($vcted_pfizer_negros_second / $eli_pop_negros * 100,2);
        $p_cvrge_pfizer_siquijor_second = number_format($vcted_pfizer_siquijor_second / $eli_pop_siquijor * 100,2);

        //TOTAL_PERCENT_COVERAGE_SECOND
        $total_p_cvrge_bohol_second = number_format($p_cvrge_sinovac_bohol_second + $p_cvrge_astra_bohol_second + $p_cvrge_sputnikv_bohol_second + $p_cvrge_pfizer_bohol_second,2);
        $total_p_cvrge_cebu_second = number_format($p_cvrge_sinovac_cebu_second + $p_cvrge_astra_cebu_second + $p_cvrge_sputnikv_cebu_second + $p_cvrge_pfizer_cebu_second,2);
        $total_p_cvrge_negros_second = number_format($p_cvrge_sinovac_negros_second + $p_cvrge_astra_negros_second + $p_cvrge_sputnikv_negros_second + $p_cvrge_pfizer_negros_second,2);
        $total_p_cvrge_siquijor_second = number_format($p_cvrge_sinovac_siquijor_second + $p_cvrge_astra_siquijor_second + $p_cvrge_sputnikv_siquijor_second + $p_cvrge_pfizer_siquijor_second,2);

        //TOTAL_P_COVERAGE_REGION_FIRST
        $total_p_cvrge_sinovac_region_first = $p_cvrge_sinovac_bohol_first + $p_cvrge_sinovac_cebu_first + $p_cvrge_sinovac_negros_first + $p_cvrge_sinovac_siquijor_first;
        $total_p_cvrge_astra_region_first = $p_cvrge_astra_bohol_first + $p_cvrge_astra_cebu_first + $p_cvrge_astra_negros_first + $p_cvrge_astra_siquijor_first;
        $total_p_cvrge_sputnikv_region_first = $p_cvrge_sputnikv_bohol_first + $p_cvrge_sputnikv_cebu_first + $p_cvrge_sputnikv_negros_first + $p_cvrge_sputnikv_siquijor_first;
        $total_p_cvrge_pfizer_region_first = $p_cvrge_pfizer_bohol_first + $p_cvrge_pfizer_cebu_first + $p_cvrge_pfizer_negros_first + $p_cvrge_pfizer_siquijor_first;
        $total_p_cvrge_region_first = $total_p_cvrge_sinovac_region_first + $total_p_cvrge_astra_region_first + $p_cvrge_sinovac_negros_first + $p_cvrge_sinovac_siquijor_first;

        //TOTAL_P_COVERAGE_REGION_FIRST
        $total_p_cvrge_sinovac_region_second = $p_cvrge_sinovac_bohol_second + $p_cvrge_sinovac_cebu_second + $p_cvrge_sinovac_negros_second + $p_cvrge_sinovac_siquijor_second;
        $total_p_cvrge_astra_region_second = $p_cvrge_astra_bohol_second + $p_cvrge_astra_cebu_second + $p_cvrge_astra_negros_second + $p_cvrge_astra_siquijor_second;
        $total_p_cvrge_sputnikv_region_second = $p_cvrge_sputnikv_bohol_second + $p_cvrge_sputnikv_cebu_second + $p_cvrge_sputnikv_negros_second + $p_cvrge_sputnikv_siquijor_second;
        $total_p_cvrge_pfizer_region_second = $p_cvrge_pfizer_bohol_second + $p_cvrge_pfizer_cebu_second + $p_cvrge_pfizer_negros_second + $p_cvrge_pfizer_siquijor_second;
        $total_p_cvrge_region_second = $total_p_cvrge_sinovac_region_second + $total_p_cvrge_astra_region_second + $p_cvrge_sinovac_negros_second + $p_cvrge_sinovac_siquijor_second;

        //TOTAL_WASTAGE
        $total_wastage_bohol = $wastage_sinovac_bohol_first + $wastage_astra_bohol_first + $wastage_sputnikv_bohol_first + $wastage_pfizer_bohol_first;
        $total_wastage_cebu = $wastage_sinovac_cebu_first + $wastage_astra_cebu_first + $wastage_sputnikv_cebu_first + $wastage_pfizer_cebu_first;
        $total_wastage_negros = $wastage_sinovac_negros_first + $wastage_astra_negros_first + $wastage_sputnikv_negros_first + $wastage_pfizer_negros_first;
        $total_wastage_siquijor = $wastage_sinovac_siquijor_first + $wastage_astra_siquijor_first + $wastage_sputnikv_siquijor_first + $wastage_pfizer_siquijor_first;

        //TOTAL REFUSAL
        $total_refusal_firts = $refused_first_bohol + $refused_first_cebu + $refused_first_negros + $refused_first_siquijor;
        $total_refusal_second = $refused_second_bohol + $refused_second_cebu + $refused_second_negros + $refused_second_siquijor;

        //TOTAL DEFFERED
        $total_deferred_first = $deferred_first_bohol + $deferred_first_cebu + $deferred_first_negros + $deferred_first_siquijor;
        $total_deferred_second = $deferred_second_bohol + $deferred_second_cebu + $deferred_second_negros + $deferred_second_siquijor;

        //WASTAGE_SINOVAC_FIRST
        $wastage_sinovac_first = $wastage_sinovac_bohol_first + $wastage_sinovac_cebu_first + $wastage_sinovac_negros_first + $wastage_sinovac_siquijor_first;
        $wastage_astra_first = $wastage_astra_bohol_first + $wastage_astra_cebu_first + $wastage_astra_negros_first + $wastage_astra_siquijor_first;
        $wastage_sputnikv_first = $wastage_sputnikv_bohol_first + $wastage_sputnikv_cebu_first + $wastage_sputnikv_negros_first + $wastage_sputnikv_siquijor_first;
        $wastage_pfizer_first = $wastage_pfizer_bohol_first + $wastage_pfizer_cebu_first + $wastage_pfizer_negros_first + $wastage_pfizer_siquijor_first;

        //WASTAGE
        $wastage_region = $wastage_sinovac_first + $wastage_astra_first + $wastage_sputnikv_first + $wastage_pfizer_first;

        //CONSUMPTION_RATE_SINOVAC_FIRST
        $c_rate_sinovac_bohol_first = number_format($vcted_sinovac_bohol_first / $sinovac_bohol * 100,2);
        $c_rate_sinovac_cebu_first = number_format($vcted_sinovac_cebu_first / $sinovac_cebu * 100,2);
        $c_rate_sinovac_negros_first = number_format($vcted_sinovac_negros_first / $sinovac_negros * 100,2);
        $c_rate_sinovac_siquijor_first = number_format($vcted_sinovac_siquijor_first / $sinovac_siquijor * 100,2);

        //CONSUMPTION_RATE_ASTRAZENECA_FIRST
        $c_rate_astra_bohol_first = number_format($vcted_astra_bohol_first / $astra_bohol * 100,2);
        $c_rate_astra_cebu_first = number_format($vcted_astra_cebu_first / $astra_cebu * 100,2);
        $c_rate_astra_negros_first = number_format($vcted_astra_negros_first / $astra_negros * 100,2);
        $c_rate_astra_siquijor_first = number_format($vcted_astra_siquijor_first / $astra_siquijor * 100,2);

        //CONSUMPTION_RATE_SPUTNIKV_FIRST
        $c_rate_sputnikv_bohol_first = number_format($vcted_sputnikv_bohol_first / $sputnikv_bohol * 100,2);
        $c_rate_sputnikv_cebu_first = number_format($vcted_sputnikv_cebu_first / $sputnikv_cebu * 100,2);
        $c_rate_sputnikv_negros_first = number_format($vcted_sputnikv_negros_first / $sputnikv_negros * 100,2);
        $c_rate_sputnikv_siquijor_first = number_format($vcted_sputnikv_siquijor_first / $sputnikv_siquijor * 100,2);

        //CONSUMPTION_RATE_PFIZER_FIRST
        $c_rate_pfizer_bohol_first = number_format($vcted_pfizer_bohol_first / $pfizer_bohol * 100,2);
        $c_rate_pfizer_cebu_first = number_format($vcted_pfizer_cebu_first / $pfizer_cebu * 100,2);
        $c_rate_pfizer_negros_first = number_format($vcted_pfizer_negros_first / $pfizer_negros * 100,2);
        $c_rate_pfizer_siquijor_first = number_format($vcted_pfizer_siquijor_first / $pfizer_siquijor * 100,2);

        //TOTAL_CONSUMPTION_RATE
        $total_c_rate_bohol = $c_rate_sinovac_bohol_first + $c_rate_astra_bohol_first + $c_rate_sputnikv_bohol_first + $c_rate_pfizer_bohol_first;
        $total_c_rate_cebu = $c_rate_sinovac_cebu_first + $c_rate_astra_cebu_first + $c_rate_sputnikv_cebu_first + $c_rate_pfizer_cebu_first;
        $total_c_rate_negros = $c_rate_sinovac_negros_first + $c_rate_astra_negros_first + $c_rate_sputnikv_negros_first + $c_rate_pfizer_negros_first;
        $total_c_rate_siquijor = $c_rate_sinovac_siquijor_first + $c_rate_astra_siquijor_first + $c_rate_sputnikv_siquijor_first + $c_rate_pfizer_siquijor_first;

        //CONSUMPTION_RATE_SINOVAC_SECOND
        $c_rate_sinovac_bohol_first = number_format($vcted_sinovac_bohol_first / $sinovac_bohol * 100,2);
        $c_rate_sinovac_cebu_first = number_format($vcted_sinovac_cebu_first / $sinovac_cebu * 100,2);
        $c_rate_sinovac_negros_first = number_format($vcted_sinovac_negros_first / $sinovac_negros * 100,2);
        $c_rate_sinovac_siquijor_first = number_format($vcted_sinovac_siquijor_first / $sinovac_siquijor * 100,2);

        //CONSUMPTION_RATE_ASTRAZENECA_FIRST
        $c_rate_astra_bohol_first = number_format($vcted_astra_bohol_first / $astra_bohol * 100,2);
        $c_rate_astra_cebu_first = number_format($vcted_astra_cebu_first / $astra_cebu * 100,2);
        $c_rate_astra_negros_first = number_format($vcted_astra_negros_first / $astra_negros * 100,2);
        $c_rate_astra_siquijor_first = number_format($vcted_astra_siquijor_first / $astra_siquijor * 100,2);


        return view('vaccine.vaccine_summary_report',[
            "sinovac_bohol" => $sinovac_bohol,
            "sinovac_cebu" => $sinovac_cebu,
            "sinovac_negros" => $sinovac_negros,
            "sinovac_siquijor" => $sinovac_siquijor,
            "sinovac_cebu_facility" => $sinovac_cebu_facility,
            "sinovac_mandaue_facility" => $sinovac_mandaue_facility,
            "sinovac_lapu_facility" => $sinovac_lapu_facility,
            "sinovac_region" => $sinovac_bohol + $sinovac_cebu + $sinovac_negros + $sinovac_siquijor + $sinovac_cebu_facility + $sinovac_mandaue_facility + $sinovac_lapu_facility,

            "astra_bohol" => $astra_bohol,
            "astra_cebu" => $astra_cebu,
            "astra_negros" => $astra_negros,
            "astra_siquijor" => $astra_siquijor,
            "astra_cebu_facility" => $astra_cebu_facility,
            "astra_mandaue_facility" => $astra_mandaue_facility,
            "astra_lapu_facility" => $astra_lapu_facility,
            "astra_region" => $astra_bohol + $astra_cebu + $astra_negros + $astra_siquijor + $astra_cebu_facility + $astra_mandaue_facility + $astra_lapu_facility,

            "sputnikv_bohol" => $sputnikv_bohol,
            "sputnikv_cebu" => $sputnikv_cebu,
            "sputnikv_negros" => $sputnikv_negros,
            "sputnikv_siquijor" => $sputnikv_siquijor,
            "sputnikv_cebu_facility" => $sputnikv_cebu_facility,
            "sputnikv_mandaue_facility" => $sputnikv_mandaue_facility,
            "sputnikv_lapu_facility" => $sputnikv_lapu_facility,
            "sputnikv_region" => $sputnikv_bohol + $sputnikv_cebu + $sputnikv_negros + $sputnikv_siquijor + $sputnikv_cebu_facility + $sputnikv_mandaue_facility + $sputnikv_lapu_facility,


            "pfizer_bohol" => $pfizer_bohol,
            "pfizer_cebu" => $pfizer_cebu,
            "pfizer_negros" => $pfizer_negros,
            "pfizer_siquijor" => $pfizer_siquijor,
            "pfizer_cebu_facility" => $pfizer_cebu_facility,
            "pfizer_mandaue_facility" => $pfizer_mandaue_facility,
            "pfizer_lapu_facility" => $pfizer_lapu_facility,
            "pfizer_region" => $pfizer_bohol + $pfizer_cebu + $pfizer_negros + $pfizer_siquijor + $pfizer_cebu_facility + $pfizer_mandaue_facility + $pfizer_lapu_facility,

            "total_region" => $sinovac_region + $astra_region + $sputnikv_region + $pfizer_region,
            "total_bohol" => $sinovac_bohol + $astra_bohol + $sputnikv_bohol + $pfizer_bohol,
            "total_negros" => $sinovac_negros + $astra_negros + $sputnikv_negros + $pfizer_negros,
            "total_siquijor" => $sinovac_siquijor + $astra_siquijor + $sputnikv_siquijor + $pfizer_siquijor,
            "total_cebu_facility" => $sinovac_cebu_facility + $astra_cebu_facility + $sputnikv_cebu_facility + $pfizer_cebu_facility,
            "total_mandaue_facility" => $sinovac_mandaue_facility + $astra_mandaue_facility + $sputnikv_mandaue_facility + $pfizer_mandaue_facility,
            "total_lapu_facility" => $sinovac_lapu_facility + $astra_lapu_facility + $sputnikv_lapu_facility + $pfizer_lapu_facility,

            //ELIGIBLE POP
            "eli_pop_bohol" => $eli_pop_bohol,
            "eli_pop_cebu" => $eli_pop_cebu,
            "eli_pop_negros" => $eli_pop_negros,
            "eli_pop_siquijor" => $eli_pop_siquijor,
            "eli_pop_cebu_facility"=> $eli_pop_cebu_facility,
            "eli_pop_mandaue_facility"=> $eli_pop_mandaue_facility,
            "eli_pop_lapu_facility"=> $eli_pop_lapu_facility,

            //VACCINATED_SINOVAC_FIRST
            "vcted_sinovac_bohol_first" => $vcted_sinovac_bohol_first,
            "vcted_sinovac_cebu_first" => $vcted_sinovac_cebu_first,
            "vcted_sinovac_negros_first" => $vcted_sinovac_negros_first,
            "vcted_sinovac_siquijor_first" => $vcted_sinovac_siquijor_first,
            //"vcted_sinovac_cebu_facility_first" => $vcted_sinovac_cebu_facility_first,

            //"vcted_sinovac_cebu_faclity_first" => $vcted_sinovac_cebu_faclity_first,
            //"vcted_sinovac_mandaue_faclity_first" => $vcted_sinovac_mandaue_faclity_first,
            //"vcted_sinovac_lapu_faclity_first" => $vcted_sinovac_lapu_faclity_first,

            //VACCINATED_SINOVAC_SECOND
            "vcted_sinovac_bohol_second" => $vcted_sinovac_bohol_second,
            "vcted_sinovac_cebu_second" => $vcted_sinovac_cebu_second,
            "vcted_sinovac_negros_second" => $vcted_sinovac_negros_second,
            "vcted_sinovac_siquijor_second" => $vcted_sinovac_siquijor_second,

            //VACCINATED_ASTRAZENECA_FIRST
            "vcted_astra_bohol_first" => $vcted_astra_bohol_first,
            "vcted_astra_cebu_first" => $vcted_astra_cebu_first,
            "vcted_astra_negros_first" => $vcted_astra_negros_first,
            "vcted_astra_siquijor_first" => $vcted_astra_siquijor_first,

            //VACCINATED_ASTRAZENECA_SECOND
            "vcted_astra_bohol_second" => $vcted_astra_bohol_second,
            "vcted_astra_cebu_second" => $vcted_astra_cebu_second,
            "vcted_astra_negros_second" => $vcted_astra_negros_second,
            "vcted_astra_siquijor_second" => $vcted_astra_siquijor_second,

            //VACCINATED_SPUTNIKV_FIRST
            "vcted_sputnikv_bohol_first" => $vcted_sputnikv_bohol_first,
            "vcted_sputnikv_cebu_first" => $vcted_sputnikv_cebu_first,
            "vcted_sputnikv_negros_first" => $vcted_sputnikv_negros_first,
            "vcted_sputnikv_siquijor_first" => $vcted_sputnikv_siquijor_first,

            //VACCINATED_SPUTNIKV_SECOND
            "vcted_sputnikv_bohol_second" => $vcted_sputnikv_bohol_second,
            "vcted_sputnikv_cebu_second" => $vcted_sputnikv_cebu_second,
            "vcted_sputnikv_negros_second" => $vcted_sputnikv_negros_second,
            "vcted_sputnikv_siquijor_second" => $vcted_sputnikv_siquijor_second,

             //VACCINATED_PFIZER_FIRST
            "vcted_pfizer_bohol_first" => $vcted_pfizer_bohol_first,
            "vcted_pfizer_cebu_first" => $vcted_pfizer_cebu_first,
            "vcted_pfizer_negros_first" => $vcted_pfizer_negros_first,
            "vcted_pfizer_siquijor_first" => $vcted_pfizer_siquijor_first,

            //VACCINATED_PFIZER_SECOND
            "vcted_pfizer_bohol_second" => $vcted_pfizer_bohol_second,
            "vcted_pfizer_cebu_second" => $vcted_pfizer_cebu_second,
            "vcted_pfizer_negros_second" => $vcted_pfizer_negros_second,
            "vcted_pfizer_siquijor_second" => $vcted_pfizer_siquijor_second,

            //TOTAL VACCINATED_FIRST
            "total_vcted_first_bohol" => $vcted_sinovac_bohol_first + $vcted_astra_bohol_first + $vcted_sputnikv_bohol_first + $vcted_pfizer_bohol_first,
            "total_vcted_first_cebu" => $vcted_sinovac_cebu_first + $vcted_astra_cebu_first + $vcted_sputnikv_cebu_first + $vcted_pfizer_cebu_first,
            "total_vcted_first_negros" => $vcted_sinovac_negros_first + $vcted_astra_negros_first + $vcted_sputnikv_negros_first + $vcted_pfizer_negros_first,
            "total_vcted_first_siquijor" => $vcted_sinovac_siquijor_first + $vcted_astra_siquijor_first + $vcted_sputnikv_siquijor_first + $vcted_pfizer_siquijor_first,

            //TOTAL VACCINATED SECOND
            "total_vcted_second_bohol" => $vcted_sinovac_bohol_second + $vcted_astra_bohol_second + $vcted_sputnikv_bohol_second + $vcted_pfizer_bohol_second,
            "total_vcted_second_cebu" => $vcted_sinovac_cebu_second + $vcted_astra_cebu_second + $vcted_sputnikv_siquijor_second + $vcted_pfizer_cebu_second,
            "total_vcted_second_negros" => $vcted_sinovac_negros_second + $vcted_astra_negros_second + $vcted_sputnikv_negros_second + $vcted_pfizer_negros_second,
            "total_vcted_second_siquijor" => $vcted_sinovac_siquijor_second + $vcted_astra_siquijor_second + $vcted_sputnikv_siquijor_second + $vcted_pfizer_siquijor_second,

            //REGION VACCINATED
            "region_sinovac_first_dose" => $vcted_sinovac_bohol_first + $vcted_sinovac_cebu_first + $vcted_sinovac_negros_first + $vcted_sinovac_siquijor_first,
            "region_sinovac_second_dose" => $vcted_sinovac_bohol_second + $vcted_sinovac_cebu_second + $vcted_sinovac_negros_second + $vcted_sinovac_siquijor_second,
            "region_astra_first_dose" => $vcted_astra_bohol_first + $vcted_astra_cebu_first + $vcted_astra_negros_first + $vcted_astra_siquijor_first,
            "region_astra_second_dose" => $vcted_astra_bohol_second + $vcted_astra_cebu_second + $vcted_astra_negros_second + $vcted_astra_siquijor_second,
            "region_sputnikv_first_dose" => $vcted_sputnikv_bohol_first + $vcted_sputnikv_cebu_first + $vcted_sputnikv_negros_first + $vcted_sputnikv_siquijor_first,
            "region_sputnikv_second_dose" => $vcted_sputnikv_bohol_second + $vcted_sputnikv_cebu_second + $vcted_sputnikv_negros_second + $vcted_sputnikv_siquijor_second,
            "region_pfizer_first_dose" => $vcted_pfizer_bohol_first + $vcted_pfizer_cebu_first + $vcted_pfizer_negros_first + $vcted_pfizer_siquijor_first,
            "region_pfizer_second_dose" => $vcted_pfizer_bohol_second + $vcted_pfizer_cebu_second + $vcted_pfizer_negros_second + $vcted_pfizer_siquijor_second,

            //TOTAL DOSE
            "first_dose_total" => $region_sinovac_first_dose + $region_astra_first_dose + $region_sputnikv_first_dose + $region_pfizer_first_dose,
            "second_dose_total" => $region_sinovac_second_dose + $region_astra_second_dose + $region_sputnikv_second_dose + $region_pfizer_second_dose,

            //TOTAL ELI POP
            "total_elipop_region" => $eli_pop_bohol + $eli_pop_cebu + $eli_pop_siquijor + $eli_pop_cebu_facility + $eli_pop_mandaue_facility + $eli_pop_lapu_facility,

            //PERCENT COVERAGE SINOVAC FIRST DOSE
            "p_cvrge_sinovac_bohol_first" => $p_cvrge_sinovac_bohol_first,
            "p_cvrge_sinovac_cebu_first" => $p_cvrge_sinovac_cebu_first,
            "p_cvrge_sinovac_negros_first" => $p_cvrge_sinovac_negros_first,
            "p_cvrge_sinovac_siquijor_first" => $p_cvrge_sinovac_siquijor_first,

            //PERCENT COVERAGE ASTRA FIRST DOSE
            "p_cvrge_astra_bohol_first" => $p_cvrge_astra_bohol_first,
            "p_cvrge_astra_cebu_first" => $p_cvrge_astra_cebu_first,
            "p_cvrge_astra_negros_first" => $p_cvrge_astra_negros_first,
            "p_cvrge_astra_siquijor_first" => $p_cvrge_astra_siquijor_first,

            //PERCENT COVERAGE SPUTNIKV FIRST DOSE
            "p_cvrge_sputnikv_bohol_first" => $p_cvrge_sputnikv_bohol_first,
            "p_cvrge_sputnikv_cebu_first" => $p_cvrge_sputnikv_cebu_first,
            "p_cvrge_sputnikv_negros_first" => $p_cvrge_sputnikv_negros_first,
            "p_cvrge_sputnikv_siquijor_first" => $p_cvrge_sputnikv_siquijor_first,

            //PERCENT COVERAGE PFIZER FIRST DOSE
            "p_cvrge_pfizer_bohol_first" => $p_cvrge_pfizer_bohol_first,
            "p_cvrge_pfizer_cebu_first" => $p_cvrge_pfizer_cebu_first,
            "p_cvrge_pfizer_negros_first" => $p_cvrge_pfizer_negros_first,
            "p_cvrge_pfizer_siquijor_first" => $p_cvrge_pfizer_siquijor_first,

            //TOTAL_PERCENT_COVERAGE_FIRST
            "total_p_cvrge_bohol_first" => $total_p_cvrge_bohol_first,
            "total_p_cvrge_cebu_first" => $total_p_cvrge_cebu_first,
            "total_p_cvrge_negros_first" => $total_p_cvrge_negros_first,
            "total_p_cvrge_siquijor_first" => $total_p_cvrge_siquijor_first,

            //PERCENT COVERAGE SINOVAC SECOND DOSE
            "p_cvrge_sinovac_bohol_second" => $p_cvrge_sinovac_bohol_second,
            "p_cvrge_sinovac_cebu_second" => $p_cvrge_sinovac_cebu_second,
            "p_cvrge_sinovac_negros_second" => $p_cvrge_sinovac_negros_second,
            "p_cvrge_sinovac_siquijor_second" => $p_cvrge_sinovac_siquijor_second,

            //PERCENT COVERAGE ASTRA SECOND DOSE
            "p_cvrge_astra_bohol_second" => $p_cvrge_astra_bohol_second,
            "p_cvrge_astra_cebu_second" => $p_cvrge_astra_cebu_second,
            "p_cvrge_astra_negros_second" => $p_cvrge_astra_negros_second,
            "p_cvrge_astra_siquijor_second" => $p_cvrge_astra_siquijor_second,

            //PERCENT COVERAGE SPUTNIKV SECOND DOSE
            "p_cvrge_sputnikv_bohol_second" => $p_cvrge_sputnikv_bohol_second,
            "p_cvrge_sputnikv_cebu_second" => $p_cvrge_sputnikv_cebu_second,
            "p_cvrge_sputnikv_negros_second" => $p_cvrge_sputnikv_negros_second,
            "p_cvrge_sputnikv_siquijor_second" => $p_cvrge_sputnikv_siquijor_second,

            //PERCENT COVERAGE PFIZER SECOND DOSE
            "p_cvrge_pfizer_bohol_second" => $p_cvrge_pfizer_bohol_second,
            "p_cvrge_pfizer_cebu_second" => $p_cvrge_pfizer_cebu_second,
            "p_cvrge_pfizer_negros_second" => $p_cvrge_pfizer_negros_second,
            "p_cvrge_pfizer_siquijor_second" => $p_cvrge_pfizer_siquijor_second,

            //TOTAL_PERCENT_COVERAGE_SECOND
            "total_p_cvrge_bohol_second" => $total_p_cvrge_bohol_second,
            "total_p_cvrge_cebu_second" => $total_p_cvrge_cebu_second,
            "total_p_cvrge_negros_second" => $total_p_cvrge_negros_second,
            "total_p_cvrge_siquijor_second" => $total_p_cvrge_siquijor_second,

            //TOTAL_REGION_COVERAGE_FIRST
            "total_p_cvrge_sinovac_region_first" => $total_p_cvrge_sinovac_region_first,
            "total_p_cvrge_astra_region_first" => $total_p_cvrge_astra_region_first,
            "total_p_cvrge_sputnikv_region_first" => $total_p_cvrge_sputnikv_region_first,
            "total_p_cvrge_pfizer_region_first" => $total_p_cvrge_pfizer_region_first,
            "total_p_cvrge_region_first" => $total_p_cvrge_region_first,

             //TOTAL_REGION_COVERAGE_SECOND
            "total_p_cvrge_sinovac_region_second" => $total_p_cvrge_sinovac_region_second,
            "total_p_cvrge_astra_region_second" => $total_p_cvrge_astra_region_second,
            "total_p_cvrge_sputnikv_region_second" => $total_p_cvrge_sputnikv_region_second,
            "total_p_cvrge_pfizer_region_second" => $total_p_cvrge_pfizer_region_second,
            "total_p_cvrge_region_second" => $total_p_cvrge_region_second,

            //WASTAGE_SINOVAC_FIRST
           "wastage_sinovac_bohol_first" => $wastage_sinovac_bohol_first,
           "wastage_sinovac_cebu_first" => $wastage_sinovac_cebu_first,
           "wastage_sinovac_negros_first" => $wastage_sinovac_negros_first,
           "wastage_sinovac_siquijor_first" => $wastage_sinovac_siquijor_first,

            //WASTAGE_ASTRA_FIRST
            "wastage_astra_bohol_first" => $wastage_astra_bohol_first,
            "wastage_astra_cebu_first" => $wastage_astra_cebu_first,
            "wastage_astra_negros_first" => $wastage_astra_negros_first,
            "wastage_astra_siquijor_first" => $wastage_astra_siquijor_first,

            //WASTAGE_SPUTNIKV_FIRST
            "wastage_sputnikv_bohol_first" => $wastage_sputnikv_bohol_first,
            "wastage_sputnikv_cebu_first" => $wastage_sputnikv_cebu_first,
            "wastage_sputnikv_negros_first" => $wastage_sputnikv_negros_first,
            "wastage_sputnikv_siquijor_first" => $wastage_sputnikv_siquijor_first,

            //WASTAGE_PFIZER_FIRST
            "wastage_pfizer_bohol_first" => $wastage_pfizer_bohol_first,
            "wastage_pfizer_cebu_first" => $wastage_pfizer_cebu_first,
            "wastage_pfizer_negros_first" => $wastage_pfizer_negros_first,
            "wastage_pfizer_siquijor_first" => $wastage_pfizer_siquijor_first,

            //REFUSED_FIRST
            "refused_first_bohol" => $refused_first_bohol,
            "refused_first_cebu" => $refused_first_cebu,
            "refused_first_negros" => $refused_first_negros,
            "refused_first_siquijor" => $refused_first_siquijor,

             //REFUSED_SECOND
             "refused_second_bohol" => $refused_second_bohol,
             "refused_second_cebu" => $refused_second_cebu,
             "refused_second_negros" => $refused_second_negros,
             "refused_second_siquijor" => $refused_second_siquijor,

            //DEFERRED_FIRST
            "deferred_first_bohol" => $deferred_first_bohol,
            "deferred_first_cebu" => $deferred_first_cebu,
            "deferred_first_negros" => $deferred_first_negros,
            "deferred_first_siquijor" => $deferred_first_siquijor,

            //DEFERRED_SECOND
            "deferred_second_bohol" => $deferred_second_bohol,
            "deferred_second_cebu" => $deferred_second_cebu,
            "deferred_second_negros" => $deferred_second_negros,
            "deferred_second_siquijor" => $deferred_second_siquijor,

            //TOTAL_WASTAGE
            "total_wastage_bohol" => $total_wastage_bohol,
            "total_wastage_cebu" => $total_wastage_cebu,
            "total_wastage_negros" => $total_wastage_negros,
            "total_wastage_siquijor" => $total_wastage_siquijor,

            //TOTAL REFUSAL
            "total_refusal_first" =>$total_refusal_firts,
            "total_refusal_second" =>$total_refusal_second,

            //TOTAL DEFERRED
            "total_deferred_first" =>$total_deferred_first,
            "total_deferred_second" =>$total_deferred_second,

            //TOTAL WASTAGE
            "wastage_sinovac_first" => $wastage_sinovac_first,
            "wastage_astra_first" => $wastage_astra_first,
            "wastage_sputnikv_first" => $wastage_sputnikv_first,
            "wastage_pfizer_first" => $wastage_pfizer_first,
            "wastage_region" => $wastage_region,

            //CONSUMPTION_RATE_SINOVAC
            "c_rate_sinovac_bohol_first" => $c_rate_sinovac_bohol_first,
            "c_rate_sinovac_cebu_first" => $c_rate_sinovac_cebu_first,
            "c_rate_sinovac_negros_first" => $c_rate_sinovac_negros_first,
            "c_rate_sinovac_siquijor_first" => $c_rate_sinovac_siquijor_first,

            //CONSUMPTION_RATE_ASTRA
            "c_rate_astra_bohol_first" => $c_rate_astra_bohol_first,
            "c_rate_astra_cebu_first" => $c_rate_astra_cebu_first,
            "c_rate_astra_negros_first" => $c_rate_astra_negros_first,
            "c_rate_astra_siquijor_first" => $c_rate_astra_siquijor_first,

            //CONSUMPTION_RATE_SPUTNIKV
            "c_rate_sputnikv_bohol_first" => $c_rate_sputnikv_bohol_first,
            "c_rate_sputnikv_cebu_first" => $c_rate_sputnikv_cebu_first,
            "c_rate_sputnikv_negros_first" => $c_rate_sputnikv_negros_first,
            "c_rate_sputnikv_siquijor_first" => $c_rate_sputnikv_siquijor_first,

            //CONSUMPTION_RATE_PFIZER
            "c_rate_pfizer_bohol_first" => $c_rate_pfizer_bohol_first,
            "c_rate_pfizer_cebu_first" => $c_rate_pfizer_cebu_first,
            "c_rate_pfizer_negros_first" => $c_rate_pfizer_negros_first,
            "c_rate_pfizer_siquijor_first" => $c_rate_pfizer_siquijor_first,

            //TOTAL_CONSUMPTION_RATE
            "total_c_rate_bohol" => $total_c_rate_bohol,
            "total_c_rate_cebu" => $total_c_rate_cebu,
            "total_c_rate_negros" => $total_c_rate_negros,
            "total_c_rate_siquijor" => $total_c_rate_siquijor,


        ]);

    }
    public function vaccineTab5Report()
    {
        return view( 'vaccine.vaccine_tab5_report');
    }
    public function vaccineTab6Report()
    {
        return view( 'vaccine.vaccine_tab6_report');
    }
    public function vaccineTab7Report()
    {
        return view( 'vaccine.vaccine_tab7_report');
    }
}



