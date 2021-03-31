@extends('layouts.app')

@section('content')

    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-header">
                <form action="{{ asset('vaccine/vaccineview') }}" method="GET">
                    {{ csrf_field() }}
                    <div class="input-group input-group-md" style="width: 70%">
                        <input type="text" class="form-control" style="width: 100%" placeholder="Search ownership" name="search" value="{{ $search }}">
                        <span class="input-group-btn">
                            <input type="text" class="form-control" style="width: 50%" id="date_range" placeholder="Enter date range.." name="date_range" value="{{ date("m/d/Y",strtotime($date_range_start)).' - '.date("m/d/Y",strtotime($date_range_end)) }}">
                            <button type="submit" class="btn btn-success"><i class="fa fa-filter"></i> Filter</button>
                            <a href="{{ asset('export/client/call') }}" type="button" class="btn btn-danger"><i class="fa fa-file-excel-o"></i> Export Excel</a>
                            <button type="button" class="btn btn-warning" onclick="refreshPage()"><i class="fa fa-eye"></i> View All</button>
                            <button type="button" class="btn btn-primary" onclick="newVaccinated()"><i class="fa fa-eyedropper"></i> New Vaccinated</button>
                        </span>
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
                <div class="">
                    @if(count($vaccine)>0)
                        <div class="table-responsive">
                            <table class="table table-striped" style="font-size: 8pt">
                                <thead class="bg-gray">
                                <tr>
                                    <th>Type of Vaccine</th>
                                    <th>Priority</th>
                                    <th>Encoded by</th>
                                    <th>Province</th>
                                    <th>Municipality</th>
                                    <th>Facility</th>
                                    <th>No. of eligible population A1.1-A1.7</th>
                                    <th>Ownership</th>
                                    <th>No. of Vaccine Allocated</th>
                                    <th>Date of Delivery</th>
                                    <th>First Dose</th>
                                    <th>Second Dose</th>
                                    <th>Target Dose Per Day</th>
                                    <th>No. of Vaccinated</th>
                                    <th>Mild</th>
                                    <th>Serious</th>
                                    <th>Deferred</th>
                                    <th>Refused</th>
                                    <th>Percentage Coverage</th>
                                    <th>Consumption Rate</th>
                                    <th>Remaining Unvaccinated</th>
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

                                        </td>
                                        <td>
                                            <?php
                                            $transacted_by = \App\User::find($row->encoded_by);
                                            $transacted_by = $transacted_by->fname.' '.ucfirst($transacted_by->mname[0]).'. '.$transacted_by->lname;
                                            ?>
                                            <span>{{ $transacted_by }}</span><br>

                                        </td>
                                        <td>
                                            <?php
                                            $province = \App\Province::find($row->province_id);
                                            ?>
                                            <span>{{ $province->description }}</span><br>
                                        </td>
                                        <td>
                                            <?php
                                            $municipality = \App\Muncity::find($row->muncity_id);
                                            ?>
                                            <span>{{ $municipality->description }}</span><br>
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
                                            {{ date('F j, Y',strtotime($row->dateof_del)) }}
                                                @else
                                                <span class="label label-danger">Pending</span>
                                                @endif
                                        </td>
                                        <td>
                                            @if($row->first_dose)
                                                {{ date('F j, Y',strtotime($row->first_dose)) }}
                                            @else
                                                <span class="label label-danger">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($row->second_dose)
                                                {{ date('F j, Y',strtotime($row->second_dose)) }}
                                            @else
                                                <span class="label label-danger">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $row->tgtdoseper_day }}
                                        </td>
                                        <td>
                                            {{ $row->numof_vaccinated }}
                                        </td>
                                        <td>
                                            {{ $row->aef1 }}
                                        </td>
                                        <td>
                                            {{ $row->aef1_qty }}
                                        </td>
                                        <td>
                                            {{ $row->deferred }}
                                        </td>
                                        <td>
                                            {{ $row->refused }}
                                        </td>
                                        <td>
                                            {{ ($row->numof_vaccinated/$row->no_eli_pop) * 100 }}%

                                        </td>
                                        <td>
                                            {{ $row->numof_vaccinated * 100 /$row->nvac_allocated }}
                                        </td>
                                        <td>
                                            {{ $row->no_eli_pop - $row->numof_vaccinated }}
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
                    <h3 id="myModalLabel"><i class="fa fa-eyedropper"></i> Vaccine Information</h3>
                </div>
                <div class="modal-body vaccinated_content">

                </div><!-- /.modal-content -->
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection

@section('js')
    <script>
        $("#container").removeClass("container");
        $("#container").addClass("container-fluid");

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

        $('.sinovac_count').html("<?php echo $sinovac_count; ?>");
        $('.astrazeneca_count').html("<?php echo $astrazeneca_count; ?>");
        $('.moderna_count').html("<?php echo $moderna_count; ?>");
        $('.pfizer_count').html("<?php echo $pfizer_count; ?>");


    </script>

@endsection

