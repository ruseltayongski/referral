<hr>
<?php
//ELIGIBLE POP SINOVAC
$total_e_pop_frtline_prov =\DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'','')")[0]->frontline_health_workers; //Frontline(A1) SINOVAC_FIRST
$total_e_pop_sr_prov  = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'','')")[0]->senior_citizens; //Senior (A2) SINOVAC_FIRST
$total_e_pop_svac_prov = $total_e_pop_frtline_prov + $total_e_pop_sr_prov; //TOTAL ELI POP SINOVAC_FIRST


//ELIGIBLE_POP_ASTRAZENECA
$total_e_pop_astra_frtline_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'','')")[0]->frontline_health_workers; //Frontline(A1) ASTRA_FIRST
$total_e_pop_astra_sr_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'','')")[0]->senior_citizens; //SENIOR(A2) ASTRA_FIRST
$total_e_pop_astra_prov = $total_e_pop_astra_frtline_prov + $total_e_pop_astra_sr_prov; //TOTAL E POP ASTRA_FIRST


//VACCINE_ALLOCATED
$total_vallocated_svac_frst_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->sinovac_allocated_first; //VACCINE ALLOCATED (FD) SINOVAC_FIRST
$total_vallocated_svac_scnd_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->sinovac_allocated_second; //VACCINE ALLOCATED (SD) SINOVAC_FIRST
$total_vallocated_astra_frst_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->astrazeneca_allocated_first; //VACCINE_ALLOCATED (FD) ASTRA_FIRST
Session::put("total_e_pop_frtline_prov",$total_e_pop_frtline_prov);
Session::put("total_e_pop_sr_prov",$total_e_pop_sr_prov);
$total_vallocated_astra_scnd_prov= \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->astrazeneca_allocated_second; //VACCINE ALLOCATED (SD) ASTRA_FIRST


//SINOVAC
$total_svac_a1_frst_prov = 0; //VACCINATED (A1) SINOVAC_FIRST
$total_svac_a2_frst_prov= 0; //VACCINATED (A2) SINOVAC_FIRST
$total_svac_a1_scnd_prov = 0; //VACCINATED (A1) SINOVAC_SECOND
$total_svac_a2_scnd_prov = 0; //VACCINATED (A2) SINOVAC_SECOND

$total_svac_a1_frst_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','frontline_health_workers')")[0]->vaccinated_first_a;
$total_svac_a2_frst_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','indigent_senior_citizens')")[0]->vaccinated_first_a;
$total_svac_a1_scnd_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','frontline_health_workers')")[0]->vaccinated_second_a;
$total_svac_a2_scnd_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','indigent_senior_citizens')")[0]->vaccinated_second_a;

//ASTRAZENECA
$total_astra_a1_frst_prov = 0; //VACCINATED (A1) ASTRA_FIRST
$total_astra_a2_frst_prov = 0; //VACCINATED (A2) ASTRA_FIRST
$total_astra_a1_scnd_prov = 0; //VACCINATED (A1) ASTRA_SECOND
$total_astra_a2_scnd_prov = 0; //VACCINATED (A2) ASTRA_SECOND

$total_astra_a1_frst_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','frontline_health_workers')")[0]->vaccinated_first_a; // A1 ROW-3
$total_astra_a2_frst_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_first_a;
$total_astra_a1_scnd_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','frontline_health_workers')")[0]->vaccinated_second_a;
$total_astra_a2_scnd_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_second_a;

$total_vcted_svac_frst_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->vaccinated_first; //TOTAL VACCINATED SINOVAC_FIRST
$total_vcted_astra_frst_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->vaccinated_first; //TOTAL VACCINATED ASTRA_FIRST

$total_vcted_svac_scnd_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->vaccinated_second; //TOTAL VACCINATED SINOVAC_SECOND
$total_vcted_astra_scnd_prov = \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->vaccinated_second; //TOTAL VACCINATED ASTRA_SECOND

$total_mild_svac_frst_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->mild_first; //MILD SINOVAC_FIRST
$total_mild_astra_frst_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->mild_first; //MILD ASTRA_FIRST

$total_mild_svac_scnd_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->mild_second; //MILD SINOVAC_SECOND
$total_mild_astra_scnd_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->mild_second; //MILD ASTRA_SECOND

$total_srs_svac_frst_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->serious_first; //SERIOUS SINOVAC_FIRST
$total_srs_astra_frst_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->serious_first; //SERIOUS ASTRA_FIRST

$total_srs_svac_scnd_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->serious_second; //SERIOUS  SINOVAC_SECOND
$total_srs_astra_scnd_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->serious_second; //SERIOUS ASTRA_SECOND

$total_dfrd_svac_frst_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->deferred_first; //DEFERRED SINOVAC_FIRST
$total_dfrd_astra_frst_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->deferred_first; //DEFERRED ASTRA_FIRS

$total_dfrd_svac_scnd_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->deferred_second; //DEFERRED  SINOVAC_SECOND
$total_dfrd_astra_scnd_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->deferred_second; //DEFERRED ASTRA_SECOND

$total_rfsd_svac_frst_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->refused_first; //REFUSED SINOVAC_FIRST
$total_rfsd_astra_frst_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->refused_first; //REFUSED ASTRA_FIRST

$total_rfsd_svac_scnd_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->refused_second; //REFUSED  SINOVAC_SECOND
$total_rfsd_astra_scnd_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->refused_second; //REFUSED ASTRA_SECOND

$total_wstge_svac_frst_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->wastage_first; //WASTAGE SINOVAC_FIRST
$total_wstge_astra_frst_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->wastage_first; //WASTAGE ASTRA_FIRST

$total_wstge_svac_scnd_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Sinovac','')")[0]->wastage_second; //WASTAGE SINOVAC_SECOND
$total_wstge_astra_scnd_prov =  \DB::connection('mysql')->select("call vaccine_facility_overall($province_id,'Astrazeneca','')")[0]->wastage_second; //WASTAGE ASTRA_SECOND

$total_vallocated_frst_svac = $total_vallocated_svac_frst_prov + $total_vallocated_svac_scnd_prov; //TOTAL VACCINE ALLOCATED SINOVAC_FIRST
$total_vallocated_frst_astra = $total_vallocated_astra_frst_prov + $total_vallocated_astra_scnd_prov; //TOTAL VACCINE ALLOCATED ASTRA_FIRS
$total_vallocated = $total_vallocated_frst_svac + $total_vallocated_frst_astra;

$total_vallocated_frst_prov = $total_vallocated_svac_frst_prov + $total_vallocated_astra_frst_prov; //TOTAL VACCINE ALLOCATED FIRST
$total_vallocated_scnd_prov = $total_vallocated_svac_scnd_prov + $total_vallocated_astra_scnd_prov; //TOTAL VACCINE ALLOCATED SECOND
$total_vallocated = $total_vallocated_frst_prov + $total_vallocated_scnd_prov;

$total_vcted_first_prov = $total_vcted_svac_frst_prov + $total_vcted_astra_frst_prov;
$total_vcted_scnd_prov = $total_vcted_svac_scnd_prov + $total_vcted_astra_scnd_prov;
$total_vcted_svac_frst_prov = $total_svac_a1_frst_prov +  $total_svac_a2_frst_prov; //TOTAL VACCINATED SINOVAC_FIRST
$total_vcted_svac_scnd_prov = $total_svac_a1_scnd_prov + $total_svac_a2_scnd_prov; //TOTAL VACCINATED SINOVAC_SECOND
$total_vcted_astra_frst_prov = $total_astra_a1_frst_prov + $total_astra_a2_frst_prov; // TOTAL VACCINATED ASTRA_FIRST
$total_vcted_astra_scnd_prov = $total_astra_a1_scnd_prov + $total_astra_a2_scnd_prov; //TOTAL VACCINATED ASTRA_SECOND

$total_rfsd_frst_prov = $total_rfsd_svac_frst_prov + $total_rfsd_astra_frst_prov; //TOTAL REFUSED
$total_rfsd_scnd_prov = $total_rfsd_svac_scnd_prov + $total_rfsd_astra_scnd_prov; //TOTAL REFUSED  2

$total_r_unvcted_frst_prov = $total_e_pop_svac_prov - $total_vcted_first_prov - $total_rfsd_frst_prov;
$total_r_unvcted_scnd_prov = $total_e_pop_astra_prov - $total_vcted_scnd_prov - $total_rfsd_scnd_prov;

$total_vcted_svac_frst_prov = $total_svac_a1_frst_prov + $total_astra_a1_frst_prov;
$total_vcted_svac_scnd_prov = $total_svac_a2_frst_prov + $total_astra_a2_frst_prov;
$total_vcted_svac_prov = $total_vcted_svac_frst_prov + $total_vcted_svac_scnd_prov;

$total_vcted_scnd_a1_prov = $total_svac_a1_scnd_prov + $total_astra_a1_scnd_prov; //TOTAL VACCINATED (FD) 2
$total_vcted_scnd_a2_prov = $total_svac_a2_scnd_prov + $total_astra_a2_scnd_prov; //TOTAL VACCINATED (SD) 2
$total_vcted_astra_prov = $total_vcted_scnd_a1_prov + $total_vcted_scnd_a2_prov; //TOTAL VACCINATED 2

$total_p_cvrge_frst_prov = $total_vcted_svac_prov / $total_e_pop_astra_prov * 100; //TOTAL PERCENT_COVERAGE
$total_p_cvrge_scnd_prov = $total_vcted_astra_prov / $total_e_pop_astra_prov * 100; //TOTAL PERCENT_COVERAGE  2

$total_c_rate_frst_prov = $total_vcted_svac_prov / $total_vallocated_frst_prov * 100; //TOTAL CONSUMPTION RATE
$total_c_rate_scnd_prov =  $total_vcted_astra_prov / $total_vallocated_scnd_prov * 100; //TOTAL CONSUMPTION RATE  2

$total_p_cvrge_svac_frst_prov = $total_vcted_svac_frst_prov / $total_e_pop_svac_prov * 100; //PERCENT COVERAGE SINOVAC_FIRST //
$total_p_cvrge_svac_scnd_prov =  $total_vcted_svac_scnd_prov / $total_e_pop_svac_prov * 100; //PERCENT COVERAGE  SINOVAC_SECOND
$total_p_cvrge_astra_frst_prov = $total_vcted_astra_frst_prov / $total_e_pop_astra_prov * 100; //PERCENT_COVERAGE ASTRA_FIRST
$total_p_cvage_astra_scnd_prov =  $total_vcted_astra_scnd_prov / $total_e_pop_astra_prov * 100; //PERCENT_COVERAGE_ASTRA_SECOND


$total_c_rate_svac_frst_prov = $total_vcted_svac_frst_prov / $total_vallocated_svac_frst_prov * 100; //CONSUMPTION RATE SINOVAC_FIRST //DARAAAA
$total_c_rate_svac_scnd_prov = $total_vcted_svac_scnd_prov / $total_vallocated_svac_scnd_prov * 100; //CONSUMPTION RATE SINOVAC_SECOND
$total_c_rate_astra_frst_prov = $total_vcted_astra_frst_prov / $total_vallocated_astra_frst_prov * 100; //CONSUMPTION RATE ASTRA_FIRST
$total_c_rate_astra_scnd_prov = $total_vcted_astra_scnd_prov / $total_vallocated_astra_scnd_prov * 100; //CONSUMPTION RATE ASTRA_SECOND

$total_p_cvrge_svac_frst_prov = $total_vcted_svac_frst_prov / $total_e_pop_svac_prov * 100; //PERCENT COVERAGE SINOVAC_FIRST
$total_p_cvrge_svac_scnd_prov =  $total_vcted_svac_scnd_prov / $total_e_pop_svac_prov * 100; //PERCENT COVERAGE  SINOVAC_SECOND
$total_p_cvrge_astra_frst_prov = $total_vcted_astra_frst_prov / $total_e_pop_astra_prov * 100; //PERCENT_COVERAGE ASTRA_FIRST /
$total_p_cvage_astra_scnd_prov =  $total_vcted_astra_scnd_prov / $total_e_pop_astra_prov * 100; //PERCENT_COVERAGE_ASTRA_SECOND


$total_r_unvcted_frst_svac_prov = $total_e_pop_svac_prov - $total_vcted_svac_frst_prov - $total_rfsd_svac_frst_prov; //REMAINING UNVACCINATED SINOVAC_FIRST
$total_r_unvcted_frst_astra_prov = $total_e_pop_astra_prov - $total_vcted_astra_frst_prov - $total_rfsd_astra_frst_prov; //REMAINUNG UNVACCINATED ASTRA_FIRST
$total_r_unvcted_scnd_svac_prov = $total_e_pop_svac_prov - $total_vcted_svac_scnd_prov - $total_rfsd_svac_scnd_prov; //REMAINING UNVACCINATED  SINOVAC_SECOND
$total_r_unvcted_scnd_astra_prov = $total_e_pop_astra_prov - $total_vcted_astra_scnd_prov - $total_rfsd_astra_scnd_prov;  //REMAINING UNVACCINATED ASTRA_SECOND

//TOTAL_REMAINING_UNVACCINATED_ALL
$total_r_unvcted_all_frst_prov  = $total_e_pop_svac_prov - $total_vcted_svac_prov - $total_rfsd_frst_prov; //TOTAL REMAINUNG UNVACCINATED
$total_r_unvcted_all_scnd_prov  = $total_e_pop_svac_prov - $total_vcted_astra_prov - $total_rfsd_scnd_prov; //TOTAL REMAINING UNVACCIANTED  2

$sinovac_dashboard = $total_vcted_svac_frst_prov  + $total_vcted_svac_scnd_prov;
$astra_dashboard = $total_vcted_astra_frst_prov  + $total_vcted_astra_scnd_prov;

Session::put("sinovac_dashboard",$sinovac_dashboard);
Session::put("astra_dashboard",$astra_dashboard);
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
        <th colspan="3">
            <center>
                <a style="color:black"> Eligible Population</a>
            </center>
        </th>
        <th colspan="3">
            <center>
                <a style="color:black"> Vaccine Allocated</a>
            </center>
        </th>
        <th colspan="3"><center>Total Vaccinated </center></th>
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
        <th>Frontline(A1)</th>
        <th>Seniors(A2)</th>
        <th>Total</th>
        <th>FD</th>
        <th>SD</th>
        <th>Total</th>
        <th>A1</th>
        <th>A2</th>
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
        <td rowspan="2">{{ $total_e_pop_frtline_prov }}</td>  <!-- Frontline(A1) SINOVAC_FIRST-->
        <td rowspan="2">{{ $total_e_pop_sr_prov }} </td>   <!-- Senior (A2) SINOVAC_FIRST -->
        <td rowspan="2">{{ $total_e_pop_svac_prov }} <!-- TOTAL ELI POP SINOVAC_FIRST -->
        </td>
        <td rowspan="2">{{ $total_vallocated_svac_frst_prov }} </td> <!-- VACCINE ALLOCATED (FD) SINOVAC_FIRST -->
        <td rowspan="2">{{ $total_vallocated_svac_scnd_prov }} </td>  <!-- VACCINE ALLOCATED (SD) SINOVAC_FIRST -->
        <td rowspan="2"> {{ $total_vallocated_frst_svac }}</td>  <!-- TOTAL VACCINE ALLOCATED SINOVAC_FIRST -->
        <td>
            <span class="label label-success">{{ $total_svac_a1_frst_prov }}</span>   <!-- VACCINATED (A1) SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_svac_a2_frst_prov }}</span>  <!-- VACCINATED (A2) SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_vcted_svac_frst_prov }}</span>  <!-- TOTAL VACCINATED SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_mild_svac_frst_prov }}</span>  <!-- MILD SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_srs_svac_frst_prov }}</span> <!-- SERIOUS SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_dfrd_svac_frst_prov }}</span> <!-- DEFERRED SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_rfsd_svac_frst_prov }}</span> <!-- REFUSED SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_wstge_svac_frst_prov }}</span> <!-- WASTAGE SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_p_cvrge_svac_frst_prov,2) }}%</span> <!-- PERCENT COVERAGE SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_c_rate_svac_frst_prov,2) }}%</span> <!-- CONSUMPTION RATE SINOVAC_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_r_unvcted_frst_svac_prov }}</span> <!-- REMAINING UNVACCINATED SINOVAC_FIRST -->
        </td>
    </tr>
    <tr style="background-color: #ffd8d6">
        <td>
            <span class="label label-warning">{{ $total_svac_a1_scnd_prov }}</span>   <!-- VACCINATED (A1) SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_svac_a2_scnd_prov }}</span> <!-- VACCINATED (A2) SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_vcted_svac_scnd_prov }}</span> <!-- TOTAL VACCINATED SINOVAC_SECOND -->
        </td> <!-- 1-4 -->
        <td>
            <span class="label label-warning">{{ $total_mild_svac_scnd_prov }}</span> <!-- MILD SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_srs_svac_scnd_prov }}</span> <!-- SERIOUS  SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_dfrd_svac_scnd_prov }}</span> <!-- DEFERRED  SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_rfsd_svac_scnd_prov }}</span> <!-- REFUSED  SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_wstge_svac_scnd_prov }}</span> <!-- WASTAGE SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_p_cvrge_svac_scnd_prov,2)}}%</span> <!-- PERCENT COVERAGE  SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_c_rate_svac_scnd_prov,2) }}%</span> <!-- CONSUMPTION RATE SINOVAC_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_r_unvcted_scnd_svac_prov }} </span> <!-- REMAINING UNVACCINATED  SINOVAC_SECOND -->
        </td>
    </tr>
    </tbody>
    <tbody id="collapse_astra_grand" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
    <tr style="background-color: #f2fcac">
        <td rowspan="2">

        </td> <!-- 1-5 -->
        <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_frtline_prov }}</td>  <!-- Frontline(A1) ASTRA_FIRST-->
        <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_sr_prov }}</td> <!-- SENIOR(A2) ASTRA_FIRST -->
        <td rowspan="2" style="color:black;">
        {{ $total_e_pop_astra_prov }} <!-- TOTAL E POP ASTRA_FIRST -->
        </td>
        <td rowspan="2" style="color:black">{{ $total_vallocated_astra_frst_prov }}</td> <!-- VACCINE_ALLOCATED (FD) ASTRA_FIRST-->
        <td rowspan="2" style="color:black">{{ $total_vallocated_astra_scnd_prov }}</td>  <!-- VACCINE ALLOCATED (SD) ASTRA_FIRST -->
        <td rowspan="2" style="color:black;">{{ $total_vallocated_frst_astra }}</td>  <!-- TOTAL VACCINE ALLOCATED ASTRA_FIRST -->
        <td style="color:black;">
            <span class="label label-success">{{ $total_astra_a1_frst_prov }}</span>  <!-- VACCINATED (A1) ASTRA_FIRST -->
        </td>
        <td style="color:black">
            <span class="label label-success">{{ $total_astra_a2_frst_prov }}</span> <!-- VACCINATED (A2) ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_vcted_astra_frst_prov }}</span> <!-- TOTAL VACCINATED ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_mild_astra_frst_prov }}</span> <!-- MILD ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_srs_astra_frst_prov }}</span> <!-- SERIOUS ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_dfrd_astra_frst_prov }}</span> <!-- DEFERRED ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_rfsd_astra_frst_prov }}</span> <!-- REFUSED ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_wstge_astra_frst_prov }}</span> <!-- WASTAGE ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_p_cvrge_astra_frst_prov,2) }}%</span> <!-- PERCENT_COVERAGE ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_c_rate_astra_frst_prov,2) }}%</span> <!-- CONSUMPTION RATE ASTRA_FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_r_unvcted_frst_astra_prov }}</span> <!-- REMAINUNG UNVACCINATED ASTRA_FIRST -->
        </td>
    </tr>
    <tr style="background-color: #f2fcac">
        <td style="color: black;">
            <span class="label label-warning">{{ $total_astra_a1_scnd_prov }}</span>  <!-- VACCINATED (A1) ASTRA_SECOND -->
        </td>
        <td style="color:black;">
            <span class="label label-warning">{{ $total_astra_a2_scnd_prov }}</span>  <!-- VACCINATED (A2) ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_vcted_astra_scnd_prov }}</span>  <!-- TOTAL VACCINATED ASTRA_SECOND -->
        </td> <!-- 1-6 -->
        <td>
            <span class="label label-warning">{{ $total_mild_astra_scnd_prov }}</span>  <!-- MILD ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_srs_astra_scnd_prov }}</span> <!-- SERIOUS ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_dfrd_astra_scnd_prov }}</span> <!-- DEFERRED ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_rfsd_astra_scnd_prov }}</span> <!-- REFUSED ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_wstge_astra_scnd_prov }}</span> <!-- WASTAGE ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_p_cvage_astra_scnd_prov,2) }}%</span> <!-- PERCENT_COVERAGE_ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_c_rate_astra_scnd_prov,2) }}%</span> <!-- CONSUMPTION RATE ASTRA_SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_r_unvcted_scnd_astra_prov }}</span> <!-- REMAINING UNVACCINATED ASTRA_SECOND -->
        </td>
    </tr>
    </tbody>
    <tbody>
    <tr>
        <td>Total</td> <!-- 1-7 -->
        <td>{{ $total_e_pop_astra_frtline_prov }}</td> <!-- TOTAL FRONTLINE -->
        <td>{{ $total_e_pop_astra_sr_prov }}</td>  <!-- TOTAL SR -->
        <td>{{ $total_e_pop_astra_prov }}</td>  <!-- TOTAL E POP  -->
        <td>{{ $total_vallocated_frst_prov }}</td>  <!-- TOTAL VACCINE ALLOCATED FIRST -->
        <td>{{ $total_vallocated_scnd_prov }} </td> <!-- TOTAL VACCINE ALLOCATED SECOND -->
        <td>
            <b>{{ $total_vallocated }} </b>  <!-- TOTAL VACCINE ALLOCATED  -->
        <td>
            <b class="label label-success" style="margin-right: 5%">{{$total_vcted_svac_frst_prov}}</b> <!-- TOTAL VACCINATED (A1) -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_vcted_svac_scnd_prov}}</b>  <!-- TOTAL VACCINATED (A2) -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_vcted_svac_prov }}</b>  <!-- TOTAL VACCINATED -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_mild_svac_frst_prov + $total_mild_astra_frst_prov }}</b>  <!-- TOTAL MILD -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_srs_svac_frst_prov + $total_srs_astra_frst_prov }}</b>  <!-- TOTAL SERIOUS -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_dfrd_svac_frst_prov + $total_dfrd_astra_frst_prov }}</b>  <!-- TOTAL DEFERRED -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_rfsd_frst_prov }}</b>  <!-- TOTAL REFUSED -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_wstge_svac_frst_prov + $total_wstge_astra_frst_prov }}</b>  <!-- TOTAL WASTAGE -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ number_format($total_p_cvrge_frst_prov,2) }}%</b>  <!-- TOTAL PERCENT_COVERAGE -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ number_format($total_c_rate_frst_prov,2) }}% </b>  <!-- TOTAL CONSUMPTION RATE -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_r_unvcted_all_frst_prov }}</b>  <!-- TOTAL REMAINUNG UNVACCINATED  -->
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
        <td>

            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_scnd_a1_prov }}</b> <!-- TOTAL VACCINATED (FD) 2 -->
        </td>
        <td>

            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_scnd_a2_prov }}</b> <!-- TOTAL VACCINATED (SD) 2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_astra_prov }}</b> <!-- TOTAL VACCINATED 2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_mild_svac_scnd_prov + $total_mild_astra_scnd_prov  }}</b> <!-- TOTAL MILD 2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_srs_svac_scnd_prov + $total_srs_astra_scnd_prov }}</b> <!-- TOTAL SERIOUS  2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_dfrd_svac_scnd_prov + $total_dfrd_astra_scnd_prov }}</b> <!-- TOTAL DEFERRED  2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_rfsd_scnd_prov }}</b> <!-- TOTAL REFUSED  2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_wstge_svac_scnd_prov + $total_wstge_astra_scnd_prov }}</b> <!-- TOTAL WASTAGE  2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ number_format($total_p_cvrge_scnd_prov,2) }}%</b> <!-- TOTAL PERCENT_COVERAGE  2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%"> {{ number_format($total_c_rate_scnd_prov,2) }}%</b> <!-- TOTAL CONSUMPTION RATE  2 -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_r_unvcted_all_scnd_prov }}</b> <!-- TOTAL REMAINING UNVACCIANTED  2 -->
        </td>
    </tr>
    </tbody>
</table>