<tr style="background-color: #59ab91">
    <td style="width: 15%">
        <input type="text" id="date_picker{{ $count }}" name="" value="<?php if(isset($vaccine->dateof_del)) echo date('m/d/Y',strtotime($vaccine->dateof_del)) ?>" class="form-control" required>
    </td>
    <td style="width: 5%">
        <input type="text" name="numof_vaccinated"  value="<?php if(isset($vaccine->numof_vaccinated)) echo $vaccine->numof_vaccinated ?>" class="form-control">
    </td>
    <td style="width: 5%">
        <input type="text" name="Serious" value="" class="form-control">
    </td>
    <td style="width: 5%">
        <input type="text" name="Serious" value="" class="form-control">
    </td>
    <td style="width: 10%">
        <input type="text" name="refused" value="<?php if(isset($vaccine->refused)) echo $vaccine->refused ?>" class="form-control">
    </td>
    <td style="width: 10%">
        <input type="text" name="deferred" value="<?php if(isset($vaccine->deferred)) echo $vaccine->deferred ?>" class="form-control">
    </td>
    <td style="width: 10%">
        <input type="text" name="wastage" value="<?php if(isset($vaccine->wastage)) echo $vaccine->wastage?>" class="form-control">
    </td>
</tr>
<tr style="background-color: #f39c12">
    <td>
        <input type="text" id="date_picker2{{ $count }}" name="" value="<?php if(isset($vaccine->dateof_del)) echo date('m/d/Y',strtotime($vaccine->dateof_del)) ?>" class="form-control" required>
    </td>
    <td>
        <input type="text" name="numof_vaccinated" value="<?php if(isset($vaccine->numof_vaccinated)) echo $vaccine->numof_vaccinated ?>" class="form-control">
    </td>
    <td style="width: 5%">
        <input type="text" name="Serious" value="" class="form-control">
    </td>
    <td style="width: 5%">
        <input type="text" name="Serious" value="" class="form-control">
    </td>
    <td>
        <input type="text" name="refused" value="<?php if(isset($vaccine->refused)) echo $vaccine->refused ?>" class="form-control">
    </td>
    <td>
        <input type="text" name="deferred" value="<?php if(isset($vaccine->deferred)) echo $vaccine->deferred ?>" class="form-control">
    </td>
    <td>
        <input type="text" name="wastage" value="<?php if(isset($vaccine->wastage)) echo $vaccine->wastage?>" class="form-control">
    </td>

</tr>
<tr>
    <td colspan="7"><hr></td>
</tr>
<script>
    $("#date_picker"+"{{ $count }}").daterangepicker({
        "singleDatePicker":true
    });
    $("#date_picker2"+"{{ $count }}").daterangepicker({
        "singleDatePicker":true
    });
</script>