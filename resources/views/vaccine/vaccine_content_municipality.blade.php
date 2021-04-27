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
                $total_percent_coverage_first = 0;
                $total_percent_coverage_second = 0;

                //total vaccinated sinovac
                $total_eli_pop_sinovac_frontline = 0;
                $total_eli_pop_sinovac_frontline_flag = true;
                $total_eli_pop_sinovac_senior = 0;
                $total_eli_pop_sinovac_senior_flag = true;
                $total_vaccine_allocated_sinovac = 0;
                $total_vaccine_allocated_sinovac_flag = true;
                $total_vaccine_allocated_sinovac_first = 0;
                $total_vaccine_allocated_sinovac_second = 0;
                $total_vaccinated_sinovac_first = 0;
                $total_vaccinated_sinovac_second = 0;
                $total_mild_sinovac_first = 0;
                $total_mild_sinovac_second = 0;
                $total_serious_sinovac_first = 0;
                $total_serious_sinovac_second = 0;
                $total_deferred_sinovac_first = 0;
                $total_deferred_sinovac_second = 0;
                $total_refused_sinovac_first = 0;
                $total_refused_sinovac_second = 0;
                $total_wastage_sinovac_first = 0;
                $total_wastage_sinovac_second = 0;

                //total vaccinated astra
                $total_eli_pop_astra_frontline = 0;
                $total_eli_pop_astra_frontline_flag = true;
                $total_eli_pop_astra_senior = 0;
                $total_eli_pop_astra_senior_flag = true;
                $total_vaccine_allocated_astra = 0;
                $total_vaccine_allocated_astra_flag = true;
                $total_vaccine_allocated_astra_first = 0;
                $total_vaccine_allocated_astra_second = 0;
                $total_vaccinated_astra_first = 0;
                $total_vaccinated_astra_second = 0;
                $total_mild_astra_first = 0;
                $total_mild_astra_second = 0;
                $total_serious_astra_first = 0;
                $total_serious_astra_second = 0;
                $total_deferred_astra_first = 0;
                $total_deferred_astra_second = 0;
                $total_refused_astra_first = 0;
                $total_refused_astra_second = 0;
                $total_wastage_astra_first = 0;
                $total_wastage_astra_second = 0;


                //sinovac
                $total_sinovac_a1_first = 0;
                $total_sinovac_a2_first = 0;
                $total_sinovac_a1_second = 0;
                $total_sinovac_a2_second = 0;

                //astra
                $total_astra_a1_first = 0;
                $total_astra_a2_first = 0;
                $total_astra_a1_second = 0;
                $total_astra_a2_second = 0;


            ?>
            @if(count($vaccine_accomplishment)>0)
                @foreach($vaccine_accomplishment as $vaccine)
                    <?php
                        //modified vaccine accomplishment table
                        if($vaccine->priority == 'frontline_health_workers'){
                            $muncity = \App\Muncity::find($vaccine->muncity_id)->first();
                            $vaccine->no_eli_pop = $muncity->frontline_health_workers;
                        }
                        elseif($vaccine->priority == 'indigent_senior_citizens'){
                            $muncity = \App\Muncity::find($vaccine->muncity_id)->first();
                            $vaccine->no_eli_pop = $muncity->senior_citizens;
                        }

                        if($vaccine->typeof_vaccine == 'Sinovac'){
                            $muncity = \App\Muncity::find($vaccine->muncity_id)->first();
                            $vaccine->vaccine_allocated_first = $muncity->sinovac_allocated_first;
                            $vaccine->vaccine_allocated_second = $muncity->sinovac_allocated_second;
                            $total_vaccine_allocated_sinovac_first += $muncity->sinovac_allocated_first;
                            $total_vaccine_allocated_sinovac_second += $muncity->sinovac_allocated_second;
                        }
                        elseif($vaccine->typeof_vaccine == 'Astrazeneca'){
                            $muncity = \App\Muncity::find($vaccine->muncity_id)->first();
                            $vaccine->vaccine_allocated_first = $muncity->astrazeneca_allocated_first;
                            $vaccine->vaccine_allocated_second = $muncity->astrazeneca_allocated_second;
                            $total_vaccine_allocated_astra_first += $muncity->astrazeneca_allocated_first;
                            $total_vaccine_allocated_astra_second += $muncity->astrazeneca_allocated_second;
                        }

                        $total_percent_coverage_first += $total_vaccinated_first / $total_eli_pop * 100;
                        $total_percent_coverage_second += $total_vaccinated_second / $total_eli_pop * 100;

                        if($vaccine->typeof_vaccine == "Sinovac"){
                            $total_eli_pop_sinovac_frontline = $muncity->frontline_health_workers;
                            $total_eli_pop_sinovac_senior = $muncity->senior_citizens;
                            if($vaccine->priority == "frontline_health_workers"){
                                $total_sinovac_a1_first += $vaccine->vaccinated_first; //correct
                                $total_sinovac_a1_second += $vaccine->vaccinated_second; //correct
                            }
                            elseif($vaccine->priority == "indigent_senior_citizens"){
                                $total_sinovac_a2_first += $vaccine->vaccinated_first; //correct
                                $total_sinovac_a2_second += $vaccine->vaccinated_second; //correct

                            }
                            if($total_vaccine_allocated_sinovac_flag){
                                $total_vaccine_allocated_sinovac += $vaccine->vaccine_allocated;
                                $total_vaccine_allocated_sinovac_flag = false;
                            }
                            $total_vaccinated_sinovac_first += $vaccine->vaccinated_first;
                            $total_vaccinated_sinovac_second += $vaccine->vaccinated_second;
                            $total_mild_sinovac_first += $vaccine->mild_first;
                            $total_mild_sinovac_second += $vaccine->mild_second;
                            $total_serious_sinovac_first += $vaccine->serious_first;
                            $total_serious_sinovac_second += $vaccine->serious_second;
                            $total_deferred_sinovac_first += $vaccine->deferred_first;
                            $total_deferred_sinovac_second += $vaccine->deferred_second;
                            $total_refused_sinovac_first += $vaccine->refused_first;
                            $total_refused_sinovac_second += $vaccine->refused_second;
                            $total_wastage_sinovac_first += $vaccine->wastage_first;
                            $total_wastage_sinovac_second += $vaccine->wastage_second;
                        }

                        if($vaccine->typeof_vaccine == "Astrazeneca"){
                            $total_eli_pop_astra_frontline = $muncity->frontline_health_workers;
                            $total_eli_pop_astra_senior = $muncity->senior_citizens;
                            if($vaccine->priority == "frontline_health_workers"){
                                $total_astra_a1_first += $vaccine->vaccinated_first; //correct
                                $total_astra_a1_second += $vaccine->vaccinated_second; //correct
                            }
                            elseif($vaccine->priority == "indigent_senior_citizens" ){
                                $total_astra_a2_first += $vaccine->vaccinated_first; //correct
                                $total_astra_a2_second += $vaccine->vaccinated_second; //correct
                            }
                            if($total_vaccine_allocated_astra_flag){
                                $total_vaccine_allocated_astra += $vaccine->vaccine_allocated;
                                $total_vaccine_allocated_astra_flag = false;
                            }

                            $total_vaccinated_astra_first += $vaccine->vaccinated_first;
                            $total_vaccinated_astra_second += $vaccine->vaccinated_second;
                            $total_mild_astra_first += $vaccine->mild_first;
                            $total_mild_astra_second += $vaccine->mild_second;
                            $total_serious_astra_first += $vaccine->serious_first;
                            $total_serious_astra_second += $vaccine->serious_second;
                            $total_deferred_astra_first += $vaccine->deferred_first;
                            $total_deferred_astra_second += $vaccine->deferred_second;
                            $total_refused_astra_first += $vaccine->refused_first;
                            $total_refused_astra_second += $vaccine->refused_second;
                            $total_wastage_astra_first += $vaccine->wastage_first;
                            $total_wastage_astra_second += $vaccine->wastage_second;
                        }

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
                                <option value="frontline_health_workers" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'frontline_health_workers')echo 'selected';} ?>>Frontline Health Workers</option>
                                <option value="indigent_senior_citizens" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'indigent_senior_citizens')echo 'selected';} ?>>Senior Citizens</option>
                                <option value="remaining_indigent_population" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'remaining_indigent_population')echo 'selected';} ?> disabled>Remaining Indigent Population</option>
                                <option value="uniform_personnel" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'uniform_personnel')echo 'selected';} ?> disabled>Uniform Personnel</option>
                                <option value="teachers_school_workers" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'teachers_school_workers')echo 'selected';} ?> disabled>Teachers & School Workers</option>
                                <option value="all_government_workers" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'all_government_workers')echo 'selected';} ?> disabled>All Government Workers (National & Local)</option>
                                <option value="essential_workers" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'essential_workers')echo 'selected';} ?> disabled>Essential Workers</option>
                                <option value="socio_demographic" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'socio_demographic')echo 'selected';} ?> disabled>Socio-demographic groups & significant higher risk other than senior citizen and indigent population (e.g.PDL,PWD,IP,Filipinos living in high-density areas)</option>
                                <option value="ofw" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'ofw')echo 'selected';} ?> disabled >OFW's</option>
                                <option value="remaining_workforce" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'remaining_workforce')echo 'selected';} ?> disabled>Other remaining workforce</option>
                                <option value="remaining_filipino_citizen" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'remaining_filipino_citizen')echo 'selected';} ?> disabled>Remaining Filipino Citizen</option>
                                <option value="etc" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'etc')echo 'selected';} ?> disabled >ETC.</option>
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
        <th colspan="3"><center>Eligible Population</center></th>
        <th colspan="3">Vaccine Allocated</th>
        <th colspan="3">Total Vaccinated</th>
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
    <tbody id="collapseTwo" class="collapse bg-danger" aria-labelledby="headingTwo" data-parent="#accordionExample">
    <tr style="background-color: #ffd8d6">
        <td rowspan="2">

        </td> <!-- 1-3 -->
        <td rowspan="2">{{ $total_eli_pop_sinovac_frontline }}</td>
        <td rowspan="2">{{ $total_eli_pop_sinovac_senior }}</td>
        <td rowspan="2">
            <?php $total_eli_pop_sinovac = $total_eli_pop_sinovac_frontline + $total_eli_pop_sinovac_senior; ?>
            {{ $total_eli_pop_sinovac }}
        </td>
        <td rowspan="2">{{ $total_vaccine_allocated_sinovac_first }}</td>
        <td rowspan="2">{{ $total_vaccine_allocated_sinovac_second }}</td>
        <td rowspan="2">{{ $total_vaccine_allocated_sinovac_first + $total_vaccine_allocated_sinovac_second }} </td>
        <td>
            <span class="label label-success">{{ $total_sinovac_a1_first }} </span>
        </td>
        <td>
            <span class="label label-success">{{ $total_sinovac_a2_first }} </span>
        </td>
        <td>
            <span class="label label-success">{{ $total_vaccinated_sinovac_first }}</span>
        </td>
        <td>
            <span class="label label-success">{{ $total_mild_sinovac_first }}</span>
        </td>
        <td>
            <span class="label label-success">{{ $total_serious_sinovac_first }}</span>
        </td>
        <td>
            <span class="label label-success">{{ $total_deferred_sinovac_first }}</span>
        </td>
        <td>
            <span class="label label-success">{{ $total_refused_sinovac_first }}</span>
        </td>
        <td>
            <span class="label label-success">{{ $total_wastage_sinovac_first }}</span>
        </td>
        <td>
            <?php $percent_coverage_sinovac_first = ($total_vaccinated_sinovac_first / $total_eli_pop_sinovac) * 100; ?>
            <span class="label label-success">{{ number_format($percent_coverage_sinovac_first,2) }}%</span>
        </td>
        <td>
            <span class="label label-success">{{ number_format(($total_vaccinated_sinovac_first / $total_vaccine_allocated_sinovac) * 100,2) }}%</span>
        </td>
        <td>
            <span class="label label-success">{{ $total_eli_pop_sinovac - $total_vaccinated_sinovac_first - $total_refused_sinovac_first }}</span>
        </td>
    </tr>
    <tr style="background-color: #ffd8d6">
        <td>
            <span class="label label-warning">{{ $total_sinovac_a1_second }} </span>
        </td>
        <td>
            <span class="label label-warning">{{ $total_sinovac_a2_second }} </span>
        </td>
        <td>
            <span class="label label-warning">{{ $total_vaccinated_sinovac_second }}</span>
        </td> <!-- 1-4 -->
        <td>
            <span class="label label-warning">{{ $total_mild_sinovac_second }}</span>
        </td>
        <td>
            <span class="label label-warning">{{ $total_serious_sinovac_second }}</span>
        </td>
        <td>
            <span class="label label-warning">{{ $total_deferred_sinovac_second }}</span>
        </td>
        <td>
            <span class="label label-warning">{{ $total_refused_sinovac_second }}</span>
        </td>
        <td>
            <span class="label label-warning">{{ $total_wastage_sinovac_second }}</span>
        </td>
        <td>
            <?php  $percent_coverage_sinovac_second = ($total_vaccinated_sinovac_second / $total_eli_pop_sinovac) * 100; ?>
            <span class="label label-warning">{{ number_format($percent_coverage_sinovac_second,2) }}%</span>
        </td>
        <td>
            <span class="label label-warning">{{ number_format(($total_vaccinated_sinovac_second / $total_vaccine_allocated_sinovac) * 100,2) }}%</span>
        </td>
        <td>
            <span class="label label-warning">{{ $total_eli_pop_sinovac - $total_vaccinated_sinovac_second - $total_refused_sinovac_second }} </span>
        </td>
    </tr>
    </tbody>
    <tr>
    </tr>
    <tbody id="collapse2" class="collapse bg-primary" aria-labelledby="headingTwo" data-parent="#accordionExample">
        <tr style="background-color: #f2fcac">
            <td rowspan="2"></td> <!-- 1-5 -->
            <td rowspan="2" style="color:black;">{{ $total_eli_pop_astra_frontline }}</td>
            <td rowspan="2" style="color:black;">{{ $total_eli_pop_astra_senior }}</td>
            <td rowspan="2" style="color:black;">
                <?php $total_eli_pop_astra = $total_eli_pop_astra_frontline + $total_eli_pop_astra_senior; ?>
                {{ $total_eli_pop_astra }}
            </td>
            <td rowspan="2" style="color:black;">{{ $total_vaccine_allocated_astra_first }}</td>
            <td rowspan="2" style="color: black;">{{ $total_vaccine_allocated_astra_second }}</td>
            <td rowspan="2" style="color:black;">{{ $total_vaccine_allocated_astra_first + $total_vaccine_allocated_astra_second }}</td>
            <td style="color:black;">
                <span class="label label-success">{{ $total_astra_a1_first }}</span>
            </td>
            <td  style="color:black;">
                <span class="label label-success">{{ $total_astra_a2_first }} </span>
            </td>
            <td>
                <span class="label label-success">{{ $total_vaccinated_astra_first }}</span>
            </td>
            <td>
                <span class="label label-success">{{ $total_mild_astra_first }}</span>
            </td>
            <td>
                <span class="label label-success">{{ $total_serious_astra_first }}</span>
            </td>
            <td>
                <span class="label label-success">{{ $total_deferred_astra_first }}</span>
            </td>
            <td>
                <span class="label label-success">{{ $total_refused_astra_first }}</span>
            </td>
            <td>
                <span class="label label-success">{{ $total_wastage_astra_first }}</span>
            </td>
            <td>
                <?php $percent_coverage_astra_first = ($total_vaccinated_astra_first / $total_eli_pop_astra) * 100; ?>
                <span class="label label-success">{{ number_format($percent_coverage_astra_first,2) }}%</span>
            </td>
            <td>
                <span class="label label-success">{{ number_format(($total_vaccinated_astra_first / $total_vaccine_allocated_astra) * 100,2) }}%</span>
            </td>
            <td>
                <span class="label label-success">{{ $total_eli_pop_astra - $total_vaccinated_astra_first - $total_refused_astra_first }} </span>
            </td>
        </tr>
        <tr style="background-color: #f2fcac">
            <td style="color:black;">
                <span class="label label-warning">{{ $total_astra_a1_second }}</span>
            </td>
            <td style="color:black;">
                <span class="label label-warning">{{ $total_astra_a2_second }} </span>
            </td>
            <td>
                <span class="label label-warning">{{ $total_vaccinated_astra_second }}</span>
            </td> <!-- 1-6 -->
            <td>
                <span class="label label-warning">{{ $total_mild_astra_second }}</span>
            </td>
            <td>
                <span class="label label-warning">{{ $total_serious_astra_second }}</span>
            </td>
            <td>
                <span class="label label-warning">{{ $total_deferred_astra_second }}</span>
            </td>
            <td>
                <span class="label label-warning">{{ $total_refused_astra_second }}</span>
            </td>
            <td>
                <span class="label label-warning">{{ $total_wastage_astra_second }}</span>
            </td>
            <td>
                <?php $percent_coverage_astra_second = ($total_vaccinated_astra_first / $total_eli_pop_astra) * 100; ?>
                <span class="label label-warning">{{ number_format($percent_coverage_astra_second,2) }}%</span>
            </td>
            <td>
                <span class="label label-warning">{{ number_format(($total_vaccinated_astra_second / $total_vaccine_allocated_astra) * 100,2) }}%</span>
            </td>
            <td>
                <span class="label label-warning">{{ $total_eli_pop_astra - $total_vaccinated_astra_second - $total_refused_astra_second }}</span>
            </td>
        </tr>
    </tbody>
    <tr>
        <td>Total</td> <!-- 1-7 -->
        <td>
           <b>{{ $muncity->frontline_health_workers }}</b>
        </td>
        <td>
            <b>{{ $muncity->senior_citizens }}</b>
        </td>
        <td>
            <?php $total_eli_pop = $muncity->frontline_health_workers + $muncity->senior_citizens ?>
           <b>{{ $total_eli_pop }}</b>
        </td>
        <td >
            <?php $total_allocated_first = $total_vaccine_allocated_sinovac_first + $total_vaccine_allocated_astra_first; ?>
           <b>{{ $total_allocated_first }}</b>
        </td>
        <td >
            <?php $total_allocated_second = $total_vaccine_allocated_sinovac_second + $total_vaccine_allocated_astra_second ?>
            <b>{{ $total_allocated_second }}</b>
        </td>
        <td>
            <?php $total_allocated = $total_allocated_first + $total_allocated_second;?>
            <b>{{ $total_allocated }}</b>
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{$total_sinovac_a1_first + $total_astra_a1_first}}</b>
        </td>
        <td>
            <b class="label label-success" style="margin-right: 5%">{{ $total_sinovac_a2_first + $total_astra_a2_first }}</b>
        </td>
        <td>
            <?php $total_vaccinated_first = $total_vaccinated_sinovac_first + $total_vaccinated_astra_first;?>
            <b class="label label-success" style="margin-right: 5%">{{ $total_vaccinated_first }}</b>
        </td>
        <td>
            <?php $total_mild_first = $total_mild_sinovac_first + $total_mild_astra_first;?>
            <b class="label label-success" style="margin-right: 5%">{{ $total_mild_first }}</b>
        </td>
        <td>
            <?php $total_serious_first = $total_serious_sinovac_first + $total_serious_astra_first;?>
            <b class="label label-success" style="margin-right: 5%">{{ $total_serious_first }}</b>
        </td>
        <td>
            <?php $total_deferred_first = $total_deferred_sinovac_first + $total_deferred_astra_first;?>
            <b class="label label-success" style="margin-right: 5%">{{ $total_deferred_first }}</b>
        </td>
        <td>
            <?php $total_refused_first = $total_refused_sinovac_first + $total_refused_astra_first;?>
            <b class="label label-success" style="margin-right: 5%">{{ $total_refused_first }}</b>
        </td>
        <td>
            <?php $total_wastage_first = $total_wastage_sinovac_first + $total_wastage_astra_first;?>
            <b class="label label-success" style="margin-right: 5%">{{ $total_wastage_first }}</b>
        </td>
        <td>
            <?php $total_percent_coverage_first = $total_vaccinated_first / $total_eli_pop * 100; ?>
            <b class="label label-success" style="margin-right: 5%">{{ number_format($total_percent_coverage_first,2) }}%</b>
        </td>
        <td>
            <?php $total_consumption_rate_first = number_format($total_vaccinated_first / $total_allocated * 100,2); ?>
            <b class="label label-success" style="margin-right: 5%">{{ $total_consumption_rate_first }}%</b>
        </td>
        <td>
            <?php $total_remaining = $total_eli_pop - $total_vaccinated_first - $total_refused_first; ?>
            <b class="label label-success" style="margin-right: 5%">{{ $total_remaining }}</b>
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
            <b class="label label-warning" style="margin-right: 5%">{{$total_sinovac_a1_second + $total_astra_a1_second}}</b>
        </td>
        <td>
            <b class="label label-warning" style="margin-right: 5%">{{$total_sinovac_a2_second + $total_astra_a2_second}}</b>
        </td>
        <td>
            <?php $total_vaccinated_second = $total_vaccinated_sinovac_second + $total_vaccinated_astra_second;?>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_vaccinated_second }}</b>
        </td>
        <td>
            <?php $total_mild_second = $total_mild_sinovac_second + $total_mild_astra_second;?>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_mild_second }}</b>
        </td>
        <td>
            <?php $total_serious_second = $total_serious_sinovac_second + $total_serious_astra_second;?>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_serious_second }}</b>
        </td>
        <td>
            <?php $total_deferred_second = $total_deferred_sinovac_second + $total_deferred_astra_second;?>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_deferred_second }}</b>
        </td>
        <td>
            <?php $total_refused_second = $total_refused_sinovac_second + $total_refused_astra_second;?>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_refused_second }}</b>
        </td>
        <td>
            <?php $total_wastage_second = $total_wastage_sinovac_second + $total_wastage_astra_second;?>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_wastage_second }}</b>
        </td>
        <td>
            <?php $total_percent_coverage_second = $total_vaccinated_second / $total_eli_pop * 100; ?>
            <b class="label label-warning" style="margin-right: 5%">{{ number_format($total_percent_coverage_second,2) }}%</b>
        </td>
        <td>
            <?php $total_consumption_rate_second = number_format($total_vaccinated_second / $total_allocated * 100,2); ?>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_consumption_rate_second }}%</b>
        </td>
        <td>
            <?php $total_remaining_second = $total_eli_pop - $total_vaccinated_second - $total_refused_second; ?>
            <b class="label label-warning" style="margin-right: 5%">{{ $total_remaining_second }}</b>
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
            '            <option value="frontline_health_workers" >Frontline Health Workers</option>\n' +
            '            <option value="indigent_senior_citizens" >Senior Citizens</option>\n' +
            '            <option value="remaining_indigent_population"  disabled>Remaining Indigent Population</option>\n' +
            '            <option value="uniform_personnel" disabled>Uniform Personnel</option>\n' +
            '            <option value="teachers_school_workers" disabled>Teachers & School Workers</option>\n' +
            '            <option value="all_government_workers" disabled>All Government Workers (National & Local)</option>\n' +
            '            <option value="essential_workers"  disabled>Essential Workers</option>\n' +
            '            <option value="socio_demographic" disabled>Socio-demographic groups & significant higher risk other than senior citizen and indigent population (e.g.PDL,PWD,IP,Filipinos living in high-density areas)</option>\n' +
            '            <option value="ofw" disabled >OFW\'s</option>\n' +
            '            <option value="remaining_workforce"  disabled>Other remaining workforce</option>\n' +
            '            <option value="remaining_filipino_citizen" disabled>Remaining Filipino Citizen</option>\n' +
            '            <option value="etc"  disabled >ETC.</option>\n' +
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


