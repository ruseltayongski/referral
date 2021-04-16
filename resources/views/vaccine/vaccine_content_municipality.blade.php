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
                    <th>Type Of Vaccine</th>
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
                $total_vaccinated_first = 0;
                $total_vaccinated_second = 0;
                $total_mild_first = 0;
                $total_mild_second = 0;
                $total_serious_first = 0;
                $total_serious_second = 0;
                $total_refused_first = 0;
                $total_refused_second = 0;
                $total_deferred_first = 0;
                $total_deferred_second = 0;
                $total_wastage_first = 0;
                $total_wastage_second = 0;
                $total_consumption_rate_first = 0;
                $total_consumption_rate_second = 0;
                $total_percent_coverage_first = 0;
                $total_percent_coverage_second = 0;
                $total_eli_pop = 0;
                $no_eli_pop = 0;
                $front_line_health_workers = 0;
                $front_line_health_workers_flag = false;
                $senior_citizen = 0;
                $senior_citizen_flag = false;
                $sinovac_allocated = 0;
                $astrazeneca_allocated =0;
                $sinovac_allocated_flag = false;
                $astrazeneca_allocated_flag = false;
                $total_vaccine_allocated_sinovac = 0;
                $total_vaccine_allocated_astrazeneca = 0;
            ?>
            @if(count($vaccine_accomplishment)>0)
                @foreach($vaccine_accomplishment as $vaccine)
                    <?php
                        $total_vaccinated_first += $vaccine->vaccinated_first;
                        $total_vaccinated_second += $vaccine->vaccinated_second;
                        $total_mild_first += $vaccine->mild_first;
                        $total_mild_second += $vaccine->mild_second;
                        $total_serious_first += $vaccine->serious_first;
                        $total_serious_second += $vaccine->serious_second;
                        $total_refused_first += $vaccine->refused_first;
                        $total_refused_second += $vaccine->refused_second;
                        $total_deferred_first += $vaccine->deferred_first;
                        $total_deferred_second += $vaccine->deferred_second;
                        $total_wastage_first += $vaccine->wastage_first;
                        $total_wastage_second += $vaccine->wastage_second;
                        $total_eli_pop += $vaccine->total_eli_pop;
                        $total_percent_coverage_first += $total_vaccinated_first / $total_eli_pop * 100;
                        $total_percent_coverage_second += $total_vaccinated_second / $total_eli_pop * 100;

                        if($vaccine->priority == 'frontline_health_workers' && !$front_line_health_workers_flag){
                            $front_line_health_workers = $vaccine->no_eli_pop;
                            $front_line_health_workers_flag = true;
                        }
                        if($vaccine->priority == 'indigent_senior_citizens' && !$senior_citizen_flag){
                            $senior_citizen = $vaccine->no_eli_pop;
                            $senior_citizen_flag = true;
                        }

                        if($vaccine->typeof_vaccine == 'Sinovac' && !$sinovac_allocated_flag){
                            $sinovac_allocated = $vaccine->vaccine_allocated;
                            $sinovac_allocated_flag = true;
                        }
                        if($vaccine->typeof_vaccine == 'Astrazeneca' && !$astrazeneca_allocated_flag){
                            $astrazeneca_allocated = $vaccine->vaccine_allocated;
                            $astrazeneca_allocated_flag = true;
                        }

                    ?>
                    <tr style="background-color: #59ab91">
                        <input type="hidden" name="province_id" value="{{ $province_id }}">
                        <input type="hidden" name="muncity_id" value="{{ $muncity_id }}">
                        <td style="width: 15%">
                            <input type="text" id="date_picker{{ $vaccine->id.$vaccine->encoded_by }}" name="date_first[]" value="<?php if(isset($vaccine->date_first)) echo date('m/d/Y',strtotime($vaccine->date_first)) ?>" class="form-control" required>
                        </td>
                        <td style="width: 15%" rowspan="2">
                            <select name="typeof_vaccine[]" id="typeof_vaccine" class="select2" required>
                                <option value="">Select Option</option>
                                <option value="Sinovac" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Sinovac')echo 'selected';} ?>>Sinovac</option>
                                <option value="Astrazeneca" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Astrazeneca')echo 'selected';} ?>>Astrazeneca</option>
                                <option value="Moderna" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Moderna')echo 'selected';} ?> disabled>Moderna</option>
                                <option value="Pfizer" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Pfizer')echo 'selected';} ?> disabled>Pfizer</option>
                            </select>
                           <br><br> <input type="text" name="vaccine_allocated[]" value="<?php if(isset($vaccine->vaccine_allocated)) echo $vaccine->vaccine_allocated ?>" class="form-control" readonly>
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
                            <br><br><input type="text" name="no_eli_pop[]" id="no_eli_pop{{ $vaccine->id.$vaccine->encoded_by }}" value="{{ $vaccine->no_eli_pop }}" class="form-control" readonly>
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
</form>

<table  class="table table-striped" >
    <tr>
        <th>Total Eligible Population</th>
        <th>Total Vaccine Allocated</th>
        <th>Total Vaccinated</th>
        <th>Total Mild Qty </th>
        <th>Total Serious Qty </th>
        <th>Total Deferred </th>
        <th>Total Refused </th>
        <th>Total Wastage </th>
        <th>Total Consumption Rate</th>
        <th>Total Percent Coverage</th>
        <th>Total Remaining Unvaccinated</th>
    </tr>
    <tr>
        <td>
            <span class="label label-danger"></span>
        </td>
        <td>
            <span class="label label-danger">{{ $sinovac_allocated }}</span>
        </td>
        <td>
            <span class="label label-danger"></span>
        </td>
        <td>
            <span class="label label-danger"></span>
        </td>
        <td>
            <span class="label label-danger"></span>
        </td>
        <td>
            <span class="label label-danger"></span>
        </td>
        <td>
            <span class="label label-danger"></span>
        </td>
        <td>
            <span class="label label-danger"></span>
        </td>
        <td>
            <span class="label label-danger"></span>
        </td>
        <td>
            <span class="label label-danger">{{ $front_line_health_workers }}</span>
        </td>
        <td>
            <span class="label label-danger"></span>
        </td>
    </tr>
    <tr>
        <td>
           <span class="label label-primary"></span>
        </td>
        <td>
            <span class="label label-primary">{{ $astrazeneca_allocated }}</span>
        </td>
        <td>
            <span class="label label-primary"></span>
        </td>
        <td>
            <span class="label label-primary"></span>
        </td>
        <td>
            <span class="label label-primary"></span>
        </td>
        <td>
            <span class="label label-primary"></span>
        </td>
        <td>
            <span class="label label-primary"></span>
        </td>
        <td>
            <span class="label label-primary"></span>
        </td>
        <td>
            <span class="label label-primary"></span>
        </td>
        <td>
            <span class="label label-primary">{{ $senior_citizen }}</span>
        </td>
        <td>
            <span class="label label-primary"></span>
        </td>
    </tr>
    <tr>
        <td>
           <span class="label label-success">{{ $front_line_health_workers }}</span>
        </td>
        <td>
            <span class="label label-success"></span>
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_vaccinated_first) }}</span>
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_mild_first) }}</span>
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_serious_first) }}</span>
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_deferred_first) }}</span>
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_refused_first) }}</span>
        </td>
        <td>
            <span class="label label-success">{{ number_format($total_wastage_first) }}</span>
        </td>
        <td>
            <span class="label label-success">{{ $senior_citizen }}</span>
        </td>
        <td>
            <span class="label label-success"></span>
        </td>
        <td>
            <span class="label label-success">{{ $senior_citizen }}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="label label-warning">{{ $senior_citizen }}</span>
        </td>
        <td>
            <span class="label label-warning"></span>
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_vaccinated_second) }}</span>
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_mild_second) }}</span>
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_serious_second) }}</span>
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_deferred_second) }}</span>
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_refused_second) }}</span>
        </td>
        <td>
            <span class="label label-warning">{{ number_format($total_wastage_second) }}</span>
        </td>
        <td>
            <span class="label label-warning">{{ $front_line_health_workers+$senior_citizen }}</span>
        </td>
        <td>
            <span class="label label-warning"></span>
        </td>
        <td>
            <span class="label label-warning">{{ $front_line_health_workers+$senior_citizen }}</span>
        </td>
    </tr>
    <tr>
        <td>
            <b style="margin-left:2%">{{ $front_line_health_workers+$senior_citizen }}</b>
        </td>
        <td>
            <b style="margin-left:2%">{{ $sinovac_allocated+$astrazeneca_allocated }}</b>
        </td>
        <td>
            <b style="margin-left:2%">{{ number_format($total_vaccinated_first + $total_vaccinated_second) }}</b>
        </td>
        <td>
            <b style="margin-left:2%">{{ number_format($total_mild_first + $total_mild_second) }}</b>
        </td>
        <td>
            <b style="margin-left:2%">{{ number_format($total_serious_first + $total_serious_second) }}</b>
        </td>
        <td>
            <b style="margin-left:2%">{{ number_format($total_deferred_first + $total_deferred_second) }}</b>
        </td>
        <td>
            <b style="margin-left:2%">{{ number_format($total_refused_first + $total_refused_second) }}</b>
        </td>
        <td>
            <b style="margin-left:2%">{{  number_format($total_wastage_first + $total_wastage_second) }}</b>
        </td>
        <td>
            <b style="margin-left:2%">{{ $front_line_health_workers+$senior_citizen }}</b>
        </td>
        <td>
            <b style="margin-left:2%">{{ $front_line_health_workers+$senior_citizen }}</b>
        </td>
        <td>
            <b style="margin-left:2%">{{ $front_line_health_workers+$senior_citizen }}</b>
        </td>
    </tr>
</table>
<!--
<div class="row">
    <div class="col-sm-3 col-xs-6">
        <div class="description-block border-right">
            <span class="description-header total_vaccinated">{{ number_format($total_vaccinated_first + $total_vaccinated_second) }}</span>
            <h5 class="description-header"><span class="text-green total_vaccinated_first">{{ number_format($total_vaccinated_first) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow total_vaccinated_second">{{ number_format($total_vaccinated_second) }}</span></h5>
            <span class="description-text">TOTAL VACCINATED</span>
        </div>
    </div>
    <div class="col-sm-3 col-xs-6">
        <div class="description-block border-right">
            <span class="description-header total_vaccinated_aefi">{{ number_format($total_mild_first + $total_mild_second) }}</span>
            <h5 class="description-header"><span class="text-green total_mild_first" >{{ number_format($total_mild_first) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow total_mild_second">{{ number_format($total_mild_second) }}</span></h5>
            <span class="description-text">TOTAL MILD QUANTITY</span>
        </div>
    </div>
    <div class="col-sm-3 col-xs-6">
        <div class="description-block border-right">
            <span class="description-header total_vaccinated_aefi2">{{ number_format($total_serious_first + $total_serious_second) }}</span>
            <h5 class="description-header"><span class="text-green total_serious_first">{{ number_format($total_serious_first) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow total_serious_second">{{ number_format($total_serious_second) }}</span></h5>
            <span class="description-text">TOTAL SERIOUS QUANTITY</span>
        </div>
    </div>
    <div class="col-sm-3 col-xs-6">
        <div class="description-block border-right">
            <span class="description-header total_deferred">{{ number_format($total_deferred_first + $total_deferred_second) }}</span>
            <h5 class="description-header"><span class="text-green total_deferred_first">{{ number_format($total_deferred_first) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow total_deferred_second">{{ number_format($total_deferred_second) }}</span></h5>
            <span class="description-text">TOTAL DEFERRED</span>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-sm-3 col-xs-6">
        <div class="description-block border-right">
            <span class="description-header total_refused">{{ number_format($total_refused_first + $total_refused_second) }}</span>
            <h5 class="description-header"><span class="text-green total_refused_first">{{ number_format($total_refused_first) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow total_refused_second">{{ number_format($total_refused_second) }}</span></h5>
            <span class="description-text">TOTAL REFUSED</span>
        </div>
    </div>

    <div class="col-sm-3 col-xs-6">
        <div class="description-block">
            <span class="description-header total_wastage">{{ number_format($total_wastage_first + $total_wastage_second) }}</span>
            <h5 class="description-header"><span class="text-green total_wastage_first">{{ number_format($total_wastage_first) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow total_wastage_second">{{ number_format($total_wastage_second) }}</span></h5>
            <span class="description-text">TOTAL WASTAGE</span>
        </div>
    </div>
    <div class="col-sm-3 col-xs-6">
        <div class="description-block">
            <span class="description-header">{{ $front_line_health_workers+$senior_citizen }}</span>
            <h5 class="description-header"><span class="text-green total_remainingunvaccinated_first bg-green"><span style="color: white;padding: 2%;">{{ $front_line_health_workers }}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="bg-yellow total_remainingunvaccinated_second"><span style="color: white;padding: 2%;">{{ $senior_citizen }}</span></span></h5>
            <span class="description-text">TOTAL ELIGIBLE POPULATION</span>
        </div>
    </div>
    <div class="col-sm-3 col-xs-6">
        <div class="description-block">
            <span class="description-header">{{ $sinovac_allocated+$astrazeneca_allocated }}</span>
            <h5 class="description-header"><span class="text-green total_remainingunvaccinated_first bg-red"><span style="color: white;padding: 2%;">{{ $sinovac_allocated }}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="bg-blue total_remainingunvaccinated_second"><span style="color: white;padding: 2%;">{{ $astrazeneca_allocated }}</span></span></h5>
            <span class="description-text">TOTAL VACCINE ALLOCATED</span>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-4 col-xs-6">
        <div class="description-block">
            <span class="description-percentage text-green total_remaining_unvaccinated"><i class="fa fa-caret-up"></i></span>
            <h5 class="description-header"><span class="text-green total_remainingunvaccinated_first">{{ number_format($total_eli_pop - $total_vaccinated_first - $total_refused_first ) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow total_remainingunvaccinated_second">{{ number_format( ($total_eli_pop - $numof_vaccinated_total_second - $total_refused_second)) }}</span></h5>
            <span class="description-text">TOTAL REMAINING UNVACCINATED</span>
        </div>
    </div>
    <div class="col-sm-4 col-xs-6">
        <div class="description-block">
            <span class="description-header">{{ $front_line_health_workers+$senior_citizen }}</span>
            <h5 class="description-header"><span class="text-green total_remainingunvaccinated_first bg-green"><span style="color: white;padding: 2%;">{{ $front_line_health_workers }}</span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="bg-yellow total_remainingunvaccinated_second"><span style="color: white;padding: 2%;">{{ $senior_citizen }}</span></span></h5>
            <span class="description-text">TOTAL CONSUMPTION RATE</span>
        </div>
    </div>
    <div class="col-sm-4 col-xs-6">
        <div class="description-block">
            <span class="description-header total_percent_coverage">{{ number_format(($total_percent_coverage_first + $otal_percent_coverage_second) / $total_eli_pop * 100, 2) }}%</span>
            <h5 class="description-header"><span style="color:white; padding: 0.5%;" class="bg-red total_percentcoverage_first">{{ number_format($total_percent_coverage_first,2) }}%</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span  style="color:white; padding: 0.5%;" class="bg-blue total_percentcoverage_second">{{ number_format($total_percent_coverage_second,2) }}%</span></h5>
            <span class="description-text">TOTAL PERCENT COVERAGE</span>
        </div>
    </div>
</div> -->


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
            '       <br><br><input type="text" id="vaccine_allocated'+count+'" name="vaccine_allocated[]" class="form-control" readonly>\n' +
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
            $("#vaccine_allocated"+count).val(data);
        });
    }

</script>


