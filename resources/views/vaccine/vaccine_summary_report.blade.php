
<b style="font-size:20pt"><center>CENTRAL VISAYAS COVID-19 VACCINATION SUMMARY REPORT</center></b>
<b style="font-size:17pt"><center>Data via SMS from Implementing Units as of</center></b>
<b style="font-size:17pt"><center><p class="text-blue">{{ date('F d Y')  }}</p></center></b><br>


<div class="table-responsive">
    <table style="font-size: 8pt;" class="table" border="2">
        <thead>
        <tr style="background-color: #fbe4d5">
            <th><b style="font-size: 10pt">Province</b></th>
            <th colspan="5"><b style="font-size: 10pt"><center>Vaccine Allocation</center></b></th>
            <th colspan="1"><b style="font-size: 10pt"><center>Eligible Population (A1)</center></b></th>
            <th colspan="10"><b style="font-size: 10pt"><center>Vaccinated</center></b></th>
            <th colspan="5"><b style="font-size: 10pt"><center>1st dose % Coverage(Total 1st dose Vaccinated / Total Eligible A1)</center></b></th>
            <th colspan="5"><b style="font-size: 10pt"><center>2nd dose % Coverage(Total 1st dose Vaccinated / Total Eligible A1)</center></b></th>
            <th colspan="2"><b style="font-size: 10pt"><center>Total Refusal</center></b></th>
            <th colspan="2"><b style="font-size: 10pt"><center>Total Deferral</center></b></th>
            <th colspan="5"><b style="font-size: 10pt"><center>Wastage</center></b></th>
            <th colspan="5"><b style="font-size: 10pt"><center>1st dose Consumption Rate</center></b></th>
            <th colspan="5"><b style="font-size: 10pt"><center>2nd dose Consumption Rate</center></b></th>
        </tr>
        <tr style="background-color: #fbe4d5;">
            <td></td>
            <td>Sinovac</td>
            <td>Astra</td>
            <td>Sputnikv</td>
            <td>Pfizer</td>
            <td>Total</td>
            <td></td>
            <td><center>1st Dose Sinovac</center></td>
            <td><center>2nd Dose Sinovac</center></td>
            <td><center>1st Dose AZ</center></td>
            <td><center>2nd Dose AZ</center></td>
            <td><center>1st Dose Sputnikv</center></td>
            <td><center>2nd Dose Sputnikv</center></td>
            <td><center>1st Dose Pfizer</center></td>
            <td><center>2nd Dose Pfizer</center></td>
            <td><center>1st Dose Total</center></td>
            <td><center>2nd Dose Total</center></td>
            <td>Sinovac</td>
            <td>Astra</td>
            <td>Sputnikv</td>
            <td>Pfizer</td>
            <td>Total</td>
            <td>Sinovac</td>
            <td>Astra</td>
            <td>Sputnikv</td>
            <td>Pfizer</td>
            <td>Total</td>
            <td>1st Dose</td>
            <td>2nd Dose</td>
            <td>1st Dose</td>
            <td>2nd Dose</td>
            <td>Sinovac</td>
            <td>Astra</td>
            <td>Pfizer</td>
            <td>Sputnikv</td>
            <td>Total Wastage</td>
            <td>Sinovac</td>
            <td>AZ</td>
            <td>Pfizer</td>
            <td>Sputnikv</td>
            <td>Total</td>
            <td>Sinovac</td>
            <td>AZ</td>
            <td>Pfizer</td>
            <td>Sputnikv</td>
            <td>Total</td>
        </tr>
        <tr style="background-color: #e2efd9">
            <td>Region 7</td>
            <td>{{ $sinovac_region }}</td>
            <td>{{ $astra_region }}</td>
            <td>{{ $sputnikv_region }}</td>
            <td>{{ $pfizer_region }}</td>
            <td>{{ $total_region }}</td>
            <td><center>{{ $total_elipop_region }}</center></td>
            <td>{{ $region_sinovac_first_dose }}</td>
            <td>{{ $region_sinovac_second_dose }}</td>
            <td>{{ $region_astra_first_dose }}</td>
            <td>{{ $region_astra_second_dose }}</td>
            <td>{{ $region_sputnikv_first_dose }}</td>
            <td>{{ $region_sputnikv_second_dose }}</td>
            <td>{{ $region_pfizer_first_dose }}</td>
            <td>{{ $region_pfizer_second_dose }}</td>
            <td>{{ $first_dose_total }}</td>
            <td>{{ $second_dose_total }}</td>
            <td>{{ $total_p_cvrge_sinovac_region_first }}%</td>
            <td>{{$total_p_cvrge_astra_region_first}}%</td>
            <td>{{$total_p_cvrge_sputnikv_region_first}}%</td>
            <td>{{$total_p_cvrge_pfizer_region_first}}%</td>
            <td>{{ $total_p_cvrge_region_first }}%</td>
            <td>{{ $total_p_cvrge_sinovac_region_second }}%</td>
            <td>{{$total_p_cvrge_astra_region_second}}%</td>
            <td>{{$total_p_cvrge_sputnikv_region_second}}%</td>
            <td>{{$total_p_cvrge_pfizer_region_second}}%</td>
            <td>{{ $total_p_cvrge_region_second }}%</td>
            <td><center>{{ $total_refusal_first }}</center></td>
            <td><center>{{$total_refusal_second}}</center></td>
            <td><center>{{$total_deferred_first}}</center></td>
            <td><center>{{$total_deferred_second}}</center></td>
            <td><center>{{$wastage_sinovac_first}}</center></td>
            <td><center>{{$wastage_astra_first}}</center></td>
            <td><center>{{$wastage_sputnikv_first}}</center></td>
            <td><center>{{$wastage_pfizer_first}}</center></td>
            <td><center>{{$wastage_region}}</center></td>
            <td></td>
            <td></td>
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
            <td>Bohol</td>
            <td>{{ $sinovac_bohol }}</td>
            <td>{{ $astra_bohol }}</td>
            <td>{{ $sputnikv_bohol }}</td>
            <td>{{ $pfizer_bohol }}</td>
            <td>{{ $total_bohol }}</td>
            <td><center>{{ $eli_pop_bohol }}</center></td>
            <td>{{ $vcted_sinovac_bohol_first }}</td>
            <td>{{ $vcted_sinovac_bohol_second }}</td>
            <td>{{ $vcted_astra_bohol_first }}</td>
            <td>{{ $vcted_astra_bohol_second }}</td>
            <td>{{ $vcted_sputnikv_bohol_first }}</td>
            <td>{{ $vcted_sputnikv_bohol_second }}</td>
            <td>{{ $vcted_pfizer_bohol_first }}</td>
            <td>{{ $vcted_pfizer_bohol_second }}</td>
            <td>{{ $total_vcted_first_bohol}}</td>
            <td>{{ $total_vcted_second_bohol }}</td>
            <td><center>{{ $p_cvrge_sinovac_bohol_first }}%</center></td>
            <td><center>{{ $p_cvrge_astra_bohol_first }}%</center></td>
            <td><center>{{ $p_cvrge_sputnikv_bohol_first }}%</center></td>
            <td><center>{{ $p_cvrge_pfizer_bohol_first }}%</center></td>
            <td><center>{{ $total_p_cvrge_bohol_first }}</center></td>
            <td><center>{{ $p_cvrge_sinovac_bohol_second }}%</center></td>
            <td><center>{{ $p_cvrge_astra_bohol_second }}%</center></td>
            <td><center>{{ $p_cvrge_sputnikv_bohol_second }}%</center></td>
            <td><center>{{ $p_cvrge_pfizer_bohol_second }}%</center></td>
            <td><center>{{ $total_p_cvrge_bohol_second }}%</center></td>
            <td><center>{{ $refused_first_bohol }}</center></td>
            <td><center>{{ $refused_second_bohol }}</center></td>
            <td><center>{{ $deferred_first_bohol }}</center></td>
            <td><center>{{ $deferred_second_bohol }}</center></td>
            <td><center>{{ $wastage_sinovac_bohol_first }}</center></td>
            <td><center>{{ $wastage_astra_bohol_first }}</center></td>
            <td><center>{{ $wastage_sputnikv_bohol_first }}</center></td>
            <td><center>{{ $wastage_pfizer_bohol_first }}</center></td>
            <td><center>{{$total_wastage_bohol}}</center></td>
            <td><center>{{ $c_rate_sinovac_bohol_first }}</center></td>
            <td><center>{{ $c_rate_astra_bohol_first }}</center></td>
            <td><center>{{ $c_rate_sputnikv_bohol_first }}</center></td>
            <td><center>{{ $c_rate_pfizer_bohol_first }}</center></td>
            <td><center>{{ $total_c_rate_bohol }}</center></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Cebu</td>
            <td>{{ $sinovac_cebu }}</td>
            <td>{{ $astra_cebu }}</td>
            <td>{{ $sputnikv_cebu }}</td>
            <td>{{ $pfizer_cebu }}</td>
            <td>{{ $total_cebu_facility }}</td>
            <td><center>{{ $eli_pop_cebu }}</center></td>
            <td>{{ $vcted_sinovac_cebu_first }}</td>
            <td>{{ $vcted_sinovac_cebu_second }}</td>
            <td>{{ $vcted_astra_cebu_first }}</td>
            <td>{{ $vcted_astra_cebu_second }}</td>
            <td>{{ $vcted_sputnikv_cebu_first }}</td>
            <td>{{ $vcted_sputnikv_cebu_second }}</td>
            <td>{{ $vcted_pfizer_cebu_first }}</td>
            <td>{{ $vcted_pfizer_cebu_second }}</td>
            <td>{{ $total_vcted_first_cebu}}</td>
            <td>{{ $total_vcted_second_cebu }}</td>
            <td><center>{{ $p_cvrge_sinovac_cebu_first }}%</center></td>
            <td><center>{{ $p_cvrge_astra_cebu_first }}%</center></td>
            <td><center>{{ $p_cvrge_sputnikv_cebu_first }}%</center></td>
            <td><center>{{ $p_cvrge_pfizer_cebu_first }}%</center></td>
            <td><center>{{ $total_p_cvrge_cebu_first }}</center></td>
            <td><center>{{ $p_cvrge_sinovac_cebu_second }}%</center></td>
            <td><center>{{ $p_cvrge_astra_cebu_second }}%</center></td>
            <td><center>{{ $p_cvrge_sputnikv_cebu_second }}%</center></td>
            <td><center>{{ $p_cvrge_pfizer_cebu_second }}%</center></td>
            <td><center>{{ $total_p_cvrge_cebu_second }}%</center></td>
            <td><center>{{ $refused_first_cebu }}</center></td>
            <td><center>{{ $refused_second_cebu }}</center></td>
            <td><center>{{ $deferred_first_cebu }}</center></td>
            <td><center>{{ $deferred_second_cebu }}</center></td>
            <td><center>{{ $wastage_sinovac_cebu_first }}</center></td>
            <td><center>{{ $wastage_astra_cebu_first }}</center></td>
            <td><center>{{ $wastage_sputnikv_cebu_first }}</center></td>
            <td><center>{{ $wastage_pfizer_cebu_first }}</center></td>
            <td><center>{{$total_wastage_cebu}}</center></td>
            <td><center>{{ $c_rate_sinovac_cebu_first }}</center></td>
            <td><center>{{ $c_rate_astra_cebu_first }}</center></td>
            <td><center>{{ $c_rate_sputnikv_cebu_first }}</center></td>
            <td><center>{{ $c_rate_pfizer_cebu_first }}</center></td>
            <td><center>{{ $total_c_rate_cebu }}</center></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Negros Or.</td>
            <td>{{ $sinovac_negros }}</td>
            <td>{{ $astra_negros }}</td>
            <td>{{ $sputnikv_negros }}</td>
            <td>{{ $pfizer_negros }}</td>
            <td>{{ $total_negros }}</td>
            <td><center>{{ $eli_pop_negros }}</center></td>
            <td>{{ $vcted_sinovac_negros_first }}</td>
            <td>{{ $vcted_sinovac_negros_second }}</td>
            <td>{{ $vcted_astra_negros_first }}</td>
            <td>{{ $vcted_astra_negros_second }}</td>
            <td>{{ $vcted_sputnikv_negros_first }}</td>
            <td>{{ $vcted_sputnikv_negros_second }}</td>
            <td>{{ $vcted_pfizer_cebu_first }}</td>
            <td>{{ $vcted_pfizer_cebu_second }}</td>
            <td>{{ $total_vcted_first_negros}}</td>
            <td>{{ $total_vcted_second_negros }}</td>
            <td><center>{{ $p_cvrge_sinovac_negros_first }}%</center></td>
            <td><center>{{ $p_cvrge_astra_negros_first }}%</center></td>
            <td><center>{{ $p_cvrge_sputnikv_negros_first }}%</center></td>
            <td><center>{{ $p_cvrge_pfizer_negros_first }}%</center></td>
            <td><center>{{ $total_p_cvrge_negros_first }}</center></td>
            <td><center>{{ $p_cvrge_sinovac_negros_second }}%</center></td>
            <td><center>{{ $p_cvrge_astra_negros_second }}%</center></td>
            <td><center>{{ $p_cvrge_sputnikv_negros_second }}%</center></td>
            <td><center>{{ $p_cvrge_pfizer_negros_second }}%</center></td>
            <td><center>{{ $total_p_cvrge_negros_second }}%</center></td>
            <td><center>{{ $refused_first_negros }}</center></td>
            <td><center>{{ $refused_second_negros }}</center></td>
            <td><center>{{ $deferred_first_negros }}</center></td>
            <td><center>{{ $deferred_second_negros }}</center></td>
            <td><center>{{ $wastage_sinovac_negros_first }}</center></td>
            <td><center>{{ $wastage_astra_negros_first }}</center></td>
            <td><center>{{ $wastage_sputnikv_negros_first }}</center></td>
            <td><center>{{ $wastage_pfizer_negros_first }}</center></td>
            <td><center>{{$total_wastage_negros}}</center></td>
            <td><center>{{ $c_rate_sinovac_negros_first }}</center></td>
            <td><center>{{ $c_rate_astra_negros_first }}</center></td>
            <td><center>{{ $c_rate_sputnikv_negros_first }}</center></td>
            <td><center>{{ $c_rate_pfizer_negros_first }}</center></td>
            <td><center>{{ $total_c_rate_negros }}</center></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Siquijor</td>
            <td>{{ $sinovac_siquijor }}</td>
            <td>{{ $astra_siquijor }}</td>
            <td>{{ $sputnikv_siquijor }}</td>
            <td>{{ $pfizer_siquijor }}</td>
            <td>{{ $total_siquijor }}</td>
            <td><center>{{ $eli_pop_siquijor }}</center></td>
            <td>{{ $vcted_sinovac_siquijor_first }}</td>
            <td>{{ $vcted_sinovac_siquijor_second }}</td>
            <td>{{ $vcted_astra_siquijor_first }}</td>
            <td>{{ $vcted_astra_siquijor_second }}</td>
            <td>{{ $vcted_sputnikv_siquijor_first }}</td>
            <td>{{ $vcted_sputnikv_siquijor_second }}</td>
            <td>{{ $vcted_pfizer_cebu_first }}</td>
            <td>{{ $vcted_pfizer_cebu_second }}</td>
            <td>{{ $total_vcted_first_siquijor}}</td>
            <td>{{ $total_vcted_second_siquijor }}</td>
            <td><center>{{ $p_cvrge_sinovac_siquijor_first }}%</center></td>
            <td><center>{{ $p_cvrge_astra_siquijor_first }}%</center></td>
            <td><center>{{ $p_cvrge_sputnikv_siquijor_first }}%</center></td>
            <td><center>{{ $p_cvrge_pfizer_siquijor_first }}%</center></td>
            <td><center>{{ $total_p_cvrge_siquijor_first }}</center></td>
            <td><center>{{ $p_cvrge_sinovac_siquijor_second }}%</center></td>
            <td><center>{{ $p_cvrge_astra_siquijor_second }}%</center></td>
            <td><center>{{ $p_cvrge_sputnikv_siquijor_second }}%</center></td>
            <td><center>{{ $p_cvrge_pfizer_siquijor_second }}%</center></td>
            <td><center>{{ $total_p_cvrge_siquijor_second }}%</center></td>
            <td><center>{{ $refused_first_siquijor }}</center></td>
            <td><center>{{ $refused_second_siquijor }}</center></td>
            <td><center>{{ $deferred_first_siquijor }}</center></td>
            <td><center>{{ $deferred_second_siquijor }}</center></td>
            <td><center>{{ $wastage_sinovac_siquijor_first }}</center></td>
            <td><center>{{ $wastage_astra_siquijor_first }}</center></td>
            <td><center>{{ $wastage_sputnikv_siquijor_first }}</center></td>
            <td><center>{{ $wastage_pfizer_siquijor_first }}</center></td>
            <td><center>{{$total_wastage_siquijor}}</center></td>
            <td><center>{{ $c_rate_sinovac_siquijor_first }}</center></td>
            <td><center>{{ $c_rate_astra_siquijor_first }}</center></td>
            <td><center>{{ $c_rate_sputnikv_siquijor_first }}</center></td>
            <td><center>{{ $c_rate_pfizer_siquijor_first }}</center></td>
            <td><center>{{ $total_c_rate_siquijor }}</center></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Cebu City</td>
            <td>{{ $sinovac_cebu_facility }}</td>
            <td>{{ $astra_cebu_facility }}</td>
            <td>{{ $sputnikv_cebu_facility }}</td>
            <td>{{ $pfizer_cebu_facility }}</td>
            <td>{{ $total_cebu_facility }}</td>
            <td><centeR>{{ $eli_pop_cebu_facility }}</centeR></td>
            <td>{{ $vcted_sinovac_cebu_facility_first }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
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
            <td>Mandaue City</td>
            <td>{{ $sinovac_mandaue_facility }}</td>
            <td>{{ $astra_mandaue_facility }}</td>
            <td>{{ $sputnikv_mandaue_facility }}</td>
            <td>{{ $pfizer_mandaue_facility }}</td>
            <td>{{ $total_mandaue_facility }}</td>
            <td><center>{{ $eli_pop_mandaue_facility }}</center></td>
            <td>{{ $vcted_sinovac_mandaue_first }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
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
            <td>Lapu-lapu City</td>
            <td>{{ $sinovac_lapu_facility }}</td>
            <td>{{ $astra_lapu_facility }}</td>
            <td>{{ $sputnikv_lapu_facility }}</td>
            <td>{{ $pfizer_lapu_facility }}</td>
            <td>{{ $total_lapu_facility }}</td>
            <td><center>{{ $eli_pop_lapu_facility }}</center></td>
            <td>{{ $vcted_sinovac_lapu_first }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </thead>
    </table>
</div>


