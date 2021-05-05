<style>
    input[type=radio] {
        width: 20%;
        height: 2em;
    }
</style>
<div class="modal-header">
    <h3 id="myModalLabel"><i class="fa fa-location-arrow" style="color:green"></i> {{ \App\Muncity::find($muncity_id)->description }}</h3>
</div>
 <form action="{{ asset('vaccine/saved') }}" method="POST" id="form_submit" autocomplete="off">
        {{ csrf_field() }}
        <input type="hidden" name="vaccine_id" value="{{ $vaccine->id }}">
        <br>
        <table class="table" style="font-size: 8pt">
            <thead class="bg-gray">
                <tr>
                    <th>Dose Date</th>
                    <th width="15%">Type Of Vaccine</th>
                    <th>Priority</th>
                    <th>Vaccinated</th>
                    <th>Mild</th>
                    <th>Serious</th>
                    <th>Refused</th>
                    <th>Deferred</th>
                    <th>Wastage</th>

                </tr>
            </thead>
            <?php
                //TOTAL PERCENT COVERAGE
                $total_p_cvrge_frst = 0;
                $total_p_cvrge_scnd = 0;

               //TOTAL_VACCINATED_SINOVAC
                $total_e_pop_svac_a1 = 0;
                $total_e_pop_svac_a1_flag = true;
                $total_e_pop_svac_a2 = 0;
                $total_e_pop_svac_a2_flag = true;
                $total_e_pop_svac_a3 = 0;
                $total_e_pop_svac_a3_flag = true;
                $total_e_pop_svac_a4 = 0;
                $total_e_pop_svac_a4_flag = true;
                $total_vallocated_svac = 0;
                $total_vallocated_svac_flag = true;
                $total_vallocated_svac_frst = 0;
                $total_vallocated_svac_scnd = 0;
                $total_vcted_svac_frst = 0;
                $total_vcted_svac_scnd = 0;
                $total_mild_svac_frst = 0;
                $total_mild_svac_scnd = 0;
                $total_srs_svac_frst = 0;
                $total_srs_svac_scnd = 0;
                $total_dfrd_svac_frst = 0;
                $total_dfrd_svac_scnd = 0;
                $total_rfsd_svac_frst = 0;
                $total_rfsd_svac_scnd = 0;
                $total_wstge_svac_frst = 0;
                $total_wstge_svac_scnd = 0;


                //TOTAL_VACCINATED_ASTRAZENECA
                $total_e_pop_astra_a1 = 0;
                $total_e_pop_astra_a1_flag = true;
                $total_e_pop_astra_a2 = 0;
                $total_e_pop_astra_a2_flag = true;
                $total_e_pop_astra_a3 = 0;
                $total_e_pop_astra_a3_flag = true;
                $total_e_pop_astra_a4 = 0;
                $total_e_pop_astra_a4_flag = true;
                $total_vallocated_astra = 0;
                $total_vallocated_astra_flag = true;
                $total_vallocated_astra_frst = 0;
                $total_vallocated_astra_scnd = 0;
                $total_vcted_astra_frst = 0;
                $total_vcted_astra_scnd = 0;
                $total_mild_astra_frst = 0;
                $total_mild_astra_scnd = 0;
                $total_srs_astra_frst = 0;
                $total_srs_astra_scnd = 0;
                $total_dfrd_astra_frst = 0;
                $total_dfrd_astra_scnd = 0;
                $total_rfsd_astra_frst = 0;
                $total_rfsd_astra_scnd = 0;
                $total_wstge_astra_frst = 0;
                $total_wstge_astra_scnd = 0;


                //SINOVAC
                $total_svac_a1_frst = 0;
                $total_svac_a2_frst = 0;
                $total_svac_a3_frst = 0;
                $total_svac_a4_frst = 0;
                $total_svac_a1_scnd = 0;
                $total_svac_a2_scnd = 0;
                $total_svac_a3_scnd = 0;
                $total_svac_a4_scnd = 0;


                //ASTRAZENECA
                $total_astra_a1_frst = 0;
                $total_astra_a2_frst = 0;
                $total_astra_a3_frst = 0;
                $total_astra_a4_frst = 0;
                $total_astra_a1_scnd = 0;
                $total_astra_a2_scnd = 0;
                $total_astra_a3_scnd = 0;
                $total_astra_a4_scnd = 0;


            ?>
            @if(count($vaccine_accomplishment)>0)
                @foreach($vaccine_accomplishment as $vaccine)
                    <?php
                        //modified vaccine accomplishment table
                        if($vaccine->priority == 'a1'){
                            $muncity = \App\Muncity::find($vaccine->muncity_id)->first();
                            $vaccine->no_eli_pop = $muncity->a1;
                        }
                        elseif($vaccine->priority == 'a2'){
                            $muncity = \App\Muncity::find($vaccine->muncity_id)->first();
                            $vaccine->no_eli_pop = $muncity->a2;
                        }
                        elseif($vaccine->priority == 'a3'){
                            $muncity = \App\Muncity::find($vaccine->muncity_id)->first();
                            $vaccine->no_eli_pop = $muncity->a3;
                        }
                        elseif($vaccine->priority == 'a4'){
                            $muncity = \App\Muncity::find($vaccine->muncity_id)->first();
                            $vaccine->no_eli_pop = $muncity->a4;
                        }

                        if($vaccine->typeof_vaccine == 'Sinovac'){
                            $muncity = \App\Muncity::find($vaccine->muncity_id)->first();
                            $vaccine->vaccine_allocated_first = $muncity->sinovac_allocated_first;
                            $vaccine->vaccine_allocated_second = $muncity->sinovac_allocated_second;
                            $total_vallocated_svac_frst += $muncity->sinovac_allocated_first; //VACCINE ALLOCATED(FD) SINOVAC FIRST
                            $total_vallocated_svac_scnd += $muncity->sinovac_allocated_second; //VACCINE ALLOCATED(SD) SINOVAC FIRST
                        }
                        elseif($vaccine->typeof_vaccine == 'Astrazeneca'){
                            $muncity = \App\Muncity::find($vaccine->muncity_id)->first();
                            $vaccine->vaccine_allocated_first = $muncity->astrazeneca_allocated_first;
                            $vaccine->vaccine_allocated_second = $muncity->astrazeneca_allocated_second;
                            $total_vallocated_astra_frst += $muncity->astrazeneca_allocated_first; //VACCINE ALLOCATED(FD) ASTRA FIRST
                            $total_vallocated_astra_scnd += $muncity->astrazeneca_allocated_second; //VACCINE ALLOCATED(SD) ASTRA FIRST
                        }



                        if($vaccine->typeof_vaccine == "Sinovac"){
                            $total_e_pop_svac_a1 = $muncity->a1; //A1 SINOVAC FIRST
                            $total_e_pop_svac_a2 = $muncity->a2; //A2 SINOVAC FIRST
                            $total_e_pop_svac_a3 = $muncity->a3; //A3 SINOVAC FIRST
                            $total_e_pop_svac_a4 = $muncity->a4; //A4 SINOVAC FIRST
                            if($vaccine->priority == "a1"){
                                $total_svac_a1_frst += $vaccine->vaccinated_first; //VACCINATED (A1) SINOVAC FIRST
                                $total_svac_a1_scnd += $vaccine->vaccinated_second; //VACCINATED (A1) SINOVAC SECOND
                            }
                            elseif($vaccine->priority == "a2"){
                                $total_svac_a2_frst += $vaccine->vaccinated_first; //VACCINATED (A2) SINOVAC FIRST
                                $total_svac_a2_scnd += $vaccine->vaccinated_second; //VACCINATED (A2) SINOVAC SECOND

                            }
                            elseif($vaccine->priority == "a3"){
                                $total_svac_a3_frst += $vaccine->vaccinated_first; //VACCINATED (A3) SINOVAC FIRST
                                $total_svac_a3_scnd += $vaccine->vaccinated_second; //VACCINATED (A3) SINOVAC SECOND

                            }
                            elseif($vaccine->priority == "a4"){
                                $total_svac_a4_frst += $vaccine->vaccinated_first; //VACCINATED (A4) SINOVAC FIRST
                                $total_svac_a4_scnd += $vaccine->vaccinated_second; //VACCINATED (A4) SINOVAC SECOND

                            }
                            if($total_vallocated_svac_flag){
                                $total_vallocated_svac += $vaccine->vaccine_allocated;
                                $total_vallocated_svac_flag = false;
                            }
                            $total_vcted_svac_frst += $vaccine->vaccinated_first; //TOTAL VACCINATED SINOVAC FIRST
                            $total_vcted_svac_scnd += $vaccine->vaccinated_second; //TOTAL VACCINATED SINOVAC SECOND
                            $total_mild_svac_frst += $vaccine->mild_first; //MILD SINOVAC  FIRST
                            $total_mild_svac_scnd += $vaccine->mild_second; //MILD SINOVAC SECOND
                            $total_srs_svac_frst += $vaccine->serious_first; //SERIOUS SINOVAC FIRST
                            $total_srs_svac_scnd += $vaccine->serious_second; //SERIOUS SINOVAC SECOND
                            $total_dfrd_svac_frst += $vaccine->deferred_first; //DEFERRED SINOVAC FIRST
                            $total_dfrd_svac_scnd += $vaccine->deferred_second; //DEFERRED SINOVAC SECOND
                            $total_rfsd_svac_frst += $vaccine->refused_first; //REFUSED SINOVAC  FIRST
                            $total_rfsd_svac_scnd += $vaccine->refused_second; //REFUSED SINOVAC SECOND
                            $total_wstge_svac_frst += $vaccine->wastage_first; //WASTAGE SINOVAC  FIRST
                            $total_wstge_svac_scnd += $vaccine->wastage_second; //WASTAGE SINOVAC SECOND
                        }

                        if($vaccine->typeof_vaccine == "Astrazeneca"){
                            $total_e_pop_astra_a1 = $muncity->a1;
                            $total_e_pop_astra_a2 = $muncity->a2;
                            $total_e_pop_astra_a3 = $muncity->a3;
                            $total_e_pop_astra_a4 = $muncity->a4;
                            if($vaccine->priority == "a1"){
                                $total_astra_a1_frst += $vaccine->vaccinated_first; // VACCINATED (A1) ASTRA FIRST
                                $total_astra_a1_scnd += $vaccine->vaccinated_second; //VACCINATED (A1) ASTRA SECOND
                            }
                            elseif($vaccine->priority == "a2" ){
                                $total_astra_a2_frst += $vaccine->vaccinated_first; //VACCINATED (A2) ASTRA FIRST
                                $total_astra_a2_scnd += $vaccine->vaccinated_second; //VACCINATED (A2) ASTRA SECOND
                            }
                            elseif($vaccine->priority == "a3" ){
                                $total_astra_a3_frst += $vaccine->vaccinated_first; //VACCINATED (A3) ASTRA FIRST
                                $total_astra_a3_scnd += $vaccine->vaccinated_second; //VACCINATED (A3) ASTRA SECOND
                            }
                            elseif($vaccine->priority == "a4" ){
                                $total_astra_a4_frst += $vaccine->vaccinated_first; //VACCINATED (A4) ASTRA FIRST
                                $total_astra_a4_scnd += $vaccine->vaccinated_second; //VACCINATED (A4) ASTRA SECOND
                            }
                            if($total_vallocated_astra_flag){
                                $total_vallocated_astra += $vaccine->vaccine_allocated;
                                $total_vallocated_astra_flag = false;
                            }

                            $total_vcted_astra_frst += $vaccine->vaccinated_first; //TOTAL VACCINATED  ASTRA FIRST
                            $total_vcted_astra_scnd += $vaccine->vaccinated_second; //TOTAL VACCINATED ASTRA SECOND
                            $total_mild_astra_frst += $vaccine->mild_first; //MILD ASTRA FIRST
                            $total_mild_astra_scnd += $vaccine->mild_second; //MILD ASTRA SECOND
                            $total_srs_astra_frst += $vaccine->serious_first; //SERIOUS ASTRA FIRST
                            $total_srs_astra_scnd += $vaccine->serious_second; //SERIOUS ASTRA SECOND
                            $total_dfrd_astra_frst += $vaccine->deferred_first; //DEFERRED ASTRA FIRST
                            $total_dfrd_astra_scnd += $vaccine->deferred_second; //DEFERRED ASTRA SECOND
                            $total_rfsd_astra_frst += $vaccine->refused_first; //REFUSED ASTRA FIRST
                            $total_rfsd_astra_scnd += $vaccine->refused_second; //REFUSED ASTRA SECOND
                            $total_wstge_astra_frst += $vaccine->wastage_first; //WASTAGE ASTRA FIRST
                            $total_wstge_astra_scnd += $vaccine->wastage_second; //WASTAGE ASTRA SECOND

                        }


                            $total_allocated_overall_svac_frst = $total_vallocated_svac_frst + $total_vallocated_svac_scnd; //VACCINE ALLOCATED TOTAL SINOVAC
                            $total_allocated_overall_astra_frst = $total_vallocated_astra_frst + $total_vallocated_astra_scnd; //TOTAL VACCINE ALLOCATED ASTRA FIRST


                            $total_e_pop_svac = $total_e_pop_svac_a1 +$total_e_pop_svac_a2 + $total_e_pop_svac_a3 + $total_e_pop_svac_a4;  //ELIPOP TOTAL SINOVAC FIRST
                            $total_e_pop_astra = $total_e_pop_astra_a1 + $total_e_pop_astra_a2 + $total_e_pop_astra_a3 + $total_e_pop_astra_a4 ;  //ELIPOP TOTAL ASTRA FIRST
                            $total_e_pop = $muncity->a1 + $muncity->a2 + $muncity->a3 + $muncity->a4; //TOTAL_ELI_POP

                            $p_cvrge_astra_frst = ($total_vcted_astra_frst / $total_e_pop_astra) * 100; //PERCENT COVERAGE ASTRA FIRST
                            $p_cvrge_astra_scnd = ($total_vcted_astra_scnd / $total_e_pop_astra) * 100; //PERCENT COVERAGE ASTRA SECOND
                            $p_cvrge_svac_frst = ($total_vcted_svac_frst / $total_e_pop_svac) * 100; //PERCENT COVERAGE SINOVAC FIRST
                            $p_cvrge_svac_scnd = ($total_vcted_svac_scnd / $total_e_pop_svac) * 100; //PERCENT COVERAGE SINOVAC SECOND


                            $total_allocated_frst = $total_vallocated_svac_frst + $total_vallocated_astra_frst; //TOTAL ALLOCATED_FIRST
                            $total_allocated_scnd = $total_vallocated_svac_scnd + $total_vallocated_astra_scnd; //TOTAL ALLOCATED_SECOND
                            $total_allocated = $total_allocated_frst + $total_allocated_scnd; //TOTAL_ALLOCATED

                            $total_vcted_frst = $total_vcted_svac_frst + $total_vcted_astra_frst; //TOTAL_VACCINATED
                            $total_vcted_scnd = $total_vcted_svac_scnd + $total_vcted_astra_scnd; //TOTAL_VACCINATED 2


                            $total_mild_frst = $total_mild_svac_frst + $total_mild_astra_frst; //TOTAL_MILD
                            $total_mild_scnd = $total_mild_svac_scnd + $total_mild_astra_scnd; //TOTAL_MILD 2

                            $total_srs_frst = $total_srs_svac_frst + $total_srs_astra_frst; //TOTAL_SERIOUS
                            $total_srs_scnd = $total_srs_svac_scnd + $total_srs_astra_scnd; //TOTAL_SERIOUS 2

                            $total_dfrd_frst = $total_dfrd_svac_frst + $total_dfrd_astra_frst; //TOTAL_DEFERRED
                            $total_dfrd_scnd = $total_dfrd_svac_scnd + $total_dfrd_astra_scnd; //TOTAL_DEFERRED 2

                            $total_rfsd_frst = $total_rfsd_svac_frst + $total_rfsd_astra_frst; //TOTAL_REFUSED
                            $total_rfsd_scnd = $total_rfsd_svac_scnd + $total_rfsd_astra_scnd; //TOTAL_REFUSED 2

                            $total_wstge_frst = $total_wstge_svac_frst + $total_wstge_astra_frst; //TOTAL_WASTAGE
                            $total_wstge_scnd = $total_wstge_svac_scnd + $total_wstge_astra_scnd; //TOTAL_WASTAGE 2

                            $total_p_cvrge_frst = $total_vcted_frst / $total_e_pop * 100; //TOTAL_PERCENT_COVERAGE
                            $total_p_cvrge_scnd = $total_vcted_scnd / $total_e_pop * 100; //TOTAL_PERCENT_COVERAGE_2



                            $total_remaining = $total_e_pop - $total_vcted_frst - $total_rfsd_frst; //TOTAL_REMAINING_UNVACCINATED
                            $total_remaining_scnd = $total_e_pop - $total_vcted_scnd - $total_rfsd_scnd; //TOTAL_REMAINING_UNVACCINATED 2


                            $total_c_rate_svac_frst =  $total_vcted_svac_frst / $total_vallocated_svac_frst * 100; //CONSUMPTION RATE SINOVAC FIRST
                            $total_c_rate_svac_scnd = $total_vcted_svac_scnd / $total_vallocated_svac_scnd * 100; //CONSUMPTION RATE SINOVAC SECOND
                            $total_c_rate_astra_frst =  $total_vcted_astra_frst / $total_vallocated_astra_frst * 100; //CONSUMPTION RATE ASTRA FIRST
                            $total_c_rate_astra_scnd =  $total_vcted_astra_scnd / $total_vallocated_astra_scnd * 100; //CONSUMPTION RATE ASTRA SECOND

                            $total_c_rate_frst = number_format($total_vcted_frst / $total_allocated_frst * 100,2); //TOTAL_CONSUMPTION_RATE
                            $total_c_rate_scnd = number_format($total_vcted_scnd / $total_allocated_scnd * 100,2); //TOTAL_CONSUMPTION_RATE 2



                    ?>
                    <tr style="background-color: #59ab91">
                        <input type="hidden" name="province_id" value="{{ $province_id }}">
                        <input type="hidden" name="muncity_id" value="{{ $muncity_id }}">
                        <td style="width: 15%">
                            <input type="text" id="date_picker{{ $vaccine->id.$vaccine->encoded_by }}" name="date_first[]" value="<?php if(isset($vaccine->date_first)) echo date('m/d/Y',strtotime($vaccine->date_first)) ?>" class="form-control" required>
                        </td>
                        <td rowspan="2">
                            <select name="typeof_vaccine[]" id="typeof_vaccine" class="select2" required>
                                <option value="">Select Option</option>
                                <option value="Sinovac" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Sinovac')echo 'selected';} ?>>Sinovac</option>
                                <option value="Astrazeneca" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Astrazeneca')echo 'selected';} ?>>Astrazeneca</option>
                                <option value="Moderna" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Moderna')echo 'selected';} ?> disabled>Moderna</option>
                                <option value="Pfizer" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Pfizer')echo 'selected';} ?> disabled>Pfizer</option>
                            </select>
                            <br><br>
                            <div class="row">
                                <div class="col-md-6" style="padding: 2%;">
                                    <input type="text" name="vaccine_allocated_first[]" value="<?php if(isset($vaccine->vaccine_allocated_first)) echo $vaccine->vaccine_allocated_first; ?>" class="form-control" readonly>
                                </div>
                                <div class="col-md-6" style="background-color: #f39c12;padding: 2%">
                                    <input type="text" name="vaccine_allocated_second[]" value="<?php if(isset($vaccine->vaccine_allocated_second)) echo $vaccine->vaccine_allocated_second; ?>" class="form-control" readonly>
                                </div>
                            </div>
                        </td>
                        <td style="width: 15%" rowspan="2">
                            <select name="priority[]" id="priority{{ $vaccine->id.$vaccine->encoded_by }}" class="select2" onchange="getEliPop('<?php echo $muncity_id; ?>','<?php echo $vaccine->id.$vaccine->encoded_by; ?>')">
                                <option value="">Select Priority</option>
                                <option value="a1" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'a1')echo 'selected';} ?>>A1</option>
                                <option value="a2" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'a2')echo 'selected';} ?>>A2</option>
                                <option value="a3" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'a3')echo 'selected';} ?>>A3</option>
                                <option value="a4" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'a4')echo 'selected';} ?>>A4</option>
                                <option value="a5" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'a5')echo 'selected';} ?> disabled>A5</option>
                                <option value="b1" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'a6')echo 'selected';} ?> disabled>B1</option>
                                <option value="b2" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'a7')echo 'selected';} ?> disabled>B2</option>
                                <option value="b3" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'a8')echo 'selected';} ?> disabled>B3</option>
                                <option value="b4" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'ofw')echo 'selected';} ?> disabled >B4</option>
                                <option value="b5" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'ofw')echo 'selected';} ?> disabled >B5</option>
                                <option value="b6" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'ofw')echo 'selected';} ?> disabled >B6</option>
                                <option value="c" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'ofw')echo 'selected';} ?> disabled >C</option>
                            </select>
                            <br><br>
                            <input type="text" name="no_eli_pop[]" id="no_eli_pop{{ $vaccine->id.$vaccine->encoded_by }}" value="{{ $vaccine->no_eli_pop }}" class="form-control" readonly>
                        </td>
                        <td style="width: 5%">
                            <input type="text" name="vaccinated_first[]"  value="<?php if(isset($vaccine->vaccinated_first)) echo $vaccine->vaccinated_first ?>" class="form-control">
                        </td>
                        <td style="width: 5%">
                            <input type="text" name="mild_first[]" value="<?php if(isset($vaccine->mild_first)) echo $vaccine->mild_first ?>" class="form-control">
                        </td>
                        <td style="width: 5%">
                            <input type="text" name="serious_first[]" value="<?php if(isset($vaccine->serious_first)) echo $vaccine->serious_first ?>" class="form-control">
                        </td>
                        <td style="width: 10%">
                            <input type="text" name="refused_first[]" value="<?php if(isset($vaccine->refused_first)) echo $vaccine->refused_first ?>" class="form-control">
                        </td>
                        <td style="width: 10%">
                            <input type="text" name="deferred_first[]" value="<?php if(isset($vaccine->deferred_first)) echo $vaccine->deferred_first ?>" class="form-control">
                        </td>
                        <td style="width: 10%">
                            <input type="text" name="wastage_first[]" value="<?php if(isset($vaccine->wastage_first)) echo $vaccine->wastage_first?>" class="form-control">
                        </td>
                    </tr>
                    <tr style="background-color: #f39c12">
                        <td>
                            <input type="text" id="date_picker2{{ $vaccine->id.$vaccine->encoded_by }}" name="date_second[]" value="<?php if(isset($vaccine->date_second)) echo date('m/d/Y',strtotime($vaccine->date_second)) ?>" class="form-control">
                        </td>

                        <td>
                            <input type="text" name="vaccinated_second[]" value="<?php if(isset($vaccine->vaccinated_second)) echo $vaccine->vaccinated_second ?>" class="form-control">
                        </td>
                        <td style="width: 5%">
                            <input type="text" name="mild_second[]" value="<?php if(isset($vaccine->mild_second)) echo $vaccine->mild_second ?>" class="form-control">
                        </td>
                        <td style="width: 5%">
                            <input type="text" name="serious_second[]" value="<?php if(isset($vaccine->serious_first)) echo $vaccine->serious_second ?>" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="refused_second[]" value="<?php if(isset($vaccine->refused_second)) echo $vaccine->refused_second ?>" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="deferred_second[]" value="<?php if(isset($vaccine->deferred_second)) echo $vaccine->deferred_second ?>" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="wastage_second[]" value="<?php if(isset($vaccine->wastage_second)) echo $vaccine->wastage_second?>" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="9"><hr></td>
                    </tr>
                    <script>
                        $("#date_picker"+"{{ $vaccine->id.$vaccine->encoded_by }}").daterangepicker({
                            "singleDatePicker":true
                        });
                        $("#date_picker2"+"{{ $vaccine->id.$vaccine->encoded_by }}").daterangepicker({
                            "singleDatePicker":true
                        });
                    </script>
                @endforeach
            @endif
            <script>
                $("#date_picker").daterangepicker({
                    "singleDatePicker":true
                });
                $("#date_picker2").daterangepicker({
                    "singleDatePicker":true
                });
            </script>
            <tbody id="tbody_content_vaccine">

            </tbody>
            <tr>
                <td colspan="9">
                    <a href="#" onclick="addTbodyContent('<?php echo $province_id; ?>','<?php echo $muncity_id; ?>')" class="pull-right red" id="workAdd"><i class="fa fa-user-plus"></i> Add Daily Accomplishment</a>
                </td>
            </tr>
            <tr>
                <td colspan="9">
                    <div class="pull-right">
                        <button type="button" class="btn btn-default btn-md" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-success btn-md"><i class="fa fa-send"></i> Submit</button>
                    </div>
                </td>
            </tr>
    </table>
     <button class="btn btn-link collapsed" style="color:red" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
        <b>Sinovac</b>
     </button>
     <button class="btn btn-link collapsed" style="color:darkgoldenrod;" type="button" data-toggle="collapse" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
        <b>Astrazeneca</b>
     </button>
</form>
@if(count($vaccine_accomplishment) > 0)
<table style="font-size: 10pt;" class="table table-striped" border="2">
    <tr>
        <th>Type of Vaccine</th> <!-- Type of Vaccine 1-1 -->
        <th colspan="5"><center>Eligible Population</center></th>
        <th colspan="3">Vaccine Allocated</th>
        <th colspan="5"><center>Total Vaccinated</center></th>
        <th>Mild</th>
        <th>Serious</th>
        <th>Deferred</th>
        <th>Refused</th>
        <th>Wastage</th>
        <th>Percent Coverage</th>
        <th>Consumption Rate</th>
        <th>Remaining Unvacinated</th>
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
    <tbody id="collapseTwo" class="collapse bg-danger" aria-labelledby="headingTwo" data-parent="#accordionExample">
    <tr style="background-color: #ffd8d6">
        <td rowspan="2">

        </td> <!-- 1-3 -->
        <td rowspan="2">{{ $total_e_pop_svac_a1 }}</td> <!-- A1 SINOVAC FIRST -->
        <td rowspan="2">{{$total_e_pop_svac_a2 }}</td> <!-- A2 SINOVAC FIRST -->
        <td rowspan="2">{{ $total_e_pop_svac_a3 }}</td> <!-- A3 SINOVAC FIRST -->
        <td rowspan="2">{{ $total_e_pop_svac_a4 }}</td> <!-- A4 SINOVAC FIRST -->
        <td rowspan="2">{{ $total_e_pop_svac }}</td> <!-- ELIPOP TOTAL SINOVAC FIRST  -->
        <td rowspan="2">{{ $total_vallocated_svac_frst }}</td>  <!-- VACCINE ALLOCATED(FD) SINOVAC FIRST -->
        <td rowspan="2">{{ $total_vallocated_svac_scnd }}</td> <!-- VACCINE ALLOCATED(SD) SINOVAC FIRST -->
        <td rowspan="2">{{ $total_allocated_overall_svac_frst }}</td><!-- VACCINE ALLOCATED TOTAL SINOVAC -->
        <td>
            <span class="label label-success">{{ $total_svac_a1_frst }}</span> <!-- VACCINATED (A1) SINOVAC FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_svac_a2_frst }}</span> <!-- VACCINATED (A2) SINOVAC FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_svac_a3_frst }}</span> <!-- VACCINATED (A3) SINOVAC FIRST -->
        </td>
        <td>
            <span class="label label-success"> {{ $total_svac_a4_frst }}</span> <!-- VACCINATED (A4) SINOVAC FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_vcted_svac_frst }}</span>  <!-- TOTAL VACCINATED SINOVAC FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_mild_svac_frst }}</span>   <!-- MILD SINOVAC  FIRST-->
        </td>
        <td>
            <span class="label label-success">{{ $total_srs_svac_frst }}</span>  <!-- SERIOUS SINOVAC FIRST-->
        </td>
        <td>
            <span class="label label-success">{{ $total_dfrd_svac_frst }}</span> <!-- DEFERRED SINOVAC FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_rfsd_svac_frst }}</span> <!-- REFUSED SINOVAC  FIRST-->
        </td>
        <td>
            <span class="label label-success">{{ $total_wstge_svac_frst }}</span> <!-- WASTAGE SINOVAC  FIRST-->
        </td>
        <td>
            <span class="label label-success">{{ number_format($p_cvrge_svac_frst,2) }}%</span> <!-- PERCENT COVERAGE SINOVAC FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_c_rate_svac_frst,2) }}%</span> <!-- CONSUMPTION RATE SINOVAC FIRST -->
        </td>
        <td>
            <span class="label label-success">{{ $total_e_pop_svac - $total_vcted_svac_frst - $total_rfsd_svac_frst }}</span> <!-- REMAINING UNVACCINATED SINOVAC FIRST -->
        </td>
    </tr>
    <tr style="background-color: #ffd8d6">
        <td>
            <span class="label label-warning">{{ $total_svac_a1_scnd }} </span> <!-- VACCINATED (A1) SINOVAC SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_svac_a2_scnd }} </span> <!-- VACCINATED (A2) SINOVAC SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_svac_a3_scnd }} </span> <!-- VACCINATED (A3) SINOVAC SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_svac_a4_scnd }} </span> <!-- VACCINATED (A4) SINOVAC SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_vcted_svac_scnd }}</span> <!-- TOTAL VACCINATED SINOVAC SECOND -->
        </td> <!-- 1-4 -->
        <td>
            <span class="label label-warning">{{ $total_mild_svac_scnd }}</span> <!-- MILD SINOVAC SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_srs_svac_scnd }}</span> <!-- SERIOUS SINOVAC SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_dfrd_svac_scnd }}</span> <!-- DEFERRED SINOVAC SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_rfsd_svac_scnd }}</span> <!-- REFUSED SINOVAC SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_wstge_svac_scnd }}</span> <!-- WASTAGE SINOVAC SECOND -->
        </td>
        <td>

            <span class="label label-warning">{{ number_format($p_cvrge_svac_scnd,2) }}%</span> <!-- PERCENT COVERAGE SINOVAC SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_c_rate_svac_scnd,2)}}%</span> <!-- CONSUMPTION RATE SINOVAC SECOND -->
        </td>
        <td>
            <span class="label label-warning">{{ $total_e_pop_svac - $total_vcted_svac_scnd - $total_rfsd_svac_scnd }} </span> <!-- REMAINING UNVACCINATED SINOVAC SECOND -->
        </td>
    </tr>
    </tbody>
    <tr>
    </tr>
    <tbody id="collapse2" class="collapse bg-primary" aria-labelledby="headingTwo" data-parent="#accordionExample">
        <tr style="background-color: #f2fcac">
            <td rowspan="2"></td> <!-- 1-5 -->
            <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_a1 }}</td>  <!-- Frontline(A1) ASTRA -->
            <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_a2 }}</td>  <!-- SENIOR(A2) ASTRA -->
            <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_a3 }}</td>
            <td rowspan="2" style="color:black;">{{ $total_e_pop_astra_a4 }}</td>
            <td rowspan="2" style="color:black;">{{ $total_e_pop_astra }}</td>  <!-- ELIPOP TOTAL ASTRA FIRST  -->
            <td rowspan="2" style="color:black;">{{ $total_vallocated_astra_frst }}</td>  <!-- VACCINE ALLOCATED(FD) ASTRA FIRST -->
            <td rowspan="2" style="color: black;">{{ $total_vallocated_astra_scnd }}</td>  <!-- VACCINE ALLOCATED(SD) ASTRA FIRST -->
            <td rowspan="2" style="color:black;">{{ $total_allocated_overall_astra_frst }}</td>  <!-- TOTAL VACCINE ALLOCATED ASTRA FIRST -->
            <td style="color:black;">
                <span class="label label-success">{{ $total_astra_a1_frst }}</span>  <!-- VACCINATED (A1) ASTRA FIRST -->
            </td>
            <td  style="color:black;">
                <span class="label label-success">{{ $total_astra_a2_frst }} </span> <!-- VACCINATED (A2) ASTRA FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ $total_astra_a3_frst }} </span> <!-- VACCINATED (A3) ASTRA FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ $total_astra_a4_frst }} </span> <!-- VACCINATED (A4) ASTRA FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ $total_vcted_astra_frst }}</span> <!-- TOTAL VACCINATED  ASTRA FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ $total_mild_astra_frst }}</span> <!-- MILD ASTRA FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ $total_srs_astra_frst }}</span>  <!-- SERIOUS ASTRA FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ $total_dfrd_astra_frst }}</span>  <!-- DEFERRED ASTRA FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ $total_rfsd_astra_frst }}</span>  <!-- REFUSED ASTRA FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ $total_wstge_astra_frst }}</span>  <!-- WASTAGE ASTRA FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ number_format($p_cvrge_astra_frst,2) }}%</span>  <!-- PERCENT COVERAGE ASTRA FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ number_format($total_c_rate_astra_frst,2) }}%</span>  <!-- CONSUMPTION RATE ASTRA FIRST -->
            </td>
            <td>
                <span class="label label-success">{{ $total_e_pop_astra - $total_vcted_astra_frst - $total_rfsd_astra_frst }} </span>  <!-- REMAINING UNVACCINATED ASTRA FIRST -->
            </td>
        </tr>
        <tr style="background-color: #f2fcac">
            <td style="color:black;">
                <span class="label label-warning">{{ $total_astra_a1_scnd }}</span> <!-- VACCINATED (A1) ASTRA SECOND -->
            </td>
            <td style="color:black;">
                <span class="label label-warning">{{ $total_astra_a2_scnd }} </span>  <!-- VACCINATED (A2) ASTRA SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ $total_astra_a3_scnd }}</span> <!-- VACCINATED (A3) ASTRA SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ $total_astra_a4_scnd }}</span> <!-- VACCINATED (A4) ASTRA SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ $total_vcted_astra_scnd }}</span> <!-- TOTAL VACCINATED ASTRA SECOND -->
            </td> <!-- 1-6 -->
            <td>
                <span class="label label-warning">{{ $total_mild_astra_scnd }}</span> <!-- MILD ASTRA SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ $total_srs_astra_scnd }}</span> <!-- SERIOUS ASTRA SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ $total_dfrd_astra_scnd }}</span> <!-- DEFERRED ASTRA SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ $total_rfsd_astra_scnd }}</span> <!-- REFUSED ASTRA SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ $total_wstge_astra_scnd }}</span> <!-- WASTAGE ASTRA SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ number_format($p_cvrge_astra_scnd,2) }}%</span> <!-- PERCENT COVERAGE ASTRA SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ number_format($total_c_rate_astra_scnd,2) }}%</span> <!-- CONSUMPTION RATE ASTRA SECOND -->
            </td>
            <td>
                <span class="label label-warning">{{ $total_e_pop_astra - $total_vcted_astra_scnd - $total_rfsd_astra_scnd }}</span> <!-- REMAINING UNVACCINATED ASTRA SECOND -->
            </td>
        </tr>
    </tbody>
    <tr>
        <td>Total</td> <!-- 1-7 -->
        <td>
           <b>{{ $muncity->a1 }}</b>  <!-- TOTAL A1 FIRST -->
        </td>
        <td>
            <b>{{ $muncity->a2 }}</b>  <!-- TOTAL A2 FIRST -->
        </td>
        <td>
            <b>{{ $muncity->a3 }}</b>  <!-- TOTAL A3 FIRST -->
        </td>
        <td>
            <b>{{ $muncity->a4 }}</b>  <!-- TOTAL A4 FIRST -->
        </td>
        <td>
           <b>{{ $total_e_pop }}</b>  <!-- TOTAL_ELI_POP  -->
        </td>
        <td >
           <b>{{ $total_allocated_frst }}</b>  <!-- TOTAL ALLOCATED_FIRST -->
        </td>
        <td >
            <b>{{ $total_allocated_scnd }}</b>  <!-- TOTAL ALLOCATED_SECOND -->
        </td>
        <td>
            <b>{{ $total_allocated }}</b>  <!-- TOTAL_ALLOCATED-->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{$total_svac_a1_frst + $total_astra_a1_frst}}</b>  <!-- TOTAL_A1   -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_svac_a2_frst + $total_astra_a2_frst }}</b>  <!-- TOTAL_A2   -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_svac_a3_frst + $total_astra_a3_frst }}</b>  <!-- TOTAL_A3   -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_svac_a4_frst + $total_astra_a4_frst }}</b>  <!-- TOTAL_A4   -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_vcted_frst }}</b>  <!-- TOTAL_VACCINATED  -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_mild_frst }}</b>  <!-- TOTAL_MILD  -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_srs_frst }}</b>  <!-- TOTAL_SERIOUS   -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_dfrd_frst }}</b>  <!-- TOTAL_DEFERRED  -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_rfsd_frst }}</b>  <!-- TOTAL_REFUSED  -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_wstge_frst }}</b>  <!-- TOTAL_WASTAGE -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ number_format($total_p_cvrge_frst,2) }}%</b>  <!-- TOTAL_PERCENT_COVERAGE -->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_c_rate_frst }}%</b>  <!-- TOTAL_CONSUMPTION_RATE-->
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_remaining }}</b>  <!-- TOTAL_REMAINING_UNVACCINATED -->
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
            <b class="label label-warning" style="margin-right: 5%">{{$total_svac_a1_scnd + $total_astra_a1_scnd}}</b> <!-- TOTAL_A1   -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{$total_svac_a2_scnd + $total_astra_a2_scnd}}</b> <!-- TOTAL_A2   -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{$total_svac_a3_scnd + $total_astra_a3_scnd}}</b> <!-- TOTAL_A3   -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{$total_svac_a4_scnd + $total_astra_a4_scnd}}</b> <!-- TOTAL_A4   -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_scnd }}</b> <!-- TOTAL_VACCINATED 2   -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_mild_scnd }}</b> <!-- TOTAL_MILD 2   -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_srs_scnd }}</b> <!-- TOTAL_SERIOUS 2   -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_dfrd_scnd }}</b> <!-- TOTAL_DEFERRED 2   -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_rfsd_scnd }}</b> <!-- TOTAL_REFUSED 2   -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_wstge_scnd }}</b> <!-- TOTAL_WASTAGE 2   -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ number_format($total_p_cvrge_scnd,2) }}%</b> <!-- TOTAL_PERCENT_COVERAGE_2   -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_c_rate_scnd }}%</b> <!-- TOTAL_CONSUMPTION_RATE 2   -->
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_remaining_scnd }}</b> <!-- TOTAL_REMAINING_UNVACCINATED 2   -->
        </td>
    </tr>
</table>
@else
    <div class="alert alert-warning">
        <div class="text-warning">No data!</div>
    </div>
@endif

<script>
    var count = 0;
    function addTbodyContent(province_id,muncity_id) {
        count++;
        $('#tbody_content_vaccine').append('<tr style="background-color: #59ab91">\n' +
            '    <input type="hidden" name="province_id" value="'+province_id+'" >\n' +
            '    <input type="hidden" name="muncity_id" value="'+muncity_id+'" >\n' +
            '    <td style="width: 15%">\n' +
            '        <input type="text" id="date_picker'+count+'" name="date_first[]" class="form-control" >\n' +
            '    </td>\n' +
            '    <td style="width: 15%" rowspan="2">\n' +
            '        <select name="typeof_vaccine[]" id="typeof_vaccine'+count+'" onchange="getVaccineAllocated('+muncity_id+','+count+')" class="select2" required>\n' +
            '            <option value="">Select Option</option>\n' +
            '            <option value="Sinovac">Sinovac</option>\n' +
            '            <option value="Astrazeneca">Astrazeneca</option>\n' +
            '            <option value="Moderna" disabled>Moderna</option>\n' +
            '            <option value="Pfizer" disabled>Pfizer</option>\n' +
            '        </select>\n' +
            '       <br><br>' +
            '<div class="row"><div class="col-md-6" style="padding:2%"><input type="text" id="vaccine_allocated_first'+count+'" name="vaccine_allocated_first[]" class="form-control" readonly></div><div class="col-md-6" style="background-color: #f39c12;padding: 2%"><input type="text" id="vaccine_allocated_second'+count+'" name="vaccine_allocated_second[]" class="form-control" readonly></div></div> \n' +
            '    </td>\n' +
            '    <td style="width: 15%" rowspan="2">\n' +
            '        <select name="priority[]" id="priority'+count+'" onchange="getEliPop('+muncity_id+','+count+')" class="select2" >\n' +
            '            <option value="">Select Priority</option>\n' +
            '            <option value="a1" >A1</option>\n' +
            '            <option value="a2" >A2</option>\n' +
            '            <option value="a3" >A3</option>\n' +
            '            <option value="a4" >A4</option>\n' +
            '            <option value="a5" disabled>A5</option>\n' +
            '            <option value="b1" disabled>B1</option>\n' +
            '            <option value="b2" disabled>B2</option>\n' +
            '            <option value="b3" disabled>B3</option>\n' +
            '            <option value="b4" disabled >B4</option>\n' +
            '            <option value="b5" disabled>B5</option>\n' +
            '            <option value="b6" disabled>B6</option>\n' +
            '            <option value="c"  disabled>C</option>\n' +
            '        </select>\n' +
            '       <br><br><input type="text" name="no_eli_pop[]" id="no_eli_pop'+count+'" class="form-control" readonly>\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="vaccinated_first[]" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="mild_first[]" value="" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="serious_first[]" value="" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="refused_first[]" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="deferred_first[]" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="wastage_first[]" class="form-control">\n' +
            '    </td>\n' +
            '</tr>\n' +
            '<tr style="background-color: #f39c12">\n' +
            '    <td>\n' +
            '        <input type="text" id="date_picker2'+count+'" name="date_second[]"  class="form-control">\n' +
            '    </td>\n' +
            '    <td>\n' +
            '        <input type="text" name="vaccinated_second[]" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="mild_second[]" value="" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="serious_second[]" value="" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="refused_second[]" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="deferred_second[]" class="form-control">\n' +
            '    </td>\n' +
            '    <td>\n' +
            '        <input type="text" name="wastage_second[]"  class="form-control">\n' +
            '    </td>\n' +
            '\n' +
            '</tr>\n' +
            '<tr>\n' +
            '    <td colspan="9"><hr></td>\n' +
            '</tr>');

        $("#date_picker"+count).daterangepicker({
            "singleDatePicker":true
        });
        $("#date_picker2"+count).daterangepicker({
            "singleDatePicker":true
        });

        $(".select2").select2({ width: '100%' });
    }

    function getEliPop(muncity_id,count){
        var url = "<?php echo asset('vaccine/no_eli_pop').'/'; ?>"+muncity_id+"/"+$("#priority"+count).val();
        $.get(url,function(data){
            $("#no_eli_pop"+count).val(data);
        });
    }

    function getVaccineAllocated(muncity_id,count){
        var url = "<?php echo asset('vaccine/allocated').'/'; ?>"+muncity_id+"/"+$("#typeof_vaccine"+count).val();
        $.get(url,function(data){
            $("#vaccine_allocated_first"+count).val(data[0]);
            $("#vaccine_allocated_second"+count).val(data[1]);
        });
    }

</script>


