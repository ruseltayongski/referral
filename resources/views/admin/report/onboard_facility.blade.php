@extends('layouts.app')

@section('content')
    <style>
        label {
            padding: 0px !important;
        }
    </style>
    <div class="row col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <strong style="font-size: 15pt">LEGEND:</strong>
                <ul>
                    <li><span class="text-yellow">YELLOW - ON BOARD WITH TRANSACTION</span></li>
                    <li><span class="text-red">RED - ON BOARD BUT NO TRANSACTION</span></li>
                </ul>
            </div>
            <div class="box-body">
                @if(count($data) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <tr class="bg-black">
                                <th></th>
                                <th>Facility Name</th>
                                <th>Chief Hospital</th>
                                <th>Contact No</th>
                                <th>Register At</th>
                                <th>Login At</th>
                                <th>Hospital Type</th>
                            </tr>
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

                                $hospital_type[1]['government'] = 0;
                                $hospital_type_total[1]['government'] = 0;
                                $government_transaction[1]['with_transaction'] = 0;
                                $government_transaction[1]['no_transaction'] = 0;

                                $hospital_type_hospital[2]['government'] = 0;
                                $hospital_type_total[2]['government'] = 0;
                                $government_transaction[2]['with_transaction'] = 0;
                                $government_transaction[2]['no_transaction'] = 0;

                                $private_transaction[1]['with_transaction'] = 0;
                                $private_transaction[1]['no_transaction'] = 0;
                                $private_transaction[2]['with_transaction'] = 0;
                                $private_transaction[2]['no_transaction'] = 0;

                                $province = [];
                            ?>
                            @foreach($data as $row)
                                <?php
                                    $count++;

                                    if($row->status == 'onboard'){
                                        $facility_onboard[$row->province_id]++;
                                        $hospital_type[$row->province_id][$row->hospital_type]++;
                                        if($row->transaction == 'transaction'){
                                            $facility_transaction[$row->province_id]['with_transaction']++;
                                            if($row->hospital_type == 'government'){
                                                $government_transaction[$row->province_id]['with_transaction']++;
                                            }elseif($row->hospital_type == 'private'){
                                                $private_transaction[$row->province_id]['with_transaction']++;
                                            }
                                        } else {
                                            $facility_transaction[$row->province_id]['no_transaction']++;
                                            if($row->hospital_type == 'government'){
                                                $government_transaction[$row->province_id]['no_transaction']++;
                                            }elseif($row->hospital_type == 'private'){
                                                $private_transaction[$row->province_id]['no_transaction']++;
                                            }
                                        }
                                    }

                                    $hospital_type_total[$row->province_id][$row->hospital_type]++;
                                    $facility_total[$row->province_id]++;
                                ?>
                                @if(!isset($province[$row->province]))
                                    <?php $province[$row->province] = true; ?>
                                    <tr>
                                        <td colspan="7">
                                            <div class="form-group">
                                                <b style="color: #ff298e;font-size: 17pt;">{{ $row->province }}</b><br>
                                                <div id="chartOverall{{ $row->province_id }}" style="height: 200px; width: 100%;"></div>
                                                <strong class="text-green">Overall - </strong>
                                                <span class="progress-number"><b class="{{ 'facility_onboard'.$row->province_id }}"></b> <small class="text-blue">(ON BOARD)</small> / <b class="{{ 'facility_total'.$row->province_id }}"></b> <small class="text-blue">(REGISTER)</small></span> = <b class="text-red facility_percent{{ $row->province_id }}"></b>
                                                <div class="progress sm">
                                                    <div class="progress-bar progress-bar-striped facility_progress{{ $row->province_id }}" ></div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div id="chartGovernment{{ $row->province_id }}" style="height: 200px; width: 100%;"></div>
                                                <strong class="text-green">Government Hospital - {{ $hospital_type[$row->province][$row->hospital_type] }}</strong>
                                                <span class="progress-number"><b class="{{ 'government_hospital'.$row->province_id }}"></b> <small class="text-blue">(ON BOARD)</small> / <b class="{{ 'government_hospital_total'.$row->province_id }}"></b> <small class="text-blue">(REGISTER)</small></span> = <b class="text-red government_percent{{ $row->province_id }}"></b>
                                                <div class="progress sm" >
                                                    <div class="progress-bar progress-bar-striped government_hospital_progress{{ $row->province_id }}" ></div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <strong class="text-green">Private Hospital - </strong>
                                                <span class="progress-number"><b class="{{ 'private_hospital'.$row->province_id }}"></b> <small class="text-blue">(ON BOARD)</small> / <b class="{{ 'private_hospital_total'.$row->province_id }}"></b> <small class="text-blue">(REGISTER)</small></span> = <b class="text-red private_percent{{ $row->province_id }}"></b>
                                                <div class="progress sm">
                                                    <div class="progress-bar progress-bar-red private_hospital_progress{{ $row->province_id }}" ></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                <tr class="@if($row->status == 'onboard'){{ 'bg-yellow' }}@endif">
                                    <td>{{ $count }}</td>
                                    <td class="@if($row->transaction == 'no_transaction' && $row->status == 'onboard'){{ 'bg-red' }}@endif">{{ $row->name }}</td>
                                    <td>{{ $row->chief_hospital }}</td>
                                    <td width="10%"><small>{{ $row->contact }}</small></td>
                                    <td >
                                        <small>{{ date("F d,Y",strtotime($row->register_at)) }}</small><br>
                                        <i>(<small>{{ date("g:i a",strtotime($row->register_at)) }}</small>)</i>
                                    </td>
                                    <td >
                                        @if($row->login_at == 'not_login')
                                            <small>NOT LOGIN</small>
                                        @else
                                            <small>{{ date("F d,Y",strtotime($row->login_at)) }}</small><br>
                                            <i>(<small>{{ date("g:i a",strtotime($row->login_at)) }}</small>)</i>
                                        @endif
                                    </td>
                                    <td><span class="{{ $row->hospital_type == 'government' ? 'badge bg-green' : 'badge bg-blue' }}">{{ ucfirst($row->hospital_type) }}</span></td>
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
        window.onload = function() {
            CanvasJS.addColorSet("greenShades",
                [//colorSet Array
                    "#6762ff",
                    "#ff686b"
                ]);

            var with_transaction1 = Math.round("<?php echo $facility_transaction[1]['with_transaction']; ?>" / "<?php echo $facility_onboard[1]; ?>" * 100);
            var no_transaction1 = Math.round("<?php echo $facility_transaction[1]['no_transaction']; ?>" / "<?php echo $facility_onboard[1]; ?>" * 100);
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
                        { label: "With Transaction in "+with_transaction1+"% in "+"<?php echo $facility_transaction[1]['with_transaction']; ?> out of <?php echo $facility_onboard[1]; ?>",legendText : "With Transaction", y: with_transaction1 },
                        { label: "No Transaction in "+no_transaction1+"% in "+"<?php echo $facility_transaction[1]['no_transaction']; ?> out of <?php echo $facility_onboard[1]; ?>",legendText : "No Transaction", y: no_transaction1 }
                    ]
                }]
            };
            $("#chartOverall1").CanvasJSChart(options1);


            var with_transaction2 = Math.round("<?php echo $facility_transaction[2]['with_transaction']; ?>" / "<?php echo $facility_onboard[2]; ?>" * 100);
            var no_transaction2 = Math.round("<?php echo $facility_transaction[2]['no_transaction']; ?>" / "<?php echo $facility_onboard[2]; ?>" * 100);
            var options2 = {
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
                        { label: "With Transaction in "+with_transaction2+"% in "+"<?php echo $facility_transaction[2]['with_transaction']; ?> out of <?php echo $facility_onboard[2]; ?>",legendText : "With Transaction", y: with_transaction2 },
                        { label: "No Transaction in "+no_transaction2+"% in "+"<?php echo $facility_transaction[2]['no_transaction']; ?> out of <?php echo $facility_onboard[2]; ?>",legendText : "No Transaction", y: no_transaction2 }
                    ]
                }]
            };
            $("#chartOverall2").CanvasJSChart(options2);


            @if($hospital_type[1]['government'] != 0)
            var government_with_transaction1 = Math.round("<?php echo $government_transaction[1]['with_transaction']; ?>" / "<?php echo $hospital_type[1]['government']; ?>" * 100);
            var government_no_transaction1 = Math.round("<?php echo $government_transaction[1]['no_transaction']; ?>" / "<?php echo $hospital_type[1]['government']; ?>" * 100);
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
                        { label: "With Transaction in "+government_with_transaction1+"% in "+"<?php echo $government_transaction[1]['with_transaction']; ?> out of <?php echo $hospital_type[1]['government']; ?>",legendText : "With Transaction", y: government_with_transaction1 },
                        { label: "No Transaction in "+government_no_transaction1+"% in "+"<?php echo $government_transaction[1]['no_transaction']; ?> out of <?php echo $hospital_type[1]['government'] ?>",legendText : "No Transaction", y: government_no_transaction1 }
                    ]
                }]
            };
            $("#chartGovernment1").CanvasJSChart(government_options1);
            @endif

            @if($hospital_type[2]['government'] != 0)
            var government_with_transaction2 = Math.round("<?php echo $government_transaction[2]['with_transaction']; ?>" / "<?php echo $hospital_type[2]['government']; ?>" * 100);
            var government_no_transaction2 = Math.round("<?php echo $government_transaction[2]['no_transaction']; ?>" / "<?php echo $hospital_type[2]['government']; ?>" * 100);
            var government_options2 = {
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
                        { label: "With Transaction in "+government_with_transaction2+"% in "+"<?php echo $government_transaction[2]['with_transaction']; ?> out of <?php echo $hospital_type[2]['government']; ?>",legendText : "With Transaction", y: government_with_transaction2 },
                        { label: "No Transaction in "+government_no_transaction2+"% in "+"<?php echo $government_transaction[2]['no_transaction']; ?> out of <?php echo $hospital_type[2]['government'] ?>",legendText : "No Transaction", y: government_no_transaction2 }
                    ]
                }]
            };
            $("#chartGovernment2").CanvasJSChart(government_options2);
            @endif

        }
    </script>

    <script>
        //Date range picker
        $('#onboard_picker').daterangepicker({
            "singleDatePicker": true
        });

        $(".facility_onboard1").html("<?php echo $facility_onboard[1] ?>");
        $(".facility_total1").html("<?php echo $facility_total[1] ?>");
        var facility_progress1 = Math.round("<?php echo $facility_onboard[1] ?>" / "<?php echo $facility_total[1] ?>" * 100);
        $('.facility_progress1').css('width',facility_progress1+"%");
        $('.facility_percent1').html(facility_progress1+"%");


        $(".facility_onboard2").html("<?php echo $facility_onboard[2] ?>");
        $(".facility_total2").html("<?php echo $facility_total[2] ?>");
        var facility_progress2 = Math.round("<?php echo $facility_onboard[2] ?>" / "<?php echo $facility_total[2] ?>" * 100);
        $('.facility_progress2').css('width',facility_progress2+"%");
        $('.facility_percent2').html(facility_progress2+"%");

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

    </script>
@endsection

