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
                    <li><b class="text-yellow">YELLOW - ON BOARD WITH TRANSACTION</b></li>
                    <li><b class="text-red">RED - ON BOARD BUT NO TRANSACTION</b></li>
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
                                <th>Hospital Type</th>
                            </tr>
                            <?php
                                $count = 0;
                                $facility_onboard[1] = 0;
                                $facility_total[1] = 0;
                                $facility_onboard[2] = 0;
                                $facility_total[2] = 0;
                                $province = [];
                            ?>
                            @foreach($data as $row)
                                <?php
                                    $count++;

                                    if($row->status == 'onboard')
                                        $facility_onboard[$row->province_id]++;

                                    $facility_total[$row->province_id]++;
                                ?>
                                @if(!isset($province[$row->province]))
                                    <?php $province[$row->province] = true; ?>
                                    <tr>
                                        <td colspan="5">
                                            <div class="form-group">
                                                <strong class="text-green">{{ $row->province }} - </strong>
                                                <span class="progress-number"><b class="{{ 'facility_onboard'.$row->province_id }}"></b> <small class="text-blue">(ON BOARD)</small> / <b class="{{ 'facility_total'.$row->province_id }}"></b> <small class="text-blue">(REGISTER)</small></span>
                                                <div class="progress sm">
                                                    <div class="progress-bar progress-bar-red facility_progress{{ $row->province_id }}" ></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                <tr class="@if($row->status == 'onboard'){{ 'bg-yellow' }}@endif">
                                    <td>{{ $count }}</td>
                                    <td class="@if($row->transaction == 'no_transaction' && $row->status == 'onboard'){{ 'bg-red' }}@endif">{{ $row->name }}</td>
                                    <td>{{ $row->chief_hospital }}</td>
                                    <td width="10%">{{ $row->contact }}</td>
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
    <script>
        //Date range picker
        $('#onboard_picker').daterangepicker({
            "singleDatePicker": true
        });
        $(".facility_onboard1").html("<?php echo $facility_onboard[1] ?>");
        $(".facility_total1").html("<?php echo $facility_total[1] ?>");
        $(".facility_onboard2").html("<?php echo $facility_onboard[2] ?>");
        $(".facility_total2").html("<?php echo $facility_total[2] ?>");

        var facility_progress1 = Math.round("<?php echo $facility_onboard[1] ?>" / "<?php echo $facility_total[1] ?>" * 100);
        var facility_progress2 = Math.round("<?php echo $facility_onboard[2] ?>" / "<?php echo $facility_total[2] ?>" * 100);


        $('.facility_progress1').css('width',facility_progress1+"%");
        $('.facility_progress2').css('width',facility_progress2+"%");

    </script>
@endsection

