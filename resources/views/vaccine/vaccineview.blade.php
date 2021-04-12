@extends('layouts.app')

@section('content')
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-header">
                <form action="{{ asset('vaccine/vaccineview') }}" method="GET">
                    {{ csrf_field() }}
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="typeof_vaccine_filter" id="typeof_vaccine_filter" class="select2">
                                    <option value="">Select Type of Vaccine</option>
                                    <option value="Sinovac" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Sinovac')echo 'selected';} ?>>Sinovac</option>
                                    <option value="Astrazeneca" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Astrazeneca')echo 'selected';} ?>>Astrazeneca</option>
                                    <option value="Moderna" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Moderna')echo 'selected';} ?> disabled>Moderna</option>
                                    <option value="Pfizer" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Pfizer')echo 'selected';} ?> disabled>Pfizer</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="province_id_filter" id="province_id_filter" class="select2" onchange="onChangeProvinceFilter($(this).val())">
                                    <option value="">Select Province</option>
                                    @foreach($province as $row)
                                        <option value="{{ $row->id }}"  <?php if(isset($vaccine->province_id)){if($vaccine->province_id == $row->id)echo 'selected';} ?> >{{ $row->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="municipality_filter" id="municipality_filter" class="select2">
                                    <option value="">Select Municipality</option>
                                    @if(isset($vaccine->muncity_id))
                                        @foreach($muncity as $row)
                                            <option value="{{ $row->id }}"  <?php if(isset($vaccine->muncity_id)){if($vaccine->muncity_id == $row->id)echo 'selected';} ?> >{{ $row->description }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
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
                                    <button type="button" class="btn btn-primary" onclick="newVaccinated()"><i class="fa fa-eyedropper"></i> New Vaccinated</button>
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
                <strong>Legend:</strong>
                <table>
                    <tbody><tr>
                        <td style="font-size: 15pt;">
                            <i class="fa fa-circle text-green"></i>
                        </td>
                        <td>&nbsp;&nbsp;First Dose</td>
                    </tr>
                    <tr>
                        <td style="font-size: 15pt;">
                            <i class="fa fa-circle text-yellow"></i>
                        </td>
                        <td>&nbsp;&nbsp;Second Dose</td>
                    </tr>
                    </tbody>
                </table>
                <br>
                <div class="">
                    @if(count($vaccine)>0)
                        <div class="table-responsive">
                            <table class="table table-striped" style="font-size: 8pt">
                                <thead class="bg-gray">
                                <tr>
                                    <th>Type of Vaccine</th>
                                    <th>Municipality</th>
                                    <th>Priority</th>
                                    <th>Province</th>
                                    <th>Facility</th>
                                    <th>No. of eligible population</th>
                                    <th>Ownership</th>
                                    <th>No. of Vaccine Allocated</th>
                                    <th style="width:7%;">Date of Delivery</th>
                                    <th>Target Dose <br>Per Day</th>
                                    <th>Number <br>of Vaccinated</th>
                                    <th>AEFI</th>
                                    <th>AEFI Qty</th>
                                    <th>Total Deferred</th>
                                    <th>Total Refused</th>
                                    <th>Total Wastage</th>
                                    <th>Percentage Coverage</th>
                                    <th>Consumption Rate</th>
                                    <th>Remaining Unvaccinated</th>
                                    <th>Encoded by</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sinovac_count = 0;
                                $astrazeneca_count =0;
                                $moderna_count =0;
                                $pfizer_count =0;
                                ?>
                                @foreach($vaccine as $row)
                                    <?php
                                    if($row->typeof_vaccine == 'Sinovac'){
                                        $sinovac_count++;
                                    }
                                    elseif ($row->typeof_vaccine == 'Astrazeneca'){
                                        $astrazeneca_count++;
                                    }
                                    elseif ($row->typeof_vaccine == 'Moderna'){
                                        $moderna_count++;
                                    }
                                    elseif ($row->typeof_vaccine == 'Pfizer'){
                                        $pfizer_count++;
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <b class="text-blue" style="font-size:11pt;cursor: pointer;" onclick="updateVaccinatedView('<?php echo $row->id; ?>',$(this))">{{ $row->typeof_vaccine }}</b>
                                        </td>
                                        <td>
                                            <?php
                                            $municipality = \App\Muncity::find($row->muncity_id);
                                            ?>
                                            <b class="text-green" style="font-size:11pt;cursor: pointer;" onclick="muncityVaccinated('<?php echo $row->id; ?>',$(this))">{{ $municipality->description }}</b>
                                        </td>
                                        <td>
                                          <b> <?php
                                            if($row->priority =='frontline_health_workers'){
                                                echo 'Frontline Health Workers';
                                            }
                                            elseif($row->priority =='indigent_senior_citizens'){
                                                echo 'Indigent Senior Citizens';
                                            }
                                            elseif($row->priority =='remaining_indigent_population'){
                                                echo 'Remaining Indigent Population';
                                            }
                                            elseif($row->priority =='uniform_personnel'){
                                                echo 'Uniform Personnel';
                                            }
                                            elseif($row->priority =='teachers_school_workers'){
                                                echo 'Teachers & School Workers';
                                            }
                                            elseif($row->priority =='all_government_workers'){
                                                echo 'TAll Government Workers (National & Local';
                                            }
                                            elseif($row->priority =='essential_workers'){
                                                echo 'Essential Workers';
                                            }
                                            elseif($row->priority =='socio_demographic'){
                                                echo 'Socio-demographic groups & significant higher risk other than senior citizen and indigent population (e.g.PDL,PWD,IP,Filipinos living in high-density areas)';
                                            }
                                            elseif($row->priority =='ofw'){
                                                echo "OFW's";
                                            }
                                            elseif($row->priority =='remaining_workforce'){
                                                echo "Other remaining workforce";
                                            }
                                            elseif($row->priority =='remaining_filipino_citizen'){
                                                echo "Remaining Filipino Citizen";
                                            }
                                            elseif($row->priority =='etc'){
                                                echo "ETC.";
                                            }
                                            ?>
                                          </b>
                                        </td>
                                        <td>
                                            <?php
                                            $province = \App\Province::find($row->province_id);
                                            ?>
                                            <span>{{ $province->description }}</span><br>
                                        </td>
                                        <td>
                                            <?php
                                            $facility = \App\Facility::find($row->facility_id);
                                            ?>
                                            <span>{{ $facility->name}}</span><br>
                                        </td>
                                        <td>
                                            {{ $row->no_eli_pop }}
                                        </td>
                                        <td>
                                            {{ $row->ownership }}
                                        </td>
                                        <td>
                                            {{ $row->nvac_allocated }}
                                        </td>
                                        <td>
                                            @if($row->dateof_del)
                                                <p class="text-green">{{ date('F j, Y',strtotime($row->dateof_del)) }}</p>
                                            @else
                                                <p class="text-green">Pending </p>
                                            @endif
                                            @if($row->dateof_del2)
                                                <p class="text-yellow">{{ date('F j, Y',strtotime($row->dateof_del2)) }}</p>
                                            @else
                                                <p class="text-yellow">Pending </p>
                                            @endif
                                         <a href="#" onclick="addNewDelivery('<?php echo $row->id; ?>',$(this))" id="workAdd"><i class="fa fa-user-plus"></i> Add New Delivery</a>
                                        </td>
                                        <td>
                                            {{ $row->tgtdoseper_day }}
                                        </td>
                                        <td>
                                            <div style="width:20%;">
                                                <p class="text-green">{{ $row->numof_vaccinated }}</p>
                                                <p class="text-yellow">{{ $row->numof_vaccinated2 }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-green">{{ $row->aefi }}</p>
                                            <p class="text-yellow">{{ $row->aefi2 }}</p>
                                        </td>
                                        <td>
                                            <div style="width:40%;">
                                                <p class="text-green">{{ $row->aefi_qty }}</p>
                                                <p class="text-yellow">{{ $row->aefi_qty2 }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="width:30%;">
                                                <p class="text-green">{{ $row->deferred }}</p>
                                                <p class="text-yellow" >{{ $row->deferred2 }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="width:30%;">
                                                <p class="text-green">{{ $row->refused }}</p>
                                                <p class="text-yellow">{{ $row->refused2 }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="width:30%;">
                                                <p class="text-green">{{ $row->wastage }}</p>
                                                <p class="text-yellow">{{ $row->wastage2 }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $percentage_coverage1 = number_format(($row->numof_vaccinated/$row->no_eli_pop) * 100, 2);
                                            $percentage_coverage2 = number_format(($row->numof_vaccinated2/$row->no_eli_pop) * 100, 2);
                                            ?>
                                            <div style="width:40%;">
                                                <p class="text-green">{{ $percentage_coverage1 }}%</p>
                                                <p class="text-yellow">{{ $percentage_coverage2 }}%</p>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $consumption_rate1 = number_format(($row->numof_vaccinated/$row->nvac_allocated) * 100, 2);
                                            $consumption_rate2 = number_format(($row->numof_vaccinated2/$row->nvac_allocated) * 100, 2);
                                            ?>
                                            <div style="width:40%;">
                                                <p class="text-green">{{ $consumption_rate1 }}%</p>
                                                <p class="text-yellow">{{ $consumption_rate2 }}%</p>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $remaining1 = $row->no_eli_pop - $row->numof_vaccinated - $row->refused;
                                            $remaining2 = $row->no_eli_pop - $row->numof_vaccinated2- $row->refused2;
                                            ?>
                                            <div style="width:40%;">
                                                <p class="text-green">{{ $remaining1 }}</p>
                                                <p class="text-yellow">{{ $remaining2 }}</p>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $transacted_by = \App\User::find($row->encoded_by);
                                            $transacted_by = $transacted_by->fname.' '.ucfirst($transacted_by->mname[0]).'. '.$transacted_by->lname;
                                            ?>
                                            <span>{{ $transacted_by }}</span><br>

                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="pagination">
                            {{ $vaccine->links() }}
                        </div>
                    @else
                        <div class="alert alert-warning">
                        <span class="text-warning">
                            <i class="fa fa-warning"></i> No data found!
                        </span>
                        </div>
                    @endif
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-green"><i class="fa fa-caret-up"></i></span>
                                <h5 class="description-header">{{ number_format($number_eligible_pop_total) }}</h5>
                                <span class="description-text">TOTAL ELIGIBLE POPULATION</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-green"><i class="fa fa-caret-up"></i></span>
                                <h5 class="description-header">{{ number_format($numof_vaccine_allocated) }}</h5>
                                <span class="description-text">TOTAL VACCINE ALLOCATED</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block border-right">
                                <span class="description-header">{{ number_format($numof_vaccinated_total_first + $numof_vaccinated_total_second) }}</span>
                                <h5 class="description-header"><span class="text-green">{{ number_format($numof_vaccinated_total_first) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow">{{ number_format($numof_vaccinated_total_second) }}</span></h5>
                                <span class="description-text">TOTAL VACCINATED</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-3 col-xs-6">
                            <div class="description-block border-right">
                                <span class="description-percentage text-green"><i class="fa fa-caret-up"></i></span>
                                <h5 class="description-header">{{ number_format($targetdose_perday) }}</h5>
                                <span class="description-text">TOTAL TARGET DOSE PER DAY</span>
                            </div>
                            <!-- /.description-block -->
                        </div>
                    </div>
                    <!-- /.row -->
                </div>

                <div class="row">
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block border-right">
                            <span class="description-header">{{ number_format($aefi_qty_first + $aefi_qty_second) }}</span>
                            <h5 class="description-header"><span class="text-green">{{ number_format($aefi_qty_first) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow">{{ number_format($aefi_qty_second) }}</span></h5>
                            <span class="description-text">TOTAL AEFI QUANTITY</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block border-right">
                            <span class="description-header">{{ number_format($total_deferred_first + $total_deferred_second) }}</span>
                            <h5 class="description-header"><span class="text-green">{{ number_format($total_deferred_first) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow">{{ number_format($total_deferred_second) }}</span></h5>
                            <span class="description-text">TOTAL DEFERRED</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block border-right">
                            <span class="description-header">{{ number_format($total_refused_first + $total_refused_second) }}</span>
                            <h5 class="description-header"><span class="text-green">{{ number_format($total_refused_first) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow">{{ number_format($total_refused_second) }}</span></h5>
                            <span class="description-text">TOTAL REFUSED</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block">
                            <span class="description-header">{{ number_format($total_wastage_first + $total_wastage_second) }}</span>
                            <h5 class="description-header"><span class="text-green">{{ number_format($total_wastage_first) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow">{{ number_format($total_wastage_second) }}</span></h5>
                            <span class="description-text">TOTAL WASTAGE</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block">
                            <span class="description-header">{{ number_format(($numof_vaccinated_total_first + $numof_vaccinated_total_second) / $number_eligible_pop_total * 100, 2) }}%</span>
                            <h5 class="description-header"><span class="text-green">{{ number_format( ($numof_vaccinated_total_first) / $number_eligible_pop_total * 100,2) }}%</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow">{{ number_format( ($numof_vaccinated_total_second) / $number_eligible_pop_total * 100,2) }}%</span></h5>
                            <span class="description-text">TOTAL PERCENT COVERAGE</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block">
                            <span class="description-header">{{ number_format(($numof_vaccinated_total_first + $numof_vaccinated_total_second) / ($numof_vaccine_allocated) * 100,2)}}%</span>
                            <h5 class="description-header"><span class="text-green">{{ number_format( ($numof_vaccinated_total_first) / $numof_vaccine_allocated  * 100, 2) }}%</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow">{{ number_format( ($numof_vaccinated_total_second) / $numof_vaccine_allocated  * 100, 2) }}%</span></h5>
                            <span class="description-text">TOTAL CONSUMPTION RATE</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-xs-6">
                        <div class="description-block">
                            <span class="description-percentage text-green"><i class="fa fa-caret-up"></i></span>
                            <h5 class="description-header"><span class="text-green">{{ number_format($number_eligible_pop_total - $numof_vaccinated_total_first - $total_refused_first ) }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-yellow">{{ number_format( ($number_eligible_pop_total - $numof_vaccinated_total_second - $total_refused_second)) }}</span></h5>
                            <span class="description-text">TOTAL REMAINING UNVACCINATED</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                </div>
                <!-- /.row -->
            </div>

        </div>
    </div>

    <div class="modal fade" role="dialog" id="loading_modal">
        <center style="margin-top: 10%;">
            <img src="{{ asset('resources/img/loading.gif') }}" alt="">
        </center>
    </div><!-- /.modal -->

    <div class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" id="vaccine_modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="myModalLabel"><i class="fa fa-medkit" style="color:green"></i> Vaccine Information</h3>
                </div>
                <div class="modal-body vaccinated_content">

                </div><!-- /.modal-content -->
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    <div class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" id="vaccine_modal_municipality">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="myModalLabel"><i class="fa fa-location-arrow" style="color:green"></i> Alicia</h3>
                </div>
                <div class="modal-body vaccinated_content_municipality">

                </div><!-- /.modal-content -->
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" id="dateof_delivery_modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body dateof_delivery_content">

                </div><!-- /.modal-content -->
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection

@section('js')
    <script>
        $("#container").removeClass("container");
        $("#container").addClass("container-fluid");

        $("#dateof_delivery_picker1").daterangepicker({
            "singleDatePicker":true
        });
        $("#dateof_delivery_picker2").daterangepicker({
            "singleDatePicker":true
        });

        function newVaccinated(){
            $("#vaccine_modal").modal('show');
            $("b").css("background-color","");
            $(".vaccinated_content").html(loading);
            var url = "<?php echo asset('vaccine/vaccinated_content'); ?>";
            $.get(url,function(data){
                setTimeout(function(){
                    $(".vaccinated_content").html(data);
                    $(".select2").select2({ width: '100%' });
                },500);
            });
        }

        function muncityVaccinated(vaccine_id,data){
            $("#vaccine_modal_municipality").modal('show');
            $(".vaccinated_content_municipality").html(loading);
            $("b").css("background-color","");
            data.css("background-color","yellow");
            var url = "<?php echo asset('vaccine/vaccinated/municipality/content').'/'; ?>"+vaccine_id;
            $.get(url,function(data){
                setTimeout(function(){
                    $(".vaccinated_content_municipality").html(data);
                    $(".select2").select2({ width: '100%' });
                },500);
            });
        }


        function updateVaccinatedView(vaccine_id,data){
            $("#vaccine_modal").modal('show');
            $(".vaccinated_content").html(loading);
            $("b").css("background-color","");
            data.css("background-color","yellow");
            var url = "<?php echo asset('vaccine/update_view').'/'; ?>"+vaccine_id;
            $.get(url,function(data){
                setTimeout(function(){
                    $(".vaccinated_content").html(data);
                    $(".select2").select2({ width: '100%' });
                },500);
            });
        }

        function addNewDelivery(vaccine_id,data){
            event.preventDefault();
            $("#dateof_delivery_modal").modal('show');
            $(".dateof_delivery_content").html(loading);
            $("b").css("background-color","");
            data.css("background-color","yellow");
            var url = "<?php echo asset('vaccine/new_delivery').'/'; ?>"+vaccine_id;
            $.get(url,function(data){
                setTimeout(function(){
                    $(".dateof_delivery_content").html(data);
                    $(".select2").select2({ width: '100%' });
                },500);
            });
        }


        function onChangeProvinceFilter($province_id){
            $('.loading').show();
            if($province_id){
                var url = "{{ url('opcen/onchange/province') }}";
                $.ajax({
                    url: url+'/'+$province_id,
                    type: 'GET',
                    success: function(data){
                        console.log(data);
                        $("#municipality_filter").select2("val", "");
                        $('#municipality_filter').empty()
                            .append($('<option>', {
                                value: '',
                                text : 'Select Option'
                            }));
                        jQuery.each(data, function(i,val){
                            $('#municipality_filter').append($('<option>', {
                                value: val.id,
                                text : val.description
                            }));
                            $('.loading').hide();
                        });
                    },
                    error: function(){
                        $('#serverModal').modal();
                    }
                });

            } else {
                $("#municipality_filter").select2("val", "");
                $('#municipality_filter').empty()
                    .append($('<option>', {
                        value: '',
                        text : 'Select Option'
                    }));
            }
        }

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

        @if(Session::get("vaccine_update"))
        <?php
        Session::put('vaccine_update',false);
        ?>
        Lobibox.notify('warning', {
            size: 'mini',
            rounded: true,
            msg: 'Your vaccination record is successfully updated!'
        });
        @endif

        $('.sinovac_count').html("<?php echo $sinovac_count; ?>");
        $('.astrazeneca_count').html("<?php echo $astrazeneca_count; ?>");
        $('.moderna_count').html("<?php echo $moderna_count; ?>");
        $('.pfizer_count').html("<?php echo $pfizer_count; ?>");

        $('#date_range').daterangepicker();

    </script>

@endsection

