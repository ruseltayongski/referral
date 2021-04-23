@extends('layouts.app')

@section('content')
    <style>
        @media (min-width: 0px) {
            .modal_w {
                width: 98%;
            }
        }

    </style>
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-right">
                <form action="{{ asset('admin/municipality').'/'.$province_id }}" method="POST" class="form-inline">
                    {{ csrf_field() }}
                    <div class="form-group-lg" style="margin-bottom: 10px;">
                        <input type="text" class="form-control" name="keyword_muncity" placeholder="Search municipality..." value="{{ Session::get("keyword_muncity") }}">
                        <button type="submit" class="btn btn-success btn-sm btn-flat">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <button type="submit" value="view_all" name="view_all" class="btn btn-warning btn-sm btn-flat">
                            <i class="fa fa-eye"></i> View All
                        </button>
                        <a href="#facility_modal" data-toggle="modal" class="btn btn-info btn-sm btn-flat" onclick="MunicipalityBody('<?php echo $province_id; ?>','empty')">
                            <i class="fa fa-hospital-o"></i> Add Municipality
                        </a>
                    </div>
                </form>
            </div>
            <h1>{{ $title }}</h1>
            <b class="text-yellow" style="font-size: 13pt;">{{ $province_name }} Province</b>
        </div>
        <div class="box-body">
            @if(count($data)>0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        @foreach($data as $row)
                            <?php
                                $vaccine = \App\VaccineAccomplished::where("muncity_id",$row->id)->orderBy("date_first","asc")->first();
                            ?>
                            <tr>
                                <td style="white-space: nowrap;">
                                    <b>
                                        <a  class="text-green" style="font-size:11pt;cursor: pointer;" onclick="muncityVaccinated('<?php echo $row->province_id; ?>','<?php echo $row->id; ?>',$(this))">
                                            {{ $row->description }}
                                        </a>
                                    </b>
                                </td>
                                <td style="white-space: nowrap;">
                                    <a
                                            href="#facility_modal"
                                            data-toggle="modal"
                                            onclick="MunicipalityBody('<?php echo $province_id; ?>','<?php echo $row->id; ?>')"
                                    >
                                        @if($row->frontline_health_workers || $row->senior_citizens)
                                            {{ $row->frontline_health_workers + $row->senior_citizens }}
                                        @else
                                            empty
                                        @endif
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="14">
                                    <table style="font-size: 10pt;" class="table table-striped">
                                        <tbody><tr>
                                            <th>Type of Vaccine</th> <!-- Type of Vaccine 1-1 -->
                                            <th colspan="3"><center>Eligible Population</center></th>
                                            <th>Vaccine Allocated</th>
                                            <th>Total Vaccinated</th>
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
                                            <th>Frontline</th>
                                            <th>Seniors</th>
                                            <th>Total</th>
                                            <td></td>
                                            <td></td>
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
                                            <td colspan="14">
                                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                    Sinovac
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody><tbody id="collapseTwo" class="collapse bg-danger" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                        <tr style="background-color: #ffd8d6">
                                            <td rowspan="2">

                                            </td> <!-- 1-3 -->
                                            <td rowspan="2">600</td>
                                            <td rowspan="2">0</td>
                                            <td rowspan="2">
                                                600
                                            </td>
                                            <td rowspan="2">1600</td>
                                            <td>
                                                <span class="label label-success">146</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">4</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">6</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">9</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">7</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">5</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">24.33%</span>
                                            </td>
                                            <td>
                                                <span class="label label-success"> 9.13%</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">447</span>
                                            </td>
                                        </tr>
                                        <tr style="background-color: #ffd8d6">
                                            <td>
                                                <span class="label label-warning">22</span>
                                            </td> <!-- 1-4 -->
                                            <td>
                                                <span class="label label-warning">6</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">5</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">5</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">5</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">5</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">3.67%</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">1.38%</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">573 </span>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tbody><tr>
                                            <td colspan="14">
                                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                                    Astrazeneca
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody><tbody id="collapse2" class="collapse bg-primary" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                        <tr style="background-color: #c3f6ff">
                                            <td rowspan="2"></td> <!-- 1-5 -->
                                            <td rowspan="2" style="color:black;">0</td>
                                            <td rowspan="2" style="color:black;">350</td>
                                            <td rowspan="2" style="color:black;">
                                                350
                                            </td>
                                            <td rowspan="2" style="color:black;">700</td>
                                            <td>
                                                <span class="label label-success">60</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">5</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">5</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">5</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">5</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">5</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">17.14%</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">8.57%</span>
                                            </td>
                                            <td>
                                                <span class="label label-success">285 </span>
                                            </td>
                                        </tr>
                                        <tr style="background-color: #c3f6ff">
                                            <td>
                                                <span class="label label-warning">30</span>
                                            </td> <!-- 1-6 -->
                                            <td>
                                                <span class="label label-warning">3</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">3</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">3</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">3</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">3</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">17.14%</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">4.29%</span>
                                            </td>
                                            <td>
                                                <span class="label label-warning">317</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tbody><tr>
                                            <td>Total</td> <!-- 1-7 -->
                                            <td>
                                                <b> 600 </b>
                                            </td>
                                            <td>
                                                <b> 350 </b>
                                            </td>
                                            <td>
                                                <b> 950 </b>
                                            </td>
                                            <td>
                                                <b>2300</b>
                                            </td>
                                            <td>
                                                <b class="label label-success" style="margin-right: 5%">206</b>
                                            </td>
                                            <td>
                                                <b class="label label-success" style="margin-right: 5%">9</b>
                                            </td>
                                            <td>
                                                <b class="label label-success" style="margin-right: 5%">11</b>
                                            </td>
                                            <td>
                                                <b class="label label-success" style="margin-right: 5%">14</b>
                                            </td>
                                            <td>
                                                <b class="label label-success" style="margin-right: 5%">12</b>
                                            </td>
                                            <td>
                                                <b class="label label-success" style="margin-right: 5%">10</b>
                                            </td>
                                            <td>
                                                <b class="label label-success" style="margin-right: 5%">8.96%</b>
                                            </td>
                                            <td>
                                                <b class="label label-success" style="margin-right: 5%">21.68%</b>
                                            </td>
                                            <td>
                                                <b class="label label-success" style="margin-right: 5%">732</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td> <!-- 1-7 -->
                                            <td>

                                            </td>
                                            <td>

                                            </td>
                                            <td>

                                            </td>
                                            <td>

                                            </td>
                                            <td>
                                                <b class="label label-warning" style="margin-right: 5%">52</b>
                                            </td>
                                            <td>
                                                <b class="label label-warning" style="margin-right: 5%">9</b>
                                            </td>
                                            <td>
                                                <b class="label label-warning" style="margin-right: 5%">8</b>
                                            </td>
                                            <td>
                                                <b class="label label-warning" style="margin-right: 5%">8</b>
                                            </td>
                                            <td>
                                                <b class="label label-warning" style="margin-right: 5%">8</b>
                                            </td>
                                            <td>
                                                <b class="label label-warning" style="margin-right: 5%">8</b>
                                            </td>
                                            <td>
                                                <b class="label label-warning" style="margin-right: 5%">2.26%</b>
                                            </td>
                                            <td>
                                                <b class="label label-warning" style="margin-right: 5%">5.47%</b>
                                            </td>
                                            <td>
                                                <b class="label label-warning" style="margin-right: 5%">890</b>
                                            </td>
                                        </tr>
                                        </tbody></table>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    <div class="text-center">
                        {{ $data->links() }}
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


    @include('admin.modal.facility_modal')
@endsection
@section('js')
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
    </script>
@endsection

