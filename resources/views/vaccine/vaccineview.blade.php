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


                                     //ELIGIBLE POPULATION SINOVAC
                                     $total_epop_svac_frtline = $row->frontline_health_workers;
                                     $total_epop_svac_sr = $row->senior_citizens;
                                     $total_epop_svac = $total_epop_svac_frtline + $total_epop_svac_sr;
                                 //$total_epop_svac_frtline = $row->frontline_health_workers;
                                 //$total_eli_pop_sinovac_senior  = $row->senior_citizens;
                                 //$total_eli_pop_sinovac = $total_eli_pop_sinovac_frontline + $total_eli_pop_sinovac_senior;

                                     //ELIGIBLE POPULATION ASTRACENECA
                                     $total_epop_astra_frtline = $row->frontline_health_workers;
                                     $total_epop_astra_sr = $row->senior_citizens;
                                     $total_epop_astra = $total_epop_astra_frtline + $total_epop_astra_sr;
                                 //$total_eli_pop_astra_frontline = $row->frontline_health_workers;
                                 //$total_eli_pop_astra_senior = $row->senior_citizens;
                                 //$total_eli_pop_astra = $total_eli_pop_astra_frontline + $total_eli_pop_astra_senior;


                                     //VACCINE_ALLOCATED
                                     $total_vallocated_svac_frst = $row->sinovac_allocated_first;
                                     $total_vallocated_svac_scnd = $row->sinovac_allocated_second;
                                     $total_vallocated_astra_frst = $row->astrazeneca_allocated_first;
                                     $total_vallocated_astra_scnd = $row->astrazeneca_allocated_second;
                                 //$total_vaccine_allocated_sinovac_first = $row->sinovac_allocated_first;
                                 //$total_vaccine_allocated_sinovac_second = $row->sinovac_allocated_second;
                                 //$total_vaccine_allocated_astra_first = $row->astrazeneca_allocated_first;
                                 //$total_vaccine_allocated_astra_second = $row->astrazeneca_allocated_second;

                                    //SINOVAC
                                    $total_svac_a1_frst = 0;
                                    $total_svac_a2_frst = 0;
                                    $total_svac_a1_scnd = 0;
                                    $total_svac_a2_scnd = 0;
                                //$total_sinovac_a1_first = 0;
                                //$total_sinovac_a2_first = 0;
                                //$total_sinovac_a1_second = 0;
                                //$total_sinovac_a2_second = 0;

                                    $total_svac_a1_frst = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','frontline_health_workers')")[0]->vaccinated_first_a;
                                    $total_svac_a2_frst = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','indigent_senior_citizens')")[0]->vaccinated_first_a;
                                    $total_svac_a1_scnd = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','frontline_health_workers')")[0]->vaccinated_second_a;
                                    $total_svac_a2_scnd = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','indigent_senior_citizens')")[0]->vaccinated_second_a;
                                //$total_sinovac_a1_first = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','frontline_health_workers')")[0]->vaccinated_first_a;
                                //$total_sinovac_a2_first = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','indigent_senior_citizens')")[0]->vaccinated_first_a;
                                //$total_sinovac_a1_second = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','frontline_health_workers')")[0]->vaccinated_second_a;
                                //$total_sinovac_a2_second = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','indigent_senior_citizens')")[0]->vaccinated_second_a;

                                    //ASTRACENECA
                                    $total_astra_a1_frst = 0;
                                    $total_astra_a2_frst = 0;
                                    $total_astra_a1_scnd = 0;
                                    $total_astra_a2_scnd = 0;
                                //$total_astra_a1_first = 0;
                                //$total_astra_a2_first = 0;
                                //$total_astra_a1_second = 0;
                                //$total_astra_a2_second = 0;

                                    $total_astra_a1_frst = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','frontline_health_workers')")[0]->vaccinated_first_a;
                                    $total_astra_a2_frst = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_first_a;
                                    $total_astra_a1_scnd = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','frontline_health_workers')")[0]->vaccinated_second_a;
                                    $total_astra_a2_scnd = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_second_a;
                                //$total_astra_a1_first = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','frontline_health_workers')")[0]->vaccinated_first_a;
                                //$total_astra_a2_first = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_first_a;
                                //$total_astra_a1_second = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','frontline_health_workers')")[0]->vaccinated_second_a;
                                //$total_astra_a2_second = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_second_a;

                                    //TOTAL_VACCINATED_FIRST
                                    $total_vcted_svac_frst = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->vaccinated_first;
                                    $total_vcted_astra_frst = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->vaccinated_first;
                                //$total_vaccinated_sinovac_first = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->vaccinated_first;
                                //$total_vaccinated_astra_first = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->vaccinated_first;

                                    //TOTAL_VACCINATED_SECOND
                                    $total_vcted_svac_scnd = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->vaccinated_second;
                                    $total_vcted_astra_scnd = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->vaccinated_second;
                                //$total_vaccinated_sinovac_second = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->vaccinated_second;
                                //$total_vaccinated_astra_second = \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->vaccinated_second;

                                    //TOTAL_MILD_FIRST
                                    $total_mild_svac_frst =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->mild_first;
                                    $total_mild_astra_frst =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->mild_first;
                                //$total_mild_sinovac_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->mild_first;
                                //$total_mild_astra_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->mild_first;

                                    //TOTAL_MILD_SECOND
                                    $total_mild_svac_scnd =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->mild_second;
                                    $total_mild_astra_scnd =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->mild_second;
                                //$total_mild_sinovac_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->mild_second;
                                //$total_mild_astra_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->mild_second;

                                    //TOTAL_SERIOUS_FIRST
                                    $total_srs_svac_frst =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->serious_first;
                                    $total_srs_astra_frst =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->serious_first;
                                //$total_serious_sinovac_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->serious_first;
                                //$total_serious_astra_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->serious_first;

                                    //TOTAL_SERIOUS_SECOND
                                    $total_srs_svac_scnd =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->serious_second;
                                    $total_srs_astra_scnd =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->serious_second;
                                //$total_serious_sinovac_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->serious_second;
                                //$total_serious_astra_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->serious_second;

                                    //TOTAL_DEFERRED_FIRST
                                    $total_dfrd_svac_frst =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->deferred_first;
                                    $total_dfrd_astra_frst =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->deferred_first;
                                //$total_deferred_sinovac_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->deferred_first;
                                //$total_deferred_astra_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->deferred_first;

                                    //TOTAL_DEFERRED_SECOND
                                    $total_dfrd_svac_scnd =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->deferred_second;
                                    $total_dfrd_astra_scnd =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->deferred_second;
                                //$total_deferred_sinovac_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->deferred_second;
                                //$total_deferred_astra_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->deferred_second;


                                    //TOTAL_REFUSED_FIRST
                                    $total_rfsd_svac_frst =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->refused_first;
                                    $total_rfsd_astra_frst =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->refused_first;
                                //$total_refused_sinovac_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->refused_first;
                                //$total_refused_astra_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->refused_first;

                                    //TOTAL_REFUSED_SECOND
                                    $total_rfsd_svac_scnd =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->refused_second;
                                    $total_rfsd_astra_scnd =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->refused_second;
                                //$total_refused_sinovac_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->refused_second;
                                //$total_refused_astra_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->refused_second;

                                    //TOTAL_WASTAGE_FIRST
                                    $total_wstge_svac_frst =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->wastage_first;
                                    $total_wstge_astra_frst =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->wastage_first;
                                //$total_wastage_sinovac_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->wastage_first;
                                //$total_wastage_astra_first =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->wastage_first;

                                    //TOTAL_WASTAGE_SECOND
                                    $total_wstge_svac_scnd =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->wastage_second;
                                    $total_wstge_astra_scnd =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->wastage_second;
                                //$total_wastage_sinovac_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Sinovac','')")[0]->wastage_second;
                                //$total_wastage_astra_second =  \DB::connection('mysql')->select("call vaccine_data('$vaccine->muncity_id','Astrazeneca','')")[0]->wastage_second;

                                    //$total_eli_pop = $total_eli_pop_sinovac + $total_eli_pop_astra;
                                    //$total_percent_coverage_first = ($total_vaccinated_first /$total_eli_pop ) * 100;
                                    //$total_percent_coverage_second = ($total_vaccinated_second / $total_eli_pop) * 100;


                                    //TOTAL_VACCINATED
                                    $total_vcted_frst = $total_vcted_svac_frst + $total_vcted_astra_frst;
                                    $total_vcted_scnd = $total_vcted_svac_scnd + $total_vcted_astra_scnd;
                                //$total_vaccinated_first = $total_vaccinated_sinovac_first + $total_vaccinated_astra_first;
                                //$total_vaccinated_second = $total_vaccinated_sinovac_second + $total_vaccinated_astra_second;


                                    //TOTAL_VACCINE_ALLOCATED
                                    $total_vallocated_svac = $total_vallocated_svac_frst + $total_vallocated_svac_scnd;
                                    $total_vallocated_astra = $total_vallocated_astra_frst + $total_vallocated_astra_scnd;
                                    $total_vallocated  = $total_vallocated_svac + $total_vallocated_astra;

                                    $total_vallocated_frst =  $total_vallocated_svac_frst + $total_vallocated_astra_frst;
                                    $total_vallocated_scnd = $total_vallocated_svac_scnd + $total_vallocated_astra_scnd;
                                //$total_vaccine_allocated_sinovac = $total_vaccine_allocated_sinovac_first + $total_vaccine_allocated_sinovac_second;
                                //$total_vaccine_allocated_astra = $total_vaccine_allocated_astra_first + $total_vaccine_allocated_astra_second;


                                    //TOTAL_REFUSED_FIRST
                                    $total_rfsd_frst = $total_rfsd_svac_frst + $total_rfsd_astra_frst;
                                //$total_refused_first = $total_refused_sinovac_first + $total_refused_astra_first;

                                    //TOTAL_REFUSED_SECOND
                                    $total_rfsd_scnd = $total_rfsd_svac_scnd + $total_rfsd_astra_scnd;
                                //$total_refused_second = $total_refused_sinovac_second + $total_refused_astra_second;


                                    //PERCENT_COVERAGE_FIRST
                                    $p_cvrge_svac_frst = $total_vcted_svac_frst / $total_epop_svac * 100;
                                    $p_cvrge_astra_frst = $total_vcted_astra_frst / $total_epop_astra * 100;
                                //$percent_coverage_sinovac_first = number_format($total_vaccinated_sinovac_first / $total_eli_pop_sinovac * 100,2);
                                //$percent_coverage_astra_first = number_format($total_vaccinated_astra_first / $total_eli_pop_astra * 100,2);

                                    //PERCENT_COVERAGE_SECOND
                                    $p_cvrge_svac_scnd = $total_vcted_svac_scnd / $total_epop_svac * 100;
                                    $p_cvrge_astra_scnd = $total_vcted_astra_scnd / $total_epop_astra * 100;
                                //$percent_coverage_sinovac_second = number_format($total_vaccinated_sinovac_second / $total_eli_pop_sinovac * 100,2);
                                //$percent_coverage_astra_second = number_format($total_vaccinated_astra_second / $total_eli_pop_astra * 100,2);

                                        //TOTAL_PERCENT_COVERAGE
                                    $total_p_cvrge_frst = $total_vcted_frst / $total_vallocated_frst * 100;
                                    $total_p_cvrge_scnd = $total_vcted_scnd / $total_vallocated_scnd * 100;


                                    //CONSUMPTION RATE FIRST
                                    $total_c_rate_svac_frst = $total_vcted_svac_frst / $total_vallocated_svac_frst * 100;
                                    $total_c_rate_astra_frst = $total_vcted_astra_frst / $total_vallocated_astra_frst * 100;
                                //$total_consumption_rate_sinovac_first = $total_vaccinated_sinovac_first / $total_vaccine_allocated_sinovac_first * 100;
                                //$total_consumption_rate_astra_first = $total_vaccinated_astra_first / $total_vaccine_allocated_astra_first * 100;

                                    //CONSUMPTION RATE SECOND
                                    $total_c_rate_svac_scnd = $total_vcted_svac_scnd / $total_vallocated_svac_scnd * 100;
                                    $total_c_rate_astra_scnd = $total_vcted_astra_scnd / $total_vallocated_astra_scnd * 100;
                                //$total_consumption_rate_sinovac_second = $total_vaccinated_sinovac_second / $total_vaccine_allocated_sinovac_second * 100;
                                //$total_consumption_rate_astra_second = $total_vaccinated_astra_second / $total_vaccine_allocated_astra_second * 100;

                                     //TOTAL_CONSUMPTION RATE
                                    $total_c_rate_frst = $total_vcted_frst / $total_vallocated_svac * 100;
                                    $total_c_rate_scnd = $total_vcted_scnd / $total_vallocated_astra * 100;
                                //$total_consumption_rate_first = $total_vaccinated_first / $total_allocated_first * 100;
                                //$total_consumption_rate_second = $total_vaccinated_second / $total_allocated_second * 100;



                                    //REMAINING_UNVACCINATED_FIRST
                                    $total_r_unvcted_frst_svac = $total_epop_svac_frtline - $total_vcted_svac_frst - $total_rfsd_svac_frst;
                                    $total_r_unvcted_frst_astra = $total_epop_astra_frtline - $total_vcted_astra_frst - $total_rfsd_astra_frst;
                                //$total_remaining_unvaccinated_first_sinovac = $total_eli_pop_sinovac - $total_vaccinated_sinovac_first - $total_refused_sinovac_first;
                                //$total_remaining_unvaccinated_first_astra = $total_eli_pop_astra - $total_vaccinated_astra_first - $total_refused_astra_first;

                                    //REMAINING_UNVACCINATED_SECOND
                                    $total_r_unvcted_scnd_svac = $total_epop_svac_sr - $total_vcted_svac_scnd - $total_rfsd_svac_scnd;
                                    $total_r_unvcted_scnd_astra = $total_epop_astra_sr - $total_vcted_astra_scnd - $total_rfsd_astra_scnd;
                                //$total_remaining_unvaccinated_second_sinovac = $total_eli_pop_sinovac - $total_vaccinated_sinovac_second - $total_refused_sinovac_second;
                                //$total_remaining_unvaccinated_second_astra = $total_eli_pop_astra - $total_vaccinated_astra_second - $total_refused_astra_second;

                                    //TOTAL_REMAINING_UNVACCINATED
                                    $total_r_unvcted_frst = $total_epop_svac - $total_vcted_frst - $total_rfsd_frst;
                                    $total_r_unvcted_scnd = $total_epop_astra - $total_vcted_scnd - $total_rfsd_scnd;
                                //$total_remaining_unvaccinated_first = $total_eli_pop - $total_vaccinated_first - $total_refused_first;
                                //$total_remaining_unvaccinated_second = $total_eli_pop - $total_vaccinated_second - $total_refused_second;

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
                                                    <td rowspan="2">{{ $total_epop_svac_frtline }}</td> <!-- TOTAL_E_POP_FRONTLINE_SINOVAC   -->
                                                    <td rowspan="2">{{ $total_epop_svac_sr }}</td> <!-- E_POP_SENIOR_SINOVAC-->
                                                    <td rowspan="2">{{ $total_epop_svac }}</td> <!-- E_POP_SINOVAC FIRST  -->
                                                    <td rowspan="2">{{ $total_vallocated_svac_frst }}</td> <!-- VACCINE ALLOCATED_SINOVAC (FD)  -->
                                                    <td rowspan="2">{{ $total_vallocated_svac_scnd }}</td> <!-- VACCINE ALLOCATED_SINOVAC (SD)  -->
                                                    <td rowspan="2">{{ $total_vallocated_svac }}</td>  <!-- TOTAL VACCINE ALLOCATED_SINOVAC   -->
                                                    <td>
                                                        <span class="label label-success">{{ $total_svac_a1_frst }}</span>  <!--  A1_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_svac_a2_frst }}</span> <!-- T A2_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{  $total_vcted_svac_frst }}</span> <!-- TOTAL VACCINATED_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_mild_svac_frst }}</span> <!-- MILD_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_srs_svac_frst }}</span>  <!-- SERIOUS_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_dfrd_svac_frst }}</span>  <!-- DEFERRED_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_rfsd_svac_frst }}</span>  <!-- REFUSED_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_wstge_svac_frst }}</span>  <!-- WASTAGF_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ number_format($p_cvrge_svac_frst,2) }}%</span>  <!-- PERCENT_COVERAGE_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ number_format($total_c_rate_svac_frst,2) }}%</span>  <!-- CONSUMPTION RATE_SINOVAC-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-success">{{ $total_r_unvcted_frst_svac }}</span>  <!-- REMAINING UNVACCINATED_SINOVAC-->
                                                    </td>
                                                </tr>
                                                <tr style="background-color: #ffd8d6">
                                                    <td>
                                                        <span class="label label-warning">{{ $total_svac_a1_scnd }}</span>   <!-- A1_SINOVAC2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_svac_a2_scnd }}</span> <!-- A2_SINOVAC2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_vcted_svac_scnd }}</span> <!-- TOTAL_VACCINATED_SINOVAC2-->
                                                    </td> <!-- 1-4 -->
                                                    <td>
                                                        <span class="label label-warning">{{ $total_mild_svac_scnd }}</span> <!-- MILD_SINOVAC2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_srs_svac_scnd }}</span> <!-- SERIOUS_SINOVAC2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_dfrd_svac_scnd }}</span> <!-- DEFERRED_SINOVAC2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_rfsd_svac_scnd }}</span> <!-- REFUSED_SINOVAC2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_wstge_svac_scnd }}</span> <!--WASTAGE_SINOVAC2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ number_format($p_cvrge_svac_scnd,2) }}%</span> <!-- PERCENT_COVERAGE_SINOVAC2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ number_format($total_c_rate_svac_scnd,2) }}%</span> <!-- CONSUMPTION RATE SINOVAC2-->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_r_unvcted_scnd_svac }} </span> <!-- REMAINING UNVACCINATED_SINOVAC2-->
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
                                                    <td>
                                                        <span class="label label-warning">{{ $total_vcted_astra_scnd }}</span> <!-- TOTAL VACCINATED_ASTRA2-->
                                                    </td> <!-- 1-6 -->
                                                    <td>
                                                        <span class="label label-warning">{{ $total_mild_astra_scnd }}</span> <!-- MILD_ASTRA2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_srs_astra_scnd }}</span> <!-- SERIOUS_ASTRA2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_dfrd_astra_scnd }}</span> <!-- DEFERRED_ASTRA2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_rfsd_astra_scnd }}</span> <!-- REFUSED_ASTRA2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_wstge_astra_scnd }}</span> <!-- WASTAGE_ASTRA2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ number_format($p_cvrge_astra_scnd,2)}}%</span> <!-- PERCENT_COVERAGE_ASTRA2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ number_format($total_c_rate_astra_scnd,2) }}%</span> <!-- CONSUMPTION_RATE_ASTRA2  -->
                                                    </td>
                                                    <td>
                                                        <span class="label label-warning">{{ $total_r_unvcted_scnd_astra }}</span> <!-- REMAINUNG_UNVACCIANTED_ASTRA2  -->
                                                    </td>
                                                </tr>
                                                </tbody>
                                                <tbody><tr>
                                                    <td>Total</td> <!-- 1-7 -->
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>
                                                       <b>{{ $total_vallocated_frst }}</b> <!-- TOTAL_VACCINE_ALLOCATED_FIRST  -->
                                                    </td>
                                                    <td>
                                                        <b>{{ $total_vallocated_scnd }} </b> <!-- TOTAL_VACCINE_ALLOCATED_SECOND  -->
                                                    </td>
                                                    <td><b>{{$total_vallocated }}</b> <!-- TOTAL_VACCINE_ALLOCATED  -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_svac_a1_frst + $total_astra_a1_frst }}</b> <!-- TOTAL_A1  -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-success" style="margin-right: 5%">{{ $total_svac_a2_frst + $total_astra_a2_frst }}</b> <!-- TOTAL_A2  -->
                                                    </td>
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
                                                        <b class="label label-success" style="margin-right: 5%">{{ number_format($total_c_rate_frst,2) }}%</b>  <!-- CONSUMPTION_RATE -->
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
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_svac_a1_scnd + $total_astra_a1_scnd }}</b>  <!-- TOTAL_A1 - 2 -->
                                                    </td>
                                                    <td>
                                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_svac_a2_scnd + $total_astra_a2_scnd }} </b>  <!-- TOTAL_A2 - 2 -->
                                                    </td>
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
                            <hr>
                            <?php
                            //ELIGIBLE POP SINOVAC
                            $total_e_pop_frtline_prov =\DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->frontline_health_workers; //Frontline(A1) SINOVAC_FIRST
                            $total_e_pop_sr_prov  = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->senior_citizens;
                            $total_e_pop_svac_prov = $total_e_pop_frtline_prov + $total_e_pop_sr_prov;
                        //$total_eli_pop_frontline_prov =\DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->frontline_health_workers; //Frontline(A1) SINOVAC_FIRST
                        //$total_eli_pop_senior_prov  = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->senior_citizens;
                        //$total_eli_pop_prov = $total_eli_pop_frontline_prov + $total_eli_pop_senior_prov;


                            //ELIGIBLE_POP_ASTRAZENECA
                            $total_e_pop_astra_frtline_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->frontline_health_workers;
                            $total_e_pop_astra_sr_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->senior_citizens;
                            $total_e_pop_astra_prov = $total_e_pop_astra_frtline_prov + $total_e_pop_astra_sr_prov;
                        //$total_eli_pop_astra_frontline_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->frontline_health_workers;
                        //$total_eli_pop_astra_senior_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'','')")[0]->senior_citizens;
                        //$total_eli_pop_astra_prov = $total_eli_pop_astra_frontline_prov + $total_eli_pop_astra_senior_prov;


                            //VACCINE_ALLOCATED
                            $total_vallocated_svac_frst_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->sinovac_allocated_first;
                            $total_vallocated_svac_scnd_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->sinovac_allocated_second;
                            $total_vallocated_astra_frst_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->astrazeneca_allocated_first;
                            $total_vallocated_astra_scnd_prov= \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->astrazeneca_allocated_second;
                         //$total_vaccine_allocated_sinovac_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->sinovac_allocated_first;
                         //$total_vaccine_allocated_sinovac_second_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->sinovac_allocated_second;
                         //$total_vaccine_allocated_astra_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->astrazeneca_allocated_first;
                         //$total_vaccine_allocated_astra_second_prov= \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->astrazeneca_allocated_second;


                            //SINOVAC
                            $total_svac_a1_frst_prov = 0;
                            $total_svac_a2_frst_prov= 0;
                            $total_svac_a1_scnd_prov = 0;
                            $total_svac_a2_scnd_prov = 0;

                            $total_svac_a1_frst_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','frontline_health_workers')")[0]->vaccinated_first_a;
                            $total_svac_a2_frst_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','indigent_senior_citizens')")[0]->vaccinated_first_a;
                            $total_svac_a1_scnd_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','frontline_health_workers')")[0]->vaccinated_second_a;
                            $total_svac_a2_scnd_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','indigent_senior_citizens')")[0]->vaccinated_second_a;
                        //$total_sinovac_a1_first_prov = 0;
                        //$total_sinovac_a2_first_prov= 0;
                        //$total_sinovac_a1_second_prov = 0;
                        //$total_sinovac_a2_second_prov = 0;

                        //$total_sinovac_a1_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','frontline_health_workers')")[0]->vaccinated_first_a;
                        //$total_sinovac_a2_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','indigent_senior_citizens')")[0]->vaccinated_first_a;
                        //$total_sinovac_a1_second_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','frontline_health_workers')")[0]->vaccinated_second_a;
                        //$total_sinovac_a2_second_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','indigent_senior_citizens')")[0]->vaccinated_second_a;

                            //ASTRAZENECA
                            $total_astra_a1_frst_prov = 0;
                            $total_astra_a2_frst_prov = 0;
                            $total_astra_a1_scnd_prov = 0;
                            $total_astra_a2_scnd_prov = 0;

                            $total_astra_a1_frst_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','frontline_health_workers')")[0]->vaccinated_first_a; // A1 ROW-3
                            $total_astra_a2_frst_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_first_a;
                            $total_astra_a1_scnd_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','frontline_health_workers')")[0]->vaccinated_second_a;
                            $total_astra_a2_scnd_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_second_a;
                        //$total_astra_a1_first_prov = 0;
                        //$total_astra_a2_first_prov = 0;
                        //$total_astra_a1_second_prov = 0;
                        //$total_astra_a2_second_prov = 0;

                        //$total_astra_a1_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','frontline_health_workers')")[0]->vaccinated_first_a; // A1 ROW-3
                        //$total_astra_a2_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_first_a;
                        //$total_astra_a1_second_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','frontline_health_workers')")[0]->vaccinated_second_a;
                        //$total_astra_a2_second_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','indigent_senior_citizens')")[0]->vaccinated_second_a;


                            //VACCINATED_FIRST
                            $total_vcted_svac_frst_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->vaccinated_first;
                            $total_vcted_astra_frst_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->vaccinated_first;
                        //$total_vaccinated_sinovac_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->vaccinated_first;
                        //$total_vaccinated_astra_first_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->vaccinated_first;

                            //VACCINATED_SECOND
                            $total_vcted_svac_scnd_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->vaccinated_second;
                            $total_vcted_astra_scnd_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->vaccinated_second;
                        //$total_vaccinated_sinovac_second_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->vaccinated_second;
                        //$total_vaccinated_astra_second_prov = \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->vaccinated_second;

                            //MILD_FIRST
                            $total_mild_svac_frst_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->mild_first;
                            $total_mild_astra_frst_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->mild_first;
                        //$total_mild_sinovac_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->mild_first;
                        //$total_mild_astra_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->mild_first;

                            //MILD_SECOND
                            $total_mild_svac_scnd_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->mild_second;
                            $total_mild_astra_scnd_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->mild_second;
                        //$total_mild_sinovac_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->mild_second;
                        //$total_mild_astra_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->mild_second;


                            //SERIOUS_FIRST
                            $total_srs_svac_frst_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->serious_first;
                            $total_srs_astra_frst_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->serious_first;
                        //$total_serious_sinovac_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->serious_first;
                        //$total_serious_astra_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->serious_first;

                            //SERIOUS_SECOND
                            $total_srs_svac_scnd_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->serious_second;
                            $total_srs_astra_scnd_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->serious_second;
                        //$total_serious_sinovac_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->serious_second;
                        //$total_serious_astra_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->serious_second;


                            //DEFERRED_FIRST
                            $total_dfrd_svac_frst_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->deferred_first;
                            $total_dfrd_astra_frst_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->deferred_first;
                        //$total_deferred_sinovac_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->deferred_first;
                        //$total_deferred_astra_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->deferred_first;

                            //DEFERRED_SECOND
                            $total_dfrd_svac_scnd_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->deferred_second;
                            $total_dfrd_astra_scnd_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->deferred_second;
                        //$total_deferred_sinovac_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->deferred_second;
                        //$total_deferred_astra_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->deferred_second;


                            //REFUSED_FIRST
                            $total_rfsd_svac_frst_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->refused_first;
                            $total_rfsd_astra_frst_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->refused_first;
                        //$total_refused_sinovac_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->refused_first;
                        //$total_refused_astra_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->refused_first;

                            //REFUSED_SECOND
                            $total_rfsd_svac_scnd_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->refused_second;
                            $total_rfsd_astra_scnd_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->refused_second;
                        //$total_refused_sinovac_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->refused_second;
                        //$total_refused_astra_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->refused_second;


                            //WASTAGE_FIRST
                            $total_wstge_svac_frst_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->wastage_first;
                            $total_wstge_astra_frst_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->wastage_first;
                        //$total_wastage_sinovac_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->wastage_first;
                        //$total_wastage_astra_first_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->wastage_first;


                            //WASTAGE_SECOND
                            $total_wstge_svac_scnd_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->wastage_second;
                            $total_wstge_astra_scnd_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->wastage_second;
                        //$total_wastage_sinovac_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Sinovac','')")[0]->wastage_second;
                        //$total_wastage_astra_second_prov =  \DB::connection('mysql')->select("call vaccine_data_province($province_id,'Astrazeneca','')")[0]->wastage_second;



                            //VACCINE_ALLOCATED
                            $total_vallocated_frst_svac = $total_vallocated_svac_frst_prov + $total_vallocated_svac_scnd_prov;
                            $total_vallocated_frst_astra = $total_vallocated_astra_frst_prov + $total_vallocated_astra_scnd_prov;
                            $total_vallocated = $total_vallocated_frst_svac + $total_vallocated_frst_astra;

                            //TOTAL_VACCINE_ALLOCATED
                            $total_vallocated_frst_prov = $total_vallocated_svac_frst_prov + $total_vallocated_astra_frst_prov;
                            $total_vallocated_scnd_prov = $total_vallocated_svac_scnd_prov + $total_vallocated_astra_scnd_prov;
                            $total_vallocated = $total_vallocated_frst_prov + $total_vallocated_scnd_prov;


                            //VACCINATED
                            $total_vcted_first_prov = $total_vcted_svac_frst_prov + $total_vcted_astra_frst_prov;
                            $total_vcted_scnd_prov = $total_vcted_svac_scnd_prov + $total_vcted_astra_scnd_prov;
                            $total_vcted_svac_frst_prov = $total_svac_a1_frst_prov +  $total_svac_a2_frst_prov;
                            $total_vcted_svac_scnd_prov = $total_svac_a1_scnd_prov + $total_svac_a2_scnd_prov;

                            $total_vcted_astra_frst_prov = $total_astra_a1_frst_prov + $total_astra_a2_frst_prov;
                            $total_vcted_astra_scnd_prov = $total_astra_a1_scnd_prov + $total_astra_a2_scnd_prov;


                            //REFUSED FIRST
                            $total_rfsd_frst_prov = $total_rfsd_svac_frst_prov + $total_rfsd_astra_frst_prov;
                            //REFUSED SECOND
                            $total_rfsd_scnd_prov = $total_rfsd_svac_scnd_prov + $total_rfsd_astra_scnd_prov;


                            //PERCENT_COVERAGE
                            $total_p_cvrge_svac_frst_prov = $total_vcted_svac_frst_prov / $total_e_pop_svac_prov * 100;
                            $total_p_cvrge_svac_scnd_prov =  $total_vcted_svac_scnd_prov / $total_e_pop_svac_prov * 100;
                            $total_p_cvrge_astra_frst_prov = $total_vcted_astra_frst_prov / $total_e_pop_astra_prov * 100;
                            $total_p_cvage_astra_scnd_prov =  $total_vcted_astra_scnd_prov / $total_e_pop_astra_prov * 100;


                            //CONSUMPTION_RATE
                            $total_c_rate_svac_frst_prov = $total_vcted_svac_frst_prov / $total_vallocated_svac_frst_prov * 100;
                            $total_c_rate_svac_scnd_prov = $total_vcted_svac_scnd_prov / $total_vallocated_svac_scnd_prov * 100;
                            $total_c_rate_astra_frst_prov = $total_vcted_astra_frst_prov / $total_vallocated_astra_frst_prov * 100;
                            $total_c_rate_astra_scnd_prov = $total_vcted_astra_scnd_prov / $total_vallocated_astra_scnd_prov * 100;

                            //REMAINING UNVACCINATED
                            $total_r_unvcted_frst_svac_prov = $total_e_pop_svac_prov - $total_vcted_svac_frst_prov - $total_rfsd_svac_frst_prov;
                            $total_r_unvcted_frst_astra_prov = $total_e_pop_astra_prov - $total_vcted_astra_frst_prov - $total_rfsd_astra_frst_prov;
                            $total_r_unvcted_scnd_svac_prov = $total_e_pop_svac_prov - $total_vcted_svac_scnd_prov - $total_rfsd_svac_scnd_prov;
                            $total_r_unvcted_scnd_astra_prov = $total_e_pop_astra_prov - $total_vcted_astra_scnd_prov - $total_rfsd_astra_scnd_prov;


                            //TOTAL_REMAINING_UNVACCINATED
                            $total_r_unvcted_frst_prov = $total_e_pop_svac_prov - $total_vcted_first_prov - $total_rfsd_frst_prov;
                            $total_r_unvcted_scnd_prov = $total_e_pop_astra_prov - $total_vcted_scnd_prov - $total_rfsd_scnd_prov;



                            //TOTAL_VACCINATED
                            $total_vcted_svac_frst_prov = $total_svac_a1_frst_prov + $total_astra_a1_frst_prov;
                            $total_vcted_svac_scnd_prov = $total_svac_a2_frst_prov + $total_astra_a2_frst_prov;
                            $total_vcted_svac_prov = $total_vcted_svac_frst_prov + $total_vcted_svac_scnd_prov;

                            $total_vcted_scnd_a1 = $total_svac_a1_scnd_prov + $total_astra_a1_scnd_prov;
                            $total_vcted_scnd_a2 = $total_svac_a2_scnd_prov + $total_astra_a2_scnd_prov;
                            $total_vcted_astra_prov = $total_vcted_scnd_a1 + $total_vcted_scnd_a2;


                            //TOTAL_PERCENT_COVERAGE
                            $total_p_cvrge_frst = $total_vcted_svac_frst_prov / $total_vallocated_frst_prov * 100;
                            $total_p_cvrge_scnd = $total_vcted_svac_scnd_prov / $total_vallocated_scnd_prov * 100;


                            //TOTAL_CONSUMPTION_RATE
                            $total_c_rate_frst = $total_vcted_svac_prov / $total_vallocated_frst_prov * 100;
                            $total_c_rate_scnd =  $total_vcted_astra_prov / $total_vallocated_scnd_prov * 100;

                            //TOTAL_REMAINING_UNVACCINATED_ALL
                            $total_r_unvcted_all_frst  = $total_e_pop_svac_prov - $total_vcted_svac_prov - $total_rfsd_frst_prov;
                            $total_r_unvcted_all_scnd  = $total_e_pop_svac_prov - $total_vcted_scnd_prov - $total_rfsd_scnd_prov;

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
                                    <td rowspan="2">{{ $total_e_pop_frtline_prov }}</td>  <!-- Frontline(A1) SINOVAC_FIRST-->
                                    <td rowspan="2">{{ $total_e_pop_sr_prov }} </td>   <!-- Senior (A2) SINOVAC_FIRST-->
                                    <td rowspan="2">{{ $total_e_pop_svac_prov }} <!-- TOTAL ELI POP SINOVAC_FIRST-->
                                    </td>
                                    <td rowspan="2">{{ $total_vallocated_svac_frst_prov }} </td> <!-- VACCINE ALLOCATED (FD) SINOVAC_FIRST-->
                                    <td rowspan="2">{{ $total_vallocated_svac_scnd_prov }} </td>  <!-- VACCINE ALLOCATED (SD) SINOVAC_FIRST-->
                                    <td rowspan="2"> {{ $total_vallocated_frst_svac }}</td>  <!-- TOTAL VACCINE ALLOCATED SINOVAC_FIRST-->
                                    <td>
                                        <span class="label label-success">{{ $total_svac_a1_frst_prov }}</span>   <!-- VACCINATED (A1) SINOVAC_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_svac_a2_frst_prov }}</span>  <!-- VACCINATED (A2) SINOVAC_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_vcted_svac_frst_prov }}</span>  <!-- TOTAL VACCINATED SINOVAC_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_mild_svac_frst_prov }}</span>  <!-- MILD SINOVAC_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_srs_svac_frst_prov }}</span> <!-- SERIOUS SINOVAC_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_dfrd_svac_frst_prov }}</span> <!-- DEFERRED SINOVAC_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_rfsd_svac_frst_prov }}</span> <!-- REFUSED SINOVAC_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_wstge_svac_frst_prov }}</span> <!-- WASTAGE SINOVAC_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ number_format($total_p_cvrge_svac_frst_prov,2) }}%</span> <!-- PERCENT COVERAGE SINOVAC_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ number_format($total_c_rate_svac_frst_prov,2) }}%</span> <!-- CONSUMPTION RATE SINOVAC_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_r_unvcted_frst_svac_prov }}</span> <!-- REMAINING UNVACCINATED SINOVAC_FIRST-->
                                    </td>
                                </tr>
                                <tr style="background-color: #ffd8d6">
                                    <td>
                                        <span class="label label-warning">{{ $total_svac_a1_scnd_prov }}</span>   <!-- VACCINATED (A1) SINOVAC_SECOND-->
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_svac_a2_scnd_prov }}</span> <!-- VACCINATED (A2) SINOVAC_SECOND-->
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_vcted_svac_scnd_prov }}</span> <!-- TOTAL VACCINATED SINOVAC_SECOND-->
                                    </td> <!-- 1-4 -->
                                    <td>
                                        <span class="label label-warning">{{ $total_mild_svac_scnd_prov }}</span> <!-- MILD SINOVAC_SECOND-->
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_srs_svac_scnd_prov }}</span> <!-- SERIOUS  SINOVAC_SECOND-->
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_dfrd_svac_scnd_prov }}</span> <!-- DEFERRED  SINOVAC_SECOND-->
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_rfsd_svac_scnd_prov }}</span> <!-- REFUSED  SINOVAC_SECOND-->
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_wstge_svac_scnd_prov }}</span> <!-- WASTAGE SINOVAC_SECOND-->
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ number_format($total_p_cvrge_svac_scnd_prov,2)}}%</span> <!-- PERCENT COVERAGE  SINOVAC_SECOND-->
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ number_format($total_c_rate_svac_scnd_prov,2) }}%</span> <!-- CONSUMPTION RATE SINOVAC_SECOND-->
                                    </td>
                                    <td>
                                        <span class="label label-warning">{{ $total_r_unvcted_scnd_svac_prov }} </span> <!-- REMAINING UNVACCINATED  SINOVAC_SECOND-->
                                    </td>
                                </tr>
                                </tbody>
                                <tbody><tr>

                                <tr style="background-color: #f2fcac">
                                    <td rowspan="2">

                                    </td> <!-- 1-5 -->
                                    <td rowspan="2" style="color:black;">{{ $total_e_pop_frtline_prov }}</td>  <!-- Frontline(A1) ASTRA_FIRST-->
                                    <td rowspan="2" style="color:black;">{{ $total_e_pop_sr_prov }}</td> <!-- SENIOR(A2) ASTRA_FIRST-->
                                    <td rowspan="2" style="color:black;">
                                        {{ $total_e_pop_svac_prov }} <!-- TOTAL E POP ASTRA_FIRST-->
                                    </td>
                                    <td rowspan="2" style="color:black">{{ $total_vallocated_astra_frst_prov }}</td> <!-- VACCINE_ALLOCATED (FD) ASTRA_FIRST-->
                                    <td rowspan="2" style="color:black">{{ $total_vallocated_astra_scnd_prov }}</td>  <!-- VACCINE ALLOCATED (SD) ASTRA_FIRST-->
                                    <td rowspan="2" style="color:black;">{{ $total_vallocated_frst_astra }}</td>  <!-- TOTAL VACCINE ALLOCATED ASTRA_FIRST-->
                                    <td style="color:black;">
                                        <span class="label label-success">{{ $total_astra_a1_frst_prov }}</span>  <!-- VACCINATED (A1) ASTRA_FIRST-->
                                    </td>
                                    <td style="color:black">
                                        <span class="label label-success">{{ $total_astra_a2_frst_prov }}</span> <!-- VACCINATED (A2) ASTRA_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_vcted_astra_frst_prov }}</span> <!-- TOTAL VACCINATED ASTRA_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_mild_astra_frst_prov }}</span> <!-- MILD ASTRA_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_srs_astra_frst_prov }}</span> <!-- SERIOUS ASTRA_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_dfrd_astra_frst_prov }}</span> <!-- DEFERRED ASTRA_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_rfsd_astra_frst_prov }}</span> <!-- REFUSED ASTRA_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_wstge_astra_frst_prov }}</span> <!-- WASTAGE ASTRA_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ number_format($total_p_cvrge_astra_frst_prov,2) }}%</span> <!-- PERCENT_COVERAGE ASTRA_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ number_format($total_c_rate_astra_frst_prov,2) }}%</span> <!-- CONSUMPTION RATE ASTRA_FIRST-->
                                    </td>
                                    <td>
                                        <span class="label label-success">{{ $total_r_unvcted_frst_astra_prov }}</span> <!-- REMAINUNG UNVACCINATED ASTRA_FIRST-->
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
                                <tbody><tr>
                                    <td>Total</td> <!-- 1-7 -->
                                    <td></td>
                                    <td></td>
                                    <td></td>
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
                                        <b class="label label-success" style="margin-right: 5%">{{ number_format($total_p_cvrge_frst,2) }}%</b>  <!-- TOTAL PERCENT_COVERAGE -->
                                    </td>
                                    <td>
                                        <b class="label label-success" style="margin-right: 5%">{{ number_format($total_c_rate_frst,2) }}% </b>  <!-- TOTAL CONSUMPTION RATE -->
                                    </td>
                                    <td>
                                        <b class="label label-success" style="margin-right: 5%">{{ $total_r_unvcted_all_frst }}</b>  <!-- TOTAL REMAINUNG UNVACCINATED  -->
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

                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_scnd_a1 }}</b> <!-- TOTAL VACCINATED (FD) 2 -->
                                    </td>
                                    <td>

                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_vcted_scnd_a2 }}</b> <!-- TOTAL VACCINATED (SD) 2 -->
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
                                        <b class="label label-warning" style="margin-right: 5%">{{ number_format($total_p_cvrge_scnd,2) }}%</b> <!-- TOTAL PERCENT_COVERAGE  2 -->
                                    </td>
                                    <td>
                                        <b class="label label-warning" style="margin-right: 5%"> {{ number_format($total_c_rate_scnd,2) }}%</b> <!-- TOTAL CONSUMPTION RATE  2 -->
                                    </td>
                                    <td>
                                        <b class="label label-warning" style="margin-right: 5%">{{ $total_r_unvcted_all_scnd }}</b> <!-- TOTAL REMAINING UNVACCIANTED  2 -->
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
