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
    public $sinovac_bohol,$sinovac_cebu,$sinovac_negros,$sinovac_siquijor,$sinovac_cebu_facility,$sinovac_mandaue_facility,$sinovac_lapu_facility;
    public $astra_bohol, $astra_cebu, $astra_negros, $astra_siquijor, $astra_cebu_facility, $astra_mandaue_facility, $astra_lapu_facility;
    public $sputnikv_bohol, $sputnikv_cebu, $sputnikv_negros, $sputnikv_siquijor, $sputnikv_cebu_facility, $sputnikv_mandaue_facility, $sputnikv_lapu_facility;
    public $pfizer_bohol, $pfizer_cebu, $pfizer_negros, $pfizer_siquijor, $pfizer_cebu_facility, $pfizer_mandaue_facility, $pfizer_lapu_facility;
    public $sinovac_region, $astra_region, $sputnikv_region, $pfizer_region;
    public $total_bohol, $total_cebu, $total_negros, $total_siquijor, $total_cebu_facility, $total_mandaue_facility, $total_lapu_facility, $total_region; //TOTAL REGION
    public $eli_pop_bohol, $eli_pop_cebu, $eli_pop_negros, $eli_pop_siquijor, $eli_pop_cebu_facility, $eli_pop_mandaue_facility, $eli_pop_lapu_facility; //ELIGIBLE POP SINOVAC
    public $vcted_sinovac_bohol_first, $vcted_sinovac_cebu_first, $vcted_sinovac_negros_first, $vcted_sinovac_siquijor_first,
           $vcted_sinovac_cebu_facility_first, $vcted_sinovac_mandaue_facility_first, $vcted_sinovac_lapu_facility_first; //VACCINATED_SINOVAC_FIRST
    public $vcted_sinovac_bohol_second, $vcted_sinovac_cebu_second, $vcted_sinovac_negros_second, $vcted_sinovac_siquijor_second,
           $vcted_sinovac_cebu_facility_second, $vcted_sinovac_mandaue_facility_second, $vcted_sinovac_lapu_facility_second; //VACCINATED_SINOVAC_SECOND
    public $vcted_astra_bohol_first, $vcted_astra_cebu_first, $vcted_astra_negros_first, $vcted_astra_siquijor_first,
           $vcted_astra_cebu_facility_first, $vcted_astra_mandaue_facility_first, $vcted_astra_lapu_facility_first; //VACCINATED_ASTRA_FIRST
    public $vcted_astra_bohol_second, $vcted_astra_cebu_second, $vcted_astra_negros_second, $vcted_astra_siquijor_second,
           $vcted_astra_cebu_facility_second, $vcted_astra_mandaue_facility_second, $vcted_astra_lapu_facility_second; //VACCINATED_SINOVAC_SECOND
    public $vcted_sputnikv_bohol_first, $vcted_sputnikv_cebu_first, $vcted_sputnikv_negros_first, $vcted_sputnikv_siquijor_first,
           $vcted_sputnikv_cebu_facility_first, $vcted_sputnikv_mandaue_facility_first, $vcted_sputnikv_lapu_facility_first; //VACCINATED_SPUTNIKV_FIRST
    public $vcted_sputnikv_bohol_second, $vcted_sputnikv_cebu_second, $vcted_sputnikv_negros_second, $vcted_sputnikv_siquijor_second,
           $vcted_sputnikv_cebu_facility_second, $vcted_sputnikv_mandaue_facility_second, $vcted_sputnikv_lapu_facility_second;  //VACCINATED_SPUTNIKV_SECOND
    public $vcted_pfizer_bohol_first, $vcted_pfizer_cebu_first, $vcted_pfizer_negros_first, $vcted_pfizer_siquijor_first,
           $vcted_pfizer_cebu_facility_first, $vcted_pfizer_mandaue_facility_first, $vcted_pfizer_lapu_facility_first; //VACCINATED_PFIZER_FIRST
    public $vcted_pfizer_bohol_second, $vcted_pfizer_cebu_second, $vcted_pfizer_negros_second, $vcted_pfizer_siquijor_second,
           $vcted_pfizer_cebu_facility_second, $vcted_pfizer_mandaue_facility_second, $vcted_pfizer_lapu_facility_second;  //VACCINATED_SPUTNIKV_SECOND
    public $total_vcted_first_bohol, $total_vcted_first_cebu, $total_vcted_first_negros, $total_vcted_first_siquijor,
           $total_vcted_cebu_facility_first, $total_vcted_mandaue_facility_first, $total_vcted_lapu_facility_first;   //TOTAL VACCINATED_FIRST
    public $total_vcted_second_bohol, $total_vcted_second_cebu, $total_vcted_second_negros, $total_vcted_second_siquijor,
           $total_vcted_cebu_facility_second, $total_vcted_mandaue_facility_second, $total_vcted_lapu_facility_second;   //TOTAL VACCINATED_SECOND
    public $region_sinovac_first_dose, $region_sinovac_second_dose, $region_astra_first_dose, $region_astra_second_dose,
           $region_sputnikv_first_dose, $region_sputnikv_second_dose, $region_pfizer_first_dose, $region_pfizer_second_dose; //REGION VACCINATED
    public $first_dose_total, $second_dose_total;   //TOTAL DOSE
    public $total_elipop_region;  //TOTAL ELI POP
    public $p_cvrge_sinovac_bohol_first, $p_cvrge_sinovac_cebu_first, $p_cvrge_sinovac_negros_first, $p_cvrge_sinovac_siquijor_first,
           $p_cvrge_sinovac_cebu_facility_first, $p_cvrge_sinovac_mandaue_facility_first, $p_cvrge_sinovac_lapu_facility_first;   //PERCENT COVERAGE SINOVAC FIRST
    public $p_cvrge_astra_bohol_first, $p_cvrge_astra_cebu_first, $p_cvrge_astra_negros_first, $p_cvrge_astra_siquijor_first,
           $p_cvrge_astra_cebu_facility_first, $p_cvrge_astra_mandaue_facility_first, $p_cvrge_astra_lapu_facility_first;   //PERCENT COVERAGE ASTRA FIRST
    public $p_cvrge_sputnikv_bohol_first, $p_cvrge_sputnikv_cebu_first, $p_cvrge_sputnikv_negros_first, $p_cvrge_sputnikv_siquijor_first,
           $p_cvrge_sputnikv_cebu_facility_first, $p_cvrge_sputnikv_mandaue_facility_first, $p_cvrge_sputnikv_lapu_facility_first;   //PERCENT COVERAGE SPUTNIKV FIRST
    public $p_cvrge_pfizer_bohol_first, $p_cvrge_pfizer_cebu_first, $p_cvrge_pfizer_negros_first, $p_cvrge_pfizer_siquijor_first,
           $p_cvrge_pfizer_cebu_facility_first, $p_cvrge_pfizer_mandaue_facility_first, $p_cvrge_pfizer_lapu_facility_first;   //PERCENT COVERAGE PFIZER FIRST
    public $total_p_cvrge_bohol_first, $total_p_cvrge_cebu_first, $total_p_cvrge_negros_first, $total_p_cvrge_siquijor_first,
           $total_p_cvrge_cebu_facility_first, $total_p_cvrge_mandaue_facility_first, $total_p_cvrge_lapu_facility_first;  //TOTAL_PERCENT_COVERAGE_FIRST
    public $p_cvrge_sinovac_bohol_second, $p_cvrge_sinovac_cebu_second, $p_cvrge_sinovac_negros_second, $p_cvrge_sinovac_siquijor_second,
           $p_cvrge_sinovac_cebu_facility_second, $p_cvrge_sinovac_mandaue_facility_second, $p_cvrge_sinovac_lapu_facility_second;   //PERCENT COVERAGE SINOVAC SECOND
    public $p_cvrge_astra_bohol_second, $p_cvrge_astra_cebu_second, $p_cvrge_astra_negros_second, $p_cvrge_astra_siquijor_second,
           $p_cvrge_astra_cebu_facility_second, $p_cvrge_astra_mandaue_facility_second, $p_cvrge_astra_lapu_facility_second;   //PERCENT COVERAGE ASTRA SECOND
    public $p_cvrge_sputnikv_bohol_second, $p_cvrge_sputnikv_cebu_second, $p_cvrge_sputnikv_negros_second, $p_cvrge_sputnikv_siquijor_second,
           $p_cvrge_sputnikv_cebu_facility_second, $p_cvrge_sputnikv_mandaue_facility_second, $p_cvrge_sputnikv_lapu_facility_second;   //PERCENT COVERAGE SPUTNIKV SECOND
    public $p_cvrge_pfizer_bohol_second, $p_cvrge_pfizer_cebu_second, $p_cvrge_pfizer_negros_second, $p_cvrge_pfizer_siquijor_second,
           $p_cvrge_pfizer_cebu_facility_second, $p_cvrge_pfizer_mandaue_facility_second, $p_cvrge_pfizer_lapu_facility_second;   //PERCENT COVERAGE PFIZER SECOND
    public $total_p_cvrge_bohol_second, $total_p_cvrge_cebu_second, $total_p_cvrge_negros_second, $total_p_cvrge_siquijor_second,
           $total_p_cvrge_cebu_facility_second, $total_p_cvrge_mandaue_facility_second, $total_p_cvrge_lapu_facility_second;  //TOTAL_PERCENT_COVERAGE_SECOND
    public $total_p_cvrge_sinovac_region_first, $total_p_cvrge_astra_region_first, $total_p_cvrge_sputnikv_region_first,
           $total_p_cvrge_pfizer_region_first, $total_p_cvrge_region_first; //TOTAL_REGION_COVERAGE_FIRST
    public $total_p_cvrge_sinovac_region_second, $total_p_cvrge_astra_region_second, $total_p_cvrge_sputnikv_region_second,
           $total_p_cvrge_pfizer_region_second, $total_p_cvrge_region_second; //TOTAL_REGION_COVERAGE_SECOND
    public $wastage_sinovac_bohol_first, $wastage_sinovac_cebu_first, $wastage_sinovac_negros_first, $wastage_sinovac_siquijor_first,
           $wastage_sinovac_cebu_facility_first, $wastage_sinovac_mandaue_facility_first, $wastage_sinovac_lapu_facility_first;  //WASTAGE_SINOVAC
    public $wastage_astra_bohol_first, $wastage_astra_cebu_first, $wastage_astra_negros_first, $wastage_astra_siquijor_first,
           $wastage_astra_cebu_facility_first, $wastage_astra_mandaue_facility_first, $wastage_astra_lapu_facility_first;  //WASTAGE_ASTRA
    public $wastage_sputnikv_bohol_first, $wastage_sputnikv_cebu_first, $wastage_sputnikv_negros_first, $wastage_sputnikv_siquijor_first,
           $wastage_sputnikv_cebu_facility_first, $wastage_sputnikv_mandaue_facility_first, $wastage_sputnikv_lapu_facility_first;  //WASTAGE_SPUTNIKV
    public $wastage_pfizer_bohol_first, $wastage_pfizer_cebu_first, $wastage_pfizer_negros_first, $wastage_pfizer_siquijor_first,
           $wastage_pfizer_cebu_facility_first, $wastage_pfizer_mandaue_facility_first, $wastage_pfizer_lapu_facility_first;  //WASTAGE_PFIZER
    public $total_wastage_bohol, $total_wastage_cebu, $total_wastage_negros, $total_wastage_siquijor,
           $total_wastage_cebu_facility, $total_wastage_mandaue_facility, $total_wastage_lapu_facility; //TOTAL_WASTAGE
    public $wastage_sinovac_first, $wastage_astra_first, $wastage_sputnikv_first, $wastage_pfizer_first, $wastage_region;  //TOTAL_WASTAGE2
    public $refused_first_bohol, $refused_first_cebu, $refused_first_negros, $refused_first_siquijor,
           $refused_cebu_facility_first, $refused_mandaue_facility_first, $refused_lapu_facility_first;  //REFUSED_FIRST
    public $refused_second_bohol, $refused_second_cebu, $refused_second_negros, $refused_second_siquijor,
           $refused_cebu_facility_second, $refused_mandaue_facility_second, $refused_lapu_facility_second;  //REFUSED_SECOND
    public $total_refusal_first, $total_refusal_second;  //TOTAL REFUSAL
    public $deferred_first_bohol, $deferred_first_cebu, $deferred_first_negros, $deferred_first_siquijor,
           $deferred_cebu_facility_first, $deferred_mandaue_facility_first, $deferred_lapu_facility_first; //DEFERRED_FIRST
    public $deferred_second_bohol, $deferred_second_cebu, $deferred_second_negros, $deferred_second_siquijor,
           $deferred_cebu_facility_second, $deferred_mandaue_facility_second, $deferred_lapu_facility_second; //DEFERRED_SECOND
    public $total_deferred_first, $total_deferred_second; //TOTAL DEFERRED
    public $c_rate_sinovac_bohol_first, $c_rate_sinovac_cebu_first, $c_rate_sinovac_negros_first, $c_rate_sinovac_siquijor_first,
           $c_rate_sinovac_cebu_facility_first, $c_rate_sinovac_mandaue_facility_first, $c_rate_sinovac_lapu_facility_first; //CONSUMPTION_RATE_SINOVAC_FIRST
    public $c_rate_astra_bohol_first, $c_rate_astra_cebu_first, $c_rate_astra_negros_first, $c_rate_astra_siquijor_first,
           $c_rate_astra_cebu_facility_first, $c_rate_astra_mandaue_facility_first, $c_rate_astra_lapu_facility_first; //CONSUMPTION_RATE_ASTRA_FIRST
    public $c_rate_sputnikv_bohol_first, $c_rate_sputnikv_cebu_first, $c_rate_sputnikv_negros_first, $c_rate_sputnikv_siquijor_first,
           $c_rate_sputnikv_cebu_facility_first, $c_rate_sputnikv_mandaue_facility_first, $c_rate_sputnikv_lapu_facility_first; //CONSUMPTION_RATE_SPUTNIKV_FIRST
    public $c_rate_pfizer_bohol_first, $c_rate_pfizer_cebu_first, $c_rate_pfizer_negros_first, $c_rate_pfizer_siquijor_first,
           $c_rate_pfizer_cebu_facility_first, $c_rate_pfizer_mandaue_facility_first, $c_rate_pfizer_lapu_facility_first; //CONSUMPTION_RATE_PFIZER_FIRST
    public $total_c_rate_bohol_first, $total_c_rate_cebu_first, $total_c_rate_negros_first, $total_c_rate_siquijor_first,
           $total_c_rate_cebu_facility_first, $total_c_rate_mandaue_facility_first, $total_c_rate_lapu_facility_first; //TOTAL_CONSUMPTION_RATE_FIRST
    public $c_rate_sinovac_bohol_second, $c_rate_sinovac_cebu_second, $c_rate_sinovac_negros_second, $c_rate_sinovac_siquijor_second,
           $c_rate_sinovac_cebu_facility_second, $c_rate_sinovac_mandaue_facility_second, $c_rate_sinovac_lapu_facility_second; //CONSUMPTION_RATE_SINOVAC_SECOND
    public $c_rate_astra_bohol_second, $c_rate_astra_cebu_second, $c_rate_astra_negros_second, $c_rate_astra_siquijor_second,
           $c_rate_astra_cebu_facility_second, $c_rate_astra_mandaue_facility_second, $c_rate_astra_lapu_facility_second; //CONSUMPTION_RATE_ASTRA_SECOND
    public $c_rate_sputnikv_bohol_second, $c_rate_sputnikv_cebu_second, $c_rate_sputnikv_negros_second, $c_rate_sputnikv_siquijor_second,
           $c_rate_sputnikv_cebu_facility_second, $c_rate_sputnikv_mandaue_facility_second, $c_rate_sputnikv_lapu_facility_second; //CONSUMPTION_RATE_SPUTNIKV_SECOND
    public $c_rate_pfizer_bohol_second, $c_rate_pfizer_cebu_second, $c_rate_pfizer_negros_second, $c_rate_pfizer_siquijor_second,
           $c_rate_pfizer_cebu_facility_second, $c_rate_pfizer_mandaue_facility_second, $c_rate_pfizer_lapu_facility_second; //CONSUMPTION_RATE_FPIZER_SECOND
    public $total_c_rate_bohol_second, $total_c_rate_cebu_second, $total_c_rate_negros_second, $total_c_rate_siquijor_second,
           $total_c_rate_cebu_facility_second, $total_c_rate_mandaue_facility_second, $total_c_rate_lapu_facility_second; //TOTAL_CONSUMPTION_RATE_SECOND
    public $c_rate_region_sinovac_first, $c_rate_region_astra_first, $c_rate_region_sputnikv_first, $c_rate_region_pfizer_first; //TOTAL_CONSUMPTION_RATE_REGION_FIRST
    public $c_rate_region_sinovac_second, $c_rate_region_astra_second, $c_rate_region_sputnikv_second, $c_rate_region_pfizer_second; //TOTAL_CONSUMPTION_RATE_REGION_SECOND
    public $total_c_rate_region_first; //TOTAL_CONSUMPTION_RATE_FIRST
    public $total_c_rate_region_second; //TOTAL_CONSUMPTION_RATE_SECOND
    public $priority_set;
    //for grand variable

    public $data_facility;  //DATA FACILITY
    public $a1_target_facility, $a2_target_facility, $a3_target_facility, $a4_target_facility; //TARGET FACILITY
    public $a1_completion_facility, $a2_completion_facility, $a3_completion_facility, $a4_completion_facility; //COMPLETION FACILITY
    public $a1_vaccinated_facility ,  $a2_vaccinated_facility, $a3_vaccinated_facility, $a4_vaccinated_facility; //VACCINATED FACILITY


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

        $a1_target_facility = Muncity::select(DB::raw("sum(coalesce(a1,0)) as a1_target_facility"))->first()->a1_target_facility;
        $a2_target_facility = Muncity::select(DB::raw("sum(coalesce(a2,0)) as a2_target_facility"))->first()->a2_target_facility;
        $a3_target_facility = Muncity::select(DB::raw("sum(coalesce(a3,0)) as a3_target_facility"))->first()->a3_target_facility;
        $a4_target_facility = Muncity::select(DB::raw("sum(coalesce(a4,0)) as a4_target_facility"))->first()->a4_target_facility;

        $a1_target = $a1_target + $a1_target_facility;
        $a2_target = $a2_target + $a2_target_facility;
        $a3_target = $a3_target + $a3_target_facility;
        $a4_target = $a4_target + $a4_target_facility;

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
        $elipop_facility = Facility::select(DB::raw("sum(COALESCE(a1,0)+COALESCE(a2,0)+COALESCE(a3,0)+COALESCE(a4,0)) as elipop_facility"))->first()->elipop_facility;

        $total_eli_pop = $elipop_muncity + $elipop_facility;

        $percent_coverage_first = number_format($total_vaccinated_first / $total_eli_pop * 100,2);
        $percent_coverage_second = number_format($total_vaccinated_second / $total_eli_pop * 100,2);

        $total_vaccine_allocated_first = Muncity::select(DB::raw("sum(COALESCE(sinovac_allocated_first,0)+COALESCE(astrazeneca_allocated_first,0)) as total_vaccine_allocated_first"))->first()->total_vaccine_allocated_first;
        $total_vaccine_allocated_second = Muncity::select(DB::raw("sum(COALESCE(sinovac_allocated_second,0)+COALESCE(astrazeneca_allocated_second,0)) as total_vaccine_allocated_second"))->first()->total_vaccine_allocated_second;

        $total_vaccine_allocated_first_facility = Facility::select(DB::raw("sum(COALESCE(sinovac_allocated_first,0)+COALESCE(astrazeneca_allocated_first,0)) as total_vaccine_allocated_first_facility"))->first()->total_vaccine_allocated_first_facility;
        $total_vaccine_allocated_second_facility = Facility::select(DB::raw("sum(COALESCE(sinovac_allocated_second,0)+COALESCE(astrazeneca_allocated_second,0)) as total_vaccine_allocated_second_facility"))->first()->total_vaccine_allocated_second_facility;

        $total_overall_vaccine_allocated_first = $total_vaccine_allocated_first + $total_vaccine_allocated_first_facility;
        $total_overall_vaccine_allocated_second = $total_vaccine_allocated_second + $total_vaccine_allocated_second_facility;


        $consumption_rate_first =  number_format($total_vaccinated_first / $total_overall_vaccine_allocated_first * 100,2);
        $consumption_rate_second =  number_format($total_vaccinated_second / $total_overall_vaccine_allocated_second * 100,2);


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
        $a1_target = Muncity::select(DB::raw("sum(coalesce(a1,0)) as a1_target"))->where("province_id",$province_id)->first()->a1_target;
        $a2_target = Muncity::select(DB::raw("sum(coalesce(a2,0)) as a2_target"))->where("province_id",$province_id)->first()->a2_target;
        $a3_target = Muncity::select(DB::raw("sum(coalesce(a3,0)) as a3_target"))->where("province_id",$province_id)->first()->a3_target;
        $a4_target = Muncity::select(DB::raw("sum(coalesce(a4,0)) as a4_target"))->where("province_id",$province_id)->first()->a4_target;

        $a1_completion = VaccineAccomplished::select(DB::raw("sum(coalesce(vaccinated_second,0)) as a1_completion"))->where("province_id",$province_id)->where("priority","a1")->first()->a1_completion;
        $a2_completion = VaccineAccomplished::select(DB::raw("sum(coalesce(vaccinated_second,0)) as a2_completion"))->where("province_id",$province_id)->where("priority","a2")->first()->a2_completion;
        $a3_completion = VaccineAccomplished::select(DB::raw("sum(coalesce(vaccinated_second,0)) as a3_completion"))->where("province_id",$province_id)->where("priority","a3")->first()->a3_completion;
        $a4_completion = VaccineAccomplished::select(DB::raw("sum(coalesce(vaccinated_second,0)) as a4_completion"))->where("province_id",$province_id)->where("priority","a4")->first()->a4_completion;
        $a1_completion = number_format($a1_completion / $a1_target * 100,2);
        $a2_completion = number_format($a2_completion / $a2_target * 100,2);
        $a3_completion = number_format($a3_completion / $a3_target * 100,2);
        $a4_completion = number_format($a4_completion / $a4_target * 100,2);


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
            'date_end' => $date_end,
            'a1_target' => $a1_target,
            'a2_target' => $a2_target,
            'a3_target' => $a3_target,
            'a4_target' => $a4_target,
            'a1_completion' => $a1_completion,
            'a2_completion' => $a2_completion,
            'a3_completion' => $a3_completion,
            'a4_completion' => $a4_completion,
        ]);
    }

    public function TargetCompletionFacility($muncity_id){
        $this->data_facility = Facility::where("province",2)
            ->where("vaccine_used","yes")
            ->where("tricity_id",63);
        $this->a1_target_facility = Facility::select(DB::raw("sum(coalesce(a1,0)) as a1_target_facility"))->where("tricity_id",63)->first()->a1_target_facility;
        $this->a2_target_facility = Facility::select(DB::raw("sum(coalesce(a2,0)) as a2_target_facility"))->where("tricity_id",63)->first()->a2_target_facility;
        $this->a3_target_facility = Facility::select(DB::raw("sum(coalesce(a3,0)) as a3_target_facility"))->where("tricity_id",63)->first()->a3_target_facility;
        $this->a4_target_facility = Facility::select(DB::raw("sum(coalesce(a4,0)) as a4_target_facility"))->where("tricity_id",63)->first()->a4_target_facility;


        $this->a1_vaccinated_facility = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as a1_vaccinated_facility"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.priority","a1")
            ->first()
            ->a1_vaccinated_facility;
        $this->a2_vaccinated_facility = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as a2_vaccinated_facility"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.priority","a2")
            ->first()
            ->a2_vaccinated_facility;
        $this->a3_vaccinated_facility = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as a3_vaccinated_facility"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.priority","a3")
            ->first()
            ->a3_vaccinated_facility;

        $this->a4_vaccinated_facility = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as a4_vaccinated_facility"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.priority","a3")
            ->first()
            ->a4_vaccinated_facility;

        $this->a1_completion_facility = number_format($this->a1_vaccinated_facility / $this->a1_target_facility * 100,2);
        $this->a2_completion_facility = number_format($this->a2_vaccinated_facility / $this->a2_target_facility * 100,2);
        $this->a3_completion_facility = number_format($this->a3_vaccinated_facility / $this->a3_target_facility * 100,2);
        $this->a4_completion_facility = number_format($this->a4_vaccinated_facility / $this->a4_target_facility * 100,2);
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
            $this->TargetCompletionFacility("63");
        }
        elseif($tri_city == 'mandaue'){
            $this->TargetCompletionFacility("80");
        }
        elseif($tri_city == 'lapu'){
            $this->TargetCompletionFacility("76");
        }

        $facility = $this->data_facility->orderBy("name","asc")
            ->get();

        if($request->muncity_filter)
            $this->data_facility= $this->data_facility->where("id",$request->muncity_filter);


        $this->data_facility = $this->data_facility
            ->orderBy("name","asc")
            ->paginate(10);


        return view('vaccine.vaccine_facility',[
            'title' => 'List of Facility',
            'province_name' => "Cebu",
            'province_id' => 2,
            'data' => $this->data_facility,
            'date_start' => $date_start,
            'date_end' => $date_end,
            'tri_city' => $tri_city,
            "facility" => $facility,
            'muncity_filter' => $request->muncity_filter,
            "typeof_vaccine_filter" => $request->typeof_vaccine_filter,
            'a1_target_facility'  =>  $this->a1_target_facility,
            'a2_target_facility'  =>  $this->a2_target_facility,
            'a3_target_facility'  =>  $this->a3_target_facility,
            'a4_target_facility'  =>  $this->a4_target_facility,
            'a1_vaccinated_facility'  =>  $this->a1_vaccinated_facility,
            'a2_vaccinated_facility'  =>  $this->a2_vaccinated_facility,
            'a3_vaccinated_facility'  =>  $this->a3_vaccinated_facility,
            'a4_vaccinated_facility'  =>  $this->a4_vaccinated_facility,
            'a1_completion_facility'  =>  $this->a1_completion_facility,
            'a2_completion_facility'  =>  $this->a2_completion_facility,
            'a3_completion_facility'  =>  $this->a3_completion_facility,
            'a4_completion_facility'  =>  $this->a4_completion_facility
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

    public function tabValueDeclaration(){
        return [
            "sinovac_bohol" => $this->sinovac_bohol,
            "sinovac_cebu" => $this->sinovac_cebu,
            "sinovac_negros" => $this->sinovac_negros,
            "sinovac_siquijor" => $this->sinovac_siquijor,
            "sinovac_cebu_facility" => $this->sinovac_cebu_facility,
            "sinovac_mandaue_facility" => $this->sinovac_mandaue_facility,
            "sinovac_lapu_facility" => $this->sinovac_lapu_facility,
            "sinovac_region" => $this->sinovac_bohol + $this->sinovac_cebu + $this->sinovac_negros + $this->sinovac_siquijor + $this->sinovac_cebu_facility + $this->sinovac_mandaue_facility + $this->sinovac_lapu_facility,

            "astra_bohol" => $this->astra_bohol,
            "astra_cebu" => $this->astra_cebu,
            "astra_negros" => $this->astra_negros,
            "astra_siquijor" => $this->astra_siquijor,
            "astra_cebu_facility" => $this->astra_cebu_facility,
            "astra_mandaue_facility" => $this->astra_mandaue_facility,
            "astra_lapu_facility" => $this->astra_lapu_facility,
            "astra_region" => $this->astra_bohol + $this->astra_cebu + $this->astra_negros + $this->astra_siquijor + $this->astra_cebu_facility + $this->astra_mandaue_facility + $this->astra_lapu_facility,

            "sputnikv_bohol" => $this->sputnikv_bohol,
            "sputnikv_cebu" => $this->sputnikv_cebu,
            "sputnikv_negros" => $this->sputnikv_negros,
            "sputnikv_siquijor" => $this->sputnikv_siquijor,
            "sputnikv_cebu_facility" => $this->sputnikv_cebu_facility,
            "sputnikv_mandaue_facility" => $this->sputnikv_mandaue_facility,
            "sputnikv_lapu_facility" => $this->sputnikv_lapu_facility,
            "sputnikv_region" => $this->sputnikv_bohol + $this->sputnikv_cebu + $this->sputnikv_negros + $this->sputnikv_siquijor + $this->sputnikv_cebu_facility + $this->sputnikv_mandaue_facility + $this->sputnikv_lapu_facility,


            "pfizer_bohol" => $this->pfizer_bohol,
            "pfizer_cebu" => $this->pfizer_cebu,
            "pfizer_negros" => $this->pfizer_negros,
            "pfizer_siquijor" => $this->pfizer_siquijor,
            "pfizer_cebu_facility" => $this->pfizer_cebu_facility,
            "pfizer_mandaue_facility" => $this->pfizer_mandaue_facility,
            "pfizer_lapu_facility" => $this->pfizer_lapu_facility,
            "pfizer_region" => $this->pfizer_bohol + $this->pfizer_cebu + $this->pfizer_negros + $this->pfizer_siquijor + $this->pfizer_cebu_facility + $this->pfizer_mandaue_facility + $this->pfizer_lapu_facility,

            "total_region" => $this->total_region,
            "total_bohol" => $this->total_bohol,
            "total_cebu" => $this->total_cebu,
            "total_negros" => $this->total_negros,
            "total_siquijor" => $this->total_siquijor,
            "total_cebu_facility" => $this->total_cebu_facility,
            "total_mandaue_facility" => $this->total_mandaue_facility,
            "total_lapu_facility" => $this->total_lapu_facility,

            //ELIGIBLE POP
            "eli_pop_bohol" => $this->eli_pop_bohol,
            "eli_pop_cebu" => $this->eli_pop_cebu,
            "eli_pop_negros" => $this->eli_pop_negros,
            "eli_pop_siquijor" => $this->eli_pop_siquijor,
            "eli_pop_cebu_facility"=> $this->eli_pop_cebu_facility,
            "eli_pop_mandaue_facility"=> $this->eli_pop_mandaue_facility,
            "eli_pop_lapu_facility"=> $this->eli_pop_lapu_facility,

            //VACCINATED_SINOVAC_FIRST
            "vcted_sinovac_bohol_first" => $this->vcted_sinovac_bohol_first,
            "vcted_sinovac_cebu_first" => $this->vcted_sinovac_cebu_first,
            "vcted_sinovac_negros_first" => $this->vcted_sinovac_negros_first,
            "vcted_sinovac_siquijor_first" => $this->vcted_sinovac_siquijor_first,
            "vcted_sinovac_cebu_facility_first" => $this->vcted_sinovac_cebu_facility_first,
            "vcted_sinovac_mandaue_facility_first" => $this->vcted_sinovac_mandaue_facility_first,
            "vcted_sinovac_lapu_facility_first" => $this->vcted_sinovac_lapu_facility_first,

            //VACCINATED_SINOVAC_SECOND
            "vcted_sinovac_bohol_second" => $this->vcted_sinovac_bohol_second,
            "vcted_sinovac_cebu_second" => $this->vcted_sinovac_cebu_second,
            "vcted_sinovac_negros_second" => $this->vcted_sinovac_negros_second,
            "vcted_sinovac_siquijor_second" => $this->vcted_sinovac_siquijor_second,
            "vcted_sinovac_cebu_facility_second" => $this->vcted_sinovac_cebu_facility_second,
            "vcted_sinovac_mandaue_facility_second" => $this->vcted_sinovac_mandaue_facility_second,
            "vcted_sinovac_lapu_facility_second" => $this->vcted_sinovac_lapu_facility_second,

            //VACCINATED_ASTRAZENECA_FIRST
            "vcted_astra_bohol_first" => $this->vcted_astra_bohol_first,
            "vcted_astra_cebu_first" => $this->vcted_astra_cebu_first,
            "vcted_astra_negros_first" => $this->vcted_astra_negros_first,
            "vcted_astra_siquijor_first" => $this->vcted_astra_siquijor_first,
            "vcted_astra_cebu_facility_first" => $this->vcted_astra_cebu_facility_first,
            "vcted_astra_mandaue_facility_first" => $this->vcted_astra_mandaue_facility_first,
            "vcted_astra_lapu_facility_first" => $this->vcted_astra_lapu_facility_first,

            //VACCINATED_ASTRAZENECA_SECOND
            "vcted_astra_bohol_second" => $this->vcted_astra_bohol_second,
            "vcted_astra_cebu_second" => $this->vcted_astra_cebu_second,
            "vcted_astra_negros_second" => $this->vcted_astra_negros_second,
            "vcted_astra_siquijor_second" => $this->vcted_astra_siquijor_second,
            "vcted_astra_cebu_facility_second" => $this->vcted_astra_cebu_facility_second,
            "vcted_astra_mandaue_facility_second" => $this->vcted_astra_mandaue_facility_second,
            "vcted_astra_lapu_facility_second" => $this->vcted_astra_lapu_facility_second,


            //VACCINATED_SPUTNIKV_FIRST
            "vcted_sputnikv_bohol_first" => $this->vcted_sputnikv_bohol_first,
            "vcted_sputnikv_cebu_first" => $this->vcted_sputnikv_cebu_first,
            "vcted_sputnikv_negros_first" => $this->vcted_sputnikv_negros_first,
            "vcted_sputnikv_siquijor_first" => $this->vcted_sputnikv_siquijor_first,
            "vcted_sputnikv_cebu_facility_first" => $this->vcted_sputnikv_cebu_facility_first,
            "vcted_sputnikv_mandaue_facility_first" => $this->vcted_sputnikv_mandaue_facility_first,
            "vcted_sputnikv_lapu_facility_first" => $this->vcted_sputnikv_lapu_facility_first,


            //VACCINATED_SPUTNIKV_SECOND
            "vcted_sputnikv_bohol_second" => $this->vcted_sputnikv_bohol_second,
            "vcted_sputnikv_cebu_second" => $this->vcted_sputnikv_cebu_second,
            "vcted_sputnikv_negros_second" => $this->vcted_sputnikv_negros_second,
            "vcted_sputnikv_siquijor_second" => $this->vcted_sputnikv_siquijor_second,
            "vcted_sputnikv_cebu_facility_second" => $this->vcted_sputnikv_cebu_facility_second,
            "vcted_sputnikv_mandaue_facility_second" => $this->vcted_sputnikv_mandaue_facility_second,
            "vcted_sputnikv_lapu_facility_second" => $this->vcted_sputnikv_lapu_facility_second,


            //VACCINATED_PFIZER_FIRST
            "vcted_pfizer_bohol_first" => $this->vcted_pfizer_bohol_first,
            "vcted_pfizer_cebu_first" => $this->vcted_pfizer_cebu_first,
            "vcted_pfizer_negros_first" => $this->vcted_pfizer_negros_first,
            "vcted_pfizer_siquijor_first" => $this->vcted_pfizer_siquijor_first,
            "vcted_pfizer_cebu_facility_first" => $this->vcted_pfizer_cebu_facility_first,
            "vcted_pfizer_mandaue_facility_first" => $this->vcted_pfizer_mandaue_facility_first,
            "vcted_pfizer_lapu_facility_first" => $this->vcted_pfizer_lapu_facility_first,


            //VACCINATED_PFIZER_SECOND
            "vcted_pfizer_bohol_second" => $this->vcted_pfizer_bohol_second,
            "vcted_pfizer_cebu_second" => $this->vcted_pfizer_cebu_second,
            "vcted_pfizer_negros_second" => $this->vcted_pfizer_negros_second,
            "vcted_pfizer_siquijor_second" => $this->vcted_pfizer_siquijor_second,
            "vcted_pfizer_cebu_facility_second" => $this->vcted_pfizer_cebu_facility_second,
            "vcted_pfizer_mandaue_facility_second" => $this->vcted_pfizer_mandaue_facility_second,
            "vcted_pfizer_lapu_facility_second" => $this->vcted_pfizer_lapu_facility_second,


            //TOTAL VACCINATED_FIRST
            "total_vcted_first_bohol" => $this->total_vcted_first_bohol,
            "total_vcted_first_cebu" => $this->total_vcted_first_cebu,
            "total_vcted_first_negros" => $this->total_vcted_first_negros,
            "total_vcted_first_siquijor" => $this->total_vcted_first_siquijor,
            "total_vcted_cebu_facility_first" => $this->total_vcted_cebu_facility_first,
            "total_vcted_mandaue_facility_first" => $this->total_vcted_mandaue_facility_first,
            "total_vcted_lapu_facility_first" => $this->total_vcted_lapu_facility_first,

            //TOTAL VACCINATED SECOND
            "total_vcted_second_bohol" => $this->total_vcted_second_bohol,
            "total_vcted_second_cebu" => $this->total_vcted_second_cebu,
            "total_vcted_second_negros" => $this->total_vcted_second_negros,
            "total_vcted_second_siquijor" => $this->total_vcted_second_siquijor,
            "total_vcted_cebu_facility_second" => $this->total_vcted_cebu_facility_second,
            "total_vcted_mandaue_facility_second" => $this->total_vcted_mandaue_facility_second,
            "total_vcted_lapu_facility_second" => $this->total_vcted_lapu_facility_second,

            //REGION VACCINATED
            "region_sinovac_first_dose" => $this->region_sinovac_first_dose,
            "region_sinovac_second_dose" => $this->region_sinovac_second_dose,
            "region_astra_first_dose" => $this->region_astra_first_dose,
            "region_astra_second_dose" => $this->region_astra_second_dose,
            "region_sputnikv_first_dose" => $this->region_sputnikv_first_dose,
            "region_sputnikv_second_dose" => $this->region_sputnikv_second_dose,
            "region_pfizer_first_dose" => $this->region_pfizer_first_dose,
            "region_pfizer_second_dose" => $this->region_pfizer_second_dose,

            //TOTAL DOSE
            "first_dose_total" => $this->first_dose_total,
            "second_dose_total" => $this->second_dose_total,

            //TOTAL ELI POP
            "total_elipop_region" => $this->total_elipop_region,

            //PERCENT COVERAGE SINOVAC FIRST DOSE
            "p_cvrge_sinovac_bohol_first" => $this->p_cvrge_sinovac_bohol_first,
            "p_cvrge_sinovac_cebu_first" => $this->p_cvrge_sinovac_cebu_first,
            "p_cvrge_sinovac_negros_first" => $this->p_cvrge_sinovac_negros_first,
            "p_cvrge_sinovac_siquijor_first" => $this->p_cvrge_sinovac_siquijor_first,
            "p_cvrge_sinovac_cebu_facility_first" => $this->p_cvrge_sinovac_cebu_facility_first,
            "p_cvrge_sinovac_mandaue_facility_first" => $this->p_cvrge_sinovac_mandaue_facility_first,
            "p_cvrge_sinovac_lapu_facility_first" => $this->p_cvrge_sinovac_lapu_facility_first,

            //PERCENT COVERAGE ASTRA FIRST DOSE
            "p_cvrge_astra_bohol_first" => $this->p_cvrge_astra_bohol_first,
            "p_cvrge_astra_cebu_first" => $this->p_cvrge_astra_cebu_first,
            "p_cvrge_astra_negros_first" => $this->p_cvrge_astra_negros_first,
            "p_cvrge_astra_siquijor_first" => $this->p_cvrge_astra_siquijor_first,
            "p_cvrge_astra_cebu_facility_first" => $this->p_cvrge_astra_cebu_facility_first,
            "p_cvrge_astra_mandaue_facility_first" => $this->p_cvrge_astra_mandaue_facility_first,
            "p_cvrge_astra_lapu_facility_first" => $this->p_cvrge_astra_lapu_facility_first,

            //PERCENT COVERAGE SPUTNIKV FIRST DOSE
            "p_cvrge_sputnikv_bohol_first" => $this->p_cvrge_sputnikv_bohol_first,
            "p_cvrge_sputnikv_cebu_first" => $this->p_cvrge_sputnikv_cebu_first,
            "p_cvrge_sputnikv_negros_first" => $this->p_cvrge_sputnikv_negros_first,
            "p_cvrge_sputnikv_siquijor_first" => $this->p_cvrge_sputnikv_siquijor_first,
            "p_cvrge_sputnikv_cebu_facility_first" => $this->p_cvrge_sputnikv_cebu_facility_first,
            "p_cvrge_sputnikv_mandaue_facility_first" => $this->p_cvrge_sputnikv_mandaue_facility_first,
            "p_cvrge_sputnikv_lapu_facility_first" => $this->p_cvrge_sputnikv_lapu_facility_first,

            //PERCENT COVERAGE PFIZER FIRST DOSE
            "p_cvrge_pfizer_bohol_first" => $this->p_cvrge_pfizer_bohol_first,
            "p_cvrge_pfizer_cebu_first" => $this->p_cvrge_pfizer_cebu_first,
            "p_cvrge_pfizer_negros_first" => $this->p_cvrge_pfizer_negros_first,
            "p_cvrge_pfizer_siquijor_first" => $this->p_cvrge_pfizer_siquijor_first,
            "p_cvrge_pfizer_cebu_facility_first" => $this->p_cvrge_pfizer_cebu_facility_first,
            "p_cvrge_pfizer_mandaue_facility_first" => $this->p_cvrge_pfizer_mandaue_facility_first,
            "p_cvrge_pfizer_lapu_facility_first" => $this->p_cvrge_pfizer_lapu_facility_first,

            //TOTAL_PERCENT_COVERAGE_FIRST
            "total_p_cvrge_bohol_first" => $this->total_p_cvrge_bohol_first,
            "total_p_cvrge_cebu_first" => $this->total_p_cvrge_cebu_first,
            "total_p_cvrge_negros_first" => $this->total_p_cvrge_negros_first,
            "total_p_cvrge_siquijor_first" => $this->total_p_cvrge_siquijor_first,
            "total_p_cvrge_cebu_facility_first" => $this->total_p_cvrge_cebu_facility_first,
            "total_p_cvrge_mandaue_facility_first" => $this->total_p_cvrge_mandaue_facility_first,
            "total_p_cvrge_lapu_facility_first" => $this->total_p_cvrge_lapu_facility_first,

            //PERCENT COVERAGE SINOVAC SECOND DOSE
            "p_cvrge_sinovac_bohol_second" => $this->p_cvrge_sinovac_bohol_second,
            "p_cvrge_sinovac_cebu_second" => $this->p_cvrge_sinovac_cebu_second,
            "p_cvrge_sinovac_negros_second" => $this->p_cvrge_sinovac_negros_second,
            "p_cvrge_sinovac_siquijor_second" => $this->p_cvrge_sinovac_siquijor_second,
            "p_cvrge_sinovac_cebu_facility_second" => $this->p_cvrge_sinovac_cebu_facility_second,
            "p_cvrge_sinovac_mandaue_facility_second" => $this->p_cvrge_sinovac_mandaue_facility_second,
            "p_cvrge_sinovac_lapu_facility_second" => $this->p_cvrge_sinovac_lapu_facility_second,

            //PERCENT COVERAGE ASTRA SECOND DOSE
            "p_cvrge_astra_bohol_second" => $this->p_cvrge_astra_bohol_second,
            "p_cvrge_astra_cebu_second" => $this->p_cvrge_astra_cebu_second,
            "p_cvrge_astra_negros_second" => $this->p_cvrge_astra_negros_second,
            "p_cvrge_astra_siquijor_second" => $this->p_cvrge_astra_siquijor_second,
            "p_cvrge_astra_cebu_facility_second" => $this->p_cvrge_astra_cebu_facility_second,
            "p_cvrge_astra_mandaue_facility_second" => $this->p_cvrge_astra_mandaue_facility_second,
            "p_cvrge_astra_lapu_facility_second" => $this->p_cvrge_astra_lapu_facility_second,

            //PERCENT COVERAGE SPUTNIKV SECOND DOSE
            "p_cvrge_sputnikv_bohol_second" => $this->p_cvrge_sputnikv_bohol_second,
            "p_cvrge_sputnikv_cebu_second" => $this->p_cvrge_sputnikv_cebu_second,
            "p_cvrge_sputnikv_negros_second" => $this->p_cvrge_sputnikv_negros_second,
            "p_cvrge_sputnikv_siquijor_second" => $this->p_cvrge_sputnikv_siquijor_second,
            "p_cvrge_sputnikv_cebu_facility_second" => $this->p_cvrge_sputnikv_cebu_facility_second,
            "p_cvrge_sputnikv_mandaue_facility_second" => $this->p_cvrge_sputnikv_mandaue_facility_second,
            "p_cvrge_sputnikv_lapu_facility_second" => $this->p_cvrge_sputnikv_lapu_facility_second,

            //PERCENT COVERAGE PFIZER SECOND DOSE
            "p_cvrge_pfizer_bohol_second" => $this->p_cvrge_pfizer_bohol_second,
            "p_cvrge_pfizer_cebu_second" => $this->p_cvrge_pfizer_cebu_second,
            "p_cvrge_pfizer_negros_second" => $this->p_cvrge_pfizer_negros_second,
            "p_cvrge_pfizer_siquijor_second" => $this->p_cvrge_pfizer_siquijor_second,
            "p_cvrge_pfizer_cebu_facility_second" => $this->p_cvrge_pfizer_cebu_facility_second,
            "p_cvrge_pfizer_mandaue_facility_second" => $this->p_cvrge_pfizer_mandaue_facility_second,
            "p_cvrge_pfizer_lapu_facility_second" => $this->p_cvrge_pfizer_lapu_facility_second,

            //TOTAL_PERCENT_COVERAGE_SECOND
            "total_p_cvrge_bohol_second" => $this->total_p_cvrge_bohol_second,
            "total_p_cvrge_cebu_second" => $this->total_p_cvrge_cebu_second,
            "total_p_cvrge_negros_second" => $this->total_p_cvrge_negros_second,
            "total_p_cvrge_siquijor_second" => $this->total_p_cvrge_siquijor_second,
            "total_p_cvrge_cebu_facility_second" => $this->total_p_cvrge_cebu_facility_second,
            "total_p_cvrge_mandaue_facility_second" => $this->total_p_cvrge_mandaue_facility_second,
            "total_p_cvrge_lapu_facility_second" => $this->total_p_cvrge_lapu_facility_second,

            //TOTAL_REGION_COVERAGE_FIRST
            "total_p_cvrge_sinovac_region_first" => $this->total_p_cvrge_sinovac_region_first,
            "total_p_cvrge_astra_region_first" => $this->total_p_cvrge_astra_region_first,
            "total_p_cvrge_sputnikv_region_first" => $this->total_p_cvrge_sputnikv_region_first,
            "total_p_cvrge_pfizer_region_first" => $this->total_p_cvrge_pfizer_region_first,
            "total_p_cvrge_region_first" => $this->total_p_cvrge_region_first,

            //TOTAL_REGION_COVERAGE_SECOND
            "total_p_cvrge_sinovac_region_second" => $this->total_p_cvrge_sinovac_region_second,
            "total_p_cvrge_astra_region_second" => $this->total_p_cvrge_astra_region_second,
            "total_p_cvrge_sputnikv_region_second" => $this->total_p_cvrge_sputnikv_region_second,
            "total_p_cvrge_pfizer_region_second" => $this->total_p_cvrge_pfizer_region_second,
            "total_p_cvrge_region_second" => $this->total_p_cvrge_region_second,

            //WASTAGE_SINOVAC_FIRST
            "wastage_sinovac_bohol_first" => $this->wastage_sinovac_bohol_first,
            "wastage_sinovac_cebu_first" => $this->wastage_sinovac_cebu_first,
            "wastage_sinovac_negros_first" => $this->wastage_sinovac_negros_first,
            "wastage_sinovac_siquijor_first" => $this->wastage_sinovac_siquijor_first,
            "wastage_sinovac_cebu_facility_first" => $this->wastage_sinovac_cebu_facility_first,
            "wastage_sinovac_mandaue_facility_first" => $this->wastage_sinovac_mandaue_facility_first,
            "wastage_sinovac_lapu_facility_first" => $this->wastage_sinovac_lapu_facility_first,

            //WASTAGE_ASTRA_FIRST
            "wastage_astra_bohol_first" => $this->wastage_astra_bohol_first,
            "wastage_astra_cebu_first" => $this->wastage_astra_cebu_first,
            "wastage_astra_negros_first" => $this->wastage_astra_negros_first,
            "wastage_astra_siquijor_first" => $this->wastage_astra_siquijor_first,
            "wastage_astra_cebu_facility_first" => $this->wastage_astra_cebu_facility_first,
            "wastage_astra_mandaue_facility_first" => $this->wastage_astra_mandaue_facility_first,
            "wastage_astra_lapu_facility_first" => $this->wastage_astra_lapu_facility_first,

            //WASTAGE_SPUTNIKV_FIRST
            "wastage_sputnikv_bohol_first" => $this->wastage_sputnikv_bohol_first,
            "wastage_sputnikv_cebu_first" => $this->wastage_sputnikv_cebu_first,
            "wastage_sputnikv_negros_first" => $this->wastage_sputnikv_negros_first,
            "wastage_sputnikv_siquijor_first" => $this->wastage_sputnikv_siquijor_first,
            "wastage_sputnikv_cebu_facility_first" => $this->wastage_sputnikv_cebu_facility_first,
            "wastage_sputnikv_mandaue_facility_first" => $this->wastage_sputnikv_mandaue_facility_first,
            "wastage_sputnikv_lapu_facility_first" => $this->wastage_sputnikv_lapu_facility_first,

            //WASTAGE_PFIZER_FIRST
            "wastage_pfizer_bohol_first" => $this->wastage_pfizer_bohol_first,
            "wastage_pfizer_cebu_first" => $this->wastage_pfizer_cebu_first,
            "wastage_pfizer_negros_first" => $this->wastage_pfizer_negros_first,
            "wastage_pfizer_siquijor_first" => $this->wastage_pfizer_siquijor_first,
            "wastage_pfizer_cebu_facility_first" => $this->wastage_pfizer_cebu_facility_first,
            "wastage_pfizer_mandaue_facility_first" => $this->wastage_pfizer_mandaue_facility_first,
            "wastage_pfizer_lapu_facility_first" => $this->wastage_pfizer_lapu_facility_first,

            //REFUSED_FIRST
            "refused_first_bohol" => $this->refused_first_bohol,
            "refused_first_cebu" => $this->refused_first_cebu,
            "refused_first_negros" => $this->refused_first_negros,
            "refused_first_siquijor" => $this->refused_first_siquijor,
            "refused_cebu_facility_first" => $this->refused_cebu_facility_first,
            "refused_mandaue_facility_first" => $this->refused_mandaue_facility_first,
            "refused_lapu_facility_first" => $this->refused_lapu_facility_first,

            //REFUSED_SECOND
            "refused_second_bohol" => $this->refused_second_bohol,
            "refused_second_cebu" => $this->refused_second_cebu,
            "refused_second_negros" => $this->refused_second_negros,
            "refused_second_siquijor" => $this->refused_second_siquijor,
            "refused_cebu_facility_second" => $this->refused_cebu_facility_second,
            "refused_mandaue_facility_second" => $this->refused_mandaue_facility_second,
            "refused_lapu_facility_second" => $this->refused_lapu_facility_second,

            //DEFERRED_FIRST
            "deferred_first_bohol" => $this->deferred_first_bohol,
            "deferred_first_cebu" => $this->deferred_first_cebu,
            "deferred_first_negros" => $this->deferred_first_negros,
            "deferred_first_siquijor" => $this->deferred_first_siquijor,
            "deferred_cebu_facility_first" => $this->deferred_cebu_facility_first,
            "deferred_mandaue_facility_first" => $this->deferred_mandaue_facility_first,
            "deferred_lapu_facility_first" => $this->deferred_lapu_facility_first,

            //DEFERRED_SECOND
            "deferred_second_bohol" => $this->deferred_second_bohol,
            "deferred_second_cebu" => $this->deferred_second_cebu,
            "deferred_second_negros" => $this->deferred_second_negros,
            "deferred_second_siquijor" => $this->deferred_second_siquijor,
            "deferred_cebu_facility_second" => $this->deferred_cebu_facility_second,
            "deferred_mandaue_facility_second" => $this->deferred_mandaue_facility_second,
            "deferred_lapu_facility_second" => $this->deferred_lapu_facility_second,


            //TOTAL_WASTAGE
            "total_wastage_bohol" => $this->total_wastage_bohol,
            "total_wastage_cebu" => $this->total_wastage_cebu,
            "total_wastage_negros" => $this->total_wastage_negros,
            "total_wastage_siquijor" => $this->total_wastage_siquijor,
            "total_wastage_cebu_facility" => $this->total_wastage_cebu_facility,
            "total_wastage_mandaue_facility" => $this->total_wastage_mandaue_facility,
            "total_wastage_lapu_facility" => $this->total_wastage_lapu_facility,

            //TOTAL REFUSAL
            "total_refusal_first" =>$this->total_refusal_first, //REFUSED FIRST REGION
            "total_refusal_second" =>$this->total_refusal_second,

            //TOTAL DEFERRED
            "total_deferred_first" =>$this->total_deferred_first,
            "total_deferred_second" =>$this->total_deferred_second,

            //TOTAL WASTAGE
            "wastage_sinovac_first" => $this->wastage_sinovac_first,
            "wastage_astra_first" => $this->wastage_astra_first,
            "wastage_sputnikv_first" => $this->wastage_sputnikv_first,
            "wastage_pfizer_first" => $this->wastage_pfizer_first,
            "wastage_region" => $this->wastage_region,

            //CONSUMPTION_RATE_SINOVAC
            "c_rate_sinovac_bohol_first" => $this->c_rate_sinovac_bohol_first,
            "c_rate_sinovac_cebu_first" => $this->c_rate_sinovac_cebu_first,
            "c_rate_sinovac_negros_first" => $this->c_rate_sinovac_negros_first,
            "c_rate_sinovac_siquijor_first" => $this->c_rate_sinovac_siquijor_first,
            "c_rate_sinovac_cebu_facility_first" => $this->c_rate_sinovac_cebu_facility_first,
            "c_rate_sinovac_mandaue_facility_first" => $this->c_rate_sinovac_mandaue_facility_first,
            "c_rate_sinovac_lapu_facility_first" => $this->c_rate_sinovac_lapu_facility_first,

            //CONSUMPTION_RATE_ASTRA
            "c_rate_astra_bohol_first" => $this->c_rate_astra_bohol_first,
            "c_rate_astra_cebu_first" => $this->c_rate_astra_cebu_first,
            "c_rate_astra_negros_first" => $this->c_rate_astra_negros_first,
            "c_rate_astra_siquijor_first" => $this->c_rate_astra_siquijor_first,
            "c_rate_astra_cebu_facility_first" => $this->c_rate_astra_cebu_facility_first,
            "c_rate_astra_mandaue_facility_first" => $this->c_rate_astra_mandaue_facility_first,
            "c_rate_astra_lapu_facility_first" => $this->c_rate_astra_lapu_facility_first,

            //CONSUMPTION_RATE_SPUTNIKV
            "c_rate_sputnikv_bohol_first" => $this->c_rate_sputnikv_bohol_first,
            "c_rate_sputnikv_cebu_first" => $this->c_rate_sputnikv_cebu_first,
            "c_rate_sputnikv_negros_first" => $this->c_rate_sputnikv_negros_first,
            "c_rate_sputnikv_siquijor_first" => $this->c_rate_sputnikv_siquijor_first,
            "c_rate_sputnikv_cebu_facility_first" => $this->c_rate_sputnikv_cebu_facility_first,
            "c_rate_sputnikv_mandaue_facility_first" => $this->c_rate_sputnikv_mandaue_facility_first,
            "c_rate_sputnikv_lapu_facility_first" => $this->c_rate_sputnikv_lapu_facility_first,

            //CONSUMPTION_RATE_PFIZER
            "c_rate_pfizer_bohol_first" => $this->c_rate_pfizer_bohol_first,
            "c_rate_pfizer_cebu_first" => $this->c_rate_pfizer_cebu_first,
            "c_rate_pfizer_negros_first" => $this->c_rate_pfizer_negros_first,
            "c_rate_pfizer_siquijor_first" => $this->c_rate_pfizer_siquijor_first,
            "c_rate_pfizer_cebu_facility_first" => $this->c_rate_pfizer_cebu_facility_first,
            "c_rate_pfizer_mandaue_facility_first" => $this->c_rate_pfizer_mandaue_facility_first,
            "c_rate_pfizer_lapu_facility_first" => $this->c_rate_pfizer_mandaue_facility_first,

            //TOTAL_CONSUMPTION_RATE_FIRST
            "total_c_rate_bohol_first" => $this->total_c_rate_bohol_first,
            "total_c_rate_cebu_first" => $this->total_c_rate_cebu_first,
            "total_c_rate_negros_first" => $this->total_c_rate_negros_first,
            "total_c_rate_siquijor_first" => $this->total_c_rate_siquijor_first,
            "total_c_rate_cebu_facility_first" => $this->total_c_rate_cebu_facility_first,
            "total_c_rate_mandaue_facility_first" => $this->total_c_rate_mandaue_facility_first,
            "total_c_rate_lapu_facility_first" => $this->total_c_rate_lapu_facility_first,

            //CONSUMPTION_RATE_SINOVAC_SECOND
            "c_rate_sinovac_bohol_second" => $this->c_rate_sinovac_bohol_second,
            "c_rate_sinovac_cebu_second" => $this->c_rate_sinovac_cebu_second,
            "c_rate_sinovac_negros_second" => $this->c_rate_sinovac_negros_second,
            "c_rate_sinovac_siquijor_second" => $this->c_rate_sinovac_siquijor_second,
            "c_rate_sinovac_cebu_facility_second" => $this->c_rate_sinovac_cebu_facility_second,
            "c_rate_sinovac_mandaue_facility_second" => $this->c_rate_sinovac_mandaue_facility_second,
            "c_rate_sinovac_lapu_facility_second" => $this->c_rate_sinovac_lapu_facility_second,

            //CONSUMPTION_RATE_ASTRAZENECA_SECOND
            "c_rate_astra_bohol_second" => $this->c_rate_astra_bohol_second,
            "c_rate_astra_cebu_second" => $this->c_rate_astra_cebu_second,
            "c_rate_astra_negros_second" => $this->c_rate_astra_negros_second,
            "c_rate_astra_siquijor_second" => $this->c_rate_astra_siquijor_second,
            "c_rate_astra_cebu_facility_second" => $this->c_rate_astra_cebu_facility_second,
            "c_rate_astra_mandaue_facility_second" => $this->c_rate_astra_mandaue_facility_second,
            "c_rate_astra_lapu_facility_second" => $this->c_rate_astra_lapu_facility_second,

            //CONSUMPTION_RATE_SPUTNIKV_SECOND
            "c_rate_sputnikv_bohol_second" => $this->c_rate_sputnikv_bohol_second,
            "c_rate_sputnikv_cebu_second" => $this->c_rate_sputnikv_cebu_second,
            "c_rate_sputnikv_negros_second" => $this->c_rate_sputnikv_negros_second,
            "c_rate_sputnikv_siquijor_second" => $this->c_rate_sputnikv_siquijor_second,
            "c_rate_sputnikv_cebu_facility_second" => $this->c_rate_sputnikv_cebu_facility_second,
            "c_rate_sputnikv_mandaue_facility_second" => $this->c_rate_sputnikv_mandaue_facility_second,
            "c_rate_sputnikv_lapu_facility_second" => $this->c_rate_sputnikv_lapu_facility_second,

            //CONSUMPTION_RATE_PFIZER_SECOND
            "c_rate_pfizer_bohol_second" => $this->c_rate_pfizer_bohol_second,
            "c_rate_pfizer_cebu_second" => $this->c_rate_pfizer_cebu_second,
            "c_rate_pfizer_negros_second" => $this->c_rate_pfizer_negros_second,
            "c_rate_pfizer_siquijor_second" => $this->c_rate_pfizer_siquijor_second,
            "c_rate_pfizer_cebu_facility_second" => $this->c_rate_pfizer_cebu_facility_second,
            "c_rate_pfizer_mandaue_facility_second" => $this->c_rate_pfizer_mandaue_facility_second,
            "c_rate_pfizer_lapu_facility_second" => $this->c_rate_pfizer_lapu_facility_second,


            //TOTAL_CONSUMPTION_RATE_SECOND
            "total_c_rate_bohol_second" => $this->total_c_rate_bohol_second,
            "total_c_rate_cebu_second" => $this->total_c_rate_cebu_second,
            "total_c_rate_negros_second" => $this->total_c_rate_negros_second,
            "total_c_rate_siquijor_second" => $this->total_c_rate_siquijor_second,
            "total_c_rate_cebu_facility_second" => $this->total_c_rate_cebu_facility_second,
            "total_c_rate_mandaue_facility_second" => $this->total_c_rate_mandaue_facility_second,
            "total_c_rate_lapu_facility_second" => $this->total_c_rate_lapu_facility_second,

            //TOTAL_CONSUMPTION_RATE_REGION_FIRST
            "c_rate_region_sinovac_first" => $this->c_rate_region_sinovac_first,
            "c_rate_region_astra_first" => $this->c_rate_region_astra_first,
            "c_rate_region_sputnikv_first" => $this->c_rate_region_sputnikv_first,
            "c_rate_region_pfizer_first" => $this->c_rate_region_pfizer_first,

            //TOTAL_CONSUMPTION_RATE_REGION_SECOND
            "c_rate_region_sinovac_second" => $this->c_rate_region_sinovac_second,
            "c_rate_region_astra_second" => $this->c_rate_region_astra_second,
            "c_rate_region_sputnikv_second" => $this->c_rate_region_sputnikv_second,
            "c_rate_region_pfizer_second" => $this->c_rate_region_pfizer_second,

            //TOTAL_CONSUMPTION_RATE_FIRST
            "total_c_rate_region_first" => $this->total_c_rate_region_first,

            //TOTAL_CONSUMPTION_RATE_SECOND
            "total_c_rate_region_second" => $this->total_c_rate_region_second,
            //priority set
            "priority_set" => $this->priority_set
        ];
    }

    public function setPriority($priority){
        //allocated
        $this->sinovac_bohol = Muncity::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_bohol"))->where("province_id",1)->first()->sinovac_bohol; //VACCINE ALLOCATED SINOVAC BOHOL
        $this->sinovac_cebu = Muncity::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_cebu"))->where("province_id",2)->first()->sinovac_cebu; //VACCINE ALLOCATED SINOVAC CEBU
        $this->sinovac_negros = Muncity::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_negros"))->where("province_id",3)->first()->sinovac_negros; //VACCINE ALLOCATED SINOVAC NEGROS
        $this->sinovac_siquijor = Muncity::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_siquijor"))->where("province_id",4)->first()->sinovac_siquijor; //VACCINE ALLOCATED SINOVAC SIQUIJOR
        $this->sinovac_cebu_facility = Facility::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_cebu_facility"))->where("tricity_id",63)->first()->sinovac_cebu_facility; //VACCINE ALLOCATED SINOVAC CEBU FACILITY
        $this->sinovac_mandaue_facility = Facility::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_mandaue_facility"))->where("tricity_id",80)->first()->sinovac_mandaue_facility; //VACCINE ALLOCATED SINOVAC MANDAUE_FACILITY
        $this->sinovac_lapu_facility = Facility::select(DB::raw("SUM(coalesce(sinovac_allocated_first,0)+coalesce(sinovac_allocated_second,0)) as sinovac_lapu_facility"))->where("tricity_id",76)->first()->sinovac_lapu_facility; //VACCINE ALLOCATED SINOVAC LAPU-LAPU FACILITY

        $this->astra_bohol = Muncity::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_bohol"))->where("province_id",1)->first()->astra_bohol; //VACCINE ALLOCATED ASTRA BOHOL
        $this->astra_cebu = Muncity::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_cebu"))->where("province_id",2)->first()->astra_cebu; //VACCINE ALLOCATED ASTRA CEBU
        $this->astra_negros = Muncity::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_negros"))->where("province_id",3)->first()->astra_negros; //VACCINE ALLOCATED ASTRA NEGROS
        $this->astra_siquijor = Muncity::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_siquijor"))->where("province_id",4)->first()->astra_siquijor; //VACCINE ALLOCATED ASTRA SIQUIKOR
        $this->astra_cebu_facility = Facility::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_cebu_facility"))->where("tricity_id",63)->first()->astra_cebu_facility; //VACCINE ALLOCATED ASTRA CEBU FACILITY
        $this->astra_mandaue_facility = Facility::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_mandaue_facility"))->where("tricity_id",80)->first()->astra_mandaue_facility; //VACCINE ALLOCATED ASTRA MANDAUE FACILITY
        $this->astra_lapu_facility = Facility::select(DB::raw("SUM(coalesce(astrazeneca_allocated_first,0)+coalesce(astrazeneca_allocated_second,0)) as astra_lapu_facility"))->where("tricity_id",76)->first()->astra_lapu_facility; //VACCINE ALLOCATED ASTRA LAPU-LAPU FACILITY

        $this->sputnikv_bohol = Muncity::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_bohol"))->where("province_id",1)->first()->sputnikv_bohol; //VACCINE ALLOCATED SPUTNIKV BOHOL
        $this->sputnikv_cebu = Muncity::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_cebu"))->where("province_id",2)->first()->sputnikv_cebu; //VACCINE ALLOCATED SPUTNIKV CEBU
        $this->sputnikv_negros = Muncity::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_negros"))->where("province_id",3)->first()->sputnikv_negros; //VACCINE ALLOCATED SPUTNIKV NEGROS
        $this->sputnikv_siquijor = Muncity::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_siquijor"))->where("province_id",4)->first()->sputnikv_siquijor; //VACCINE ALLOCATED SPUTNIKV SIQUIJOR
        $this->sputnikv_cebu_facility = Facility::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_cebu_facility"))->where("tricity_id",63)->first()->sputnikv_cebu_facility; //VACCINE ALLOCATED SPUTNIKV CEBU FACILITY
        $this->sputnikv_mandaue_facility = Facility::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_mandaue_facility"))->where("tricity_id",80)->first()->sputnikv_mandaue_facility; //VACCINE ALLOCATED SPUTNIKV MANDAUAE FACILITY
        $this->sputnikv_lapu_facility = Facility::select(DB::raw("SUM(coalesce(sputnikv_allocated_first,0)+coalesce(sputnikv_allocated_second,0)) as sputnikv_lapu_facility"))->where("tricity_id",76)->first()->sputnikv_lapu_facility; //VACCINE ALLOCATED SPUTNIKV LAPU-LAPU FACILTY

        $this->pfizer_bohol = Muncity::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_bohol"))->where("province_id",1)->first()->pfizer_bohol; //VACCINE ALLOCATED PFIZER BOHOL
        $this->pfizer_cebu = Muncity::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_cebu"))->where("province_id",2)->first()->pfizer_cebu; //VACCINE ALLOCATED PFIZER CEBU
        $this->pfizer_negros = Muncity::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_negros"))->where("province_id",3)->first()->pfizer_negros; //VACCINE ALLOCATED PFIZER NEGROS
        $this->pfizer_siquijor = Muncity::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_siquijor"))->where("province_id",4)->first()->pfizer_siquijor; //VACCINE ALLOCATED PFIZER SIQUIJOR
        $this->pfizer_cebu_facility = Facility::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_cebu_facility"))->where("tricity_id",63)->first()->pfizer_cebu_facility; //VACCINE ALLOCATED PFIZER CEBU FACILITY
        $this->pfizer_mandaue_facility = Facility::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_mandaue_facility"))->where("tricity_id",80)->first()->pfizer_mandaue_facility; //VACCINE ALLOCATED PFIZER MANDAUE FACILITY
        $this->pfizer_lapu_facility = Facility::select(DB::raw("SUM(coalesce(pfizer_allocated_first,0)+coalesce(pfizer_allocated_second,0)) as pfizer_lapu_facility"))->where("tricity_id",76)->first()->pfizer_lapu_facility; //VACCINE ALLOCATED PFIZER LAPU-LAPU FACILITY

        $this->sinovac_region = $this->sinovac_bohol + $this->sinovac_cebu + $this->sinovac_negros + $this->sinovac_siquijor + $this->sinovac_cebu_facility + $this->sinovac_mandaue_facility + $this->sinovac_lapu_facility; //VACCINE ALLOCATED SINOVAC REGION
        $this->astra_region = $this->astra_bohol + $this->astra_cebu + $this->astra_negros + $this->astra_siquijor + $this->astra_cebu_facility + $this->astra_mandaue_facility + $this->astra_lapu_facility; //VACCINE ALLOCATED ASTRA REGION
        $this->sputnikv_region = $this->sputnikv_bohol + $this->sputnikv_cebu + $this->sputnikv_negros + $this->sputnikv_siquijor + $this->sputnikv_cebu_facility + $this->sputnikv_mandaue_facility + $this->sputnikv_lapu_facility; //VACCINE ALLOCATED SPUTNIKV REGION
        $this->pfizer_region = $this->pfizer_bohol + $this->pfizer_cebu + $this->pfizer_negros + $this->pfizer_siquijor + $this->pfizer_cebu_facility + $this->pfizer_mandaue_facility + $this->pfizer_lapu_facility; //VACCINE ALLOCATED PFIZER REGION

        //ELIGIBLE POP
        $this->eli_pop_bohol = Muncity::select(DB::raw("SUM(coalesce($priority,0)) as eli_pop_bohol"))->where("province_id",1)->first()->eli_pop_bohol; //TOTAL ELIGIBLE POP BOHOL
        $this->eli_pop_cebu = Muncity::select(DB::raw("SUM(coalesce($priority,0)) as eli_pop_cebu"))->where("province_id",2)->first()->eli_pop_cebu; //ELIGIBLE POP CEBU
        $this->eli_pop_negros = Muncity::select(DB::raw("SUM(coalesce($priority,0)) as eli_pop_negros"))->where("province_id",3)->first()->eli_pop_negros;  //ELIGIBLE POP NEGROS
        $this->eli_pop_siquijor = Muncity::select(DB::raw("SUM(coalesce($priority,0)) as eli_pop_siquijor"))->where("province_id",4)->first()->eli_pop_siquijor; //ELIGIBLE POP SIQUIJOR
        $this->eli_pop_cebu_facility = Muncity::select(DB::raw("SUM(coalesce(facility.$priority,0)) as eli_pop_cebu_facility"))
            ->leftJoin("facility","facility.tricity_id","=","muncity.id")
            ->where("muncity.id","=",63)
            ->first()->eli_pop_cebu_facility; //ELIGIBLE POP CEBU FACILITY
        $this->eli_pop_mandaue_facility = Muncity::select(DB::raw("SUM(coalesce(facility.$priority,0)) as eli_pop_mandaue_facility"))
            ->leftJoin("facility","facility.tricity_id","=","muncity.id")
            ->where("muncity.id","=",80)
            ->first()->eli_pop_mandaue_facility; //ELIGIBLE POP MANDAUAE FACILITY
        $this->eli_pop_lapu_facility = Muncity::select(DB::raw("SUM(coalesce(facility.$priority,0)) as eli_pop_lapu_facility"))
            ->leftJoin("facility","facility.tricity_id","=","muncity.id")
            ->where("muncity.id","=",76)
            ->first()->eli_pop_lapu_facility; //ELIGIBLE POP LAPU-LAPU FACILITY


        //VACCINATED SINOVAC FIRST
        $this->vcted_sinovac_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sinovac_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'Sinovac')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sinovac_bohol_first; //VACCINATED SINOVAC_FIRST BOHOL
        $this->vcted_sinovac_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sinovac_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'Sinovac')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sinovac_cebu_first; //VACCINATED SINOVAC_FIRST CEBU
        $this->vcted_sinovac_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sinovac_negros_first"))->where("province_id",3)->where("typeof_vaccine",'Sinovac')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sinovac_negros_first; //VACCINATED SINOVAC_FIRST NEGROS
        $this->vcted_sinovac_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sinovac_siquijor_first"))->where("province_id",4)->where("typeof_vaccine",'Sinovac')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sinovac_siquijor_first; //VACCINATED SINOVAC_FIRST SIQUIJOR
        $this->vcted_sinovac_cebu_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as vcted_sinovac_cebu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.typeof_vaccine",'Sinovac')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_sinovac_cebu_facility_first;  //VACCINATED SINOVAC_FIRST CEBU FACILITY
        $this->vcted_sinovac_mandaue_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as vcted_sinovac_mandaue_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.typeof_vaccine",'Sinovac')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_sinovac_mandaue_facility_first;  //VACCINATED SINOVAC_FIRST MANDAUE FACILITY
        $this->vcted_sinovac_lapu_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as vcted_sinovac_lapu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.typeof_vaccine",'Sinovac')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_sinovac_lapu_facility_first;  //VACCINATED SINOVAC_FIRST LAPU-LAPU FACILITY
        //VACCINATED SINOVAC SECOND
        $this->vcted_sinovac_bohol_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sinovac_bohol_second"))->where("province_id",1)->where("typeof_vaccine",'Sinovac')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sinovac_bohol_second; //VACCINATED SINOVAC_SECOND BOHOL
        $this->vcted_sinovac_cebu_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sinovac_cebu_second"))->where("province_id",2)->where("typeof_vaccine",'Sinovac')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sinovac_cebu_second; //VACCINATED SINOVAC_SECOND CEBU
        $this->vcted_sinovac_negros_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sinovac_negros_second"))->where("province_id",3)->where("typeof_vaccine",'Sinovac')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sinovac_negros_second; //VACCINATED SINOVAC_SECOND NEGROS
        $this->vcted_sinovac_siquijor_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sinovac_siquijor_second"))->where("province_id",4)->where("typeof_vaccine",'Sinovac')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sinovac_siquijor_second; //VACCINATED SINOVAC_SECOND SIQUIJOR
        $this->vcted_sinovac_cebu_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as vcted_sinovac_cebu_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.typeof_vaccine",'Sinovac')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_sinovac_cebu_facility_second; //VACCINATED SINOVAC_SECOND CEBU FACILITY
        $this->vcted_sinovac_mandaue_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as vcted_sinovac_mandaue_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.typeof_vaccine",'Sinovac')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_sinovac_mandaue_facility_second; //VACCINATED SINOVAC_SECOND MANDAUE FACILITY
        $this->vcted_sinovac_lapu_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as vcted_sinovac_lapu_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.typeof_vaccine",'Sinovac')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_sinovac_lapu_facility_second; //VACCINATED SINOVAC_SECOND LAPU-LAPU FACILITY

        //VACCINATED ASTRAZENECA FIRST
        $this->vcted_astra_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_astra_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'Astrazeneca')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_astra_bohol_first; //VACCINATED ASTRA_FIRST BOHOL
        $this->vcted_astra_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_astra_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'Astrazeneca')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_astra_cebu_first; //VACCINATED ASTRA_FIRST CEBU
        $this->vcted_astra_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_astra_negros_first"))->where("province_id",3)->where("typeof_vaccine",'Astrazeneca')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_astra_negros_first; //VACCINATED ASTRA_FIRST NEGROS
        $this->vcted_astra_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_astra_siquijor_first"))->where("province_id",4)->where("typeof_vaccine",'Astrazeneca')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_astra_siquijor_first; //VACCINATED ASTRA_FIRST SIQUIJOR
        $this->vcted_astra_cebu_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as vcted_astra_cebu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.typeof_vaccine",'Astrazeneca')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_astra_cebu_facility_first; //VACCINATED ASTRA_FIRST CEBU FACILITY
        $this->vcted_astra_mandaue_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as vcted_astra_mandaue_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.typeof_vaccine",'Astrazeneca')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_astra_mandaue_facility_first; //VACCINATED ASTRA_FIRST MANDAUE FACILITY
        $this->vcted_astra_lapu_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as vcted_astra_lapu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.typeof_vaccine",'Astrazeneca')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_astra_lapu_facility_first; //VACCINATED ASTRA_FIRST LAPU-LAPU FACILITY

        //VACCINATED ASTRAZENECA SECOND
        $this->vcted_astra_bohol_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_astra_bohol_second"))->where("province_id",1)->where("typeof_vaccine",'Astrazeneca')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_astra_bohol_second; //VACCINATED ASTRA_SECOND BOHOL
        $this->vcted_astra_cebu_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_astra_cebu_second"))->where("province_id",2)->where("typeof_vaccine",'Astrazeneca')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_astra_cebu_second; //VACCINATED ASTRA_SECOND CEBU
        $this->vcted_astra_negros_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_astra_negros_second"))->where("province_id",3)->where("typeof_vaccine",'Astrazeneca')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_astra_negros_second; //VACCINATED ASTRA_SECOND NEGROS
        $this->vcted_astra_siquijor_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_astra_siquijor_second"))->where("province_id",4)->where("typeof_vaccine",'Astrazeneca')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_astra_siquijor_second; //VACCINATED ASTRA_SECOND SIQUIJOR
        $this->vcted_astra_cebu_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as vcted_astra_cebu_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.typeof_vaccine",'Astrazeneca')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_astra_cebu_facility_second; //VACCINATED ASTRA_SECOND CEBU FACILITY
        $this->vcted_astra_mandaue_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as vcted_astra_mandaue_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.typeof_vaccine",'Astrazeneca')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_astra_mandaue_facility_second; //VACCINATED ASTRA_SECOND MANDAUE FACILITY
        $this->vcted_astra_lapu_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as vcted_astra_lapu_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.typeof_vaccine",'Astrazeneca')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_astra_lapu_facility_second; //VACCINATED ASTRA_SECOND LAPU-LAPU FACILITY
        //VACCINATED SPUTNIK V FIRST
        $this->vcted_sputnikv_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sputnikv_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'SputnikV')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sputnikv_bohol_first; //VACCINATED SPUTNIKV_FIRST BOHOL
        $this->vcted_sputnikv_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sputnikv_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'SputnikV')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sputnikv_cebu_first; //VACCINATED SPUTNIKV_FIRST CEBU
        $this->vcted_sputnikv_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sputnikv_negros_first"))->where("province_id",3)->where("typeof_vaccine",'SputnikV')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sputnikv_negros_first; //VACCINATED SPUTNIKV_FIRST NEGROS
        $this->vcted_sputnikv_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_sputnikv_siquijor_first"))->where("province_id",4)->where("typeof_vaccine",'SputnikV')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sputnikv_siquijor_first; //VACCINATED SPUTNIKV_FIRST SIQUIJOR
        $this->vcted_sputnikv_cebu_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as vcted_sputnikv_cebu_facility_first")) //VACCINATED SPUTNIKV_FIRST CEBU FACILITY
        ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.typeof_vaccine",'SputnikV')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_sputnikv_cebu_facility_first; //VACCINATED SPUTNIKV_FIRST CEBU FACILITY
        $this->vcted_sputnikv_mandaue_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as vcted_sputnikv_mandaue_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.typeof_vaccine",'SputnikV')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_sputnikv_mandaue_facility_first; //VACCINATED SPUTNIKV_FIRST MANDAUE FACILITY
        $this->vcted_sputnikv_lapu_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as vcted_sputnikv_lapu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.typeof_vaccine",'SputnikV')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_sputnikv_lapu_facility_first; //VACCINATED SPUTNIKV_FIRST LAPU-LAPU FACILITY

        //VACCINATED SPUTNIK V SECOND
        $this->vcted_sputnikv_bohol_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sputnikv_bohol_second"))->where("province_id",1)->where("typeof_vaccine",'SputnikV')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sputnikv_bohol_second; //VACCINATED SPUTNIKV_SECOND BOHOL
        $this->vcted_sputnikv_cebu_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sputnikv_cebu_second"))->where("province_id",2)->where("typeof_vaccine",'SputnikV')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sputnikv_cebu_second; //VACCINATED SPUTNIKV_SECOND CEBU
        $this->vcted_sputnikv_negros_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sputnikv_negros_second"))->where("province_id",3)->where("typeof_vaccine",'SputnikV')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sputnikv_negros_second; //VACCINATED SPUTNIKV_SECOND NEGROS
        $this->vcted_sputnikv_siquijor_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_sputnikv_siquijor_second"))->where("province_id",4)->where("typeof_vaccine",'SputnikV')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_sputnikv_siquijor_second; //VACCINATED SPUTNIKV_SECOND SIQUIJOR
        $this->vcted_sputnikv_cebu_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as vcted_sputnikv_cebu_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.typeof_vaccine",'Sputnikv')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_sputnikv_cebu_facility_second; //VACCINATED SPUTNIKV_SECOND CEBU FACILITY
        $this->vcted_sputnikv_mandaue_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as vcted_sputnikv_mandaue_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.typeof_vaccine",'Sputnikv')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_sputnikv_mandaue_facility_second; //VACCINATED SPUTNIKV_SECOND MANDAUE FACILITY
        $this->vcted_sputnikv_lapu_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as vcted_sputnikv_lapu_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.typeof_vaccine",'Sputnikv')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_sputnikv_lapu_facility_second; //VACCINATED SPUTNIKV_SECOND LAPU-LAPU FACILITY
        //VACCINATED PFIZER FIRST
        $this->vcted_pfizer_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_pfizer_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'Pfizer')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_pfizer_bohol_first; //VACCINATED PFIZER_FIRST BOHOL
        $this->vcted_pfizer_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_pfizer_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'Pfizer')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_pfizer_cebu_first; //VACCINATED PFIZER_FIRST CEBU
        $this->vcted_pfizer_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_pfizer_negros_first"))->where("province_id",3)->where("typeof_vaccine",'Pfizer')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_pfizer_negros_first; //VACCINATED PFIZER_FIRST NEGROS
        $this->vcted_pfizer_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_first,0)) as vcted_pfizer_siquijor_first"))->where("province_id",4)->where("typeof_vaccine",'Pfizer')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_pfizer_siquijor_first; //VACCINATED PFIZER_FIRST SIQUIJOR
        $this->vcted_pfizer_cebu_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as vcted_pfizer_cebu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.typeof_vaccine",'Pfizer')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_pfizer_cebu_facility_first; //VACCINATED PFIZER_FIRST CEBU FACILITY
        $this->vcted_pfizer_mandaue_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as vcted_pfizer_mandaue_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.typeof_vaccine",'Pfizer')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_pfizer_mandaue_facility_first; //VACCINATED PFIZER_FIRST MANDAUE_FACILITY
        $this->vcted_pfizer_lapu_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as vcted_pfizer_lapu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.typeof_vaccine",'Pfizer')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_pfizer_lapu_facility_first; //VACCINATED PFIZER_FIRST LAPU-LAPU FACILITY

        //VACCINATED PFIZER V SECOND
        $this->vcted_pfizer_bohol_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_pfizer_bohol_second"))->where("province_id",1)->where("typeof_vaccine",'Pfizer')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_pfizer_bohol_second; //VACCINATED PFIZER_SECOND BOHOL
        $this->vcted_pfizer_cebu_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_pfizer_cebu_second"))->where("province_id",2)->where("typeof_vaccine",'Pfizer')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_pfizer_cebu_second; //VACCINATED PFIZER_SECOND CEBU
        $this->vcted_pfizer_negros_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_pfizer_negros_second"))->where("province_id",3)->where("typeof_vaccine",'Pfizer')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_pfizer_negros_second; //VACCINATED PFIZER_SECOND NEGROS
        $this->vcted_pfizer_siquijor_second = VaccineAccomplished::select(DB::raw("SUM(coalesce(vaccinated_second,0)) as vcted_pfizer_siquijor_second"))->where("province_id",4)->where("typeof_vaccine",'Pfizer')->whereNull("facility_id")->where("priority",$priority)->first()->vcted_pfizer_siquijor_second; //VACCINATED PFIZER_SECOND SIQUIJOR
        $this->vcted_pfizer_cebu_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as vcted_pfizer_cebu_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.typeof_vaccine",'Pfizer')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_pfizer_cebu_facility_second; //VACCINATED PFIZER_SECOND CEBU FACILITY
        $this->vcted_pfizer_mandaue_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as vcted_pfizer_mandaue_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.typeof_vaccine",'Pfizer')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_pfizer_mandaue_facility_second; //VACCINATED PFIZER_SECOND MANDAUE FACILITY
        $this->vcted_pfizer_lapu_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as vcted_pfizer_lapu_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.typeof_vaccine",'Pfizer')
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->vcted_pfizer_lapu_facility_second; //VACCINATED PFIZER_SECOND LAPU-LAPU FACILITY
        //TOTAL_REFUSED_FIRST
        $this->refused_first_bohol = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_first,0)) as refused_first_bohol"))->where("province_id",1)->where("priority",$priority)->first()->refused_first_bohol; //REFUSED FIRST BOHOL
        $this->refused_first_cebu = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_first,0)) as refused_first_cebu"))->where("province_id",2)->where("priority",$priority)->first()->refused_first_cebu; //REFUSED FIRST CEBU
        $this->refused_first_negros = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_first,0)) as refused_first_negros"))->where("province_id",3)->where("priority",$priority)->first()->refused_first_negros; //REFUSED FIRST NEGROS
        $this->refused_first_siquijor = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_first,0)) as refused_first_siquijor"))->where("province_id",4)->where("priority",$priority)->first()->refused_first_siquijor; //REFUSED FIRST SIQUIJOR
        $this->refused_cebu_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.refused_first,0)) as refused_cebu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->refused_cebu_facility_first; //REFUSED FIRST CEBU FACILITY
        $this->refused_mandaue_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as refused_mandaue_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->refused_mandaue_facility_first; //REFUSED FIRST MANDAUAE FACILITY
        $this->refused_lapu_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_first,0)) as refused_lapu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->refused_lapu_facility_first; //REFUSED FIRST LAPU-LAPU FACILITY

        //TOTAL_REFUSED_SECOND
        $this->refused_second_bohol = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_second,0)) as refused_second_bohol"))->where("province_id",1)->where("priority",$priority)->first()->refused_second_bohol; //REFUSED SECOND BOHOL
        $this->refused_second_cebu = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_second,0)) as refused_second_cebu"))->where("province_id",2)->where("priority",$priority)->first()->refused_second_cebu; //REFUSED SECOND CEBU
        $this->refused_second_negros = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_second,0)) as refused_second_negros"))->where("province_id",3)->where("priority",$priority)->first()->refused_second_negros; //REFUSED SECOND NEGROS
        $this->refused_second_siquijor = VaccineAccomplished::select(DB::raw("SUM(coalesce(refused_second,0)) as refused_second_siquijor"))->where("province_id",4)->where("priority",$priority)->first()->refused_second_siquijor; //REFUSED SECOND SIQUIJOR
        $this->refused_cebu_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.refused_second ,0)) as refused_cebu_facility_second "))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->refused_cebu_facility_second; //REFUSED SECOND CEBU FACILITY
        $this->refused_mandaue_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as refused_mandaue_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->refused_mandaue_facility_second; //REFUSED SECOND MANDAUAE FACILITY
        $this->refused_lapu_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.vaccinated_second,0)) as refused_lapu_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->refused_lapu_facility_second; //REFUSED SECOND LAPU-LAPU FACILITY

        //TOTAL_DEFERRED_FIRST
        $this->deferred_first_bohol = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_first,0)) as deferred_first_bohol"))->where("province_id",1)->where("priority",$priority)->first()->deferred_first_bohol; //DEFERRED FIRST BOHOL
        $this->deferred_first_cebu = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_first,0)) as deferred_first_cebu"))->where("province_id",2)->where("priority",$priority)->first()->deferred_first_cebu; //DEFERRED FIRST CEBU
        $this->deferred_first_negros = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_first,0)) as deferred_first_negros"))->where("province_id",3)->where("priority",$priority)->first()->deferred_first_negros; //DEFERRED FIRST NEGROS
        $this->deferred_first_siquijor = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_first,0)) as deferred_first_siquijor"))->where("province_id",4)->where("priority",$priority)->first()->deferred_first_siquijor; //DEFERRED FIRST SIQUIJOR
        $this->deferred_cebu_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.deferred_first ,0)) as deferred_cebu_facility_first "))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->deferred_cebu_facility_first; //DEFERRED FIRST CEBU FACILITY
        $this->deferred_mandaue_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.deferred_first,0)) as deferred_mandaue_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->deferred_mandaue_facility_first; //DEFERRED FIRST MANDAUE FACILITY
        $this->deferred_lapu_facility_first = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.deferred_first,0)) as deferred_lapu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->deferred_lapu_facility_first; //DEFERRED FIRST LAPU-LAPU FACILITY

        //TOTAL_DEFERRED_SECOND
        $this->deferred_second_bohol = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_second,0)) as deferred_second_bohol"))->where("province_id",1)->where("priority",$priority)->first()->deferred_second_bohol; //DEFERRED SECOND BOHOL
        $this->deferred_second_cebu = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_second,0)) as deferred_second_cebu"))->where("province_id",2)->where("priority",$priority)->first()->deferred_second_cebu; //DEFERRED SECOND CEBU
        $this->deferred_second_negros = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_second,0)) as deferred_second_negros"))->where("province_id",3)->where("priority",$priority)->first()->deferred_second_negros; //DEFERRED SECOND NEGROS
        $this->deferred_second_siquijor = VaccineAccomplished::select(DB::raw("SUM(coalesce(deferred_second,0)) as deferred_second_siquijor"))->where("province_id",4)->where("priority",$priority)->first()->deferred_second_siquijor;  //DEFERRED SECOND SIQUIJOR
        $this->deferred_cebu_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.deferred_second ,0)) as deferred_cebu_facility_second "))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->deferred_cebu_facility_second; //DEFERRED SECOND CEBU FACILITY
        $this->deferred_mandaue_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.deferred_second,0)) as deferred_mandaue_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->deferred_mandaue_facility_second; //DEFERRED SECOND MANDAUE FACILITY
        $this->deferred_lapu_facility_second = Facility::select(DB::raw("SUM(coalesce(vaccine_accomplish.deferred_second,0)) as deferred_lapu_facility_second"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.priority",$priority)
            ->first()
            ->deferred_lapu_facility_second; //DEFERRED SECOND LAPU-LAPU FACILITY

        //WASTAGE_SINOVAC_FIRST
        $this->wastage_sinovac_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sinovac_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'Sinovac')->where("priority",$priority)->first()->wastage_sinovac_bohol_first; //WASTAGE SINOVAC FIRST BOHOL
        $this->wastage_sinovac_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sinovac_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'Sinovac')->where("priority",$priority)->first()->wastage_sinovac_cebu_first; //WASTAGE SINOVAC FIRST CEBU
        $this->wastage_sinovac_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sinovac_negros_first"))->where("province_id",3)->where("typeof_vaccine",'Sinovac')->where("priority",$priority)->first()->wastage_sinovac_negros_first; //WASTAGE SINOVAC FIRST NEGROS
        $this->wastage_sinovac_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sinovac_siquior_first"))->where("province_id",4)->where("typeof_vaccine",'Sinovac')->where("priority",$priority)->first()->wastage_sinovac_siquior_first; //WASTAGE SINOVAC FIRST SIQUIJOR
        $this->wastage_sinovac_cebu_facility_first = Facility::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sinovac_cebu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.priority",$priority)
            ->where("typeof_vaccine",'Sinovac')
            ->first()
            ->wastage_sinovac_cebu_facility_first; //WASTAGE SINOVAC FIRST CEBU FACILITY
        $this->wastage_sinovac_mandaue_facility_first = Facility::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sinovac_mandaue_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.priority",$priority)
            ->where("typeof_vaccine",'Sinovac')
            ->first()
            ->wastage_sinovac_mandaue_facility_first; //WASTAGE SINOVAC FIRST MANDAUE FACILITY
        $this->wastage_sinovac_lapu_facility_first = Facility::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sinovac_lapu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.priority",$priority)
            ->where("typeof_vaccine",'Sinovac')
            ->first()
            ->wastage_sinovac_lapu_facility_first; //WASTAGE SINOVAC FIRST LAPU-LAPU FACILITY
        //WASTAGE_ASTRA
        $this->wastage_astra_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_astra_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'Astrazeneca')->where("priority",$priority)->first()->wastage_astra_bohol_first; //WASTAGE ASTRA FIRST BOHOL
        $this->wastage_astra_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_astra_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'Astrazeneca')->where("priority",$priority)->first()->wastage_astra_cebu_first;  //WASTAGE ASTRA FIRST CEBU
        $this->wastage_astra_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_astra_negros_first"))->where("province_id",3)->where("typeof_vaccine",'Astrazeneca')->where("priority",$priority)->first()->wastage_astra_negros_first;  //WASTAGE ASTRA FIRST NEGROS
        $this->wastage_astra_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_astra_siquior_first"))->where("province_id",4)->where("typeof_vaccine",'Astrazeneca')->where("priority",$priority)->first()->wastage_astra_siquior_first;  //WASTAGE ASTRA FIRST SIQUIJOR
        $this->wastage_astra_cebu_facility_first = Facility::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_astra_cebu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.priority",$priority)
            ->where("typeof_vaccine",'Astrazeneca')
            ->first()
            ->wastage_astra_cebu_facility_first;  //WASTAGE ASTRA FIRST CEBU FACILITY
        $this->wastage_astra_mandaue_facility_first = Facility::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_astra_mandaue_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.priority",$priority)
            ->where("typeof_vaccine",'Astrazeneca')
            ->first()
            ->wastage_astra_mandaue_facility_first; //WASTAGE ASTRA FIRST MANDAUE FACILITY
        $this->wastage_astra_lapu_facility_first = Facility::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_astra_lapu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.priority",$priority)
            ->where("typeof_vaccine",'Astrazeneca')
            ->first()
            ->wastage_astra_lapu_facility_first; //WASTAGE ASTRA FIRST LAPU-LAPU FACILITY

        //WASTAGE_SPUTNIKV
        $this->wastage_sputnikv_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sputnikv_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'SputnikV')->where("priority",$priority)->first()->wastage_sputnikv_bohol_first; //WASTAGE SPUTNIKV FIRST BOHOL
        $this->wastage_sputnikv_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sputnikv_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'SputnikV')->where("priority",$priority)->first()->wastage_sputnikv_cebu_first; //WASTAGE SPUTNIKV FIRST CEBU
        $this->wastage_sputnikv_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sputnikv_negros_first"))->where("province_id",3)->where("typeof_vaccine",'SputnikV')->where("priority",$priority)->first()->wastage_sputnikv_negros_first; //WASTAGE SPUTNIKV FIRST NEGROS
        $this->wastage_sputnikv_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sputnikv_siquior_first"))->where("province_id",4)->where("typeof_vaccine",'SputnikV')->where("priority",$priority)->first()->wastage_sputnikv_siquior_first; //WASTAGE SPUTNIKV FIRST SIQUIJOR
        $this->wastage_sputnikv_cebu_facility_first = Facility::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sputnikv_cebu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.priority",$priority)
            ->where("typeof_vaccine",'SputnikV')
            ->first()
            ->wastage_sputnikv_cebu_facility_first; //WASTAGE SPUTNIKV FIRST CEBU FACILITY
        $this->wastage_sputnikv_mandaue_facility_first = Facility::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sputnikv_mandaue_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.priority",$priority)
            ->where("typeof_vaccine",'SputnikV')
            ->first()
            ->wastage_sputnikv_mandaue_facility_first; //WASTAGE SPUTNIKV FIRST MANDAUE FACILITY
        $this->wastage_sputnikv_lapu_facility_first = Facility::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_sputnikv_lapu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.priority",$priority)
            ->where("typeof_vaccine",'SputnikV')
            ->first()
            ->wastage_sputnikv_lapu_facility_first; //WASTAGE SPUTNIKV FIRST LAPU-LAPU FACILITY


        //WASTAGE_PFIZER
        $this->wastage_pfizer_bohol_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_pfizer_bohol_first"))->where("province_id",1)->where("typeof_vaccine",'Pfizer')->where("priority",$priority)->first()->wastage_pfizer_bohol_first; //WASTAGE PFIZER FIRST BOHOL
        $this->wastage_pfizer_cebu_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_pfizer_cebu_first"))->where("province_id",2)->where("typeof_vaccine",'Pfizer')->where("priority",$priority)->first()->wastage_pfizer_cebu_first; //WASTAGE PFIZER FIRST CEBU
        $this->wastage_pfizer_negros_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_pfizer_negros_first"))->where("province_id",3)->where("typeof_vaccine",'Pfizer')->where("priority",$priority)->first()->wastage_pfizer_negros_first; //WASTAGE PFIZER FIRST NEGROS
        $this->wastage_pfizer_siquijor_first = VaccineAccomplished::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_pfizer_siquior_first"))->where("province_id",4)->where("typeof_vaccine",'Pfizer')->where("priority",$priority)->first()->wastage_pfizer_siquior_first; //WASTAGE PFIZER FIRST SIQUIJOR
        $this->wastage_pfizer_cebu_facility_first = Facility::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_pfizer_cebu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",63)
            ->where("vaccine_accomplish.priority",$priority)
            ->where("typeof_vaccine",'Pfizer')
            ->first()
            ->wastage_pfizer_cebu_facility_first; //WASTAGE PFIZER FIRST CEBU FACILITY
        $this->wastage_pfizer_mandaue_facility_first = Facility::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_pfizer_mandaue_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",80)
            ->where("vaccine_accomplish.priority",$priority)
            ->where("typeof_vaccine",'Pfizer')
            ->first()
            ->wastage_pfizer_mandaue_facility_first; //WASTAGE PFIZER FIRST MANDAUE FACILITY
        $this->wastage_pfizer_lapu_facility_first = Facility::select(DB::raw("SUM(coalesce(wastage_first,0)+coalesce(wastage_second,0)) as wastage_pfizer_lapu_facility_first"))
            ->leftJoin("vaccine_accomplish","vaccine_accomplish.facility_id","=","facility.id")
            ->where("facility.tricity_id","=",76)
            ->where("vaccine_accomplish.priority",$priority)
            ->where("typeof_vaccine",'Pfizer')
            ->first()
            ->wastage_pfizer_lapu_facility_first; //WASTAGE PFIZER FIRST LAPU-LAPU FACILITY

        //REGION VACCINATED
        $this->region_sinovac_first_dose = $this->vcted_sinovac_bohol_first + $this->vcted_sinovac_cebu_first + $this->vcted_sinovac_negros_first + $this->vcted_sinovac_siquijor_first +
                                           $this->vcted_sinovac_cebu_facility_first + $this->vcted_sinovac_mandaue_facility_first + $this->vcted_sinovac_lapu_facility_first ; //VACCINATED SINOVAC_FIRST REGION
        $this->region_sinovac_second_dose = $this->vcted_sinovac_bohol_second + $this->vcted_sinovac_cebu_second + $this->vcted_sinovac_negros_second + $this->vcted_sinovac_siquijor_second +
                                      $this->vcted_sinovac_cebu_facility_second + $this->vcted_sinovac_mandaue_facility_second + $this->vcted_sinovac_lapu_facility_second; //VACCINATED SINOVAC_SECOND REGION
        $this->region_astra_first_dose = $this->vcted_astra_bohol_first + $this->vcted_astra_cebu_first + $this->vcted_astra_negros_first + $this->vcted_astra_siquijor_first +
                                   $this->vcted_astra_cebu_facility_first + $this->vcted_astra_mandaue_facility_first + $this->vcted_astra_lapu_facility_first; //VACCINATED ASTRA_FIRST REGION
        $this->region_astra_second_dose = $this->vcted_astra_bohol_second + $this->vcted_astra_cebu_second + $this->vcted_astra_negros_second + $this->vcted_astra_siquijor_second +
                                    $this->vcted_astra_cebu_facility_second + $this->vcted_astra_mandaue_facility_second + $this->vcted_astra_cebu_facility_second; //VACCINATED ASTRA_SECOND REGION
        $this->region_sputnikv_first_dose = $this->vcted_sputnikv_bohol_first + $this->vcted_sputnikv_cebu_first + $this->vcted_sputnikv_negros_first + $this->vcted_sputnikv_siquijor_first +
                                      $this->vcted_sputnikv_cebu_facility_first + $this->vcted_sputnikv_mandaue_facility_first + $this->vcted_sputnikv_lapu_facility_first; //VACCINATED SPUTNIKV_FIRST REGION
        $this->region_sputnikv_second_dose = $this->vcted_sputnikv_bohol_second + $this->vcted_sputnikv_cebu_second + $this->vcted_sputnikv_negros_second + $this->vcted_sputnikv_siquijor_second +
                                       $this->vcted_sputnikv_cebu_facility_second + $this->vcted_sputnikv_mandaue_facility_second + $this->vcted_sputnikv_lapu_facility_second; //VACCINATED SPUTNIKV_SECOND REGION
        $this->region_pfizer_first_dose = $this->vcted_pfizer_bohol_first + $this->vcted_pfizer_cebu_first + $this->vcted_pfizer_negros_first + $this->vcted_pfizer_siquijor_first +
                                    $this->vcted_pfizer_cebu_facility_first + $this->vcted_pfizer_mandaue_facility_first + $this->vcted_pfizer_lapu_facility_first; //VACCINATED PFIZER_FIRST REGION
        $this->region_pfizer_second_dose = $this->vcted_pfizer_bohol_second + $this->vcted_pfizer_cebu_second + $this->vcted_pfizer_negros_second + $this->vcted_pfizer_siquijor_second +
                                     $this->vcted_pfizer_cebu_facility_second + $this->vcted_pfizer_mandaue_facility_second + $this->vcted_pfizer_lapu_facility_second; //VACCINATED PFIZER_SECOND REGION

        $this->total_elipop_region = $this->eli_pop_bohol + $this->eli_pop_cebu + $this->eli_pop_siquijor + $this->eli_pop_cebu_facility + $this->eli_pop_mandaue_facility + $this->eli_pop_lapu_facility; //TOTAL ELIGBLE_POP REGION
        $this->first_dose_total = $this->region_sinovac_first_dose + $this->region_astra_first_dose + $this->region_sputnikv_first_dose + $this->region_pfizer_first_dose; //TOTAL VACCINATED FIRST REGION
        $this->second_dose_total = $this->region_sinovac_second_dose + $this->region_astra_second_dose + $this->region_sputnikv_second_dose + $this->region_pfizer_second_dose; //TOTAL VACCINATED SECOND REGION

        $this->total_region = $this->sinovac_region + $this->astra_region + $this->sputnikv_region + $this->pfizer_region; //VACCINE ALLOCATED TOTAL REGION
        $this->total_bohol = $this->sinovac_bohol + $this->astra_bohol + $this->sputnikv_bohol + $this->pfizer_bohol; //TOTAL VACCINE ALLOCATED BOHOL
        $this->total_cebu = $this->sinovac_cebu + $this->astra_cebu + $this->sputnikv_cebu + $this->pfizer_cebu; //TOTAL VACCINE ALLOCATED CEBU
        $this->total_negros = $this->sinovac_negros + $this->astra_negros + $this->sputnikv_negros + $this->pfizer_negros; //TOTAL VACCINE ALLOCATED NEGROS
        $this->total_siquijor = $this->sinovac_siquijor + $this->astra_siquijor + $this->sputnikv_siquijor + $this->pfizer_siquijor; //TOTAL VACCINE ALLOCATED SIQUIJOR
        $this->total_cebu_facility = $this->sinovac_cebu_facility + $this->astra_cebu_facility + $this->sputnikv_cebu_facility + $this->pfizer_cebu_facility; //TOTAL VACCINE ALLOCATED CEBU FACILITY
        $this->total_mandaue_facility = $this->sinovac_mandaue_facility + $this->astra_mandaue_facility + $this->sputnikv_mandaue_facility + $this->pfizer_mandaue_facility; //TOTAL VACCINE ALLOCATED MANDAUE FACILITY
        $this->total_lapu_facility = $this->sinovac_lapu_facility + $this->astra_lapu_facility + $this->sputnikv_lapu_facility + $this->pfizer_lapu_facility; //TOTAL VACCINE ALLOCATED LAPU-LAPU FACILITY


        //TOTAL VACCINATED_FIRST
        $this->total_vcted_first_bohol = $this->vcted_sinovac_bohol_first + $this->vcted_astra_bohol_first + $this->vcted_sputnikv_bohol_first + $this->vcted_pfizer_bohol_first; //TOTAL VACCINATED FIRST BOHOL
        $this->total_vcted_first_cebu = $this->vcted_sinovac_cebu_first + $this->vcted_astra_cebu_first + $this->vcted_sputnikv_cebu_first + $this->vcted_pfizer_cebu_first; //TOTAL VACCINATED FIRST CEBU
        $this->total_vcted_first_negros = $this->vcted_sinovac_negros_first + $this->vcted_astra_negros_first + $this->vcted_sputnikv_negros_first + $this->vcted_pfizer_negros_first; //TOTAL VACCINATED FIRST NEGROS
        $this->total_vcted_first_siquijor = $this->vcted_sinovac_siquijor_first + $this->vcted_astra_siquijor_first + $this->vcted_sputnikv_siquijor_first + $this->vcted_pfizer_siquijor_first; //TOTAL VACCINATED FIRST SIQUIJOR
        $this->total_vcted_cebu_facility_first = $this->vcted_sinovac_cebu_facility_first + $this->vcted_astra_cebu_facility_first + $this->vcted_sputnikv_cebu_facility_first + $this->vcted_pfizer_cebu_facility_first; //TOTAL VACCINATED FIRST CEBU FACILITY
        $this->total_vcted_mandaue_facility_first = $this->vcted_sinovac_mandaue_facility_first + $this->vcted_astra_mandaue_facility_first + $this->vcted_sputnikv_mandaue_facility_first + $this->vcted_pfizer_mandaue_facility_first; //TOTAL VACCINATED MANDAUE FACILITY
        $this->total_vcted_lapu_facility_first = $this->vcted_sinovac_lapu_facility_first + $this->vcted_astra_lapu_facility_first + $this->vcted_sputnikv_lapu_facility_first + $this->vcted_pfizer_lapu_facility_first; //TOTAL VACCINATED FIRST LAPU-LAPU FACILITY

        //TOTAL VACCINATED SECOND
        $this->total_vcted_second_bohol = $this->vcted_sinovac_bohol_second + $this->vcted_astra_bohol_second + $this->vcted_sputnikv_bohol_second + $this->vcted_pfizer_bohol_second; //TOTAL VACCINATED SECOND BOHOL
        $this->total_vcted_second_cebu = $this->vcted_sinovac_cebu_second + $this->vcted_astra_cebu_second + $this->vcted_sputnikv_siquijor_second + $this->vcted_pfizer_cebu_second; //TOTAL VACCINATED SECOND CEBU
        $this->total_vcted_second_negros = $this->vcted_sinovac_negros_second + $this->vcted_astra_negros_second + $this->vcted_sputnikv_negros_second + $this->vcted_pfizer_negros_second; //TOTAL VACCINATED SECOND NEGROS
        $this->total_vcted_second_siquijor = $this->vcted_sinovac_siquijor_second + $this->vcted_astra_siquijor_second + $this->vcted_sputnikv_siquijor_second + $this->vcted_pfizer_siquijor_second; //TOTAL VACCINATED SECOND SIQUIJOR
        $this->total_vcted_cebu_facility_second = $this->vcted_sinovac_cebu_facility_second + $this->vcted_astra_cebu_facility_second + $this->vcted_sputnikv_cebu_facility_second + $this->vcted_pfizer_cebu_facility_second; //TOTAL VACCINATED SECOND CEBU FACILITY
        $this->total_vcted_mandaue_facility_second = $this->vcted_sinovac_mandaue_facility_second + $this->vcted_astra_mandaue_facility_second + $this->vcted_sputnikv_mandaue_facility_second + $this->vcted_pfizer_mandaue_facility_second; //TOTAL VACCINATED SECOND MANDAUE FACILITY
        $this->total_vcted_lapu_facility_second = $this->vcted_sinovac_lapu_facility_second + $this->vcted_astra_lapu_facility_second + $this->vcted_sputnikv_lapu_facility_second + $this->vcted_pfizer_lapu_facility_second; //TOTAL VACCINATED SECOND LAPU-LAPU FACILITY


        //PERCENT_COVERAGE_SINOVAC_FIRST
        $this->p_cvrge_sinovac_bohol_first = number_format($this->vcted_sinovac_bohol_first / $this->eli_pop_bohol * 100,2); //PERCENT COVERAGE SINOVAC FIRST BOHOL
        $this->p_cvrge_sinovac_cebu_first = number_format($this->vcted_sinovac_cebu_first / $this->eli_pop_cebu * 100,2); //PERCENT COVERAGE SINOVAC FIRST CEBU
        $this->p_cvrge_sinovac_negros_first = number_format($this->vcted_sinovac_negros_first / $this->eli_pop_negros * 100,2); //PERCENT COVERAGE SINOVAC FIRST NEGROS
        $this->p_cvrge_sinovac_siquijor_first = number_format($this->vcted_sinovac_siquijor_first / $this->eli_pop_siquijor * 100,2); //PERCENT COVERAGE SINOVAC FIRST SIQUIJOR
        $this->p_cvrge_sinovac_cebu_facility_first = number_format($this->vcted_sinovac_cebu_facility_first / $this->eli_pop_cebu_facility * 100,2); //PERCENT COVERAGE SINOVAC FIRST CEBU FACILITY
        $this->p_cvrge_sinovac_mandaue_facility_first = number_format($this->vcted_sinovac_mandaue_facility_first / $this->eli_pop_mandaue_facility * 100,2); //PERCENT COVERAGE SINOVAC FIRST MANDAUAE FACILITY
        $this->p_cvrge_sinovac_lapu_facility_first = number_format($this->vcted_sinovac_lapu_facility_first / $this->eli_pop_lapu_facility * 100,2); //PERCENT COVERAGE SINOVAC FIRST LAPU-LAPU FACILITY

        //PERCENT_COVERAGE_ASTRA_FIRST
        $this->p_cvrge_astra_bohol_first = number_format($this->vcted_astra_bohol_first / $this->eli_pop_bohol * 100,2); //PERCENT COVERAGE ASTRA FIRST BOHOL
        $this->p_cvrge_astra_cebu_first = number_format($this->vcted_astra_cebu_first / $this->eli_pop_cebu * 100,2); //PERCENT COVERAGE ASTRA FIRST CEBU
        $this->p_cvrge_astra_negros_first = number_format($this->vcted_astra_negros_first / $this->eli_pop_negros * 100,2); //PERCENT COVERAGE ASTRA FIRST NEGROS
        $this->p_cvrge_astra_siquijor_first = number_format($this->vcted_astra_siquijor_first / $this->eli_pop_siquijor * 100,2); //PERCENT COVERAGE ASTRA FIRST SIQUIJOR
        $this->p_cvrge_astra_cebu_facility_first = number_format($this->vcted_astra_cebu_facility_first / $this->eli_pop_cebu_facility * 100,2); //PERCENT COVERAGE ASTRA FIRST CEBU FACILITY
        $this->p_cvrge_astra_mandaue_facility_first = number_format($this->vcted_astra_mandaue_facility_first / $this->eli_pop_mandaue_facility * 100,2); //PERCENT COVERAGE ASTRA MANDAUE FACILITY
        $this->p_cvrge_astra_lapu_facility_first = number_format($this->vcted_astra_lapu_facility_first / $this->eli_pop_lapu_facility * 100,2); //PERCENT COVERAGE ASTRA FIRST LAPU-LAPU FACILITY

        //PERCENT_COVERAGE_SPUTNIKV_FIRST
        $this->p_cvrge_sputnikv_bohol_first = number_format($this->vcted_sputnikv_bohol_first / $this->eli_pop_bohol * 100,2); //PERCENT COVERAGE SPUTNIKV FIRST BOHOL
        $this->p_cvrge_sputnikv_cebu_first = number_format($this->vcted_sputnikv_cebu_first / $this->eli_pop_cebu * 100,2); //PERCENT COVERAGE SPUTNIKV FIRST CEBU
        $this->p_cvrge_sputnikv_negros_first = number_format($this->vcted_sputnikv_negros_first / $this->eli_pop_negros * 100,2); //PERCENT COVERAGE SPUTNIKV FIRST NEGROS
        $this->p_cvrge_sputnikv_siquijor_first = number_format($this->vcted_sputnikv_siquijor_first / $this->eli_pop_siquijor * 100,2); //PERCENT COVERAGE SPUTNIKV FIRST SIQUIJOR
        $this->p_cvrge_sputnikv_cebu_facility_first = number_format($this->vcted_sputnikv_cebu_facility_first / $this->eli_pop_cebu_facility * 100,2); //PERCENT COVERAGE SPUTNIKV FIRST CEBU FACILITY
        $this->p_cvrge_sputnikv_mandaue_facility_first = number_format($this->vcted_sputnikv_mandaue_facility_first / $this->eli_pop_mandaue_facility * 100,2); //PERCENT COVERAGE SPUTNIKV FIRST MANDAUE FACILITY
        $this->p_cvrge_sputnikv_lapu_facility_first = number_format($this->vcted_sputnikv_lapu_facility_first / $this->eli_pop_lapu_facility * 100,2); //PERCENT COVERAGE SPUTNIKV FIRST LAPU-LAPU FACILITY

        //PERCENT_COVERAGE_PFIZER_FIRST
        $this->p_cvrge_pfizer_bohol_first = number_format($this->vcted_pfizer_bohol_first / $this->eli_pop_bohol * 100,2); //PERCENT COVERAGE PFIZER FIRST BOHOL
        $this->p_cvrge_pfizer_cebu_first = number_format($this->vcted_pfizer_cebu_first / $this->eli_pop_cebu * 100,2); //PERCENT COVERAGE PFIZER FIRST CEBU
        $this->p_cvrge_pfizer_negros_first = number_format($this->vcted_pfizer_negros_first / $this->eli_pop_negros * 100,2); //PERCENT COVERAGE PFIZER FIRST NEGROS
        $this->p_cvrge_pfizer_siquijor_first = number_format($this->vcted_pfizer_siquijor_first / $this->eli_pop_siquijor * 100,2); //PERCENT COVERAGE PFIZER FIRST SIQUIJOR
        $this->p_cvrge_pfizer_cebu_facility_first = number_format($this->vcted_pfizer_cebu_facility_first / $this->eli_pop_cebu_facility * 100,2); //PERCENT COVERAGE PFIZER FIRST CEBU FACILITY
        $this->p_cvrge_pfizer_mandaue_facility_first = number_format($this->vcted_pfizer_mandaue_facility_first / $this->eli_pop_mandaue_facility * 100,2); //PERCENT COVERAGE PFIZER FIRST MANDAUE FACILITY
        $this->p_cvrge_pfizer_lapu_facility_first = number_format($this->vcted_pfizer_lapu_facility_first / $this->eli_pop_lapu_facility * 100,2); //PERCENT COVERAGE PFIZER FIRST LAPU-LAPU FACILITY

        //TOTAL_PERCENT_COVERAGE_FIRST
        $this->total_p_cvrge_bohol_first = number_format($this->total_vcted_first_bohol / $this->eli_pop_bohol * 100,2 ); //TOTAL PERCENT COVERAGE FIRST BOHOL
        $this->total_p_cvrge_cebu_first = number_format($this->total_vcted_first_cebu / $this->eli_pop_cebu * 100,2 ); //TOTAL PERCENT COVERAGE FIRST CEBU
        $this->total_p_cvrge_negros_first = number_format($this->total_vcted_first_negros / $this->eli_pop_negros * 100,2 );  //TOTAL PERCENT COVERAGE FIRST NEGROS
        $this->total_p_cvrge_siquijor_first = number_format($this->total_vcted_first_siquijor / $this->eli_pop_siquijor * 100,2 );  //TOTAL PERCENT COVERAGE FIRST SIQUIJOR
        $this->total_p_cvrge_cebu_facility_first = number_format($this->total_vcted_cebu_facility_first / $this->eli_pop_cebu_facility * 100,2 );  //TOTAL PERCENT COVERAGE FIRST CEBU FACILITY
        $this->total_p_cvrge_mandaue_facility_first = number_format($this->total_vcted_mandaue_facility_first / $this->eli_pop_mandaue_facility * 100,2 ); //TOTAL PERCENT COVERAGE FIRST MANDAUE FACILITY
        $this->total_p_cvrge_lapu_facility_first = number_format($this->total_vcted_lapu_facility_first / $this->eli_pop_lapu_facility * 100,2 );  //TOTAL PERCENT COVERAGE FIRST LAPU-LAPU FACILITY

        //PERCENT_COVERAGE_SINOVAC_SECOND
        $this->p_cvrge_sinovac_bohol_second = number_format($this->vcted_sinovac_bohol_second / $this->eli_pop_bohol * 100,2); //PPERCENT COVERAGE SINOVAC SECOND BOHOL
        $this->p_cvrge_sinovac_cebu_second = number_format($this->vcted_sinovac_cebu_second / $this->eli_pop_cebu * 100,2); //PPERCENT COVERAGE SINOVAC SECOND CEBU
        $this->p_cvrge_sinovac_negros_second = number_format($this->vcted_sinovac_negros_second / $this->eli_pop_negros * 100,2); //PPERCENT COVERAGE SINOVAC SECOND NEGROS
        $this->p_cvrge_sinovac_siquijor_second = number_format($this->vcted_sinovac_siquijor_second / $this->eli_pop_siquijor * 100,2); //PPERCENT COVERAGE SINOVAC SECOND SIQUIJOR
        $this->p_cvrge_sinovac_cebu_facility_second = number_format($this->vcted_sinovac_cebu_facility_second / $this->eli_pop_cebu_facility * 100,2); //PPERCENT COVERAGE SINOVAC SECOND CEBU FACILITY
        $this->p_cvrge_sinovac_mandaue_facility_second = number_format($this->vcted_sinovac_mandaue_facility_second / $this->eli_pop_mandaue_facility * 100,2); //PPERCENT COVERAGE SINOVAC SECOND MANDAUE FACILITY
        $this->p_cvrge_sinovac_lapu_facility_second = number_format($this->vcted_sinovac_lapu_facility_second / $this->eli_pop_lapu_facility * 100,2); //PPERCENT COVERAGE SINOVAC SECOND LAPU-LAPU FACILITY

        //PERCENT_COVERAGE_ASTRA_SECOND
        $this->p_cvrge_astra_bohol_second = number_format($this->vcted_astra_bohol_second / $this->eli_pop_bohol * 100,2); //PERCENT COVERAGE ASTRA SECOND BOHOL
        $this->p_cvrge_astra_cebu_second = number_format($this->vcted_astra_cebu_second / $this->eli_pop_cebu * 100,2); //PERCENT COVERAGE ASTRA SECOND CEBU
        $this->p_cvrge_astra_negros_second = number_format($this->vcted_astra_negros_second / $this->eli_pop_negros * 100,2); //PERCENT COVERAGE ASTRA SECOND NEGROS
        $this->p_cvrge_astra_siquijor_second = number_format($this->vcted_astra_siquijor_second / $this->eli_pop_siquijor * 100,2); //PERCENT COVERAGE ASTRA SECOND SIQUIJOR
        $this->p_cvrge_astra_cebu_facility_second = number_format($this->vcted_astra_cebu_facility_second / $this->eli_pop_cebu_facility * 100,2); //PERCENT COVERAGE ASTRA SECOND CEBU FACILITY
        $this->p_cvrge_astra_mandaue_facility_second = number_format($this->vcted_astra_mandaue_facility_second / $this->eli_pop_mandaue_facility * 100,2); //PERCENT COVERAGE ASTRA SECOND MANDAUE FACILITY
        $this->p_cvrge_astra_lapu_facility_second = number_format($this->vcted_astra_lapu_facility_second / $this->eli_pop_lapu_facility * 100,2); //PERCENT COVERAGE ASTRA SECOND LAPU-LAPU FACILITY


        //PERCENT_COVERAGE_SPUTNIKV_SECOND
        $this->p_cvrge_sputnikv_bohol_second = number_format($this->vcted_sputnikv_bohol_second / $this->eli_pop_bohol * 100,2); //PERCENT COVERAGE SPUTNIKV SECOND BOHOL
        $this->p_cvrge_sputnikv_cebu_second = number_format($this->vcted_sputnikv_cebu_second / $this->eli_pop_cebu * 100,2); //PERCENT COVERAGE SPUTNIKV SECOND CEBU
        $this->p_cvrge_sputnikv_negros_second = number_format($this->vcted_sputnikv_negros_second / $this->eli_pop_negros * 100,2); //PERCENT COVERAGE SPUTNIKV SECOND NEGROS
        $this->p_cvrge_sputnikv_siquijor_second = number_format($this->vcted_sputnikv_siquijor_second / $this->eli_pop_siquijor * 100,2); //PERCENT COVERAGE SPUTNIKV SECOND SIQUIJOR
        $this->p_cvrge_sputnikv_cebu_facility_second = number_format($this->vcted_sputnikv_cebu_facility_second / $this->eli_pop_cebu_facility * 100,2); //PERCENT COVERAGE SPUTNIKV SECOND CEBU FACILITY
        $this->p_cvrge_sputnikv_mandaue_facility_second = number_format($this->vcted_sputnikv_mandaue_facility_second / $this->eli_pop_mandaue_facility * 100,2); //PERCENT COVERAGE SPUTNIKV MANDAUE FACILITY
        $this->p_cvrge_sputnikv_lapu_facility_second = number_format($this->vcted_sputnikv_lapu_facility_second / $this->eli_pop_lapu_facility * 100,2); //PERCENT COVERAGE SPUTNIKV SECOND LAPU-LAPU FACILIY

        //PERCENT_COVERAGE_PFIZER_SECOND
        $this->p_cvrge_pfizer_bohol_second = number_format($this->vcted_pfizer_bohol_second / $this->eli_pop_bohol * 100,2); //PERCENT COVERAGE PFIZER SECOND BOHOL
        $this->p_cvrge_pfizer_cebu_second = number_format($this->vcted_pfizer_cebu_second / $this->eli_pop_cebu * 100,2); //PERCENT COVERAGE PFIZER SECOND CEBU
        $this->p_cvrge_pfizer_negros_second = number_format($this->vcted_pfizer_negros_second / $this->eli_pop_negros * 100,2); //PERCENT COVERAGE PFIZER SECOND NEGROS
        $this->p_cvrge_pfizer_siquijor_second = number_format($this->vcted_pfizer_siquijor_second / $this->eli_pop_siquijor * 100,2); //PERCENT COVERAGE PFIZER SECOND SIQUIJOR
        $this->p_cvrge_pfizer_cebu_facility_second = number_format($this->vcted_pfizer_cebu_facility_second / $this->eli_pop_cebu_facility * 100,2); //PERCENT COVERAGE PFIZER SECOND CEBU FACILITY
        $this->p_cvrge_pfizer_mandaue_facility_second = number_format($this->vcted_pfizer_mandaue_facility_second / $this->eli_pop_mandaue_facility * 100,2); //PERCENT COVERAGE PFIZER SECOND MANDAUE FACILITY
        $this->p_cvrge_pfizer_lapu_facility_second = number_format($this->vcted_pfizer_lapu_facility_second / $this->eli_pop_lapu_facility * 100,2); //PERCENT COVERAGE PFIZER SECOND LAPU-LAPU FACILITY

        //TOTAL_PERCENT_COVERAGE_SECOND
        $this->total_p_cvrge_bohol_second = number_format($this->total_vcted_second_bohol / $this->eli_pop_bohol * 100,2 ); //TOTAL PERCENT COVERAGE SECOND BOHOL
        $this->total_p_cvrge_cebu_second = number_format($this->total_vcted_second_cebu / $this->eli_pop_cebu * 100,2 ); //TOTAL PERCENT COVERAGE SECOND CEBU
        $this->total_p_cvrge_negros_second = number_format($this->total_vcted_second_negros / $this->eli_pop_negros * 100,2 );  //TOTAL PERCENT COVERAGE SECOND NEGROS
        $this->total_p_cvrge_siquijor_second =  number_format($this->total_vcted_first_siquijor / $this->eli_pop_siquijor * 100,2 ); //TOTAL PERCENT COVERAGE SECOND SIQUIJOR
        $this->total_p_cvrge_cebu_facility_second = number_format($this->total_vcted_cebu_facility_second / $this->eli_pop_cebu_facility * 100,2 );  //TOTAL PERCENT COVERAGE SECOND CEBU FACILITY
        $this->total_p_cvrge_mandaue_facility_second = number_format($this->total_vcted_mandaue_facility_second / $this->eli_pop_mandaue_facility * 100,2 ); //TOTAL PERCENT COVERAGE SECOND MANDAUE FACILITY
        $this->total_p_cvrge_lapu_facility_second = number_format($this->total_vcted_lapu_facility_second / $this->eli_pop_lapu_facility * 100,2 ); //TOTAL PERCENT COVERAGE SECOND LAPU-LAPU FACILITY

        //TOTAL_P_COVERAGE_REGION_FIRST
        $this->total_p_cvrge_sinovac_region_first = number_format($this->region_sinovac_first_dose / $this->total_elipop_region * 100,2); //TOTAL PERCENT COVERAGE SINOVAC FIRST REGION
        $this->total_p_cvrge_astra_region_first =  number_format($this->region_astra_first_dose / $this->total_elipop_region * 100,2); //TOTAL PERCENT COVERAGE ASTRA FIRST REGION
        $this->total_p_cvrge_sputnikv_region_first = number_format($this->region_sputnikv_first_dose / $this->total_elipop_region * 100,2); //TOTAL PERCENT COVERAGE SPUTNIKV FIRST REGION
        $this->total_p_cvrge_pfizer_region_first =  number_format($this->region_pfizer_first_dose / $this->total_elipop_region * 100,2); //TOTAL PERCENT COVERAGE PFIZER FIRST REGION
        $this->total_p_cvrge_region_first = number_format($this->first_dose_total / $this->total_elipop_region * 100,2) ; //TOTAL PERCENT COVERAGE FIRST REGION

        //TOTAL_P_COVERAGE_REGION_SECOND
        $this->total_p_cvrge_sinovac_region_second =  number_format($this->region_sinovac_second_dose / $this->total_elipop_region * 100,2);; //TOTAL PERCENT COVERAGE SINOVAC SECOND REGION
        $this->total_p_cvrge_astra_region_second = number_format($this->region_astra_second_dose / $this->total_elipop_region * 100,2); //TOTAL PERCENT COVERAGE ASTRA SECOND REGION
        $this->total_p_cvrge_sputnikv_region_second =  number_format($this->region_sputnikv_second_dose / $this->total_elipop_region * 100,2); //TOTAL PERCENT COVERAGE SPUTNIKV SECOND REGION
        $this->total_p_cvrge_pfizer_region_second = number_format($this->region_pfizer_second_dose / $this->total_elipop_region * 100,2); //TOTAL PERCENT COVERAGE PFIZER SECOND REGION
        $this->total_p_cvrge_region_second =number_format($this->second_dose_total / $this->total_elipop_region * 100,2) ; //TOTAL PERCENT COVERAGE SECOND REGION

        //TOTAL_WASTAGE
        $this->total_wastage_bohol = $this->wastage_sinovac_bohol_first + $this->wastage_astra_bohol_first + $this->wastage_sputnikv_bohol_first + $this->wastage_pfizer_bohol_first; //TOTAL WASTAGE FIRST BOHOL
        $this->total_wastage_cebu = $this->wastage_sinovac_cebu_first + $this->wastage_astra_cebu_first + $this->wastage_sputnikv_cebu_first + $this->wastage_pfizer_cebu_first; //TOTAL WASTAGE FIRST CEBU
        $this->total_wastage_negros = $this->wastage_sinovac_negros_first + $this->wastage_astra_negros_first + $this->wastage_sputnikv_negros_first + $this->wastage_pfizer_negros_first; //TOTAL WASTAGE FIRST NEGROS
        $this->total_wastage_siquijor = $this->wastage_sinovac_siquijor_first + $this->wastage_astra_siquijor_first + $this->wastage_sputnikv_siquijor_first + $this->wastage_pfizer_siquijor_first; //TOTAL WASTAGE FIRST SIQUIJOR
        $this->total_wastage_cebu_facility = $this->wastage_sinovac_cebu_facility_first + $this->wastage_astra_cebu_facility_first + $this->wastage_sputnikv_cebu_facility_first + $this->wastage_pfizer_cebu_facility_first; //TOTAL WASTAGE FIRST CEBU FACILITY
        $this->total_wastage_mandaue_facility = $this->wastage_sinovac_mandaue_facility_first + $this->wastage_astra_mandaue_facility_first + $this->wastage_sputnikv_mandaue_facility_first + $this->wastage_pfizer_mandaue_facility_first; //TOTAL WASTAGE FIRST MANDAUE FACILITY
        $this->total_wastage_lapu_facility = $this->wastage_sinovac_lapu_facility_first + $this->wastage_astra_lapu_facility_first + $this->wastage_sputnikv_lapu_facility_first + $this->wastage_pfizer_lapu_facility_first; //TOTAL WASTAGE FIRST LAPU-LAPU FACILITY

        //TOTAL REFUSAL
        $this->total_refusal_first = $this->refused_first_bohol + $this->refused_first_cebu + $this->refused_first_negros + $this->refused_first_siquijor + $this->refused_cebu_facility_first + $this->refused_mandaue_facility_first + $this->refused_lapu_facility_first ; //REFUSED FIRST REGION
        $this->total_refusal_second = $this->refused_second_bohol + $this->refused_second_cebu + $this->refused_second_negros + $this->refused_second_siquijor + $this->refused_cebu_facility_second + $this->refused_mandaue_facility_second + $this->refused_lapu_facility_second; //REFUSED SECOND REGION

        //TOTAL DEFFERED
        $this->total_deferred_first = $this->deferred_first_bohol + $this->deferred_first_cebu + $this->deferred_first_negros + $this->deferred_first_siquijor + $this->deferred_cebu_facility_first + $this->deferred_mandaue_facility_first + $this->deferred_lapu_facility_first; //DEFERRED FIRST REGION
        $this->total_deferred_second = $this->deferred_second_bohol + $this->deferred_second_cebu + $this->deferred_second_negros + $this->deferred_second_siquijor + $this->deferred_cebu_facility_second + $this->deferred_mandaue_facility_second + $this->deferred_lapu_facility_second ; //DEFERRED SECOND REGION

        //WASTAGE_SINOVAC_FIRST
        $this->wastage_sinovac_first = $this->wastage_sinovac_bohol_first + $this->wastage_sinovac_cebu_first + $this->wastage_sinovac_negros_first + $this->wastage_sinovac_siquijor_first + $this->wastage_sinovac_cebu_facility_first + $this->wastage_sinovac_mandaue_facility_first + $this->wastage_sinovac_lapu_facility_first; //WASTAGE SINOVAC REGION
        $this->wastage_astra_first = $this->wastage_astra_bohol_first + $this->wastage_astra_cebu_first + $this->wastage_astra_negros_first + $this->wastage_astra_siquijor_first + $this->wastage_astra_cebu_facility_first + $this->wastage_astra_mandaue_facility_first + $this->wastage_astra_lapu_facility_first ; //WASTAGE ASTRA  REGION
        $this->wastage_sputnikv_first = $this->wastage_sputnikv_bohol_first + $this->wastage_sputnikv_cebu_first + $this->wastage_sputnikv_negros_first + $this->wastage_sputnikv_siquijor_first + $this->wastage_sputnikv_cebu_facility_first + $this->wastage_sputnikv_mandaue_facility_first + $this->wastage_sputnikv_lapu_facility_first; //WASTAGE SPUTNIKV  REGION
        $this->wastage_pfizer_first = $this->wastage_pfizer_bohol_first + $this->wastage_pfizer_cebu_first + $this->wastage_pfizer_negros_first + $this->wastage_pfizer_siquijor_first + $this->wastage_pfizer_cebu_facility_first + $this->wastage_pfizer_mandaue_facility_first + $this->wastage_pfizer_lapu_facility_first; //WASTAGE PFIZER REGION

        //WASTAGE
        $this->wastage_region = $this->wastage_sinovac_first + $this->wastage_astra_first + $this->wastage_sputnikv_first + $this->wastage_pfizer_first; //TOTAL WASTAGE REGION

        //CONSUMPTION_RATE_SINOVAC_FIRST
        $this->c_rate_sinovac_bohol_first = number_format($this->vcted_sinovac_bohol_first / $this->sinovac_bohol * 100,2); //CONSUMPTION RATE SINOVAC FIRST BOHOL
        $this->c_rate_sinovac_cebu_first = number_format($this->vcted_sinovac_cebu_first / $this->sinovac_cebu * 100,2); //CONSUMPTION RATE SINOVAC FIRST CEBU
        $this->c_rate_sinovac_negros_first = number_format($this->vcted_sinovac_negros_first / $this->sinovac_negros * 100,2); //CONSUMPTION RATE SINOVAC FIRST NEGROS
        $this->c_rate_sinovac_siquijor_first = number_format($this->vcted_sinovac_siquijor_first / $this->sinovac_siquijor * 100,2); //CONSUMPTION RATE SINOVAC FIRST SIQUIJOR
        $this->c_rate_sinovac_cebu_facility_first = number_format($this->vcted_sinovac_cebu_facility_first / $this->sinovac_cebu_facility * 100,2); //CONSUMPTION RATE SINOVAC FIRST CEBU FACILITY
        $this->c_rate_sinovac_mandaue_facility_first = number_format($this->vcted_sinovac_mandaue_facility_first / $this->sinovac_mandaue_facility * 100,2); //CONSUMPTION RATE SINOVAC FIRST MANDAUE FACILITY
        $this->c_rate_sinovac_lapu_facility_first = number_format($this->vcted_sinovac_lapu_facility_first / $this->sinovac_lapu_facility * 100,2); //CONSUMPTION RATE SINOVAC FIRST LAPU-LAPU FACILITY

        //CONSUMPTION_RATE_ASTRAZENECA_FIRST
        $this->c_rate_astra_bohol_first = number_format($this->vcted_astra_bohol_first / $this->astra_bohol * 100,2); //CONSUMPTION RATE ASTRA FIRST BOHOL
        $this->c_rate_astra_cebu_first = number_format($this->vcted_astra_cebu_first / $this->astra_cebu * 100,2); //CONSUMPTION RATE ASTRA FIRST CEBU
        $this->c_rate_astra_negros_first = number_format($this->vcted_astra_negros_first / $this->astra_negros * 100,2); //CONSUMPTION RATE ASTRA FIRST NEGROS
        $this->c_rate_astra_siquijor_first = number_format($this->vcted_astra_siquijor_first / $this->astra_siquijor * 100,2); //CONSUMPTION RATE ASTRA FIRST SIQUIJOR
        $this->c_rate_astra_cebu_facility_first = number_format($this->vcted_astra_cebu_facility_first / $this->astra_cebu_facility * 100,2); //CONSUMPTION RATE ASTRA FIRST CEBU FACILITY
        $this->c_rate_astra_mandaue_facility_first = number_format($this->vcted_astra_mandaue_facility_first / $this->astra_mandaue_facility * 100,2); //CONSUMPTION RATE ASTRA FIRST MANDAUE FACILITY
        $this->c_rate_astra_lapu_facility_first = number_format($this->vcted_astra_lapu_facility_first / $this->astra_lapu_facility * 100,2); //CONSUMPTION RATE ASTRA FIRST LAPU-LAPU

        //CONSUMPTION_RATE_SPUTNIKV_FIRST
        $this->c_rate_sputnikv_bohol_first = number_format($this->vcted_sputnikv_bohol_first / $this->sputnikv_bohol * 100,2); //CONSUMPTION RATE SPUTNIKV FIRST BOHOL
        $this->c_rate_sputnikv_cebu_first = number_format($this->vcted_sputnikv_cebu_first / $this->sputnikv_cebu * 100,2); //CONSUMPTION RATE SPUTNIKV FIRST CEBU
        $this->c_rate_sputnikv_negros_first = number_format($this->vcted_sputnikv_negros_first / $this->sputnikv_negros * 100,2); //CONSUMPTION RATE SPUTNIKV FIRST NEGROS
        $this->c_rate_sputnikv_siquijor_first = number_format($this->vcted_sputnikv_siquijor_first / $this->sputnikv_siquijor * 100,2); //CONSUMPTION RATE SPUTNIKV FIRST SIQUIJOR
        $this->c_rate_sputnikv_cebu_facility_first = number_format($this->vcted_sputnikv_cebu_facility_first / $this->sputnikv_cebu_facility * 100,2); //CONSUMPTION RATE SPUTNIKV FIRST CEBU FACILITY
        $this->c_rate_sputnikv_mandaue_facility_first = number_format($this->vcted_sputnikv_mandaue_facility_first / $this->sputnikv_mandaue_facility * 100,2); //CONSUMPTION RATE SPUTNIKV FIRST MANDAUE FACILITY
        $this->c_rate_sputnikv_lapu_facility_first = number_format($this->vcted_sputnikv_lapu_facility_first / $this->sputnikv_lapu_facility * 100,2); //CONSUMPTION RATE SPUTNIKV FIRST LAPU-LAPU

        //CONSUMPTION_RATE_PFIZER_FIRST
        $this->c_rate_pfizer_bohol_first = number_format($this->vcted_pfizer_bohol_first / $this->pfizer_bohol * 100,2); //CONSUMPTION RATE PFIZER FIRST BOHOL
        $this->c_rate_pfizer_cebu_first = number_format($this->vcted_pfizer_cebu_first / $this->pfizer_cebu * 100,2); //CONSUMPTION RATE PFIZER FIRST CEBU
        $this->c_rate_pfizer_negros_first = number_format($this->vcted_pfizer_negros_first / $this->pfizer_negros * 100,2); //CONSUMPTION RATE PFIZER FIRST NEGROS
        $this->c_rate_pfizer_siquijor_first = number_format($this->vcted_pfizer_siquijor_first / $this->pfizer_siquijor * 100,2); //CONSUMPTION RATE PFIZER FIRST SIQUIJOR
        $this->c_rate_pfizer_cebu_facility_first = number_format($this->vcted_pfizer_cebu_facility_first / $this->pfizer_cebu_facility * 100,2); //CONSUMPTION RATE PFIZER FIRST CEBU FACILITY
        $this->c_rate_pfizer_mandaue_facility_first = number_format($this->vcted_pfizer_mandaue_facility_first / $this->pfizer_mandaue_facility * 100,2); //CONSUMPTION RATE PFIZER FIRST MANDAUE FACILITY
        $this->c_rate_pfizer_lapu_facility_first = number_format($this->vcted_pfizer_lapu_facility_first / $this->pfizer_lapu_facility * 100,2); //CONSUMPTION RATE PFIZER FIRST LAPU-LAPU

        //TOTAL_CONSUMPTION_RATE_FIRST
        $this->total_c_rate_bohol_first = number_format($this->total_vcted_first_bohol / $this->total_bohol * 100,2); //TOTAL CONSUMPTION RATE FIRST BOHOL
        $this->total_c_rate_cebu_first = number_format($this->total_vcted_first_cebu / $this->total_cebu * 100,2); //TOTAL CONSUMPTION RATE FIRST CEBU
        $this->total_c_rate_negros_first = number_format($this->total_vcted_first_negros / $this->total_negros * 100,2); //TOTAL CONSUMPTION RATE FIRST NEGROS
        $this->total_c_rate_siquijor_first = number_format($this->total_vcted_first_siquijor / $this->total_siquijor * 100,2); //TOTAL CONSUMPTION RATE FIRST SIQUIJOR
        $this->total_c_rate_cebu_facility_first = number_format($this->total_vcted_cebu_facility_first / $this->total_cebu_facility * 100,2); //TOTAL CONSUMPTION RATE FIRST CEBU FACILITY
        $this->total_c_rate_mandaue_facility_first = number_format($this->total_vcted_mandaue_facility_first / $this->total_mandaue_facility * 100,2); //TOTAL CONSUMPTION RATE FIRST MANDAUE FACILITY
        $this->total_c_rate_lapu_facility_first = number_format($this->total_vcted_lapu_facility_first / $this->total_lapu_facility * 100,2); //TOTAL CONSUMPTION RATE FIRST LAPU-LAPU FACILITY

        //CONSUMPTION_RATE_REGION_FIRST //DEARA
        $this->c_rate_region_sinovac_first = number_format($this->region_sinovac_first_dose / $this->sinovac_region * 100,2); //CONSUMPTION RATE SINOVAC FIRST REGION
        $this->c_rate_region_astra_first = number_format($this->region_astra_first_dose / $this->astra_region * 100,2); //CONSUMPTION RATE ASTRA FIRST REGION
        $this->c_rate_region_sputnikv_first = number_format($this->region_sputnikv_first_dose / $this->sputnikv_region * 100,2); //CONSUMPTION RATE SPUTNIKV FIRST REGION
        $this->c_rate_region_pfizer_first =number_format($this->region_pfizer_first_dose / $this->pfizer_region * 100,2); //CONSUMPTION RATE PFIZER FIRST REGION

        //TOTAL_C_RATE_REGION_FIRST
        $this->total_c_rate_region_first = number_format($this->first_dose_total / $this->total_region * 100,2 ); //TOTAL CONSUMPTION RATE FIRST REGION


        //CONSUMPTION_RATE_SINOVAC_SECOND
        $this->c_rate_sinovac_bohol_second = number_format($this->vcted_sinovac_bohol_second / $this->sinovac_bohol * 100,2); //CONSUMPTION RATE SINOVAC SECOND BOHOL
        $this->c_rate_sinovac_cebu_second = number_format($this->vcted_sinovac_cebu_second / $this->sinovac_cebu * 100,2); //CONSUMPTION RATE SINOVAC SECOND CEBU
        $this->c_rate_sinovac_negros_second = number_format($this->vcted_sinovac_negros_second / $this->sinovac_negros * 100,2); //CONSUMPTION RATE SINOVAC SECOND NEGROS
        $this->c_rate_sinovac_siquijor_second = number_format($this->vcted_sinovac_siquijor_second / $this->sinovac_siquijor * 100,2); //CONSUMPTION RATE SINOVAC SECOND SIQUIJOR
        $this->c_rate_sinovac_cebu_facility_second = number_format($this->vcted_sinovac_cebu_facility_second / $this->sinovac_cebu_facility * 100,2); //CONSUMPTION RATE SINOVAC SECOND CEBU FACILITY
        $this->c_rate_sinovac_mandaue_facility_second = number_format($this->vcted_sinovac_mandaue_facility_second / $this->sinovac_mandaue_facility * 100,2); //CONSUMPTION RATE SINOVAC SECOND MANDAUE FACILITY
        $this->c_rate_sinovac_lapu_facility_second = number_format($this->vcted_sinovac_lapu_facility_second / $this->sinovac_lapu_facility * 100,2); //CONSUMPTION RATE SINOVAC SECOND LAPU-LAPU

        //CONSUMPTION_RATE_ASTRAZENECA_SECOND
        $this->c_rate_astra_bohol_second = number_format($this->vcted_astra_bohol_second / $this->astra_bohol * 100,2); //CONSUMPTION RATE ASTRA SECOND BOHOL
        $this->c_rate_astra_cebu_second = number_format($this->vcted_astra_cebu_second / $this->astra_cebu * 100,2); //CONSUMPTION RATE ASTRA SECOND CEBU
        $this->c_rate_astra_negros_second = number_format($this->vcted_astra_negros_second / $this->astra_negros * 100,2); //CONSUMPTION RATE ASTRA SECOND NEGROS
        $this->c_rate_astra_siquijor_second = number_format($this->vcted_astra_siquijor_second / $this->astra_siquijor * 100,2); //CONSUMPTION RATE ASTRA SECOND SIQUIJOR
        $this->c_rate_astra_cebu_facility_second = number_format($this->vcted_astra_cebu_facility_second / $this->astra_cebu_facility * 100,2); //CONSUMPTION RATE ASTRA SECOND CEBU FACILITY
        $this->c_rate_astra_mandaue_facility_second = number_format($this->vcted_astra_mandaue_facility_second / $this->astra_mandaue_facility * 100,2); //CONSUMPTION RATE ASTRA SECOND MANDAUE FACILITY
        $this->c_rate_astra_lapu_facility_second = number_format($this->vcted_astra_lapu_facility_second / $this->astra_lapu_facility * 100,2); //CONSUMPTION RATE ASTRA SECOND LAPU-LAPU

        //CONSUMPTION_RATE_SPUTNIKV_SECOND
        $this->c_rate_sputnikv_bohol_second = number_format($this->vcted_sputnikv_bohol_second / $this->sputnikv_bohol * 100,2); //CONSUMPTION RATE SPUTNIKV SECOND BOHOL
        $this->c_rate_sputnikv_cebu_second = number_format($this->vcted_sputnikv_cebu_second / $this->sputnikv_cebu * 100,2); //CONSUMPTION RATE SPUTNIKV SECOND CEBU
        $this->c_rate_sputnikv_negros_second = number_format($this->vcted_sputnikv_negros_second / $this->sputnikv_negros * 100,2); //CONSUMPTION RATE SPUTNIKV SECOND NEGROS
        $this->c_rate_sputnikv_siquijor_second = number_format($this->vcted_sputnikv_siquijor_second / $this->sputnikv_siquijor * 100,2); //CONSUMPTION RATE SPUTNIKV SECOND SIQUIJOR
        $this->c_rate_sputnikv_cebu_facility_second = number_format($this->vcted_sputnikv_cebu_facility_second / $this->sputnikv_cebu_facility * 100,2); //CONSUMPTION RATE SPUTNIKV SECOND CEBU FACILITY
        $this->c_rate_sputnikv_mandaue_facility_second = number_format($this->vcted_sputnikv_mandaue_facility_second / $this->sputnikv_mandaue_facility * 100,2); //CONSUMPTION RATE SPUTNIKV SECOND MANDAUE FACILITY
        $this->c_rate_sputnikv_lapu_facility_second = number_format($this->vcted_sputnikv_lapu_facility_second / $this->sputnikv_mandaue_facility * 100,2); //CONSUMPTION RATE SPUTNIKV SECOND LAPU-LAPU FACILITY


        //CONSUMPTION_RATE_PFIZER_SECOND
        $this->c_rate_pfizer_bohol_second = number_format($this->vcted_pfizer_bohol_second / $this->pfizer_bohol * 100,2); //CONSUMPTION RATE PFIZER SECOND BOHOL
        $this->c_rate_pfizer_cebu_second = number_format($this->vcted_pfizer_cebu_second / $this->pfizer_cebu * 100,2); //CONSUMPTION RATE PFIZER SECOND CEBU
        $this->c_rate_pfizer_negros_second = number_format($this->vcted_pfizer_negros_second / $this->pfizer_negros * 100,2); //CONSUMPTION RATE PFIZER SECOND NEGROS
        $this->c_rate_pfizer_siquijor_second = number_format($this->vcted_pfizer_siquijor_second / $this->pfizer_siquijor * 100,2); //CONSUMPTION RATE PFIZER SECOND SIQUIJOR
        $this->c_rate_pfizer_cebu_facility_second = number_format($this->vcted_pfizer_cebu_facility_second / $this->pfizer_cebu_facility * 100,2); //CONSUMPTION RATE PFIZER SECOND CEBU FACILITY
        $this->c_rate_pfizer_mandaue_facility_second = number_format($this->vcted_pfizer_mandaue_facility_second / $this->pfizer_mandaue_facility * 100,2); //CONSUMPTION RATE PFIZER SECOND MANDAUE FACILITY
        $this->c_rate_pfizer_lapu_facility_second = number_format($this->vcted_pfizer_lapu_facility_second / $this->pfizer_mandaue_facility * 100,2); //CONSUMPTION RATE PFIZER SECOND LAPU-LAPU FACILITY


        //TOTAL_CONSUMPTION_RATE_SECOND
        $this->total_c_rate_bohol_second = number_format($this->total_vcted_second_bohol / $this->total_bohol * 100,2); //TOTAL CONSUMPTION RATE SECOND BOHOL
        $this->total_c_rate_cebu_second = number_format($this->total_vcted_second_cebu / $this->total_cebu * 100,2); //TOTAL CONSUMPTION RATE SECOND CEBU
        $this->total_c_rate_negros_second = number_format($this->total_vcted_second_negros / $this->total_negros * 100,2); //TOTAL CONSUMPTION RATE SECOND NEGROS
        $this->total_c_rate_siquijor_second = number_format($this->total_vcted_second_siquijor / $this->total_siquijor * 100,2); //TOTAL CONSUMPTION RATE SECOND SIQUIJOR
        $this->total_c_rate_cebu_facility_second = number_format($this->total_vcted_cebu_facility_second / $this->total_cebu_facility * 100,2);  //TOTAL CONSUMPTION RATE SECOND CEBU FACILITY
        $this->total_c_rate_mandaue_facility_second = number_format($this->total_vcted_mandaue_facility_second / $this->total_mandaue_facility * 100,2); //TOTAL CONSUMPTION RATE SECOND MANDAUE FACILITY
        $this->total_c_rate_lapu_facility_second =  number_format($this->total_vcted_lapu_facility_second / $this->total_lapu_facility * 100,2); //TOTAL CONSUMPTION RATE SECOND LAPU-LAPU FACI

        //CONSUMPTION_RATE_REGION_SECOND
        $this->c_rate_region_sinovac_second = number_format($this->region_sinovac_second_dose / $this->sinovac_region * 100,2);  //CONSUMPTION RATE SINOVAC SECOND REGION
        $this->c_rate_region_astra_second = number_format($this->region_astra_second_dose / $this->astra_region * 100,2); //CONSUMPTION RATE ASTRA SECOND REGION
        $this->c_rate_region_sputnikv_second = number_format($this->region_sputnikv_second_dose / $this->sputnikv_region * 100,2); //CONSUMPTION RATE SPUTNIKV SECOND REGION
        $this->c_rate_region_pfizer_second =  number_format($this->region_pfizer_second_dose / $this->pfizer_region * 100,2); //CONSUMPTION RATE PFIZER SECOND REGION
        //TOTAL_C_RATE_REGION_SECOND
        $this->total_c_rate_region_second = number_format($this->second_dose_total / $this->total_region * 100,2 ); //TOTAL CONSUMPTION RATE SECOND REGION


        //priority_set
        $this->priority_set = "(".strtoupper($priority).")";
    }

    public function vaccineSummaryReport()
    {
        $this->setPriority("a1");
        return view('vaccine.vaccine_summary_report',$this->tabValueDeclaration());
    }

    public function vaccineTab5Report()
    {
        $this->setPriority("a2");
        return view( 'vaccine.vaccine_summary_report',$this->tabValueDeclaration());
    }

    public function vaccineTab6Report()
    {
        $this->setPriority("a3");
        return view( 'vaccine.vaccine_summary_report',$this->tabValueDeclaration());
    }

    public function vaccineTab7Report()
    {
        $this->setPriority("a4");
        return view( 'vaccine.vaccine_summary_report',$this->tabValueDeclaration());
    }

    public function vaccineTab8Report()
    {
        $this->setPriority("a1");
        $total_elipop_region = $this->total_elipop_region;
        $eli_pop_bohol = $this->eli_pop_bohol;
        $eli_pop_cebu = $this->eli_pop_cebu;
        $eli_pop_negros = $this->eli_pop_negros;
        $eli_pop_siquijor = $this->eli_pop_siquijor;
        $eli_pop_cebu_facility = $this->eli_pop_cebu_facility;
        $eli_pop_mandaue_facility = $this->eli_pop_mandaue_facility;
        $eli_pop_lapu_facility = $this->eli_pop_lapu_facility;
        $region_sinovac_first_dose = $this->region_sinovac_first_dose;
        $region_sinovac_second_dose = $this->region_sinovac_second_dose;
        $region_astra_first_dose = $this->region_astra_first_dose;
        $region_astra_second_dose = $this->region_astra_second_dose;
        $region_sputnikv_first_dose = $this->region_sputnikv_first_dose;
        $region_sputnikv_second_dose = $this->region_sputnikv_second_dose;
        $region_pfizer_first_dose = $this->region_pfizer_first_dose;
        $region_pfizer_second_dose = $this->region_pfizer_second_dose;

        //VACCINATED SINOVAC A1
        $vcted_sinovac_bohol_first = $this->vcted_sinovac_bohol_first;
        $vcted_sinovac_bohol_second = $this->vcted_sinovac_bohol_second;
        $vcted_sinovac_cebu_first = $this->vcted_sinovac_cebu_first;
        $vcted_sinovac_cebu_second = $this->vcted_sinovac_cebu_second;
        $vcted_sinovac_negros_first = $this->vcted_sinovac_negros_first;
        $vcted_sinovac_negros_second = $this->vcted_sinovac_negros_second;
        $vcted_sinovac_siquijor_first = $this->vcted_sinovac_siquijor_first;
        $vcted_sinovac_siquijor_second = $this->vcted_sinovac_siquijor_second;
        $vcted_sinovac_cebu_facility_first = $this->vcted_sinovac_cebu_facility_first;
        $vcted_sinovac_cebu_facility_second = $this->vcted_sinovac_cebu_facility_second;
        $vcted_sinovac_mandaue_facility_first = $this->vcted_sinovac_mandaue_facility_first;
        $vcted_sinovac_mandaue_facility_second = $this->vcted_sinovac_mandaue_facility_second;
        $vcted_sinovac_lapu_facility_first = $this->vcted_sinovac_lapu_facility_first;
        $vcted_sinovac_lapu_facility_second = $this->vcted_sinovac_lapu_facility_second;

        //VACCINATED ASTRA A1
        $vcted_astra_bohol_first = $this->vcted_astra_bohol_first;
        $vcted_astra_bohol_second = $this->vcted_astra_bohol_second;
        $vcted_astra_cebu_first = $this->vcted_astra_cebu_first;
        $vcted_astra_cebu_second = $this->vcted_astra_cebu_second;
        $vcted_astra_negros_first = $this->vcted_astra_negros_first;
        $vcted_astra_negros_second = $this->vcted_astra_negros_second;
        $vcted_astra_siquijor_first = $this->vcted_astra_siquijor_first;
        $vcted_astra_siquijor_second = $this->vcted_astra_siquijor_second;
        $vcted_astra_cebu_facility_first = $this->vcted_astra_cebu_facility_first;
        $vcted_astra_cebu_facility_second = $this->vcted_astra_cebu_facility_second;
        $vcted_astra_mandaue_facility_first = $this->vcted_astra_mandaue_facility_first;
        $vcted_astra_mandaue_facility_second = $this->vcted_astra_mandaue_facility_second;
        $vcted_astra_lapu_facility_first = $this->vcted_astra_lapu_facility_first;
        $vcted_astra_lapu_facility_second = $this->vcted_astra_lapu_facility_second;

        //VACCINATED SPUTNIKV A1
        $vcted_sputnikv_bohol_first = $this->vcted_sputnikv_bohol_first;
        $vcted_sputnikv_bohol_second = $this->vcted_sputnikv_bohol_second;
        $vcted_sputnikv_cebu_first = $this->vcted_sputnikv_cebu_first;
        $vcted_sputnikv_cebu_second = $this->vcted_sputnikv_cebu_second;
        $vcted_sputnikv_negros_first = $this->vcted_sputnikv_negros_first;
        $vcted_sputnikv_negros_second = $this->vcted_sputnikv_negros_second;
        $vcted_sputnikv_siquijor_first = $this->vcted_sputnikv_siquijor_first;
        $vcted_sputnikv_siquijor_second = $this->vcted_sputnikv_siquijor_second;
        $vcted_sputnikv_cebu_facility_first = $this->vcted_sputnikv_cebu_facility_first;
        $vcted_sputnikv_cebu_facility_second = $this->vcted_sputnikv_cebu_facility_second;
        $vcted_sputnikv_mandaue_facility_first = $this->vcted_sputnikv_mandaue_facility_first;
        $vcted_sputnikv_mandaue_facility_second = $this->vcted_sputnikv_mandaue_facility_second;
        $vcted_sputnikv_lapu_facility_first = $this->vcted_sputnikv_lapu_facility_first;
        $vcted_sputnikv_lapu_facility_second = $this->vcted_sputnikv_lapu_facility_second;

        //VACCINATED PFIZER A1
        $vcted_pfizer_bohol_first = $this->vcted_pfizer_bohol_first;
        $vcted_pfizer_bohol_second = $this->vcted_pfizer_bohol_second;
        $vcted_pfizer_cebu_first = $this->vcted_pfizer_cebu_first;
        $vcted_pfizer_cebu_second = $this->vcted_pfizer_cebu_second;
        $vcted_pfizer_negros_first = $this->vcted_pfizer_negros_first;
        $vcted_pfizer_negros_second = $this->vcted_pfizer_negros_second;
        $vcted_pfizer_siquijor_first = $this->vcted_pfizer_siquijor_first;
        $vcted_pfizer_siquijor_second = $this->vcted_pfizer_siquijor_second;
        $vcted_pfizer_cebu_facility_first = $this->vcted_pfizer_cebu_facility_first;
        $vcted_pfizer_cebu_facility_second = $this->vcted_pfizer_cebu_facility_second;
        $vcted_pfizer_mandaue_facility_first = $this->vcted_pfizer_mandaue_facility_first;
        $vcted_pfizer_mandaue_facility_second = $this->vcted_pfizer_mandaue_facility_second;
        $vcted_pfizer_lapu_facility_first = $this->vcted_pfizer_lapu_facility_first;
        $vcted_pfizer_lapu_facility_second = $this->vcted_pfizer_lapu_facility_second;

        //TOTAL REFUSED FIRST A1
        $total_refusal_first = $this->total_refusal_first;
        $refused_first_bohol = $this->refused_first_bohol;
        $refused_first_cebu = $this->refused_first_cebu;
        $refused_first_negros = $this->refused_first_negros;
        $refused_first_siquijor = $this->refused_first_siquijor;
        $refused_cebu_facility_first = $this->refused_cebu_facility_first;
        $refused_mandaue_facility_first = $this->refused_mandaue_facility_first;
        $refused_lapu_facility_first = $this->refused_lapu_facility_first;

        //TOTAL REFUSED SECOND A1
        $total_refusal_second = $this->total_refusal_second;
        $refused_second_bohol = $this->refused_second_bohol;
        $refused_second_cebu = $this->refused_second_cebu;
        $refused_second_negros = $this->refused_second_negros;
        $refused_second_siquijor = $this->refused_second_siquijor;
        $refused_cebu_facility_second = $this->refused_cebu_facility_second;
        $refused_mandaue_facility_second = $this->refused_mandaue_facility_second;
        $refused_lapu_facility_second = $this->refused_lapu_facility_second;

        //TOTAL DEFERRED FIRST A1
        $total_deferred_first = $this->total_deferred_first;
        $deferred_first_bohol = $this->deferred_first_bohol;
        $deferred_first_cebu = $this->deferred_first_cebu;
        $deferred_first_negros = $this->deferred_first_negros;
        $deferred_first_siquijor = $this->deferred_first_siquijor;
        $deferred_cebu_facility_first = $this->deferred_cebu_facility_first;
        $deferred_mandaue_facility_first = $this->deferred_mandaue_facility_first;
        $deferred_lapu_facility_first = $this->deferred_lapu_facility_first;

        //TOTAL DEFERRED SECOND A1
        $total_deferred_second = $this->total_deferred_second;
        $deferred_second_bohol = $this->deferred_second_bohol;
        $deferred_second_cebu = $this->deferred_second_cebu;
        $deferred_second_negros = $this->deferred_second_negros;
        $deferred_second_siquijor = $this->deferred_second_siquijor;
        $deferred_cebu_facility_second = $this->deferred_cebu_facility_second;
        $deferred_mandaue_facility_second = $this->deferred_mandaue_facility_second;
        $deferred_lapu_facility_second = $this->deferred_lapu_facility_second;

        //TOTAL WASTAGE
        $wastage_sinovac_first = $this->wastage_sinovac_first;
        $wastage_astra_first = $this->wastage_astra_first;
        $wastage_sputnikv_first = $this->wastage_sputnikv_first;
        $wastage_pfizer_first = $this->wastage_pfizer_first;

        //WASTAGE SINOVAC A1
        $wastage_sinovac_bohol_first = $this->wastage_sinovac_bohol_first;
        $wastage_sinovac_cebu_first = $this->wastage_sinovac_cebu_first;
        $wastage_sinovac_negros_first = $this->wastage_sinovac_negros_first;
        $wastage_sinovac_siquijor_first = $this->wastage_sinovac_siquijor_first;
        $wastage_sinovac_cebu_facility_first = $this->wastage_sinovac_cebu_facility_first;
        $wastage_sinovac_mandaue_facility_first = $this->wastage_sinovac_mandaue_facility_first;
        $wastage_sinovac_lapu_facility_first = $this->wastage_sinovac_lapu_facility_first;

        //WASTAGE ASTRA A1
        $wastage_astra_bohol_first = $this->wastage_astra_bohol_first;
        $wastage_astra_cebu_first = $this->wastage_astra_cebu_first;
        $wastage_astra_negros_first = $this->wastage_astra_negros_first;
        $wastage_astra_siquijor_first = $this->wastage_astra_siquijor_first;
        $wastage_astra_cebu_facility_first = $this->wastage_astra_cebu_facility_first;
        $wastage_astra_mandaue_facility_first = $this->wastage_astra_mandaue_facility_first;
        $wastage_astra_lapu_facility_first = $this->wastage_astra_lapu_facility_first;

        //WASTAGE SPUTNIKV A1
        $wastage_sputnikv_bohol_first = $this->wastage_sputnikv_bohol_first;
        $wastage_sputnikv_cebu_first = $this->wastage_sputnikv_cebu_first;
        $wastage_sputnikv_negros_first = $this->wastage_sputnikv_negros_first;
        $wastage_sputnikv_siquijor_first = $this->wastage_sputnikv_siquijor_first;
        $wastage_sputnikv_cebu_facility_first = $this->wastage_sputnikv_cebu_facility_first;
        $wastage_sputnikv_mandaue_facility_first = $this->wastage_sputnikv_mandaue_facility_first;
        $wastage_sputnikv_lapu_facility_first = $this->wastage_sputnikv_lapu_facility_first;

        //WASTAGE PFIZER A1
        $wastage_pfizer_bohol_first = $this->wastage_pfizer_bohol_first;
        $wastage_pfizer_cebu_first = $this->wastage_pfizer_cebu_first;
        $wastage_pfizer_negros_first = $this->wastage_pfizer_negros_first;
        $wastage_pfizer_siquijor_first = $this->wastage_pfizer_siquijor_first;
        $wastage_pfizer_cebu_facility_first = $this->wastage_pfizer_cebu_facility_first;
        $wastage_pfizer_mandaue_facility_first = $this->wastage_pfizer_mandaue_facility_first;
        $wastage_pfizer_lapu_facility_first = $this->wastage_pfizer_lapu_facility_first;


        $this->setPriority("a2");
        $total_elipop_region += $this->total_elipop_region;
        $eli_pop_bohol += $this->eli_pop_bohol;
        $eli_pop_cebu += $this->eli_pop_cebu;
        $eli_pop_negros += $this->eli_pop_negros;
        $eli_pop_siquijor += $this->eli_pop_siquijor;
        $eli_pop_cebu_facility += $this->eli_pop_cebu_facility;
        $eli_pop_mandaue_facility += $this->eli_pop_mandaue_facility;
        $eli_pop_lapu_facility += $this->eli_pop_lapu_facility;
        $region_sinovac_first_dose += $this->region_sinovac_first_dose;
        $region_sinovac_second_dose += $this->region_sinovac_second_dose;
        $region_astra_first_dose += $this->region_astra_first_dose;
        $region_astra_second_dose += $this->region_astra_second_dose;
        $region_sputnikv_first_dose += $this->region_sputnikv_first_dose;
        $region_sputnikv_second_dose += $this->region_sputnikv_second_dose;
        $region_pfizer_first_dose += $this->region_pfizer_first_dose;
        $region_pfizer_second_dose += $this->region_pfizer_second_dose;

        //VACCINATED SINOVAC A2
        $vcted_sinovac_bohol_first += $this->vcted_sinovac_bohol_first;
        $vcted_sinovac_bohol_second += $this->vcted_sinovac_bohol_second;
        $vcted_sinovac_cebu_first += $this->vcted_sinovac_cebu_first;
        $vcted_sinovac_cebu_second += $this->vcted_sinovac_cebu_second;
        $vcted_sinovac_negros_first += $this->vcted_sinovac_negros_first;
        $vcted_sinovac_negros_second += $this->vcted_sinovac_negros_second;
        $vcted_sinovac_siquijor_first += $this->vcted_sinovac_siquijor_first;
        $vcted_sinovac_siquijor_second += $this->vcted_sinovac_siquijor_second;
        $vcted_sinovac_cebu_facility_first += $this->vcted_sinovac_cebu_facility_first;
        $vcted_sinovac_cebu_facility_second += $this->vcted_sinovac_cebu_facility_second;
        $vcted_sinovac_mandaue_facility_first += $this->vcted_sinovac_mandaue_facility_first;
        $vcted_sinovac_mandaue_facility_second += $this->vcted_sinovac_mandaue_facility_second;
        $vcted_sinovac_lapu_facility_first += $this->vcted_sinovac_lapu_facility_first;
        $vcted_sinovac_lapu_facility_second += $this->vcted_sinovac_lapu_facility_second;

        //VACCINATED ASTRA A2
        $vcted_astra_bohol_first += $this->vcted_astra_bohol_first;
        $vcted_astra_bohol_second += $this->vcted_astra_bohol_second;
        $vcted_astra_cebu_first += $this->vcted_astra_cebu_first;
        $vcted_astra_cebu_second += $this->vcted_astra_cebu_second;
        $vcted_astra_negros_first += $this->vcted_astra_negros_first;
        $vcted_astra_negros_second += $this->vcted_astra_negros_second;
        $vcted_astra_siquijor_first += $this->vcted_astra_siquijor_first;
        $vcted_astra_siquijor_second += $this->vcted_astra_siquijor_second;
        $vcted_astra_cebu_facility_first += $this->vcted_astra_cebu_facility_first;
        $vcted_astra_cebu_facility_second += $this->vcted_astra_cebu_facility_second;
        $vcted_astra_mandaue_facility_first += $this->vcted_astra_mandaue_facility_first;
        $vcted_astra_mandaue_facility_second += $this->vcted_astra_mandaue_facility_second;
        $vcted_astra_lapu_facility_first += $this->vcted_astra_lapu_facility_first;
        $vcted_astra_lapu_facility_second += $this->vcted_astra_lapu_facility_second;

        //VACCINATED SPUTNIKV A2
        $vcted_sputnikv_bohol_first += $this->vcted_sputnikv_bohol_first;
        $vcted_sputnikv_bohol_second += $this->vcted_sputnikv_bohol_second;
        $vcted_sputnikv_cebu_first += $this->vcted_sputnikv_cebu_first;
        $vcted_sputnikv_cebu_second += $this->vcted_sputnikv_cebu_second;
        $vcted_sputnikv_negros_first += $this->vcted_sputnikv_negros_first;
        $vcted_sputnikv_negros_second += $this->vcted_sputnikv_negros_second;
        $vcted_sputnikv_siquijor_first += $this->vcted_sputnikv_siquijor_first;
        $vcted_sputnikv_siquijor_second += $this->vcted_sputnikv_siquijor_second;
        $vcted_sputnikv_cebu_facility_first += $this->vcted_sputnikv_cebu_facility_first;
        $vcted_sputnikv_cebu_facility_second += $this->vcted_sputnikv_cebu_facility_second;
        $vcted_sputnikv_mandaue_facility_first += $this->vcted_sputnikv_mandaue_facility_first;
        $vcted_sputnikv_mandaue_facility_second += $this->vcted_sputnikv_mandaue_facility_second;
        $vcted_sputnikv_lapu_facility_first += $this->vcted_sputnikv_lapu_facility_first;
        $vcted_sputnikv_lapu_facility_second += $this->vcted_sputnikv_lapu_facility_second;

        //VACCINATED PFIZER A2
        $vcted_pfizer_bohol_first += $this->vcted_pfizer_bohol_first;
        $vcted_pfizer_bohol_second += $this->vcted_pfizer_bohol_second;
        $vcted_pfizer_cebu_first += $this->vcted_pfizer_cebu_first;
        $vcted_pfizer_cebu_second += $this->vcted_pfizer_cebu_second;
        $vcted_pfizer_negros_first += $this->vcted_pfizer_negros_first;
        $vcted_pfizer_negros_second += $this->vcted_pfizer_negros_second;
        $vcted_pfizer_siquijor_first += $this->vcted_pfizer_siquijor_first;
        $vcted_pfizer_siquijor_second += $this->vcted_pfizer_siquijor_second;
        $vcted_pfizer_cebu_facility_first += $this->vcted_pfizer_cebu_facility_first;
        $vcted_pfizer_cebu_facility_second += $this->vcted_pfizer_cebu_facility_second;
        $vcted_pfizer_mandaue_facility_first += $this->vcted_pfizer_mandaue_facility_first;
        $vcted_pfizer_mandaue_facility_second += $this->vcted_pfizer_mandaue_facility_second;
        $vcted_pfizer_lapu_facility_first += $this->vcted_pfizer_lapu_facility_first;
        $vcted_pfizer_lapu_facility_second += $this->vcted_pfizer_lapu_facility_second;

        //TOTAL REFUSED FIRST A2
        $total_refusal_first += $this->total_refusal_first;
        $refused_first_bohol += $this->refused_first_bohol;
        $refused_first_cebu += $this->refused_first_cebu;
        $refused_first_negros += $this->refused_first_negros;
        $refused_first_siquijor += $this->refused_first_siquijor;
        $refused_cebu_facility_first += $this->refused_cebu_facility_first;
        $refused_mandaue_facility_first += $this->refused_mandaue_facility_first;
        $refused_lapu_facility_first += $this->refused_lapu_facility_first;

        //TOTAL REFUSED SECOND A2
        $total_refusal_second += $this->total_refusal_second;
        $refused_second_bohol += $this->refused_second_bohol;
        $refused_second_cebu += $this->refused_second_cebu;
        $refused_second_negros += $this->refused_second_negros;
        $refused_second_siquijor += $this->refused_second_siquijor;
        $refused_cebu_facility_second += $this->refused_cebu_facility_second;
        $refused_mandaue_facility_second += $this->refused_cebu_facility_second;
        $refused_lapu_facility_second += $this->refused_cebu_facility_second;

        //TOTAL DEFERRED FIRST A2
        $total_deferred_first += $this->total_deferred_first;
        $deferred_first_bohol += $this->deferred_first_bohol;
        $deferred_first_cebu += $this->deferred_first_cebu;
        $deferred_first_negros += $this->deferred_first_negros;
        $deferred_first_siquijor += $this->deferred_first_siquijor;
        $deferred_cebu_facility_first += $this->deferred_cebu_facility_first;
        $deferred_mandaue_facility_first += $this->deferred_mandaue_facility_first;
        $deferred_lapu_facility_first += $this->deferred_lapu_facility_first;

        //TOTAL DEFERRED SECOND A2
        $total_deferred_second += $this->total_deferred_second;
        $deferred_second_bohol += $this->deferred_second_bohol;
        $deferred_second_cebu += $this->deferred_second_cebu;
        $deferred_second_negros += $this->deferred_second_negros;
        $deferred_second_siquijor += $this->deferred_second_siquijor;
        $deferred_cebu_facility_second += $this->deferred_cebu_facility_second;
        $deferred_mandaue_facility_second += $this->deferred_mandaue_facility_second;
        $deferred_lapu_facility_second += $this->deferred_lapu_facility_second;

        //TOTAL WASTAGE A2
        $wastage_sinovac_first += $this->wastage_sinovac_first;
        $wastage_astra_first += $this->wastage_astra_first;
        $wastage_sputnikv_first += $this->wastage_sputnikv_first;
        $wastage_pfizer_first += $this->wastage_pfizer_first;

        //WASTAGE SINOVAC A2
        $wastage_sinovac_bohol_first += $this->wastage_sinovac_bohol_first;
        $wastage_sinovac_cebu_first += $this->wastage_sinovac_cebu_first;
        $wastage_sinovac_negros_first += $this->wastage_sinovac_negros_first;
        $wastage_sinovac_siquijor_first += $this->wastage_sinovac_siquijor_first;
        $wastage_sinovac_cebu_facility_first += $this->wastage_sinovac_cebu_facility_first;
        $wastage_sinovac_mandaue_facility_first += $this->wastage_sinovac_mandaue_facility_first;
        $wastage_sinovac_lapu_facility_first += $this->wastage_sinovac_lapu_facility_first;

        //WASTAGE ASTRA A2
        $wastage_astra_bohol_first += $this->wastage_astra_bohol_first;
        $wastage_astra_cebu_first += $this->wastage_astra_cebu_first;
        $wastage_astra_negros_first += $this->wastage_astra_negros_first;
        $wastage_astra_siquijor_first += $this->wastage_astra_siquijor_first;
        $wastage_astra_cebu_facility_first += $this->wastage_astra_cebu_facility_first;
        $wastage_astra_mandaue_facility_first += $this->wastage_astra_mandaue_facility_first;
        $wastage_astra_lapu_facility_first += $this->wastage_astra_lapu_facility_first;

        //WASTAGE SPUTNIKV A2
        $wastage_sputnikv_bohol_first += $this->wastage_sputnikv_bohol_first;
        $wastage_sputnikv_cebu_first += $this->wastage_sputnikv_cebu_first;
        $wastage_sputnikv_negros_first += $this->wastage_sputnikv_negros_first;
        $wastage_sputnikv_siquijor_first += $this->wastage_sputnikv_siquijor_first;
        $wastage_sputnikv_cebu_facility_first += $this->wastage_sputnikv_cebu_facility_first;
        $wastage_sputnikv_mandaue_facility_first += $this->wastage_sputnikv_mandaue_facility_first;
        $wastage_sputnikv_lapu_facility_first += $this->wastage_sputnikv_lapu_facility_first;

        //WASTAGE PFIZER A2
        $wastage_pfizer_bohol_first += $this->wastage_pfizer_bohol_first;
        $wastage_pfizer_cebu_first += $this->wastage_pfizer_cebu_first;
        $wastage_pfizer_negros_first += $this->wastage_pfizer_negros_first;
        $wastage_pfizer_siquijor_first += $this->wastage_pfizer_siquijor_first;
        $wastage_pfizer_cebu_facility_first += $this->wastage_pfizer_cebu_facility_first;
        $wastage_pfizer_mandaue_facility_first += $this->wastage_pfizer_mandaue_facility_first;
        $wastage_pfizer_lapu_facility_first += $this->wastage_pfizer_lapu_facility_first;


        $this->setPriority("a3");
        $total_elipop_region += $this->total_elipop_region;
        $eli_pop_bohol += $this->eli_pop_bohol;
        $eli_pop_cebu += $this->eli_pop_cebu;
        $eli_pop_negros += $this->eli_pop_negros;
        $eli_pop_siquijor += $this->eli_pop_siquijor;
        $eli_pop_cebu_facility += $this->eli_pop_cebu_facility;
        $eli_pop_mandaue_facility += $this->eli_pop_mandaue_facility;
        $eli_pop_lapu_facility += $this->eli_pop_lapu_facility;
        $region_sinovac_first_dose += $this->region_sinovac_first_dose;
        $region_sinovac_second_dose += $this->region_sinovac_second_dose;
        $region_astra_first_dose += $this->region_astra_first_dose;
        $region_astra_second_dose += $this->region_astra_second_dose;
        $region_sputnikv_first_dose += $this->region_sputnikv_first_dose;
        $region_sputnikv_second_dose += $this->region_sputnikv_second_dose;
        $region_pfizer_first_dose += $this->region_pfizer_first_dose;
        $region_pfizer_second_dose += $this->region_pfizer_second_dose;

        //VACCINATED SINOVAC A3
        $vcted_sinovac_bohol_first += $this->vcted_sinovac_bohol_first;
        $vcted_sinovac_bohol_second += $this->vcted_sinovac_bohol_second;
        $vcted_sinovac_cebu_first += $this->vcted_sinovac_cebu_first;
        $vcted_sinovac_cebu_second += $this->vcted_sinovac_cebu_second;
        $vcted_sinovac_negros_first += $this->vcted_sinovac_negros_first;
        $vcted_sinovac_negros_second += $this->vcted_sinovac_negros_second;
        $vcted_sinovac_siquijor_first += $this->vcted_sinovac_siquijor_first;
        $vcted_sinovac_siquijor_second += $this->vcted_sinovac_siquijor_second;
        $vcted_sinovac_cebu_facility_first += $this->vcted_sinovac_cebu_facility_first;
        $vcted_sinovac_cebu_facility_second += $this->vcted_sinovac_cebu_facility_second;
        $vcted_sinovac_mandaue_facility_first += $this->vcted_sinovac_mandaue_facility_first;
        $vcted_sinovac_mandaue_facility_second += $this->vcted_sinovac_mandaue_facility_second;
        $vcted_sinovac_lapu_facility_first += $this->vcted_sinovac_lapu_facility_first;
        $vcted_sinovac_lapu_facility_second += $this->vcted_sinovac_lapu_facility_second;

        //VACCINATED ASTRA A3
        $vcted_astra_bohol_first += $this->vcted_astra_bohol_first;
        $vcted_astra_bohol_second += $this->vcted_astra_bohol_second;
        $vcted_astra_cebu_first += $this->vcted_astra_cebu_first;
        $vcted_astra_cebu_second += $this->vcted_astra_cebu_second;
        $vcted_astra_negros_first += $this->vcted_astra_negros_first;
        $vcted_astra_negros_second += $this->vcted_astra_negros_second;
        $vcted_astra_siquijor_first += $this->vcted_astra_siquijor_first;
        $vcted_astra_siquijor_second += $this->vcted_astra_siquijor_second;
        $vcted_astra_cebu_facility_first += $this->vcted_astra_cebu_facility_first;
        $vcted_astra_cebu_facility_second += $this->vcted_astra_cebu_facility_second;
        $vcted_astra_mandaue_facility_first += $this->vcted_astra_mandaue_facility_first;
        $vcted_astra_mandaue_facility_second += $this->vcted_astra_mandaue_facility_second;
        $vcted_astra_lapu_facility_first += $this->vcted_astra_lapu_facility_first;
        $vcted_astra_lapu_facility_second += $this->vcted_astra_lapu_facility_second;

        //VACCINATED SPUTNIKV A3
        $vcted_sputnikv_bohol_first += $this->vcted_sputnikv_bohol_first;
        $vcted_sputnikv_bohol_second += $this->vcted_sputnikv_bohol_second;
        $vcted_sputnikv_cebu_first += $this->vcted_sputnikv_cebu_first;
        $vcted_sputnikv_cebu_second += $this->vcted_sputnikv_cebu_second;
        $vcted_sputnikv_negros_first += $this->vcted_sputnikv_negros_first;
        $vcted_sputnikv_negros_second += $this->vcted_sputnikv_negros_second;
        $vcted_sputnikv_siquijor_first += $this->vcted_sputnikv_siquijor_first;
        $vcted_sputnikv_siquijor_second += $this->vcted_sputnikv_siquijor_second;
        $vcted_sputnikv_cebu_facility_first += $this->vcted_sputnikv_cebu_facility_first;
        $vcted_sputnikv_cebu_facility_second += $this->vcted_sputnikv_cebu_facility_second;
        $vcted_sputnikv_mandaue_facility_first += $this->vcted_sputnikv_mandaue_facility_first;
        $vcted_sputnikv_mandaue_facility_second += $this->vcted_sputnikv_mandaue_facility_second;
        $vcted_sputnikv_lapu_facility_first += $this->vcted_sputnikv_lapu_facility_first;
        $vcted_sputnikv_lapu_facility_second += $this->vcted_sputnikv_lapu_facility_second;

        //VACCINATED PFIZER A3
        $vcted_pfizer_bohol_first += $this->vcted_pfizer_bohol_first;
        $vcted_pfizer_bohol_second += $this->vcted_pfizer_bohol_second;
        $vcted_pfizer_cebu_first += $this->vcted_pfizer_cebu_first;
        $vcted_pfizer_cebu_second += $this->vcted_pfizer_cebu_second;
        $vcted_pfizer_negros_first += $this->vcted_pfizer_negros_first;
        $vcted_pfizer_negros_second += $this->vcted_pfizer_negros_second;
        $vcted_pfizer_siquijor_first += $this->vcted_pfizer_siquijor_first;
        $vcted_pfizer_siquijor_second += $this->vcted_pfizer_siquijor_second;
        $vcted_pfizer_cebu_facility_first += $this->vcted_pfizer_cebu_facility_first;
        $vcted_pfizer_cebu_facility_second += $this->vcted_pfizer_cebu_facility_second;
        $vcted_pfizer_mandaue_facility_first += $this->vcted_pfizer_mandaue_facility_first;
        $vcted_pfizer_mandaue_facility_second += $this->vcted_pfizer_mandaue_facility_second;
        $vcted_pfizer_lapu_facility_first += $this->vcted_pfizer_lapu_facility_first;
        $vcted_pfizer_lapu_facility_second += $this->vcted_pfizer_lapu_facility_second;

        //TOTAL REFUSED SECOND A3
        $total_refusal_second += $this->total_refusal_second;
        $refused_second_bohol += $this->refused_second_bohol;
        $refused_second_cebu += $this->refused_second_cebu;
        $refused_second_negros += $this->refused_second_negros;
        $refused_second_siquijor += $this->refused_second_siquijor;
        $refused_cebu_facility_second += $this->refused_cebu_facility_second;
        $refused_mandaue_facility_second += $this->refused_mandaue_facility_second;
        $refused_lapu_facility_second += $this->refused_lapu_facility_second;

        //TOTAL DEFERRED FIRST A3
        $total_deferred_first += $this->total_deferred_first;
        $deferred_first_bohol += $this->deferred_first_bohol;
        $deferred_first_cebu += $this->deferred_first_cebu;
        $deferred_first_negros += $this->deferred_first_negros;
        $deferred_first_siquijor += $this->deferred_first_siquijor;
        $deferred_cebu_facility_first += $this->deferred_cebu_facility_first;
        $deferred_mandaue_facility_first += $this->deferred_mandaue_facility_first;
        $deferred_lapu_facility_first += $this->deferred_lapu_facility_first;

        //TOTAL DEFERRED SECOND A3
        $total_deferred_second += $this->total_deferred_second;
        $deferred_second_bohol += $this->deferred_second_bohol;
        $deferred_second_cebu += $this->deferred_second_cebu;
        $deferred_second_negros += $this->deferred_second_negros;
        $deferred_second_siquijor += $this->deferred_second_siquijor;
        $deferred_cebu_facility_second += $this->deferred_cebu_facility_second;
        $deferred_mandaue_facility_second += $this->deferred_mandaue_facility_second;
        $deferred_lapu_facility_second += $this->deferred_lapu_facility_second;

        //TOTAL WASTAGE A3
        $wastage_sinovac_first += $this->wastage_sinovac_first;
        $wastage_astra_first += $this->wastage_astra_first;
        $wastage_sputnikv_first += $this->wastage_sputnikv_first;
        $wastage_pfizer_first += $this->wastage_pfizer_first;

        //WASTAGE SINOVAC A3
        $wastage_sinovac_bohol_first += $this->wastage_sinovac_bohol_first;
        $wastage_sinovac_cebu_first += $this->wastage_sinovac_cebu_first;
        $wastage_sinovac_negros_first += $this->wastage_sinovac_negros_first;
        $wastage_sinovac_siquijor_first += $this->wastage_sinovac_siquijor_first;
        $wastage_sinovac_cebu_facility_first += $this->wastage_sinovac_cebu_facility_first;
        $wastage_sinovac_mandaue_facility_first += $this->wastage_sinovac_mandaue_facility_first;
        $wastage_sinovac_lapu_facility_first += $this->wastage_sinovac_lapu_facility_first;

        //WASTAGE ASTRA A3
        $wastage_astra_bohol_first += $this->wastage_astra_bohol_first;
        $wastage_astra_cebu_first += $this->wastage_astra_cebu_first;
        $wastage_astra_negros_first += $this->wastage_astra_negros_first;
        $wastage_astra_siquijor_first += $this->wastage_astra_siquijor_first;
        $wastage_astra_cebu_facility_first += $this->wastage_astra_cebu_facility_first;
        $wastage_astra_mandaue_facility_first += $this->wastage_astra_mandaue_facility_first;
        $wastage_astra_lapu_facility_first += $this->wastage_astra_lapu_facility_first;

        //WASTAGE SPUTNIKV A3
        $wastage_sputnikv_bohol_first += $this->wastage_sputnikv_bohol_first;
        $wastage_sputnikv_cebu_first += $this->wastage_sputnikv_cebu_first;
        $wastage_sputnikv_negros_first += $this->wastage_sputnikv_negros_first;
        $wastage_sputnikv_siquijor_first += $this->wastage_sputnikv_siquijor_first;
        $wastage_sputnikv_cebu_facility_first += $this->wastage_sputnikv_cebu_facility_first;
        $wastage_sputnikv_mandaue_facility_first += $this->wastage_sputnikv_mandaue_facility_first;
        $wastage_sputnikv_lapu_facility_first += $this->wastage_sputnikv_lapu_facility_first;

        //WASTAGE PFIZER A3
        $wastage_pfizer_bohol_first += $this->wastage_pfizer_bohol_first;
        $wastage_pfizer_cebu_first += $this->wastage_pfizer_cebu_first;
        $wastage_pfizer_negros_first += $this->wastage_pfizer_negros_first;
        $wastage_pfizer_siquijor_first += $this->wastage_pfizer_siquijor_first;
        $wastage_pfizer_cebu_facility_first += $this->wastage_pfizer_cebu_facility_first;
        $wastage_pfizer_mandaue_facility_first += $this->wastage_pfizer_mandaue_facility_first;
        $wastage_pfizer_lapu_facility_first += $this->wastage_pfizer_lapu_facility_first;

        $this->setPriority("a4");
        $total_elipop_region += $this->total_elipop_region;
        $eli_pop_cebu += $this->eli_pop_cebu;
        $eli_pop_bohol += $this->eli_pop_bohol;
        $eli_pop_negros += $this->eli_pop_negros;
        $eli_pop_siquijor += $this->eli_pop_siquijor;
        $eli_pop_cebu_facility += $this->eli_pop_cebu_facility;
        $eli_pop_mandaue_facility += $this->eli_pop_mandaue_facility;
        $eli_pop_lapu_facility += $this->eli_pop_lapu_facility;
        $region_sinovac_first_dose += $this->region_sinovac_first_dose;
        $region_sinovac_second_dose += $this->region_sinovac_second_dose;
        $region_astra_first_dose += $this->region_astra_first_dose;
        $region_astra_second_dose += $this->region_astra_second_dose;
        $region_sputnikv_first_dose += $this->region_sputnikv_first_dose;
        $region_sputnikv_second_dose += $this->region_sputnikv_second_dose;
        $region_pfizer_first_dose += $this->region_pfizer_first_dose;
        $region_pfizer_second_dose += $this->region_pfizer_second_dose;

        //VACCINATED SINOVAC A4
        $vcted_sinovac_bohol_first += $this->vcted_sinovac_bohol_first;
        $vcted_sinovac_bohol_second += $this->vcted_sinovac_bohol_second;
        $vcted_sinovac_cebu_first += $this->vcted_sinovac_cebu_first;
        $vcted_sinovac_cebu_second += $this->vcted_sinovac_cebu_second;
        $vcted_sinovac_negros_first += $this->vcted_sinovac_negros_first;
        $vcted_sinovac_negros_second += $this->vcted_sinovac_negros_second;
        $vcted_sinovac_siquijor_first += $this->vcted_sinovac_siquijor_first;
        $vcted_sinovac_siquijor_second += $this->vcted_sinovac_siquijor_second;
        $vcted_sinovac_cebu_facility_first += $this->vcted_sinovac_cebu_facility_first;
        $vcted_sinovac_cebu_facility_second += $this->vcted_sinovac_cebu_facility_second;
        $vcted_sinovac_mandaue_facility_first += $this->vcted_sinovac_mandaue_facility_first;
        $vcted_sinovac_mandaue_facility_second += $this->vcted_sinovac_mandaue_facility_second;
        $vcted_sinovac_lapu_facility_first += $this->vcted_sinovac_lapu_facility_first;
        $vcted_sinovac_lapu_facility_second += $this->vcted_sinovac_lapu_facility_second;

        //VACCINATED ASTRA A4
        $vcted_astra_bohol_first += $this->vcted_astra_bohol_first;
        $vcted_astra_bohol_second += $this->vcted_astra_bohol_second;
        $vcted_astra_cebu_first += $this->vcted_astra_cebu_first;
        $vcted_astra_cebu_second += $this->vcted_astra_cebu_second;
        $vcted_astra_negros_first += $this->vcted_astra_negros_first;
        $vcted_astra_negros_second += $this->vcted_astra_negros_second;
        $vcted_astra_siquijor_first += $this->vcted_astra_siquijor_first;
        $vcted_astra_siquijor_second += $this->vcted_astra_siquijor_second;
        $vcted_astra_cebu_facility_first += $this->vcted_astra_cebu_facility_first;
        $vcted_astra_cebu_facility_second += $this->vcted_astra_cebu_facility_second;
        $vcted_astra_mandaue_facility_first += $this->vcted_astra_mandaue_facility_first;
        $vcted_astra_mandaue_facility_second += $this->vcted_astra_mandaue_facility_second;
        $vcted_astra_lapu_facility_first += $this->vcted_astra_lapu_facility_first;
        $vcted_astra_lapu_facility_second += $this->vcted_astra_lapu_facility_second;

        //VACCINATED SPUTNIKV A4
        $vcted_sputnikv_bohol_first += $this->vcted_sputnikv_bohol_first;
        $vcted_sputnikv_bohol_second += $this->vcted_sputnikv_bohol_second;
        $vcted_sputnikv_cebu_first += $this->vcted_sputnikv_cebu_first;
        $vcted_sputnikv_cebu_second += $this->vcted_sputnikv_cebu_second;
        $vcted_sputnikv_negros_first += $this->vcted_sputnikv_negros_first;
        $vcted_sputnikv_negros_second += $this->vcted_sputnikv_negros_second;
        $vcted_sputnikv_siquijor_first += $this->vcted_sputnikv_siquijor_first;
        $vcted_sputnikv_siquijor_second += $this->vcted_sputnikv_siquijor_second;
        $vcted_sputnikv_cebu_facility_first += $this->vcted_sputnikv_cebu_facility_first;
        $vcted_sputnikv_cebu_facility_second += $this->vcted_sputnikv_cebu_facility_second;
        $vcted_sputnikv_mandaue_facility_first += $this->vcted_sputnikv_mandaue_facility_first;
        $vcted_sputnikv_mandaue_facility_second += $this->vcted_sputnikv_mandaue_facility_second;
        $vcted_sputnikv_lapu_facility_first += $this->vcted_sputnikv_lapu_facility_first;
        $vcted_sputnikv_lapu_facility_second += $this->vcted_sputnikv_lapu_facility_second;

        //VACCINATED PFIZER A4
        $vcted_pfizer_bohol_first += $this->vcted_pfizer_bohol_first;
        $vcted_pfizer_bohol_second += $this->vcted_pfizer_bohol_second;
        $vcted_pfizer_cebu_first += $this->vcted_pfizer_cebu_first;
        $vcted_pfizer_cebu_second += $this->vcted_pfizer_cebu_second;
        $vcted_pfizer_negros_first += $this->vcted_pfizer_negros_first;
        $vcted_pfizer_negros_second += $this->vcted_pfizer_negros_second;
        $vcted_pfizer_siquijor_first += $this->vcted_pfizer_siquijor_first;
        $vcted_pfizer_siquijor_second += $this->vcted_pfizer_siquijor_second;
        $vcted_pfizer_cebu_facility_first += $this->vcted_pfizer_cebu_facility_first;
        $vcted_pfizer_cebu_facility_second += $this->vcted_pfizer_cebu_facility_second;
        $vcted_pfizer_mandaue_facility_first += $this->vcted_pfizer_mandaue_facility_first;
        $vcted_pfizer_mandaue_facility_second += $this->vcted_pfizer_mandaue_facility_second;
        $vcted_pfizer_lapu_facility_first += $this->vcted_pfizer_lapu_facility_first;
        $vcted_pfizer_lapu_facility_second += $this->vcted_pfizer_lapu_facility_second;


        //TOTAL REFUSED FIRST A4
        $total_refusal_first += $this->total_refusal_first;
        $refused_first_bohol += $this->refused_first_bohol;
        $refused_first_cebu += $this->refused_first_cebu;
        $refused_first_negros += $this->refused_first_negros;
        $refused_first_siquijor += $this->refused_first_siquijor;
        $refused_cebu_facility_first += $this->refused_cebu_facility_first;
        $refused_mandaue_facility_first += $this->refused_mandaue_facility_first;
        $refused_lapu_facility_first += $this->refused_lapu_facility_first;

        //TOTAL REFUSED SECOND A4
        $total_refusal_second += $this->total_refusal_second;
        $refused_second_bohol += $this->refused_second_bohol;
        $refused_second_cebu += $this->refused_second_cebu;
        $refused_second_negros += $this->refused_second_negros;
        $refused_second_siquijor += $this->refused_second_siquijor;
        $refused_cebu_facility_second += $this->refused_cebu_facility_second;
        $refused_mandaue_facility_second += $this->refused_mandaue_facility_second;
        $refused_lapu_facility_second += $this->refused_lapu_facility_second;

        //TOTAL DEFERRED FIRST A4
        $total_deferred_first += $this->total_deferred_first;
        $deferred_first_bohol += $this->deferred_first_bohol;
        $deferred_first_cebu += $this->deferred_first_cebu;
        $deferred_first_negros += $this->deferred_first_negros;
        $deferred_first_siquijor += $this->deferred_first_siquijor;
        $deferred_cebu_facility_first += $this->deferred_cebu_facility_first;
        $deferred_mandaue_facility_first += $this->deferred_mandaue_facility_first;
        $deferred_lapu_facility_first += $this->deferred_lapu_facility_first;

        //TOTAL DEFERRED SECOND A4
        $total_deferred_second += $this->total_deferred_second;
        $deferred_second_bohol += $this->deferred_second_bohol;
        $deferred_second_cebu += $this->deferred_second_cebu;
        $deferred_second_negros += $this->deferred_second_negros;
        $deferred_second_siquijor += $this->deferred_second_siquijor;
        $deferred_cebu_facility_second += $this->deferred_cebu_facility_second;
        $deferred_mandaue_facility_second += $this->deferred_mandaue_facility_second;
        $deferred_lapu_facility_second += $this->deferred_lapu_facility_second;

        //TOTAL WASTAGE A4
        $wastage_sinovac_first += $this->wastage_sinovac_first;
        $wastage_astra_first += $this->wastage_astra_first;
        $wastage_sputnikv_first += $this->wastage_sputnikv_first;
        $wastage_pfizer_first += $this->wastage_pfizer_first;

        //WASTAGE SINOVAC A4
        $wastage_sinovac_bohol_first += $this->wastage_sinovac_bohol_first;
        $wastage_sinovac_cebu_first += $this->wastage_sinovac_cebu_first;
        $wastage_sinovac_negros_first += $this->wastage_sinovac_negros_first;
        $wastage_sinovac_siquijor_first += $this->wastage_sinovac_siquijor_first;
        $wastage_sinovac_cebu_facility_first += $this->wastage_sinovac_cebu_facility_first;
        $wastage_sinovac_mandaue_facility_first += $this->wastage_sinovac_mandaue_facility_first;
        $wastage_sinovac_lapu_facility_first += $this->wastage_sinovac_lapu_facility_first;

        //WASTAGE ASTRA A4
        $wastage_astra_bohol_first += $this->wastage_astra_bohol_first;
        $wastage_astra_cebu_first += $this->wastage_astra_cebu_first;
        $wastage_astra_negros_first += $this->wastage_astra_negros_first;
        $wastage_astra_siquijor_first += $this->wastage_astra_siquijor_first;
        $wastage_astra_cebu_facility_first += $this->wastage_astra_cebu_facility_first;
        $wastage_astra_mandaue_facility_first += $this->wastage_astra_mandaue_facility_first;
        $wastage_astra_lapu_facility_first += $this->wastage_astra_lapu_facility_first;

        //WASTAGE SPUTNIKV A4
        $wastage_sputnikv_bohol_first += $this->wastage_sputnikv_bohol_first;
        $wastage_sputnikv_cebu_first += $this->wastage_sputnikv_cebu_first;
        $wastage_sputnikv_negros_first += $this->wastage_sputnikv_negros_first;
        $wastage_sputnikv_siquijor_first += $this->wastage_sputnikv_siquijor_first;
        $wastage_sputnikv_cebu_facility_first += $this->wastage_sputnikv_cebu_facility_first;
        $wastage_sputnikv_mandaue_facility_first += $this->wastage_sputnikv_mandaue_facility_first;
        $wastage_sputnikv_lapu_facility_first += $this->wastage_sputnikv_lapu_facility_first;

        //WASTAGE PFIZER A4
        $wastage_pfizer_bohol_first += $this->wastage_pfizer_bohol_first;
        $wastage_pfizer_cebu_first += $this->wastage_pfizer_cebu_first;
        $wastage_pfizer_negros_first += $this->wastage_pfizer_negros_first;
        $wastage_pfizer_siquijor_first += $this->wastage_pfizer_siquijor_first;
        $wastage_pfizer_cebu_facility_first += $this->wastage_pfizer_cebu_facility_first;
        $wastage_pfizer_mandaue_facility_first += $this->wastage_pfizer_mandaue_facility_first;
        $wastage_pfizer_lapu_facility_first += $this->wastage_pfizer_lapu_facility_first;

        //GRAND OVERALL
        $this->total_elipop_region = $total_elipop_region;
        $this->eli_pop_bohol = $eli_pop_bohol;
        $this->eli_pop_cebu = $eli_pop_cebu;
        $this->eli_pop_negros = $eli_pop_negros;
        $this->eli_pop_siquijor = $eli_pop_siquijor;
        $this->eli_pop_cebu_facility = $eli_pop_cebu_facility;
        $this->eli_pop_mandaue_facility = $eli_pop_mandaue_facility;
        $this->eli_pop_lapu_facility = $eli_pop_lapu_facility;
        $this->region_sinovac_first_dose = $region_sinovac_first_dose;
        $this->region_sinovac_second_dose = $region_sinovac_second_dose;
        $this->region_astra_first_dose = $region_astra_first_dose;
        $this->region_astra_second_dose = $region_astra_second_dose;
        $this->region_sputnikv_first_dose = $region_sputnikv_first_dose;
        $this->region_sputnikv_second_dose = $region_sputnikv_second_dose;
        $this->region_pfizer_first_dose = $region_pfizer_first_dose;
        $this->region_pfizer_second_dose = $region_pfizer_second_dose;

        //GRAND OVERALL VACCINATED SINOVAC
        $this->vcted_sinovac_bohol_first = $vcted_sinovac_bohol_first;
        $this->vcted_sinovac_bohol_second = $vcted_sinovac_bohol_second;
        $this->vcted_sinovac_cebu_first = $vcted_sinovac_cebu_first;
        $this->vcted_sinovac_cebu_second = $vcted_sinovac_cebu_second;
        $this->vcted_sinovac_negros_first = $vcted_sinovac_negros_first;
        $this->vcted_sinovac_negros_second = $vcted_sinovac_negros_second;
        $this->vcted_sinovac_siquijor_first = $vcted_sinovac_siquijor_first;
        $this->vcted_sinovac_siquijor_second = $vcted_sinovac_siquijor_second;
        $this->vcted_sinovac_cebu_facility_first = $vcted_sinovac_cebu_facility_first;
        $this->vcted_sinovac_cebu_facility_second = $vcted_sinovac_cebu_facility_second;
        $this->vcted_sinovac_mandaue_facility_first = $vcted_sinovac_mandaue_facility_first;
        $this->vcted_sinovac_mandaue_facility_second = $vcted_sinovac_mandaue_facility_second;
        $this->vcted_sinovac_lapu_facility_first = $vcted_sinovac_lapu_facility_first;
        $this->vcted_sinovac_lapu_facility_second = $vcted_sinovac_lapu_facility_second;

        //GRAND OVERALL VACCINATED ASTRA
        $this->vcted_astra_bohol_first = $vcted_astra_bohol_first;
        $this->vcted_astra_bohol_second = $vcted_astra_bohol_second;
        $this->vcted_astra_cebu_first = $vcted_astra_cebu_first;
        $this->vcted_astra_cebu_second = $vcted_astra_cebu_second;
        $this->vcted_astra_negros_first = $vcted_astra_negros_first;
        $this->vcted_astra_negros_second = $vcted_astra_negros_second;
        $this->vcted_astra_siquijor_first = $vcted_astra_siquijor_first;
        $this->vcted_astra_siquijor_second = $vcted_astra_siquijor_second;
        $this->vcted_astra_cebu_facility_first = $vcted_astra_cebu_facility_first;
        $this->vcted_astra_cebu_facility_second = $vcted_astra_cebu_facility_second;
        $this->vcted_astra_mandaue_facility_first = $vcted_astra_mandaue_facility_first;
        $this->vcted_astra_mandaue_facility_second = $vcted_astra_mandaue_facility_second;
        $this->vcted_astra_lapu_facility_first = $vcted_astra_lapu_facility_first;
        $this->vcted_astra_lapu_facility_second = $vcted_astra_lapu_facility_second;

        //GRAND OVERALL VACCINATED SPUTNIKV
        $this->vcted_sputnikv_bohol_first = $vcted_sputnikv_bohol_first;
        $this->vcted_sputnikv_bohol_second = $vcted_sputnikv_bohol_second;
        $this->vcted_sputnikv_cebu_first = $vcted_sputnikv_cebu_first;
        $this->vcted_sputnikv_cebu_second = $vcted_sputnikv_cebu_second;
        $this->vcted_sputnikv_negros_first = $vcted_sputnikv_negros_first;
        $this->vcted_sputnikv_negros_second = $vcted_sputnikv_negros_second;
        $this->vcted_sputnikv_siquijor_first = $vcted_sputnikv_siquijor_first;
        $this->vcted_sputnikv_siquijor_second = $vcted_sputnikv_siquijor_second;
        $this->vcted_sputnikv_cebu_facility_first = $vcted_sputnikv_cebu_facility_first;
        $this->vcted_sputnikv_cebu_facility_second = $vcted_sputnikv_cebu_facility_second;
        $this->vcted_sputnikv_mandaue_facility_first = $vcted_sputnikv_mandaue_facility_first;
        $this->vcted_sputnikv_mandaue_facility_second = $vcted_sputnikv_mandaue_facility_second;
        $this->vcted_sputnikv_lapu_facility_first = $vcted_sputnikv_lapu_facility_first;
        $this->vcted_sputnikv_lapu_facility_second = $vcted_sputnikv_lapu_facility_second;

        //GRAND OVERALL VACCINATED PFIZER
        $this->vcted_pfizer_bohol_first = $vcted_pfizer_bohol_first;
        $this->vcted_pfizer_bohol_second = $vcted_pfizer_bohol_second;
        $this->vcted_pfizer_cebu_first = $vcted_pfizer_cebu_first;
        $this->vcted_pfizer_cebu_second = $vcted_pfizer_cebu_second;
        $this->vcted_pfizer_negros_first = $vcted_pfizer_negros_first;
        $this->vcted_pfizer_negros_second = $vcted_pfizer_negros_second;
        $this->vcted_pfizer_siquijor_first = $vcted_pfizer_siquijor_first;
        $this->vcted_pfizer_siquijor_second = $vcted_pfizer_siquijor_second;
        $this->vcted_pfizer_cebu_facility_first = $vcted_pfizer_cebu_facility_first;
        $this->vcted_pfizer_cebu_facility_second = $vcted_pfizer_cebu_facility_second;
        $this->vcted_pfizer_mandaue_facility_first = $vcted_pfizer_mandaue_facility_first;
        $this->vcted_pfizer_mandaue_facility_second = $vcted_pfizer_mandaue_facility_second;
        $this->vcted_pfizer_lapu_facility_first = $vcted_pfizer_lapu_facility_first;
        $this->vcted_pfizer_lapu_facility_second = $vcted_pfizer_lapu_facility_second;

        //PERCENT COVERAGE SINOVAC FIRST
        $this->total_p_cvrge_sinovac_region_first = number_format($this->region_sinovac_first_dose / $total_elipop_region * 100,2);
        $this->p_cvrge_sinovac_bohol_first = number_format($this->vcted_sinovac_bohol_first / $eli_pop_bohol * 100,2);
        $this->p_cvrge_sinovac_cebu_first = number_format($this->vcted_sinovac_cebu_first / $eli_pop_cebu * 100,2);
        $this->p_cvrge_sinovac_negros_first = number_format($this->vcted_sinovac_negros_first / $eli_pop_negros * 100,2);
        $this->p_cvrge_sinovac_siquijor_first = number_format($this->vcted_sinovac_siquijor_first / $eli_pop_siquijor * 100,2);
        $this->p_cvrge_sinovac_cebu_facility_first = number_format($this->vcted_sinovac_cebu_facility_first / $eli_pop_cebu_facility * 100,2);
        $this->p_cvrge_sinovac_mandaue_facility_first =  number_format($this->vcted_sinovac_mandaue_facility_first / $eli_pop_mandaue_facility * 100,2);
        $this->p_cvrge_sinovac_lapu_facility_first =  number_format($this->vcted_sinovac_lapu_facility_first / $eli_pop_lapu_facility * 100,2);

        //PERCENT COVERAGE ASTRA FIRST
        $this->total_p_cvrge_astra_region_first = number_format($this->region_astra_first_dose / $total_elipop_region * 100,2);
        $this->p_cvrge_astra_bohol_first =   number_format($this->vcted_astra_bohol_first / $eli_pop_bohol * 100,2);
        $this->p_cvrge_astra_cebu_first = number_format($this->vcted_astra_cebu_first / $eli_pop_cebu * 100,2);
        $this->p_cvrge_astra_negros_first = number_format($this->vcted_astra_negros_first / $eli_pop_negros * 100,2);
        $this->p_cvrge_astra_siquijor_first = number_format($this->vcted_astra_siquijor_first / $eli_pop_siquijor * 100,2);
        $this->p_cvrge_astra_cebu_facility_first = number_format($this->vcted_astra_cebu_facility_first / $eli_pop_cebu_facility * 100,2);
        $this->p_cvrge_astra_mandaue_facility_first = number_format($this->vcted_astra_mandaue_facility_first / $eli_pop_mandaue_facility * 100,2);
        $this->p_cvrge_astra_lapu_facility_first = number_format($this->vcted_astra_lapu_facility_first / $eli_pop_lapu_facility * 100,2);

        //PERCENT COVERAGE SPUTNIKV FIRST
        $this->total_p_cvrge_sputnikv_region_first = number_format($this->region_sputnikv_first_dose / $total_elipop_region * 100,2);
        $this->p_cvrge_sputnikv_bohol_first =  number_format($this->vcted_sputnikv_bohol_first / $eli_pop_bohol * 100,2);
        $this->p_cvrge_sputnikv_cebu_first = number_format($this->vcted_sputnikv_cebu_first / $eli_pop_cebu * 100,2);
        $this->p_cvrge_sputnikv_negros_first = number_format($this->vcted_sputnikv_negros_first / $eli_pop_negros * 100,2);
        $this->p_cvrge_sputnikv_siquijor_first = number_format($this->vcted_sputnikv_siquijor_first / $eli_pop_siquijor * 100,2);
        $this->p_cvrge_sputnikv_cebu_facility_first =  number_format($this->vcted_sputnikv_cebu_facility_first / $eli_pop_cebu_facility * 100,2);
        $this->p_cvrge_sputnikv_mandaue_facility_first = number_format($this->vcted_sputnikv_mandaue_facility_first / $eli_pop_mandaue_facility * 100,2);
        $this->p_cvrge_sputnikv_lapu_facility_first = number_format($this->vcted_sputnikv_lapu_facility_first / $eli_pop_lapu_facility * 100,2);

        //OVERALL PERCENT COVERAGE PFIZER FIRST
        $this->total_p_cvrge_pfizer_region_first = number_format($this->region_pfizer_first_dose / $total_elipop_region * 100,2);
        $this->p_cvrge_pfizer_bohol_first =  number_format($this->vcted_pfizer_bohol_first / $eli_pop_bohol * 100,2);
        $this->p_cvrge_pfizer_cebu_first = number_format($this->vcted_pfizer_cebu_first / $eli_pop_cebu * 100,2);
        $this->p_cvrge_pfizer_negros_first = number_format($this->vcted_pfizer_negros_first / $eli_pop_negros * 100,2);
        $this->p_cvrge_pfizer_siquijor_first = number_format($this->vcted_pfizer_siquijor_first / $eli_pop_siquijor * 100,2);
        $this->p_cvrge_pfizer_cebu_facility_first =  number_format($this->vcted_pfizer_cebu_facility_first / $eli_pop_cebu_facility * 100,2);
        $this->p_cvrge_pfizer_mandaue_facility_first = number_format($this->vcted_pfizer_mandaue_facility_first / $eli_pop_mandaue_facility * 100,2);
        $this->p_cvrge_pfizer_lapu_facility_first = number_format($this->vcted_pfizer_lapu_facility_first / $eli_pop_lapu_facility * 100,2);

        //GRAND OVERALL PERCENT COVERAGE SINOVAC SECOND
        $this->total_p_cvrge_sinovac_region_second = number_format($this->region_sinovac_second_dose / $total_elipop_region * 100,2);
        $this->p_cvrge_sinovac_bohol_second = number_format($this->vcted_sinovac_bohol_second / $eli_pop_bohol * 100,2);
        $this->p_cvrge_sinovac_cebu_second = number_format($this->vcted_sinovac_cebu_second / $eli_pop_cebu * 100,2);
        $this->p_cvrge_sinovac_negros_second = number_format($this->vcted_sinovac_negros_second / $eli_pop_negros * 100,2);
        $this->p_cvrge_sinovac_siquijor_second = number_format($this->vcted_sinovac_siquijor_second / $eli_pop_siquijor * 100,2);
        $this->p_cvrge_sinovac_cebu_facility_second = number_format($this->vcted_sinovac_cebu_facility_second / $eli_pop_cebu_facility * 100,2);
        $this->p_cvrge_sinovac_mandaue_facility_second =  number_format($this->vcted_sinovac_mandaue_facility_second / $eli_pop_mandaue_facility * 100,2);
        $this->p_cvrge_sinovac_lapu_facility_second =  number_format($this->vcted_sinovac_lapu_facility_second / $eli_pop_lapu_facility * 100,2);

        //GRAND OVERALL PERCENT COVERAGE ASTRA SECOND
        $this->total_p_cvrge_astra_region_second = number_format($this->region_astra_second_dose / $total_elipop_region * 100,2);
        $this->p_cvrge_astra_bohol_second = number_format($this->vcted_astra_bohol_second / $eli_pop_bohol * 100,2);
        $this->p_cvrge_astra_cebu_second = number_format($this->vcted_astra_cebu_second / $eli_pop_cebu * 100,2);
        $this->p_cvrge_astra_negros_second = number_format($this->vcted_astra_negros_second / $eli_pop_negros * 100,2);
        $this->p_cvrge_astra_siquijor_second = number_format($this->vcted_astra_siquijor_second / $eli_pop_siquijor * 100,2);
        $this->p_cvrge_astra_cebu_facility_second = number_format($this->vcted_astra_cebu_facility_second / $eli_pop_cebu_facility * 100,2);
        $this->p_cvrge_astra_mandaue_facility_second =  number_format($this->vcted_astra_mandaue_facility_second / $eli_pop_mandaue_facility * 100,2);
        $this->p_cvrge_astra_lapu_facility_second =  number_format($this->vcted_astra_lapu_facility_second / $eli_pop_lapu_facility * 100,2);

        //GRAND OVERALL PERCENT COVERAGE SPUTNIKV SECOND
        $this->total_p_cvrge_sputnikv_region_second = number_format($this->region_sputnikv_second_dose / $total_elipop_region * 100,2);
        $this->p_cvrge_sputnikv_bohol_second = number_format($this->vcted_sputnikv_bohol_second / $eli_pop_bohol * 100,2);
        $this->p_cvrge_sputnikv_cebu_second = number_format($this->vcted_sputnikv_cebu_second / $eli_pop_cebu * 100,2);
        $this->p_cvrge_sputnikv_negros_second = number_format($this->vcted_sputnikv_negros_second / $eli_pop_negros * 100,2);
        $this->p_cvrge_sputnikv_siquijor_second = number_format($this->vcted_sputnikv_siquijor_second / $eli_pop_siquijor * 100,2);
        $this->p_cvrge_sputnikv_cebu_facility_second = number_format($this->vcted_sputnikv_cebu_facility_second / $eli_pop_cebu_facility * 100,2);
        $this->p_cvrge_sputnikv_mandaue_facility_second =  number_format($this->vcted_sputnikv_mandaue_facility_second / $eli_pop_mandaue_facility * 100,2);
        $this->p_cvrge_sputnikv_lapu_facility_second =  number_format($this->vcted_sputnikv_lapu_facility_second / $eli_pop_lapu_facility * 100,2);

        //GRAND OVERALL PERCENT COVERAGE PFIZER SECOND
        $this->total_p_cvrge_pfizer_region_second = number_format($this->region_pfizer_second_dose / $total_elipop_region * 100,2);
        $this->p_cvrge_pfizer_bohol_second = number_format($this->vcted_pfizer_bohol_second / $eli_pop_bohol * 100,2);
        $this->p_cvrge_pfizer_cebu_second = number_format($this->vcted_pfizer_cebu_second / $eli_pop_cebu * 100,2);
        $this->p_cvrge_pfizer_negros_second = number_format($this->vcted_pfizer_negros_second / $eli_pop_negros * 100,2);
        $this->p_cvrge_pfizer_siquijor_second = number_format($this->vcted_pfizer_siquijor_second / $eli_pop_siquijor * 100,2);
        $this->p_cvrge_pfizer_cebu_facility_second = number_format($this->vcted_pfizer_cebu_facility_second / $eli_pop_cebu_facility * 100,2);
        $this->p_cvrge_pfizer_mandaue_facility_second =  number_format($this->vcted_pfizer_mandaue_facility_second / $eli_pop_mandaue_facility * 100,2);
        $this->p_cvrge_pfizer_lapu_facility_second =  number_format($this->vcted_pfizer_lapu_facility_second / $eli_pop_lapu_facility * 100,2);

        //GRAND OVER ALL TOTAL REFUSED FIRST
        $this->total_refusal_first = $total_refusal_first;
        $this->refused_first_bohol = $refused_first_bohol;
        $this->refused_first_cebu = $refused_first_cebu;
        $this->refused_first_negros = $refused_first_negros;
        $this->refused_first_siquijor = $refused_first_siquijor;
        $this->refused_cebu_facility_first = $refused_cebu_facility_first;
        $this->refused_mandaue_facility_first = $refused_mandaue_facility_first;
        $this->refused_lapu_facility_first = $refused_lapu_facility_first;

        //GRAND OVER ALL TOTAL REFUSED SECOND
        $this->total_refusal_second = $total_refusal_second;
        $this->refused_second_bohol = $refused_second_bohol;
        $this->refused_second_cebu = $refused_second_cebu;
        $this->refused_second_negros = $refused_second_negros;
        $this->refused_second_siquijor = $refused_second_siquijor;
        $this->refused_cebu_facility_second = $refused_cebu_facility_second;
        $this->refused_mandaue_facility_second = $refused_mandaue_facility_second;
        $this->refused_lapu_facility_second = $refused_lapu_facility_second;

        //GRAND OVER ALL TOTAL DEFERRED FIRST
        $this->total_deferred_first = $total_deferred_first;
        $this->deferred_first_bohol = $deferred_first_bohol;
        $this->deferred_first_cebu = $deferred_first_cebu;
        $this->deferred_first_negros = $deferred_first_negros;
        $this->deferred_first_siquijor = $deferred_first_siquijor;
        $this->deferred_cebu_facility_first = $deferred_cebu_facility_first;
        $this->deferred_mandaue_facility_first = $deferred_mandaue_facility_first;
        $this->deferred_lapu_facility_first = $deferred_lapu_facility_first;

        //GRAND OVER ALL TOTAL DEFERRED SECOND
        $this->total_deferred_second = $total_deferred_second;
        $this->deferred_second_bohol = $deferred_second_bohol;
        $this->deferred_second_cebu = $deferred_second_cebu;
        $this->deferred_second_negros = $deferred_second_negros;
        $this->deferred_second_siquijor = $deferred_second_siquijor;
        $this->deferred_cebu_facility_second = $deferred_cebu_facility_second;
        $this->deferred_mandaue_facility_second = $deferred_mandaue_facility_second;
        $this->deferred_lapu_facility_second = $deferred_lapu_facility_second;

        //GRAND OVERALL WASTAGE A4
        $this->wastage_sinovac_first = $wastage_sinovac_first;
        $this->wastage_astra_first = $wastage_astra_first;
        $this->wastage_sputnikv_first = $wastage_sputnikv_first;
        $this->wastage_pfizer_first = $wastage_pfizer_first;

        //GRAND OVERALL SINOVAC A4
        $this->wastage_sinovac_bohol_first = $wastage_sinovac_bohol_first;
        $this->wastage_sinovac_cebu_first = $wastage_sinovac_cebu_first;
        $this->wastage_sinovac_negros_first = $wastage_sinovac_negros_first;
        $this->wastage_sinovac_siquijor_first = $wastage_sinovac_siquijor_first;
        $this->wastage_sinovac_cebu_facility_first = $wastage_sinovac_cebu_facility_first;
        $this->wastage_sinovac_mandaue_facility_first = $wastage_sinovac_mandaue_facility_first;
        $this->wastage_sinovac_lapu_facility_first = $wastage_sinovac_lapu_facility_first;

        //GRAND OVERALL ASTRA A4
        $this->wastage_astra_bohol_first = $wastage_astra_bohol_first;
        $this->wastage_astra_cebu_first = $wastage_astra_cebu_first;
        $this->wastage_astra_negros_first = $wastage_astra_negros_first;
        $this->wastage_astra_siquijor_first = $wastage_astra_siquijor_first;
        $this->wastage_astra_cebu_facility_first = $wastage_astra_cebu_facility_first;
        $this->wastage_astra_mandaue_facility_first = $wastage_astra_mandaue_facility_first;
        $this->wastage_astra_lapu_facility_first = $wastage_astra_lapu_facility_first;

        //GRAND OVERALL SPUTNIKV A4
        $this->wastage_sputnikv_bohol_first = $wastage_sputnikv_bohol_first;
        $this->wastage_sputnikv_cebu_first = $wastage_sputnikv_cebu_first;
        $this->wastage_sputnikv_negros_first = $wastage_sputnikv_negros_first;
        $this->wastage_sputnikv_siquijor_first = $wastage_sputnikv_siquijor_first;
        $this->wastage_sputnikv_cebu_facility_first = $wastage_sputnikv_cebu_facility_first;
        $this->wastage_sputnikv_mandaue_facility_first = $wastage_sputnikv_mandaue_facility_first;
        $this->wastage_sputnikv_lapu_facility_first = $wastage_sputnikv_lapu_facility_first;

        //GRAND OVERALL PFIZER
        $this->wastage_pfizer_bohol_first = $wastage_pfizer_bohol_first;
        $this->wastage_pfizer_cebu_first = $wastage_pfizer_cebu_first;
        $this->wastage_pfizer_negros_first = $wastage_pfizer_negros_first;
        $this->wastage_pfizer_siquijor_first = $wastage_pfizer_siquijor_first;
        $this->wastage_pfizer_cebu_facility_first = $wastage_pfizer_cebu_facility_first;
        $this->wastage_pfizer_mandaue_facility_first = $wastage_pfizer_mandaue_facility_first;
        $this->wastage_pfizer_lapu_facility_first = $wastage_pfizer_lapu_facility_first;

        //TOTAL CONSUMPTION RATE REGION

        $this->c_rate_region_sinovac_first = number_format($this->region_sinovac_first_dose / $this->sinovac_region * 100 ,2);
        $this->c_rate_region_astra_first = number_format($this->region_astra_first_dose / $this->astra_region * 100 ,2);
        $this->c_rate_region_sputnikv_first = number_format($this->region_sputnikv_first_dose / $this->sputnikv_region * 100 ,2);
        $this->c_rate_region_pfizer_first = number_format($this->region_pfizer_first_dose / $this->pfizer_region * 100 ,2);
        $this->total_c_rate_region_first = number_format($this->first_dose_total / $this->total_region * 100,2);

        //TOTAL CONSUMPTION RATE BOHOL
        $this->c_rate_sinovac_bohol_first = number_format($this->vcted_sinovac_bohol_first / $this->sinovac_bohol * 100,2);
        $this->c_rate_astra_bohol_first = number_format($this->vcted_astra_bohol_first / $this->astra_bohol * 100,2);
        $this->c_rate_sputnikv_bohol_first = number_format($this->vcted_sputnikv_bohol_first / $this->sputnikv_bohol * 100,2);
        $this->c_rate_pfizer_bohol_first = number_format($this->vcted_pfizer_bohol_first / $this->pfizer_bohol * 100,2);
        $this->total_c_rate_bohol_first = number_format($this->total_vcted_first_bohol / $this->total_bohol,2);

        //TOTAL CONSUMPTION RATE CEBU
        $this->c_rate_sinovac_cebu_first = number_format($this->vcted_sinovac_cebu_first / $this->sinovac_cebu * 100,2);
        $this->c_rate_astra_cebu_first = number_format($this->vcted_astra_cebu_first / $this->astra_cebu * 100,2);
        $this->c_rate_sputnikv_cebu_first = number_format($this->vcted_sputnikv_cebu_first / $this->sputnikv_cebu * 100,2);
        $this->c_rate_pfizer_cebu_first = number_format($this->vcted_pfizer_cebu_first / $this->pfizer_cebu * 100,2);
        $this->total_c_rate_cebu_first = number_format($this->total_vcted_first_cebu / $this->total_cebu,2);

        //TOTAL CONSUMPTION RATE NEGROS
        $this->c_rate_sinovac_negros_first = number_format($this->vcted_sinovac_negros_first / $this->sinovac_negros * 100,2);
        $this->c_rate_astra_negros_first = number_format($this->vcted_astra_negros_first / $this->astra_negros * 100,2);
        $this->c_rate_sputnikv_negros_first = number_format($this->vcted_sputnikv_negros_first / $this->sputnikv_negros * 100,2);
        $this->c_rate_pfizer_negros_first = number_format($this->vcted_pfizer_negros_first / $this->pfizer_negros * 100,2);
        $this->total_c_rate_negros_first = number_format($this->total_vcted_first_negros / $this->total_negros,2);

        //TOTAL CONSUMPTION RATE SIQUIJOR
        $this->c_rate_sinovac_siquijor_first = number_format($this->vcted_sinovac_siquijor_first / $this->sinovac_siquijor * 100,2);
        $this->c_rate_astra_siquijor_first = number_format($this->vcted_astra_siquijor_first / $this->astra_siquijor * 100,2);
        $this->c_rate_sputnikv_siquijor_first = number_format($this->vcted_sputnikv_siquijor_first / $this->sputnikv_siquijor * 100,2);
        $this->c_rate_pfizer_siquijor_first = number_format($this->vcted_pfizer_siquijor_first / $this->pfizer_siquijor * 100,2);
        $this->total_c_rate_siquijor_first = number_format($this->total_vcted_first_siquijor / $this->total_siquijor,2);

        //TOTAL CONSUMPTION RATE CEBU FACILITY
        $this->c_rate_sinovac_cebu_facility_first = number_format($this->vcted_sinovac_cebu_facility_first / $this->sinovac_cebu_facility * 100 ,2 );
        $this->c_rate_astra_cebu_facility_first =  number_format($this->vcted_astra_cebu_facility_first / $this->astra_cebu_facility * 100  ,2 );
        $this->c_rate_sputnikv_cebu_facility_first = number_format($this->vcted_sputnikv_cebu_facility_first / $this->sputnikv_cebu_facility * 100  ,2 );
        $this->c_rate_pfizer_cebu_facility_first = number_format($this->vcted_pfizer_cebu_facility_first / $this->pfizer_cebu_facility * 100  ,2 );
        $this->total_c_rate_cebu_facility_first = number_format($this->total_vcted_cebu_facility_first / $this->total_cebu_facility * 100,2);

        //TOTAL CONSUMPTION RATE MANDAUE FACILITY
        $this->c_rate_sinovac_mandaue_facility_first = number_format($this->vcted_sinovac_mandaue_facility_first / $this->sinovac_mandaue_facility * 100 ,2 );
        $this->c_rate_astra_mandaue_facility_first =  number_format($this->vcted_astra_mandaue_facility_first / $this->astra_mandaue_facility * 100  ,2 );
        $this->c_rate_sputnikv_mandaue_facility_first = number_format($this->vcted_sputnikv_mandaue_facility_first / $this->sputnikv_mandaue_facility * 100  ,2 );
        $this->c_rate_pfizer_mandaue_facility_first = number_format($this->vcted_pfizer_mandaue_facility_first / $this->pfizer_mandaue_facility * 100  ,2 );
        $this->total_c_rate_mandaue_facility_first = number_format($this->total_vcted_mandaue_facility_first / $this->total_mandaue_facility * 100,2);

        //TOTAL CONSUMPTION RATE LAPU-LAPU FACILITY
        $this->c_rate_sinovac_lapu_facility_first = number_format($this->vcted_sinovac_lapu_facility_first / $this->sinovac_lapu_facility * 100 ,2 );
        $this->c_rate_astra_lapu_facility_first =  number_format($this->vcted_astra_lapu_facility_first / $this->astra_lapu_facility * 100  ,2 );
        $this->c_rate_sputnikv_lapu_facility_first = number_format($this->vcted_sputnikv_lapu_facility_first / $this->sputnikv_lapu_facility * 100  ,2 );
        $this->c_rate_pfizer_lapu_facility_first = number_format($this->vcted_pfizer_lapu_facility_first / $this->pfizer_lapu_facility * 100  ,2 );
        $this->total_c_rate_lapu_facility_first = number_format($this->total_vcted_lapu_facility_first / $this->total_lapu_facility * 100,2);

        //TOTAL CONSUMPTION RATE REGION

        $this->c_rate_region_sinovac_second = number_format($this->region_sinovac_second_dose / $this->sinovac_region * 100 ,2);
        $this->c_rate_region_astra_second = number_format($this->region_astra_second_dose / $this->astra_region * 100 ,2);
        $this->c_rate_region_sputnikv_second = number_format($this->region_sputnikv_second_dose / $this->sputnikv_region * 100 ,2);
        $this->c_rate_region_pfizer_second = number_format($this->region_pfizer_second_dose / $this->pfizer_region * 100 ,2);
        $this->total_c_rate_region_second = number_format($this->second_dose_total / $this->total_region * 100,2);

        //TOTAL CONSUMPTION RATE BOHOL
        $this->c_rate_sinovac_bohol_second = number_format($this->vcted_sinovac_bohol_second / $this->sinovac_bohol * 100,2);
        $this->c_rate_astra_bohol_second = number_format($this->vcted_astra_bohol_second / $this->astra_bohol * 100,2);
        $this->c_rate_sputnikv_bohol_second = number_format($this->vcted_sputnikv_bohol_second / $this->sputnikv_bohol * 100,2);
        $this->c_rate_pfizer_bohol_second = number_format($this->vcted_pfizer_bohol_second / $this->pfizer_bohol * 100,2);
        $this->total_c_rate_bohol_second = number_format($this->total_vcted_second_bohol / $this->total_bohol,2);

        //TOTAL CONSUMPTION RATE CEBU
        $this->c_rate_sinovac_cebu_second = number_format($this->vcted_sinovac_cebu_second / $this->sinovac_cebu * 100,2);
        $this->c_rate_astra_cebu_second = number_format($this->vcted_astra_cebu_second / $this->astra_cebu * 100,2);
        $this->c_rate_sputnikv_cebu_second = number_format($this->vcted_sputnikv_cebu_second / $this->sputnikv_cebu * 100,2);
        $this->c_rate_pfizer_cebu_second = number_format($this->vcted_pfizer_cebu_second / $this->pfizer_cebu * 100,2);
        $this->total_c_rate_cebu_second = number_format($this->total_vcted_second_cebu / $this->total_cebu,2);

        //TOTAL CONSUMPTION RATE NEGROS
        $this->c_rate_sinovac_negros_second = number_format($this->vcted_sinovac_negros_second / $this->sinovac_negros * 100,2);
        $this->c_rate_astra_negros_second = number_format($this->vcted_astra_negros_second / $this->astra_negros * 100,2);
        $this->c_rate_sputnikv_negros_second = number_format($this->vcted_sputnikv_negros_second / $this->sputnikv_negros * 100,2);
        $this->c_rate_pfizer_negros_second = number_format($this->vcted_pfizer_negros_second / $this->pfizer_negros * 100,2);
        $this->total_c_rate_negros_second = number_format($this->total_vcted_second_negros / $this->total_negros,2);

        //TOTAL CONSUMPTION RATE SIQUIJOR
        $this->c_rate_sinovac_siquijor_second = number_format($this->vcted_sinovac_siquijor_second / $this->sinovac_siquijor * 100,2);
        $this->c_rate_astra_siquijor_second = number_format($this->vcted_astra_siquijor_second / $this->astra_siquijor * 100,2);
        $this->c_rate_sputnikv_siquijor_second = number_format($this->vcted_sputnikv_siquijor_second / $this->sputnikv_siquijor * 100,2);
        $this->c_rate_pfizer_siquijor_second = number_format($this->vcted_pfizer_siquijor_second / $this->pfizer_siquijor * 100,2);
        $this->total_c_rate_siquijor_second = number_format($this->total_vcted_second_siquijor / $this->total_siquijor,2);

        //TOTAL CONSUMPTION RATE CEBU FACILITY
        $this->c_rate_sinovac_cebu_facility_second = number_format($this->vcted_sinovac_cebu_facility_second / $this->sinovac_cebu_facility * 100 ,2 );
        $this->c_rate_astra_cebu_facility_second =  number_format($this->vcted_astra_cebu_facility_second / $this->astra_cebu_facility * 100  ,2 );
        $this->c_rate_sputnikv_cebu_facility_second = number_format($this->vcted_sputnikv_cebu_facility_second / $this->sputnikv_cebu_facility * 100  ,2 );
        $this->c_rate_pfizer_cebu_facility_second = number_format($this->vcted_pfizer_cebu_facility_second / $this->pfizer_cebu_facility * 100  ,2 );
        $this->total_c_rate_cebu_facility_second = number_format($this->total_vcted_cebu_facility_second / $this->total_cebu_facility * 100,2);

        //TOTAL CONSUMPTION RATE MANDAUE FACILITY
        $this->c_rate_sinovac_mandaue_facility_second = number_format($this->vcted_sinovac_mandaue_facility_second / $this->sinovac_mandaue_facility * 100 ,2 );
        $this->c_rate_astra_mandaue_facility_second =  number_format($this->vcted_astra_mandaue_facility_second / $this->astra_mandaue_facility * 100  ,2 );
        $this->c_rate_sputnikv_mandaue_facility_second = number_format($this->vcted_sputnikv_mandaue_facility_second / $this->sputnikv_mandaue_facility * 100  ,2 );
        $this->c_rate_pfizer_mandaue_facility_second = number_format($this->vcted_pfizer_mandaue_facility_second / $this->pfizer_mandaue_facility * 100  ,2 );
        $this->total_c_rate_mandaue_facility_second = number_format($this->total_vcted_mandaue_facility_second / $this->total_mandaue_facility * 100,2);

        //TOTAL CONSUMPTION RATE LAPU-LAPU FACILITY
        $this->c_rate_sinovac_lapu_facility_second = number_format($this->vcted_sinovac_lapu_facility_second / $this->sinovac_lapu_facility * 100 ,2 );
        $this->c_rate_astra_lapu_facility_second =  number_format($this->vcted_astra_lapu_facility_second / $this->astra_lapu_facility * 100  ,2 );
        $this->c_rate_sputnikv_lapu_facility_second = number_format($this->vcted_sputnikv_lapu_facility_second / $this->sputnikv_lapu_facility * 100  ,2 );
        $this->c_rate_pfizer_lapu_facility_second = number_format($this->vcted_pfizer_lapu_facility_second / $this->pfizer_lapu_facility * 100  ,2 );
        $this->total_c_rate_lapu_facility_second = number_format($this->total_vcted_lapu_facility_second / $this->total_lapu_facility * 100,2);

        $this->priority_set = "";

        return view( 'vaccine.vaccine_summary_report',$this->tabValueDeclaration());
    }
}



