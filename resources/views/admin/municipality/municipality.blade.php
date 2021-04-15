@extends('layouts.app')

@section('content')
    <style>
        @media (min-width: 1200px) {
            .modal_w {
                width: 70%;
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
                        <tr class="bg-black">
                            <th>Municipality</th>
                            <th>Eligible Population</th>
                            <th>Total Vaccine Allocated</th>
                            <th>Start Date</th>
                            <th>Priority</th>
                            <th>Vaccine</th>
                            <th>Vaccinated</th>
                            <th>AEFI</th>
                            <th>Deferred</th>
                            <th>Refused</th>
                            <th>Wastage</th>
                            <th>% Coverage</th>
                            <th>Consumption Rate</th>
                            <th>Remaining Unvaccinated</th>
                        </tr>
                        @foreach($data as $row)
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
                                <td>
                                    <p>{{ $row->vaccine_allocated }}</p>
                                </td>
                                <td>

                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
                                </td>
                                <td>
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
                console.log(data);
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
    </script>
@endsection

