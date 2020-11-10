@extends('layouts.app')

@section('content')
    <div class="box box-success">
        <div class="box-body">
            <div class="box-header with-border">
                <h3>
                    Referral that not accepted within 30 minutes as
                </h3>
                <span class="text-blue">({{ date("F d,Y H:i:s",strtotime($date_start)) }} to {{ date("F d,Y H:i:s",strtotime($date_end)) }})</span><br><br>
                <form action="{{ asset('monitoring') }}" method="POST" class="form-inline">
                    {{ csrf_field() }}
                    <div class="form-group-sm">
                        <input type="text" class="form-control active" name="date_range" value="{{ date("m/d/Y",strtotime($date_start)).' - '.date("m/d/Y",strtotime($date_end)) }}" placeholder="Filter your daterange here..." id="consolidate_date_range" autocomplete="off">
                        <button type="submit" class="btn-sm btn-info btn-flat"><i class="fa fa-search"></i> Filter</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-responsive">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Referring Facility</th>
                        <th>Referred To</th>
                        <th>Date Referred</th>
                        <th>Turn around time not accepted</th>
                        <th>Remarks</th>
                    </tr>
                    <?php $count=0; ?>
                    @foreach($pending_activity as $row)
                        <?php $count++; ?>
                        <tr>
                            <td width="2%" style="vertical-align:top;" class="align-baseline"><span class="text-yellow">{{ $count }}</span></td>
                            <td width="5%" style="vertical-align:top">
                                <a href="{{ asset('doctor/track/patient?referredCode=').$row->code }}" class="btn btn-sm btn-success" target="_blank">
                                    <i class="fa fa-stethoscope"></i> Track
                                </a>
                            </td>
                            <td width="23%;">
                                {{ $row->referring_facility }}<br>
                                <span class="text-green">{{ $row->contact_from }}</span>
                            </td>
                            <td width="23%;">
                                {{ $row->referred_to }}<br>
                                <span class="text-green">{{ $row->contact_to }}</span>
                            </td>
                            <td width="13%">
                                {{ date("F d,Y",strtotime($row->date_referred)) }}<br>
                                <small class="text-yellow">({{ date('g:i a',strtotime($row->date_referred)) }})</small>
                            </td>
                            <td><span class="text-green" style="font-size: 15pt;">{{ $row->time_not_accepted }}</span><br> minutes</td>
                            <td>
                                <?php
                                    $monitoring_not_accepted = \App\MonitoringNotAccepted::where("code","=",$row->code)->get();
                                ?>
                                @if(count($monitoring_not_accepted) > 0)
                                    @foreach($monitoring_not_accepted as $monitoring)
                                        <span class="text-red">={{ $monitoring->remarks }}</span><br><br>
                                    @endforeach
                                    @if(Session::get('auth')->level == 'opcen')
                                        <button class="btn btn-sm btn-primary" href="#add_remark" data-toggle="modal" onclick="addRemark(
                                                '<?php echo $row->activity_id; ?>',
                                                '<?php echo $row->code; ?>',
                                                '<?php echo $row->referring_facility_id ?>',
                                                '<?php echo $row->referred_to_id ?>'
                                                )"
                                        ><i class="fa fa-sticky-note"></i> Add more remark</button>
                                    @endif
                                @else
                                    @if(Session::get('auth')->level == 'opcen')
                                            <button class="btn btn-sm btn-primary" href="#add_remark" data-toggle="modal" onclick="addRemark(
                                                    '<?php echo $row->activity_id; ?>',
                                                    '<?php echo $row->code; ?>',
                                                    '<?php echo $row->referring_facility_id ?>',
                                                    '<?php echo $row->referred_to_id ?>'
                                                    )"
                                            ><i class="fa fa-sticky-note"></i> Add remark</button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="add_remark">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body monitoring_remark">

                </div><!-- /.modal-content -->
            </div>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


@endsection

@section('js')
    <script>
        //Date range picker
        $('#consolidate_date_range').daterangepicker();

        @if(Session::get('add_remark'))
        Lobibox.notify('success', {
            title: "",
            msg: "Successfully added ramark!",
            size: 'mini',
            rounded: true
        });
        <?php Session::put("add_remark",false); ?>
        @endif

        function addRemark(activity_id,code,referring_facility,referred_to){
            $(".monitoring_remark").html(loading);
            var json = {
                "_token" : "<?php echo csrf_token()?>",
                "activity_id" : activity_id,
                "code" : code,
                "referring_facility" : referring_facility,
                "referred_to" : referred_to
            };
            console.log(json);
            var url = "<?php echo asset('monitoring/remark') ?>";
            $.post(url,json,function(result){
                setTimeout(function(){
                    $(".monitoring_remark").html(result);
                },500);
            })
        }
    </script>
@endsection

