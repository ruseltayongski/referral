<tr style="background-color: #59ab91">
    <input type="hidden" name="province_id" value="{{ $province_id }}">
    <input type="hidden" name="muncity_id" value="{{ $muncity_id }}">
    <td style="width: 15%">
        <input type="text" id="date_picker{{ $count }}" name="date_first[]" class="form-control" required>
    </td>
    <td style="width: 15%" rowspan="2">
        <select name="typeof_vaccine[]" id="typeof_vaccine" class="select2" required>
            <option value="">Select Option</option>
            <option value="Sinovac">Sinovac</option>
            <option value="Astrazeneca">Astrazeneca</option>
            <option value="Moderna" disabled>Moderna</option>
            <option value="Pfizer" disabled>Pfizer</option>
        </select>
    </td>
    <td style="width: 15%" rowspan="2">
        <select name="priority[]" id="" class="select2" >
            <option value="">Select Priority</option>
            <option value="frontline_health_workers" >Frontline Health Workers</option>
            <option value="indigent_senior_citizens" >Senior Citizens</option>
            <option value="remaining_indigent_population"  disabled>Remaining Indigent Population</option>
            <option value="uniform_personnel" disabled>Uniform Personnel</option>
            <option value="teachers_school_workers" disabled>Teachers & School Workers</option>
            <option value="all_government_workers" disabled>All Government Workers (National & Local)</option>
            <option value="essential_workers"  disabled>Essential Workers</option>
            <option value="socio_demographic" disabled>Socio-demographic groups & significant higher risk other than senior citizen and indigent population (e.g.PDL,PWD,IP,Filipinos living in high-density areas)</option>
            <option value="ofw" disabled >OFW's</option>
            <option value="remaining_workforce"  disabled>Other remaining workforce</option>
            <option value="remaining_filipino_citizen" disabled>Remaining Filipino Citizen</option>
            <option value="etc"  disabled >ETC.</option>
        </select>
    </td>
    <td style="width: 5%">
        <input type="text" name="vaccinated_first[]" class="form-control">
    </td>
    <td style="width: 5%">
        <input type="text" name="mild_first[]" value="" class="form-control">
    </td>
    <td style="width: 5%">
        <input type="text" name="serious_first[]" value="" class="form-control">
    </td>
    <td style="width: 5%">
        <input type="text" name="refused_first[]" class="form-control">
    </td>
    <td style="width: 5%">
        <input type="text" name="deferred_first[]" class="form-control">
    </td>
    <td style="width: 5%">
        <input type="text" name="wastage_first[]" class="form-control">
    </td>
</tr>
<tr style="background-color: #f39c12">
    <td>
        <input type="text" id="date_picker2{{ $count }}" name="date_second[]"  class="form-control">
    </td>
    <td>
        <input type="text" name="vaccinated_second[]" class="form-control">
    </td>
    <td style="width: 5%">
        <input type="text" name="mild_second[]" value="" class="form-control">
    </td>
    <td style="width: 5%">
        <input type="text" name="serious_second[]" value="" class="form-control">
    </td>
    <td style="width: 5%">
        <input type="text" name="refused_second[]" class="form-control">
    </td>
    <td style="width: 5%">
        <input type="text" name="deferred_second[]" class="form-control">
    </td>
    <td>
        <input type="text" name="wastage_second[]"  class="form-control">
    </td>

</tr>
<tr>
    <td colspan="9"><hr></td>
</tr>
<script>
    $("#date_picker"+"{{ $count }}").daterangepicker({
        "singleDatePicker":true
    });
    $("#date_picker2"+"{{ $count }}").daterangepicker({
        "singleDatePicker":true
    });
</script>