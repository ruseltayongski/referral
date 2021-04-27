@extends('layouts.app')

@section('content')
    <style>
        @media (min-width: 0px) {
            .modal_w {
                width: 80%;
            }
        }

    </style>

    <div class="box box-success">
        <div class="box-header">
            <form action="{{ asset('vaccine/vaccineview') }}" method="GET">
                {{ csrf_field() }}
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="typeof_vaccine_filter" id="typeof_vaccine_filter" class="select2">
                                <option value="">Select Type of Vaccine</option>
                                <option value="Sinovac" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Sinovac')echo 'selected';} ?>>Sinovac</option>
                                <option value="Astrazeneca" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Astrazeneca')echo 'selected';} ?>>Astrazeneca</option>
                                <option value="Moderna" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Moderna')echo 'selected';} ?> disabled>Moderna</option>
                                <option value="Pfizer" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Pfizer')echo 'selected';} ?> disabled>Pfizer</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="province_id_filter" id="province_id_filter" class="select2" onchange="onChangeProvinceFilter($(this).val())">
                                <option value="">Select Province</option>
                                @foreach($province as $row)
                                    <option value="{{ $row->id }}"  <?php if(isset($vaccine->province_id)){if($vaccine->province_id == $row->id)echo 'selected';} ?> >{{ $row->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="priority" id="" class="select2">
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
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="date_range" placeholder="Enter date range.." name="date_range" value="{{ date("m/d/Y",strtotime($date_range_start)).' - '.date("m/d/Y",strtotime($date_range_end)) }}">
                        </div>
                        <div class="col-md-9">
                         <span class="input-group-btn">
                            <button type="submit" class="btn btn-success" onclick="load"><i class="fa fa-filter"></i> Filter</button>
                            <a href="{{ asset('vaccine/export/excel') }}" type="button" class="btn btn-danger"><i class="fa fa-file-excel-o"></i> Export Excel</a>
                            <button type="button" class="btn btn-warning" onclick="refreshPage()"><i class="fa fa-eye"></i> View All</button>
                             <!--
                            <button type="button" class="btn btn-primary" onclick="newVaccinated()"><i class="fa fa-eyedropper"></i> New Vaccinated</button>
                            -->
                        </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="row" style="padding-left: 1%;padding-right: 1%">
            <div class="col-lg-3">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>Sinovac</h3>
                        <p style="font-size:13pt" class="sinovac_count"></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-erlenmeyer-flask-bubbles"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>Astrazeneca</h3>

                        <p style="font-size:13pt" class="astrazeneca_count"></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-erlenmeyer-flask-bubbles"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>Moderna</h3>

                        <p style="font-size:13pt" class="moderna_count"></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-erlenmeyer-flask-bubbles"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>Pfizer</h3>

                        <p style="font-size:13pt" class="pfizer_count"></p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-erlenmeyer-flask-bubbles"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="box-body">
            @if(count($data)>0)
                <div class="row">
                    <div class="col-md-9">
                        <div class="table-responsive">
                            <table style="width: 100%;">
                                @foreach($data as $row)
                                    <?php

                                    $vaccine = \App\VaccineAccomplished::where("muncity_id",$row->id)->orderBy("date_first","asc")->first();
                                    //Eligible_pop_sinovac
                                    $total_eli_pop_sinovac_frontline = $row->frontline_health_workers;
                                    $total_eli_pop_sinovac_senior  = $row->senior_citizens;
                                    $total_eli_pop_sinovac = $total_eli_pop_sinovac_frontline + $total_eli_pop_sinovac_senior;
                                    //Eligible_pop_astrazeneca
                                    $total_eli_pop_astra_frontline = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','frontline_health_workers')")[0]->total_eli_pop;
                                    $total_eli_pop_astra_senior = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','indigent_senior_citizens')")[0]->total_eli_pop;
                                    $total_eli_pop_astra = $total_eli_pop_astra_frontline + $total_eli_pop_astra_senior;
                                    //vaccine_allocated
                                    $total_vaccine_allocated_sinovac_first = $row->sinovac_allocated_first;
                                    $total_vaccine_allocated_sinovac_second = $row->sinovac_allocated_second;
                                    $total_vaccine_allocated_astra_first = $row->astrazeneca_allocated_first;
                                    $total_vaccine_allocated_astra_second = $row->astrazeneca_allocated_second;

                                    //sinovac
                                    $total_sinovac_a1_first = 0;
                                    $total_sinovac_a2_first = 0;
                                    $total_sinovac_a1_second = 0;
                                    $total_sinovac_a2_second = 0;

                                    $total_sinovac_a1_first = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','frontline_health_workers')")[0]->vaccinated_first_a;
                                    $total_sinovac_a2_first = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','indigent_senior_citizens')")[0]->vaccinated_first_a;
                                    $total_sinovac_a1_second = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','frontline_health_workers')")[0]->vaccinated_second_a;
                                    $total_sinovac_a2_second = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','indigent_senior_citizens')")[0]->vaccinated_second_a;

                                    //astra
                                    $total_astra_a1_first = 0;
                                    $total_astra_a2_first = 0;
                                    $total_astra_a1_second = 0;
                                    $total_astra_a2_second = 0;

                                    $total_astra_a1_first = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','frontline_health_workers')")[0]->vaccinated_first_a;
                                    $total_astra_a2_first = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_first_a;
                                    $total_astra_a1_second = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','frontline_health_workers')")[0]->vaccinated_second_a;
                                    $total_astra_a2_second = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_second_a;



                                    //total_vaccinated_first
                                    $total_vaccinated_sinovac_first = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->vaccinated_first;
                                    $total_vaccinated_astra_first = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->vaccinated_first;

                                    //total_vaccinated_second
                                    $total_vaccinated_sinovac_second = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->vaccinated_second;
                                    $total_vaccinated_astra_second = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->vaccinated_second;

                                    //total_mild_first
                                    $total_mild_sinovac_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->mild_first;
                                    $total_mild_astra_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->mild_first;
                                    //total_mild_second
                                    $total_mild_sinovac_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->mild_second;
                                    $total_mild_astra_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->mild_second;
                                    //total_serious_first
                                    $total_serious_sinovac_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->serious_first;
                                    $total_serious_astra_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->serious_first;
                                    //total_serious_second
                                    $total_serious_sinovac_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->serious_second;
                                    $total_serious_astra_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->serious_second;
                                    //total_deferred_first
                                    $total_deferred_sinovac_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->deferred_first;
                                    $total_deferred_astra_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->deferred_first;
                                    //total_deferred_second
                                    $total_deferred_sinovac_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->deferred_second;
                                    $total_deferred_astra_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->deferred_second;
                                    //total_refused_first
                                    $total_refused_sinovac_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->refused_first;
                                    $total_refused_astra_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->refused_first;
                                    //total_refused_second
                                    $total_refused_sinovac_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->refused_second;
                                    $total_refused_astra_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->refused_second;
                                    //total_wastage_first
                                    $total_wastage_sinovac_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->wastage_first;
                                    $total_wastage_astra_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->wastage_first;
                                    //total_wastage_second
                                    $total_wastage_sinovac_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->wastage_second;
                                    $total_wastage_astra_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->wastage_second;

                                    $total_eli_pop = $total_eli_pop_sinovac + $total_eli_pop_astra;

                                    $total_vaccinated_first = $total_vaccinated_sinovac_first + $total_vaccinated_astra_first;
                                    $total_vaccinated_second = $total_vaccinated_sinovac_second + $total_vaccinated_astra_second;




                                    $total_percent_coverage_first = ($total_vaccinated_first /$total_eli_pop ) * 100;
                                    $total_percent_coverage_second = ($total_vaccinated_second / $total_eli_pop) * 100;



                                    //consumption_rate_first
                                    $total_consumption_rate_sinovac_first = number_format($total_vaccinated_sinovac_first / $total_vaccine_allocated_sinovac * 100,2);
                                    $total_consumption_rate_astra_first = number_format($total_vaccinated_astra_first / $total_vaccine_allocated_astra * 100,2);

                                    //total_consumption_rate
                                    $total_vaccine_allocated_first = $total_vaccine_allocated_sinovac + $total_vaccine_allocated_astra;
                                    $total_consumption_rate_first = number_format($total_vaccinated_first / $total_vaccine_allocated_first * 100,2);

                                    //consumpiton_rate_second
                                    $total_consumption_rate_sinovac_second = number_format($total_vaccinated_sinovac_second / $total_vaccine_allocated_sinovac * 100,2);
                                    $total_consumption_rate_astra_second = number_format($total_vaccinated_astra_second / $total_vaccine_allocated_astra * 100,2);

                                    //total_consumption_rate
                                    $total_vaccine_allocated_second = $total_vaccine_allocated_sinovac + $total_vaccine_allocated_astra;
                                    $total_consumption_rate_second = number_format($total_vaccinated_second / $total_vaccine_allocated_second * 100,2);

                                    //remaining_unvaccinated_first
                                    $total_remaining_unvaccinated_first_sinovac = $total_eli_pop_sinovac - $total_vaccinated_sinovac_first - $total_refused_sinovac_first;
                                    $total_remaining_unvaccinated_first_astra = $total_eli_pop_astra - $total_vaccinated_astra_first - $total_refused_astra_first;

                                    //total_remaining_unnvaccinated_second
                                    $total_remaining_unvaccinated_second_sinovac = $total_eli_pop_sinovac - $total_vaccinated_sinovac_second - $total_refused_sinovac_second;
                                    $total_remaining_unvaccinated_second_astra = $total_eli_pop_astra - $total_vaccinated_astra_second - $total_refused_astra_second;

                                    //total_refused_first
                                    $total_refused_first = $total_refused_sinovac_first + $total_refused_astra_first;
                                    //total_refused_second
                                    $total_refused_second = $total_refused_sinovac_second + $total_refused_astra_second;
                                    //total_remianing_unnvaccinated
                                    $total_remaining_unvaccinated_first = $total_eli_pop - $total_vaccinated_first - $total_refused_first;
                                    $total_remaining_unvaccinated_second = $total_eli_pop - $total_vaccinated_second - $total_refused_second;

                                    //percent_coverage_first
                                    $percent_coverage_sinovac_first = number_format($total_vaccinated_sinovac_first / $total_eli_pop_sinovac * 100,2);
                                    $percent_coverage_astra_first = number_format($total_vaccinated_astra_first / $total_eli_pop_astra * 100,2);
                                    //total_percent_coverage_first

                                    //percent_coverage_second
                                    $percent_coverage_sinovac_second = number_format($total_vaccinated_sinovac_second / $total_eli_pop_sinovac * 100,2);
                                    $percent_coverage_astra_second = number_format($total_vaccinated_astra_second / $total_eli_pop_astra * 100,2);

                                    ?>
                                    <tr>
                                        <td style="white-space: nowrap;" colspan="12">
                                            <b>
                                                <a  class="text-green" style= "font-size:14pt;cursor: pointer; " onclick="muncityVaccinated('<?php echo $row->province_id; ?>','<?php echo $row->id; ?>',$(this))">
                                                    {{ $row->description }}
                                                </a>
                                            </b>
                                            <button class="btn btn-link collapsed" style="color: red;" type="button" data-toggle="collapse" data-target="#collapse_sinovac{{ $row->id }}" aria-expanded="false" aria-controls="collapse_sinovac{{ $row->id }}">
                                                <b>Sinovac</b>
                                            </button>
                                            <button class="btn btn-link collapsed" style="color: darkgoldenrod;" type="button" data-toggle="collapse" data-target="#collapse_astra{{ $row->id }}" aria-expanded="false" aria-controls="collapse_astra{{ $row->id }}">
                                                <b>Astrazeneca</b>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="14">
                                            <table style="font-size: 8pt;" class="table" border="2">
                                                <tbody><tr>
                                                    <th>Type of Vaccine</th> <!-- Type of Vaccine 1-1 -->
                                                    <th colspan="3">
                                                        <center><a
                                                                    href="#facility_modal"
                                                                    data-toggle="modal"
                                                                    onclick="MunicipalityBody('<?php echo $province_id; ?>','<?php echo $row->id; ?>')"
                                                            >
                                                                Eligible Population
                                                            </a></center>
                                                    </th>
                                                    <th colspan="3">
                                                        <center><a
                                                                    href="#vaccine_modal_allocated"
                                                                    data-toggle="modal"
                                                                    onclick="vaccineAllocated('<?php echo $row->province_id; ?>','<?php echo $row->id; ?>')"
                                                            >
                                                                Vaccine Allocated
                                                            </a></center>
                                                    </th>
                                                    <th colspan="3">Total Vaccinated</th>
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
                                                </tbody><tbody id="collapse_sinovac{{ $row->id }}" class="collapse bg-danger" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                                <tr style="background-color: #ffd8d6">
                                                    <td rowspan="2">

                                                    </td> <!-- 1-3 -->
                                                    <td rowspan="2">{{ $total_eli_pop_sinovac_frontline }}</td>
                                                    <td rowspan="2">{{ $total_eli_pop_sinovac_senior }} </td>
                                                    <td rowspan="2">
                                                        <?php $total_eli_pop_sinovac = $total_eli_pop_sinovac_frontline + $total_eli_pop_sinovac_senior; ?>
                                                        {{ $total_eli_pop_sinovac }}
                                                    </td>
                                                    <td rowspan="2">{{ $total_vaccine_allocated_sinovac_first }}</td>
                                                    <td rowspan="2">{{ $total_vaccine_allocated_sinovac_second }}</td>
                                                    <td rowspan="2">
                                                        <?php $total_vaccine_allocated_sinovac = $total_vaccine_allocated_sinovac_first + $total_vaccine_allocated_sinovac_second;?>
                                                        {{ $total_vaccine_allocated_sinovac }}
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_sinovac_a1_first }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_sinovac_a2_first }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{  $total_vaccinated_sinovac_first }}</span>
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
                                                        <span class="label label-success">{{ $percent_coverage_sinovac_first }}%</span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_consumption_rate_sinovac_first }}%</span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_remaining_unvaccinated_first_sinovac }}</span>
                                                    </td>
                                                </tr>
                                                <tr style="background-color: #ffd8d6">
                                                    <td>
                                                        <span class="label label-warning">{{ $total_sinovac_a1_second }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_sinovac_a2_second }}</span>
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
                                                        <span class="label label-warning">{{ $percent_coverage_sinovac_second }}%</span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_consumption_rate_sinovac_second }}%</span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_remaining_unvaccinated_second_sinovac }} </span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                                <tbody><tr>

                                                </tr>
                                                </tbody><tbody id="collapse_astra{{ $row->id }}" class="collapse bg-primary" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                                <tr style="background-color: #f2fcac">
                                                    <td rowspan="2">

                                                    </td> <!-- 1-5 -->
                                                    <td rowspan="2" style="color:black;">{{ $total_eli_pop_astra_frontline }}</td>
                                                    <td rowspan="2" style="color:black;">{{ $total_eli_pop_astra_senior }}</td>
                                                    <td rowspan="2" style="color:black;">
                                                        <?php $total_eli_pop_astra = $total_eli_pop_astra_frontline + $total_eli_pop_astra_senior;?>
                                                        {{ $total_eli_pop_astra }}
                                                    </td>
                                                    <td rowspan="2" style="color:black;">{{ $total_vaccine_allocated_astra_first }}</td>
                                                    <td rowspan="2" style="color:black;">{{ $total_vaccine_allocated_astra_second }}</td>
                                                    <td rowspan="2" style="color:black;">
                                                        <?php $total_vaccine_allocated_astra = $total_vaccine_allocated_astra_first + $total_vaccine_allocated_astra_second;?>
                                                        {{ $total_vaccine_allocated_astra }}
                                                    </td>
                                                    <td style="color:black;">
                                                        <span class="label label-success">{{ $total_astra_a1_first }}</span>
                                                    </td>
                                                    <td style="color:black">
                                                        <span class="label label-success">{{ $total_astra_a2_first }}</span>
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
                                                        <span class="label label-success">{{ $percent_coverage_astra_first }}%</span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_consumption_rate_astra_first }}%</span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_remaining_unvaccinated_first_astra }}</span>
                                                    </td>
                                                </tr>
                                                <tr style="background-color: #f2fcac">
                                                    <td style="color: black;">
                                                        <span class="label label-warning">{{ $total_astra_a1_second }}</span>
                                                    </td>
                                                    <td style="color:black;">
                                                        <span class="label label-warning">{{ $total_astra_a2_second }}</span>
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
                                                        <span class="label label-warning">{{ $percent_coverage_astra_second }}%</span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_consumption_rate_astra_second }}%</span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_remaining_unvaccinated_second_astra }}</span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                                <tbody><tr>
                                                    <td>Total</td> <!-- 1-7 -->
                                                    <td>
                                                        <b> {{ $total_eli_pop_sinovac_frontline }} </b>
                                                    </td>
                                                    <td>
                                                        <b> {{ $total_eli_pop_sinovac_senior }} </b>
                                                    </td>
                                                    <td>
                                                        <b> <?php $total_eli_pop = $total_eli_pop_sinovac_frontline + $total_eli_pop_sinovac_senior;?>
                                                            {{ $total_eli_pop }}</b>
                                                    </td>
                                                    <td>
                                                        <?php $total_allocated_first = $total_vaccine_allocated_sinovac_first + $total_vaccine_allocated_astra_first;?>
                                                       <b>{{ $total_allocated_first }}</b>
                                                    </td>
                                                    <td>
                                                        <?php $total_allocated_second = $total_vaccine_allocated_sinovac_second + $total_vaccine_allocated_astra_second ?>
                                                        <b>{{ $total_allocated_second }}</b>
                                                    </td>
                                                    <td>
                                                        <?php $total_vaccine_allocated = $total_allocated_first + $total_allocated_second ?>
                                                        <b>{{ $total_vaccine_allocated }} </b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_sinovac_a1_first + $total_astra_a1_first }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_sinovac_a2_first + $total_astra_a2_first }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_vaccinated_first }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_mild_sinovac_first + $total_mild_astra_first }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_serious_sinovac_first + $total_serious_astra_first }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_deferred_sinovac_first + $total_deferred_astra_first }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_refused_first }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_wastage_sinovac_first + $total_wastage_astra_first }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ number_format($total_percent_coverage_first,2) }}%</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_consumption_rate_first }}%</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_remaining_unvaccinated_first }} </b>
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
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_sinovac_a1_second + $total_astra_a1_second }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_sinovac_a2_second + $total_astra_a2_second }} </b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_vaccinated_second }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_mild_sinovac_second + $total_mild_astra_second  }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_serious_sinovac_second + $total_serious_astra_second }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_deferred_sinovac_second + $total_deferred_astra_second }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_refused_second }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_wastage_sinovac_second + $total_wastage_astra_second }}</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ number_format($total_percent_coverage_second,2) }}%</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_consumption_rate_second }}%</b>
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_remaining_unvaccinated_second }}</b>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            <div>
                                {{ $data->links() }}
                            </div>
                            <hr>
                            <?php
                            //Eligible_pop_sinovac
                            $total_eli_pop_sinovac_frontline_prov =\DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','frontline_health_workers')")[0]->frontline_health_workers;
                            $total_eli_pop_sinovac_senior_prov  = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->senior_citizens;
                            $total_eli_pop_sinovac_prov = $total_eli_pop_sinovac_frontline_prov + $total_eli_pop_sinovac_senior_prov;
                            //Eligible_pop_astrazeneca
                            $total_eli_pop_astra_frontline_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->frontline_health_workers;
                            $total_eli_pop_astra_senior_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->senior_citizens;
                            $total_eli_pop_astra_prov = $total_eli_pop_astra_frontline_prov + $total_eli_pop_astra_senior_prov;
                            //vaccine_allocated
                            $total_vaccine_allocated_sinovac_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','frontline_health_workers')")[0]->vaccine_allocated_first;
                            $total_vaccine_allocated_sinovac_second_prov = $row->sinovac_allocated_second;
                            $total_vaccine_allocated_astra_first_prov = $row->astrazeneca_allocated_first;
                            $total_vaccine_allocated_astra_second_prov = $row->astrazeneca_allocated_second;



                            //sinovac
                            $total_sinovac_a1_first_prov = 0;
                            $total_sinovac_a2_first_prov= 0;
                            $total_sinovac_a1_second_prov = 0;
                            $total_sinovac_a2_second_prov = 0;

                            $total_sinovac_a1_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','frontline_health_workers')")[0]->vaccinated_first_a;
                            $total_sinovac_a2_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','indigent_senior_citizens')")[0]->vaccinated_first_a;
                            $total_sinovac_a1_second_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','frontline_health_workers')")[0]->vaccinated_second_a;
                            $total_sinovac_a2_second_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','indigent_senior_citizens')")[0]->vaccinated_second_a;

                            //astra
                            $total_astra_a1_first_prov = 0;
                            $total_astra_a2_first_prov = 0;
                            $total_astra_a1_second_prov = 0;
                            $total_astra_a2_second_prov = 0;

                            $total_astra_a1_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','frontline_health_workers')")[0]->vaccinated_first_a;
                            $total_astra_a2_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_first_a;
                            $total_astra_a1_second_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','frontline_health_workers')")[0]->vaccinated_second_a;
                            $total_astra_a2_second_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_second_a;



                            //total_vaccinated_first
                            $total_vaccinated_sinovac_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->vaccinated_first;
                            $total_vaccinated_astra_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->vaccinated_first;

                            //total_vaccinated_second
                            $total_vaccinated_sinovac_second_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->vaccinated_second;
                            $total_vaccinated_astra_second_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->vaccinated_second;

                            //total_mild_first
                            $total_mild_sinovac_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->mild_first;
                            $total_mild_astra_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->mild_first;
                            //total_mild_second
                            $total_mild_sinovac_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->mild_second;
                            $total_mild_astra_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->mild_second;
                            //total_serious_first
                            $total_serious_sinovac_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->serious_first;
                            $total_serious_astra_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->serious_first;
                            //total_serious_second
                            $total_serious_sinovac_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->serious_second;
                            $total_serious_astra_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->serious_second;
                            //total_deferred_first
                            $total_deferred_sinovac_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->deferred_first;
                            $total_deferred_astra_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->deferred_first;
                            //total_deferred_second
                            $total_deferred_sinovac_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->deferred_second;
                            $total_deferred_astra_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->deferred_second;
                            //total_refused_first
                            $total_refused_sinovac_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->refused_first;
                            $total_refused_astra_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->refused_first;
                            //total_refused_second
                            $total_refused_sinovac_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->refused_second;
                            $total_refused_astra_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->refused_second;
                            //total_wastage_first
                            $total_wastage_sinovac_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->wastage_first;
                            $total_wastage_astra_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->wastage_first;
                            //total_wastage_second
                            $total_wastage_sinovac_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->wastage_second;
                            $total_wastage_astra_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->wastage_second;

                            $total_eli_pop_prov = $total_eli_pop_sinovac_prov + $total_eli_pop_astra_prov;

                            $total_vaccinated_first_prov = $total_vaccinated_sinovac_first_prov + $total_vaccinated_astra_first_prov;
                            $total_vaccinated_second_prov = $total_vaccinated_sinovac_second_prov + $total_vaccinated_astra_second_prov;

                            $total_percent_coverage_first_prov = ($total_vaccinated_first_prov /$total_eli_pop_prov ) * 100;
                            $total_percent_coverage_second_prov = ($total_vaccinated_second_prov / $total_eli_pop_prov) * 100;

                            //consumption_rate_first
                            $total_consumption_rate_sinovac_first_prov = number_format($total_vaccinated_sinovac_first_prov / $total_vaccine_allocated_sinovac_prov * 100,2);
                            $total_consumption_rate_astra_first_prov = number_format($total_vaccinated_astra_first_prov / $total_vaccine_allocated_astra_prov * 100,2);

                            //total_consumption_rate
                            $total_vaccine_allocated_first_prov = $total_vaccine_allocated_sinovac_prov + $total_vaccine_allocated_astra_prov;
                            $total_consumption_rate_first_prov = number_format($total_vaccinated_first_prov / $total_vaccine_allocated_first_prov * 100,2);

                            //consumpiton_rate_second
                            $total_consumption_rate_sinovac_second_prov = number_format($total_vaccinated_sinovac_second_prov / $total_vaccine_allocated_sinovac_prov * 100,2);
                            $total_consumption_rate_astra_second_prov = number_format($total_vaccinated_astra_second_prov / $total_vaccine_allocated_astra_prov * 100,2);

                            //total_consumption_rate
                            $total_vaccine_allocated_second_prov = $total_vaccine_allocated_sinovac_prov + $total_vaccine_allocated_astra_prov;
                            $total_consumption_rate_second_prov = number_format($total_vaccinated_second_prov / $total_vaccine_allocated_second_prov * 100,2);

                            //remaining_unvaccinated_first
                            $total_remaining_unvaccinated_first_sinovac_prov = $total_eli_pop_sinovac_prov - $total_vaccinated_sinovac_first_prov - $total_refused_sinovac_first_prov;
                            $total_remaining_unvaccinated_first_astra_prov = $total_eli_pop_astra_prov - $total_vaccinated_astra_first_prov - $total_refused_astra_first_prov;

                            //total_remaining_unnvaccinated_second
                            $total_remaining_unvaccinated_second_sinovac_prov = $total_eli_pop_sinovac_prov - $total_vaccinated_sinovac_second_prov - $total_refused_sinovac_second_prov;
                            $total_remaining_unvaccinated_second_astra_prov = $total_eli_pop_astra_prov - $total_vaccinated_astra_second_prov - $total_refused_astra_second_prov;

                            //total_refused_first
                            $total_refused_first_prov = $total_refused_sinovac_first_prov + $total_refused_astra_first_prov;
                            //total_refused_second
                            $total_refused_second_prov = $total_refused_sinovac_second_prov + $total_refused_astra_second_prov;
                            //total_remianing_unnvaccinated
                            $total_remaining_unvaccinated_first_prov = $total_eli_pop_prov - $total_vaccinated_first_prov - $total_refused_first_prov;
                            $total_remaining_unvaccinated_second_prov = $total_eli_pop_prov - $total_vaccinated_second_prov - $total_refused_second_prov;

                            //percent_coverage_first
                            $percent_coverage_sinovac_first_prov = number_format($total_vaccinated_sinovac_first_prov / $total_eli_pop_sinovac_prov * 100,2);
                            $percent_coverage_astra_first_prov = number_format($total_vaccinated_astra_first_prov / $total_eli_pop_astra_prov * 100,2);
                            //total_percent_coverage_first

                            //percent_coverage_second
                            $percent_coverage_sinovac_second_prov = number_format($total_vaccinated_sinovac_second_prov / $total_eli_pop_sinovac_prov * 100,2);
                            $percent_coverage_astra_second_prov = number_format($total_vaccinated_astra_second_prov / $total_eli_pop_astra_prov * 100,2);
                            ?>
                            <h4>Grand Total</h4>
                            <button class="btn btn-link collapsed" style="color: red;" type="button" data-toggle="collapse" data-target="#collapse_sinovac_grand" aria-expanded="false" aria-controls="collapse_sinovac_grandtotal">
                                <b>Sinovac</b>
                            </button>
                            <button class="btn btn-link collapsed" style="color: darkgoldenrod;" type="button" data-toggle="collapse" data-target="#collapse_astra_grand" aria-expanded="false" aria-controls="collapse_astra_grandtotal">
                                <b>Astrazeneca</b>
                            </button>

                            <table style="font-size: 8pt;" class="table" border="2">
                                <tbody><tr>
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
                                    <th colspan="3">Total Vaccinated</th>
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
                                </tbody><tbody id="collapse_sinovac_grand" class="collapse bg-danger" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                <tr style="background-color: #ffd8d6">
                                    <td rowspan="2">

                                    </td> <!-- 1-3 -->
                                    <td rowspan="2">{{ $total_eli_pop_sinovac_frontline_prov }}</td>
                                    <td rowspan="2">{{ $total_eli_pop_sinovac_senior_prov }} </td>
                                    <td rowspan="2">
                                        <?php $total_eli_pop_sinovac_prov = $total_eli_pop_sinovac_frontline_prov + $total_eli_pop_sinovac_senior_prov; ?>
                                        {{ $total_eli_pop_sinovac_prov }}
                                    </td>
                                    <td rowspan="2">{{ $total_vaccine_allocated_sinovac_first_prov }}</td>
                                    <td rowspan="2">{{ $total_vaccine_allocated_sinovac_second_prov }}</td>
                                    <td rowspan="2"> {{ $total_vaccine_allocated_sinovac_prov }}</td>
                                    <td>
                                        <span class="label label-success">{{ $total_sinovac_a1_first_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_sinovac_a2_first_prov }}</span>
                                    </td>
                                    <td>
                                        <?php $total_vaccinated_sinovac_first_prov = $total_sinovac_a1_first_prov + $total_sinovac_a2_first_prov;?>
                                        <span class="label label-success">{{ $total_vaccinated_sinovac_first_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_mild_sinovac_first_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_serious_sinovac_first_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_deferred_sinovac_first_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_refused_sinovac_first_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_wastage_sinovac_first_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $percent_coverage_sinovac_first_prov }}%</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_consumption_rate_sinovac_first_prov }}%</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_remaining_unvaccinated_first_sinovac_prov }}</span>
                                    </td>
                                </tr>
                                <tr style="background-color: #ffd8d6">
                                    <td>
                                        <span class="label label-warning">{{ $total_sinovac_a1_second_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_sinovac_a2_second_prov }}</span>
                                    </td>
                                    <td>
                                        <?php $total_vaccinated_sinovac_second_prov = $total_sinovac_a1_second_prov + $total_sinovac_a2_second_prov; ?>
                                        <span class="label label-warning">{{ $total_vaccinated_sinovac_second_prov }}</span>
                                    </td> <!-- 1-4 -->
                                    <td>
                                        <span class="label label-warning">{{ $total_mild_sinovac_second_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_serious_sinovac_second_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_deferred_sinovac_second_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_refused_sinovac_second_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_wastage_sinovac_second_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $percent_coverage_sinovac_second_prov }}%</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_consumption_rate_sinovac_second_prov }}%</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_remaining_unvaccinated_second_sinovac_prov }} </span>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody><tr>

                                </tr>
                                </tbody><tbody id="collapse_astra_grand" class="collapse bg-primary" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                <tr style="background-color: #f2fcac">
                                    <td rowspan="2">

                                    </td> <!-- 1-5 -->
                                    <td rowspan="2" style="color:black;">{{ $total_eli_pop_astra_frontline_prov }}</td>
                                    <td rowspan="2" style="color:black;">{{ $total_eli_pop_astra_senior_prov }}</td>
                                    <td rowspan="2" style="color:black;">
                                        <?php $total_eli_pop_astra_prov = $total_eli_pop_astra_frontline_prov + $total_eli_pop_astra_senior_prov;?>
                                        {{ $total_eli_pop_astra_prov }}
                                    </td>
                                    <td rowspan="2"></td>
                                    <td rowspan="2"></td>
                                    <td rowspan="2" style="color:black;">{{ $total_vaccine_allocated_astra_prov }}</td>
                                    <td style="color:black;">
                                        <span class="label label-success">{{ $total_astra_a1_first_prov }}</span>
                                    </td>
                                    <td style="color:black">
                                        <span class="label label-success">{{ $total_astra_a2_first_prov }}</span>
                                    </td>
                                    <td>
                                        <?php $total_vaccinated_astra_first_prov = $total_astra_a1_first_prov + $total_astra_a2_first_prov;?>
                                        <span class="label label-success">{{ $total_vaccinated_astra_first_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_mild_astra_first_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_serious_astra_first_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_deferred_astra_first_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_refused_astra_first_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_wastage_astra_first_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $percent_coverage_astra_first_prov }}%</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_consumption_rate_astra_first_prov }}%</span>
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_remaining_unvaccinated_first_astra_prov }}</span>
                                    </td>
                                </tr>
                                <tr style="background-color: #f2fcac">
                                    <td style="color: black;">
                                        <span class="label label-warning">{{ $total_astra_a1_second_prov }}</span>
                                    </td>
                                    <td style="color:black;">
                                        <span class="label label-warning">{{ $total_astra_a2_second_prov }}</span>
                                    </td>
                                    <td>
                                        <?php $total_vaccinated_astra_second_prov = $total_astra_a1_second_prov + $total_astra_a2_second_prov;?>
                                        <span class="label label-warning">{{ $total_vaccinated_astra_second_prov }}</span>
                                    </td> <!-- 1-6 -->
                                    <td>
                                        <span class="label label-warning">{{ $total_mild_astra_second_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_serious_astra_second_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_deferred_astra_second_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_refused_astra_second_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_wastage_astra_second_prov }}</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $percent_coverage_astra_second_prov }}%</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_consumption_rate_astra_second_prov }}%</span>
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_remaining_unvaccinated_second_astra_prov }}</span>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody><tr>
                                    <td>Total</td> <!-- 1-7 -->
                                    <td>
                                        <b> {{ $total_eli_pop_sinovac_frontline_prov }} </b>
                                    </td>
                                    <td>
                                        <b> {{ $total_eli_pop_sinovac_senior_prov }} </b>
                                    </td>
                                    <td>
                                        <b> <?php $total_eli_pop_prov = $total_eli_pop_sinovac_frontline_prov + $total_eli_pop_sinovac_senior_prov;?>
                                            {{ $total_eli_pop_prov }}</b>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <b>
                                            {{ $total_vaccine_allocated_first_prov }} </b>
                                    </td>
                                    <td>
                                        <?php $total_first_prov = $total_sinovac_a1_first_prov + $total_astra_a1_first_prov; ?>
                                        <b class="label label-success" style="margin-right: 5%">{{ $total_first_prov}}</b>
                                    </td>
                                    <td>
                                        <?php $total_second_prov = $total_sinovac_a2_first_prov + $total_astra_a2_first_prov;?>
                                        <b class="label label-success" style="margin-right: 5%">{{ $total_second_prov }}</b>
                                    </td>
                                    <td>
                                        <?php $total_vaccinated_first_prov = $total_first_prov + $total_second_prov;?>
                                        <b class="label label-success" style="margin-right: 5%">{{ $total_vaccinated_first_prov }} </b>
                                    </td>
                                    <td>
                                        <b class="label label-success" style="margin-right: 5%">{{ $total_mild_sinovac_first_prov + $total_mild_astra_first_prov }}</b>
                                    </td>
                                    <td>
                                        <b class="label label-success" style="margin-right: 5%">{{ $total_serious_sinovac_first_prov + $total_serious_astra_first_prov }}</b>
                                    </td>
                                    <td>
                                        <b class="label label-success" style="margin-right: 5%">{{ $total_deferred_sinovac_first_prov + $total_deferred_astra_first_prov }}</b>
                                    </td>
                                    <td>
                                        <b class="label label-success" style="margin-right: 5%">{{ $total_refused_first_prov }}</b>
                                    </td>
                                    <td>
                                        <b class="label label-success" style="margin-right: 5%">{{ $total_wastage_sinovac_first_prov + $total_wastage_astra_first_prov }}</b>
                                    </td>
                                    <td>
                                        <b class="label label-success" style="margin-right: 5%">{{ number_format($total_percent_coverage_first_prov,2) }}%</b>
                                    </td>
                                    <td>
                                        <b class="label label-success" style="margin-right: 5%">{{ $total_consumption_rate_first_prov }}%</b>
                                    </td>
                                    <td>
                                        <b class="label label-success" style="margin-right: 5%">{{ $total_remaining_unvaccinated_first_prov }} </b>
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
                                        <?php $total_first_prov1 = $total_sinovac_a1_second_prov + $total_astra_a1_second_prov; ?>
                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_first_prov1 }}</b>
                                    </td>
                                    <td>
                                        <?php $total_second_prov1 = $total_sinovac_a2_second_prov + $total_astra_a2_second_prov; ?>
                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_second_prov1 }} </b>
                                    </td>
                                    <td>
                                        <?php $total_vaccinated_second_prov = $total_first_prov1 + $total_second_prov1;?>
                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_vaccinated_second_prov }} </b>
                                    </td>
                                    <td>
                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_mild_sinovac_second_prov + $total_mild_astra_second_prov  }}</b>
                                    </td>
                                    <td>
                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_serious_sinovac_second_prov + $total_serious_astra_second_prov }}</b>
                                    </td>
                                    <td>
                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_deferred_sinovac_second_prov + $total_deferred_astra_second_prov }}</b>
                                    </td>
                                    <td>
                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_refused_second_prov }}</b>
                                    </td>
                                    <td>
                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_wastage_sinovac_second_prov + $total_wastage_astra_second_prov }}</b>
                                    </td>
                                    <td>
                                        <b class="label label-warning" style="margin-right: 5%">{{ number_format($total_percent_coverage_second_prov,2) }}%</b>
                                    </td>
                                    <td>
                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_consumption_rate_second_prov }}%</b>
                                    </td>
                                    <td>
                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_remaining_unvaccinated_second_prov }}</b>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-3" style="padding-right: 2%">
                        <div id="chartContainer1" style="height: 370px; width: 100%;"></div><br><br><br>
                        <div id="chartContainer2" style="height: 370px; width: 100%;"></div><br><br><br>
                        <div id="chartContainer3" style="height: 370px; width: 100%;"></div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                <span class="text-warning">
                    <i class="fa fa-warning"></i> No Municipality found!
                </span>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade"  role="dialog" data-backdrop="static" data-keyboard="false" id="vaccine_modal_municipality" style="min-width: 100%">
        <div class="modal-dialog modal-lg modal_w" role="document">
            <div class="modal-content">
                <div class="modal-body vaccinated_content_municipality">

                </div><!-- /.modal-content -->
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <div class="modal fade" role="dialog" id="vaccine_modal_allocated">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body vaccine_allocated_modal">
                    <center>
                        <img src="{{ asset('resources/img/loading.gif') }}" alt="">
                    </center>
                </div><!-- /.modal-content -->
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->



    @include('admin.modal.facility_modal')
@endsection
@section('js')
    @include('script.chart')
    <script>
        <?php $user = Session::get('auth'); ?>
        $("#container").removeClass("container");
        $("#container").addClass("container-fluid");

        function muncityVaccinated(province_id,muncity_id,data){
            $("#vaccine_modal_municipality").modal('show');
            $(".vaccinated_content_municipality").html(loading);
            $("a").css("background-color","");
            data.css("background-color","yellow");
            var url = "<?php echo asset('vaccine/vaccinated/municipality/content').'/'; ?>"+province_id+"/"+muncity_id;
            $.get(url,function(data){
                setTimeout(function(){
                    $(".vaccinated_content_municipality").html(data);
                    $(".select2").select2({ width: '100%' });
                },500);
            });
        }

        function MunicipalityBody(province_id,muncity_id){
            var json;
            if(muncity_id == 'empty'){
                json = {
                    "province_id" : province_id,
                    "_token" : "<?php echo csrf_token()?>"
                };
            } else {
                json = {
                    "province_id" : province_id,
                    "muncity_id" : muncity_id ,
                    "_token" : "<?php echo csrf_token()?>"
                };
            }
            var url = "<?php echo asset('admin/municipality/crud/body') ?>";
            $.post(url,json,function(result){
                $(".facility_body").html(result);
            })
        }

        function vaccineAllocated(province_id,muncity_id){
            var url = "<?php echo asset('vaccine/vaccine_allocated_modal'); ?>";
            json = {
                "province_id" : province_id,
                "muncity_id" : muncity_id ,
                "_token" : "<?php echo csrf_token()?>"
            };
            $.post(url,json,function(result){
                $(".vaccine_allocated_modal").html(result);
            })
        }

        function MunicipalityDelete(muncity_id){
            $(".muncity_id").val(muncity_id);
        }

        @if(Session::get('municipality'))
        Lobibox.notify('success', {
            title: "",
            msg: "<?php echo Session::get("municipality_message"); ?>",
            size: 'mini',
            rounded: true
        });
        <?php
        Session::put("municipality",false);
        Session::put("municipality_message",false)
        ?>
        @endif

        @if(Session::get("vaccine_saved"))
        <?php
        Session::put('vaccine_saved',false);
        ?>
        Lobibox.notify('success', {
            size: 'mini',
            rounded: true,
            msg: 'Your vaccination record is successfully saved!'
        });
        @endif

            window.onload = function() {

            var options1 = {
                title: {
                    text: "Vaccination"
                },
                data: [
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "column",
                        dataPoints: [
                            { label: "Sinovac",  y: 10  },
                            { label: "AstraZeneca", y: 15  },
                            { label: "Pfizer", y: 25  },
                            { label: "Sputnik V",  y: 30  },
                            { label: "Moderna",  y: 28  }
                        ]
                    }
                ]
            };
            $("#chartContainer1").CanvasJSChart(options1);

            var options2 = {
                title: {
                    text: "Priority"
                },
                data: [
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "column",
                        dataPoints: [
                            { label: "(A1",  y: 10  },
                            { label: "(A2)", y: 15  },
                            { label: "(A3)", y: 25  },
                            { label: "(A4)",  y: 30  },
                            { label: "(A5)",  y: 28  },
                            { label: "(A5)",  y: 28  }
                        ]
                    }
                ]
            };
            $("#chartContainer2").CanvasJSChart(options2);


            var options3 = {
                title: {
                    text: "Total Percentage"
                },
                data: [{
                    type: "pie",
                    startAngle: 45,
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabel: "{label} ({y})",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: [
                        { label: "Percent Coverage", y: 36 },
                        { label: "Consumption Rate", y: 31 },
                        { label: "Remaining Unvaccinated", y: 7 },
                        { label: "Twitter", y: 7 },
                        { label: "Facebook", y: 6 },
                        { label: "Google", y: 10 },
                        { label: "Others", y: 3 }
                    ]
                }]
            };
            $("#chartContainer3").CanvasJSChart(options3);

        };

    </script>
@endsection
