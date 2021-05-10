<hr>
<?php
//ELIGIBLE POP SINOVAC
$total_e_pop_svac_a1_grand =\DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'','')")[0]->a1; //A1 SINOVAC_FIRST
$total_e_pop_svac_a2_grand  = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'','')")[0]->a2; //A2 SINOVAC_FIRST
$total_e_pop_svac_a3_grand  = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'','')")[0]->a3; //A3 SINOVAC_FIRST
$total_e_pop_svac_a4_grand  = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'','')")[0]->a4; //A4 SINOVAC_FIRST
$total_e_pop_svac_grand = $total_e_pop_svac_a1_grand + $total_e_pop_svac_a2_grand + $total_e_pop_svac_a3_grand + $total_e_pop_svac_a4_grand ; //TOTAL ELI POP SINOVAC_FIRST


//ELIGIBLE_POP_ASTRAZENECA
$total_e_pop_astra_a1_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'','')")[0]->a1; //A1 ASTRA_FIRST
$total_e_pop_astra_a2_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'','')")[0]->a2; //A2 ASTRA_FIRST
$total_e_pop_astra_a3_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'','')")[0]->a2; //A3 ASTRA_FIRST
$total_e_pop_astra_a4_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'','')")[0]->a2; //A4 ASTRA_FIRST
$total_e_pop_astra_grand = $total_e_pop_astra_a1_grand + $total_e_pop_astra_a2_grand + $total_e_pop_astra_a3_grand + $total_e_pop_astra_a4_grand; //TOTAL E POP ASTRA_FIRST


//VACCINE_ALLOCATED
$total_vallocated_svac_frst_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->sinovac_allocated_first; //VACCINE ALLOCATED (FD) SINOVAC_FIRST
$total_vallocated_svac_scnd_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->sinovac_allocated_second; //VACCINE ALLOCATED (SD) SINOVAC_FIRST
$total_vallocated_astra_frst_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->astrazeneca_allocated_first; //VACCINE_ALLOCATED (FD) ASTRA_FIRST
$total_vallocated_astra_scnd_grand= \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->astrazeneca_allocated_second; //VACCINE ALLOCATED (SD) ASTRA_FIRST
//Session::put("total_e_pop_frtline_grand",$total_e_pop_a1_grand);
//Session::put("total_e_pop_sr_grand",$total_e_pop_a2_grand);

//SINOVAC
$total_svac_a1_frst_grand = 0; //VACCINATED (A1) SINOVAC_FIRST
$total_svac_a2_frst_grand= 0; //VACCINATED (A2) SINOVAC_FIRST
$total_svac_a3_frst_grand= 0; //VACCINATED (A3) SINOVAC_FIRST
$total_svac_a4_frst_grand= 0; //VACCINATED (A4) SINOVAC_FIRST
$total_svac_a1_scnd_grand = 0; //VACCINATED (A1) SINOVAC_SECOND
$total_svac_a2_scnd_grand = 0; //VACCINATED (A2) SINOVAC_SECOND
$total_svac_a3_scnd_grand = 0; //VACCINATED (A3) SINOVAC_SECOND
$total_svac_a4_scnd_grand = 0; //VACCINATED (A4) SINOVAC_SECOND

$total_svac_a1_frst_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','a1')")[0]->vaccinated_first_a;
$total_svac_a2_frst_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','a2')")[0]->vaccinated_first_a;
$total_svac_a3_frst_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','a3')")[0]->vaccinated_first_a;
$total_svac_a4_frst_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','a4')")[0]->vaccinated_first_a;
$total_svac_a1_scnd_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','a1')")[0]->vaccinated_second_a;
$total_svac_a2_scnd_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','a2')")[0]->vaccinated_second_a;
$total_svac_a3_scnd_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','a3')")[0]->vaccinated_second_a;
$total_svac_a4_scnd_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','a4')")[0]->vaccinated_second_a;

//ASTRAZENECA
$total_astra_a1_frst_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','a1')")[0]->vaccinated_first_a;
$total_astra_a2_frst_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','a2')")[0]->vaccinated_first_a;
$total_astra_a3_frst_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','a3')")[0]->vaccinated_first_a;
$total_astra_a4_frst_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','a4')")[0]->vaccinated_first_a;
$total_astra_a1_scnd_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','a1')")[0]->vaccinated_second_a;
$total_astra_a2_scnd_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','a2')")[0]->vaccinated_second_a;
$total_astra_a3_scnd_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','a3')")[0]->vaccinated_second_a;
$total_astra_a4_scnd_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','a4')")[0]->vaccinated_second_a;


$total_vcted_astra_scnd_grand = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->vaccinated_second; //TOTAL VACCINATED ASTRA_SECOND

$total_mild_svac_frst_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->mild_first; //MILD SINOVAC_FIRST
$total_mild_astra_frst_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->mild_first; //MILD ASTRA_FIRST

$total_mild_svac_scnd_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->mild_second; //MILD SINOVAC_SECOND
$total_mild_astra_scnd_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->mild_second; //MILD ASTRA_SECOND

$total_srs_svac_frst_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->serious_first; //SERIOUS SINOVAC_FIRST
$total_srs_astra_frst_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->serious_first; //SERIOUS ASTRA_FIRST

$total_srs_svac_scnd_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->serious_second; //SERIOUS  SINOVAC_SECOND
$total_srs_astra_scnd_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->serious_second; //SERIOUS ASTRA_SECOND

$total_dfrd_svac_frst_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->deferred_first; //DEFERRED SINOVAC_FIRST
$total_dfrd_astra_frst_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->deferred_first; //DEFERRED ASTRA_FIRS

$total_dfrd_svac_scnd_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->deferred_second; //DEFERRED  SINOVAC_SECOND
$total_dfrd_astra_scnd_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->deferred_second; //DEFERRED ASTRA_SECOND

$total_rfsd_svac_frst_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->refused_first; //REFUSED SINOVAC_FIRST
$total_rfsd_astra_frst_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->refused_first; //REFUSED ASTRA_FIRST

$total_rfsd_svac_scnd_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->refused_second; //REFUSED  SINOVAC_SECOND
$total_rfsd_astra_scnd_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->refused_second; //REFUSED ASTRA_SECOND

$total_wstge_svac_frst_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->wastage_first; //WASTAGE SINOVAC_FIRST
$total_wstge_astra_frst_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->wastage_first; //WASTAGE ASTRA_FIRST

$total_wstge_svac_scnd_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->wastage_second; //WASTAGE SINOVAC_SECOND
$total_wstge_astra_scnd_grand =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->wastage_second; //WASTAGE ASTRA_SECOND

$total_vallocated_frst_svac = $total_vallocated_svac_frst_grand + $total_vallocated_svac_scnd_grand; //TOTAL VACCINE ALLOCATED SINOVAC_FIRST
$total_vallocated_frst_astra = $total_vallocated_astra_frst_grand + $total_vallocated_astra_scnd_grand; //TOTAL VACCINE ALLOCATED ASTRA_FIRS
$total_vallocated = $total_vallocated_frst_svac + $total_vallocated_frst_astra;

$total_vallocated_frst_grand = $total_vallocated_svac_frst_grand + $total_vallocated_astra_frst_grand; //TOTAL VACCINE ALLOCATED FIRST
$total_vallocated_scnd_grand = $total_vallocated_svac_scnd_grand + $total_vallocated_astra_scnd_grand; //TOTAL VACCINE ALLOCATED SECOND
$total_vallocated = $total_vallocated_frst_grand + $total_vallocated_scnd_grand;


$total_vcted_svac_frst_grand = $total_svac_a1_frst_grand + $total_svac_a2_frst_grand + $total_svac_a3_frst_grand + $total_svac_a4_frst_grand; //TOTAL VACCINATED SINOVAC_FIRST
$total_vcted_svac_scnd_grand = $total_svac_a1_scnd_grand + $total_svac_a2_scnd_grand + $total_svac_a3_scnd_grand + $total_svac_a4_scnd_grand; //TOTAL VACCINATED SINOVAC_SECOND


$total_vcted_astra_frst_grand = $total_astra_a1_frst_grand + $total_astra_a2_frst_grand + $total_astra_a3_frst_grand + $total_astra_a4_frst_grand; // TOTAL VACCINATED ASTRA_FIRST
$total_vcted_astra_scnd_grand = $total_astra_a1_scnd_grand + $total_astra_a2_scnd_grand + $total_astra_a3_scnd_grand + $total_astra_a4_scnd_grand; //TOTAL VACCINATED ASTRA_SECOND

$total_vcted_a1_first_grand = $total_svac_a1_frst_grand + $total_astra_a1_frst_grand; //TOTAL VACCINATED (A1)
$total_vcted_a2_first_grand = $total_svac_a2_frst_grand + $total_astra_a2_frst_grand; //TOTAL VACCINATED (A2)
$total_vcted_a3_first_grand = $total_svac_a3_frst_grand + $total_astra_a3_frst_grand; //TOTAL VACCINATED (A3)
$total_vcted_a4_first_grand = $total_svac_a4_frst_grand + $total_astra_a4_frst_grand; //TOTAL VACCINATED (A4)

$total_vcted_first_overall =  $total_vcted_a1_first_grand + $total_vcted_a2_first_grand + $total_vcted_a3_first_grand + $total_vcted_a4_first_grand; //TOTAL VACCINATED FIRST DOSE OVERALL

$total_vcted_scnd_a1_grand = $total_svac_a1_scnd_grand + $total_astra_a1_scnd_grand; //TOTAL VACCINATED SECOND DOSE A1
$total_vcted_scnd_a2_grand = $total_svac_a2_scnd_grand + $total_astra_a2_scnd_grand; //TOTAL VACCINATED SECOND DOSE A2
$total_vcted_scnd_a3_grand = $total_svac_a3_scnd_grand + $total_astra_a3_scnd_grand; //TOTAL VACCINATED SECOND DOSE A3
$total_vcted_scnd_a4_grand = $total_svac_a4_scnd_grand + $total_astra_a4_scnd_grand; //TOTAL VACCINATED SECOND DOSE A4

$total_vcted_scnd_overall =  $total_vcted_scnd_a1_grand + $total_vcted_scnd_a2_grand + $total_vcted_scnd_a3_grand + $total_vcted_scnd_a4_grand; //TOTAL VACCINATED SECOND DOSE OVERALL


$total_rfsd_frst_grand = $total_rfsd_svac_frst_grand + $total_rfsd_astra_frst_grand; //TOTAL REFUSED
$total_rfsd_scnd_grand = $total_rfsd_svac_scnd_grand + $total_rfsd_astra_scnd_grand; //TOTAL REFUSED  2

//PERCENT COVERAGE
$total_p_cvrge_svac_frst_grand = $total_vcted_svac_frst_grand / $total_e_pop_svac_grand * 100; //PERCENT COVERAGE SINOVAC_FIRST
$total_p_cvrge_svac_scnd_grand = $total_vcted_svac_scnd_grand / $total_e_pop_svac_grand * 100; //PERCENT COVERAGE SINOVAC_SECOND
$total_p_cvrge_astra_frst_grand = $total_vcted_astra_frst_grand / $total_e_pop_astra_grand * 100; //PERCENT COVERAGE ASTRA_FIRST
$total_p_cvrge_astra_scnd_grand = $total_vcted_astra_scnd_grand / $total_e_pop_astra_grand * 100; //PERCENT COVERAGE ASTRA_SECOND
$total_p_cvrge_overall_frst = $total_vcted_first_overall / $total_e_pop_svac_grand * 100; //PERCENT_COVERAGE_OVERALL_FIRST
$total_p_cvrge_overall_scnd = $total_vcted_scnd_overall / $total_e_pop_svac_grand * 100; //PERCENT_COVERAGE_OVERALL_FIRST


//CONSUMPTTION RATE
$total_c_rate_svac_frst_grand = $total_vcted_svac_frst_grand / $total_vallocated_svac_frst_grand * 100; //CONSUMPTION RATE SINOVAC_FIRST goods
$total_c_rate_astra_frst_grand = $total_vcted_astra_frst_grand / $total_vallocated_astra_frst_grand * 100; //CONSUMPTION RATE ASTRA_FIRST goods
$total_c_rate_svac_scnd_grand = $total_vcted_svac_scnd_grand / $total_vallocated_svac_scnd_grand * 100; //CONSUMPTION RATE SINOVAC_SECOND goods
$total_c_rate_astra_scnd_grand = $total_vcted_astra_scnd_grand / $total_vallocated_astra_scnd_grand * 100; //CONSUMPTION RATE ASTRA_SECOND goods

$total_c_rate_frst_grand = $total_vcted_first_overall / $total_vallocated_frst_grand * 100; //TOTAL CONSUMPTION RATE goods
$total_c_rate_scnd_grand =  $total_vcted_scnd_overall / $total_vallocated_scnd_grand * 100; //TOTAL CONSUMPTION RATE  2 goods


//REMAINING UNVACCINATED
$total_r_unvcted_frst_svac_grand = $total_e_pop_svac_grand - $total_vcted_svac_frst_grand - $total_rfsd_svac_frst_grand; //REMAINING UNVACCINATED SINOVAC_FIRST goods
$total_r_unvcted_frst_astra_grand = $total_e_pop_astra_grand - $total_vcted_astra_frst_grand - $total_rfsd_astra_frst_grand; //REMAINUNG UNVACCINATED ASTRA_FIRST goods
$total_r_unvcted_scnd_svac_grand = $total_e_pop_svac_grand - $total_vcted_svac_scnd_grand - $total_rfsd_svac_scnd_grand; //REMAINING UNVACCINATED  SINOVAC_SECOND goods
$total_r_unvcted_scnd_astra_grand = $total_e_pop_astra_grand - $total_vcted_astra_scnd_grand - $total_rfsd_astra_scnd_grand;  //REMAINING UNVACCINATED ASTRA_SECOND goods

$total_r_unvcted_all_frst_grand = $total_e_pop_svac_grand - $total_vcted_first_overall - $total_rfsd_frst_grand; //TOTAL REMAINUNG UNVACCINATED goods //
$total_r_unvcted_all_scnd_grand = $total_e_pop_astra_grand - $total_vcted_scnd_overall - $total_rfsd_scnd_grand; //TOTAL REMAINING UNVACCIANTED  2 goods


$sinovac_dashboard = $total_vcted_svac_frst_grand + $total_vcted_svac_scnd_grand;
$astra_dashboard = $total_vcted_astra_frst_grand + $total_vcted_astra_scnd_grand;
$a1_dashboard = $total_vcted_a1_first_grand + $total_vcted_scnd_a1_grand;
$a2_dashboard = $total_vcted_a2_first_grand + $total_vcted_scnd_a2_grand;
$a3_dashboard = $total_vcted_a3_first_grand + $total_vcted_scnd_a3_grand;
$a4_dashboard = $total_vcted_a4_first_grand + $total_vcted_scnd_a4_grand;
$percent_coverage_dashboard_first = $total_p_cvrge_overall_frst;
$percent_coverage_dashboard_second = $total_p_cvrge_overall_scnd;
$consumption_rate_dashboard_first = $total_c_rate_frst_grand;
$consumption_rate_dashboard_second = $total_c_rate_scnd_grand;




Session::put("sinovac_dashboard",$sinovac_dashboard);
Session::put("astra_dashboard",$astra_dashboard);
Session::put("a1_dashboard",$a1_dashboard);
Session::put("a2_dashboard",$a2_dashboard);
Session::put("a3_dashboard",$a3_dashboard);
Session::put("a4_dashboard",$a4_dashboard);
Session::put("percent_coverage_dashboard_first",$percent_coverage_dashboard_first);
Session::put("percent_coverage_dashboard_second",$percent_coverage_dashboard_second);
Session::put("consumption_rate_dashboard_first",$consumption_rate_dashboard_first);
Session::put("consumption_rate_dashboard_second",$consumption_rate_dashboard_second);
?>

<h4>Grand Total</h4>
<button class="btn btn-link collapsed" style="color: red;" type="button" data-toggle="collapse" data-target="#collapse_sinovac_grand" aria-expanded="false" aria-controls="collapse_sinovac_grandtotal">
    <b>Sinovac</b>
</button>
<button class="btn btn-link collapsed" style="color: darkgoldenrod;" type="button" data-toggle="collapse" data-target="#collapse_astra_grand" aria-expanded="false" aria-controls="collapse_astra_grandtotal">
    <b>Astrazeneca</b>
</button>
<table style="font-size: 8pt;" class="table" border="2">
    <tbody>
    <tr>
        <th>Type of Vaccine</th> <!-- Type of Vaccine 1-1 -->
        <th colspan="5">
            <center>
                <a style="color:black"> Eligible Population</a>
            </center>
        </th>
        <th colspan="3">
            <center>
                <a style="color:black"> Vaccine Allocated</a>
            </center>
        </th>
        <th colspan="5"><center>Total Vaccinated </center></th>
        <th>Mild</th>
        <th>Serious</th>
        <th>Deferred</th>
        <th>Refused</th>
        <th>Wastage</th>
        <th>Percent Coverage</th>
        <th>Consumption Rate</th>
        <th>Remaining Unvaccinated</th>
    </tr>
    <tr>
        <td></td> <!-- 1-2 -->
        <th>A1</th>
        <th>A2</th>
        <th>A3</th>
        <th>A4</th>
        <th>Total</th>
        <th>1st</th>
        <th>2nd</th>
        <th>Total</th>
        <th>A1</th>
        <th>A2</th>
        <th>A3</th>
        <th>A4</th>
        <th>Total</th>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
    </tr>
    </tbody>
    <tbody id="collapse_sinovac_grand" class="collapse bg-danger" aria-labelledby="headingTwo" data-parent="#accordionExample">
    <tr style="background-color: #ffd8d6">
        <td rowspan="2">
        </td> <!-- 1-3 -->
        <td rowspan="2">{{ $total_e_pop_svac_a1_grand }}</td>  <!-- A1 SINOVAC_FIRST-->
        <td rowspan="2">{{ $total_e_pop_svac_a1_grand }} </td>   <!-- A2 SINOVAC_FIRST -->
        <td rowspan="2">{{ $total_e_pop_svac_a1_grand }}</td>  <!-- A3 SINOVAC_FIRST -->
        <td rowspan="2">{{ $total_e_pop_svac_a1_grand }}</td>  <!-- A4 SINOVAC_FIRST -->
        <td rowspan="2">{{ $total_e_pop_svac_grand }}</td> <!-- TOTAL ELI POP SINOVAC_FIRST -->
        <td rowspan="2">{{ $total_vallocated_svac_frst_grand }} </td> <!-- VACCINE ALLOCATED (FD) SINOVAC_FIRST -->
        <td rowspan="2">{{ $total_vallocated_svac_scnd_grand }} </td>  <!-- VACCINE ALLOCATED (SD) SINOVAC_FIRST -->
        <td rowspan="2"> {{ $total_vallocated_frst_svac }}</td>  <!-- TOTAL VACCINE ALLOCATED SINOVAC_FIRST -->
        <td>
            <span class="label label-success">{{ $total_svac_a1_frst_grand }}</span>   <!-- VACCINATED (A1) SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_svac_a2_frst_grand }}</span>  <!-- VACCINATED (A2) SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_svac_a3_frst_grand }}</span>  <!-- VACCINATED (A3) SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_svac_a4_frst_grand }}</span>  <!-- VACCINATED (A4) SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_vcted_svac_frst_grand }}</span>  <!-- TOTAL VACCINATED SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_mild_svac_frst_grand }}</span>  <!-- MILD SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_srs_svac_frst_grand }}</span> <!-- SERIOUS SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_dfrd_svac_frst_grand }}</span> <!-- DEFERRED SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_rfsd_svac_frst_grand }}</span> <!-- REFUSED SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_wstge_svac_frst_grand }}</span> <!-- WASTAGE SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_p_cvrge_svac_frst_grand,2) }}%</span> <!-- PERCENT COVERAGE SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_c_rate_svac_frst_grand,2) }}%</span> <!-- CONSUMPTION RATE SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_r_unvcted_frst_svac_grand }}</span> <!-- REMAINING UNVACCINATED SINOVAC_FIRST -->
        </td>
    </tr>
    <tr style="background-color: #ffd8d6">
        <td>
            <span class="label label-warning">{{ $total_svac_a1_scnd_grand }}</span>   <!-- VACCINATED (A1) SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_svac_a2_scnd_grand }}</span> <!-- VACCINATED (A2) SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_svac_a3_scnd_grand }}</span> <!-- VACCINATED (A3) SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_svac_a4_scnd_grand }}</span> <!-- VACCINATED (A4) SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_vcted_svac_scnd_grand }}</span> <!-- TOTAL VACCINATED SINOVAC_SECOND -->
        </td> <!-- 1-4 -->
        <td>
            <span class="label label-warning">{{ $total_mild_svac_scnd_grand }}</span> <!-- MILD SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_srs_svac_scnd_grand }}</span> <!-- SERIOUS  SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_dfrd_svac_scnd_grand }}</span> <!-- DEFERRED  SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_rfsd_svac_scnd_grand }}</span> <!-- REFUSED  SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_wstge_svac_scnd_grand }}</span> <!-- WASTAGE SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_p_cvrge_svac_scnd_grand,2) }}%</span> <!-- PERCENT COVERAGE  SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_c_rate_svac_scnd_grand,2) }}%</span> <!-- CONSUMPTION RATE SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_r_unvcted_scnd_svac_grand }}</span> <!-- REMAINING UNVACCINATED  SINOVAC_SECOND -->
        </td>
    </tr>
    </tbody>
    <tbody id="collapse_astra_grand" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
    <tr style="background-color: #f2fcac">
        <td rowspan="2">

        </td> <!-- 1-5 -->
        <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_a1_grand }}</td>  <!-- A1 ASTRA_FIRST-->
        <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_a2_grand }}</td> <!-- A2 ASTRA_FIRST -->
        <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_a3_grand }}</td> <!-- A3 ASTRA_FIRST -->
        <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_a4_grand }}</td> <!-- A4 ASTRA_FIRST -->
        <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_grand }}</td> <!-- TOTAL E POP ASTRA_FIRST -->
        <td rowspan="2" style="color:black">{{ $total_vallocated_astra_frst_grand }}</td> <!-- VACCINE_ALLOCATED (FD) ASTRA_FIRST-->
        <td rowspan="2" style="color:black">{{ $total_vallocated_astra_scnd_grand }}</td>  <!-- VACCINE ALLOCATED (SD) ASTRA_FIRST -->
        <td rowspan="2" style="color:black;">{{ $total_vallocated_frst_astra }}</td>  <!-- TOTAL VACCINE ALLOCATED ASTRA_FIRST -->
        <td style="color:black;">
            <span class="label label-success">{{ $total_astra_a1_frst_grand }}</span>  <!-- VACCINATED (A1) ASTRA_FIRST -->
        </td>
        <td style="color:black">
            <span class="label label-success">{{ $total_astra_a2_frst_grand }}</span> <!-- VACCINATED (A2) ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_astra_a3_frst_grand }}</span> <!-- VACCINATED (A3) ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_astra_a4_frst_grand }}</span> <!-- VACCINATED (A4) ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_vcted_astra_frst_grand }}</span> <!-- TOTAL VACCINATED ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_mild_astra_frst_grand }}</span> <!-- MILD ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_srs_astra_frst_grand }}</span> <!-- SERIOUS ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_dfrd_astra_frst_grand }}</span> <!-- DEFERRED ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_rfsd_astra_frst_grand }}</span> <!-- REFUSED ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_wstge_astra_frst_grand }}</span> <!-- WASTAGE ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_p_cvrge_astra_frst_grand,2) }}%</span> <!-- PERCENT_COVERAGE ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_c_rate_astra_frst_grand,2) }}%</span> <!-- CONSUMPTION RATE ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_r_unvcted_frst_astra_grand }}</span> <!-- REMAINUNG UNVACCINATED ASTRA_FIRST -->
        </td>
    </tr>
    <tr style="background-color: #f2fcac">
        <td style="color: black;">
            <span class="label label-warning">{{ $total_astra_a1_scnd_grand }}</span>  <!-- VACCINATED (A1) ASTRA_SECOND -->
        </td>
        <td style="color:black;">
            <span class="label label-warning">{{ $total_astra_a2_scnd_grand }}</span>  <!-- VACCINATED (A2) ASTRA_SECOND -->
        </td>
        <td style="color:black;">
            <span class="label label-warning">{{ $total_astra_a3_scnd_grand }}</span>  <!-- VACCINATED (A3) ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_astra_a4_scnd_grand }}</span>  <!-- VACCINATED (A4) ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_vcted_astra_scnd_grand }}</span>  <!-- TOTAL VACCINATED ASTRA_SECOND -->
        </td> <!-- 1-6 -->
        <td>
            <span class="label label-warning">{{ $total_mild_astra_scnd_grand }}</span>  <!-- MILD ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_srs_astra_scnd_grand }}</span> <!-- SERIOUS ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_dfrd_astra_scnd_grand }}</span> <!-- DEFERRED ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_rfsd_astra_scnd_grand }}</span> <!-- REFUSED ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_wstge_astra_scnd_grand }}</span> <!-- WASTAGE ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_p_cvrge_astra_scnd_grand,2) }}%</span> <!-- PERCENT COVERAGE ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_c_rate_astra_scnd_grand,2) }}%</span> <!-- CONSUMPTION RATE ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_r_unvcted_scnd_astra_grand }}</span> <!-- REMAINING UNVACCINATED ASTRA_SECOND -->
        </td>
    </tr>
    </tbody>
    <tbody>
    <tr>
        <td>Total</td> <!-- 1-7 -->
        <td>{{ $total_e_pop_astra_a1_grand }}</td> <!-- TOTAL A1 -->
        <td>{{ $total_e_pop_astra_a2_grand }}</td>  <!-- TOTAL A2 -->
        <td>{{ $total_e_pop_astra_a3_grand }}</td><!-- TOTAL A3 -->
        <td>{{ $total_e_pop_astra_a4_grand }}</td> <!-- TOTAL A4 -->
        <td>{{ $total_e_pop_astra_grand }}</td>  <!-- TOTAL E POP  -->
        <td>{{ $total_vallocated_frst_grand }}</td>  <!-- TOTAL VACCINE ALLOCATED FIRST -->
        <td>{{ $total_vallocated_scnd_grand }} </td> <!-- TOTAL VACCINE ALLOCATED SECOND -->
        <td><b>{{ $total_vallocated }} </b></td>  <!-- TOTAL VACCINE ALLOCATED  -->
        <td>
            <b class="label label-success" style="margin-right: 5%">{{$total_vcted_a1_first_grand }}</b> <!-- TOTAL VACCINATED (A1) -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_vcted_a2_first_grand }}</b>  <!-- TOTAL VACCINATED (A2) -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{$total_vcted_a3_first_grand }}</b> <!-- TOTAL VACCINATED (A3) -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{$total_vcted_a4_first_grand }}</b> <!-- TOTAL VACCINATED (A4) -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_vcted_first_overall }}</b>  <!-- TOTAL VACCINATED FIRST DOSE OVERALL-->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_mild_svac_frst_grand + $total_mild_astra_frst_grand }}</b>  <!-- TOTAL MILD -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_srs_svac_frst_grand + $total_srs_astra_frst_grand }}</b>  <!-- TOTAL SERIOUS -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_dfrd_svac_frst_grand + $total_dfrd_astra_frst_grand }}</b>  <!-- TOTAL DEFERRED -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_rfsd_frst_grand }}</b>  <!-- TOTAL REFUSED -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_wstge_svac_frst_grand + $total_wstge_astra_frst_grand }}</b>  <!-- TOTAL WASTAGE -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ number_format($total_p_cvrge_overall_frst,2) }}%</b>  <!-- PERCENT_COVERAGE_OVERALL_FIRST -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ number_format($total_c_rate_frst_grand,2) }}%</b>  <!-- TOTAL CONSUMPTION RATE -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_r_unvcted_all_frst_grand }}</b>  <!-- TOTAL REMAINUNG UNVACCINATED  -->
        </td>
    </tr>
    <tr>
        <td></td> <!-- 1-7 -->
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_scnd_a1_grand }}</b> <!-- TOTAL VACCINATED SECOND DOSE A1 -->
        </td>
        <td>

            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_scnd_a2_grand }}</b> <!-- TOTAL VACCINATED SECOND DOSE A2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_scnd_a3_grand }}</b> <!-- TOTAL VACCINATED SECOND DOSE A3 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_scnd_a4_grand }}</b> <!-- TOTAL VACCINATED SECOND DOSE A4-->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_scnd_overall }}</b> <!-- //TOTAL VACCINATED SECOND DOSE OVERALL -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_mild_svac_scnd_grand + $total_mild_astra_scnd_grand  }}</b> <!-- TOTAL MILD 2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_srs_svac_scnd_grand + $total_srs_astra_scnd_grand }}</b> <!-- TOTAL SERIOUS  2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_dfrd_svac_scnd_grand + $total_dfrd_astra_scnd_grand }}</b> <!-- TOTAL DEFERRED  2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_rfsd_scnd_grand }}</b> <!-- TOTAL REFUSED  2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_wstge_svac_scnd_grand + $total_wstge_astra_scnd_grand }}</b> <!-- TOTAL WASTAGE  2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ number_format($total_p_cvrge_overall_scnd,2) }}%</b> <!-- PERCENT_COVERAGE_OVERALL_FIRST -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ number_format($total_c_rate_scnd_grand,2) }}%</b> <!-- TOTAL CONSUMPTION RATE  2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_r_unvcted_all_scnd_grand }}</b> <!-- TOTAL REMAINING UNVACCIANTED  2 -->
        </td>
    </tr>
    </tbody>
</table>