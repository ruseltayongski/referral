<style>
    input[type=radio] {
        width: 20%;
        height: 2em;
    }
</style>
<form action="<?php
    if(isset($vaccine->typeof_vaccine)){
        echo asset('vaccine/update');
    }
    else{
        echo asset('vaccine/saved');
    }
?>" method="POST" id="form_submit" autocomplete="off">
    {{ csrf_field() }}
    <input type="hidden" name="vaccine_id" value="{{ $vaccine->id }}">
    <div class="row">
       <div class="col-md-4">
           <small>Type of Vaccine</small>
           <select name="typeof_vaccine" id="" class="select2" required>
               <option value="">Select Option</option>
               <option value="Sinovac" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Sinovac')echo 'selected';} ?>>Sinovac</option>
               <option value="Astrazeneca" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Astrazeneca')echo 'selected';} ?>>Astrazeneca</option>
               <option value="Moderna" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Moderna')echo 'selected';} ?>>Moderna</option>
               <option value="Pfizer" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Pfizer')echo 'selected';} ?>>Pfizer</option>
           </select>
       </div>
        <div class="col-md-4">
            <small>Priority</small>
            <select name="priority" id="" class="select2">
                <option value="">Select Option</option>
                <option value="frontline_health_workers" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'frontline_health_workers')echo 'selected';} ?>>Frontline Health Workers</option>
                <option value="indigent_senior_citizens" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'indigent_senior_citizens')echo 'selected';} ?>>Indigent Senior Citizens</option>
                <option value="remaining_indigent_population" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'remaining_indigent_population')echo 'selected';} ?>>Remaining Indigent Population</option>
                <option value="uniform_personnel" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'uniform_personnel')echo 'selected';} ?>>Uniform Personnel</option>
                <option value="teachers_school_workers" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'teachers_school_workers')echo 'selected';} ?>>Teachers & School Workers</option>
                <option value="all_government_workers" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'all_government_workers')echo 'selected';} ?>>All Government Workers (National & Local)</option>
                <option value="essential_workers" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'essential_workers')echo 'selected';} ?>>Essential Workers</option>
                <option value="socio_demographic" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'socio_demographic')echo 'selected';} ?>>Socio-demographic groups & significant higher risk other than senior citizen and indigent population (e.g.PDL,PWD,IP,Filipinos living in high-density areas)</option>
                <option value="ofw" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'ofw')echo 'selected';} ?>>OFW's</option>
                <option value="remaining_workforce" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'remaining_workforce')echo 'selected';} ?>>Other remaining workforce</option>
                <option value="remaining_filipino_citizen" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'remaining_filipino_citizen')echo 'selected';} ?>>Remaining Filipino Citizen</option>
                <option value="etc" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'etc')echo 'selected';} ?>>ETC.</option>
            </select>
        </div>
        <div class="col-md-4">
            <small>Sub-Priority</small>
            <input type="text" name="sub_priority" value="<?php if(isset($vaccine->sub_priority)) echo $vaccine->sub_priority ?>" class="form-control">
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <small>Province</small>
            <select name="province_id" id="province_id" class="select2" onchange="onChangeProvince($(this).val())">
                <option value="">Select Option</option>
                @foreach($province as $row)
                    <option value="{{ $row->id }}"  <?php if(isset($vaccine->province_id)){if($vaccine->province_id == $row->id)echo 'selected';} ?> >{{ $row->description }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <small>Municipality</small>
            <select name="muncity_id" id="municipality" class="select2">
                <option value="">Select Option</option>
                @if(isset($vaccine->muncity_id))
                    @foreach($muncity as $row)
                        <option value="{{ $row->id }}"  <?php if(isset($vaccine->muncity_id)){if($vaccine->muncity_id == $row->id)echo 'selected';} ?> >{{ $row->description }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-4">
            <small>Facility</small>
            <select name="facility_id" id="facility" class="select2">
                <option value="">Select Option</option>
                @if(isset($vaccine->province_id))
                    @foreach($facility as $row)
                        <option value="{{ $row->id }}"  <?php if(isset($vaccine->facility_id)){if($vaccine->facility_id == $row->id)echo 'selected';} ?> >{{ $row->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <small>No. of Eligble Population A1.1-A1.7 </small>
            <input type="text" name="no_eli_pop" value="<?php if(isset($vaccine->no_eli_pop)) echo $vaccine->no_eli_pop ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>Ownership</small>
            <input type="text" name="ownership" value="<?php if(isset($vaccine->ownership)) echo $vaccine->ownership ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>No. of Vaccine Allocated</small>
            <input type="text" name="nvac_allocated" value="<?php if(isset($vaccine->nvac_allocated)) echo $vaccine->nvac_allocated ?>" class="form-control">
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <small>Date of Delivery</small>
            <input type="text" id="date_picker2" name="dateof_del" value="<?php if(isset($vaccine->dateof_del)) echo $vaccine->dateof_del ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>Fist Dose</small>
            <input type="text" id="date_picker" name="first_dose" value="<?php if(isset($vaccine->first_dose)) echo $vaccine->first_dose ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>Second Dose</small>
            <input type="text" id="date_picker1" name="second_dose" value="<?php if(isset($vaccine->second_dose)) echo $vaccine->second_dose ?>" class="form-control">
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <small>Target Dose Per Day</small>
            <input type="text" name="tgtdoseper_day" value="<?php if(isset($vaccine->tgtdoseper_day)) echo $vaccine->tgtdoseper_day ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>No. of Vaccinated</small>
            <input type="text" name="numof_vaccinated" value="<?php if(isset($vaccine->numof_vaccinated)) echo $vaccine->numof_vaccinated ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>% Coverage A1.1-A1.7</small>
            <input type="text" name="percent_coverage" value="<?php if(isset($vaccine->percent_coverage)) echo $vaccine->percent_coverage?>" class="form-control">
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-4">
            <small>AEF1</small>
            <b style="margin-left:10%" class="text-green display_aefi hide"></b>
            <input type="hidden" id="aef_qty" name="aef_qty">
            <select name="aef1" id="" class="select2" onchange="aef1_qty($(this))">
                <option value="" readonly>Select Option</option>
                <option value="Mild" <?php if(isset($vaccine->aef1)){if($vaccine->aef1 == 'Mild')echo 'selected';} ?>>Mild</option>
                <option value="Serious"<?php if(isset($vaccine->aef1)){if($vaccine->aef1 == 'Serious')echo 'selected';} ?> >Serious</option>
            </select>
        </div>
        <div class="col-md-4">
            <small>Deferred</small>
            <input type="text" name="deferred" value="<?php if(isset($vaccine->deferred)) echo $vaccine->deferred ?>" class="form-control">
        </div>
        <div class="col-md-4">
            <small>Refused</small>
            <input type="text" name="refused" value="<?php if(isset($vaccine->refused)) echo $vaccine->refused ?>" class="form-control">
        </div>
    </div>
    <br>
    <div class="pull-right">
        <button type="button" class="btn btn-default btn-md" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
        @if(isset($vaccine->typeof_vaccine))
            <button type="submit" class="btn btn-warning btn-md"><i class="fa fa-send"></i> Update</button>
            @else
            <button type="submit" class="btn btn-success btn-md"><i class="fa fa-send"></i> Submit</button>
        @endif
    </div>
    <br>
    <br>
</form>
<script>
    $("#date_picker").daterangepicker({
       "singleDatePicker":true
    });
    $("#date_picker1").daterangepicker({
        "singleDatePicker":true
    });
    $("#date_picker2").daterangepicker({
        "singleDatePicker":true
    });

    function aef1_qty(aef1_value) {
        Lobibox.prompt('text',
            {
                title: 'How many AEFI '+aef1_value.val()+"?",
                callback: function ($this, type, ev) {
                    if($('.lobibox-input').val()) {
                        $('.display_aefi').removeClass('hide');
                        $('.display_aefi').html(aef1_value.val()+":"+($('.lobibox-input').val()));
                        $('#aef_qty').val($('.lobibox-input').val());
                    }
                }
            });
    }

    function onChangeProvince($province_id){
        if($province_id){
            var url = "{{ url('opcen/onchange/province') }}";
            $.ajax({
                url: url+'/'+$province_id,
                type: 'GET',
                success: function(data){
                    $("#municipality").select2("val", "");
                    $('#municipality').empty()
                        .append($('<option>', {
                            value: '',
                            text : 'Select Option'
                        }));
                    jQuery.each(data, function(i,val){
                        $('#municipality').append($('<option>', {
                            value: val.id,
                            text : val.description
                        }));
                    });
                },
                error: function(){
                    $('#serverModal').modal();
                }
            });

            var url = "{{ url('vaccine/onchange/facility') }}";
            $.ajax({
                url: url+'/'+$province_id,
                type: 'GET',
                success: function(data){
                    console.log(data);
                    $("#facility").select2("val", "");
                    $('#facility').empty()
                        .append($('<option>', {
                            value: '',
                            text : 'Select Option'
                        }));
                    jQuery.each(data, function(i,val){
                        $('#facility').append($('<option>', {
                            value: val.id,
                            text : val.name
                        }));
                    });
                },
                error: function(e){
                    alert(e.message)
                }
            });
        } else {
            $("#municipality").select2("val", "");
            $('#municipality').empty()
                .append($('<option>', {
                    value: '',
                    text : 'Select Option'
                }));
        }
    }


</script>

