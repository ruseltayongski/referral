@extends('layouts.app')

@section('content')
    <style>
        .editable-empty{
            color: #ff3d3c;
        }
    </style>
    <div class="box box-success">
        <div class="box-body">
            <div class="box-header with-border">
                <h3>Bed Availability as of <i id="time"></i></h3>
            </div>
            <div class="table-responsive">
                <table class="table table-striped" border="1">
                    <tr>
                        <th class="info" rowspan="4" style="vertical-align: middle;"><center>Name of Hospital</center></th>
                        <th class="success" style="vertical-align: middle;" rowspan="4"><center>Hospital Category</center></th>
                        <th style="background-color: #ffb3b8;" colspan="12"><center>Number of avalable Beds</center></th>
                        <th class="info" colspan="4"><center>Number of Waitlist</center></th>
                        <th class="bg-pink" style="background-color: #ffb3b8;width: 10%;vertical-align: middle;margin-left: 20px;" rowspan="4"><center>Remarks</center></th>
                        <th class="bg-pink" style="background-color: #ffb3b8;vertical-align: middle;" rowspan="4"><center>Encoded By</center></th>
                    </tr>
                    <tr>
                        <th class="danger" colspan="6"><center>COVID BEDS</center></th>
                        <th class="warning" colspan="6"><center>Non-COVID BEDS</center></th>
                        <th class="danger" colspan="2" rowspan="2" style="vertical-align: middle"><center>COVID BEDS</center></th>
                        <th class="warning" colspan="2" rowspan="2" style="vertical-align: middle"><center>Non-COVID BEDS</center></th>
                    </tr>
                    <tr>
                        <th class="danger" colspan="4"></th>
                        <th class="info" colspan="2">Mechanical Ventilators</th>
                        <th class="warning" colspan="4"></th>
                        <th class="info" colspan="2">Mechanical Ventilators</th>
                    </tr>
                    <tr>
                        <th><center>Emergency Room (ER)</center></th>
                        <th><center>ICU - Intensive Care Units</center></th>
                        <th><center>COVID Beds</center></th>
                        <th><center>Isolation Beds</center></th>
                        <th><center>Used</center></th>
                        <th><center>Vacant</center></th>
                        <th><center>Emergency Room (ER)</center></th>
                        <th><center>ICU - Intensive Care Units</center></th>
                        <th><center>Regular Beds</center></th>
                        <th><center>Isolation Beds</center></th>
                        <th><center>Used</center></th>
                        <th><center>Vacant</center></th>
                        <th><center>Emergency Room (ER)</center></th>
                        <th><center>ICU - Intensive Care Units</center></th>
                        <th><center>Emergency Room (ER)</center></th>
                        <th><center>ICU - Intensive Care Units</center></th>
                    </tr>
                    <tr>
                        <td><b class="text-success">{{ $facility->name }}</b></td>
                        <td><b class="text-success"><center>{{ ucfirst($facility->level) }}</center></b></td>
                        <td><a href="#" class="text_editable" data-title="Emergency Room (ER)" id="emergency_room_covid" >{{ $facility->emergency_room_covid }}</a></td>
                        <td><a href="#" class="text_editable" data-title="ICU - Intensive Care Units" id="icu_covid" >{{ $facility->icu_covid }}</a></td>
                        <td><a href="#" class="text_editable" data-title="COVID Beds" id="beds_covid" >{{ $facility->beds_covid }}</a></td>
                        <td><a href="#" class="text_editable" data-title="Isolation Beds" id="isolation_covid" >{{ $facility->isolation_covid }}</a></td>
                        <td><a href="#" class="text_editable" data-title="Mechanical Ventilators Used" id="mechanical_used_covid" >{{ $facility->mechanical_used_covid }}</a></td>
                        <td><a href="#" class="text_editable" data-title="Mechanical Ventilators Vacant" id="mechanical_vacant_covid" >{{ $facility->mechanical_vacant_covid }}</a></td>
                        <td><a href="#" class="text_editable" data-title="Emergency Room (ER)" id="emergency_room_non" >{{ $facility->emergency_room_non }}</a></td>
                        <td><a href="#" class="text_editable" data-title="ICU - Intensive Care Units" id="icu_non" >{{ $facility->icu_non }}</a></td>
                        <td><a href="#" class="text_editable" data-title="COVID Beds" id="beds_non" >{{ $facility->beds_non }}</a></td>
                        <td><a href="#" class="text_editable" data-title="Isolation Beds" id="isolation_non">{{ $facility->isolation_non }}</a></td>
                        <td><a href="#" class="text_editable" data-title="Mechanical Ventilators Used" id="mechanical_used_non" >{{ $facility->mechanical_used_non }}</a></td>
                        <td><a href="#" class="text_editable" data-title="Mechanical Ventilators None" id="mechanical_vacant_non" >{{ $facility->mechanical_vacant_non }}</a></td>
                        <td><a href="#" class="text_editable" data-title="Emergency Room (ER)" id="emergency_room_covid_wait" >{{ $facility->emergency_room_covid_wait }}</a></td>
                        <td><a href="#" class="text_editable" data-title="ICU - Intensive Care Units" id="icu_covid_wait" >{{ $facility->icu_covid_wait }}</a></td>
                        <td><a href="#" class="text_editable" data-title="Emergency Room (ER)" id="emergency_room_non_wait" >{{ $facility->emergency_room_non_wait }}</a></td>
                        <td><a href="#" class="text_editable" data-title="ICU - Intensive Care Units" id="icu_non_wait" >{{ $facility->icu_non_wait }}</a></td>
                        <td>{{ $facility->remarks }}</td>
                        <td>
                            <?php
                            $encoded_by = \App\BedTracker::
                            select("bed_tracker.id","users.fname","users.mname","users.lname","bed_tracker.created_at")
                                ->leftJoin("users","users.id","=","bed_tracker.encoded_by")
                                ->where("bed_tracker.facility_id","=",$facility->id)
                                ->where("users.level","!=","opcen")
                                ->orderBy("bed_tracker.id","desc")
                                ->first();
                            $created_at = $encoded_by->created_at;
                            $encoded_by = $encoded_by->fname.' '.$encoded_by->mname[0].'. '.$encoded_by->lname;
                            echo $encoded_by;
                            ?><br>
                            @if($created_at)
                                <small class="text-blue">{{ date("F d,Y",strtotime($created_at)) }}</small><br>
                                <small class="text-yellow">({{ date('g:i a',strtotime($created_at)) }})</small>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
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
                type : this.id == 'remarks' ? 'textarea' : 'text',
                name: 'username',
                title: $(this).data("title"),
                emptytext: 'empty',
                success: function(response, newValue) {
                    var url = "<?php echo asset('bed_update'); ?>";
                    var json = {
                        "_token" : "<?php echo csrf_token(); ?>",
                        "facility_id" : "<?php echo $facility->id; ?>",
                        "column" : this.id,
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

