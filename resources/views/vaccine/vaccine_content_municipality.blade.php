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
    function addTbodyContent1(province_id,muncity_id) {
        count++;
        var url = "<?php echo asset('vaccine/tbody/content'); ?>"+"/"+count+"/"+province_id+"/"+muncity_id;
        $.get(url,function(data){
            $('#tbody_content_vaccine').append(data);
            $(".select2").select2({ width: '100%' });
        });
    }

    function addTbodyContent(province_id,muncity_id) {
        $('#tbody_content_vaccine').append('<tr style="background-color: #59ab91">\n' +
            '    <input type="hidden" name="province_id">\n' +
            '    <input type="hidden" name="muncity_id">\n' +
            '    <td style="width: 15%">\n' +
            '        <input type="text" id="date_picker{{ $count }}" name="date_first[]" class="form-control" required>\n' +
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
            '        <input type="text" id="date_picker2{{ $count }}" name="date_second[]"  class="form-control">\n' +
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

        $("#date_picker"+"{{ $count }}").daterangepicker({
            "singleDatePicker":true
        });
        $("#date_picker2"+"{{ $count }}").daterangepicker({
            "singleDatePicker":true
        });

        $(".select2").select2({ width: '100%' });
    }

</script>


