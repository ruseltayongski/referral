<style>
    input[type=radio] {
        width: 20%;
        height: 2em;
    }
</style>
<div class="modal-header">
    <h3 id="myModalLabel"><i class="fa fa-location-arrow" style="color:green"></i> {{ \App\Muncity::find($muncity_id)->description }}</h3>
</div>
 <form action="{{ asset('vaccine/saved') }}" method="POST" id="form_submit" autocomplete="off">
        {{ csrf_field() }}
        <input type="hidden" name="vaccine_id" value="{{ $vaccine->id }}">
        <br>
        <table class="table" style="font-size: 8pt">
            <thead class="bg-gray">
                <tr>
                    <th>Dose Date</th>
                    <th>Type Of Vaccine</th>
                    <th>Priority</th>
                    <th>Vaccinated</th>
                    <th>Mild</th>
                    <th>Serious</th>
                    <th>Refused</th>
                    <th>Deferred</th>
                    <th>Wastage</th>

                </tr>
            </thead>
            @if(count($vaccine_accomplishment)>0)
                @foreach($vaccine_accomplishment as $vaccine)
                    <tr style="background-color: #59ab91">
                        <input type="hidden" name="province_id" value="{{ $province_id }}">
                        <input type="hidden" name="muncity_id" value="{{ $muncity_id }}">
                        <td style="width: 15%">
                            <input type="text" id="date_picker{{ $vaccine->id.$vaccine->encoded_by }}" name="date_first[]" value="<?php if(isset($vaccine->date_first)) echo date('m/d/Y',strtotime($vaccine->date_first)) ?>" class="form-control" required>
                        </td>
                        <td style="width: 15%" rowspan="2">
                            <select name="typeof_vaccine[]" id="typeof_vaccine" class="select2" required>
                                <option value="">Select Option</option>
                                <option value="Sinovac" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Sinovac')echo 'selected';} ?>>Sinovac</option>
                                <option value="Astrazeneca" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Astrazeneca')echo 'selected';} ?>>Astrazeneca</option>
                                <option value="Moderna" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Moderna')echo 'selected';} ?> disabled>Moderna</option>
                                <option value="Pfizer" <?php if(isset($vaccine->typeof_vaccine)){if($vaccine->typeof_vaccine == 'Pfizer')echo 'selected';} ?> disabled>Pfizer</option>
                            </select>
                        </td>
                        <td style="width: 15%" rowspan="2">
                            <select name="priority[]" id="" class="select2" >
                                <option value="">Select Priority</option>
                                <option value="frontline_health_workers" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'frontline_health_workers')echo 'selected';} ?>>Frontline Health Workers</option>
                                <option value="indigent_senior_citizens" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'indigent_senior_citizens')echo 'selected';} ?>>Senior Citizens</option>
                                <option value="remaining_indigent_population" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'remaining_indigent_population')echo 'selected';} ?> disabled>Remaining Indigent Population</option>
                                <option value="uniform_personnel" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'uniform_personnel')echo 'selected';} ?> disabled>Uniform Personnel</option>
                                <option value="teachers_school_workers" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'teachers_school_workers')echo 'selected';} ?> disabled>Teachers & School Workers</option>
                                <option value="all_government_workers" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'all_government_workers')echo 'selected';} ?> disabled>All Government Workers (National & Local)</option>
                                <option value="essential_workers" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'essential_workers')echo 'selected';} ?> disabled>Essential Workers</option>
                                <option value="socio_demographic" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'socio_demographic')echo 'selected';} ?> disabled>Socio-demographic groups & significant higher risk other than senior citizen and indigent population (e.g.PDL,PWD,IP,Filipinos living in high-density areas)</option>
                                <option value="ofw" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'ofw')echo 'selected';} ?> disabled >OFW's</option>
                                <option value="remaining_workforce" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'remaining_workforce')echo 'selected';} ?> disabled>Other remaining workforce</option>
                                <option value="remaining_filipino_citizen" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'remaining_filipino_citizen')echo 'selected';} ?> disabled>Remaining Filipino Citizen</option>
                                <option value="etc" <?php if(isset($vaccine->priority)){if($vaccine->priority == 'etc')echo 'selected';} ?> disabled >ETC.</option>
                            </select>
                        </td>
                        <td style="width: 5%">
                            <input type="text" name="vaccinated_first[]"  value="<?php if(isset($vaccine->vaccinated_first)) echo $vaccine->vaccinated_first ?>" class="form-control">
                        </td>
                        <td style="width: 5%">
                            <input type="text" name="mild_first[]" value="<?php if(isset($vaccine->mild_first)) echo $vaccine->mild_first ?>" class="form-control">
                        </td>
                        <td style="width: 5%">
                            <input type="text" name="serious_first[]" value="<?php if(isset($vaccine->serious_first)) echo $vaccine->serious_first ?>" class="form-control">
                        </td>
                        <td style="width: 10%">
                            <input type="text" name="refused_first[]" value="<?php if(isset($vaccine->refused_first)) echo $vaccine->refused_first ?>" class="form-control">
                        </td>
                        <td style="width: 10%">
                            <input type="text" name="deferred_first[]" value="<?php if(isset($vaccine->deferred_first)) echo $vaccine->deferred_first ?>" class="form-control">
                        </td>
                        <td style="width: 10%">
                            <input type="text" name="wastage_first[]" value="<?php if(isset($vaccine->wastage_first)) echo $vaccine->wastage_first?>" class="form-control">
                        </td>
                    </tr>
                    <tr style="background-color: #f39c12">
                        <td>
                            <input type="text" id="date_picker2{{ $vaccine->id.$vaccine->encoded_by }}" name="date_second[]" value="<?php if(isset($vaccine->date_second)) echo date('m/d/Y',strtotime($vaccine->date_second)) ?>" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="vaccinated_second[]" value="<?php if(isset($vaccine->vaccinated_second)) echo $vaccine->vaccinated_second ?>" class="form-control">
                        </td>
                        <td style="width: 5%">
                            <input type="text" name="mild_second[]" value="<?php if(isset($vaccine->mild_first)) echo $vaccine->mild_first ?>" class="form-control">
                        </td>
                        <td style="width: 5%">
                            <input type="text" name="serious_second[]" value="<?php if(isset($vaccine->serious_first)) echo $vaccine->serious_second ?>" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="refused_second[]" value="<?php if(isset($vaccine->refused_second)) echo $vaccine->refused_second ?>" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="deferred_second[]" value="<?php if(isset($vaccine->deferred_second)) echo $vaccine->deferred_second ?>" class="form-control">
                        </td>
                        <td>
                            <input type="text" name="wastage_second[]" value="<?php if(isset($vaccine->wastage_second)) echo $vaccine->wastage_second?>" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8"><hr></td>
                    </tr>
                    <script>
                        $("#date_picker"+"{{ $vaccine->id.$vaccine->encoded_by }}").daterangepicker({
                            "singleDatePicker":true
                        });
                        $("#date_picker2"+"{{ $vaccine->id.$vaccine->encoded_by }}").daterangepicker({
                            "singleDatePicker":true
                        });
                    </script>
                @endforeach
            @endif
            <script>
                $("#date_picker").daterangepicker({
                    "singleDatePicker":true
                });
                $("#date_picker2").daterangepicker({
                    "singleDatePicker":true
                });
            </script>
            <tbody id="tbody_content_vaccine">

            </tbody>
            <tr>
                <td colspan="9">
                    <a href="#" onclick="addTbodyContent('<?php echo $province_id; ?>','<?php echo $muncity_id; ?>')" class="pull-right red" id="workAdd"><i class="fa fa-user-plus"></i> Add Daily Accomplishment</a>
                </td>
            </tr>
            <tr>
                <td colspan="9">
                    <div class="pull-right">
                        <button type="button" class="btn btn-default btn-md" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-success btn-md"><i class="fa fa-send"></i> Submit</button>
                    </div>
                </td>
            </tr>
    </table>
</form>

<script>
    var count = 0;
    function addTbodyContent(province_id,muncity_id) {
        count++;
        $('#tbody_content_vaccine').append('<tr style="background-color: #59ab91">\n' +
            '    <input type="hidden" name="province_id" value="'+province_id+'" >\n' +
            '    <input type="hidden" name="muncity_id" value="'+muncity_id+'" >\n' +
            '    <td style="width: 15%">\n' +
            '        <input type="text" id="date_picker'+count+'" name="date_first[]" class="form-control" >\n' +
            '    </td>\n' +
            '    <td style="width: 15%" rowspan="2">\n' +
            '        <select name="typeof_vaccine[]" id="typeof_vaccine" class="select2" required>\n' +
            '            <option value="">Select Option</option>\n' +
            '            <option value="Sinovac">Sinovac</option>\n' +
            '            <option value="Astrazeneca">Astrazeneca</option>\n' +
            '            <option value="Moderna" disabled>Moderna</option>\n' +
            '            <option value="Pfizer" disabled>Pfizer</option>\n' +
            '        </select>\n' +
            '    </td>\n' +
            '    <td style="width: 15%" rowspan="2">\n' +
            '        <select name="priority[]" id="" class="select2" >\n' +
            '            <option value="">Select Priority</option>\n' +
            '            <option value="frontline_health_workers" >Frontline Health Workers</option>\n' +
            '            <option value="indigent_senior_citizens" >Senior Citizens</option>\n' +
            '            <option value="remaining_indigent_population"  disabled>Remaining Indigent Population</option>\n' +
            '            <option value="uniform_personnel" disabled>Uniform Personnel</option>\n' +
            '            <option value="teachers_school_workers" disabled>Teachers & School Workers</option>\n' +
            '            <option value="all_government_workers" disabled>All Government Workers (National & Local)</option>\n' +
            '            <option value="essential_workers"  disabled>Essential Workers</option>\n' +
            '            <option value="socio_demographic" disabled>Socio-demographic groups & significant higher risk other than senior citizen and indigent population (e.g.PDL,PWD,IP,Filipinos living in high-density areas)</option>\n' +
            '            <option value="ofw" disabled >OFW\'s</option>\n' +
            '            <option value="remaining_workforce"  disabled>Other remaining workforce</option>\n' +
            '            <option value="remaining_filipino_citizen" disabled>Remaining Filipino Citizen</option>\n' +
            '            <option value="etc"  disabled >ETC.</option>\n' +
            '        </select>\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="vaccinated_first[]" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="mild_first[]" value="" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="serious_first[]" value="" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="refused_first[]" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="deferred_first[]" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="wastage_first[]" class="form-control">\n' +
            '    </td>\n' +
            '</tr>\n' +
            '<tr style="background-color: #f39c12">\n' +
            '    <td>\n' +
            '        <input type="text" id="date_picker2'+count+'" name="date_second[]"  class="form-control">\n' +
            '    </td>\n' +
            '    <td>\n' +
            '        <input type="text" name="vaccinated_second[]" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="mild_second[]" value="" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="serious_second[]" value="" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="refused_second[]" class="form-control">\n' +
            '    </td>\n' +
            '    <td style="width: 5%">\n' +
            '        <input type="text" name="deferred_second[]" class="form-control">\n' +
            '    </td>\n' +
            '    <td>\n' +
            '        <input type="text" name="wastage_second[]"  class="form-control">\n' +
            '    </td>\n' +
            '\n' +
            '</tr>\n' +
            '<tr>\n' +
            '    <td colspan="9"><hr></td>\n' +
            '</tr>');

        $("#date_picker"+count).daterangepicker({
            "singleDatePicker":true
        });
        $("#date_picker2"+count).daterangepicker({
            "singleDatePicker":true
        });

        $(".select2").select2({ width: '100%' });
    }

</script>


