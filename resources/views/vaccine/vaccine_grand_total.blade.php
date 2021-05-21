<hr>
<?php
    //ELIGIBLE POP SINOVAC
    $total_e_pop_svac_a1_prov =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a1 :0;    //A1 SINOVAC_FIRST goods
    $total_e_pop_svac_a2_prov  = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a2 :0; //A2 SINOVAC_FIRST goods
    $total_e_pop_svac_a3_prov  = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a3 :0; //A3 SINOVAC_FIRST goods
    $total_e_pop_svac_a4_prov  = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a4 :0; //A4 SINOVAC_FIRST goods
    $total_e_pop_svac_prov = $total_e_pop_svac_a1_prov + $total_e_pop_svac_a2_prov + $total_e_pop_svac_a3_prov + $total_e_pop_svac_a4_prov; //TOTAL ELI POP SINOVAC_FIRST goods
        
    //ELIGIBLE_POP_ASTRAZENECA
    $total_e_pop_astra_a1_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a1 :0; //A1 ASTRA_FIRST goods
    $total_e_pop_astra_a2_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a2 :0; //A2 ASTRA_FIRST goods
    $total_e_pop_astra_a3_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a3 :0; //A3 ASTRA_FIRST goods
    $total_e_pop_astra_a4_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a4 :0; //A4 ASTRA_FIRST goods
    $total_e_pop_astra_prov = $total_e_pop_astra_a1_prov + $total_e_pop_astra_a2_prov + $total_e_pop_astra_a3_prov + $total_e_pop_astra_a4_prov; //TOTAL E POP ASTRA_FIRST goods

    //ELIGIBLE_POP_SPUTNIKV
    //$total_e_pop_sputnikv_a1_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a1 :0; //A1 SPUTNIKV_FIRST goods
    //$total_e_pop_sputnikv_a2_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a2 :0; //A2 SPUTNIKV_FIRST goods
    //$total_e_pop_sputnikv_a3_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a3 :0; //A3 SPUTNIKV_FIRST goods
    //$total_e_pop_sputnikv_a4_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a4 :0; //A4 SPUTNIKV_FIRST goods
    //$total_e_pop_sputnikv_prov = $total_e_pop_sputnikv_a1_prov + $total_e_pop_sputnikv_a2_prov + $total_e_pop_sputnikv_a3_prov + $total_e_pop_sputnikv_a4_prov; //TOTAL E POP SPUTNIKV_FIRST goods

    //ELIGIBLE_POP_PFIZER
    //$total_e_pop_pfizer_a1_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a1 :0; //A1 PFIZER_FIRST goods
    //$total_e_pop_pfizer_a2_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a2 :0; //A2 PFIZER_FIRST goods
    //$total_e_pop_pfizer_a3_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a3 :0; //A3 PFIZER_FIRST goods
    //$total_e_pop_pfizer_a4_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->a4 :0; //A4 PFIZER_FIRST goods
    //$total_e_pop_pfizer_prov = $total_e_pop_pfizer_a1_prov + $total_e_pop_pfizer_a2_prov + $total_e_pop_pfizer_a3_prov + $total_e_pop_pfizer_a4_prov; //TOTAL E POP PFIZER_FIRST goods


    //VACCINE_ALLOCATED
    $total_vallocated_svac_frst_prov = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->sinovac_allocated_first :0; //VACCINE ALLOCATED (FD) SINOVAC_FIRST goods
    $total_vallocated_svac_scnd_prov = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->sinovac_allocated_second :0; //VACCINE ALLOCATED (SD) SINOVAC_FIRST goods
    $total_vallocated_astra_frst_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->astrazeneca_allocated_first :0; //VACCINE_ALLOCATED (FD) ASTRA_FIRST goods
    $total_vallocated_astra_scnd_prov= $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->astrazeneca_allocated_second :0; //VACCINE ALLOCATED (SD) ASTRA_FIRST goods

    //$total_vallocated_sputnikv_frst_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','')")[0]->sinovac_allocated_first :0; //VACCINE ALLOCATED (FD) SPUTNIKV_FIRST goods
    //$total_vallocated_sputnikv_scnd_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','')")[0]->sinovac_allocated_second :0; //VACCINE ALLOCATED (SD) SPUTNIKV_FIRST goods
    //$total_vallocated_pfizer_frst_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','')")[0]->astrazeneca_allocated_first :0; //VACCINE_ALLOCATED (FD) PFIZER_FIRST goods
    //$total_vallocated_pfizer_scnd_prov= $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','')")[0]->astrazeneca_allocated_second :0; //VACCINE ALLOCATED (SD) PFIZER_FIRST goods

    $total_svac_a1_frst_prov = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','a1')")[0]->vaccinated_first_a :0; //VACCINATED (A1) SINOVAC_FIRST
    $total_svac_a2_frst_prov = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','a2')")[0]->vaccinated_first_a :0; //VACCINATED (A2) SINOVAC_FIRST
    $total_svac_a3_frst_prov = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','a3')")[0]->vaccinated_first_a :0; //VACCINATED (A3) SINOVAC_FIRST
    $total_svac_a4_frst_prov = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','a4')")[0]->vaccinated_first_a :0; //VACCINATED (A4) SINOVAC_FIRST
    $total_svac_a1_scnd_prov = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','a1')")[0]->vaccinated_second_a :0; //VACCINATED (A1) SINOVAC_SECOND
    $total_svac_a2_scnd_prov = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','a2')")[0]->vaccinated_second_a :0; //VACCINATED (A2) SINOVAC_SECOND
    $total_svac_a3_scnd_prov = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','a3')")[0]->vaccinated_second_a :0; //VACCINATED (A3) SINOVAC_SECOND
    $total_svac_a4_scnd_prov = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','a4')")[0]->vaccinated_second_a :0; //VACCINATED (A4) SINOVAC_SECOND


    $total_astra_a1_frst_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','a1')")[0]->vaccinated_first_a :0; //VACCINATED (A1) ASTRA_FIRST
    $total_astra_a2_frst_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','a2')")[0]->vaccinated_first_a :0; //VACCINATED (A2) ASTRA_FIRST
    $total_astra_a3_frst_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','a3')")[0]->vaccinated_first_a :0; //VACCINATED (A3) ASTRA_FIRST
    $total_astra_a4_frst_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','a4')")[0]->vaccinated_first_a :0; //VACCINATED (A3) ASTRA_FIRST
    $total_astra_a1_scnd_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','a1')")[0]->vaccinated_second_a :0; //VACCINATED (A1) ASTRA_SECOND
    $total_astra_a2_scnd_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','a2')")[0]->vaccinated_second_a :0; //VACCINATED (A2) ASTRA_SECOND
    $total_astra_a3_scnd_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','a3')")[0]->vaccinated_second_a :0; //VACCINATED (A3) ASTRA_SECOND
    $total_astra_a4_scnd_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','a4')")[0]->vaccinated_second_a :0; //VACCINATED (A4) ASTRA_SECOND

    //$total_sputnikv_a1_frst_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','a1')")[0]->vaccinated_first_a :0; //VACCINATED (A1) SPUTNIKV_FIRST
    //$total_sputnikv_a2_frst_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','a2')")[0]->vaccinated_first_a :0; //VACCINATED (A2) SPUTNIKV_FIRST
    //$total_sputnikv_a3_frst_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','a3')")[0]->vaccinated_first_a :0; //VACCINATED (A3) SPUTNIKV_FIRST
    //$total_sputnikv_a4_frst_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','a4')")[0]->vaccinated_first_a :0; //VACCINATED (A4) SPUTNIKV_FIRST
    //$total_sputnikv_a1_scnd_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','a1')")[0]->vaccinated_second_a :0; //VACCINATED (A1) SPUTNIKV_SECOND
    //$total_sputnikv_a2_scnd_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','a2')")[0]->vaccinated_second_a :0; //VACCINATED (A2) SPUTNIKV_SECOND
    //$total_sputnikv_a3_scnd_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','a3')")[0]->vaccinated_second_a :0; //VACCINATED (A3) SPUTNIKV_SECOND
    //$total_sputnikv_a4_scnd_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','a4')")[0]->vaccinated_second_a :0; //VACCINATED (A4) SPUTNIKV_SECOND

    //$total_pfizer_a1_frst_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','a1')")[0]->vaccinated_first_a :0; //VACCINATED (A1) PFIZER_FIRST
    //$total_pfizer_a2_frst_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','a2')")[0]->vaccinated_first_a :0; //VACCINATED (A2) PFIZER_FIRST
    //$total_pfizer_a3_frst_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','a3')")[0]->vaccinated_first_a :0; //VACCINATED (A3) PFIZER_FIRST
    //$total_pfizer_a4_frst_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','a4')")[0]->vaccinated_first_a :0; //VACCINATED (A3) PFIZER_FIRST
    //$total_pfizer_a1_scnd_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','a1')")[0]->vaccinated_second_a :0; //VACCINATED (A1) PFIZER_SECOND
    //$total_pfizer_a2_scnd_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','a2')")[0]->vaccinated_second_a :0; //VACCINATED (A2) PFIZER_SECOND
    //$total_pfizer_a3_scnd_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','a3')")[0]->vaccinated_second_a :0; //VACCINATED (A3) PFIZER_SECOND
    //$total_pfizer_a4_scnd_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','a4')")[0]->vaccinated_second_a :0; //VACCINATED (A4) PFIZER_SECOND

    $total_vcted_a1_grand_first = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->vaccinated_first :0; //TOTAL VACCINATED A1 GRAND FIRST

    $total_mild_svac_frst_prov =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->mild_first :0; //MILD SINOVAC_FIRST goods
    $total_mild_astra_frst_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->mild_first :0; //MILD ASTRA_FIRST goods
    //$total_mild_sputnikv_frst_prov =  $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','')")[0]->mild_first :0; //MILD SPUTNIKV_FIRST goods
    //$total_mild_pfizer_frst_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','')")[0]->mild_first :0; //MILD PFIZER_FIRST goods


    $total_mild_svac_scnd_prov =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->mild_second :0; //MILD SINOVAC_SECOND goods
    $total_mild_astra_scnd_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->mild_second :0; //MILD ASTRA_SECOND goods
    //$total_mild_sputnikv_scnd_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','')")[0]->mild_second :0; //MILD SPUTNIKV_SECOND goods
    //$total_mild_pfizer_scnd_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','')")[0]->mild_second :0; //MILD PFIZER _SECOND goods

    $total_srs_svac_frst_prov =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->serious_first :0; //SERIOUS SINOVAC_FIRST goods
    $total_srs_astra_frst_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->serious_first :0; //SERIOUS ASTRA_FIRST goods
    //$total_srs_sputnikv_frst_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','')")[0]->serious_first :0; //SERIOUS SPUTNIKV_FIRST goods
    //$total_srs_pfizer_frst_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','')")[0]->serious_first :0; //SERIOUS PFIZER_FIRST goods

    $total_srs_svac_scnd_prov =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->serious_second :0; //SERIOUS  SINOVAC_SECOND goods
    $total_srs_astra_scnd_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->serious_second :0; //SERIOUS ASTRA_SECOND goods
    //$total_srs_sputnikv_scnd_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','')")[0]->serious_second :0; //SERIOUS SPUTNIKV_SECOND goods
    //$total_srs_pfizer_scnd_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','')")[0]->serious_second :0; //SERIOUS PFIZER_SECOND goods

    $total_dfrd_svac_frst_prov =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->deferred_first :0; //DEFERRED SINOVAC_FIRST goods
    $total_dfrd_astra_frst_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ?  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->deferred_first :0; //DEFERRED ASTRA_FIRST goods
    //$total_dfrd_sputnikv_frst_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ?  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','')")[0]->deferred_first :0; //DEFERRED SPUTNIKV_FIRST goods
    //$total_dfrd_pfizer_frst_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ?  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','')")[0]->deferred_first :0; //DEFERRED PFIZER_FIRST goods

    $total_dfrd_svac_scnd_prov =   $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->deferred_second :0; //DEFERRED  SINOVAC_SECOND goods
    $total_dfrd_astra_scnd_prov =  $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->deferred_second :0; //DEFERRED ASTRA_SECOND goods
    //$total_dfrd_sputnikv_scnd_prov =  $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','')")[0]->deferred_second :0; //DEFERRED SPUTNIKV_SECOND goods
    //$total_dfrd_pfizer_scnd_prov =  $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','')")[0]->deferred_second :0; //DEFERRED PFIZER_SECOND goods

    $total_rfsd_svac_frst_prov =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->refused_first :0; //REFUSED SINOVAC_FIRST goods
    $total_rfsd_astra_frst_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->refused_first :0; //REFUSED ASTRA_FIRST goods
    //$total_rfsd_sputnikv_frst_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','')")[0]->refused_first :0; //REFUSED SPUTNIKV_FIRST goods
    //$total_rfsd_pfizer_frst_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','')")[0]->refused_first :0; //REFUSED PFIZER_FIRST goods

    $total_rfsd_svac_scnd_prov =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ?  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->refused_second :0; //REFUSED  SINOVAC_SECOND goods
    $total_rfsd_astra_scnd_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ?  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->refused_second :0; //REFUSED ASTRA_SECOND goods
    //$total_rfsd_sputnikv_scnd_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ?  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','')")[0]->refused_second :0; //REFUSED SPUTNIKV_SECOND goods
    //$total_rfsd_pfizer_scnd_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ?  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','')")[0]->refused_second :0; //REFUSED PFIZER_SECOND goods

    $total_wstge_svac_frst_prov =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->wastage_first :0; //WASTAGE SINOVAC_FIRST goods
    $total_wstge_astra_frst_prov = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->wastage_first :0; //WASTAGE ASTRA_FIRST goods
    //$total_wstge_sputnikv_frst_prov = $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','')")[0]->wastage_first :0; //WASTAGE SPUTNIKV_FIRST goods
    //$total_wstge_pfizer_frst_prov = $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','')")[0]->wastage_first :0; //WASTAGE PFIZER_FIRST goods

    $total_wstge_svac_scnd_prov =   $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->wastage_second :0; //WASTAGE SINOVAC_SECOND goods
    $total_wstge_astra_scnd_prov =  $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->wastage_second :0; //WASTAGE ASTRA_SECOND goods
    //$total_wstge_sputnikv_scnd_prov =  $typeof_vaccine_filter == "SputnikV" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'SputnikV','')")[0]->wastage_second :0; //WASTAGE SPUTNIKV_SECOND goods
    //$total_wstge_pfizer_scnd_prov =  $typeof_vaccine_filter == "Pfizer" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Pfizer','')")[0]->wastage_second :0; //WASTAGE PFIZER_SECOND goods

    $total_vallocated_frst_svac = $total_vallocated_svac_frst_prov + $total_vallocated_svac_scnd_prov; //TOTAL VACCINE ALLOCATED SINOVAC_FIRST goods
    $total_vallocated_frst_astra = $total_vallocated_astra_frst_prov + $total_vallocated_astra_scnd_prov; //TOTAL VACCINE ALLOCATED ASTRA_FIRST goods
    $total_vallocated = $total_vallocated_frst_svac + $total_vallocated_frst_astra;

    $total_vallocated_frst_prov = $total_vallocated_svac_frst_prov + $total_vallocated_astra_frst_prov; //TOTAL VACCINE ALLOCATED FIRST goods
    $total_vallocated_scnd_prov = $total_vallocated_svac_scnd_prov + $total_vallocated_astra_scnd_prov; //TOTAL VACCINE ALLOCATED SECOND goods
    $total_vallocated = $total_vallocated_frst_prov + $total_vallocated_scnd_prov;


    $total_vcted_grand_a1_first = $total_svac_a1_frst_prov + $total_astra_a1_frst_prov; //TOTAL VACCINATED GRAND A1 FIRST DOSE
    $total_vcted_grand_a2_first = $total_svac_a2_frst_prov + $total_astra_a2_frst_prov; //TOTAL VACCINATED GRAND A2 FIRST DOSE
    $total_vcted_grand_a3_first = $total_svac_a3_frst_prov + $total_astra_a3_frst_prov; //TOTAL VACCINATED GRAND A3 FIRST DOSE
    $total_vcted_grand_a4_first = $total_svac_a4_frst_prov + $total_astra_a4_frst_prov; //TOTAL VACCINATED GRAND A4 FIRST DOSE

    $total_vcted_grand_a1_second = $total_svac_a1_scnd_prov + $total_astra_a1_scnd_prov; //TOTAL VACCINATED GRAND A1 SECOND DOSE
    $total_vcted_grand_a2_second = $total_svac_a2_scnd_prov + $total_astra_a2_scnd_prov; //TOTAL VACCINATED GRAND A2 SECOND DOSE
    $total_vcted_grand_a3_second = $total_svac_a3_scnd_prov + $total_astra_a3_scnd_prov; //TOTAL VACCINATED GRAND A3 SECOND DOSE
    $total_vcted_grand_a4_second = $total_svac_a4_scnd_prov + $total_astra_a4_scnd_prov; //TOTAL VACCINATED GRAND A4 SECOND DOSE


    $total_vcted_svac_first = $total_svac_a1_frst_prov + $total_svac_a2_frst_prov + $total_svac_a3_frst_prov + $total_svac_a4_frst_prov; //TOTAL VACCINATED SINOVAC_FIRST
    $total_vcted_svac_second = $total_svac_a1_scnd_prov + $total_svac_a2_scnd_prov +$total_svac_a3_scnd_prov +$total_svac_a4_scnd_prov; //TOTAL VACCINATED SINOVAC_SECOND

    $total_vcted_astra_first = $total_astra_a1_frst_prov + $total_astra_a2_frst_prov + $total_astra_a3_frst_prov + $total_astra_a4_frst_prov; //TOTAL VACCINATED ASTRA_FIRST
    $total_vcted_astra_second = $total_astra_a1_scnd_prov + $total_astra_a2_scnd_prov +$total_astra_a3_scnd_prov +$total_astra_a4_scnd_prov; //TOTAL VACCINATED ASTRA_SECOND

    $total_vcted_overall_first =  $total_vcted_grand_a1_first + $total_vcted_grand_a2_first +  $total_vcted_grand_a3_first + $total_vcted_grand_a4_first; //TOTAL VACCINATED OVERALL FIRST
    $total_vcted_overall_second =  $total_vcted_grand_a1_second + $total_vcted_grand_a2_second +  $total_vcted_grand_a3_second + $total_vcted_grand_a4_second; //TOTAL VACCINATED OVERALL SECOND

    $total_rfsd_frst_prov = $total_rfsd_svac_frst_prov + $total_rfsd_astra_frst_prov; //TOTAL REFUSED goods
    $total_rfsd_scnd_prov = $total_rfsd_svac_scnd_prov + $total_rfsd_astra_scnd_prov; //TOTAL REFUSED  2 goods


    $total_p_cvrge_frst_prov = $total_vcted_overall_first / $total_e_pop_astra_prov * 100; //TOTAL PERCENT_COVERAGE goods
    $total_p_cvrge_scnd_prov = $total_vcted_overall_second / $total_e_pop_astra_prov * 100; //TOTAL PERCENT_COVERAGE  2 goods

    $total_c_rate_frst_prov = $total_vcted_overall_first / $total_vallocated_frst_prov * 100; //TOTAL CONSUMPTION RATE goods
    $total_c_rate_scnd_prov =  $total_vcted_overall_second / $total_vallocated_scnd_prov * 100; //TOTAL CONSUMPTION RATE  2 goods


    $total_c_rate_svac_frst_prov = $total_vcted_svac_first / $total_vallocated_svac_frst_prov * 100; //CONSUMPTION RATE SINOVAC_FIRST goods
    $total_c_rate_astra_frst_prov = $total_vcted_astra_first / $total_vallocated_astra_frst_prov * 100; //CONSUMPTION RATE ASTRA_FIRST goods
    $total_c_rate_astra_scnd_prov = $total_vcted_astra_second / $total_vallocated_astra_scnd_prov * 100; //CONSUMPTION RATE ASTRA_SECOND goods
    $total_c_rate_svac_scnd_prov = $total_vcted_svac_second / $total_vallocated_svac_scnd_prov * 100; //CONSUMPTION RATE SINOVAC_SECOND goods

    $total_p_cvrge_svac_frst_prov = $total_vcted_svac_first / $total_e_pop_svac_prov * 100; //PERCENT COVERAGE SINOVAC_FIRST goods
    $total_p_cvrge_svac_scnd_prov =  $total_vcted_svac_second / $total_e_pop_svac_prov * 100; //PERCENT COVERAGE  SINOVAC_SECOND goods
    $total_p_cvrge_astra_frst_prov = $total_vcted_astra_first / $total_e_pop_astra_prov * 100; //PERCENT_COVERAGE ASTRA_FIRST goods
    $total_p_cvage_astra_scnd_prov =  $total_vcted_astra_second / $total_e_pop_astra_prov * 100; //PERCENT_COVERAGE_ASTRA_SECOND goods


    $total_r_unvcted_frst_svac_prov = $total_e_pop_svac_prov - $total_vcted_svac_first - $total_rfsd_svac_frst_prov; //REMAINING UNVACCINATED SINOVAC_FIRST goods
    $total_r_unvcted_frst_astra_prov = $total_e_pop_astra_prov - $total_vcted_astra_first - $total_rfsd_astra_frst_prov; //REMAINUNG UNVACCINATED ASTRA_FIRST goods
    $total_r_unvcted_scnd_svac_prov = $total_e_pop_svac_prov - $total_vcted_svac_second - $total_rfsd_svac_scnd_prov; //REMAINING UNVACCINATED  SINOVAC_SECOND goods
    $total_r_unvcted_scnd_astra_prov = $total_e_pop_astra_prov - $total_vcted_astra_second - $total_rfsd_astra_scnd_prov;  //REMAINING UNVACCINATED ASTRA_SECOND goods

    $total_r_unvcted_all_frst_prov = $total_e_pop_svac_prov - $total_vcted_overall_first - $total_rfsd_frst_prov; //TOTAL REMAINUNG UNVACCINATED goods //dara
    $total_r_unvcted_all_scnd_prov = $total_e_pop_svac_prov - $total_vcted_overall_second - $total_rfsd_scnd_prov; //TOTAL REMAINING UNVACCIANTED  2 goods

    $sinovac_dashboard = $total_vcted_svac_first + $total_vcted_svac_second;
    $astra_dashboard = $total_vcted_astra_first + $total_vcted_astra_second;

    $percent_coverage_firstdose =  number_format($total_p_cvrge_frst_prov,2);
    $percent_coverage_seconddose = number_format($total_p_cvrge_scnd_prov,2);

    $consumption_rate_firstdose = number_format($total_c_rate_frst_prov,2);
    $consumption_rate_seconddose = number_format($total_c_rate_scnd_prov,2);

    $a1_dashboard = $total_vcted_grand_a1_first + $total_vcted_grand_a1_second;
    $a2_dashboard = $total_vcted_grand_a2_first + $total_vcted_grand_a2_second;
    $a3_dashboard = $total_vcted_grand_a3_first + $total_vcted_grand_a3_second;
    $a4_dashboard = $total_vcted_grand_a4_first + $total_vcted_grand_a4_second;

    Session::put("sinovac_dashboard",$sinovac_dashboard);
    Session::put("astra_dashboard",$astra_dashboard);
    Session::put("percent_coverage_firstdose",$percent_coverage_firstdose);
    Session::put("percent_coverage_seconddose",$percent_coverage_seconddose);
    Session::put("consumption_rate_firstdose",$consumption_rate_firstdose);
    Session::put("consumption_rate_secondddose",$consumption_rate_seconddose);
    Session::put("a1_dashboard",$a1_dashboard);
    Session::put("a2_dashboard",$a2_dashboard);
    Session::put("a3_dashboard",$a3_dashboard);
    Session::put("a4_dashboard",$a4_dashboard);

    ?>

<h4>Grand Total</h4>
    <button class="btn btn-link collapsed" style="color: red;" type="button" data-toggle="collapse" data-target="#collapse_sinovac_grand" aria-expanded="false" aria-controls="collapse_sinovac_grandtotal">
        <b>Sinovac</b>
    </button>
    <button class="btn btn-link collapsed" style="color: darkgoldenrod;" type="button" data-toggle="collapse" data-target="#collapse_astra_grand" aria-expanded="false" aria-controls="collapse_astra_grandtotal">
        <b>Astrazeneca</b>
    </button>
    <button class="btn btn-link collapsed" style="color: #00a65a;" type="button" data-toggle="collapse" data-target="#collapse_sputnikv_grand" aria-expanded="false" aria-controls="collapse_sinovac_grandtotal">
        <b>Sputnik V</b>
    </button>
    <button class="btn btn-link collapsed" style="color: #00c0ef;" type="button" data-toggle="collapse" data-target="#collapse_pfizer_grand" aria-expanded="false" aria-controls="collapse_astra_grandtotal">
        <b>Pfizer</b>
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
            <td rowspan="2">{{ $total_e_pop_svac_a1_prov }}</td><!-- A1 SINOVAC_FIRST -->
            <td rowspan="2">{{ $total_e_pop_svac_a2_prov }}</td><!-- A2 SINOVAC_FIRST -->
            <td rowspan="2">{{ $total_e_pop_svac_a3_prov }}</td><!-- A3 SINOVAC_FIRST -->
            <td rowspan="2">{{ $total_e_pop_svac_a4_prov }}</td><!-- A4 SINOVAC_FIRST -->
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
                <span class="label label-success">{{ $total_svac_a3_frst_prov }}</span>  <!-- VACCINATED (A3) SINOVAC_FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ $total_svac_a4_frst_prov }}</span>  <!-- VACCINATED (A4) SINOVAC_FIRST -->
            </td>
            <td>
                <span class="label label-success"> {{ $total_vcted_svac_first }}</span>  <!-- TOTAL VACCINATED SINOVAC_FIRST -->
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
                <span class="label label-warning">{{ $total_svac_a3_scnd_prov }}</span> <!-- VACCINATED (A3) SINOVAC_SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ $total_svac_a4_scnd_prov }}</span> <!-- VACCINATED (A4) SINOVAC_SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ $total_vcted_svac_second }}</span> <!-- TOTAL VACCINATED SINOVAC_SECOND -->
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
            <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_a1_prov }}</td>  <!-- A1 ASTRA_FIRST -->
            <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_a2_prov }}</td>  <!-- A2 ASTRA_FIRST-->
            <td rowspan="2">{{ $total_e_pop_astra_a3_prov }}</td>  <!-- A3 ASTRA_FIRST -->
            <td rowspan="2">{{ $total_e_pop_astra_a4_prov }}</td>  <!-- A4 ASTRA_FIRST -->
            <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_prov }} <!-- TOTAL E POP ASTRA_FIRST --></td>
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
                <span class="label label-success">{{ $total_astra_a3_frst_prov }}</span> <!-- VACCINATED (A3) ASTRA_FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ $total_astra_a4_frst_prov }}</span> <!-- VACCINATED (A4) ASTRA_FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ $total_vcted_astra_first }}</span> <!-- TOTAL VACCINATED ASTRA_FIRST -->
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
                <span class="label label-warning">{{ $total_astra_a3_scnd_prov }}</span>  <!-- VACCINATED (A3) ASTRA_SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ $total_astra_a4_scnd_prov }}</span>  <!-- VACCINATED (A4) ASTRA_SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ $total_vcted_astra_second }}</span>  <!-- TOTAL VACCINATED ASTRA_SECOND -->
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
        <!-- SPUTNIKV -->
        <tbody id="collapse_sputnikv_grand" class="collapse bg-danger" aria-labelledby="headingTwo" data-parent="#accordionExample">
        <tr style="background-color: #b1ffdb">
            <td rowspan="2">

            </td> <!-- 1-3 -->
            <td rowspan="2">xx</td><!-- A1 SPUTNIKV_FIRST -->
            <td rowspan="2">xx</td><!-- A2 SPUTNIKV_FIRST -->
            <td rowspan="2">xx</td><!-- A3 SPUTNIKV_FIRST -->
            <td rowspan="2">xx</td><!-- A4 SPUTNIKV_FIRST -->
            <td rowspan="2">xx<!-- TOTAL ELI POP SPUTNIKV_FIRST -->
            </td>
            <td rowspan="2">xx</td> <!-- VACCINE ALLOCATED (FD) SPUTNIKV_FIRST -->
            <td rowspan="2">xx</td>  <!-- VACCINE ALLOCATED (SD) SPUTNIKV_FIRST -->
            <td rowspan="2">xx</td>  <!-- TOTAL VACCINE ALLOCATED SPUTNIKV_FIRST -->
            <td>
                <span class="label label-success">xx</span>   <!-- VACCINATED (A1) SPUTNIKV_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span>  <!-- VACCINATED (A2) SPUTNIKV_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span>  <!-- VACCINATED (A3) SPUTNIKV_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span>  <!-- VACCINATED (A4) SPUTNIKV_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span>  <!-- TOTAL VACCINATED SPUTNIKV_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span>  <!-- MILD SPUTNIKV_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- SERIOUS SPUTNIKV_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- DEFERRED SPUTNIKV_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- REFUSED SPUTNIKV_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- WASTAGE SPUTNIKV_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx%</span> <!-- PERCENT COVERAGE SPUTNIKV_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx%</span> <!-- CONSUMPTION RATE SPUTNIKV_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- REMAINING UNVACCINATED SPUTNIKV_FIRST -->
            </td>
        </tr>
        <tr style="background-color: #b1ffdb">
            <td>
                <span class="label label-warning">xx</span>   <!-- VACCINATED (A1) SPUTNIKV_SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- VACCINATED (A2) SPUTNIKV_SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- VACCINATED (A3) SPUTNIKV_SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- VACCINATED (A4) SPUTNIKV_SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- TOTAL VACCINATED SPUTNIKV_SECOND -->
            </td> <!-- 1-4 -->
            <td>
                <span class="label label-warning">xx</span> <!-- MILD SPUTNIKV_SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- SERIOUS  SPUTNIKV_SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- DEFERRED  SPUTNIKV_SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- REFUSED  SPUTNIKV_SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- WASTAGE SPUTNIKV_SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx%</span> <!-- PERCENT COVERAGE  SPUTNIKV_SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx%</span> <!-- CONSUMPTION RATE SPUTNIKV_SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- REMAINING UNVACCINATED  SPUTNIKV_SECOND -->
            </td>
        </tr>
        </tbody>
        <!-- PFIZER -->
        <tbody id="collapse_pfizer_grand" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
        <tr style="background-color: #8fe7fd">
            <td rowspan="2">

            </td> <!-- 1-5 -->
            <td rowspan="2" style="color:black;">xx</td>  <!-- A1 PFIZER_FIRST -->
            <td rowspan="2" style="color:black;">xx</td>  <!-- A2 PFIZER_FIRST-->
            <td rowspan="2">xx</td>  <!-- A3 PFIZER_FIRST -->
            <td rowspan="2">xx</td>  <!-- A4 PFIZER_FIRST -->
            <td rowspan="2" style="color:black;">xx <!-- TOTAL E POP PFIZER_FIRST --></td>
            <td rowspan="2" style="color:black">xx</td> <!-- VACCINE_ALLOCATED (FD) PFIZER_FIRST-->
            <td rowspan="2" style="color:black">xx</td>  <!-- VACCINE ALLOCATED (SD) PFIZER_FIRST -->
            <td rowspan="2" style="color:black;">xx</td>  <!-- TOTAL VACCINE ALLOCATED PFIZER_FIRST -->
            <td style="color:black;">
                <span class="label label-success">xx</span>  <!-- VACCINATED (A1) PFIZER_FIRST -->
            </td>
            <td style="color:black">
                <span class="label label-success">xx</span> <!-- VACCINATED (A2) PFIZER_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- VACCINATED (A3) PFIZER_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- VACCINATED (A4) PFIZER_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- TOTAL VACCINATED PFIZER_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- MILD PFIZER_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- SERIOUS PFIZER_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- DEFERRED PFIZER_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- REFUSED PFIZER_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- WASTAGE PFIZER_FIRST -->
            </td>
            <td>
                <span class="label label-success">xx%</span> <!-- PERCENT_COVERAGE PFIZER__FIRST -->
            </td>
            <td>
                <span class="label label-success">xx%</span> <!-- CONSUMPTION RATE PFIZER__FIRST -->
            </td>
            <td>
                <span class="label label-success">xx</span> <!-- REMAINUNG UNVACCINATED PFIZER_FIRST -->
            </td>
        </tr>
        <tr style="background-color: #8fe7fd">
            <td style="color: black;">
                <span class="label label-warning">xx</span>  <!-- VACCINATED (A1) PFIZER_SECOND -->
            </td>
            <td style="color:black;">
                <span class="label label-warning">xx</span>  <!-- VACCINATED (A2) PFIZER__SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span>  <!-- VACCINATED (A3) PFIZER__SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span>  <!-- VACCINATED (A4) PFIZER__SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span>  <!-- TOTAL VACCINATED PFIZER__SECOND -->
            </td> <!-- 1-6 -->
            <td>
                <span class="label label-warning">xx</span>  <!-- MILD PFIZER__SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- SERIOUS PFIZER__SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- DEFERRED PFIZER__SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- REFUSED PFIZER_SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- WASTAGE PFIZER__SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx%</span> <!-- PERCENT_COVERAGE_PFIZER__SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx%</span> <!-- CONSUMPTION RATE PFIZER_SECOND -->
            </td>
            <td>
                <span class="label label-warning">xx</span> <!-- REMAINING UNVACCINATED PFIZER__SECOND -->
            </td>
        </tr>
        </tbody>


    <tbody>
    <tr>
        <td>Total</td> <!-- 1-7 -->
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>{{ $total_vallocated_frst_prov }}</td>  <!-- TOTAL VACCINE ALLOCATED FIRST -->
        <td>{{ $total_vallocated_scnd_prov }} </td> <!-- TOTAL VACCINE ALLOCATED SECOND -->
        <td>
            <b>{{ $total_vallocated }} </b>  <!-- TOTAL VACCINE ALLOCATED  -->
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_vcted_grand_a1_first }}</b> <!-- TOTAL VACCINATED (A1) -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_vcted_grand_a2_first }}</b>  <!-- TOTAL VACCINATED (A2) -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_vcted_grand_a3_first }}</b>  <!-- TOTAL VACCINATED (A3) -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_vcted_grand_a4_first }}</b>  <!-- TOTAL VACCINATED (A4) -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_vcted_overall_first }}</b>  <!-- TOTAL VACCINATED OVERALL FIRST -->
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
        <td></td>
        <td></td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_grand_a1_second }}</b> <!-- TOTAL VACCINATED GRAND A1 SECOND DOSE -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_grand_a2_second }}</b><!-- TOTAL VACCINATED GRAND A2 SECOND DOSE -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_grand_a3_second }}</b>  <!-- TOTAL VACCINATED GRAND A3 SECOND DOSE  -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_grand_a4_second }}</b>  <!-- TOTAL VACCINATED GRAND A4 SECOND DOSE  -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_overall_second }}</b> <!-- TOTAL VACCINATED OVERALL SECOND -->
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