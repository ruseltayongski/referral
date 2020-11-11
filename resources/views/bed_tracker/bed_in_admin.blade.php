@extends('layouts.app')

@section('content')
    <style>
        .editable-empty{
            color: #ff3d3c;
        }
        /*thead {
            position: fixed;
            top: 50px;
            width: 100%;
            height: 50px;
            line-height: 3em;
            background: #eee;
            table-layout: fixed;
            display: table;
        }*/

    </style>
    <div class="box box-success">
        <div class="box-body">
            <div class="box-header with-border">
                <h2>Bed Availability as of <span id="time"></span></h2>
            </div>
            <section class="content-area">
                <div class="table-responsive">
                    <table class="table table-striped" border="1">
                        <thead>
                        <tr>
                            <th class="info" rowspan="4" style="vertical-align: middle;"><center>Name of Hospital</center></th>
                            <th class="success" style="vertical-align: middle;" rowspan="4"><center>Hospital Category</center></th>
                            <th style="background-color: #ffb3b8;" colspan="12"><center>Number of avalable Beds</center></th>
                            <th class="info" colspan="4"><center>Number of Waitlist</center></th>
                            <th class="bg-pink" style="background-color: #ffb3b8;width: 10%;vertical-align: middle;margin-left: 20px;" rowspan="4"><center>Remarks</center></th>
                            <th class="bg-pink" style="background-color: #ffb3b8;vertical-align: middle;" rowspan="4"><center>Encoded By</center></th>
                            <th class="bg-pink" style="background-color: #ffb3b8;vertical-align: middle;" rowspan="4"><center>Last Update</center></th>
                        </tr>
                        <tr>
                            <th class="danger" colspan="6"><center>COVID BEDS</center></th>
                            <th class="warning" colspan="6"><center>Non-COVID BEDS</center></th>
                            <th class="danger" colspan="2" rowspan="2" style="vertical-align: middle"><center>COVID BEDS</center></th>
                            <th class="warning" colspan="2" rowspan="2"><center>Non-COVID BEDS</center></th>
                        </tr>
                        <tr>
                            <th class="danger" colspan="4"></th>
                            <th class="info" colspan="2">Mechanical Ventilators</th>
                            <th class="warning" colspan="4"></th>
                            <th class="info" colspan="2">Mechanical Ventilators</th>
                        </tr>
                        <tr>
                            <th>Emergency Room (ER)</th>
                            <th>ICU - Intensive Care Units</th>
                            <th>COVID Beds</th>
                            <th>Isolation Beds</th>
                            <th>Used</th>
                            <th>Vacant</th>
                            <th>Emergency Room (ER)</th>
                            <th>ICU - Intensive Care Units</th>
                            <th>COVID Beds</th>
                            <th>Isolation Beds</th>
                            <th>Used</th>
                            <th>Vacant</th>
                            <th>Emergency Room (ER)</th>
                            <th>ICU - Intensive Care Units</th>
                            <th>Emergency Room (ER)</th>
                            <th>ICU - Intensive Care Units</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($facility as $row)
                            @if(!isset($facility_checker[$row->province]))
                                <?php $facility_checker[$row->province] = true; ?>
                                <tr>
                                    <th style="font-size: 14pt;" colspan="21">{{ strtoupper(\App\Province::find($row->province)->description) }} PROVINCE</th>
                                </tr>
                            @endif
                            <tr>
                                <td width="25%">
                                    <b class="text-green">{{ $row->name }}</b><br>
                                    <span class="text-yellow">({{ $row->contact }})</span>
                                </td>
                                <td><span class="text-green">{{ $row->level }}</span></td>
                                <td>
                                    {{ $row->emergency_room_covid ? $row->emergency_room_covid : 0 }}
                                </td>
                                <td>
                                    {{ $row->icu_covid ? $row->icu_covid : 0 }}
                                </td>
                                <td>
                                    {{ $row->beds_covid ? $row->beds_covid : 0 }}
                                </td>
                                <td>
                                    {{ $row->isolation_covid ? $row->isolation_covid : 0 }}
                                </td>
                                <td>
                                    {{ $row->mechanical_used_covid ? $row->mechanical_used_covid : 0 }}
                                </td>
                                <td>
                                    {{ $row->mechanical_vacant_covid ? $row->mechanical_vacant_covid : 0 }}
                                </td>
                                <td>
                                    {{ $row->emergency_room_non ? $row->emergency_room_non : 0 }}
                                </td>
                                <td>
                                    {{ $row->icu_non ? $row->icu_non : 0 }}
                                </td>
                                <td>
                                    {{ $row->beds_non ? $row->beds_non : 0 }}
                                </td>
                                <td>
                                    {{ $row->isolation_non ? $row->isolation_non : 0 }}
                                </td>
                                <td>
                                    {{ $row->mechanical_used_non ? $row->mechanical_used_non : 0 }}
                                </td>
                                <td>
                                    {{ $row->mechanical_vacant_non ? $row->mechanical_vacant_non : 0 }}
                                </td>
                                <td>
                                    {{ $row->emergency_room_covid_wait ? $row->emergency_room_covid_wait : 0 }}
                                </td>
                                <td>
                                    {{ $row->icu_covid_wait ? $row->icu_covid_wait : 0 }}
                                </td>
                                <td>
                                    {{ $row->emergency_room_non_wait ? $row->emergency_room_non_wait : 0 }}
                                </td>
                                <td>
                                    {{ $row->icu_non_wait ? $row->icu_non_wait : 0 }}
                                </td>
                                <td>
                                    <a href="#" class="text_editable" data-title="Remarks" id="{{ $row->id }}">{{ $row->remarks }}</a>
                                </td>
                                <td>
                                    <?php
                                        $encoded_by = \App\BedTracker::
                                                    select("users.fname","users.mname","users.lname","bed_tracker.created_at")
                                                    ->leftJoin("users","users.id","=","bed_tracker.encoded_by")
                                                    ->where("bed_tracker.facility_id","=",$row->id)->orderBy("bed_tracker.created_at","asc")
                                                    ->where("users.level","!=","opcen")
                                                    ->orderBy("bed_tracker.created_at","desc")
                                                    ->first();
                                        $created_at = $encoded_by->created_at;
                                        $encoded_by = $encoded_by->fname.' '.$encoded_by->mname[0].'. '.$encoded_by->lname;
                                        echo $encoded_by;
                                    ?>
                                </td>
                                <td>
                                    @if($created_at)
                                    {{ date("F d,Y",strtotime($created_at)) }}<br>
                                    <small class="text-yellow">({{ date('g:i a',strtotime($created_at)) }})</small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>


@endsection

@section('js')
    <script src="{{ asset('resources/plugin/bootstrap3-editable/js/bootstrap-editable.min.js') }}"></script>
    <script>
        $("#container").removeClass("container");
        $("#container").addClass("container-fluid");

        var timeDisplay = document.getElementById("time");
        function refreshTime() {
            var dateString = new Date().toLocaleString("en-US");
            var formattedString = dateString.replace(", ", " - ");
            timeDisplay.innerHTML = formattedString;
        }
        setInterval(refreshTime, 1000);

        //turn to inline mode
        $.fn.editable.defaults.mode = 'popup';
        //editables
        $(".text_editable").each(function(){
            $('#'+this.id).editable({
                type : 'textarea',
                name: 'username',
                title: $(this).data("title"),
                emptytext: 'empty',
                success: function(response, newValue) {
                    var url = "<?php echo asset('bed_update'); ?>";
                    var json = {
                        "_token" : "<?php echo csrf_token(); ?>",
                        "facility_id" : this.id,
                        "column" : 'remarks',
                        "value" : newValue
                    };
                    var title = $(this).data("title");
                    $.post(url,json,function(result){
                        console.log(result);
                        Lobibox.notify('success', {
                            title: "",
                            msg: title+" saved!",
                            size: 'mini',
                            rounded: true
                        });
                    });
                }
            });
        });
    </script>
@endsection

