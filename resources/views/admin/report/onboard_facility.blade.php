@extends('layouts.app')

@section('content')
    <style>
        label {
            padding: 0px !important;
        }
    </style>
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-body">
                @if(count($data) > 0)
                    <h1 style="color: #676767">{{ $data[0]->province }}</h1>
                    <div class="box-header with-border">
                        <form action="{{ asset('onboard/facility').'/'.$province_id }}" method="GET" class="form-inline">
                            {{ csrf_field() }}
                            <div class="form-group-lg">
                                <?php $date_range = date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)); ?>
                                <input type="text" class="form-control" name="date_range" value="{{ $date_range }}" id="consolidate_date_range">
                                <button type="submit" class="btn-lg btn-info btn-flat"><i class="fa fa-search"></i> Filter</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-fixed-header">
                            <?php
                                $count = 0;

                                $facility_onboard[1] = 0;
                                $facility_total[1] = 0;
                                $facility_transaction[1]['with_transaction'] = 0;
                                $facility_transaction[1]['no_transaction'] = 0;

                                $facility_onboard[2] = 0;
                                $facility_total[2] = 0;
                                $facility_transaction[2]['with_transaction'] = 0;
                                $facility_transaction[2]['no_transaction'] = 0;

                                $facility_onboard[3] = 0;
                                $facility_total[3] = 0;
                                $facility_transaction[3]['with_transaction'] = 0;
                                $facility_transaction[3]['no_transaction'] = 0;

                                $facility_onboard[4] = 0;
                                $facility_total[4] = 0;
                                $facility_transaction[4]['with_transaction'] = 0;
                                $facility_transaction[4]['no_transaction'] = 0;

                                $hospital_type[1]['government'] = 0;
                                $hospital_type_total[1]['government'] = 0;
                                $government_transaction[1]['with_transaction'] = 0;
                                $government_transaction[1]['no_transaction'] = 0;

                                $hospital_type_hospital[2]['government'] = 0;
                                $hospital_type_total[2]['government'] = 0;
                                $government_transaction[2]['with_transaction'] = 0;
                                $government_transaction[2]['no_transaction'] = 0;

                                $hospital_type_hospital[3]['government'] = 0;
                                $hospital_type_total[3]['government'] = 0;
                                $government_transaction[3]['with_transaction'] = 0;
                                $government_transaction[3]['no_transaction'] = 0;

                                $hospital_type_hospital[4]['government'] = 0;
                                $hospital_type_total[4]['government'] = 0;
                                $government_transaction[4]['with_transaction'] = 0;
                                $government_transaction[4]['no_transaction'] = 0;

                                $private_transaction[1]['with_transaction'] = 0;
                                $private_transaction[1]['no_transaction'] = 0;
                                $private_transaction[2]['with_transaction'] = 0;
                                $private_transaction[2]['no_transaction'] = 0;
                                $private_transaction[3]['with_transaction'] = 0;
                                $private_transaction[3]['no_transaction'] = 0;
                                $private_transaction[4]['with_transaction'] = 0;
                                $private_transaction[4]['no_transaction'] = 0;

                                $rhu_transaction[1]['with_transaction'] = 0;
                                $rhu_transaction[1]['no_transaction'] = 0;
                                $rhu_transaction[2]['with_transaction'] = 0;
                                $rhu_transaction[2]['no_transaction'] = 0;
                                $rhu_transaction[3]['with_transaction'] = 0;
                                $rhu_transaction[3]['no_transaction'] = 0;
                                $rhu_transaction[4]['with_transaction'] = 0;
                                $rhu_transaction[4]['no_transaction'] = 0;

                                $birthing_transaction[1]['with_transaction'] = 0;
                                $birthing_transaction[1]['no_transaction'] = 0;
                                $birthing_transaction[2]['with_transaction'] = 0;
                                $birthing_transaction[2]['no_transaction'] = 0;
                                $birthing_transaction[3]['with_transaction'] = 0;
                                $birthing_transaction[3]['no_transaction'] = 0;
                                $birthing_transaction[4]['with_transaction'] = 0;
                                $birthing_transaction[4]['no_transaction'] = 0;

                                $province = [];
                            ?>
                            @foreach($data as $row)
                                <?php
                                    $count++;

                                    $transaction = \App\Activity::where("referred_from",$row->facility_id)->orWhere("referred_to",$row->facility_id)->orderBy("id","desc")->first();

                                    if($row->status == 'onboard'){
                                        $facility_onboard[$row->province_id]++;
                                        $hospital_type[$row->province_id][$row->hospital_type]++;
                                        if($transaction){
                                            $facility_transaction[$row->province_id]['with_transaction']++;
                                            if($row->hospital_type == 'government'){
                                                $government_transaction[$row->province_id]['with_transaction']++;
                                            }elseif($row->hospital_type == 'private'){
                                                $private_transaction[$row->province_id]['with_transaction']++;
                                            }
                                            elseif($row->hospital_type == 'RHU'){
                                                $rhu_transaction[$row->province_id]['with_transaction']++;
                                            }
                                            elseif($row->hospital_type == 'birthing_home'){
                                                $birthing_transaction[$row->province_id]['with_transaction']++;
                                            }
                                        } else {
                                            $facility_transaction[$row->province_id]['no_transaction']++;
                                            if($row->hospital_type == 'government'){
                                                $government_transaction[$row->province_id]['no_transaction']++;
                                            }elseif($row->hospital_type == 'private'){
                                                $private_transaction[$row->province_id]['no_transaction']++;
                                            }
                                            elseif($row->hospital_type == 'RHU'){
                                                $rhu_transaction[$row->province_id]['no_transaction']++;
                                            }
                                            elseif($row->hospital_type == 'birthing_home'){
                                                $birthing_transaction[$row->province_id]['no_transaction']++;
                                            }
                                        }
                                    }

                                    $hospital_type_total[$row->province_id][$row->hospital_type]++;
                                    $facility_total[$row->province_id]++;
                                ?>
                                @if(!isset($province[$row->province]))
                                    <?php $province[$row->province] = true; ?>
                                    <tr>
                                        <td colspan="9">
                                            <div class="row">
                                                <div class="col-lg-3">
                                                    <div id="chartOverall{{ $row->province_id }}" style="height: 200px; width: 100%;"></div>
                                                    <strong class="text-green">Overall - </strong>
                                                    <span class="progress-number"><b class="{{ 'facility_onboard'.$row->province_id }}"></b> <small class="text-blue">(ON BOARD)</small> / <b class="{{ 'facility_total'.$row->province_id }}"></b> <small class="text-blue">(REGISTER)</small></span> = <b class="text-red facility_percent{{ $row->province_id }}"></b>
                                                    <div class="progress sm">
                                                        <div class="progress-bar progress-bar-striped facility_progress{{ $row->province_id }}" ></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div id="chartGovernment{{ $row->province_id }}" style="height: 200px; width: 100%;"></div>
                                                    <strong class="text-green">Government Hospital - {{ $hospital_type[$row->province][$row->hospital_type] }}</strong>
                                                    <span class="progress-number"><b class="{{ 'government_hospital'.$row->province_id }}"></b> <small class="text-blue">(ON BOARD)</small> / <b class="{{ 'government_hospital_total'.$row->province_id }}"></b> <small class="text-blue">(REGISTER)</small></span> = <b class="text-red government_percent{{ $row->province_id }}"></b>
                                                    <div class="progress sm" >
                                                        <div class="progress-bar progress-bar-striped government_hospital_progress{{ $row->province_id }}" ></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div id="chartPrivate{{ $row->province_id }}" style="height: 200px; width: 100%;"></div>
                                                    <strong class="text-green">Private Hospital - </strong>
                                                    <span class="progress-number"><b class="{{ 'private_hospital'.$row->province_id }}"></b> <small class="text-blue">(ON BOARD)</small> / <b class="{{ 'private_hospital_total'.$row->province_id }}"></b> <small class="text-blue">(REGISTER)</small></span> = <b class="text-red private_percent{{ $row->province_id }}"></b>
                                                    <div class="progress sm">
                                                        <div class="progress-bar progress-bar-striped private_hospital_progress{{ $row->province_id }}" ></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div id="chartRhu{{ $row->province_id }}" style="height: 200px; width: 100%;"></div>
                                                    <strong class="text-green">RHU - </strong>
                                                    <span class="progress-number"><b class="{{ 'rhu_hospital'.$row->province_id }}"></b> <small class="text-blue">(ON BOARD)</small> / <b class="{{ 'rhu_hospital_total'.$row->province_id }}"></b> <small class="text-blue">(REGISTER)</small></span> = <b class="text-red rhu_percent{{ $row->province_id }}"></b>
                                                    <div class="progress sm">
                                                        <div class="progress-bar progress-bar-striped rhu_hospital_progress{{ $row->province_id }}" ></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div id="chartBirthing{{ $row->province_id }}" style="height: 200px; width: 100%;"></div>
                                                    <strong class="text-green">BIRTHING HOME - </strong>
                                                    <span class="progress-number"><b class="{{ 'birthing_hospital'.$row->province_id }}"></b> <small class="text-blue">(ON BOARD)</small> / <b class="{{ 'birthing_hospital_total'.$row->province_id }}"></b> <small class="text-blue">(REGISTER)</small></span> = <b class="text-red birthing_percent{{ $row->province_id }}"></b>
                                                    <div class="progress sm">
                                                        <div class="progress-bar progress-bar-striped birthing_hospital_progress{{ $row->province_id }}" ></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <thead class='header'>
                                        <tr class="bg-black">
                                            <th></th>
                                            <th>Facility Name</th>
                                            <th>Chief Hospital</th>
                                            <th>Contact No</th>
                                            <th>Registered On</th>
                                            <th>First Login</th>
                                            <th>Last Login From</th>
                                            <th>Last Logout To</th>
                                            <th>Last Transaction</th>
                                        </tr>
                                    </thead>
                                @endif
                                <tr class="@if($row->status == 'onboard'){{ 'bg-yellow' }}@endif">
                                    <td>{{ $count }}</td>
                                    <td class="@if(!$transaction && $row->status == 'onboard'){{ 'bg-red' }}@endif">
                                        {{ $row->name }}<br>
                                        <span class="{{ $row->hospital_type == 'government' ? 'badge bg-green' : 'badge bg-blue' }}">{{ ucfirst($row->hospital_type) }}</span>
                                    </td>
                                    <td>{{ $row->chief_hospital }}</td>
                                    <td width="10%"><small>{{ $row->contact }}</small></td>
                                    <td >
                                        <small>{{ date("F d,Y",strtotime($row->registered_on)) }}</small><br>
                                        <i>(<small>{{ date("g:i a",strtotime($row->registered_on)) }}</small>)</i>
                                    </td>
                                    <td >
                                        @if($row->first_login == 'not_login')
                                            <small>NOT LOGIN</small>
                                        @else
                                            <small>{{ date("F d,Y",strtotime($row->first_login)) }}</small><br>
                                            <i>(<small>{{ date("g:i a",strtotime($row->first_login)) }}</small>)</i>
                                        @endif
                                    </td>
                                    <td >
                                        @if($row->last_login_from == 'not_login')
                                            <small>NOT LOGIN</small>
                                        @else
                                            <small>{{ date("F d,Y",strtotime($row->last_login_from)) }}</small><br>
                                            <i>(<small>{{ date("g:i a",strtotime($row->last_login_from)) }}</small>)</i>
                                        @endif
                                    </td>
                                    <td >
                                        @if($row->last_logout_to == 'not_login')
                                            <small>NOT LOGOUT</small>
                                        @elseif($row->last_logout_to == '0000-00-00 00:00:00')
                                            <small>{{ date("F d,Y",strtotime(explode(" ",$row->last_login_from)[0]." 23:59:59")) }}</small><br>
                                            <i>(<small>{{ date("g:i a",strtotime(explode(" ",$row->last_login_from)[0]." 23:59:59")) }}</small>)</i>
                                        @else
                                            <small>{{ date("F d,Y",strtotime($row->last_logout_to)) }}</small><br>
                                            <i>(<small>{{ date("g:i a",strtotime($row->last_logout_to)) }}</small>)</i>
                                        @endif
                                    </td>
                                    <td >
                                        @if($transaction)
                                            <small>{{ date("F d,Y",strtotime($transaction->created_at)) }}</small><br>
                                            <i>(<small>{{ date("g:i a",strtotime($transaction->created_a)) }}</small>)</i>
                                            <span class="badge bg-green">{{ ucfirst($transaction->status) }}</span>
                                        @else
                                            <small class="badge bg-red">NO TRANSACTION</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
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
@endsection


@section('css')

@endsection

@section('js')
    <script type="text/javascript">
        //Date range picker
        $('#consolidate_date_range').daterangepicker();
        $(document).ready(function(){
            $('.table-fixed-header').fixedHeader();
        });

        window.onload = function() {
            CanvasJS.addColorSet("greenShades",
                [//colorSet Array
                    "#6762ff",
                    "#ff686b"
                ]);

            var with_transaction1 = Math.round("<?php echo $facility_transaction[$data[0]->province_id]['with_transaction']; ?>" / "<?php echo $facility_onboard[$data[0]->province_id]; ?>" * 100);
            var no_transaction1 = Math.round("<?php echo $facility_transaction[$data[0]->province_id]['no_transaction']; ?>" / "<?php echo $facility_onboard[$data[0]->province_id]; ?>" * 100);
            var options1 = {
                colorSet: "greenShades",
                exportEnabled: true,
                animationEnabled: true,
                title: {
                    text: "Overall"
                },
                data: [{
                    type: "pie",
                    startAngle: 45,
                    showInLegend: "true",
                    toolTipContent: "{y}%",
                    legendText: "{label}",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: [
                        { label: "With Transaction in "+with_transaction1+"% in "+"<?php echo $facility_transaction[$data[0]->province_id]['with_transaction']; ?> out of <?php echo $facility_onboard[$data[0]->province_id]; ?>",legendText : "With Transaction", y: with_transaction1 },
                        { label: "No Transaction in "+no_transaction1+"% in "+"<?php echo $facility_transaction[$data[0]->province_id]['no_transaction']; ?> out of <?php echo $facility_onboard[$data[0]->province_id]; ?>",legendText : "No Transaction", y: no_transaction1 }
                    ]
                }]
            };
            $("#chartOverall{{ $data[0]->province_id }}").CanvasJSChart(options1);


            @if($hospital_type[$data[0]->province_id]['government'] != 0)
            var government_with_transaction1 = Math.round("<?php echo $government_transaction[$data[0]->province_id]['with_transaction']; ?>" / "<?php echo $hospital_type[$data[0]->province_id]['government']; ?>" * 100);
            var government_no_transaction1 = Math.round("<?php echo $government_transaction[$data[0]->province_id]['no_transaction']; ?>" / "<?php echo $hospital_type[$data[0]->province_id]['government']; ?>" * 100);
            var government_options1 = {
                colorSet: "greenShades",
                exportEnabled: true,
                animationEnabled: true,
                title: {
                    text: "Government"
                },
                data: [{
                    type: "pie",
                    startAngle: 45,
                    showInLegend: "true",
                    toolTipContent: "{y}%",
                    legendText: "{label}",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: [
                        { label: "With Transaction in "+government_with_transaction1+"% in "+"<?php echo $government_transaction[$data[0]->province_id]['with_transaction']; ?> out of <?php echo $hospital_type[$data[0]->province_id]['government']; ?>",legendText : "With Transaction", y: government_with_transaction1 },
                        { label: "No Transaction in "+government_no_transaction1+"% in "+"<?php echo $government_transaction[$data[0]->province_id]['no_transaction']; ?> out of <?php echo $hospital_type[$data[0]->province_id]['government'] ?>",legendText : "No Transaction", y: government_no_transaction1 }
                    ]
                }]
            };
            $("#chartGovernment{{ $data[0]->province_id }}").CanvasJSChart(government_options1);
            @endif

            @if($hospital_type[$data[0]->province_id]['private'] != 0)
            var private_with_transaction1 = Math.round("<?php echo $private_transaction[$data[0]->province_id]['with_transaction']; ?>" / "<?php echo $hospital_type[$data[0]->province_id]['private']; ?>" * 100);
            var private_no_transaction1 = Math.round("<?php echo $private_transaction[$data[0]->province_id]['no_transaction']; ?>" / "<?php echo $hospital_type[$data[0]->province_id]['private']; ?>" * 100);
            var private_options1 = {
                colorSet: "greenShades",
                exportEnabled: true,
                animationEnabled: true,
                title: {
                    text: "Private"
                },
                data: [{
                    type: "pie",
                    startAngle: 45,
                    showInLegend: "true",
                    toolTipContent: "{y}%",
                    legendText: "{label}",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: [
                        { label: "With Transaction in "+private_with_transaction1+"% in "+"<?php echo $private_transaction[$data[0]->province_id]['with_transaction']; ?> out of <?php echo $hospital_type[$data[0]->province_id]['private']; ?>",legendText : "With Transaction", y: private_with_transaction1 },
                        { label: "No Transaction in "+private_no_transaction1+"% in "+"<?php echo $private_transaction[$data[0]->province_id]['no_transaction']; ?> out of <?php echo $hospital_type[$data[0]->province_id]['private'] ?>",legendText : "No Transaction", y: private_no_transaction1 }
                    ]
                }]
            };
            $("#chartPrivate{{ $data[0]->province_id }}").CanvasJSChart(private_options1);
            @endif

            @if($hospital_type[$data[0]->province_id]['RHU'] != 0)
            var rhu_with_transaction1 = Math.round("<?php echo $rhu_transaction[$data[0]->province_id]['with_transaction']; ?>" / "<?php echo $hospital_type[$data[0]->province_id]['RHU']; ?>" * 100);
            var rhu_no_transaction1 = Math.round("<?php echo $rhu_transaction[$data[0]->province_id]['no_transaction']; ?>" / "<?php echo $hospital_type[$data[0]->province_id]['RHU']; ?>" * 100);
            var rhu_options1 = {
                colorSet: "greenShades",
                exportEnabled: true,
                animationEnabled: true,
                title: {
                    text: "RHU"
                },
                data: [{
                    type: "pie",
                    startAngle: 45,
                    showInLegend: "true",
                    toolTipContent: "{y}%",
                    legendText: "{label}",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: [
                        { label: "With Transaction in "+rhu_with_transaction1+"% in "+"<?php echo $rhu_transaction[$data[0]->province_id]['with_transaction']; ?> out of <?php echo $hospital_type[$data[0]->province_id]['RHU']; ?>",legendText : "With Transaction", y: rhu_with_transaction1 },
                        { label: "No Transaction in "+rhu_no_transaction1+"% in "+"<?php echo $rhu_transaction[$data[0]->province_id]['no_transaction']; ?> out of <?php echo $hospital_type[$data[0]->province_id]['RHU'] ?>",legendText : "No Transaction", y: rhu_no_transaction1 }
                    ]
                }]
            };
            $("#chartRhu{{ $data[0]->province_id }}").CanvasJSChart(rhu_options1);
            @endif

            @if($hospital_type[$data[0]->province_id]['birthing_home'] != 0)
            var birthing_with_transaction1 = Math.round("<?php echo $birthing_transaction[$data[0]->province_id]['with_transaction']; ?>" / "<?php echo $hospital_type[$data[0]->province_id]['birthing_home']; ?>" * 100);
            var birthing_no_transaction1 = Math.round("<?php echo $birthing_transaction[$data[0]->province_id]['no_transaction']; ?>" / "<?php echo $hospital_type[$data[0]->province_id]['birthing_home']; ?>" * 100);
            var birthing_options1 = {
                colorSet: "greenShades",
                exportEnabled: true,
                animationEnabled: true,
                title: {
                    text: "BIRTHING HOME"
                },
                data: [{
                    type: "pie",
                    startAngle: 45,
                    showInLegend: "true",
                    toolTipContent: "{y}%",
                    legendText: "{label}",
                    yValueFormatString:"#,##0.#"%"",
                    dataPoints: [
                        { label: "With Transaction in "+birthing_with_transaction1+"% in "+"<?php echo $birthing_transaction[$data[0]->province_id]['with_transaction']; ?> out of <?php echo $hospital_type[$data[0]->province_id]['birthing_home']; ?>",legendText : "With Transaction", y: birthing_with_transaction1 },
                        { label: "No Transaction in "+birthing_no_transaction1+"% in "+"<?php echo $birthing_transaction[$data[0]->province_id]['no_transaction']; ?> out of <?php echo $hospital_type[$data[0]->province_id]['birthing_home'] ?>",legendText : "No Transaction", y: birthing_no_transaction1 }
                    ]
                }]
            };
            $("#chartBirthing{{ $data[0]->province_id }}").CanvasJSChart(birthing_options1);
            @endif

        }
    </script>

    <script>
        //Date range picker
        $('#onboard_picker').daterangepicker({
            "singleDatePicker": true
        });

        $(".facility_onboard{{ $data[0]->province_id }}").html("<?php echo $facility_onboard[$data[0]->province_id] ?>");
        $(".facility_total{{ $data[0]->province_id }}").html("<?php echo $facility_total[$data[0]->province_id] ?>");
        var facility_progress1 = Math.round("<?php echo $facility_onboard[$data[0]->province_id] ?>" / "<?php echo $facility_total[$data[0]->province_id] ?>" * 100);
        $('.facility_progress{{ $data[0]->province_id }}').css('width',facility_progress1+"%");
        $('.facility_percent{{ $data[0]->province_id }}').html(facility_progress1+"%");

        /*$(".facility_onboard2").html("<?php echo $facility_onboard[2] ?>");
        $(".facility_total2").html("<?php echo $facility_total[2] ?>");
        var facility_progress2 = Math.round("<?php echo $facility_onboard[2] ?>" / "<?php echo $facility_total[2] ?>" * 100);
        $('.facility_progress2').css('width',facility_progress2+"%");
        $('.facility_percent2').html(facility_progress2+"%");

        $(".facility_onboard3").html("<?php echo $facility_onboard[3] ?>");
        $(".facility_total3").html("<?php echo $facility_total[3] ?>");
        var facility_progress3 = Math.round("<?php echo $facility_onboard[3] ?>" / "<?php echo $facility_total[3] ?>" * 100);
        $('.facility_progress3').css('width',facility_progress3+"%");
        $('.facility_percent3').html(facility_progress3+"%");

        $(".facility_onboard4").html("<?php echo $facility_onboard[4] ?>");
        $(".facility_total4").html("<?php echo $facility_total[4] ?>");
        var facility_progress4 = Math.round("<?php echo $facility_onboard[4] ?>" / "<?php echo $facility_total[4] ?>" * 100);
        $('.facility_progress4').css('width',facility_progress4+"%");
        $('.facility_percent4').html(facility_progress4+"%");
        */

        $(".government_hospital1").html("<?php echo $hospital_type[1]['government']; ?>");
        $(".government_hospital_total1").html("<?php echo $hospital_type_total[1]['government']; ?>");
        var government_hospital_progress1 = Math.round("<?php echo $hospital_type[1]['government'] ?>" / "<?php echo $hospital_type_total[1]['government'] ?>" * 100);
        $('.government_hospital_progress1').css('width',government_hospital_progress1+"%");
        $('.government_percent1').html(government_hospital_progress1+"%");


        $(".government_hospital2").html("<?php echo $hospital_type[2]['government']; ?>");
        $(".government_hospital_total2").html("<?php echo $hospital_type_total[2]['government']; ?>");
        var government_hospital_progress2 = Math.round("<?php echo $hospital_type[2]['government'] ?>" / "<?php echo $hospital_type_total[2]['government'] ?>" * 100);
        $('.government_hospital_progress2').css('width',government_hospital_progress2+"%");
        $('.government_percent2').html(government_hospital_progress2+"%");

        $(".government_hospital3").html("<?php echo $hospital_type[3]['government']; ?>");
        $(".government_hospital_total3").html("<?php echo $hospital_type_total[3]['government']; ?>");
        var government_hospital_progress3 = Math.round("<?php echo $hospital_type[3]['government'] ?>" / "<?php echo $hospital_type_total[3]['government'] ?>" * 100);
        $('.government_hospital_progress3').css('width',government_hospital_progress3+"%");
        $('.government_percent3').html(government_hospital_progress3+"%");

        $(".government_hospital4").html("<?php echo $hospital_type[4]['government']; ?>");
        $(".government_hospital_total4").html("<?php echo $hospital_type_total[4]['government']; ?>");
        var government_hospital_progress4 = Math.round("<?php echo $hospital_type[4]['government'] ?>" / "<?php echo $hospital_type_total[4]['government'] ?>" * 100);
        $('.government_hospital_progress4').css('width',government_hospital_progress4+"%");
        $('.government_percent4').html(government_hospital_progress4+"%");

        $(".private_hospital1").html("<?php echo $hospital_type[1]['private']; ?>");
        $(".private_hospital_total1").html("<?php echo $hospital_type_total[1]['private']; ?>");
        var private_hospital_progress1 = Math.round("<?php echo $hospital_type[1]['private'] ?>" / "<?php echo $hospital_type_total[1]['private'] ?>" * 100);
        $('.private_hospital_progress1').css('width',private_hospital_progress1+"%");
        $('.private_percent1').html(private_hospital_progress1+"%");

        $(".private_hospital2").html("<?php echo $hospital_type[2]['private']; ?>");
        $(".private_hospital_total2").html("<?php echo $hospital_type_total[2]['private']; ?>");
        var private_hospital_progress2 = Math.round("<?php echo $hospital_type[2]['private'] ?>" / "<?php echo $hospital_type_total[2]['private'] ?>" * 100);
        $('.private_hospital_progress2').css('width',private_hospital_progress2+"%");
        $('.private_percent2').html(private_hospital_progress2+"%");

        $(".private_hospital3").html("<?php echo $hospital_type[3]['private']; ?>");
        $(".private_hospital_total3").html("<?php echo $hospital_type_total[3]['private']; ?>");
        var private_hospital_progress3 = Math.round("<?php echo $hospital_type[3]['private'] ?>" / "<?php echo $hospital_type_total[3]['private'] ?>" * 100);
        $('.private_hospital_progress3').css('width',private_hospital_progress3+"%");
        $('.private_percent3').html(private_hospital_progress3+"%");

        $(".private_hospital4").html("<?php echo $hospital_type[4]['private']; ?>");
        $(".private_hospital_total4").html("<?php echo $hospital_type_total[4]['private']; ?>");
        var private_hospital_progress4 = Math.round("<?php echo $hospital_type[4]['private'] ?>" / "<?php echo $hospital_type_total[4]['private'] ?>" * 100);
        $('.private_hospital_progress4').css('width',private_hospital_progress4+"%");
        $('.private_percent4').html(private_hospital_progress4+"%");

        $(".rhu_hospital1").html("<?php echo $hospital_type[1]['RHU']; ?>");
        $(".rhu_hospital_total1").html("<?php echo $hospital_type_total[1]['RHU']; ?>");
        var rhu_hospital_progress1 = Math.round("<?php echo $hospital_type[1]['RHU'] ?>" / "<?php echo $hospital_type_total[1]['RHU'] ?>" * 100);
        $('.rhu_hospital_progress1').css('width',rhu_hospital_progress1+"%");
        $('.rhu_percent1').html(rhu_hospital_progress1+"%");

        $(".rhu_hospital2").html("<?php echo $hospital_type[2]['RHU']; ?>");
        $(".rhu_hospital_total2").html("<?php echo $hospital_type_total[2]['RHU']; ?>");
        var rhu_hospital_progress2 = Math.round("<?php echo $hospital_type[2]['RHU'] ?>" / "<?php echo $hospital_type_total[2]['RHU'] ?>" * 100);
        $('.rhu_hospital_progress2').css('width',rhu_hospital_progress2+"%");
        $('.rhu_percent2').html(rhu_hospital_progress2+"%");

        $(".rhu_hospital3").html("<?php echo $hospital_type[3]['RHU']; ?>");
        $(".rhu_hospital_total3").html("<?php echo $hospital_type_total[3]['RHU']; ?>");
        var rhu_hospital_progress3 = Math.round("<?php echo $hospital_type[3]['RHU'] ?>" / "<?php echo $hospital_type_total[3]['RHU'] ?>" * 100);
        $('.rhu_hospital_progress3').css('width',rhu_hospital_progress3+"%");
        $('.rhu_percent3').html(rhu_hospital_progress3+"%");


        $(".rhu_hospital4").html("<?php echo $hospital_type[4]['RHU']; ?>");
        $(".rhu_hospital_total4").html("<?php echo $hospital_type_total[4]['RHU']; ?>");
        var rhu_hospital_progress4 = Math.round("<?php echo $hospital_type[4]['RHU'] ?>" / "<?php echo $hospital_type_total[4]['RHU'] ?>" * 100);
        $('.rhu_hospital_progress4').css('width',rhu_hospital_progress4+"%");
        $('.rhu_percent4').html(rhu_hospital_progress4+"%");


        $(".birthing_hospital1").html("<?php echo $hospital_type[1]['birthing_home']; ?>");
        $(".birthing_hospital_total1").html("<?php echo $hospital_type_total[1]['birthing_home']; ?>");
        var birthing_hospital_progress1 = Math.round("<?php echo $hospital_type[1]['birthing_home'] ?>" / "<?php echo $hospital_type_total[1]['birthing_home'] ?>" * 100);
        $('.birthing_hospital_progress1').css('width',birthing_hospital_progress1+"%");
        $('.birthing_percent1').html(birthing_hospital_progress1+"%");

        $(".birthing_hospital2").html("<?php echo $hospital_type[2]['birthing_home']; ?>");
        $(".birthing_hospital_total2").html("<?php echo $hospital_type_total[2]['birthing_home']; ?>");
        var birthing_hospital_progress2 = Math.round("<?php echo $hospital_type[2]['birthing_home'] ?>" / "<?php echo $hospital_type_total[2]['birthing_home'] ?>" * 100);
        $('.birthing_hospital_progress2').css('width',birthing_hospital_progress2+"%");
        $('.birthing_percent2').html(birthing_hospital_progress2+"%");

        $(".birthing_hospital3").html("<?php echo $hospital_type[3]['birthing_home']; ?>");
        $(".birthing_hospital_total3").html("<?php echo $hospital_type_total[3]['birthing_home']; ?>");
        var birthing_hospital_progress3 = Math.round("<?php echo $hospital_type[3]['birthing_home'] ?>" / "<?php echo $hospital_type_total[3]['birthing_home'] ?>" * 100);
        $('.birthing_hospital_progress3').css('width',birthing_hospital_progress3+"%");
        $('.birthing_percent3').html(birthing_hospital_progress3+"%");


        $(".birthing_hospital4").html("<?php echo $hospital_type[4]['birthing_home']; ?>");
        $(".birthing_hospital_total4").html("<?php echo $hospital_type_total[4]['birthing_home']; ?>");
        var birthing_hospital_progress4 = Math.round("<?php echo $hospital_type[4]['birthing_home'] ?>" / "<?php echo $hospital_type_total[4]['birthing_home'] ?>" * 100);
        $('.birthing_hospital_progress4').css('width',birthing_hospital_progress4+"%");
        $('.birthing_percent4').html(birthing_hospital_progress4+"%");

    </script>
@endsection

