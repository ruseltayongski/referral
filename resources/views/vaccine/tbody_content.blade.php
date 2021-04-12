<tr class="bg-green">
    <td>
        <input type="text" id="date_picker{{ $count }}" name="" value="<?php if(isset($vaccine->dateof_del)) echo date('m/d/Y',strtotime($vaccine->dateof_del)) ?>" class="form-control" required>
    </td>
    <td>
        <input type="text" name="numof_vaccinated" value="<?php if(isset($vaccine->numof_vaccinated)) echo $vaccine->numof_vaccinated ?>" class="form-control">
    </td>
    <td>
        <?php
        $display_aefi_property = "hide";
        $aefi_qty = "";
        if(isset($vaccine->aefi)){
            $display_aefi_property = "";
            $aefi_qty = $vaccine->aefi." : ".$vaccine->aefi_qty;
        }
        ?>
        <b style="margin-left:10%" class="text-green display_aefi{{ $count }} {{ $display_aefi_property }}">{{ $aefi_qty }}</b>
        <input type="hidden" id="aefi_qty{{ $count }}" name="aefi_qty{{ $count }}">
        <select name="aefi" id="" class="select2" onchange="aefi_qtyFunc($(this))">
            <option value="" readonly>Select Option</option>
            <option value="Mild" <?php if(isset($vaccine->aefi)){if($vaccine->aefi == 'Mild')echo 'selected';} ?>>Mild</option>
            <option value="Serious"<?php if(isset($vaccine->aefi)){if($vaccine->aefi == 'Serious')echo 'selected';} ?> >Serious</option>
        </select>
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
<tr class="bg-yellow">
    <td>
        <input type="text" id="date_picker{{ $count }}" name="" value="<?php if(isset($vaccine->dateof_del)) echo date('m/d/Y',strtotime($vaccine->dateof_del)) ?>" class="form-control" required>
    </td>
    <td>
        <input type="text" name="numof_vaccinated" value="<?php if(isset($vaccine->numof_vaccinated)) echo $vaccine->numof_vaccinated ?>" class="form-control">
    </td>
    <td>
        <?php
        $display_aefi_property = "hide";
        $aefi_qty = "";
        if(isset($vaccine->aefi)){
            $display_aefi_property = "";
            $aefi_qty = $vaccine->aefi." : ".$vaccine->aefi_qty;
        }
        ?>
        <b style="margin-left:10%" class="text-green display_aefi{{ $count }} {{ $display_aefi_property }}">{{ $aefi_qty }}</b>
        <input type="hidden" id="aefi_qty{{ $count }}" name="aefi_qty{{ $count }}">
        <select name="aefi" id="" class="select2" onchange="aefi_qtyFunc($(this))">
            <option value="" readonly>Select Option</option>
            <option value="Mild" <?php if(isset($vaccine->aefi)){if($vaccine->aefi == 'Mild')echo 'selected';} ?>>Mild</option>
            <option value="Serious"<?php if(isset($vaccine->aefi)){if($vaccine->aefi == 'Serious')echo 'selected';} ?> >Serious</option>
        </select>
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
    function aefi_qtyFunc(aefi_value) {
        Lobibox.prompt('text',
        {
            title: 'How many AEFI '+aefi_value.val()+"?",
            callback: function ($this, type, ev) {
                if($('.lobibox-input').val()) {
                    $('.display_aefi'+"{{ $count }}").removeClass('hide');
                    $('.display_aefi'+"{{ $count }}").html(aefi_value.val()+":"+($('.lobibox-input').val()));
                    $('#aefi_qty').val($('.lobibox-input').val());
                }
            }
        });
    }
</script>