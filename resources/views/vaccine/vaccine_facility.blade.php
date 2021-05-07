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
                                <option value="a1" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'a1')echo 'selected';} ?>>A1</option>
                                <option value="a2" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'a2')echo 'selected';} ?>>A2</option>
                                <option value="a3" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'a3')echo 'selected';} ?> disabled>A3</option>
                                <option value="a4" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'a4')echo 'selected';} ?> disabled>A4</option>
                                <option value="a5" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'a5')echo 'selected';} ?> disabled>A5</option>
                                <option value="b1" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'b1')echo 'selected';} ?> disabled>B1</option>
                                <option value="b2" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'b2')echo 'selected';} ?> disabled>B2</option>
                                <option value="b3" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'b3')echo 'selected';} ?> disabled>B3</option>
                                <option value="b4" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'b4')echo 'selected';} ?> disabled >B4</option>
                                <option value="b5" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'b5')echo 'selected';} ?> disabled>B5</option>
                                <option value="b6" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'b6')echo 'selected';} ?> disabled>B6/option>
                                <option value="c" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'c')echo 'selected';} ?> disabled >C</option>
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
                            <button type="submit" class="btn btn-success" onclick=""><i class="fa fa-filter"></i> Filter</button>
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
                        <p style="font-size:13pt" class="sinovac_dashboard">0</p>
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

                        <p style="font-size:13pt" class="astra_dashboard">0</p>
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

                        <p style="font-size:13pt" class="moderna_count">0</p>
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

                        <p style="font-size:13pt" class="pfizer_count">0</p>
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

                                    $total_epop_svac_frtline = $row->frontline_health_workers; //TOTAL_E_POP_FRONTLINE_SINOVAC
                                    $total_epop_svac_sr = $row->senior_citizens; //E_POP_SENIOR_SINOVAC
                                    $total_epop_svac = $total_epop_svac_frtline + $total_epop_svac_sr; //E_POP_SINOVAC FIRST

                                    $total_epop_astra_frtline = $row->frontline_health_workers; //TOTAL_E_POP_FRONTLINE_ASTRA
                                    $total_epop_astra_sr = $row->senior_citizens; //TOTAL_E_POP_SENIOR_ASTRA
                                    $total_epop_astra = $total_epop_astra_frtline + $total_epop_astra_sr; //TOTAL_E_POP_ASTRA

                                    //VACCINE_ALLOCATED
                                    $total_vallocated_svac_frst = $row->sinovac_allocated_first; //VACCINE ALLOCATED_SINOVAC (FD)
                                    $total_vallocated_svac_scnd = $row->sinovac_allocated_second; //VACCINE ALLOCATED_SINOVAC (SD)
                                    $total_vallocated_astra_frst = $row->astrazeneca_allocated_first; //VACCINE ALLOCATED_ASTRA (FD)
                                    $total_vallocated_astra_scnd = $row->astrazeneca_allocated_second; //VACCINE ALLOCATED_ASTRA (SD)

                                    //SINOVAC
                                    $total_svac_a1_frst = 0; //A1_SINOVAC
                                    $total_svac_a2_frst = 0; //A2_SINOVAC
                                    $total_svac_a1_scnd = 0; //A1_SINOVAC2
                                    $total_svac_a2_scnd = 0; //A2_SINOVAC2

                                    $total_svac_a1_frst = \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','frontline_health_workers')")[0]->vaccinated_first_a;
                                    $total_svac_a2_frst = \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','indigent_senior_citizens')")[0]->vaccinated_first_a;
                                    $total_svac_a1_scnd = \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','frontline_health_workers')")[0]->vaccinated_second_a;
                                    $total_svac_a2_scnd = \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','indigent_senior_citizens')")[0]->vaccinated_second_a;

                                    //ASTRACENECA
                                    $total_astra_a1_frst = 0; //A1_ASTRA
                                    $total_astra_a2_frst = 0; //A2_ASTRA
                                    $total_astra_a1_scnd = 0; //A1_ASTRA2
                                    $total_astra_a2_scnd = 0; //A2_ASTRA2

                                    $total_astra_a1_frst = \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','frontline_health_workers')")[0]->vaccinated_first_a;
                                    $total_astra_a2_frst = \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_first_a;
                                    $total_astra_a1_scnd = \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','frontline_health_workers')")[0]->vaccinated_second_a;
                                    $total_astra_a2_scnd = \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_second_a;


                                    $total_vcted_svac_frst = \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','')")[0]->vaccinated_first; //VACCINATED_SINOVAC
                                    $total_vcted_astra_frst = \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','')")[0]->vaccinated_first; //TOTAL VACCINATED_ASTRA

                                    $total_vcted_svac_scnd = \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','')")[0]->vaccinated_second; //TOTAL_VACCINATED_SINOVAC 2
                                    $total_vcted_astra_scnd = \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','')")[0]->vaccinated_second; //TOTAL VACCINATED_ASTRA 2

                                    $total_mild_svac_frst =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','')")[0]->mild_first; //MILD_SINOVAC
                                    $total_mild_astra_frst =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','')")[0]->mild_first; //MILD_ASTRA

                                    $total_mild_svac_scnd =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','')")[0]->mild_second; //MILD_SINOVAC 2
                                    $total_mild_astra_scnd =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','')")[0]->mild_second; //MILD_ASTRA 2

                                    $total_srs_svac_frst =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','')")[0]->serious_first; //SERIOUS_SINOVAC
                                    $total_srs_astra_frst =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','')")[0]->serious_first; //SERIOUS_ASTRA

                                    $total_srs_svac_scnd =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','')")[0]->serious_second; //SERIOUS_SINOVAC 2
                                    $total_srs_astra_scnd =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','')")[0]->serious_second; //SERIOUS_ASTRA2

                                    $total_dfrd_svac_frst =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','')")[0]->deferred_first; //DEFERRED_SINOVAC
                                    $total_dfrd_astra_frst =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','')")[0]->deferred_first; //DEFERRED_ASTRA

                                    $total_dfrd_svac_scnd =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','')")[0]->deferred_second; //DEFERRED_SINOVAC 2
                                    $total_dfrd_astra_scnd =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','')")[0]->deferred_second; //DEFERRED_ASTRA 2

                                    $total_rfsd_svac_frst =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','')")[0]->refused_first; //REFUSED_SINOVAC
                                    $total_rfsd_astra_frst =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','')")[0]->refused_first; //REFUSED_ASTRA

                                    $total_rfsd_svac_scnd =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','')")[0]->refused_second; //REFUSED_SINOVAC 2
                                    $total_rfsd_astra_scnd =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','')")[0]->refused_second; //REFUSED_ASTRA 2

                                    $total_wstge_svac_frst =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','')")[0]->wastage_first; //WASTAGF_SINOVAC
                                    $total_wstge_astra_frst =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','')")[0]->wastage_first; //WASTAGE_ASTRA

                                    $total_wstge_svac_scnd =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Sinovac','')")[0]->wastage_second; //WASTAGE_SINOVAC 2
                                    $total_wstge_astra_scnd =  \DB::connection('mysql')->select("call vaccine_facility('$vaccine->facility_id','Astrazeneca','')")[0]->wastage_second; //WASTAGE_ASTRA2


                                    $total_vcted_frst = $total_vcted_svac_frst + $total_vcted_astra_frst; //TOTAL_VACCINATED
                                    $total_vcted_scnd = $total_vcted_svac_scnd + $total_vcted_astra_scnd; //TOTAL_VACCINATED - 2

                                    $total_vallocated_svac = $total_vallocated_svac_frst + $total_vallocated_svac_scnd; //TOTAL VACCINE ALLOCATED_SINOVAC
                                    $total_vallocated_astra = $total_vallocated_astra_frst + $total_vallocated_astra_scnd; //TOTAL VACCINE ALLOCATED_ASTRA
                                    $total_vallocated  = $total_vallocated_svac + $total_vallocated_astra; //TOTAL_VACCINE_ALLOCATED

                                    $total_vallocated_frst =  $total_vallocated_svac_frst + $total_vallocated_astra_frst; //TOTAL_VACCINE_ALLOCATED_FIRST
                                    $total_vallocated_scnd = $total_vallocated_svac_scnd + $total_vallocated_astra_scnd; //TOTAL_VACCINE_ALLOCATED_SECOND

                                    $total_rfsd_frst = $total_rfsd_svac_frst + $total_rfsd_astra_frst; //TOTAL_REFUSED
                                    $total_rfsd_scnd = $total_rfsd_svac_scnd + $total_rfsd_astra_scnd; //TOTAL_REFUSED - 2

                                    $p_cvrge_svac_frst = $total_vcted_svac_frst / $total_epop_svac * 100; //PERCENT_COVERAGE_SINOVAC
                                    $p_cvrge_astra_frst = $total_vcted_astra_frst / $total_epop_astra * 100; //PERCENT_COVERAGE_ASTRA

                                    $p_cvrge_svac_scnd = $total_vcted_svac_scnd / $total_epop_svac * 100; //PERCENT_COVERAGE_SINOVAC 2
                                    $p_cvrge_astra_scnd = $total_vcted_astra_scnd / $total_epop_astra * 100; //PERCENT_COVERAGE_ASTRA 2

                                    $total_p_cvrge_frst = $total_vcted_frst / $total_epop_astra * 100; //TOTAL_PERCENT_COVERAGE
                                    $total_p_cvrge_scnd = $total_vcted_scnd / $total_epop_astra * 100; //TOTAL_PERCENT_COVERAGE - 2

                                    $total_c_rate_svac_frst = $total_vcted_svac_frst / $total_vallocated_svac_frst * 100; //CONSUMPTION RATE_SINOVAC
                                    $total_c_rate_astra_frst = $total_vcted_astra_frst / $total_vallocated_astra_frst * 100; //CONSUMPTION RATE ASTRA

                                    $total_c_rate_svac_scnd = $total_vcted_svac_scnd / $total_vallocated_svac_scnd * 100; //CONSUMPTION RATE SINOVAC 2
                                    $total_c_rate_astra_scnd = $total_vcted_astra_scnd / $total_vallocated_astra_scnd * 100; //CONSUMPTION_RATE_ASTRA 2

                                    $total_c_rate_frst = $total_vcted_frst / $total_vallocated_frst * 100; //TOTAL CONSUMPTION_RATE
                                    $total_c_rate_scnd = $total_vcted_scnd / $total_vallocated_scnd * 100; //TOTAL_CONSUMPTION_RATE - 2

                                    $total_r_unvcted_frst_svac = $total_epop_svac - $total_vcted_svac_frst - $total_rfsd_svac_frst; //REMAINING UNVACCINATED_SINOVAC
                                    $total_r_unvcted_frst_astra = $total_epop_astra - $total_vcted_astra_frst - $total_rfsd_astra_frst; //REMAINUNG UNVACCINATED_ASTRA

                                    $total_r_unvcted_scnd_svac = $total_epop_svac - $total_vcted_svac_scnd - $total_rfsd_svac_scnd; //REMAINING UNVACCINATED_SINOVAC 2
                                    $total_r_unvcted_scnd_astra = $total_epop_astra - $total_vcted_astra_scnd - $total_rfsd_astra_scnd; //REMAINUNG_UNVACCIANTED_ASTRA 2

                                    $total_r_unvcted_frst = $total_epop_svac - $total_vcted_frst - $total_rfsd_frst;
                                    $total_r_unvcted_scnd = $total_epop_astra - $total_vcted_scnd - $total_rfsd_scnd;

                                    ?>
                                    <tr>
                                        <td style="white-space: nowrap;" colspan="12">
                                            <b>
                                                <a class="text-green" style= "font-size:14pt;cursor: pointer; " onclick="facilityVaccinated('<?php echo $row->id; ?>',$(this))">
                                                    {{ $row->name }}
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
                                                    <th colspan="5">
                                                        <center><a
                                                                    href="#facility_modal"
                                                                    data-toggle="modal"
                                                                    onclick="facilityBody('<?php echo $province_id; ?>','<?php echo $row->id; ?>')"
                                                            >
                                                                Eligible Population
                                                            </a></center>
                                                    </th>
                                                    <th colspan="3">
                                                        <center><a
                                                                    href="#vaccine_facility_allocated"
                                                                    data-toggle="modal"
                                                                    onclick="vaccineFacilityAllocated('<?php echo $row->province; ?>','<?php echo $row->id; ?>')"
                                                            >
                                                                Vaccine Allocated
                                                            </a></center>
                                                    </th>
                                                    <th colspan="5"><center>Total Vaccinated</center></th>
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
                                                </tbody><tbody id="collapse_sinovac{{ $row->id }}" class="collapse bg-danger" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                                <tr style="background-color: #ffd8d6">
                                                    <td rowspan="2">
                                                    </td> <!-- 1-3 -->
                                                    <td rowspan="2">{{ $total_epop_svac_frtline }}</td> <!-- TOTAL_E_POP_FRONTLINE_SINOVAC   -->
                                                    <td rowspan="2">{{ $total_epop_svac_sr }}</td> <!-- E_POP_SENIOR_SINOVAC -->
                                                    <td rowspan="2"></td>
                                                    <td rowspan="2"></td>
                                                    <td rowspan="2">{{ $total_epop_svac }}</td> <!-- E_POP_SINOVAC FIRST  -->
                                                    <td rowspan="2">{{ $total_vallocated_svac_frst }}</td> <!-- VACCINE ALLOCATED_SINOVAC (FD)  -->
                                                    <td rowspan="2">{{ $total_vallocated_svac_scnd }}</td> <!-- VACCINE ALLOCATED_SINOVAC (SD)  -->
                                                    <td rowspan="2">{{ $total_vallocated_svac }}</td>  <!-- TOTAL VACCINE ALLOCATED_SINOVAC   -->
                                                    <td>
                                                        <span class="label label-success">{{ $total_svac_a1_frst }}</span> <!-- A1_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_svac_a2_frst }}</span> <!-- A2_SINOVAC -->
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <span class="label label-success">{{  $total_vcted_svac_frst }}</span><!-- TOTAL VACCINATED_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_mild_svac_frst }}</span> <!-- MILD_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_srs_svac_frst }}</span>  <!-- SERIOUS_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_dfrd_svac_frst }}</span>  <!-- DEFERRED_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_rfsd_svac_frst }}</span>  <!-- REFUSED_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_wstge_svac_frst }}</span>  <!-- WASTAGF_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ number_format($p_cvrge_svac_frst,2) }}%</span>  <!-- PERCENT_COVERAGE_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ number_format($total_c_rate_svac_frst,2) }}%</span>  <!-- CONSUMPTION RATE_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_r_unvcted_frst_svac }}</span>  <!-- REMAINING UNVACCINATED_SINOVAC -->
                                                    </td>
                                                </tr>
                                                <tr style="background-color: #ffd8d6">
                                                    <td>
                                                        <span class="label label-warning">{{ $total_svac_a1_scnd }}</span>   <!-- A1_SINOVAC2 -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_svac_a2_scnd }}</span> <!-- A2_SINOVAC2 -->
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_vcted_svac_scnd }}</span> <!-- TOTAL_VACCINATED_SINOVAC 2-->
                                                    </td> <!-- 1-4 -->
                                                    <td>
                                                        <span class="label label-warning">{{ $total_mild_svac_scnd }}</span> <!-- MILD_SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_srs_svac_scnd }}</span> <!-- SERIOUS_SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_dfrd_svac_scnd }}</span> <!-- DEFERRED_SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_rfsd_svac_scnd }}</span> <!-- REFUSED_SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_wstge_svac_scnd }}</span> <!--WASTAGE_SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ number_format($p_cvrge_svac_scnd,2) }}%</span> <!-- PERCENT_COVERAGE_SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ number_format($total_c_rate_svac_scnd,2) }}%</span> <!-- CONSUMPTION RATE SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_r_unvcted_scnd_svac }} </span> <!-- REMAINING UNVACCINATED_SINOVAC 2 -->
                                                    </td>
                                                </tr>
                                                </tbody>
                                                <tbody><tr>

                                                </tr>
                                                </tbody><tbody id="collapse_astra{{ $row->id }}" class="collapse bg-primary" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                                <tr style="background-color: #f2fcac">
                                                    <td rowspan="2">

                                                    </td> <!-- 1-5 -->
                                                    <td rowspan="2" style="color:black;">{{ $total_epop_astra_frtline }}</td> <!-- TOTAL_E_POP_FRONTLINE_ASTRA -->
                                                    <td rowspan="2" style="color:black;">{{ $total_epop_astra_sr }}</td>  <!-- TOTAL_E_POP_SENIOR_ASTRA -->
                                                    <td rowspan="2"></td>
                                                    <td rowspan="2"></td>
                                                    <td rowspan="2" style="color:black;">{{ $total_epop_astra }} </td>  <!-- TOTAL_E_POP_ASTRA -->
                                                    <td rowspan="2" style="color:black;">{{ $total_vallocated_astra_frst }}</td>  <!-- VACCINE ALLOCATED_ASTRA (FD) -->
                                                    <td rowspan="2" style="color:black;">{{ $total_vallocated_astra_scnd }}</td>  <!-- VACCINE ALLOCATED_ASTRA (SD) -->
                                                    <td rowspan="2" style="color:black;">{{ $total_vallocated_astra }}</td>  <!-- TOTAL VACCINE ALLOCATED_ASTRA -->
                                                    <td style="color:black;">
                                                        <span class="label label-success">{{ $total_astra_a1_frst }}</span>  <!-- A1_ASTRA  -->
                                                    </td>
                                                    <td style="color:black">
                                                        <span class="label label-success">{{ $total_astra_a2_frst }}</span>  <!-- A2_ASTRA  -->
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_vcted_astra_frst }}</span>  <!-- TOTAL VACCINATED_ASTRA-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_mild_astra_frst }}</span> <!-- MILD_ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_srs_astra_frst }}</span>  <!-- SERIOUS_ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_dfrd_astra_frst }}</span> <!-- DEFERRED_ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_rfsd_astra_frst }}</span> <!-- REFUSED_ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_wstge_astra_frst }}</span> <!-- WASTAGE_ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ number_format($p_cvrge_astra_frst,2) }}%</span> <!-- PERCENT_COVERAGE_ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ number_format($total_c_rate_astra_frst,2) }}%</span> <!-- CONSUMPTION RATE ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_r_unvcted_frst_astra }}</span> <!-- REMAINUNG UNVACCINATED_ASTRA  -->
                                                    </td>
                                                </tr>
                                                <tr style="background-color: #f2fcac">
                                                    <td style="color: black;">
                                                        <span class="label label-warning">{{ $total_astra_a1_scnd }}</span>  <!-- A1_ASTRA2  -->
                                                    </td>
                                                    <td style="color:black;">
                                                        <span class="label label-warning">{{ $total_astra_a2_scnd }}</span>  <!-- A2_ASTRA2  -->
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_vcted_astra_scnd }}</span> <!-- TOTAL VACCINATED_ASTRA 2-->
                                                    </td> <!-- 1-6 -->
                                                    <td>
                                                        <span class="label label-warning">{{ $total_mild_astra_scnd }}</span> <!-- MILD_ASTRA 2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_srs_astra_scnd }}</span> <!-- SERIOUS_ASTRA2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_dfrd_astra_scnd }}</span> <!-- DEFERRED_ASTRA 2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_rfsd_astra_scnd }}</span> <!-- REFUSED_ASTRA 2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_wstge_astra_scnd }}</span> <!-- WASTAGE_ASTRA2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ number_format($p_cvrge_astra_scnd,2)}}%</span> <!-- PERCENT_COVERAGE_ASTRA 2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ number_format($total_c_rate_astra_scnd,2) }}%</span> <!-- CONSUMPTION_RATE_ASTRA 2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_r_unvcted_scnd_astra }}</span> <!-- REMAINUNG_UNVACCIANTED_ASTRA 2  -->
                                                    </td>
                                                </tr>
                                                </tbody>
                                                <tbody><tr>
                                                    <td>Total</td> <!-- 1-7 -->
                                                    <td>{{ $total_epop_astra_frtline }}</td> <!-- TOTAL_FRONTLINE  -->
                                                    <td>{{ $total_epop_astra_sr }}</td> <!-- TOTAL_SENIOR  -->
                                                    <td></td>
                                                    <td></td>
                                                    <td>{{ $total_epop_astra }}</td> <!-- TOTAL_E_POP -->
                                                    <td>
                                                        <b>{{ $total_vallocated_frst }}</b> <!-- TOTAL_VACCINE_ALLOCATED_FIRST  -->
                                                    </td>
                                                    <td>
                                                        <b>{{ $total_vallocated_scnd }} </b> <!-- TOTAL_VACCINE_ALLOCATED_SECOND  -->
                                                    </td>
                                                    <td>
                                                        <b>{{$total_vallocated }}</b> <!-- TOTAL_VACCINE_ALLOCATED  -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_svac_a1_frst + $total_astra_a1_frst }}</b> <!-- TOTAL_A1  -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_svac_a2_frst + $total_astra_a2_frst }}</b> <!-- TOTAL_A2  -->
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_vcted_frst }}</b> <!-- TOTAL_VACCINATED  -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_mild_svac_first + $total_mild_astra_frst }}</b> <!-- TOTAL_MILD -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_srs_svac_frst + $total_srs_astra_frst }}</b>  <!-- TOTAL_SERIOUS -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_dfrd_svac_frst + $total_dfrd_astra_frst }}</b>  <!-- TOTAL_DEFERRED -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_rfsd_frst }}</b>  <!-- TOTAL_REFUSED -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_wstge_svac_frst + $total_wstge_astra_frst }}</b>  <!-- TOTAL_WASTAGE -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ number_format($total_p_cvrge_frst,2) }}%</b>  <!-- TOTAL_PERCENT_COVERAGE -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ number_format($total_c_rate_frst,2) }}%</b>  <!-- TOTAL CONSUMPTION_RATE -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_r_unvcted_frst }}</b>  <!-- REMAINUNG_UNVACCINATED -->
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
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_svac_a1_scnd + $total_astra_a1_scnd }}</b>  <!-- TOTAL_A1 - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_svac_a2_scnd + $total_astra_a2_scnd }} </b>  <!-- TOTAL_A2 - 2 -->
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_scnd }}</b>  <!-- TOTAL_VACCINATED - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_mild_svac_scnd + $total_mild_astra_scnd  }}</b>  <!-- TOTAL_MILD - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_srs_svac_scnd + $total_srs_astra_scnd }}</b> <!-- TOTAL_SERIOUS - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_dfrd_svac_scnd + $total_dfrd_astra_scnd }}</b> <!-- TOTAL_DEFERRED - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_rfsd_scnd }}</b> <!-- TOTAL_REFUSED - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_wstge_svac_scnd + $total_wstge_astra_scnd }}%</b> <!-- TOTAL_WASTAGE - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{number_format($total_p_cvrge_scnd,2)}}%</b> <!-- TOTAL_PERCENT_COVERAGE - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ number_format($total_c_rate_scnd,2)}}%</b> <!-- TOTAL_CONSUMPTION_RATE - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_r_unvcted_scnd }}</b> <!-- REMAINING_UNVACCINATED - 2 -->
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
                            @include('vaccine.vaccine_facility_overall')
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
                    <i class="fa fa-warning"></i> No Facility Found!
                </span>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade"  role="dialog" data-backdrop="static" data-keyboard="false" id="vaccine_modal_facility" style="min-width: 100%">
        <div class="modal-dialog modal-lg modal_w" role="document">
            <div class="modal-content">
                <div class="modal-body vaccinated_content_facility">

                </div><!-- /.modal-content -->
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <div class="modal fade" role="dialog" id="vaccine_facility_allocated">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body vaccine_facility_allocated">
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
        $(".sinovac_dashboard").text({{ Session::get("sinovac_dashboard") }});
        $(".astra_dashboard").text({{ Session::get("astra_dashboard") }});

        <?php $user = Session::get('auth'); ?>
        $("#container").removeClass("container");
        $("#container").addClass("container-fluid");

        function facilityVaccinated(facility_id,data){
            $("#vaccine_modal_facility").modal('show');
            $(".vaccinated_content_facility").html(loading);
            $("a").css("background-color","");
            data.css("background-color","yellow");
            var url = "<?php echo asset('vaccine/facility_content'); ?>"+"/"+facility_id;
            $.get(url,function(data){
                setTimeout(function(){
                    $(".vaccinated_content_facility").html(data);
                    $(".select2").select2({ width: '100%' });
                },500);
            });
        }

        function facilityBody(province_id,facility_id){
            var json;
            if(facility_id == 'empty'){
                json = {
                    "province_id" : province_id,
                    "_token" : "<?php echo csrf_token()?>"
                };
            } else {
                json = {
                    "province_id" : province_id,
                    "facility_id" : facility_id ,
                    "_token" : "<?php echo csrf_token()?>"
                };
            }
            var url = "<?php echo asset('vaccine/facility_eligible_pop') ?>";
            $.post(url,json,function(result){
                $(".facility_body").html(result);
            })
        }

        function vaccineFacilityAllocated(province_id,facility_id){
            var url = "<?php echo asset('vaccine/facility_allocated'); ?>";
            json = {
                "province_id" : province_id,
                "facility_id" : facility_id ,
                "_token" : "<?php echo csrf_token()?>"
            };
            $.post(url,json,function(result){
                $(".vaccine_facility_allocated").html(result);
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

            var sinovac_dashboard = <?php if(Session::get('sinovac_dashboard')) echo Session::get('sinovac_dashboard'); else echo 0; ?>;
            var astra_dashboard = <?php if(Session::get('astra_dashboard')) echo Session::get('astra_dashboard'); else echo 0; ?>;
            var options1 = {
                title: {
                    text: "Type of Vaccine"
                },
                data: [
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "column",
                        dataPoints: [
                            { label: "Sinovac",  y: sinovac_dashboard },
                            { label: "AstraZeneca", y: astra_dashboard },
                            { label: "Pfizer", y: 0  },
                            { label: "Sputnik V",  y: 0  },
                            { label: "Moderna",  y: 0  }
                        ]

                    }
                ]

            };

            $("#chartContainer1").CanvasJSChart(options1);

            var total_e_pop_frtline_prov = <?php if(Session::get('total_e_pop_frtline_prov')) echo Session::get('total_e_pop_frtline_prov'); else echo 0; ?>;
            var total_e_pop_sr_prov = <?php if(Session::get('total_e_pop_sr_prov')) echo Session::get('total_e_pop_sr_prov'); else echo 0; ?>;
            var options2 = {
                title: {
                    text: "Priority"
                },
                data: [
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "column",
                        dataPoints: [
                            { label: "(A1)",  y: total_e_pop_frtline_prov},
                            { label: "(A2)", y: total_e_pop_sr_prov },
                            { label: "(A3)", y: 0 },
                            { label: "(A4)",  y: 0  },
                            { label: "(A5)",  y: 0  },
                            { label: "(A5)",  y: 0  }
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
                    type: "doughnut",
                    startAngle: 45,
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabel: "{label} ({y})",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: [
                        { label: "Percent Coverage", y: 36 },
                        { label: "Consumption Rate", y: 31 },
                        { label: "Remaining Unvaccinated", y: 7 }

                    ]
                }]
            };
            $("#chartContainer3").CanvasJSChart(options3);

        };

    </script>

@endsection
