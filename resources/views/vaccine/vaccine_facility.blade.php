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
            <form action="{{ asset('vaccine/facility').'/'.$tri_city }}" method="GET">
                {{ csrf_field() }}
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <select name="typeof_vaccine_filter" id="typeof_vaccine_filter" class="select2">
                                <option value="">Select Type of Vaccine</option>
                                <option value="Sinovac" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'Sinovac')echo 'selected';} ?>>Sinovac</option>
                                <option value="Astrazeneca" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'Astrazeneca')echo 'selected';} ?>>Astrazeneca</option>
                                <option value="Moderna" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'Moderna')echo 'selected';} ?> disabled>Moderna</option>
                                <option value="Pfizer" <?php if(isset($typeof_vaccine_filter)){if($typeof_vaccine_filter == 'Pfizer')echo 'selected';} ?> disabled>Pfizer</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select name="muncity_filter" id="muncity_filter" class="select2">
                                <option value="">Select Tricity</option>
                                @foreach($facility as $row)
                                    <option value="{{ $row->id }}" <?php if(isset($muncity_filter)){if($muncity_filter == $row->id)echo 'selected';} ?> >{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="date_range" placeholder="Enter date range.." name="date_range" value="{{ date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)) }}">
                        </div>
                        <div class="col-md-9">
                         <span class="input-group-btn">
                            <button type="submit" class="btn btn-success" onclick="loadPage()"><i class="fa fa-filter"></i> Filter</button>
                            <a href="{{ asset('vaccine/export/excel') }}" type="button" class="btn btn-danger"><i class="fa fa-file-excel-o"></i> Export Excel</a>
                            <a href="{{ asset('vaccine/facility').'/'.$tri_city }}" type="button" class="btn btn-warning" onclick="loadPage()"><i class="fa fa-eye"></i> View All</a>
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

                                    $total_epop_svac_a1 = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $row->a1 : 0; // A1 EPOP SINOVAC
                                    $total_epop_svac_a2 = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $row->a2 : 0; // A2 EPOP SINOVAC
                                    $total_epop_svac_a3 = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $row->a3 : 0; // A3 EPOP SINOVAC
                                    $total_epop_svac_a4 = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $row->a4 : 0; // A4 EPOP SINOVAC
                                    $total_epop_svac = $total_epop_svac_a1 + $total_epop_svac_a2 + $total_epop_svac_a3 + $total_epop_svac_a4; //TOTAL_E_POP_SINOVAC 

                                    $total_epop_astra_a1 = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $row->a1: 0; //A1_EPOP ASTRA
                                    $total_epop_astra_a2 = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $row->a2: 0; //A2_EPOP ASTRA
                                    $total_epop_astra_a3 = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $row->a3: 0; //A3_EPOP ASTRA
                                    $total_epop_astra_a4 = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $row->a4: 0; //A4_EPOP ASTRA
                                    $total_epop_astra = $total_epop_astra_a1 + $total_epop_astra_a2 + $total_epop_astra_a3 + $total_epop_astra_a4; //TOTAL_E_POP_ASTRA

                                    //VACCINE_ALLOCATED
                                    $total_vallocated_svac_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $row->sinovac_allocated_first : 0; //VACCINE ALLOCATED_SINOVAC (FD)
                                    $total_vallocated_svac_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? $row->sinovac_allocated_second : 0; //VACCINE ALLOCATED_SINOVAC (SD)
                                    $total_vallocated_astra_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $row->astrazeneca_allocated_first : 0; //VACCINE ALLOCATED_ASTRA (FD)
                                    $total_vallocated_astra_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? $row->astrazeneca_allocated_second :0; //VACCINE ALLOCATED_ASTRA (SD)

                                    //SINOVAC

                                    $total_svac_a1_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','a1')")[0]->vaccinated_first : 0; //A1_SINOVAC
                                    $total_svac_a2_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','a2')")[0]->vaccinated_first : 0; //A2_SINOVAC
                                    $total_svac_a3_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','a3')")[0]->vaccinated_first : 0; //A3_SINOVAC
                                    $total_svac_a4_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','a4')")[0]->vaccinated_first : 0;  //A4_SINOVAC
                                    $total_svac_a1_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','a1')")[0]->vaccinated_second : 0; //A1_SINOVAC 2
                                    $total_svac_a2_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','a2')")[0]->vaccinated_second : 0; //A2_SINOVAC 2
                                    $total_svac_a3_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','a3')")[0]->vaccinated_second : 0; //A3_SINOVAC 2
                                    $total_svac_a4_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','a4')")[0]->vaccinated_second : 0; //A4_SINOVAC 2

                                    //ASTRACENECA

                                    $total_astra_a1_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','a1')")[0]->vaccinated_first : 0; //A1_ASTRA
                                    $total_astra_a2_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','a2')")[0]->vaccinated_first : 0; //A2_ASTRA
                                    $total_astra_a3_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','a3')")[0]->vaccinated_first : 0; //A3_ASTRA
                                    $total_astra_a4_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','a4')")[0]->vaccinated_first : 0; //A4_ASTRA
                                    $total_astra_a1_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','a1')")[0]->vaccinated_second : 0; //A1_ASTRA 2
                                    $total_astra_a2_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','a2')")[0]->vaccinated_second : 0; //A2_ASTRA 2
                                    $total_astra_a3_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','a3')")[0]->vaccinated_second : 0; //A3_ASTRA 2
                                    $total_astra_a4_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','a4')")[0]->vaccinated_second : 0; //A4_ASTRA 2


                                    $total_vcted_svac_frst =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','')")[0]->vaccinated_first : 0; //VACCINATED_SINOVAC
                                    $total_vcted_astra_frst =  $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','')")[0]->vaccinated_first : 0; //TOTAL VACCINATED_ASTRA

                                    $total_vcted_svac_scnd =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','')")[0]->vaccinated_second : 0; //TOTAL_VACCINATED_SINOVAC 2
                                    $total_vcted_astra_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','')")[0]->vaccinated_second : 0; //TOTAL VACCINATED_ASTRA 2

                                    $total_mild_svac_frst =   $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','')")[0]->mild_first : 0; //MILD_SINOVAC
                                    $total_mild_astra_frst =   $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','')")[0]->mild_first : 0; //MILD_ASTRA

                                    $total_mild_svac_scnd =   $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','')")[0]->mild_second : 0; //MILD_SINOVAC 2
                                    $total_mild_astra_scnd =  $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','')")[0]->mild_second : 0; //MILD_ASTRA 2

                                    $total_srs_svac_frst =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','')")[0]->serious_first : 0; //SERIOUS_SINOVAC
                                    $total_srs_astra_frst =  $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','')")[0]->serious_first : 0; //SERIOUS_ASTRA

                                    $total_srs_svac_scnd = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','')")[0]->serious_second : 0; //SERIOUS_SINOVAC 2
                                    $total_srs_astra_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','')")[0]->serious_second : 0; //SERIOUS_ASTRA2

                                    $total_dfrd_svac_frst =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','')")[0]->deferred_first : 0; //DEFERRED_SINOVAC
                                    $total_dfrd_astra_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','')")[0]->deferred_first : 0; //DEFERRED_ASTRA

                                    $total_dfrd_svac_scnd =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','')")[0]->deferred_second : 0; //DEFERRED_SINOVAC 2
                                    $total_dfrd_astra_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','')")[0]->deferred_second : 0; //DEFERRED_ASTRA 2

                                    $total_rfsd_svac_frst = $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','')")[0]->refused_first : 0; //REFUSED_SINOVAC
                                    $total_rfsd_astra_frst = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','')")[0]->refused_first : 0; //REFUSED_ASTRA

                                    $total_rfsd_svac_scnd =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','')")[0]->refused_second : 0; //REFUSED_SINOVAC 2
                                    $total_rfsd_astra_scnd = $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','')")[0]->refused_second : 0; //REFUSED_ASTRA 2

                                    $total_wstge_svac_frst =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','')")[0]->wastage_first : 0; //WASTAGF_SINOVAC
                                    $total_wstge_astra_frst =  $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','')")[0]->wastage_first : 0; //WASTAGE_ASTRA

                                    $total_wstge_svac_scnd =  $typeof_vaccine_filter == "Sinovac" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Sinovac','')")[0]->wastage_second : 0; //WASTAGE_SINOVAC 2
                                    $total_wstge_astra_scnd =  $typeof_vaccine_filter == "Astrazeneca" || empty($typeof_vaccine_filter) ? \DB::connection('mysql')->select("call vaccine_facility('$row->id','Astrazeneca','')")[0]->wastage_second : 0; //WASTAGE_ASTRA2


                                    $total_vcted_svac_frst = $total_svac_a1_frst + $total_svac_a2_frst + $total_svac_a3_frst + $total_svac_a4_frst; //TOTAL_VACCINATED_SINOVAC_FIRST
                                    $total_vcted_svac_scnd = $total_svac_a1_scnd + $total_svac_a2_scnd + $total_svac_a3_scnd + $total_svac_a4_scnd; //TOTAL_VACCINATED_SINOVAC_SECOND

                                    $total_vcted_astra_frst = $total_astra_a1_frst + $total_astra_a2_frst + $total_astra_a3_frst + $total_astra_a4_frst; //TOTAL_VACCINATED_ASTRA_FIRST
                                    $total_vcted_astra_scnd = $total_astra_a1_scnd + $total_astra_a2_scnd + $total_astra_a3_scnd + $total_astra_a4_scnd; //TOTAL_VACCINATED_ASTRA_SECOND

                                    $total_vcted_frst = $total_vcted_svac_frst +  $total_vcted_astra_frst; //TOTAL_VACCINATED_FIRST //DARAAAA
                                    $total_vcted_scnd = $total_vcted_svac_scnd +  $total_vcted_astra_scnd; //TOTAL_VACCINATED_SECOND


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
                                                <a class="text-green" style= "font-size:14pt;cursor: pointer; " onclick="facilityVaccinated('<?php echo $row->id; ?>','<?php echo $date_start; ?>','<?php echo $date_end; ?>',$(this))">
                                                    {{ $row->name }}
                                                </a>
                                            </b>
                                            <button class="btn btn-link collapsed" style="color: red;" type="button" data-toggle="collapse" data-target="#collapse_sinovac{{ $row->id }}" aria-expanded="false" aria-controls="collapse_sinovac{{ $row->id }}">
                                                <b>Sinovac</b>
                                            </button>
                                            <button class="btn btn-link collapsed" style="color: darkgoldenrod;" type="button" data-toggle="collapse" data-target="#collapse_astra{{ $row->id }}" aria-expanded="false" aria-controls="collapse_astra{{ $row->id }}">
                                                <b>Astrazeneca</b>
                                            </button>
                                            <br>
                                            @foreach(\App\VaccineAccomplished::where("facility_id",$row->id)->whereBetween("date_first",[$date_start,$date_end])->orderBy("date_first","asc")->get() as $x)
                                                <span class="badge bg-blue">
                                                    {{ date("F d,Y",strtotime($x->date_first)) }}
                                                </span>
                                                <?php $count++; ?>
                                                @if($count == 12)
                                                    <br>
                                                    <?php $count = 0; ?>
                                                @endif
                                            @endforeach
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
                                                    <td rowspan="2" class="total_epop_svac_a1{{ $row->id }}">{{ $total_epop_svac_a1 }}</td> <!-- A1 EPOP SINOVAC  -->
                                                    <td rowspan="2" class="total_epop_svac_a2{{ $row->id }}">{{ $total_epop_svac_a2 }}</td> <!-- A2 EPOP SINOVAC -->
                                                    <td rowspan="2" class="total_epop_svac_a3{{ $row->id }}">{{ $total_epop_svac_a3 }}</td>  <!-- A3 EPOP SINOVAC -->
                                                    <td rowspan="2" class="total_epop_svac_a4{{ $row->id }}">{{$total_epop_svac_a4}}</td> <!-- A4 EPOP SINOVAC -->
                                                    <td rowspan="2" class="total_epop_svac{{ $row->id }}">{{ $total_epop_svac }}</td> <!-- E_POP_SINOVAC FIRST  -->
                                                    <td rowspan="2" class="total_vallocated_svac_frst{{ $row->id }}">{{ $total_vallocated_svac_frst }}</td> <!-- VACCINE ALLOCATED_SINOVAC (FD)  -->
                                                    <td rowspan="2" class="total_vallocated_svac_scnd{{ $row->id }}">{{ $total_vallocated_svac_scnd }}</td> <!-- VACCINE ALLOCATED_SINOVAC (SD)  -->
                                                    <td rowspan="2" class="total_vallocated_svac{{ $row->id }}">{{ $total_vallocated_svac }}</td>  <!-- TOTAL VACCINE ALLOCATED_SINOVAC   -->
                                                    <td>
                                                        <span class="label label-success total_svac_a1_frst{{ $row->id }}">{{ $total_svac_a1_frst }}</span> <!-- A1_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_svac_a2_frst{{ $row->id }}">{{ $total_svac_a2_frst }}</span> <!-- A2_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_svac_a3_frst{{ $row->id }}">{{ $total_svac_a3_frst }}</span> <!-- A3_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_svac_a4_frst{{ $row->id }}">{{ $total_svac_a4_frst }}</span> <!-- A4_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_vcted_svac_frst{{ $row->id }}">{{  $total_vcted_svac_frst }}</span><!-- TOTAL VACCINATED_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_mild_svac_frst{{ $row->id }}" >{{ $total_mild_svac_frst }}</span> <!-- MILD_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_srs_svac_frst{{ $row->id }}">{{ $total_srs_svac_frst }}</span>  <!-- SERIOUS_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_dfrd_svac_frst{{ $row->id }}">{{ $total_dfrd_svac_frst }}</span>  <!-- DEFERRED_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_rfsd_svac_frst{{ $row->id }}">{{ $total_rfsd_svac_frst }}</span>  <!-- REFUSED_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_wstge_svac_frst{{ $row->id }}">{{ $total_wstge_svac_frst }}</span>  <!-- WASTAGF_SINOVAC -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success p_cvrge_svac_frst{{ $row->id }}">{{ number_format($p_cvrge_svac_frst,2) }}%</span>  <!-- PERCENT_COVERAGE_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_c_rate_svac_frst{{ $row->id }}">{{ number_format($total_c_rate_svac_frst,2) }}%</span>  <!-- CONSUMPTION RATE_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_r_unvcted_frst_svac{{ $row->id }}">{{ $total_r_unvcted_frst_svac }}</span>  <!-- REMAINING UNVACCINATED_SINOVAC -->
                                                    </td>
                                                </tr>
                                                <tr style="background-color: #ffd8d6">
                                                    <td>
                                                        <span class="label label-warning total_svac_a1_scnd{{ $row->id }}">{{ $total_svac_a1_scnd }}</span>   <!-- A1_SINOVAC2 -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_svac_a2_scnd{{ $row->id }}">{{ $total_svac_a2_scnd }}</span> <!-- A2_SINOVA 2 -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_svac_a3_scnd{{ $row->id }}">{{ $total_svac_a3_scnd }}</span> <!-- A3_SINOVAC 2 -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_svac_a4_scnd{{ $row->id }}">{{ $total_svac_a4_scnd }}</span> <!-- A3_SINOVAC 2 -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_vcted_svac_scnd{{ $row->id }}">{{ $total_vcted_svac_scnd }}</span> <!-- TOTAL_VACCINATED_SINOVAC 2-->
                                                    </td> <!-- 1-4 -->
                                                    <td>
                                                        <span class="label label-warning total_mild_svac_scnd{{ $row->id }}">{{ $total_mild_svac_scnd }}</span> <!-- MILD_SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_srs_svac_scnd{{ $row->id }}">{{ $total_srs_svac_scnd }}</span> <!-- SERIOUS_SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_dfrd_svac_scnd{{ $row->id }}">{{ $total_dfrd_svac_scnd }}</span> <!-- DEFERRED_SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_rfsd_svac_scnd{{ $row->id }}">{{ $total_rfsd_svac_scnd }}</span> <!-- REFUSED_SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_wstge_svac_scnd{{ $row->id }}">{{ $total_wstge_svac_scnd }}</span> <!--WASTAGE_SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning p_cvrge_svac_scnd{{ $row->id }}">{{ number_format($p_cvrge_svac_scnd,2) }}%</span> <!-- PERCENT_COVERAGE_SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_c_rate_svac_scnd{{ $row->id }}">{{ number_format($total_c_rate_svac_scnd,2) }}%</span> <!-- CONSUMPTION RATE SINOVAC 2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_r_unvcted_scnd_svac{{ $row->id }}">{{ $total_r_unvcted_scnd_svac }} </span> <!-- REMAINING UNVACCINATED_SINOVAC 2 -->
                                                    </td>
                                                </tr>
                                                </tbody>
                                                <tbody><tr>

                                                </tr>
                                                </tbody><tbody id="collapse_astra{{ $row->id }}" class="collapse bg-primary" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                                <tr style="background-color: #f2fcac">
                                                    <td rowspan="2">

                                                    </td> <!-- 1-5 -->
                                                    <td rowspan="2" style="color:black;" class="total_epop_astra_a1{{ $row->id }}">{{ $total_epop_astra_a1 }}</td> <!-- A1 EPOP ASTRA -->
                                                    <td rowspan="2" style="color:black;" class="total_epop_astra_a2{{ $row->id }}">{{ $total_epop_astra_a2 }}</td>  <!-- A2 EPOP ASTRA -->
                                                    <td rowspan="2" style="color:black;" class="total_epop_astra_a3{{ $row->id }}">{{ $total_epop_astra_a3 }}</td> <!-- A3 EPOP ASTRA -->
                                                    <td rowspan="2" style="color:black;" class="total_epop_astra_a4{{ $row->id }}">{{ $total_epop_astra_a4 }}</td> <!-- A4 EPOP ASTRA -->
                                                    <td rowspan="2" style="color:black;" class="total_epop_astra{{ $row->id }}">{{ $total_epop_astra }} </td>  <!-- TOTAL_E_POP_ASTRA -->
                                                    <td rowspan="2" style="color:black;" class="total_vallocated_astra_frst{{ $row->id }}">{{ $total_vallocated_astra_frst }}</td>  <!-- VACCINE ALLOCATED_ASTRA (FD) -->
                                                    <td rowspan="2" style="color:black;" class="total_vallocated_astra_scnd{{ $row->id }}">{{ $total_vallocated_astra_scnd }}</td>  <!-- VACCINE ALLOCATED_ASTRA (SD) -->
                                                    <td rowspan="2" style="color:black;" class="total_vallocated_astra{{ $row->id }}">{{ $total_vallocated_astra }}</td>  <!-- TOTAL VACCINE ALLOCATED_ASTRA -->
                                                    <td style="color:black;">
                                                        <span class="label label-success total_astra_a1_frst{{ $row->id }}">{{ $total_astra_a1_frst }}</span>  <!-- A1_ASTRA  -->
                                                    </td>
                                                    <td style="color:black">
                                                        <span class="label label-success total_astra_a2_frst{{ $row->id }}">{{ $total_astra_a2_frst }}</span>  <!-- A2_ASTRA  -->
                                                    </td>
                                                    <td style="color:black">
                                                        <span class="label label-success total_astra_a3_frst{{ $row->id }}">{{ $total_astra_a3_frst }}</span>  <!-- A3_ASTRA  -->
                                                    </td>
                                                    <td style="color:black">
                                                        <span class="label label-success total_astra_a4_frst{{ $row->id }}">{{ $total_astra_a4_frst }}</span>  <!-- A4_ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_vcted_astra_frst{{ $row->id }}">{{ $total_vcted_astra_frst }}</span>  <!-- TOTAL VACCINATED_ASTRA-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_mild_astra_frst{{ $row->id }}">{{ $total_mild_astra_frst }}</span> <!-- MILD_ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_srs_astra_frst{{ $row->id }}">{{ $total_srs_astra_frst }}</span>  <!-- SERIOUS_ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_dfrd_astra_frst{{ $row->id }}">{{ $total_dfrd_astra_frst }}</span> <!-- DEFERRED_ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_rfsd_astra_frst{{ $row->id }}">{{ $total_rfsd_astra_frst }}</span> <!-- REFUSED_ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_wstge_astra_frst{{ $row->id }}">{{ $total_wstge_astra_frst }}</span> <!-- WASTAGE_ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success p_cvrge_astra_frst{{ $row->id }}">{{ number_format($p_cvrge_astra_frst,2) }}%</span> <!-- PERCENT_COVERAGE_ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_c_rate_astra_frst{{ $row->id }}">{{ number_format($total_c_rate_astra_frst,2) }}%</span> <!-- CONSUMPTION RATE ASTRA  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success total_r_unvcted_frst_astra{{ $row->id }}">{{ $total_r_unvcted_frst_astra }}</span> <!-- REMAINUNG UNVACCINATED_ASTRA  -->
                                                    </td>
                                                </tr>
                                                <tr style="background-color: #f2fcac">
                                                    <td style="color: black;">
                                                        <span class="label label-warning total_astra_a1_scnd{{ $row->id }}">{{ $total_astra_a1_scnd }}</span>  <!-- A1_ASTRA2  -->
                                                    </td>
                                                    <td style="color:black;">
                                                        <span class="label label-warning total_astra_a2_scnd{{ $row->id }}">{{ $total_astra_a2_scnd }}</span>  <!-- A2_ASTRA2  -->
                                                    </td>
                                                    <td style="color:black">
                                                        <span class="label label-warning total_astra_a3_scnd{{ $row->id }}">{{ $total_astra_a3_scnd }}</span>  <!-- A3_ASTRA2  -->
                                                    </td>
                                                    <td style="color:black;">
                                                        <span class="label label-warning total_astra_a4_scnd{{ $row->id }}">{{ $total_astra_a4_scnd }}</span>  <!-- A4_ASTRA2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_vcted_astra_scnd{{ $row->id }}">{{ $total_vcted_astra_scnd }}</span> <!-- TOTAL VACCINATED_ASTRA 2-->
                                                    </td> <!-- 1-6 -->
                                                    <td>
                                                        <span class="label label-warning total_mild_astra_scnd{{ $row->id }}">{{ $total_mild_astra_scnd }}</span> <!-- MILD_ASTRA 2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_srs_astra_scnd{{ $row->id }}">{{ $total_srs_astra_scnd }}</span> <!-- SERIOUS_ASTRA2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_dfrd_astra_scnd{{ $row->id }}">{{ $total_dfrd_astra_scnd }}</span> <!-- DEFERRED_ASTRA 2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_rfsd_astra_scnd{{ $row->id }}">{{ $total_rfsd_astra_scnd }}</span> <!-- REFUSED_ASTRA 2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_wstge_astra_scnd{{ $row->id }}">{{ $total_wstge_astra_scnd }}</span> <!-- WASTAGE_ASTRA2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning p_cvrge_astra_scnd{{ $row->id }}">{{ number_format($p_cvrge_astra_scnd,2)}}%</span> <!-- PERCENT_COVERAGE_ASTRA 2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_c_rate_astra_scnd{{ $row->id }}">{{ number_format($total_c_rate_astra_scnd,2) }}%</span> <!-- CONSUMPTION_RATE_ASTRA 2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning total_r_unvcted_scnd_astra{{ $row->id }}">{{ $total_r_unvcted_scnd_astra }}</span> <!-- REMAINUNG_UNVACCIANTED_ASTRA 2  -->
                                                    </td>
                                                </tr>
                                                </tbody>
                                                <tbody><tr>
                                                    <td>Total</td> <!-- 1-7 -->
                                                    <td class=""></td> <!-- TOTAL_A1  -->
                                                    <td class=""></td> <!-- TOTAL_A2 -->
                                                    <td class=""></td> <!-- TOTAL_A3 -->
                                                    <td class=""></td> <!-- TOTAL_A4 -->
                                                    <td class=""></td> <!-- TOTAL_E_POP -->
                                                    <td>
                                                        <b class="total_vallocated_frst{{ $row->id }}">{{ $total_vallocated_frst }}</b> <!-- TOTAL_VACCINE_ALLOCATED_FIRST  -->
                                                    </td>
                                                    <td>
                                                        <b class="total_vallocated_scnd{{ $row->id }}">{{ $total_vallocated_scnd }} </b> <!-- TOTAL_VACCINE_ALLOCATED_SECOND  -->
                                                    </td>
                                                    <td>
                                                        <b class="total_vallocated{{ $row->id }}">{{$total_vallocated }}</b> <!-- TOTAL_VACCINE_ALLOCATED  -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success total_overall_a1_first{{ $row->id }}" style="margin-right: 5%">{{ $total_svac_a1_frst + $total_astra_a1_frst }}</b> <!-- TOTAL_A1  -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success total_overall_a2_first{{ $row->id }}" style="margin-right: 5%">{{ $total_svac_a2_frst + $total_astra_a2_frst }}</b> <!-- TOTAL_A2  -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success total_overall_a3_first{{ $row->id }}" style="margin-right: 5%">{{ $total_svac_a3_frst + $total_astra_a3_frst }}</b> <!-- TOTAL_A3  -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success total_overall_a4_first{{ $row->id }}" style="margin-right: 5%">{{ $total_svac_a4_frst + $total_astra_a4_frst }}</b> <!-- TOTAL_A4  -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success total_vcted_frst{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_frst }}</b> <!-- TOTAL_VACCINATED_FIRST -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success total_overall_mild_first{{ $row->id }}" style="margin-right: 5%">{{ $total_mild_svac_first + $total_mild_astra_frst }}</b> <!-- TOTAL_MILD -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success total_overall_serious_first{{ $row->id }}" style="margin-right: 5%">{{ $total_srs_svac_frst + $total_srs_astra_frst }}</b>  <!-- TOTAL_SERIOUS -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success total_overall_deferred_first{{ $row->id }}" style="margin-right: 5%">{{ $total_dfrd_svac_frst + $total_dfrd_astra_frst }}</b>  <!-- TOTAL_DEFERRED -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success total_rfsd_frst{{ $row->id }}" style="margin-right: 5%">{{ $total_rfsd_frst }}</b>  <!-- TOTAL_REFUSED -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success total_overall_wastage_first{{ $row->id }}" style="margin-right: 5%">{{ $total_wstge_svac_frst + $total_wstge_astra_frst }}</b>  <!-- TOTAL_WASTAGE -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success total_p_cvrge_frst{{ $row->id }}" style="margin-right: 5%">{{ number_format($total_p_cvrge_frst,2) }}%</b>  <!-- TOTAL_PERCENT_COVERAGE -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success total_c_rate_frst{{ $row->id }}" style="margin-right: 5%">{{ number_format($total_c_rate_frst,2) }}%</b>  <!-- TOTAL CONSUMPTION_RATE -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success total_r_unvcted_frst{{ $row->id }}" style="margin-right: 5%">{{ $total_r_unvcted_frst }}</b>  <!-- REMAINUNG_UNVACCINATED -->
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
                                                        <b class="label label-warning total_overall_a1_second{{ $row->id }}" style="margin-right: 5%">{{ $total_svac_a1_scnd + $total_astra_a1_scnd }}</b>  <!-- TOTAL_A1 - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning total_overall_a2_second{{ $row->id }}" style="margin-right: 5%">{{ $total_svac_a2_scnd + $total_astra_a2_scnd }} </b>  <!-- TOTAL_A2 - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning total_overall_a3_second{{ $row->id }}" style="margin-right: 5%">{{ $total_svac_a3_scnd + $total_astra_a3_scnd }} </b>  <!-- TOTAL_A3 - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning total_overall_a4_second{{ $row->id }}" style="margin-right: 5%">{{ $total_svac_a4_scnd + $total_astra_a4_scnd }} </b>  <!-- TOTAL_A3 - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning total_vcted_scnd{{ $row->id }}" style="margin-right: 5%">{{ $total_vcted_scnd }}</b>  <!-- TOTAL_VACCINATED_SECOND -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning total_overall_mild_second{{ $row->id }}" style="margin-right: 5%">{{ $total_mild_svac_scnd + $total_mild_astra_scnd  }}</b>  <!-- TOTAL_MILD - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning total_overall_serious_second{{ $row->id }}" style="margin-right: 5%">{{ $total_srs_svac_scnd + $total_srs_astra_scnd }}</b> <!-- TOTAL_SERIOUS - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning total_overall_deferred_second{{ $row->id }}" style="margin-right: 5%">{{ $total_dfrd_svac_scnd + $total_dfrd_astra_scnd }}</b> <!-- TOTAL_DEFERRED - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning total_rfsd_scnd{{ $row->id }}" style="margin-right: 5%">{{ $total_rfsd_scnd }}</b> <!-- TOTAL_REFUSED - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning total_overall_wastage_second{{ $row->id }}" style="margin-right: 5%">{{ $total_wstge_svac_scnd + $total_wstge_astra_scnd }}</b> <!-- TOTAL_WASTAGE - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning total_p_cvrge_scnd{{ $row->id }}" style="margin-right: 5%">{{number_format($total_p_cvrge_scnd,2)}}%</b> <!-- TOTAL_PERCENT_COVERAGE - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning total_c_rate_scnd{{ $row->id }}" style="margin-right: 5%">{{ number_format($total_c_rate_scnd,2)}}%</b> <!-- TOTAL_CONSUMPTION_RATE - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning total_r_unvcted_scnd{{ $row->id }}" style="margin-right: 5%">{{ $total_r_unvcted_scnd }}</b> <!-- REMAINING_UNVACCINATED - 2 -->
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
                        <div id="chartPercentCoverage" style="height: 370px; width: 100%;"></div><br><br><br>
                        <div id="chartConsumptionRate" style="height: 370px; width: 100%;"></div>
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
        $('#date_range').daterangepicker();
        $(".sinovac_dashboard").text({{ Session::get("sinovac_dashboard") }});
        $(".astra_dashboard").text({{ Session::get("astra_dashboard") }});

        <?php $user = Session::get('auth'); ?>
        $("#container").removeClass("container");
        $("#container").addClass("container-fluid");

        function facilityVaccinated(facility_id,date_start,date_end,data){
            $("#vaccine_modal_facility").modal('show');
            $(".vaccinated_content_facility").html(loading);
            $("a").css("background-color","");
            data.css("background-color","yellow");
            json = {
                "_token" : "<?php echo csrf_token()?>",
                "facility_id" : facility_id,
                "date_start": date_start,
                "date_end" : date_end,
                "pagination_table" : "false",
                "total_epop_svac_a1" : $(".total_epop_svac_a1"+facility_id).text(),
                "total_epop_svac_a2" : $(".total_epop_svac_a2"+facility_id).text(),
                "total_epop_svac_a3" : $(".total_epop_svac_a3"+facility_id).text(),
                "total_epop_svac_a4" : $(".total_epop_svac_a4"+facility_id).text(),
                "total_epop_svac" : $(".total_epop_svac"+facility_id).text(),
                "total_vallocated_svac_frst" : $(".total_vallocated_svac_frst"+facility_id).text(),
                "total_vallocated_svac_scnd" : $(".total_vallocated_svac_scnd"+facility_id).text(),
                "total_vallocated_svac" : $(".total_vallocated_svac"+facility_id).text(),
                "total_svac_a1_frst" : $(".total_svac_a1_frst"+facility_id).text(),
                "total_svac_a2_frst" : $(".total_svac_a2_frst"+facility_id).text(),
                "total_svac_a3_frst" : $(".total_svac_a3_frst"+facility_id).text(),
                "total_svac_a4_frst" : $(".total_svac_a4_frst"+facility_id).text(),
                "total_vcted_svac_frst" : $(".total_vcted_svac_frst"+facility_id).text(),
                "total_mild_svac_frst" : $(".total_mild_svac_frst"+facility_id).text(),
                "total_srs_svac_frst" : $(".total_srs_svac_frst"+facility_id).text(),
                "total_dfrd_svac_frst" : $(".total_dfrd_svac_frst"+facility_id).text(),
                "total_rfsd_svac_frst" : $(".total_rfsd_svac_frst"+facility_id).text(),
                "total_wstge_svac_frst" : $(".total_wstge_svac_frst"+facility_id).text(),
                "p_cvrge_svac_frst" : $(".p_cvrge_svac_frst"+facility_id).text(),
                "total_c_rate_svac_frst" : $(".total_c_rate_svac_frst"+facility_id).text(),
                "total_r_unvcted_frst_svac" : $(".total_r_unvcted_frst_svac"+facility_id).text(),

                "total_svac_a1_scnd" : $(".total_svac_a1_scnd"+facility_id).text(),
                "total_svac_a2_scnd" : $(".total_svac_a2_scnd"+facility_id).text(),
                "total_svac_a3_scnd" : $(".total_svac_a3_scnd"+facility_id).text(),
                "total_svac_a4_scnd" : $(".total_svac_a4_scnd"+facility_id).text(),
                "total_vcted_svac_scnd" : $(".total_vcted_svac_scnd"+facility_id).text(),
                "total_mild_svac_scnd" : $(".total_mild_svac_scnd"+facility_id).text(),
                "total_srs_svac_scnd" : $(".total_srs_svac_scnd"+facility_id).text(),
                "total_dfrd_svac_scnd" : $(".total_dfrd_svac_scnd"+facility_id).text(),
                "total_rfsd_svac_scnd" : $(".total_rfsd_svac_scnd"+facility_id).text(),
                "total_wstge_svac_scnd" : $(".total_wstge_svac_scnd"+facility_id).text(),
                "p_cvrge_svac_scnd" : $(".p_cvrge_svac_scnd"+facility_id).text(),
                "total_c_rate_svac_scnd" : $(".total_c_rate_svac_scnd"+facility_id).text(),
                "total_r_unvcted_scnd_svac" : $(".total_r_unvcted_scnd_svac"+facility_id).text(),

                "total_epop_astra_a1" : $(".total_epop_astra_a1"+facility_id).text(),
                "total_epop_astra_a2" : $(".total_epop_astra_a2"+facility_id).text(),
                "total_epop_astra_a3" : $(".total_epop_astra_a3"+facility_id).text(),
                "total_epop_astra_a4" : $(".total_epop_astra_a4"+facility_id).text(),
                "total_epop_astra" : $(".total_epop_astra"+facility_id).text(),

                "total_epop_overall_a1" : $(".total_epop_overall_a1"+facility_id).text(),
                "total_epop_overall_a2" : $(".total_epop_overall_a2"+facility_id).text(),
                "total_epop_overall_a3" : $(".total_epop_overall_a3"+facility_id).text(),
                "total_epop_overall_a4" : $(".total_epop_overall_a4"+facility_id).text(),
                "total_epop_overall" : $(".total_epop_overall"+facility_id).text(),


                "total_vallocated_astra_frst" : $(".total_vallocated_astra_frst"+facility_id).text(),
                "total_vallocated_astra_scnd" : $(".total_vallocated_astra_scnd"+facility_id).text(),
                "total_vallocated_astra" : $(".total_vallocated_astra"+facility_id).text(),
                "total_astra_a1_frst" : $(".total_astra_a1_frst"+facility_id).text(),
                "total_astra_a2_frst" : $(".total_astra_a2_frst"+facility_id).text(),
                "total_astra_a3_frst" : $(".total_astra_a3_frst"+facility_id).text(),
                "total_astra_a4_frst" : $(".total_astra_a4_frst"+facility_id).text(),
                "total_vcted_astra_frst" : $(".total_vcted_astra_frst"+facility_id).text(),
                "total_mild_astra_frst" : $(".total_mild_astra_frst"+facility_id).text(),
                "total_srs_astra_frst" : $(".total_srs_astra_frst"+facility_id).text(),
                "total_dfrd_astra_frst" : $(".total_dfrd_astra_frst"+facility_id).text(),
                "total_rfsd_astra_frst" : $(".total_rfsd_astra_frst"+facility_id).text(),
                "total_wstge_astra_frst" : $(".total_wstge_astra_frst"+facility_id).text(),
                "p_cvrge_astra_frst" : $(".p_cvrge_astra_frst"+facility_id).text(),
                "total_c_rate_astra_frst" : $(".total_c_rate_astra_frst"+facility_id).text(),
                "total_r_unvcted_frst_astra" : $(".total_r_unvcted_frst_astra"+facility_id).text(),

                "total_astra_a1_scnd" : $(".total_astra_a1_scnd"+facility_id).text(),
                "total_astra_a2_scnd" : $(".total_astra_a2_scnd"+facility_id).text(),
                "total_astra_a3_scnd" : $(".total_astra_a3_scnd"+facility_id).text(),
                "total_astra_a4_scnd" : $(".total_astra_a4_scnd"+facility_id).text(),
                "total_vcted_astra_scnd" : $(".total_vcted_astra_scnd"+facility_id).text(),
                "total_mild_astra_scnd" : $(".total_mild_astra_scnd"+facility_id).text(),
                "total_srs_astra_scnd" : $(".total_srs_astra_scnd"+facility_id).text(),
                "total_dfrd_astra_scnd" : $(".total_dfrd_astra_scnd"+facility_id).text(),
                "total_rfsd_astra_scnd" : $(".total_rfsd_astra_scnd"+facility_id).text(),
                "total_wstge_astra_scnd" : $(".total_wstge_astra_scnd"+facility_id).text(),
                "p_cvrge_astra_scnd" : $(".p_cvrge_astra_scnd"+facility_id).text(),
                "total_c_rate_astra_scnd" : $(".total_c_rate_astra_scnd"+facility_id).text(),
                "total_r_unvcted_scnd_astra" : $(".total_r_unvcted_scnd_astra"+facility_id).text(),

                "total_vallocated_frst" : $(".total_vallocated_frst"+facility_id).text(),
                "total_vallocated_scnd" : $(".total_vallocated_scnd"+facility_id).text(),
                "total_vallocated" : $(".total_vallocated"+facility_id).text(),
                "total_overall_a1_first" : $(".total_overall_a1_first"+facility_id).text(),
                "total_overall_a2_first" : $(".total_overall_a2_first"+facility_id).text(),
                "total_overall_a3_first" : $(".total_overall_a3_first"+facility_id).text(),
                "total_overall_a4_first" : $(".total_overall_a4_first"+facility_id).text(),
                "total_vcted_frst" : $(".total_vcted_frst"+facility_id).text(),
                "total_overall_mild_first" : $(".total_overall_mild_first"+facility_id).text(),
                "total_overall_serious_first" : $(".total_overall_serious_first"+facility_id).text(),
                "total_overall_deferred_first" : $(".total_overall_deferred_first"+facility_id).text(),
                "total_rfsd_frst" : $(".total_rfsd_frst"+facility_id).text(),
                "total_overall_wastage_first" : $(".total_overall_wastage_first"+facility_id).text(),
                "total_p_cvrge_frst" : $(".total_p_cvrge_frst"+facility_id).text(),
                "total_c_rate_frst" : $(".total_c_rate_frst"+facility_id).text(),
                "total_r_unvcted_frst" : $(".total_r_unvcted_frst"+facility_id).text(),

                "total_overall_a1_second" : $(".total_overall_a1_second"+facility_id).text(),
                "total_overall_a2_second" : $(".total_overall_a2_second"+facility_id).text(),
                "total_overall_a3_second" : $(".total_overall_a3_second"+facility_id).text(),
                "total_overall_a4_second" : $(".total_overall_a4_second"+facility_id).text(),
                "total_vcted_scnd" : $(".total_vcted_scnd"+facility_id).text(),
                "total_overall_mild_second" : $(".total_overall_mild_second"+facility_id).text(),
                "total_overall_serious_second" : $(".total_overall_serious_second"+facility_id).text(),
                "total_overall_deferred_second" : $(".total_overall_deferred_second"+facility_id).text(),
                "total_rfsd_scnd" : $(".total_rfsd_scnd"+facility_id).text(),
                "total_overall_wastage_second" : $(".total_overall_wastage_second"+facility_id).text(),
                "total_p_cvrge_scnd" : $(".total_p_cvrge_scnd"+facility_id).text(),
                "total_c_rate_scnd" : $(".total_c_rate_scnd"+facility_id).text(),
                "total_r_unvcted_scnd" : $(".total_r_unvcted_scnd"+facility_id).text(),















            };
            var url = "<?php echo asset('vaccine/facility_content'); ?>";
            $.post(url,json,function(data){
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
                            { label: "Sinovac",  y: sinovac_dashboard, color: "#dd4b39" },
                            { label: "AstraZeneca", y: astra_dashboard, color: "#f39c12" },
                            { label: "Pfizer", y: 0  },
                            { label: "Sputnik V",  y: 0  },
                            { label: "Moderna",  y: 0  }
                        ]

                    }
                ]

            };

            $("#chartContainer1").CanvasJSChart(options1);

            var a1_dashboard = <?php if(Session::get('a1_dashboard')) echo Session::get('a1_dashboard'); else echo 0; ?>;
            var a2_dashboard = <?php if(Session::get('a2_dashboard')) echo Session::get('a2_dashboard'); else echo 0; ?>;
            var a3_dashboard = <?php if(Session::get('a3_dashboard')) echo Session::get('a3_dashboard'); else echo 0; ?>;
            var a4_dashboard = <?php if(Session::get('a4_dashboard')) echo Session::get('a4_dashboard'); else echo 0; ?>;

            var options2 = {
                title: {
                    text: "Priority"
                },
                data: [
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "column",
                        dataPoints: [
                            { label: "(A1)",  y: a1_dashboard},
                            { label: "(A2)", y: a2_dashboard },
                            { label: "(A3)", y: a3_dashboard },
                            { label: "(A4)",  y: a4_dashboard },
                            { label: "(A5)",  y: 0  },
                            { label: "(A5)",  y: 0  }
                        ]
                    }
                ]
            };
            $("#chartContainer2").CanvasJSChart(options2);


            var percent_coverage_dashboard_first = <?php if(Session::get('percent_coverage_dashboard_first')) echo Session::get('percent_coverage_dashboard_first'); else echo 0; ?>;
            var percent_coverage_dashboard_second = <?php if(Session::get('percent_coverage_dashboard_second')) echo Session::get('percent_coverage_dashboard_second'); else echo 0; ?>;
            var options3 = {
                title: {
                    text: "Percentage Coverage",
                    fontSize: 23,
                },
                data: [{
                    type: "doughnut",
                    startAngle: 45,
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabel: "{label} ({y}%)",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: [
                        { label: "First Dose", y: percent_coverage_dashboard_first, color:"#00a65a" },
                        { label: "Second Dose", y: percent_coverage_dashboard_second, color:"#f39c12" },

                    ]
                }]
            };
            $("#chartPercentCoverage").CanvasJSChart(options3);

            var $consumption_rate_dashboard_first = <?php if(Session::get('consumption_rate_dashboard_first')) echo Session::get('consumption_rate_dashboard_first'); else echo 0; ?>;
            var $consumption_rate_dashboard_second = <?php if(Session::get('consumption_rate_dashboard_second')) echo Session::get('consumption_rate_dashboard_second'); else echo 0; ?>;
            var options4 = {
                title: {
                    text: "Consumption Rate",
                    fontSize: 23,
                },
                data: [{
                    type: "doughnut",
                    startAngle: 45,
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabel: "{label} ({y}%)",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: [
                        { label: "First Dose", y: $consumption_rate_dashboard_first, color:"#00a65a" },
                        { label: "Second Dose", y: $consumption_rate_dashboard_second, color:"#f39c12" },

                    ]
                }]
            };
            $("#chartConsumptionRate").CanvasJSChart(options4);
        };

    </script>

@endsection